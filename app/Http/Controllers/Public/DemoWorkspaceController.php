<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Support\DemoTickets;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DemoWorkspaceController extends Controller
{
    public function login(Request $request): View|RedirectResponse
    {
        if ($request->session()->get('demo.workspace.active')) {
            return to_route('demo.workspace.index');
        }

        return view('pages.public.demo.login');
    }

    public function enter(Request $request): RedirectResponse
    {
        $request->session()->put('demo.workspace.active', true);

        return to_route('demo.workspace.index');
    }

    public function leave(Request $request): RedirectResponse
    {
        $request->session()->forget('demo.workspace.active');

        return to_route('demo.workspace.login')->with('workspace_closed', true);
    }

    public function index(Request $request): View|RedirectResponse
    {
        if (! $request->session()->get('demo.workspace.active')) {
            return to_route('demo.workspace.login');
        }

        $states = config('bantutepat_demo_applications.states');
        $scenarios = config('bantutepat_demo_applications.scenarios');
        $statusLabels = array_combine(array_keys($states), array_column($states, 'label'));
        $typeLabels = array_combine(array_keys($scenarios), array_column($scenarios, 'label'));

        $query = $request->only('q', 'type', 'status');
        $filterErrors = Validator::make($query, [
            'q' => ['nullable', 'string', 'max:100'],
            'type' => ['nullable', Rule::in(array_keys($typeLabels))],
            'status' => ['nullable', Rule::in(array_keys($statusLabels))],
        ], [
            'q.string' => 'Gunakan teks untuk mencari tiket.',
            'q.max' => 'Gunakan paling banyak 100 karakter.',
            'in' => 'Pilih salah satu pilihan yang tersedia.',
        ])->errors();
        $filters = Arr::except($query, $filterErrors->keys());
        $keyword = Str::lower(trim($filters['q'] ?? ''));

        $tickets = collect($request->session()->get('demo.tickets', []));
        $matches = $tickets->filter(function (array $ticket, string $number) use ($filters, $keyword, $typeLabels) {
            $searchable = Str::lower(implode(' ', [
                $number,
                $ticket['program_name'] ?? $typeLabels[$ticket['type']],
            ]));

            return ($keyword === '' || Str::contains($searchable, $keyword))
                && $ticket['type'] === ($filters['type'] ?? $ticket['type'])
                && $ticket['status'] === ($filters['status'] ?? $ticket['status']);
        })->sortByDesc(fn (array $ticket) => Arr::last($ticket['history'])['at']);

        return view('pages.public.demo.workspace', [
            'tickets' => $matches,
            'total' => $tickets->count(),
            'revisionCount' => $tickets->where('status', 'REVISION_REQUIRED')->count(),
            'resubmittedCount' => $tickets->where('status', 'RESUBMITTED')->count(),
            'typeLabels' => $typeLabels,
            'statusLabels' => $statusLabels,
            'filters' => $filters,
            'filterErrors' => $filterErrors,
        ]);
    }

    public function loadExamples(Request $request): RedirectResponse
    {
        $session = $request->session();

        if (! $session->get('demo.workspace.active')) {
            return to_route('demo.workspace.login');
        }

        if ($session->get('demo.tickets', []) !== []) {
            return to_route('demo.workspace.index');
        }

        $demo = config('bantutepat_demo_applications');
        $programs = collect(config('bantutepat_demo_programs', []))->keyBy('slug');
        $at = now('Asia/Jakarta')->toIso8601String();
        foreach ($demo['workspace_examples'] as $example) {
            $scenario = $demo['scenarios'][$example['type']];
            $program = $programs->get($example['program_slug'] ?? '');
            $history = array_map(fn (string $status) => [
                'status' => $status,
                'at' => $at,
                'actor' => 'Warga contoh',
            ], $example['history']);
            $ticket = $scenario['ticket_prefix'].Str::upper(Str::random(12));

            $session->put('demo.tickets.'.$ticket, [
                'type' => $example['type'],
                'program_slug' => $program['slug'] ?? null,
                'program_name' => $program['name'] ?? null,
                'profile' => $demo['profiles'][$example['profile']],
                'operator_id' => DemoTickets::assignment($demo['profiles'][$example['profile']]),
                'reason' => $demo['proposal_reasons'][$example['reason'] ?? ''] ?? null,
                'document' => $scenario['initial_document'],
                'status' => Arr::last($example['history']),
                'history' => $history,
                'example' => true,
            ]);
        }

        return to_route('demo.workspace.index')->with('examples_loaded', true);
    }
}
