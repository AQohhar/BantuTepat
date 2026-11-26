@extends('layouts.public')

@section('title', 'Pusat Bantuan')

@section('content')
    <header class="page-intro help-intro">
        <div class="site-container">
            <h1>Pusat bantuan</h1>
            <p class="intro">Temukan jawaban tentang katalog, pengajuan, dan penggunaan prototipe BantuTepat.</p>
        </div>
    </header>

    <div class="page-section">
        <div class="site-container content-grid">
            <section class="faq" aria-labelledby="faq-heading" data-faq>
                <h2 id="faq-heading">Pertanyaan umum</h2>
                <div class="faq-search" data-faq-search hidden>
                    <div class="form-field">
                        <label for="faq-query">Cari pertanyaan</label>
                        <input id="faq-query" type="search" maxlength="80" placeholder="Contoh: tiket, wilayah, sanggahan" aria-controls="faq-questions">
                    </div>
                    <p id="faq-search-status" role="status"></p>
                </div>
                <div class="faq-questions" id="faq-questions">
                    <div class="faq-topic" data-faq-topic>
                        <h3>Mencari program</h3>
                        <details>
                            <summary>Bagaimana mencari program sesuai wilayah saya?</summary>
                            <p>Buka <a class="text-link" href="{{ route('program.index') }}">katalog program</a>, pilih wilayah dan jalur pendaftaran, lalu periksa cakupan dan syarat pada halaman detail.</p>
                        </details>
                        <details>
                            <summary>Di mana saya mencari bantuan sosial resmi?</summary>
                            <p>Hubungi kelurahan, desa, atau instansi sosial yang berwenang di wilayah Anda untuk memeriksa informasi dan ketentuan yang berlaku.</p>
                        </details>
                    </div>
                    <div class="faq-topic" data-faq-topic>
                        <h3>Pengajuan dan perbaikan</h3>
                        <details>
                            <summary>Apa yang bisa saya lakukan di versi demo?</summary>
                            <p>Anda dapat mencari program, membaca panduan, melihat statistik agregat, dan <a class="text-link" href="{{ route('guide') }}#usulan-data">mencoba alur pengajuan demo</a>. Tiket contoh hanya dapat dibuka selama sesi browser yang membuatnya masih aktif.</p>
                        </details>
                        <details>
                            <summary>Bagaimana melanjutkan tiket yang perlu perbaikan?</summary>
                            <p>Buka tiket di <a class="text-link" href="{{ route('demo.workspace.index') }}">Ruang Warga</a>, baca bukti contoh yang diminta, lalu pilih <strong>Lengkapi perbaikan</strong>. Bukti tambahan tetap tercatat pada tiket yang sama.</p>
                        </details>
                    </div>
                    <div class="faq-topic" data-faq-topic>
                        <h3>Hasil dan transparansi</h3>
                        <details>
                            <summary>Apa perbedaan sanggahan dan pengaduan?</summary>
                            <p>Sanggahan meminta peninjauan atas hasil pengajuan. Pengaduan melaporkan masalah pelayanan. Lihat <a class="text-link" href="{{ route('guide') }}#hasil-dan-sanggahan">panduan hasil dan sanggahan</a> untuk lebih jelasnya.</p>
                        </details>
                        <details>
                            <summary>Mengapa statistik wilayah tertentu disembunyikan?</summary>
                            <p>Kelompok dengan jumlah kasus terlalu kecil disembunyikan. Baca <a class="text-link" href="{{ route('transparency') }}#statistik-metode">metode statistik</a> untuk mengetahui cara ringkasan dihitung.</p>
                        </details>
                    </div>
                </div>
                <div class="panel faq-empty" hidden>
                    <h3>Pertanyaan tidak ditemukan</h3>
                    <p>Coba kata kunci yang lebih singkat atau tampilkan semua pertanyaan.</p>
                    <button class="button button-outline" type="button" data-faq-reset>Tampilkan semua pertanyaan</button>
                </div>
            </section>

            <aside class="panel detail-aside help-resources" id="privasi">
                <h2>Akses cepat bantuan</h2>
                <ul class="help-quick-links">
                    <li><a class="text-link" href="{{ route('guide') }}">Panduan pengajuan</a></li>
                    <li><a class="text-link" href="{{ route('demo.workspace.index') }}">Lihat tiket di Ruang Warga</a></li>
                    <li><a class="text-link" href="{{ route('accessibility') }}">Navigasi dengan keyboard</a></li>
                    <li><a class="text-link" href="{{ route('privacy') }}">Baca informasi privasi</a></li>
                </ul>
                <p class="notice">Gunakan data fiktif saat mencoba layanan. Kata kunci pencarian dapat muncul di alamat halaman.</p>
            </aside>
        </div>
    </div>
@endsection
