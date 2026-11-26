@extends('layouts.public')

@section('title', 'Tiket Simulasi')

@section('content')
    @php($latest = \Illuminate\Support\Arr::last($record['history']))
    <header class="page-intro case-intro">
        <div class="site-container">
            @if (session('demo.workspace.active'))
                <a class="text-link back-link" href="{{ route('demo.workspace.index') }}">Kembali ke Pengajuan Saya</a>
            @endif
            <div class="badge-row">
                <x-public.demo-ticket-status :status="$record['status']" :label="$states[$record['status']]['label']" />
            </div>
            <h1>Tiket simulasi</h1>
            <p class="intro">Nomor tiket <strong class="ticket-number">{{ $ticket }}</strong></p>
            <p class="ticket-update">Pembaruan terakhir <time datetime="{{ $latest['at'] }}">{{ \Illuminate\Support\Carbon::parse($latest['at'])->timezone('Asia/Jakarta')->format('d/m/Y H:i') }} WIB</time></p>
            <nav class="review-page-links" aria-label="Isi tiket">
                <a class="text-link" href="#demo-next-action">{{ isset($record['decision']) ? 'Lihat hasil tinjauan' : 'Lihat langkah berikutnya' }}</a>
                <a class="text-link" href="#demo-timeline-heading">Lihat riwayat proses</a>
            </nav>
        </div>
    </header>

    <div class="page-section demo-section">
        <div class="site-container detail-grid action-first-grid">
            <div class="panel-stack">
                <section class="panel" aria-labelledby="demo-summary">
                    <h2 id="demo-summary" class="demo-field-title">Ringkasan tiket</h2>
                    <dl class="detail-facts">
                        <div><dt>Jenis pengajuan</dt><dd>{{ $scenario['label'] }}</dd></div>
                        @if ($record['program_name'])
                            <div><dt>Program</dt><dd>{{ $record['program_name'] }}</dd></div>
                        @endif
                        <div><dt>Rumah tangga</dt><dd>{{ $record['profile']['name'] }}</dd></div>
                        <div><dt>Wilayah</dt><dd>{{ $record['profile']['territory'] }}</dd></div>
                        @if ($record['reason'])
                            <div><dt>Alasan usulan</dt><dd>{{ $record['reason'] }}</dd></div>
                        @endif
                        <div><dt>Berkas contoh</dt><dd>{{ $record['document'] }}</dd></div>
                        @if (isset($record['revision_document']))
                            <div><dt>Bukti perbaikan</dt><dd>{{ $record['revision_document'] }}</dd></div>
                        @endif
                    </dl>
                    <p class="ticket-session-note">Simpan tautan halaman ini untuk membuka kembali tiket selama sesi browser ini masih aktif.</p>
                </section>
                <x-public.demo-ticket-timeline :history="$record['history']" :states="$states" />
            </div>

            <aside class="panel detail-aside case-action" aria-labelledby="demo-next-action">
                @if (isset($record['decision']))
                    <h2 id="demo-next-action" class="demo-field-title" tabindex="-1">Hasil tinjauan simulasi</h2>
                    <x-public.demo-review-result :decision="$record['decision']" :label="$states[$record['status']]['label']" />
                    <x-ui.action-link href="{{ route('guide').'#hasil-dan-sanggahan' }}" variant="outline">Baca panduan tindak lanjut</x-ui.action-link>
                @else
                    <h2 id="demo-next-action" class="demo-field-title" tabindex="-1">Langkah berikutnya</h2>
                @endif
                @if (in_array($record['status'], ['SUBMITTED', 'UNDER_REVIEW'], true))
                    @if ($operatorAvailable)
                        <p>{{ $record['status'] === 'SUBMITTED' ? 'Tiket menunggu pemeriksaan awal operator.' : 'Operator sedang memeriksa berkas contoh.' }}</p>
                        <x-ui.action-link href="{{ route('demo.operator.index') }}" variant="outline">Buka Ruang Petugas demo</x-ui.action-link>
                    @else
                        <p>Tiket ini tersimpan. Penugasan operator contoh tersedia untuk {{ $operatorTerritory }}.</p>
                    @endif
                @elseif ($record['status'] === 'REVISION_REQUIRED')
                    <x-public.demo-revision-details :revision="$revision" />
                    <x-ui.action-link href="{{ route('demo.ticket.revision', $ticket) }}">Lengkapi perbaikan</x-ui.action-link>
                @elseif ($record['status'] === 'RESUBMITTED')
                    <p>Bukti perbaikan tersimpan pada tiket ini. Operator dapat meninjaunya sebelum mengirim rekomendasi.</p>
                    @if ($operatorAvailable)
                        <x-ui.action-link href="{{ route('demo.operator.index') }}" variant="outline">Buka Ruang Petugas demo</x-ui.action-link>
                    @endif
                @elseif ($record['status'] === 'PENDING_APPROVAL')
                    <p>Operator telah mengirim rekomendasi. Hasil akan tampil di tiket ini setelah verifikator menyelesaikan tinjauan simulasi.</p>
                    <x-ui.action-link href="{{ route('demo.verifier.index') }}" variant="outline">Buka Ruang Verifikator demo</x-ui.action-link>
                @endif
            </aside>
        </div>
    </div>
@endsection
