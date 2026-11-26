<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Support\DemoProgramRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProgramCatalogController extends Controller
{
    private const STATUS_LABELS = [
        'OPEN' => 'Periode aktif',
        'UPCOMING' => 'Belum dibuka',
        'INFO_ONLY' => 'Informasi saja',
    ];

    private const MODE_LABELS = [
        'SELF' => 'Mandiri',
        'BOTH' => 'Mandiri atau melalui petugas',
        'OFFICER_ONLY' => 'Hanya melalui petugas',
        'NO_PUBLIC_REGISTRATION' => 'Tanpa pendaftaran umum',
    ];

    public function home(): View
    {
        $activePrograms = collect(config('bantutepat_demo_programs', []))
            ->where('status', 'OPEN')
            ->take(2);

        return view('pages.public.home', [
            'activePrograms' => $activePrograms,
            'modeLabels' => self::MODE_LABELS,
        ]);
    }

    public function index(Request $request): View
    {
        $catalog = collect(config('bantutepat_demo_programs', []));
        $territories = $catalog->pluck('territory')->unique()->sort()->values();
        $categories = $catalog->pluck('category')->unique()->sort()->values();

        $rules = [
            'q' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', Rule::in(array_keys(self::STATUS_LABELS))],
            'mode' => ['nullable', Rule::in(array_keys(self::MODE_LABELS))],
            'territory' => ['nullable', Rule::in($territories->all())],
            'category' => ['nullable', Rule::in($categories->all())],
        ];
        $query = $request->only(array_keys($rules));
        $filterErrors = Validator::make($query, $rules, [
            'q.string' => 'Masukkan kata kunci dalam bentuk teks.',
            'q.max' => 'Gunakan kata kunci paling panjang 100 karakter.',
            'in' => 'Pilih salah satu pilihan yang tersedia.',
        ])->errors();
        $filters = Arr::except($query, $filterErrors->keys());

        $search = Str::lower(trim($filters['q'] ?? ''));
        $programs = $catalog->filter(function (array $program) use ($search, $filters) {
            $searchableText = Str::lower(implode(' ', Arr::only($program, [
                'name', 'agency', 'territory', 'description', 'category',
            ])));

            return ($search === '' || Str::contains($searchableText, $search))
                && $program['status'] === ($filters['status'] ?? $program['status'])
                && $program['mode'] === ($filters['mode'] ?? $program['mode'])
                && $program['territory'] === ($filters['territory'] ?? $program['territory'])
                && $program['category'] === ($filters['category'] ?? $program['category']);
        })->values();

        return view('pages.public.programs.index', [
            'programs' => $programs,
            'programDetails' => config('bantutepat_demo_program_details', []),
            'filters' => $filters,
            'filterErrors' => $filterErrors,
            'statusLabels' => self::STATUS_LABELS,
            'modeLabels' => self::MODE_LABELS,
            'territories' => $territories,
            'categories' => $categories,
        ]);
    }

    public function show(string $slug): View
    {
        $program = collect(config('bantutepat_demo_programs', []))->firstWhere('slug', $slug);
        $details = config('bantutepat_demo_program_details.'.$slug);

        abort_if($program === null || ! is_array($details), 404);

        $registrationMessage = match (true) {
            $program['mode'] === 'NO_PUBLIC_REGISTRATION' => 'Halaman ini menyediakan syarat dan informasi mekanisme penyelenggara. Program tidak membuka pendaftaran umum.',
            $program['status'] === 'UPCOMING' => 'Periode pendaftaran simulasi belum dibuka. Lihat jadwal dan siapkan persyaratan yang tercantum.',
            $program['mode'] === 'OFFICER_ONLY' => 'Dalam alur simulasi, petugas yang berwenang mengusulkan warga sesuai wilayah tugasnya.',
            $program['mode'] === 'BOTH' => 'Alur simulasi menyediakan dua jalur, yaitu pengajuan mandiri dan pengusulan melalui petugas. Keduanya mengikuti persyaratan wilayah program.',
            $program['mode'] === 'SELF' => 'Alur simulasi menyediakan pengajuan mandiri bagi warga di wilayah sasaran yang memenuhi ketentuan program.',
            default => 'Periksa periode dan ketentuan program sebelum menentukan jalur pengajuan.',
        };

        return view('pages.public.programs.show', [
            'program' => $program,
            'details' => $details,
            'modeLabel' => self::MODE_LABELS[$program['mode']],
            'statusLabel' => self::STATUS_LABELS[$program['status']],
            'registrationMessage' => $registrationMessage,
            'canDemoSelfApply' => DemoProgramRegistration::allowsSelfApplication($program),
        ]);
    }
}
