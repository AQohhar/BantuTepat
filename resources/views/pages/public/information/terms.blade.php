@extends('layouts.public')

@section('title', 'Syarat Layanan')

@section('content')
    <x-public.information-page title="Syarat layanan" intro="Panduan penggunaan BantuTepat sebagai prototipe pendidikan dan simulasi layanan publik."
        :sections="['lingkup' => 'Ruang lingkup prototipe', 'data-demo' => 'Penggunaan data saat demo', 'jadwal' => 'Informasi program dan jadwal']">
        <section id="lingkup" tabindex="-1">
            <h2>Ruang lingkup prototipe</h2>
            <p>BantuTepat adalah prototipe independen. Anda dapat menjelajahi program fiktif, membaca contoh persyaratan dan tahapan pengajuan, serta melihat statistik simulasi.</p>
        </section>
        <section id="data-demo" tabindex="-1">
            <h2>Penggunaan data saat demo</h2>
            <p>Gunakan data fiktif saat mencoba pencarian dan filter. Hindari memasukkan identitas, informasi keluarga, atau dokumen asli milik Anda maupun orang lain. Alamat halaman dapat memuat kata kunci yang Anda masukkan.</p>
            <p><a class="text-link" href="{{ route('privacy') }}">Baca informasi privasi</a></p>
        </section>
        <section id="jadwal" tabindex="-1">
            <h2>Informasi program dan jadwal</h2>
            <p>Nama program, wilayah, jadwal, dan dokumen di katalog adalah contoh untuk demonstrasi. Untuk ketentuan yang berlaku, dokumen resmi, dan cara mengajukan bantuan, hubungi instansi yang berwenang di wilayah Anda.</p>
        </section>
    </x-public.information-page>
@endsection
