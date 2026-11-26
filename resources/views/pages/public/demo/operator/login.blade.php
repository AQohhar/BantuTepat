@extends('layouts.public')

@section('title', 'Masuk Demo Petugas')

@section('content')
    <section class="page-section demo-access">
        <div class="site-container demo-login-grid">
            <div class="demo-login-intro">
                <h1>Ruang Petugas Wilayah</h1>
                <p class="intro">Coba cara operator desa atau kelurahan memeriksa berkas contoh dan meminta perbaikan.</p>
                <a class="text-link" href="{{ route('demo.workspace.index') }}">Buka Ruang Warga</a>
            </div>
            <section class="panel demo-login-panel" aria-labelledby="operator-profile-heading">
                <div class="section-icon-heading">
                    <span class="shortcut-mark shortcut-document" aria-hidden="true"></span>
                    <h2 id="operator-profile-heading" class="demo-field-title">{{ $operator['name'] }}</h2>
                </div>
                <p class="demo-profile-scope"><strong>Wilayah penugasan</strong> {{ $operator['territory'] }}</p>
                <p>Setelah masuk, buka tiket yang ditugaskan dalam sesi ini untuk memeriksa berkas contoh.</p>
                <form method="POST" action="{{ route('demo.operator.enter') }}">
                    @csrf
                    <button class="button" type="submit">Masuk sebagai operator contoh</button>
                </form>
            </section>
        </div>
    </section>
@endsection
