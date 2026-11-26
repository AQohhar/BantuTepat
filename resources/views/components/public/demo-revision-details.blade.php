@props(['revision'])

<dl class="detail-facts">
    <div><dt>Alasan</dt><dd>{{ $revision['reason'] }}</dd></div>
    <div><dt>Bukti yang diminta</dt><dd>{{ $revision['document'] }}</dd></div>
    @if (isset($revision['deadline']))
        <div>
            <dt>Tenggat</dt>
            <dd><time datetime="{{ $revision['deadline'] }}">{{ \Illuminate\Support\Carbon::parse($revision['deadline'])->format('d/m/Y') }}</time></dd>
        </div>
    @endif
    @if (isset($revision['requested_by']))
        <div><dt>Diminta oleh</dt><dd>{{ $revision['requested_by'] }}</dd></div>
    @endif
</dl>
