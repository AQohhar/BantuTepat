@props(['variant' => 'primary'])

<a {{ $attributes->class(['button', 'button-outline' => $variant === 'outline']) }}>
    {{ $slot }}
</a>
