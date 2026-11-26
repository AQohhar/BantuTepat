@extends('layouts.public')

@section('title', 'Masuk Demo Warga')

@section('content')
    <section class="page-section demo-access">
        <div class="site-container demo-login-grid">
            <div class="demo-login-intro">
                <h1>Masuk ke Ruang Warga</h1>
                <p class="intro">Lihat pengajuan, baca riwayat tiket, dan selesaikan permintaan perbaikan dalam simulasi BantuTepat.</p>
                <a class="text-link" href="{{ route('guide') }}">Baca panduan layanan</a>
            </div>

            <section class="panel demo-login-panel" aria-labelledby="demo-profile-heading">
                <div class="section-icon-heading">
                    <span class="shortcut-mark shortcut-ticket" aria-hidden="true"></span>
                    <h2 id="demo-profile-heading" class="demo-field-title">Profil warga contoh</h2>
                </div>
                @if (session('workspace_closed'))
                    <p class="notice" role="status">Ruang Warga ditutup. Tiket dan draf masih tersedia selama sesi browser aktif.</p>
                @endif
                <p>Gunakan profil simulasi yang tersedia. Akses demo ini tidak meminta email atau kata sandi.</p>
                <form method="POST" action="{{ route('demo.workspace.enter') }}">
                    @csrf
                    <button class="button" type="submit">Masuk sebagai warga contoh</button>
                </form>
                <p class="demo-session-note">Tiket dan draf terikat pada sesi browser ini. <a class="text-link" href="{{ route('privacy') }}#cookie">Baca tentang sesi demo</a>.</p>
            </section>
        </div>
    </section>
@endsection
