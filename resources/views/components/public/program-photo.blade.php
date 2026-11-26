@props(['program', 'sizes', 'decorative' => true, 'priority' => false])

@if (isset($program['photo']))
    @php($imagePath = 'images/programs/'.$program['photo'])
    <img {{ $attributes->class('program-photo') }}
        src="{{ asset($imagePath.'-960.jpg') }}"
        srcset="{{ asset($imagePath.'-480.jpg') }} 480w, {{ asset($imagePath.'-960.jpg') }} 960w"
        sizes="{{ $sizes }}" width="960" height="640"
        alt="{{ $decorative ? '' : ($program['photo_alt'] ?? '') }}"
        @if ($priority) fetchpriority="high" @else loading="lazy" @endif
        decoding="async">
@endif
