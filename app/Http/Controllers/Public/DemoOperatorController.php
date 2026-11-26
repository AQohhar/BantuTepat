<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Support\DemoTickets;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DemoOperatorController extends Controller
{
    public function login(Request $request): View|RedirectResponse
    {
        if ($request->session()->get('demo.operator.active')) {
            return to_route('demo.operator.index');
        }

        return view('pages.public.demo.operator.login', [
            'operator' => config('bantutepat_demo_applications.operator'),
        ]);
    }

    public function enter(Request $request): RedirectResponse
    {
        $request->session()->put('demo.operator.active', true);

        return to_route('demo.operator.index');
    }

    public function leave(Request $request): RedirectResponse
    {
        $request->session()->forget('demo.operator.active');

        return to_route('demo.operator.login');
    }

    public function index(Request $request): View
    {
        $demo = config('bantutepat_demo_applications');
        $operator = $demo['operator'];
        $statusLabels = array_combine(array_keys($demo['states']), array_column($demo['states'], 'label'));
        $typeLabels = array_combine(array_keys($demo['scenarios']), array_column($demo['scenarios'], 'label'));
        $filter = $request->query('status');
        $filterErrors = Validator::make(['status' => $filter], [
            'status' => ['nullable', Rule::in(array_keys($statusLabels))],
        ], ['status.in' => 'Pilih salah satu status yang tersedia.'])->errors();
        $status = $filterErrors->isEmpty() ? $filter : null;

        $assigned = collect($request->session()->get('demo.tickets', []))
            ->filter(fn (array $record) => DemoTickets::isAssignedToOperator($record, $operator));
        $tickets = $assigned->when($status, fn ($list) => $list->where('status', $status))
            ->sortByDesc(fn (array $record) => Arr::last($record['history'])['at']);

        return view('pages.public.demo.operator.index', [
            'operator' => $operator,
            'tickets' => $tickets,
            'assignedCount' => $assigned->count(),
            'status' => $status,
            'statusLabels' => $statusLabels,
            'typeLabels' => $typeLabels,
            'filterErrors' => $filterErrors,
        ]);
    }

    public function show(Request $request, string $ticket): View
    {
        $record = $this->assignedRecord($request, $ticket);
        $demo = config('bantutepat_demo_applications');
        $today = today('Asia/Jakarta');

        return view('pages.public.demo.operator.show', [
            'ticket' => $ticket,
            'record' => $record,
            'operator' => $demo['operator'],
            'scenario' => $demo['scenarios'][$record['type']],
            'states' => $demo['states'],
            'revision' => DemoTickets::revisionRequest($record),
            'canRecommend' => DemoTickets::canRecommend($record),
            'earliestDeadline' => $today->toDateString(),
            'suggestedDeadline' => $today->addDays($demo['operator']['revision_days'])->toDateString(),
        ]);
    }

    public function start(Request $request, string $ticket): RedirectResponse
    {
        $record = $this->assignedRecord($request, $ticket);

        if ($record['status'] === 'SUBMITTED') {
            $record = DemoTickets::mark($record, 'UNDER_REVIEW', config('bantutepat_demo_applications.operator.name'));
            $request->session()->put('demo.tickets.'.$ticket, $record);
        }

        return to_route('demo.operator.show', $ticket);
    }

    public function requestRevision(Request $request, string $ticket): RedirectResponse
    {
        $record = $this->assignedRecord($request, $ticket);

        if ($record['status'] !== 'UNDER_REVIEW') {
            return to_route('demo.operator.show', $ticket);
        }

        $scenario = config('bantutepat_demo_applications.scenarios.'.$record['type']);
        $today = today('Asia/Jakarta')->toDateString();
        $input = $request->validate([
            'reason_code' => ['required', Rule::in(array_keys($scenario['revision_options']))],
            'deadline' => ['required', 'date_format:Y-m-d', 'after_or_equal:'.$today],
        ], [
            'reason_code.required' => 'Pilih alasan dan bukti yang diminta.',
            'reason_code.in' => 'Gunakan pilihan yang tersedia untuk jenis pengajuan ini.',
            'deadline.required' => 'Isi tanggal tenggat perbaikan.',
            'deadline.date_format' => 'Masukkan tanggal tenggat yang valid.',
            'deadline.after_or_equal' => 'Pilih tanggal hari ini atau setelahnya.',
        ]);
        $operatorName = config('bantutepat_demo_applications.operator.name');
        $record['revision_request'] = [
            ...$scenario['revision_options'][$input['reason_code']],
            'reason_code' => $input['reason_code'],
            'deadline' => $input['deadline'],
            'requested_by' => $operatorName,
        ];
        $record = DemoTickets::mark($record, 'REVISION_REQUIRED', $operatorName);
        $request->session()->put('demo.tickets.'.$ticket, $record);

        return to_route('demo.operator.show', $ticket);
    }

    public function recommend(Request $request, string $ticket): RedirectResponse
    {
        $record = $this->assignedRecord($request, $ticket);

        if (! DemoTickets::canRecommend($record)) {
            return to_route('demo.operator.show', $ticket);
        }

        $request->validate(['reviewed' => ['accepted']], [
            'reviewed.accepted' => 'Konfirmasi bahwa operator telah meninjau berkas contoh.',
        ]);

        $operator = config('bantutepat_demo_applications.operator');
        $verifier = config('bantutepat_demo_applications.verifier');
        $programSlug = $record['program_slug'];
        $program = collect(config('bantutepat_demo_programs', []))->firstWhere('slug', $programSlug);
        $agency = $program['agency'] ?? $verifier['agency'];

        if ($record['profile']['territory'] !== $verifier['territory'] || $agency !== $verifier['agency'] || $operator['id'] === $verifier['id']) {
            return to_route('demo.operator.show', $ticket)->withErrors([
                'reviewed' => 'Verifikator contoh yang sesuai belum tersedia untuk tiket ini.',
            ]);
        }

        $record['recommendation'] = [
            'operator_id' => $operator['id'],
            'operator_name' => $operator['name'],
            'verifier_id' => $verifier['id'],
            'agency' => $agency,
            'basis' => $programSlug
                ? config('bantutepat_demo_program_details.'.$programSlug.'.policy_source', 'Ketentuan program simulasi')
                : 'Panduan pemeriksaan usulan data simulasi BantuTepat',
            'document' => $record['revision_document'] ?? $record['document'],
            'note' => isset($record['revision_document'])
                ? 'Bukti perbaikan telah ditinjau dan direkomendasikan untuk pemeriksaan verifikator.'
                : 'Berkas awal telah ditinjau dan direkomendasikan untuk pemeriksaan verifikator.',
        ];
        $record = DemoTickets::mark($record, 'PENDING_APPROVAL', $operator['name']);
        $request->session()->put('demo.tickets.'.$ticket, $record);

        return to_route('demo.operator.show', $ticket);
    }

    private function assignedRecord(Request $request, string $ticket): array
    {
        $record = DemoTickets::find($request->session(), $ticket);
        abort_unless(DemoTickets::isAssignedToOperator($record, config('bantutepat_demo_applications.operator')), 404);

        return $record;
    }
}
