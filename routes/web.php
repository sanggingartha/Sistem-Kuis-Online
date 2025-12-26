<?php

use App\Http\Controllers\ProfileController;
use App\Http\Middleware\RoleAccess;
use App\Livewire\KodeKuis;
use App\Livewire\Kuis as LivewireKuis;
use App\Models\Kuis;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

// QR preview untuk pengajar
Route::get('/kuis/{kuis}/qr-code-preview', function (Kuis $kuis) {
    return view('kuis.qr-code-preview', ['kuis' => $kuis]);
})->name('kuis.qr-code-preview');

Route::middleware(['auth', RoleAccess::class])->group(function () {

    // Halaman input kode / QR scan
    Route::get('/kode-kuis', KodeKuis::class)->name('kode.kuis');

    // Halaman mengerjakan kuis
    Route::get('/kuis/{kode}', LivewireKuis::class)->name('kuis.mulai');

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
