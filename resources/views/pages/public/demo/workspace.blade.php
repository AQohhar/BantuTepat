@extends('layouts.public')

@section('title', 'Pengajuan Saya')

@section('content')
    <header class="page-intro workspace-intro workspace-landing">
        <div class="site-container workspace-heading">
            <div>
                <h1>Pengajuan Saya</h1>
                <p class="intro">Tinjau status tiket simulasi dan lihat langkah yang perlu dilakukan.</p>
                <div class="actions">
                    <x-ui.action-link href="{{ route('program.index') }}">Cari program</x-ui.action-link>
                    <x-ui.action-link href="{{ route('demo.proposal') }}" variant="outline">Buat usulan data</x-ui.action-link>
                    <a class="text-link" href="{{ route('demo.operator.index') }}">Buka Ruang Petugas demo</a>
                </div>
            </div>
            <form method="POST" action="{{ route('demo.workspace.leave') }}">
                @csrf
                <button class="button button-outline" type="submit">Keluar dari ruang demo</button>
            </form>
        </div>
    </header>

    <div class="page-section demo-section">
        <div class="site-container workspace-content">
            @if (session('examples_loaded'))
                <p class="notice" role="status" data-motion-reveal="panel">Dua tiket contoh ditambahkan ke sesi demo ini.</p>
            @endif

            <dl class="workspace-stats" aria-label="Ringkasan tiket demo">
                <div class="workspace-stat-total"><dt>Tiket demo</dt><dd>{{ $total }}</dd></div>
                <div class="workspace-stat-attention">
                    <dt><a href="{{ route('demo.workspace.index', ['status' => 'REVISION_REQUIRED']) }}#workspace-ticket-heading">Perlu perbaikan</a></dt>
                    <dd>{{ $revisionCount }}</dd>
                </div>
                <div>
                    <dt><a href="{{ route('demo.workspace.index', ['status' => 'RESUBMITTED']) }}#workspace-ticket-heading">Perbaikan dikirim ulang</a></dt>
                    <dd>{{ $resubmittedCount }}</dd>
                </div>
            </dl>

            <section aria-labelledby="workspace-ticket-heading">
                <div class="results-heading workspace-results-heading">
                    <h2 id="workspace-ticket-heading">Daftar tiket</h2>
                    @if ($total > 0)
                        <p role="status">{{ $tickets->count() }} dari {{ $total }} tiket ditampilkan</p>
                    @endif
                </div>

                @if ($total > 0)
                    <form class="panel workspace-filters" method="GET" action="{{ route('demo.workspace.index') }}" aria-label="Filter tiket">
                        <div class="filter-grid workspace-filter-grid">
                            <div class="form-field workspace-filter-search">
                                <label for="workspace-q">Cari tiket</label>
                                <input id="workspace-q" name="q" type="search" maxlength="100" placeholder="Nomor tiket atau nama program"
                                    value="{{ $filters['q'] ?? '' }}"
                                    @if ($filterErrors->has('q')) aria-invalid="true" aria-describedby="workspace-q-error" @endif>
                                @if ($filterErrors->has('q'))
                                    <p id="workspace-q-error" class="field-error">{{ $filterErrors->first('q') }}</p>
                                @endif
                            </div>
                            <x-public.filter-select name="type" label="Jenis pengajuan" empty="Semua jenis"
                                :options="$typeLabels" :selected="$filters['type'] ?? null" :errors="$filterErrors" />
                            <x-public.filter-select name="status" label="Status" empty="Semua status"
                                :options="$statusLabels" :selected="$filters['status'] ?? null" :errors="$filterErrors" />
                        </div>
                        <div class="actions">
                            <button class="button" type="submit">Terapkan filter</button>
                            <a class="text-link" href="{{ route('demo.workspace.index') }}">Reset filter</a>
                        </div>
                    </form>

                    <div class="panel-stack">
                        @forelse ($tickets as $number => $ticket)
                            @php
                                $lastUpdate = \Illuminate\Support\Arr::last($ticket['history'])['at'];
                                $needsRevision = $ticket['status'] === 'REVISION_REQUIRED';
                                $hasResult = isset($ticket['decision']);
                                $detailUrl = route('demo.ticket', $number);
                            @endphp
                            <article @class(['panel workspace-ticket resident-ticket', 'workspace-ticket-attention' => $needsRevision, 'resident-ticket-result' => $hasResult])>
                                <div class="resident-ticket-summary">
                                    <div class="badge-row">
                                        <x-public.demo-ticket-status :status="$ticket['status']" :label="$statusLabels[$ticket['status']]" />
                                        @if ($ticket['example'] ?? false)
                                            <span class="badge badge-neutral">Tiket contoh</span>
                                        @endif
                                    </div>
                                    <h3>{{ $ticket['program_name'] ?? $typeLabels[$ticket['type']] }}</h3>
                                    <p class="resident-ticket-updated">Diperbarui <time datetime="{{ $lastUpdate }}">{{ \Illuminate\Support\Carbon::parse($lastUpdate)->timezone('Asia/Jakarta')->format('d/m/Y H:i') }} WIB</time></p>
                                    <p class="workspace-ticket-meta">
                                        <span class="ticket-number">{{ $number }}</span>
                                        @if ($ticket['program_name'])
                                            <span>{{ $typeLabels[$ticket['type']] }}</span>
                                        @endif
                                        <span>{{ $ticket['profile']['territory'] }}</span>
                                    </p>
                                </div>
                                <div class="workspace-ticket-next">
                                    @if ($needsRevision)
                                        <p>Lengkapi bukti contoh yang diminta petugas.</p>
                                        <div class="workspace-ticket-actions">
                                            <x-ui.action-link href="{{ route('demo.ticket.revision', $number) }}">
                                                Lengkapi perbaikan <span class="sr-only">{{ $number }}</span>
                                            </x-ui.action-link>
                                            <a class="text-link" href="{{ $detailUrl }}">Lihat detail tiket <span class="sr-only">{{ $number }}</span></a>
                                        </div>
                                    @elseif ($hasResult)
                                        <p>Baca alasan dan arahan tindak lanjut dari verifikator.</p>
                                        <div class="workspace-ticket-actions">
                                            <x-ui.action-link href="{{ $detailUrl }}#demo-next-action">
                                                Lihat hasil tinjauan <span class="sr-only">{{ $number }}</span>
                                            </x-ui.action-link>
                                        </div>
                                    @else
                                        <p>Pantau perkembangan pengajuan melalui riwayat tiket.</p>
                                        <div class="workspace-ticket-actions">
                                            <x-ui.action-link href="{{ $detailUrl }}#demo-timeline-heading" variant="outline">
                                                Lihat riwayat tiket <span class="sr-only">{{ $number }}</span>
                                            </x-ui.action-link>
                                        </div>
                                    @endif
                                </div>
                            </article>
                        @empty
                            <div class="panel empty-state">
                                <h3>Tiket tidak ditemukan</h3>
                                <p>Coba kata kunci lain atau hapus filter.</p>
                                <x-ui.action-link href="{{ route('demo.workspace.index') }}" variant="outline">Lihat semua tiket</x-ui.action-link>
                            </div>
                        @endforelse
                    </div>
                @else
                    <div class="panel empty-state">
                        <h3>Belum ada tiket di sesi ini</h3>
                        <p>Buat pengajuan sendiri atau muat dua tiket contoh untuk mencoba detail dan perbaikan.</p>
                        <form class="workspace-example-form" method="POST" action="{{ route('demo.workspace.examples') }}">
                            @csrf
                            <button class="button button-outline" type="submit">Muat tiket contoh</button>
                        </form>
                    </div>
                @endif
            </section>
        </div>
    </div>
@endsection
