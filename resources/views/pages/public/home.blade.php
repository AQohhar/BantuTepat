@extends('layouts.public')

@section('title', 'Beranda')

@section('content')
    <section class="site-container home-hero" aria-labelledby="home-title">
        <div class="hero-grid">
            <div class="hero-copy">
                <p class="home-eyebrow">Portal informasi bantuan sosial</p>
                <h1 id="home-title">Temukan jalur bantuan yang <span>tepat.</span></h1>
                <p class="intro">Telusuri program, pahami persyaratannya, dan pilih jalur layanan yang sesuai dengan kebutuhan Anda.</p>

                <div class="home-hero-actions">
                    <x-ui.action-link href="{{ route('program.index') }}">Lihat program</x-ui.action-link>
                    <a class="text-link" href="{{ route('guide') }}">Pelajari cara pengajuan</a>
                </div>

                <form class="hero-search" action="{{ route('program.index') }}" method="GET" role="search" aria-label="Cari program bantuan sosial">
                    <label class="sr-only" for="home-search">Cari nama program atau wilayah</label>
                    <input id="home-search" type="search" name="q" maxlength="100" placeholder="Program, wilayah">
                    <button class="button" type="submit">Cari program</button>
                </form>
            </div>
            <figure class="hero-media">
                <img class="hero-image" src="{{ asset('layanan-warga.jpg') }}" width="1448" height="1086"
                    alt="Ilustrasi petugas layanan menjelaskan informasi program kepada warga" fetchpriority="high" decoding="async">
                <figcaption>Ilustrasi layanan warga</figcaption>
            </figure>
        </div>
    </section>

    <section id="layanan" class="site-container home-shortcuts" aria-labelledby="quick-services-heading">
        <div class="home-section-heading">
            <p class="home-eyebrow">Pilih jalur layanan</p>
            <h2 id="quick-services-heading">Mulai dari kebutuhan Anda</h2>
            <p>Setiap jalur memiliki persyaratan dan langkah yang berbeda.</p>
        </div>
        <div class="service-shortcuts">
            <a class="service-path-card service-start" href="{{ route('program.index') }}">
                <span class="path-card-number" aria-hidden="true">01</span>
                <span class="path-card-copy"><strong>Cari program bantuan</strong><small>Bandingkan cakupan, persyaratan, dan periode program yang tersedia.</small><span class="path-card-action">Buka katalog program <span aria-hidden="true">→</span></span></span>
            </a>
            <a class="service-path-card" href="{{ route('guide') }}#usulan-data">
                <span class="path-card-number path-card-number-teal" aria-hidden="true">02</span>
                <span class="path-card-copy"><strong>Perbarui data sosial ekonomi</strong><small>Pelajari jalur usulan jika data rumah tangga belum tercatat atau perlu dikoreksi.</small><span class="path-card-action">Pahami jalur usulan <span aria-hidden="true">→</span></span></span>
            </a>
        </div>
    </section>

    @if ($activePrograms->isNotEmpty())
        <section class="page-section featured-programs">
            <div class="site-container">
                <div class="section-heading section-heading-inline">
                    <div>
                        <h2>Program dengan periode aktif</h2>
                        <p>Lihat cakupan wilayah, persyaratan, dan jalur pendaftaran program simulasi.</p>
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
                    <li><span class="home-step-number" aria-hidden="true">1</span><strong>Pahami persyaratan</strong><p>Baca wilayah, periode, dokumen, dan cara pendaftaran yang berlaku pada program.</p></li>
                    <li><span class="home-step-number" aria-hidden="true">2</span><strong>Ikuti jalur yang sesuai</strong><p>Pendaftaran program dan usulan pembaruan data adalah dua proses yang berbeda.</p></li>
                    <li><span class="home-step-number" aria-hidden="true">3</span><strong>Pantau tindak lanjut</strong><p>Dalam simulasi, nomor tiket digunakan untuk melihat riwayat pemeriksaan.</p></li>
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
