@props(['history', 'states'])

<section class="panel case-history" aria-labelledby="demo-timeline-heading">
    <h2 id="demo-timeline-heading" class="demo-field-title" tabindex="-1">Riwayat proses</h2>
    <ol class="demo-timeline">
        @foreach ($history as $event)
            <li @if ($loop->last) aria-current="step" data-motion-reveal="step" @endif>
                <strong>{{ $states[$event['status']]['label'] }}</strong>
                <p>{{ $states[$event['status']]['description'] }}</p>
                <div class="demo-event-meta">
                    @if (isset($event['actor']))
                        <span>{{ $event['actor'] }}</span>
                    @endif
                    <time datetime="{{ $event['at'] }}">{{ \Illuminate\Support\Carbon::parse($event['at'])->format('d/m/Y H:i') }} WIB</time>
                </div>
            </li>
        @endforeach
    </ol>
</section>
