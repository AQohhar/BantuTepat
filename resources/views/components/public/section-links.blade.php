@props(['label', 'links'])

<nav class="section-links" aria-label="{{ $label }}">
    <ul>
        @foreach ($links as $href => $text)
            <li><a href="{{ $href }}">{{ $text }}</a></li>
        @endforeach
    </ul>
</nav>
