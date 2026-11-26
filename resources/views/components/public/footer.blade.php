<footer class="site-footer">
    <div class="site-container footer-inner">
        <div class="footer-brand">
            <a class="wordmark" href="{{ route('home') }}">
                <span class="wordmark-mark" aria-hidden="true">B</span>
                <span>BantuTepat</span>
            </a>
            <p>Prototipe pendidikan dengan program dan data fiktif. Untuk layanan resmi, hubungi instansi berwenang di wilayah Anda.</p>
        </div>
        <nav class="footer-nav" aria-label="Layanan publik">
            <h2>Layanan</h2>
            <ul>
                <li><a href="{{ route('program.index') }}">Katalog program</a></li>
                <li><a href="{{ route('guide') }}">Panduan layanan</a></li>
                <li><a href="{{ route('transparency') }}">Transparansi</a></li>
                <li><a href="{{ route('help') }}">Pusat bantuan</a></li>
            </ul>
        </nav>
        <nav class="footer-nav" aria-label="Ruang simulasi">
            <h2>Ruang simulasi</h2>
            <ul>
                <li><a href="{{ route('demo.workspace.index') }}">Pengajuan Saya</a></li>
                <li><a href="{{ route('demo.operator.index') }}">Ruang Petugas demo</a></li>
                <li><a href="{{ route('demo.verifier.index') }}">Ruang Verifikator demo</a></li>
            </ul>
        </nav>
        <nav class="footer-nav" aria-label="Informasi situs">
            <h2>Informasi situs</h2>
            <ul>
                <li><a href="{{ route('privacy') }}" @if (request()->routeIs('privacy')) aria-current="page" @endif>Privasi</a></li>
                <li><a href="{{ route('terms') }}" @if (request()->routeIs('terms')) aria-current="page" @endif>Syarat layanan</a></li>
                <li><a href="{{ route('accessibility') }}" @if (request()->routeIs('accessibility')) aria-current="page" @endif>Aksesibilitas</a></li>
            </ul>
        </nav>
    </div>
    <div class="site-container footer-credit">
        <span>© {{ date('Y') }} BantuTepat</span>
        <a href="#halaman-atas">Kembali ke atas</a>
    </div>
</footer>
