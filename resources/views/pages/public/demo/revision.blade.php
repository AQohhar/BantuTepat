@extends('layouts.public')

@section('title', 'Perbaikan Simulasi')

@section('content')
    <header class="page-intro case-intro">
        <div class="site-container">
            <a class="text-link back-link" href="{{ route('demo.ticket', $ticket) }}">Kembali ke tiket</a>
            <h1>Perbaikan simulasi</h1>
            <p class="intro">Nomor tiket <strong class="ticket-number">{{ $ticket }}</strong></p>
        </div>
    </header>

    <div class="page-section demo-section">
        <div class="site-container revision-layout">
            <section class="panel case-documents" aria-labelledby="revision-request-heading">
                <h2 id="revision-request-heading" class="demo-field-title">Permintaan perbaikan</h2>
                <x-public.demo-revision-details :revision="$revision" />
            </section>
            <section class="panel demo-revision-panel case-action" aria-labelledby="completion-heading">
                <h2 id="completion-heading" class="demo-field-title">Lengkapi bukti contoh</h2>
                <form method="POST" action="{{ route('demo.ticket.resubmit', $ticket) }}">
                    @csrf
                    <div class="form-field">
                        <label class="demo-choice">
                            <input type="checkbox" name="revision_document" value="1" required
                                @error('revision_document') aria-invalid="true" aria-describedby="revision-error" @enderror>
                            <span><strong>Tambahkan {{ Str::lower($revision['document']) }}</strong><span>Berkas contoh sudah disediakan.</span></span>
                        </label>
                        @error('revision_document')<p id="revision-error" class="field-error" role="alert">{{ $message }}</p>@enderror
                    </div>
                    <div class="actions">
                        <button class="button" type="submit">Kirim ulang simulasi</button>
                    </div>
                </form>
            </section>
        </div>
    </div>
@endsection
