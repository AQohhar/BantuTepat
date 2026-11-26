@extends('layouts.public')

@section('title', 'Antrean Pengajuan')

@section('content')
    <header class="page-intro workspace-intro workspace-landing">
        <div class="site-container workspace-heading">
            <div>
                <h1>Antrean pengajuan</h1>
                <p class="workspace-role">Ruang Petugas · {{ $operator['territory'] }}</p>
                <p class="intro">Periksa tiket yang ditugaskan kepada {{ $operator['name'] }}.</p>
            </div>
            <form method="POST" action="{{ route('demo.operator.leave') }}">
                @csrf
                <button class="button button-outline" type="submit">Keluar dari ruang petugas</button>
            </form>
        </div>
    </header>

    <div class="page-section demo-section">
        <div class="site-container workspace-content">
            <dl class="queue-overview" aria-label="Ringkasan antrean pemeriksaan">
                <div><dt>Wilayah penugasan</dt><dd>{{ $operator['territory'] }}</dd></div>
                <div><dt>Tiket ditugaskan</dt><dd>{{ $assignedCount }}</dd></div>
                <div><dt>Sesuai filter</dt><dd>{{ $tickets->count() }}</dd></div>
            </dl>
            <section aria-labelledby="assigned-ticket-heading">
                <div class="results-heading workspace-results-heading">
                    <h2 id="assigned-ticket-heading">Tiket yang ditugaskan</h2>
                    @if ($assignedCount > 0)
                        <p role="status">{{ $tickets->count() }} dari {{ $assignedCount }} tiket ditampilkan</p>
                    @endif
                </div>
                @if ($assignedCount > 0)
                    <form class="panel workspace-filters operator-filters" method="GET" action="{{ route('demo.operator.index') }}" aria-label="Filter antrean">
                        <x-public.filter-select name="status" label="Status" empty="Semua status"
                            :options="$statusLabels" :selected="$status" :errors="$filterErrors" />
                        <div class="actions">
                            <button class="button" type="submit">Terapkan filter</button>
                            <a class="text-link" href="{{ route('demo.operator.index') }}">Reset filter</a>
                        </div>
                    </form>
                    @if ($tickets->isNotEmpty())
                        <table class="operator-queue" role="table" aria-labelledby="assigned-ticket-heading">
                            <thead role="rowgroup">
                                <tr role="row">
                                    <th scope="col" role="columnheader">Pengajuan</th>
                                    <th scope="col" role="columnheader">Status</th>
                                    <th scope="col" role="columnheader">Pembaruan</th>
                                    <th scope="col" role="columnheader">Tindakan</th>
                                </tr>
                            </thead>
                            <tbody role="rowgroup">
                                @foreach ($tickets as $number => $record)
                                    @php
                                        $lastUpdate = \Illuminate\Support\Arr::last($record['history'])['at'];
                                        $deadline = $record['status'] === 'REVISION_REQUIRED' ? ($record['revision_request']['deadline'] ?? null) : null;
                                    @endphp
                                    <tr role="row">
                                        <td class="operator-queue-title" role="cell">
                                            <strong>{{ $record['program_name'] ?? $typeLabels[$record['type']] }}</strong>
                                            <small class="ticket-number">{{ $number }} · {{ $record['profile']['territory'] }}</small>
                                        </td>
                                        <td class="operator-queue-state" role="cell">
                                            <x-public.demo-ticket-status :status="$record['status']" :label="$statusLabels[$record['status']]" />
                                            @if ($deadline)
                                                <small>Tenggat <time datetime="{{ $deadline }}">{{ \Illuminate\Support\Carbon::parse($deadline)->format('d/m/Y') }}</time></small>
                                            @endif
                                        </td>
                                        <td class="operator-queue-time" role="cell">
                                            <span class="queue-mobile-label" aria-hidden="true">Diperbarui </span><time datetime="{{ $lastUpdate }}">{{ \Illuminate\Support\Carbon::parse($lastUpdate)->timezone('Asia/Jakarta')->format('d/m/Y H:i') }} WIB</time>
                                        </td>
                                        <td class="operator-queue-action" role="cell">
                                            <x-ui.action-link href="{{ route('demo.operator.show', $number) }}" variant="outline">
                                                Buka pemeriksaan <span class="sr-only">{{ $number }}</span>
                                            </x-ui.action-link>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="panel empty-state">
                            <h3>Tidak ada tiket dengan status ini</h3>
                            <x-ui.action-link href="{{ route('demo.operator.index') }}" variant="outline">Lihat semua tiket</x-ui.action-link>
                        </div>
                    @endif
                @else
                    <div class="panel empty-state">
                        <h3>Belum ada tiket yang ditugaskan</h3>
                        <p>Muat tiket contoh dari Ruang Warga jika daftarnya kosong, atau buat pengajuan demo dengan profil dari {{ $operator['territory'] }}.</p>
                        <div class="actions operator-empty-actions">
                            <x-ui.action-link href="{{ route('demo.workspace.index') }}">Buka Ruang Warga</x-ui.action-link>
                            <x-ui.action-link href="{{ route('program.index') }}" variant="outline">Buka katalog</x-ui.action-link>
                        </div>
                    </div>
                @endif
            </section>
        </div>
    </div>
@endsection
