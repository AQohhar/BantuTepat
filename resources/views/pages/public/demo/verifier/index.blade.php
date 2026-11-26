@extends('layouts.public')

@section('title', 'Antrean Tinjauan')

@section('content')
    <header class="page-intro workspace-intro workspace-landing">
        <div class="site-container workspace-heading">
            <div>
                <h1>Antrean tinjauan</h1>
                <p class="workspace-role">Ruang Verifikator · {{ $verifier['territory'] }}</p>
                <p class="intro">{{ $verifier['name'] }} meninjau rekomendasi untuk {{ $verifier['agency'] }}.</p>
            </div>
            <form method="POST" action="{{ route('demo.verifier.leave') }}">
                @csrf
                <button class="button button-outline" type="submit">Keluar dari ruang verifikator</button>
            </form>
        </div>
    </header>

    <div class="page-section demo-section">
        <div class="site-container workspace-content">
            <nav class="review-filter review-queue-switcher" aria-label="Status tinjauan">
                <ul>
                    <li><a href="{{ route('demo.verifier.index') }}" @if (! $showResults) aria-current="page" @endif><span>Menunggu tinjauan</span><strong>{{ $pendingCount }}</strong></a></li>
                    <li><a href="{{ route('demo.verifier.index', ['tampilan' => 'hasil']) }}" @if ($showResults) aria-current="page" @endif><span>Hasil tersimpan</span><strong>{{ $completedCount }}</strong></a></li>
                </ul>
            </nav>
            <section aria-labelledby="review-queue-title">
                <div class="results-heading workspace-results-heading">
                    <h2 id="review-queue-title">{{ $showResults ? 'Hasil tinjauan' : 'Tiket yang perlu ditinjau' }}</h2>
                </div>
                <div class="panel-stack">
                    @forelse ($tickets as $number => $record)
                        <article class="panel workspace-ticket">
                            <div>
                                <x-public.demo-ticket-status :status="$record['status']" :label="$states[$record['status']]['label']" />
                                <h3>{{ $record['program_name'] ?? $scenarios[$record['type']]['label'] }}</h3>
                                <p class="workspace-ticket-meta">
                                    <span class="ticket-number">{{ $number }}</span>
                                    <span>{{ $record['profile']['territory'] }}</span>
                                </p>
                            </div>
                            <x-ui.action-link href="{{ route('demo.verifier.show', $number) }}" variant="outline">
                                {{ $showResults ? 'Lihat hasil' : 'Buka tinjauan' }} <span class="sr-only">{{ $number }}</span>
                            </x-ui.action-link>
                        </article>
                    @empty
                        <div class="panel empty-state">
                            <h3>{{ $showResults ? 'Belum ada hasil tinjauan' : 'Belum ada rekomendasi untuk ditinjau' }}</h3>
                            @if ($showResults)
                                <x-ui.action-link href="{{ route('demo.verifier.index') }}" variant="outline">Lihat antrean</x-ui.action-link>
                            @else
                                <p>Kirim rekomendasi dari Ruang Petugas untuk menambahkan tiket ke antrean ini.</p>
                                <x-ui.action-link href="{{ route('demo.operator.index') }}" variant="outline">Buka Ruang Petugas</x-ui.action-link>
                            @endif
                        </div>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
@endsection
