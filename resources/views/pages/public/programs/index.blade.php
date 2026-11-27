@extends('layouts.public')

@section('title', 'Katalog Program')

@section('content')
    @php
        $activeFilters = array_filter($filters, fn ($value) => $value !== null && $value !== '');
        $filterNames = ['q' => 'Pencarian', 'territory' => 'Wilayah', 'status' => 'Status', 'mode' => 'Jalur'];
        $filterValues = ['status' => $statusLabels, 'mode' => $modeLabels];
    @endphp
    <header class="page-intro catalog-intro">
        <div class="site-container">
            <h1>Katalog program bantuan sosial</h1>
            <p class="intro">Bandingkan jadwal, wilayah sasaran, dan jalur pendaftaran. Buka detail untuk membaca persyaratannya.</p>
        </div>
    </header>

    <div class="page-section catalog-section">
        <div class="site-container catalog-layout">
            <details class="panel catalog-filter-shell" data-catalog-filters @if ($filterErrors->isNotEmpty()) data-filter-errors @endif open>
                <summary>Filter program @if ($activeFilters) <span class="badge badge-neutral">{{ count($activeFilters) }} aktif</span> @endif</summary>
                <form class="catalog-filters" method="GET" action="{{ route('program.index') }}#catalog-results-title" aria-labelledby="catalog-filter-title">
                    <h2 id="catalog-filter-title">Filter program</h2>
                    <div class="filter-grid">
                        <div class="form-field filter-search">
                            <label for="q">Cari program</label>
                            <input type="search" name="q" id="q" maxlength="100" placeholder="Contoh: pangan"
                                value="{{ $filters['q'] ?? '' }}"
                                @if ($filterErrors->has('q')) aria-invalid="true" aria-describedby="q-error" @endif>
                            @if ($filterErrors->has('q'))
                                <p class="field-error" id="q-error">{{ $filterErrors->first('q') }}</p>
                            @endif
                        </div>
                        <x-public.filter-select name="territory" label="Wilayah" empty="Semua wilayah"
                            :options="$territories->mapWithKeys(fn ($value) => [$value => $value])" :selected="$filters['territory'] ?? null" :errors="$filterErrors" />
                        <x-public.filter-select name="status" label="Status" empty="Semua status"
                            :options="$statusLabels" :selected="$filters['status'] ?? null" :errors="$filterErrors" />
                        <x-public.filter-select name="mode" label="Jalur pendaftaran" empty="Semua jalur"
                            :options="$modeLabels" :selected="$filters['mode'] ?? null" :errors="$filterErrors" />
                    </div>
                    <div class="actions">
                        <button class="button" type="submit">Terapkan filter</button>
                        <a class="text-link" href="{{ route('program.index') }}">Reset filter</a>
                    </div>
                </form>
            </details>

            <section class="catalog-results" aria-labelledby="catalog-results-title">
                <div class="results-heading">
                    <h2 id="catalog-results-title" tabindex="-1">Daftar program</h2>
                    <p role="status">{{ $programs->count() }} program ditemukan</p>
                </div>

                @if ($activeFilters)
                    <ul class="catalog-applied-filters" aria-label="Filter aktif">
                        @foreach ($activeFilters as $key => $value)
                            @php($displayValue = $filterValues[$key][$value] ?? $value)
                            <li>
                                <a href="{{ route('program.index', \Illuminate\Support\Arr::except($activeFilters, $key)) }}#catalog-results-title"
                                    aria-label="Hapus filter {{ $filterNames[$key] }} {{ $displayValue }}">
                                    <span>{{ $displayValue }}</span><span aria-hidden="true">×</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif

                <div class="cards catalog-cards">
                    @forelse ($programs as $program)
                        <article class="program-card" data-program-tone="{{ $program['theme'] ?? 'blue' }}">
                            <x-public.program-photo :program="$program"
                                sizes="(min-width: 84rem) 480px, (min-width: 64rem) 34vw, (min-width: 38rem) 46vw, 92vw" />
                            <div class="program-card-body">
                                <div>
                                    <div class="badge-row">
                                        <span class="category-tag">{{ $program['category'] }}</span>
                                        <x-public.program-status :status="$program['status']">{{ $statusLabels[$program['status']] }}</x-public.program-status>
                                    </div>
                                    <h3>{{ $program['name'] }}</h3>
                                    <p class="program-agency">{{ $program['agency'] }}</p>
                                    <p class="program-description">{{ $program['description'] }}</p>
                                </div>
                                <dl class="program-facts">
                                    <div><dt>Wilayah</dt><dd>{{ $program['territory'] }}</dd></div>
                                    <div class="program-fact-period">
                                        <dt>Periode pendaftaran</dt>
                                        <dd>{{ $programDetails[$program['slug']]['schedule'] ?? 'Jadwal belum tersedia' }}</dd>
                                    </div>
                                    <div><dt>Jalur pendaftaran</dt><dd>{{ $modeLabels[$program['mode']] }}</dd></div>
                                </dl>
                                <x-ui.action-link href="{{ route('program.show', $program['slug']) }}" variant="outline">
                                    Lihat detail program <span class="sr-only">{{ $program['name'] }}</span>
                                </x-ui.action-link>
                            </div>
                        </article>
                    @empty
                        <div class="panel empty-state">
                            <h3>Program tidak ditemukan</h3>
                            @if ($activeFilters)
                                <p>Ubah pencarian Anda atau kosongkan filter untuk kembali ke daftar lengkap.</p>
                                <x-ui.action-link href="{{ route('program.index') }}">Hapus semua filter</x-ui.action-link>
                            @else
                                <p>Program simulasi akan tampil di sini saat tersedia.</p>
                            @endif
                        </div>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
@endsection
