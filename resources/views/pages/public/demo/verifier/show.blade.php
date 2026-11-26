@extends('layouts.public')

@section('title', 'Detail Tinjauan Simulasi')

@section('content')
    <header class="page-intro workspace-intro case-intro">
        <div class="site-container">
            <nav class="review-page-links" aria-label="Navigasi tinjauan">
                <a class="text-link" href="{{ route('demo.verifier.index', isset($record['decision']) ? ['tampilan' => 'hasil'] : []) }}">Kembali ke antrean tinjauan</a>
                <a class="text-link" href="#verifier-action-heading">{{ isset($record['decision']) ? 'Ke hasil tinjauan' : 'Ke formulir tinjauan' }}</a>
                <a class="text-link" href="#demo-timeline-heading">Riwayat proses</a>
            </nav>
            <div class="badge-row">
                <x-public.demo-ticket-status :status="$record['status']" :label="$states[$record['status']]['label']" />
            </div>
            <h1>Detail tinjauan</h1>
            <p class="intro">{{ $record['program_name'] ?? $scenario['label'] }} · <strong class="ticket-number">{{ $ticket }}</strong></p>
        </div>
    </header>

    <div class="page-section demo-section">
        <div class="site-container detail-grid review-detail-grid verifier-case-grid">
            <div class="panel-stack">
                <section class="panel case-documents" aria-labelledby="recommendation-heading">
                    <h2 id="recommendation-heading" class="demo-field-title">Rekomendasi operator</h2>
                    <p>{{ $record['recommendation']['note'] }}</p>
                    <dl class="detail-facts">
                        <div><dt>Jenis pengajuan</dt><dd>{{ $scenario['label'] }}</dd></div>
                        <div><dt>Wilayah</dt><dd>{{ $record['profile']['territory'] }}</dd></div>
                        <div><dt>Operator</dt><dd>{{ $record['recommendation']['operator_name'] }}</dd></div>
                        <div><dt>Dasar tinjauan</dt><dd>{{ $record['recommendation']['basis'] }}</dd></div>
                        <div><dt>Berkas awal</dt><dd>{{ $record['document'] }}</dd></div>
                        @if (isset($record['revision_document']))
                            <div><dt>Bukti perbaikan</dt><dd>{{ $record['revision_document'] }}</dd></div>
                        @endif
                    </dl>
                </section>
            </div>

            <aside class="panel detail-aside case-action" aria-labelledby="verifier-action-heading">
                <h2 id="verifier-action-heading" class="demo-field-title" tabindex="-1">{{ isset($record['decision']) ? 'Hasil tinjauan simulasi' : 'Catat hasil tinjauan' }}</h2>
                @if (isset($record['decision']))
                    <x-public.demo-review-result :decision="$record['decision']" :label="$states[$record['status']]['label']" />
                    <x-ui.action-link href="{{ route('demo.ticket', $ticket) }}" variant="outline">Buka tampilan warga</x-ui.action-link>
                @else
                    <p>Pilih hasil dan alasan yang sesuai dengan skenario. Hasil memakai berkas fiktif.</p>
                    <form class="operator-revision-form" method="POST" action="{{ route('demo.verifier.decide', $ticket) }}">
                        @csrf
                        <fieldset class="demo-fields" @error('result') aria-describedby="review-option-error" @enderror>
                            <legend class="operator-form-label">Pilihan hasil</legend>
                            @foreach ($options as $code => $option)
                                <label class="demo-choice">
                                    <input type="radio" name="result" value="{{ $code }}" required @checked(old('result') === $code)
                                        @error('result') aria-invalid="true" @enderror>
                                    <span><strong>{{ $states[$option['status']]['label'] }}</strong><span>{{ $option['reason'] }}</span></span>
                                </label>
                            @endforeach
                            @error('result')<p id="review-option-error" class="field-error" role="alert">{{ $message }}</p>@enderror
                        </fieldset>
                        <label class="demo-choice">
                            <input type="checkbox" name="confirmed" value="1" required @checked(old('confirmed') === '1')
                                @error('confirmed') aria-invalid="true" aria-describedby="review-confirm-error" @enderror>
                            <span>Saya telah meninjau berkas dan alasan hasil simulasi.</span>
                        </label>
                        @error('confirmed')<p id="review-confirm-error" class="field-error" role="alert">{{ $message }}</p>@enderror
                        <button class="button" type="submit">Simpan hasil tinjauan</button>
                    </form>
                @endif
            </aside>
            <x-public.demo-ticket-timeline :history="$record['history']" :states="$states" />
        </div>
    </div>
@endsection
