@props(['title', 'intro', 'sections'])

<header class="page-intro information-intro">
    <div class="site-container">
        <h1>{{ $title }}</h1>
        <p class="intro">{{ $intro }}</p>
    </div>
</header>

<div class="page-section information-section">
    <div class="site-container information-layout">
        <aside class="panel information-links">
            <details class="information-toc" data-information-toc open>
                <summary>Dalam halaman ini</summary>
                <nav aria-label="Daftar isi {{ $title }}">
                    <ul>
                        @foreach ($sections as $id => $heading)
                            <li><a href="#{{ $id }}">{{ $heading }}</a></li>
                        @endforeach
                    </ul>
                </nav>
            </details>
            <nav aria-label="Informasi situs lainnya">
                <h2>Informasi situs</h2>
                <ul>
                    @foreach (['privacy' => 'Privasi', 'terms' => 'Syarat layanan', 'accessibility' => 'Aksesibilitas'] as $name => $label)
                        @unless (request()->routeIs($name))
                            <li><a href="{{ route($name) }}">{{ $label }}</a></li>
                        @endunless
                    @endforeach
                </ul>
            </nav>
        </aside>
        <article class="panel information-content">{{ $slot }}</article>
    </div>
</div>
