@extends('layouts.public')

@section('title', 'Panduan Layanan')

@section('content')
    <header class="page-intro guide-intro">
        <div class="site-container">
            <h1>Panduan layanan</h1>
            <p class="intro">Pilih jalur yang sesuai dengan kebutuhan Anda dan pahami proses sampai hasil pemeriksaan.</p>
            <x-public.section-links label="Isi panduan" :links="[
                '#usulan-data' => 'Usulan data',
                '#daftar-program' => 'Pendaftaran program',
                '#hasil-dan-sanggahan' => 'Hasil dan sanggahan',
            ]" />
        </div>
    </header>

    <div class="page-section">
        <div class="site-container guide-layout">
            <div class="cards cards-two">
                <section class="panel guide-card" id="usulan-data" tabindex="-1">
                    <div class="section-icon-heading">
                        <span class="shortcut-mark shortcut-document" aria-hidden="true"></span>
                        <h2>Usulan atau koreksi data</h2>
                    </div>
                    <p class="guide-example">“Data rumah tangga saya belum tercatat atau berubah.”</p>
                    <p>Gunakan jalur ini saat data sosial ekonomi rumah tangga belum tercatat atau perlu diperbarui. Pengajuan dapat berjalan tanpa memilih program bantuan.</p>
                    <ol class="steps">
                        <li><strong>Pilih alasan usulan</strong><p>Jelaskan data yang belum tercatat, keliru, atau berubah.</p></li>
                        <li><strong>Siapkan informasi pendukung</strong><p>Dalam alur simulasi, warga dapat mengajukan sendiri atau didampingi petugas wilayah yang berwenang.</p></li>
                        <li><strong>Simpan nomor tiket</strong><p>Nomor tiket terbit setelah pengajuan terkirim. Nomor yang sama dipakai saat memperbaiki usulan.</p></li>
                    </ol>
                    <x-ui.action-link href="{{ route('demo.proposal') }}">Coba simulasi usulan data</x-ui.action-link>
                </section>
                <section class="panel guide-card" id="daftar-program" tabindex="-1">
                    <div class="section-icon-heading">
                        <span class="shortcut-mark shortcut-program" aria-hidden="true"></span>
                        <h2>Pendaftaran program tertentu</h2>
                    </div>
                    <p class="guide-example">“Saya ingin mengajukan bantuan dari program tertentu.”</p>
                    <p>Gunakan jalur ini untuk satu program yang sesuai dengan wilayah, periode, dan persyaratan rumah tangga Anda.</p>
                    <dl class="guide-facts">
                        <div><dt>Mandiri</dt><dd>Warga mengajukan sendiri saat periode pendaftaran aktif.</dd></div>
                        <div><dt>Mandiri atau melalui petugas</dt><dd>Warga dapat memilih salah satu dari dua jalur.</dd></div>
                        <div><dt>Hanya melalui petugas</dt><dd>Petugas berwenang mengusulkan warga sesuai wilayah tugasnya.</dd></div>
                        <div><dt>Tanpa pendaftaran umum</dt><dd>Halaman program menyediakan persyaratan dan mekanisme yang berlaku.</dd></div>
                    </dl>
                    <x-ui.action-link href="{{ route('program.index') }}">Cari program</x-ui.action-link>
                </section>
            </div>

            <section class="panel guide-outcome-panel" id="hasil-dan-sanggahan" tabindex="-1">
                <h2>Hasil, perbaikan, dan sanggahan</h2>
                <p>Baca alasan dan dasar pemeriksaan pada hasil. Penerusan dalam simulasi hanya menunjukkan bahwa berkas telah melalui proses tersebut.</p>
                <div class="guide-outcomes">
                    <div><h3>Perbaikan</h3><p>Lengkapi bagian atau bukti yang diminta sesuai tenggat. Riwayat tetap tercatat pada tiket yang sama.</p></div>
                    <div><h3>Sanggahan</h3><p>Jika jalur tersedia, ajukan keberatan atas hasil disertai bukti. Pemeriksa yang berbeda akan meninjaunya.</p></div>
                    <div><h3>Pengaduan layanan</h3><p>Gunakan kanal pengaduan terpisah untuk masalah pelayanan seperti keterlambatan atau kendala akses.</p></div>
                </div>
            </section>

            <p class="notice">Simulasi memakai data fiktif dan menyimpan tiket di sesi browser. Pengajuan resmi dan sanggahan belum tersedia pada prototipe ini.</p>
        </div>
    </div>
@endsection
