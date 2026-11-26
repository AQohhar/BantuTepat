@php
    $operatorView = request()->routeIs('demo.operator.*');
    $verifierView = request()->routeIs('demo.verifier.*');
@endphp

<div class="demo-navigation">
    <div class="site-container demo-navigation-frame">
        <nav class="demo-navigation-inner" aria-label="Navigasi simulasi">
            <a href="{{ route('demo.workspace.index') }}" @if (! $operatorView && ! $verifierView) aria-current="true" @endif>Ruang Warga</a>
            <a href="{{ route('demo.operator.index') }}" @if ($operatorView) aria-current="true" @endif>Ruang Petugas</a>
            <a href="{{ route('demo.verifier.index') }}" @if ($verifierView) aria-current="true" @endif>Ruang Verifikator</a>
        </nav>
        <p class="demo-context-note">Prototipe pendidikan · Data fiktif</p>
    </div>
</div>
