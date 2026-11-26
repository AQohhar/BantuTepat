
<?php

use App\Http\Controllers\Public\DemoApplicationController;
use App\Http\Controllers\Public\DemoOperatorController;
use App\Http\Controllers\Public\DemoVerifierController;
use App\Http\Controllers\Public\DemoWorkspaceController;
use App\Http\Controllers\Public\ProgramCatalogController;
use App\Http\Controllers\Public\TransparencyController;
use App\Http\Middleware\RequireDemoOperator;
use App\Http\Middleware\RequireDemoVerifier;
use Illuminate\Support\Facades\Route;

Route::get('/', [ProgramCatalogController::class, 'home'])->name('home');

Route::get('/program', [
    ProgramCatalogController::class,
    'index'
])->name('program.index');

Route::get('/program/{slug}', [
    ProgramCatalogController::class,
    'show'
])->name('program.show');

Route::view('/panduan', 'pages.public.guide')->name('guide');
Route::get('/transparansi', TransparencyController::class)->name('transparency');
Route::view('/bantuan', 'pages.public.help')->name('help');
Route::view('/privasi', 'pages.public.information.privacy')->name('privacy');
Route::view('/syarat-layanan', 'pages.public.information.terms')->name('terms');
Route::view('/aksesibilitas', 'pages.public.information.accessibility')->name('accessibility');

Route::prefix('demo')->name('demo.')
    ->where(['ticket' => 'DEMO-[PD]-[A-Z0-9]{12}'])
    ->middleware('cache.headers:private;no_store')
    ->group(function () {
        Route::get('/masuk', [DemoWorkspaceController::class, 'login'])->name('workspace.login')->block();
        Route::post('/masuk', [DemoWorkspaceController::class, 'enter'])->name('workspace.enter')->block();
        Route::post('/keluar', [DemoWorkspaceController::class, 'leave'])->name('workspace.leave')->block();
        Route::get('/pengajuan-saya', [DemoWorkspaceController::class, 'index'])->name('workspace.index')->block();
        Route::post('/pengajuan-saya/contoh', [DemoWorkspaceController::class, 'loadExamples'])
            ->name('workspace.examples')->block();
        Route::match(['get', 'post'], '/program/{slug}/pengajuan', [DemoApplicationController::class, 'program'])
            ->name('program')->block();
        Route::match(['get', 'post'], '/usulan-data', [DemoApplicationController::class, 'proposal'])
            ->name('proposal')->block();
        Route::get('/pengajuan/{ticket}', [DemoApplicationController::class, 'ticket'])
            ->name('ticket')->block();
        Route::get('/pengajuan/{ticket}/perbaiki', [DemoApplicationController::class, 'revision'])
            ->name('ticket.revision')->block();
        Route::post('/pengajuan/{ticket}/perbaiki', [DemoApplicationController::class, 'resubmit'])
            ->name('ticket.resubmit')->block();

        Route::prefix('wilayah')->name('operator.')->group(function () {
            Route::get('/masuk', [DemoOperatorController::class, 'login'])->name('login')->block();
            Route::post('/masuk', [DemoOperatorController::class, 'enter'])->name('enter')->block();
            Route::middleware(RequireDemoOperator::class)->group(function () {
                Route::post('/keluar', [DemoOperatorController::class, 'leave'])->name('leave')->block();
                Route::get('/kasus', [DemoOperatorController::class, 'index'])->name('index')->block();
                Route::get('/verifikasi/{ticket}', [DemoOperatorController::class, 'show'])->name('show')->block();
                Route::post('/verifikasi/{ticket}/mulai', [DemoOperatorController::class, 'start'])->name('start')->block();
                Route::post('/verifikasi/{ticket}/perbaikan', [DemoOperatorController::class, 'requestRevision'])
                    ->name('request-revision')->block();
                Route::post('/verifikasi/{ticket}/rekomendasi', [DemoOperatorController::class, 'recommend'])
                    ->name('recommend')->block();
            });
        });

        Route::prefix('instansi')->name('verifier.')->group(function () {
            Route::get('/masuk', [DemoVerifierController::class, 'login'])->name('login')->block();
            Route::post('/masuk', [DemoVerifierController::class, 'enter'])->name('enter')->block();
            Route::middleware(RequireDemoVerifier::class)->group(function () {
                Route::post('/keluar', [DemoVerifierController::class, 'leave'])->name('leave')->block();
                Route::get('/review', [DemoVerifierController::class, 'index'])->name('index')->block();
                Route::get('/review/{ticket}', [DemoVerifierController::class, 'show'])->name('show')->block();
                Route::post('/review/{ticket}', [DemoVerifierController::class, 'decide'])->name('decide')->block();
            });
        });
    });
