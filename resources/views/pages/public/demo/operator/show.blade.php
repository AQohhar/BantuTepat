@extends('layouts.public')

@section('title', 'Detail Pemeriksaan Simulasi')

@section('content')
    <header class="page-intro workspace-intro case-intro">
        <div class="site-container">
            <nav class="review-page-links" aria-label="Navigasi pemeriksaan">
                <a class="text-link" href="{{ route('demo.operator.index') }}">Kembali ke antrean</a>
                <a class="text-link" href="#operator-action-heading">Ke tindakan</a>
                <a class="text-link" href="#demo-timeline-heading">Riwayat proses</a>
            </nav>
            <div class="badge-row">
                <x-public.demo-ticket-status :status="$record['status']" :label="$states[$record['status']]['label']" />
            </div>
            <h1>Detail pemeriksaan</h1>
            <p class="intro">{{ $record['program_name'] ?? $scenario['label'] }} · <strong class="ticket-number">{{ $ticket }}</strong></p>
        </div>
    </header>

    <div class="page-section demo-section">
        <div class="site-container detail-grid review-detail-grid">
            <div class="panel-stack">
                <section class="panel" aria-labelledby="operator-summary-heading">
                    <h2 id="operator-summary-heading" class="demo-field-title">Informasi pengajuan</h2>
                    <dl class="detail-facts">
                        <div><dt>Jenis pengajuan</dt><dd>{{ $scenario['label'] }}</dd></div>
                        <div><dt>Rumah tangga contoh</dt><dd>{{ $record['profile']['name'] }}</dd></div>
                        <div><dt>Wilayah</dt><dd>{{ $record['profile']['territory'] }}</dd></div>
                        @if ($record['reason'])
                            <div><dt>Alasan usulan</dt><dd>{{ $record['reason'] }}</dd></div>
                        @endif
                        <div><dt>Ditugaskan kepada</dt><dd>{{ $operator['name'] }}</dd></div>
                    </dl>
                </section>
                <section class="panel case-documents" aria-labelledby="operator-documents-heading">
                    <h2 id="operator-documents-heading" class="demo-field-title">Berkas yang tersedia</h2>
                    <ul class="content-list">
                        <li>{{ $record['document'] }}</li>
                        @if (isset($record['revision_document']))
                            <li>{{ $record['revision_document'] }} (bukti perbaikan)</li>
                        @endif
                    </ul>
                </section>
            </div>

            <aside class="panel detail-aside case-action" aria-labelledby="operator-action-heading">
                @switch($record['status'])
                    @case('SUBMITTED')
                        <h2 id="operator-action-heading" class="demo-field-title" tabindex="-1">Pemeriksaan awal</h2>
                        <p>Mulai pemeriksaan untuk membuka formulir permintaan perbaikan.</p>
                        <form method="POST" action="{{ route('demo.operator.start', $ticket) }}">
                            @csrf
                            <button class="button" type="submit">Mulai pemeriksaan</button>
                        </form>
                        @break

                    @case('UNDER_REVIEW')
                        <h2 id="operator-action-heading" class="demo-field-title" tabindex="-1">Minta perbaikan</h2>
                        <p>Gunakan tindakan ini jika warga perlu menambahkan atau memperbarui bukti contoh.</p>
                        <form class="operator-revision-form" method="POST" action="{{ route('demo.operator.request-revision', $ticket) }}">
                            @csrf
                            <fieldset class="demo-fields" @error('reason_code') aria-describedby="operator-reason-error" @enderror>
                                <legend class="operator-form-label">Alasan dan bukti yang diminta</legend>
                                @foreach ($scenario['revision_options'] as $code => $choice)
                                    <label class="demo-choice">
                                        <input type="radio" name="reason_code" value="{{ $code }}" required
                                            @checked(old('reason_code') === $code) @error('reason_code') aria-invalid="true" @enderror>
                                        <span><strong>{{ $choice['document'] }}</strong><span>{{ $choice['reason'] }}</span></span>
                                    </label>
                                @endforeach
                                @error('reason_code')<p id="operator-reason-error" class="field-error" role="alert">{{ $message }}</p>@enderror
                            </fieldset>
                            <div class="form-field">
                                <label for="operator-deadline">Tenggat perbaikan</label>
                                <input type="date" name="deadline" id="operator-deadline" required min="{{ $earliestDeadline }}"
                                    value="{{ old('deadline', $suggestedDeadline) }}"
                                    @error('deadline') aria-invalid="true" aria-describedby="operator-deadline-error" @enderror>
                                @error('deadline')<p id="operator-deadline-error" class="field-error" role="alert">{{ $message }}</p>@enderror
                            </div>
                            <button class="button" type="submit">Kirim permintaan perbaikan</button>
                        </form>
                        @break

                    @case('REVISION_REQUIRED')
                        <h2 id="operator-action-heading" class="demo-field-title" tabindex="-1">Menunggu perbaikan warga</h2>
                        <x-public.demo-revision-details :revision="$revision" />
                        <x-ui.action-link href="{{ route('demo.ticket', $ticket) }}" variant="outline">Buka tampilan warga</x-ui.action-link>
                        @break

                    @case('RESUBMITTED')
                        <h2 id="operator-action-heading" class="demo-field-title" tabindex="-1">Perbaikan diterima</h2>
                        <p>Bukti baru tersedia di daftar berkas pada tiket ini.</p>
                        <x-public.demo-revision-details :revision="$revision" />
                        <x-ui.action-link href="{{ route('demo.ticket', $ticket) }}" variant="outline">Buka tampilan warga</x-ui.action-link>
                        @break

                    @case('PENDING_APPROVAL')
                        <h2 id="operator-action-heading" class="demo-field-title" tabindex="-1">Rekomendasi dikirim</h2>
                        <p>Tiket menunggu tinjauan verifikator dari {{ $record['recommendation']['agency'] }}.</p>
                        <x-ui.action-link href="{{ route('demo.verifier.index') }}" variant="outline">Buka Ruang Verifikator</x-ui.action-link>
                        @break

                    @default
                        <h2 id="operator-action-heading" class="demo-field-title" tabindex="-1">Tinjauan selesai</h2>
                        <p>Hasil simulasi sudah tersedia pada tiket warga.</p>
                        <x-ui.action-link href="{{ route('demo.ticket', $ticket) }}" variant="outline">Buka tampilan warga</x-ui.action-link>
                @endswitch
                @if ($canRecommend)
                    <section class="review-handoff" aria-labelledby="operator-recommendation-heading">
                        <h3 id="operator-recommendation-heading">Rekomendasikan ke verifikator</h3>
                        <p>Gunakan langkah ini jika berkas contoh sudah siap ditinjau.</p>
                        <form class="operator-revision-form" method="POST" action="{{ route('demo.operator.recommend', $ticket) }}">
                            @csrf
                            <label class="demo-choice">
                                <input type="checkbox" name="reviewed" value="1" required
                                    @error('reviewed') aria-invalid="true" aria-describedby="reviewed-error" @enderror>
                                <span>Saya telah memeriksa berkas contoh.</span>
                            </label>
                            @error('reviewed')<p id="reviewed-error" class="field-error" role="alert">{{ $message }}</p>@enderror
                            <button class="button" type="submit">Kirim rekomendasi</button>
                        </form>
                    </section>
                @endif
            </aside>
            <x-public.demo-ticket-timeline :history="$record['history']" :states="$states" />
        </div>
    </div>
@endsection
