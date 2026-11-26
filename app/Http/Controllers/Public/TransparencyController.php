<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TransparencyController extends Controller
{
    public function __invoke(Request $request): View
    {
        $demo = config('bantutepat_demo_transparency');
        $query = $request->only('period', 'territory');
        $filterErrors = Validator::make($query, [
            'period' => ['nullable', Rule::in(array_keys($demo['periods']))],
            'territory' => ['nullable', Rule::in(array_keys($demo['territories']))],
        ], ['in' => 'Pilih salah satu pilihan yang tersedia.'])->errors();

        $period = $filterErrors->has('period') ? null : ($query['period'] ?? null);
        $territory = $filterErrors->has('territory') ? null : ($query['territory'] ?? null);
        $period ??= array_key_first($demo['periods']);
        $territory ??= 'all';
        $minimum = $demo['minimum_group_size'];

        $visible = collect($demo['snapshots'][$period])->filter(
            fn (?array $counts) => $counts !== null && min($counts) >= $minimum
        );
        $counts = $visible->get($territory);
        if ($territory === 'all' && $visible->isNotEmpty()) {
            $counts = collect(array_keys($demo['status_labels']))->map(
                fn (int $position) => $visible->sum(fn (array $values) => $values[$position])
            )->all();
        }

        return view('pages.public.transparency', [
            'periods' => $demo['periods'],
            'territories' => $demo['territories'],
            'period' => $period,
            'territory' => $territory,
            'filterErrors' => $filterErrors,
            'summary' => $counts === null ? [] : array_combine($demo['status_labels'], $counts),
            'minimum' => $minimum,
        ]);
    }
}
