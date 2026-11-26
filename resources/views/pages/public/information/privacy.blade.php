@extends('layouts.public')

@section('title', 'Privasi')

@section('content')
    <x-public.information-page title="Privasi" intro="Informasi yang digunakan saat Anda mengakses prototipe BantuTepat dan cara menjaga data pribadi Anda."
        :sections="['pencarian' => 'Pencarian dan filter', 'cookie' => 'Cookie sesi dan catatan teknis', 'data-demo' => 'Data program dan pengajuan', 'periksa-data' => 'Jika Anda perlu memeriksa data']">
        <section id="pencarian" tabindex="-1">
            <h2>Pencarian dan filter</h2>
            <p>Kata kunci dan pilihan filter digunakan untuk menampilkan program atau statistik yang sesuai. Nilainya tercantum di alamat halaman dan dapat muncul di riwayat browser atau tautan yang Anda bagikan.</p>
            <p>Gunakan nama program dan wilayah simulasi sebagai kata kunci. Simpan NIK, nomor telepon, alamat rumah, dan dokumen asli di luar situs ini.</p>
        </section>
        <section id="cookie" tabindex="-1">
            <h2>Cookie sesi dan catatan teknis</h2>
            <p>Situs menggunakan cookie sesi untuk menjalankan fungsi dasarnya. Penyimpanan sesi dapat mencatat alamat IP, informasi browser, dan waktu aktivitas terakhir.</p>
            <p>Masa aktif sesi pada konfigurasi ini adalah {{ config('session.lifetime') }} menit tanpa aktivitas. Masa simpan catatan teknis mengikuti pengaturan instalasi. Anda dapat menghapus cookie dan riwayat situs dari pengaturan browser.</p>
        </section>
        <section id="data-demo" tabindex="-1">
            <h2>Data program dan pengajuan</h2>
            <p>Program, dokumen contoh, dan angka transparansi memakai data sintetis. Statistik publik disajikan dalam bentuk agregat. Formulir demo menyediakan pilihan fiktif tanpa unggah dokumen. Draf dan tiket contoh disimpan dalam sesi yang membuatnya dan hanya dapat diakses selama sesi itu aktif.</p>
        </section>
        <section id="periksa-data" tabindex="-1">
            <h2>Jika Anda perlu memeriksa data</h2>
            <p>Untuk meninjau, mengoreksi, atau meminta penghapusan data teknis yang terkait dengan Anda, hubungi pengelola yang menyediakan akses demo. Sebutkan halaman dan waktu kejadian. Hindari mengirim ulang data pribadi saat menjelaskan masalah.</p>
        </section>
    </x-public.information-page>
@endsection
