<?php

use App\Http\Controllers\ProfileController;
use App\Http\Middleware\RoleAccess;
use App\Livewire\KodeKuis;
use App\Livewire\Kuis as LivewireKuis;
use App\Livewire\LihatNilai;
use App\Models\Kuis;
use App\Services\GeminiService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Models\JawabanEssay;

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Route Tentang Kami
Route::get('/about', function () {
    return view('about');
})->name('about');

//  Route Gemini AI
Route::get('/test-gemini', function (GeminiService $gemini) {
    $result = $gemini->testConnection();
    return response()->json($result);
});

// Test Nilai Essay (hanya untuk development)
Route::middleware('auth')->get('/test-nilai-essay/{id}', function (string $id, GeminiService $gemini) {
    $jawaban = JawabanEssay::findOrFail($id);
    
    $result = $gemini->nilaiJawabanEssay($jawaban);
    
    return response()->json($result);
});

// QR preview untuk pengajar
Route::get('/kuis/{kuis}/qr-code-preview', function (Kuis $kuis) {
    return view('kuis.qr-code-preview', ['kuis' => $kuis]);
})->name('kuis.qr-code-preview');

Route::middleware(['auth', RoleAccess::class])->group(function () {

    // Halaman input kode / QR scan
    Route::get('/kode-kuis', KodeKuis::class)->name('kode.kuis');

    // Halaman mengerjakan kuis
    Route::get('/kuis/{kode}', LivewireKuis::class)->name('kuis.mulai');
    
    //  Halaman result kuis
    Route::get('/kuis/result-kuis/{hasil}', \App\Livewire\ResultKuis::class)->name('kuis.result-kuis');
    
    // Halaman lihat nilai (BARU)
    Route::get('/kuis/lihat-nilai/{hasil}', LihatNilai::class)->name('kuis.lihat-nilai');

    // Halaman Riwayat Kuis
    Route::get('/riwayat-kuis', \App\Livewire\RiwayatKuis::class)->name('riwayat.kuis');

    // Profile (Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Logout pengajar
Route::post('/pengajar/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/login');
})->name('filament.pengajar.auth.logout');

require __DIR__ . '/auth.php';