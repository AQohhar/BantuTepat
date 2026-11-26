@extends('layouts.public')

@section('title', 'Masuk Demo Verifikator')

@section('content')
    <section class="page-section demo-access">
        <div class="site-container demo-login-grid">
            <div class="demo-login-intro">
                <h1>Ruang Verifikator</h1>
                <p class="intro">Tinjau rekomendasi operator, periksa berkas contoh, dan catat hasil tinjauan simulasi.</p>
                <a class="text-link" href="{{ route('demo.operator.index') }}">Buka Ruang Petugas</a>
            </div>
            <section class="panel demo-login-panel" aria-labelledby="verifier-demo-profile">
                <div class="section-icon-heading">
                    <span class="shortcut-mark shortcut-program" aria-hidden="true"></span>
                    <h2 id="verifier-demo-profile" class="demo-field-title">{{ $verifier['name'] }}</h2>
                </div>
                <p class="demo-profile-scope"><strong>{{ $verifier['agency'] }}</strong> {{ $verifier['territory'] }}</p>
                <p>Setelah masuk, buka antrean rekomendasi untuk meninjau tiket dalam sesi ini.</p>
                <form method="POST" action="{{ route('demo.verifier.enter') }}">
                    @csrf
                    <button class="button" type="submit">Masuk sebagai verifikator contoh</button>
                </form>
            </section>
        </div>
    </section>
@endsection
