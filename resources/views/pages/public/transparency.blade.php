@extends('layouts.public')

@section('title', 'Transparansi')

@section('content')
    <header class="page-intro transparency-intro">
        <div class="site-container">
            <h1>Transparansi layanan</h1>
            <p class="intro">Lihat ringkasan status penanganan usulan data dan pendaftaran program dari data simulasi.</p>
            <a class="text-link transparency-method-link" href="#statistik-metode">Baca sumber dan metode</a>
        </div>
    </header>

    <section class="page-section catalog-section">
        <div class="site-container">
            <p class="simulation-data-note notice" role="note">Angka pada halaman ini adalah data sintetis untuk simulasi, bukan statistik layanan operasional.</p>
            <form class="panel filter-panel statistics-filters" id="statistik-filter" tabindex="-1" method="GET" action="{{ route('transparency') }}" aria-label="Filter statistik">
                <div class="filter-grid filter-grid-compact">
                    <x-public.filter-select name="period" label="Periode" :options="$periods" :selected="$period" :errors="$filterErrors" />
                    <x-public.filter-select name="territory" label="Wilayah" :options="$territories" :selected="$territory" :errors="$filterErrors" />
                </div>
                <div class="actions">
                    <button class="button" type="submit">Tampilkan statistik</button>
                    <a class="text-link" href="{{ route('transparency') }}">Reset filter</a>
                </div>
            </form>

            <div class="results-heading statistics-heading">
                <h2>Ringkasan penanganan</h2>
                <ul class="statistics-scope" aria-label="Cakupan data">
                    <li>{{ $periods[$period] }}</li>
                    <li>{{ $territories[$territory] }}</li>
                </ul>
            </div>

            @if ($summary)
                @php($total = array_sum($summary))
                <div class="content-grid statistics-grid">
                    <dl class="stat-total">
                        <dt>Total kasus ditampilkan</dt>
                        <dd>{{ number_format($total, 0, ',', '.') }}</dd>
                    </dl>
                    <section class="panel distribution" aria-labelledby="status-heading" data-motion-reveal="chart">
                        <h3 id="status-heading">Kasus menurut status</h3>
                        <dl>
                            @foreach ($summary as $label => $count)
                                @php($share = $total > 0 ? round(100 * $count / $total) : 0)
                                <div class="distribution-item">
                                    <dt>{{ $label }}</dt>
                                    <dd>{{ number_format($count, 0, ',', '.') }} <span class="metric-share">{{ $share }}%</span></dd>
                                    <dd class="metric-track" aria-hidden="true">
                                        <span style="width: {{ $share }}%"></span>
                                    </dd>
                                </div>
                            @endforeach
                        </dl>
                        <p class="distribution-note">“Penanganan selesai” berarti proses pemeriksaan telah berakhir. Baca <a class="text-link" href="#statistik-metode">sumber dan metode</a> untuk memahami cakupan angka.</p>
                    </section>
                </div>
            @else
                <div class="panel empty-state statistics-unavailable" role="status">
                    <h3>Statistik wilayah belum dapat ditampilkan</h3>
                    <p>Jumlah kasus dalam satu atau lebih kelompok terlalu kecil. Pilih wilayah atau periode lain untuk melihat ringkasan.</p>
                    @if ($territory !== 'all')
                        <x-ui.action-link href="{{ route('transparency', ['period' => $period]) }}" variant="outline">Lihat ringkasan gabungan</x-ui.action-link>
                    @else
                        <x-ui.action-link href="#statistik-filter" variant="outline">Pilih periode lain</x-ui.action-link>
                    @endif
                </div>
            @endif

            <section class="panel methodology" id="statistik-metode" tabindex="-1">
                <h2>Sumber dan cara membaca angka</h2>
                <p>Angka berasal dari dataset sintetis BantuTepat. Setiap kasus dihitung satu kali berdasarkan statusnya pada periode yang dipilih.</p>
                <p>Jika salah satu kelompok status di suatu wilayah berisi kurang dari {{ $minimum }} kasus, statistik wilayah tersebut disembunyikan dan dikecualikan dari ringkasan gabungan.</p>
            </section>
        </div>
    </section>
@endsection
