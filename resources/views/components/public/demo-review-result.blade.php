@props(['decision', 'label'])

<div class="review-result" data-motion-reveal="panel">
    <p><x-public.demo-ticket-status :status="$decision['status']" :label="$label" /></p>
    <dl class="detail-facts">
        <div><dt>Alasan</dt><dd>{{ $decision['reason'] }}</dd></div>
        <div><dt>Dasar tinjauan</dt><dd>{{ $decision['basis'] }}</dd></div>
        <div><dt>Berkas rujukan</dt><dd>{{ $decision['document'] }}</dd></div>
        <div><dt>Verifikator</dt><dd>{{ $decision['reviewer'] }}</dd></div>
        <div>
            <dt>Waktu tinjauan</dt>
            <dd><time datetime="{{ $decision['at'] }}">{{ \Illuminate\Support\Carbon::parse($decision['at'])->format('d/m/Y H:i') }} WIB</time></dd>
        </div>
    </dl>
    <p class="notice">{{ $decision['next'] }}</p>
</div>
