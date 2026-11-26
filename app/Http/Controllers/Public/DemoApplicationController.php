<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Support\DemoProgramRegistration;
use App\Support\DemoTickets;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class DemoApplicationController extends Controller
{
    private const PROGRAM = 'PROGRAM_APPLICATION';

    private const PROPOSAL = 'DATA_PROPOSAL';

    public function program(Request $request, string $slug): View|RedirectResponse
    {
        $program = collect(config('bantutepat_demo_programs', []))->firstWhere('slug', $slug);
        $details = config('bantutepat_demo_program_details.'.$slug);

        abort_if($program === null || ! is_array($details), 404);

        if (! DemoProgramRegistration::allowsSelfApplication($program)) {
            return to_route('program.show', $slug)->with('demo_unavailable', true);
        }

        return $this->wizard($request, self::PROGRAM, $program, $details);
    }

    public function proposal(Request $request): View|RedirectResponse
    {
        return $this->wizard($request, self::PROPOSAL);
    }

    public function ticket(Request $request, string $ticket): View
    {
        $record = DemoTickets::find($request->session(), $ticket);
        $operator = config('bantutepat_demo_applications.operator');

        return view('pages.public.demo.ticket', [
            'ticket' => $ticket,
            'record' => $record,
            'scenario' => config('bantutepat_demo_applications.scenarios.'.$record['type']),
            'states' => config('bantutepat_demo_applications.states'),
            'revision' => DemoTickets::revisionRequest($record),
            'operatorAvailable' => DemoTickets::isAssignedToOperator($record, $operator),
            'operatorTerritory' => $operator['territory'],
        ]);
    }

    public function revision(Request $request, string $ticket): View|RedirectResponse
    {
        $record = DemoTickets::find($request->session(), $ticket);

        if ($record['status'] !== 'REVISION_REQUIRED') {
            return to_route('demo.ticket', $ticket);
        }

        return view('pages.public.demo.revision', [
            'ticket' => $ticket,
            'revision' => DemoTickets::revisionRequest($record),
        ]);
    }

    public function resubmit(Request $request, string $ticket): RedirectResponse
    {
        $record = DemoTickets::find($request->session(), $ticket);

        if ($record['status'] !== 'REVISION_REQUIRED') {
            return to_route('demo.ticket', $ticket);
        }

        $request->validate([
            'revision_document' => ['accepted'],
        ], [
            'revision_document.accepted' => 'Pilih bukti perbaikan simulasi untuk melanjutkan.',
        ]);

        $record['revision_document'] = DemoTickets::revisionRequest($record)['document'];
        $record = DemoTickets::mark($record, 'RESUBMITTED', 'Warga contoh');
        $request->session()->put('demo.tickets.'.$ticket, $record);

        return to_route('demo.ticket', $ticket);
    }

    private function wizard(Request $request, string $type, ?array $program = null, ?array $details = null): View|RedirectResponse
    {
        $session = $request->session();
        $draftKey = $program ? 'demo.drafts.program.'.$program['slug'] : 'demo.drafts.proposal';
        $draft = $session->get($draftKey, []);
        $profiles = config('bantutepat_demo_applications.profiles');
        $reasons = config('bantutepat_demo_applications.proposal_reasons');
        $scenario = config('bantutepat_demo_applications.scenarios.'.$type);
        $path = $program ? route('demo.program', $program['slug']) : route('demo.proposal');
        $draftProfile = $profiles[$draft['profile'] ?? ''] ?? null;
        if ($draft !== [] && ($draftProfile === null || ($program && $draftProfile['territory'] !== $program['territory']))) {
            $session->forget($draftKey);
            $draft = [];
        }
        $lastStep = match (true) {
            isset($draft['document']) => 3,
            isset($draft['profile']) => 2,
            default => 1,
        };

        if ($request->isMethodSafe()) {
            $step = filter_var($request->query('langkah', 1), FILTER_VALIDATE_INT) ?: 1;

            if ($step < 1 || $step > $lastStep) {
                return redirect()->to($path.'?langkah='.$lastStep);
            }

            return view('pages.public.demo.wizard', [
                'path' => $path,
                'step' => $step,
                'program' => $program,
                'details' => $details,
                'draft' => $draft,
                'profiles' => $profiles,
                'reasons' => $reasons,
                'scenario' => $scenario,
            ]);
        }

        $input = $request->validate(['step' => ['required', 'integer', 'between:1,3']]);
        $step = (int) $input['step'];

        if ($step === 1) {
            $input = $request->validate([
                'profile' => ['required', Rule::in(array_keys($profiles))],
            ], ['profile.required' => 'Pilih profil rumah tangga contoh.', 'profile.in' => 'Pilih salah satu profil contoh yang tersedia.']);

            $profile = $profiles[$input['profile']];
            if ($program && $profile['territory'] !== $program['territory']) {
                throw ValidationException::withMessages([
                    'profile' => 'Profil ini berada di luar wilayah sasaran. Pilih profil dari '.$program['territory'].' untuk melanjutkan simulasi.',
                ])->redirectTo($path);
            }

            if (($draft['profile'] ?? null) !== $input['profile']) {
                $draft = ['id' => Str::upper(Str::random(12)), 'profile' => $input['profile']];
            }
            $session->put($draftKey, $draft);

            return redirect()->to($path.'?langkah=2');
        }

        $input = $request->validate(['draft_id' => ['required', 'string', 'regex:/^[A-Z0-9]{12}$/']]);
        $ticket = $scenario['ticket_prefix'].$input['draft_id'];
        $previousTicket = $session->get('demo.tickets.'.$ticket);

        if ($step === 3 && $previousTicket && $previousTicket['program_slug'] === ($program['slug'] ?? null)) {
            return to_route('demo.ticket', $ticket);
        }

        if ($step > $lastStep || ($draft['id'] ?? null) !== $input['draft_id']) {
            return redirect()->to($path.'?langkah='.$lastStep);
        }

        if ($step === 2) {
            $rules = ['document' => ['accepted']];
            if ($type === self::PROPOSAL) {
                $rules['reason'] = ['required', Rule::in(array_keys($reasons))];
            }

            $input = $request->validate($rules, [
                'document.accepted' => 'Pilih dokumen contoh untuk melanjutkan.',
                'reason.required' => 'Pilih alasan usulan.',
                'reason.in' => 'Pilih salah satu alasan usulan yang tersedia.',
            ]);
            $draft['document'] = $scenario['initial_document'];
            $draft['reason'] = $input['reason'] ?? null;
            $session->put($draftKey, $draft);

            return redirect()->to($path.'?langkah=3');
        }

        $request->validate(
            ['confirm' => ['accepted']],
            ['confirm.accepted' => 'Periksa dan konfirmasi ringkasan simulasi sebelum membuat tiket.'],
        );

        $record = [
            'type' => $type,
            'program_slug' => $program['slug'] ?? null,
            'program_name' => $program['name'] ?? null,
            'profile' => $draftProfile,
            'operator_id' => DemoTickets::assignment($draftProfile),
            'reason' => isset($draft['reason']) ? ($reasons[$draft['reason']] ?? null) : null,
            'document' => $draft['document'],
            'history' => [],
        ];
        $record = DemoTickets::mark($record, 'SUBMITTED', 'Warga contoh');
        $session->put('demo.tickets.'.$ticket, $record);
        $session->forget($draftKey);

        return to_route('demo.ticket', $ticket);
    }
}
