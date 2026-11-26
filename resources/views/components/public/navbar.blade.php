@php
    $navigation = [
        ['home', 'Beranda'],
        ['program.index', 'Program Bansos'],
        ['guide', 'Panduan'],
        ['transparency', 'Transparansi'],
        ['help', 'Bantuan'],
        ['demo.workspace.index', 'Ruang Warga'],
    ];
@endphp

<header class="site-header">
    <div class="site-container header-inner">
        <a class="wordmark" href="{{ route('home') }}" aria-label="BantuTepat, beranda">
            <span class="wordmark-mark" aria-hidden="true">B</span>
            <span>BantuTepat</span>
        </a>

        <nav class="nav-desktop" aria-label="Navigasi utama">
            <ul class="nav-links">
                @foreach ($navigation as [$routeName, $label])
                    @include('components.public.nav-item', ['routeName' => $routeName, 'label' => $label, 'prominent' => $routeName === 'demo.workspace.index'])
                @endforeach
            </ul>
        </nav>

        <span class="simulation-label" title="Prototipe independen dengan program dan data fiktif">Simulasi</span>

        <details class="nav-mobile">
            <summary>Menu</summary>
            <nav aria-label="Navigasi utama" class="mobile-menu-panel">
                <ul class="nav-links">
                    @foreach ($navigation as [$routeName, $label])
                        @include('components.public.nav-item', ['routeName' => $routeName, 'label' => $label, 'prominent' => false])
                    @endforeach
                </ul>
            </nav>
        </details>
    </div>
</header>
