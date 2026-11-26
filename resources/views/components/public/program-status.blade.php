@props(['status'])

<span @class([
    'badge',
    'badge-positive' => $status === 'OPEN',
    'badge-pending' => $status === 'UPCOMING',
    'badge-neutral' => $status === 'INFO_ONLY',
])>{{ $slot }}</span>
