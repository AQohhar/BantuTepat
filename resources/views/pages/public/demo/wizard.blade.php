@extends('layouts.public')

@section('title', $scenario['label'].' · Simulasi')

@section('content')
    @php($stepLabels = ['Profil', 'Berkas contoh', 'Ringkasan'])
    <header class="page-intro wizard-intro">
        <div class="site-container">
            <a class="text-link back-link" href="{{ $program ? route('program.show', $program['slug']) : route('guide').'#usulan-data' }}">
                {{ $program ? 'Kembali ke detail program' : 'Kembali ke panduan usulan' }}
            </a>
            <h1>{{ $scenario['label'] }}</h1>
            <p class="intro">Latihan dengan data fiktif. Pilihan Anda hanya digunakan dalam sesi demo ini.</p>
        </div>
    </header>

    <div class="page-section demo-section">
        <div class="site-container detail-grid">
            <div class="panel demo-form-panel">
                <p class="wizard-mobile-step">Langkah {{ $step }} dari {{ count($stepLabels) }} <strong>{{ $stepLabels[$step - 1] }}</strong></p>
                <ol class="demo-stepper" aria-label="Tahapan simulasi">
                    @foreach ($stepLabels as $label)
                        <li @if ($step === $loop->iteration) aria-current="step" @endif>
                            <span class="step-number" aria-hidden="true">{{ $loop->iteration }}</span>
                            <span>{{ $label }}</span>
                        </li>
                    @endforeach
                </ol>

                <form method="POST" action="{{ $path }}">
                    @csrf
                    <input type="hidden" name="step" value="{{ $step }}">
                    @if ($step > 1)
                        <input type="hidden" name="draft_id" value="{{ $draft['id'] }}">
                    @endif

                    @if ($step === 1)
                        <fieldset class="demo-fields" @error('profile') aria-describedby="profile-error" @enderror>
                            <legend class="demo-field-title">Pilih profil rumah tangga</legend>
                            @if ($program)
                                <p>Untuk {{ $program['name'] }}, gunakan profil contoh dari {{ $program['territory'] }}.</p>
                            @else
                                <p>Setiap profil berisi nama dan wilayah contoh.</p>
                            @endif
                            <div class="demo-options">
                                @foreach ($profiles as $id => $profile)
                                    <label class="demo-choice">
                                        <input type="radio" name="profile" value="{{ $id }}" required
                                            @checked(old('profile', $draft['profile'] ?? null) === $id)
                                            @error('profile') aria-invalid="true" @enderror>
                                        <span><strong>{{ $profile['name'] }}</strong><span>{{ $profile['territory'] }}</span></span>
                                    </label>
                                @endforeach
                            </div>
                            @error('profile')<p id="profile-error" class="field-error" role="alert">{{ $message }}</p>@enderror
                        </fieldset>
                    @elseif ($step === 2)
                        <div class="demo-fields">
                            <h2 class="demo-field-title">Siapkan berkas contoh</h2>
                            @unless ($program)
                                <div class="form-field">
                                    <label for="reason">Alasan usulan</label>
                                    <select id="reason" name="reason" required
                                        @error('reason') aria-invalid="true" aria-describedby="reason-error" @enderror>
                                        <option value="">Pilih alasan usulan</option>
                                        @foreach ($reasons as $value => $label)
                                            <option value="{{ $value }}" @selected(old('reason', $draft['reason'] ?? null) === $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('reason')<p id="reason-error" class="field-error" role="alert">{{ $message }}</p>@enderror
                                </div>
                            @endunless
                            <div class="form-field">
                                <label class="demo-choice">
                                    <input type="checkbox" name="document" value="1" required
                                        @checked(old('document', ! $errors->has('document') && isset($draft['document'])))
                                        @error('document') aria-invalid="true" aria-describedby="document-error" @enderror>
                                    <span><strong>Gunakan {{ Str::lower($scenario['initial_document']) }}</strong><span>Berkas contoh sudah disediakan.</span></span>
                                </label>
                                @error('document')<p id="document-error" class="field-error" role="alert">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    @else
                        <div class="demo-fields">
                            <h2 class="demo-field-title">Periksa ringkasan</h2>
                            <dl class="detail-facts">
                                @if ($program)
                                    <div><dt>Program</dt><dd>{{ $program['name'] }}</dd></div>
                                @endif
                                <div><dt>Rumah tangga</dt><dd>{{ $profiles[$draft['profile']]['name'] }}</dd></div>
                                <div><dt>Wilayah</dt><dd>{{ $profiles[$draft['profile']]['territory'] }}</dd></div>
                                @if (isset($draft['reason']))
                                    <div><dt>Alasan usulan</dt><dd>{{ $reasons[$draft['reason']] ?? 'Alasan tidak tersedia' }}</dd></div>
                                @endif
                                <div><dt>Berkas contoh</dt><dd>{{ $draft['document'] }}</dd></div>
                            </dl>
                            <div class="wizard-edit-links">
                                <a class="text-link" href="{{ $path }}?langkah=1">Ubah profil</a>
                                <a class="text-link" href="{{ $path }}?langkah=2">{{ $program ? 'Ubah berkas contoh' : 'Ubah alasan atau berkas' }}</a>
                            </div>
                            <div class="form-field">
                                <label class="demo-choice">
                                    <input type="checkbox" name="confirm" value="1" required
                                        @error('confirm') aria-invalid="true" aria-describedby="confirm-error" @enderror>
                                    <span>Saya sudah memeriksa ringkasan data simulasi.</span>
                                </label>
                                @error('confirm')<p id="confirm-error" class="field-error" role="alert">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    @endif

                    <div class="actions">
                        <button class="button" type="submit">{{ $step === 3 ? 'Buat tiket simulasi' : 'Lanjutkan' }}</button>
                        @if ($step > 1)
                            <a class="text-link" href="{{ $path.'?langkah='.($step - 1) }}">Kembali satu langkah</a>
                        @endif
                    </div>
                </form>
            </div>
            <aside class="panel detail-aside wizard-context" aria-labelledby="demo-context">
                @if ($program)
                    <h2 id="demo-context" class="demo-field-title">{{ $program['name'] }}</h2>
                    <dl class="detail-facts">
                        <div><dt>Instansi</dt><dd>{{ $program['agency'] }}</dd></div>
                        <div><dt>Wilayah sasaran</dt><dd>{{ $program['territory'] }}</dd></div>
                        <div><dt>Jadwal contoh</dt><dd>{{ $details['schedule'] }}</dd></div>
                        <div><dt>Jalur demo</dt><dd>Mandiri</dd></div>
                        <div><dt>Ketentuan</dt><dd>{{ $details['policy_source'] }}</dd></div>
                    </dl>
                @else
                    <h2 id="demo-context" class="demo-field-title">Tentang usulan data</h2>
                    <p>Gunakan jalur ini jika rumah tangga belum tercatat atau datanya perlu diperbarui. Anda dapat mengajukan usulan tanpa memilih program bantuan.</p>
                @endif
                <p><a class="text-link" href="{{ route('privacy') }}">Baca informasi privasi</a></p>
            </aside>
        </div>
    </div>
@endsection
