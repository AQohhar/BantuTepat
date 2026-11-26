@extends('layouts.public')

@section('title', $program['name'])

@section('content')
    @php
        $registrationAction = match (true) {
            $canDemoSelfApply => 'Siapkan berkas contoh untuk mulai',
            $program['mode'] === 'OFFICER_ONLY' => 'Ikuti jalur pengusulan melalui petugas',
            $program['status'] === 'UPCOMING' => 'Cek jadwal sebelum mengajukan',
            $program['mode'] === 'NO_PUBLIC_REGISTRATION' => 'Baca ketentuan dan mekanisme program',
            default => 'Periksa jalur pengajuan di panduan',
        };
    @endphp
    <div class="program-detail" data-program-tone="{{ $program['theme'] ?? 'blue' }}">
        <header class="program-intro">
            <div class="site-container program-header-inner">
                <a class="text-link back-link" href="{{ route('program.index') }}">Kembali ke katalog</a>
                <div @class(['program-intro-grid', 'program-intro-with-photo' => isset($program['photo'])])>
                    <div>
                        <div class="badge-row">
                            <span class="category-tag">{{ $program['category'] }}</span>
                            <x-public.program-status :status="$program['status']">{{ $statusLabel }}</x-public.program-status>
                        </div>
                        <h1>{{ $program['name'] }}</h1>
                        <p class="program-agency">{{ $program['agency'] }}</p>
                        <p class="intro">{{ $details['description'] }}</p>
                    </div>
                    <x-public.program-photo :program="$program" :decorative="false" :priority="true"
                        sizes="(min-width: 84rem) 540px, (min-width: 52rem) 43vw, 92vw" />
                </div>
            </div>
        </header>

        <div class="program-detail-nav" data-program-sections>
            <div class="site-container">
                <x-public.section-links label="Isi detail program" :links="[
                    '#informasi-program' => 'Informasi',
                    '#persyaratan' => 'Syarat',
                    '#dokumen' => 'Dokumen',
                    '#pendaftaran' => 'Daftar',
                ]" />
            </div>
        </div>

        <div class="page-section program-body-section">
            <div class="site-container detail-grid program-page-grid">
                <div class="panel-stack">
                    <section class="panel program-overview" id="informasi-program" tabindex="-1">
                        <h2>Informasi program</h2>
                        <dl class="program-highlights">
                            <div>
                                <dt><span class="fact-icon fact-icon-place" aria-hidden="true"></span>Wilayah sasaran</dt>
                                <dd>{{ $program['territory'] }}</dd>
                            </div>
                            <div>
                                <dt><span class="fact-icon fact-icon-date" aria-hidden="true"></span>Jadwal contoh</dt>
                                <dd>{{ $details['schedule'] }}</dd>
                            </div>
                            <div>
                                <dt><span class="fact-icon fact-icon-route" aria-hidden="true"></span>Jalur pendaftaran</dt>
                                <dd>{{ $modeLabel }}</dd>
                            </div>
                        </dl>
                        <dl class="program-references">
                            <div><dt>Kode program</dt><dd>{{ $details['code'] }}</dd></div>
                            <div><dt>Referensi ketentuan</dt><dd>{{ $details['policy_source'] }}</dd></div>
                        </dl>
                    </section>

                    <section class="panel program-checks" id="persyaratan" tabindex="-1">
                        <h2>Persyaratan program</h2>
                        <ul class="requirement-list" role="list">
                            @foreach ($details['requirements'] as $requirement)
                                <li>{{ $requirement }}</li>
                            @endforeach
                        </ul>
                    </section>

                    <section class="panel program-documents" id="dokumen" tabindex="-1">
                        <h2>Dokumen pendukung</h2>
                        <ul class="document-list" role="list">
                            @foreach ($details['documents'] as $document)
                                <li><span class="fact-icon fact-icon-document" aria-hidden="true"></span><span>{{ $document }}</span></li>
                            @endforeach
                        </ul>
                        <p class="notice">Gunakan dokumen fiktif saat mengikuti demonstrasi. Simpan NIK dan dokumen asli Anda di tempat yang aman.</p>
                    </section>
                </div>

                <aside class="panel detail-aside program-registration" id="pendaftaran" aria-labelledby="registration-heading" tabindex="-1">
                    <div class="registration-heading">
                        <span class="fact-icon fact-icon-route" aria-hidden="true"></span>
                        <h2 id="registration-heading">Informasi pendaftaran</h2>
                    </div>
                    <p class="registration-mode">{{ $modeLabel }}</p>
                    <p class="registration-action">{{ $registrationAction }}</p>
                    <p>{{ $registrationMessage }}</p>
                    @if (session('demo_unavailable'))
                        <p class="notice" role="status">Simulasi pendaftaran mandiri tidak tersedia untuk program ini. Baca jalur yang tercantum di atas.</p>
                    @endif
                    @if ($canDemoSelfApply)
                        <x-ui.action-link href="{{ route('demo.program', $program['slug']) }}">Coba simulasi pendaftaran</x-ui.action-link>
                        <p class="registration-note">Tiket contoh disimpan dalam sesi browser ini.</p>
                    @else
                        <x-ui.action-link href="{{ route('guide') }}#daftar-program" variant="outline">
                            {{ $program['mode'] === 'OFFICER_ONLY' ? 'Lihat panduan jalur petugas' : 'Baca panduan program' }}
                        </x-ui.action-link>
                    @endif
                </aside>
            </div>
        </div>
    </div>
@endsection
