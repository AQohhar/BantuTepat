@extends('layouts.public')

@section('title', 'Halaman tidak ditemukan')

@section('content')
    @php($ticketPage = request()->is('demo/pengajuan/*'))
    <div class="page-section">
        <div class="site-container">
            <section class="panel error-recovery" aria-labelledby="recovery-title">
                <span class="shortcut-mark shortcut-program" aria-hidden="true"></span>
                <h1 id="recovery-title">Halaman tidak ditemukan</h1>
                <p>{{ $ticketPage
                    ? 'Buka Ruang Warga untuk memeriksa tiket yang tersedia di sesi demo Anda.'
                    : 'Periksa alamat halaman atau kembali ke katalog untuk mencari program yang tersedia.' }}</p>
                <div class="actions">
                    <x-ui.action-link href="{{ route($ticketPage ? 'demo.workspace.index' : 'program.index') }}">
                        {{ $ticketPage ? 'Buka Ruang Warga' : 'Buka katalog program' }}
                    </x-ui.action-link>
                    <x-ui.action-link href="{{ route('help') }}" variant="outline">Buka pusat bantuan</x-ui.action-link>
                </div>
            </section>
        </div>
    </div>
@endsection
