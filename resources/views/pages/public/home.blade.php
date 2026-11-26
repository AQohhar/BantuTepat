@extends('layouts.public')

@section('title', 'Beranda')

@section('content')
    <section class="site-container home-hero">
        <div class="hero-grid">
            <div class="hero-copy">
                <h1>Cari bantuan sosial <span>yang sesuai.</span></h1>
                <p class="intro">Cari program di wilayah Anda, periksa syaratnya, lalu pilih jalur pengajuan yang tersedia.</p>

                <form class="hero-search" action="{{ route('program.index') }}" method="GET" role="search" aria-label="Cari program bantuan sosial">
                    <label class="sr-only" for="home-search">Cari nama program atau wilayah</label>
                    <span class="search-glyph" aria-hidden="true"></span>
                    <input id="home-search" type="search" name="q" maxlength="100" placeholder="Program, wilayah">
                    <button class="button" type="submit">Cari program</button>
                </form>

                <a class="hero-guide-link" href="{{ route('guide') }}">Baca panduan layanan</a>
            </div>
            <figure class="hero-media">
                <img class="hero-image" src="{{ asset('layanan-warga.jpg') }}" width="1448" height="1086"
                    alt="Ilustrasi petugas layanan menjelaskan dokumen kepada warga" fetchpriority="high">
                <figcaption>Ilustrasi layanan warga</figcaption>
            </figure>
        </div>
    </section>

    <section id="layanan" class="site-container home-shortcuts" aria-labelledby="quick-services-heading">
        <h2 id="quick-services-heading">Apa yang ingin Anda lakukan?</h2>
        <div class="service-shortcuts">
            <a class="service-start" href="{{ route('program.index') }}">
                <span class="shortcut-mark shortcut-program" aria-hidden="true"></span>
                <span><strong>Jelajahi program</strong><small>Bandingkan bantuan, syarat, dan jadwal di wilayah Anda.</small></span>
            </a>
            <a href="{{ route('guide') }}#usulan-data">
                <span class="shortcut-mark shortcut-document" aria-hidden="true"></span>
                <span><strong>Usulkan atau koreksi data</strong><small>Gunakan jalur ini jika data rumah tangga perlu diperbarui.</small></span>
            </a>
            <a href="{{ route('demo.workspace.index') }}">
                <span class="shortcut-mark shortcut-ticket" aria-hidden="true"></span>
                <span><strong>Pantau tiket</strong><small>Lihat hasil atau permintaan perbaikan di Ruang Warga.</small></span>
            </a>
        </div>
    </section>

    @if ($activePrograms->isNotEmpty())
        <section class="page-section featured-programs">
            <div class="site-container">
                <div class="section-heading section-heading-inline">
                    <div>
                        <h2>Program dengan periode aktif</h2>
                        <p>Lihat cakupan wilayah dan jalur pendaftaran setiap program simulasi.</p>
                    </div>
                    <x-ui.action-link href="{{ route('program.index') }}" variant="outline">Lihat semua program</x-ui.action-link>
                </div>
                <div class="cards cards-two">
                    @foreach ($activePrograms as $program)
                        <article class="featured-program" data-program-tone="{{ $program['theme'] ?? 'blue' }}">
                            <x-public.program-photo :program="$program" sizes="(min-width: 84rem) 600px, (min-width: 38rem) 46vw, 92vw" />
                            <div class="featured-copy">
                                <p class="featured-meta"><span class="category-tag">{{ $program['category'] }}</span><span>{{ $program['territory'] }}</span></p>
                                <h3><a href="{{ route('program.show', $program['slug']) }}">{{ $program['name'] }}</a></h3>
                                <p class="featured-mode">{{ $modeLabels[$program['mode']] }}</p>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <section id="alur" class="page-section section-tinted">
        <div class="site-container home-process">
            <div>
                <h2>Dari pengajuan hingga tindak lanjut</h2>
                <p class="service-intro">Simpan nomor tiket untuk melihat riwayat pemeriksaan dan bukti tambahan yang mungkin diminta.</p>
                <div class="actions">
                    <x-ui.action-link href="{{ route('guide') }}" variant="outline">Baca panduan pengajuan</x-ui.action-link>
                </div>
            </div>

            <div class="process-panel" data-motion-reveal="panel">
                <ol class="home-steps" role="list">
                    <li><span class="home-step-number" aria-hidden="true">1</span><strong>Pilih jenis layanan</strong><p>Pilih program atau siapkan usulan pembaruan data sosial ekonomi.</p></li>
                    <li><span class="home-step-number" aria-hidden="true">2</span><strong>Ikuti pemeriksaan</strong><p>Petugas memeriksa data dan meminta perbaikan bila diperlukan.</p></li>
                    <li><span class="home-step-number" aria-hidden="true">3</span><strong>Baca hasil dan alasannya</strong><p>Hasil menjelaskan dasar pemeriksaan dan jalur sanggahan yang tersedia.</p></li>
                </ol>
            </div>
        </div>
    </section>

    <section id="transparansi" class="page-section">
        <div class="site-container">
            <div class="feature-strip" data-motion-reveal="panel">
                <div>
                    <h2>Transparansi layanan</h2>
                    <p>Bandingkan status penanganan berdasarkan periode dan wilayah yang memenuhi batas minimum data.</p>
                </div>
                <x-ui.action-link href="{{ route('transparency') }}" variant="outline">Lihat ringkasan penanganan</x-ui.action-link>
            </div>
        </div>
    </section>
@endsection
