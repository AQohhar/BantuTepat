<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Jelajahi program, panduan, dan statistik simulasi layanan bantuan sosial BantuTepat.">
    <meta name="theme-color" content="#152544">
    <title>@yield('title', 'Beranda') | BantuTepat</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body id="halaman-atas" @class(['demo-view' => request()->routeIs('demo.*')])
    @if (request()->routeIs('demo.*')) data-demo-area="{{ request()->routeIs('demo.operator.*') ? 'operator' : (request()->routeIs('demo.verifier.*') ? 'verifier' : 'resident') }}" @endif>
    <a class="skip-link" href="#main-content">Lewati ke konten utama</a>
    @include('components.public.navbar')

    <main id="main-content" tabindex="-1">
        @if (request()->routeIs('demo.*'))
            @include('components.public.demo-navigation')
            <p class="sr-only" role="status" data-submit-status></p>
        @endif
        @yield('content')
    </main>

    @include('components.public.footer')
</body>
</html>
