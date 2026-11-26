@props(['status', 'label'])

<span @class([
    'badge',
    'badge-pending' => $status === 'REVISION_REQUIRED',
    'badge-positive' => in_array($status, ['RESUBMITTED', 'RECOMMENDED_FOR_FORWARD'], true),
    'badge-progress' => in_array($status, ['UNDER_REVIEW', 'PENDING_APPROVAL', 'FORWARDING_PENDING'], true),
    'badge-negative' => $status === 'CLOSED_UNPROCESSABLE',
    'badge-neutral' => $status === 'SUBMITTED',
])>{{ $label }}</span>
