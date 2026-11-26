@extends('layouts.public')

@section('title', 'Aksesibilitas')

@section('content')
    <x-public.information-page title="Aksesibilitas" intro="Cara menggunakan halaman publik BantuTepat dengan keyboard dan menyesuaikan tampilan saat membaca."
        :sections="['keyboard' => 'Navigasi dengan keyboard', 'perangkat' => 'Tampilan di perangkat Anda', 'hambatan' => 'Melaporkan hambatan akses']">
        <section id="keyboard" tabindex="-1">
            <h2>Navigasi dengan keyboard</h2>
            <ul class="content-list">
                <li>Tekan <kbd>Tab</kbd> untuk berpindah ke tautan atau kontrol berikutnya, dan <kbd>Shift</kbd> + <kbd>Tab</kbd> untuk kembali.</li>
                <li>Gunakan tautan <strong>Lewati ke konten utama</strong> di awal halaman untuk langsung menuju isi.</li>
                <li>Tekan <kbd>Enter</kbd> untuk membuka tautan, menu, atau pertanyaan di Pusat Bantuan yang sedang terfokus.</li>
            </ul>
        </section>
        <section id="perangkat" tabindex="-1">
            <h2>Tampilan di perangkat Anda</h2>
            <p>Halaman publik menyesuaikan tata letak dengan lebar layar. Gunakan pengaturan pembesaran browser untuk memperbesar teks. Status program dituliskan pada kartu sehingga informasinya tetap bisa dibaca tanpa mengandalkan warna.</p>
        </section>
        <section id="hambatan" tabindex="-1">
            <h2>Melaporkan hambatan akses</h2>
            <p>Jika ada bagian yang sulit diakses, sampaikan kepada pengelola yang menyediakan akses demo. Sertakan alamat halaman tanpa kata kunci pribadi, langkah yang Anda coba, serta perangkat, browser, atau alat bantu yang digunakan.</p>
            <p><a class="text-link" href="{{ route('help') }}">Buka Pusat Bantuan</a></p>
        </section>
    </x-public.information-page>
@endsection
