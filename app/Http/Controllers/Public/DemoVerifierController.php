<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Support\DemoTickets;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DemoVerifierController extends Controller
{
    public function login(Request $request): View|RedirectResponse
    {
        if ($request->session()->get('demo.verifier.active')) {
            return to_route('demo.verifier.index');
        }

        return view('pages.public.demo.verifier.login', [
            'verifier' => config('bantutepat_demo_applications.verifier'),
        ]);
    }

    public function enter(Request $request): RedirectResponse
    {
        $request->session()->put('demo.verifier.active', true);

        return to_route('demo.verifier.index');
    }

    public function leave(Request $request): RedirectResponse
    {
        $request->session()->forget('demo.verifier.active');

        return to_route('demo.verifier.login');
    }

    public function index(Request $request): View
    {
        $demo = config('bantutepat_demo_applications');
        $assigned = collect($request->session()->get('demo.tickets', []))
            ->filter(fn (array $record) => DemoTickets::isAssignedToVerifier($record, $demo['verifier']))
            ->sortByDesc(fn (array $record) => Arr::last($record['history'])['at']);

        $pending = $assigned->where('status', 'PENDING_APPROVAL');
        $completed = $assigned->filter(fn (array $record) => isset($record['decision']));
        $showResults = $request->query('tampilan') === 'hasil';

        return view('pages.public.demo.verifier.index', [
            'verifier' => $demo['verifier'],
            'states' => $demo['states'],
            'scenarios' => $demo['scenarios'],
            'tickets' => $showResults ? $completed : $pending,
            'pendingCount' => $pending->count(),
            'completedCount' => $completed->count(),
            'showResults' => $showResults,
        ]);
    }

    public function show(Request $request, string $ticket): View
    {
        $record = $this->assignedRecord($request, $ticket);
        $demo = config('bantutepat_demo_applications');

        return view('pages.public.demo.verifier.show', [
            'ticket' => $ticket,
            'record' => $record,
            'states' => $demo['states'],
            'scenario' => $demo['scenarios'][$record['type']],
            'options' => $demo['review_options'][$record['type']],
        ]);
    }

    public function decide(Request $request, string $ticket): RedirectResponse
    {
        $record = $this->assignedRecord($request, $ticket);

        if ($record['status'] !== 'PENDING_APPROVAL' || isset($record['decision'])) {
            return to_route('demo.verifier.show', $ticket);
        }

        $options = config('bantutepat_demo_applications.review_options.'.$record['type']);
        $input = $request->validate([
            'result' => ['required', Rule::in(array_keys($options))],
            'confirmed' => ['accepted'],
        ], [
            'result.required' => 'Pilih hasil tinjauan simulasi.',
            'result.in' => 'Pilih hasil yang tersedia untuk jenis pengajuan ini.',
            'confirmed.accepted' => 'Konfirmasi bahwa berkas dan alasan telah ditinjau.',
        ]);

        $outcome = $options[$input['result']];
        $verifier = config('bantutepat_demo_applications.verifier');
        $record = DemoTickets::mark($record, $outcome['status'], $verifier['name']);
        $record['decision'] = [
            ...$outcome,
            'reason_code' => $input['result'],
            'basis' => $record['recommendation']['basis'],
            'document' => $record['recommendation']['document'],
            'reviewer' => $verifier['name'],
            'at' => Arr::last($record['history'])['at'],
        ];
        $request->session()->put('demo.tickets.'.$ticket, $record);

        return to_route('demo.verifier.show', $ticket);
    }

    private function assignedRecord(Request $request, string $ticket): array
    {
        $record = DemoTickets::find($request->session(), $ticket);
        abort_unless(DemoTickets::isAssignedToVerifier($record, config('bantutepat_demo_applications.verifier')), 404);

        return $record;
    }
}
