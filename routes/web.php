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

    Route::get('/kuis/{kuis}/qr-code-preview', function (Kuis $kuis) {
        return view('kuis.qr-code-preview', ['kuis' => $kuis]);
    })->name('kuis.qr-code-preview');

    Route::middleware(['auth', RoleAccess::class])->group(function () {

        Route::get('/kode-kuis', KodeKuis::class)
            ->name('kode.kuis');

        // HALAMAN NGERJAIN KUIS (BERDASARKAN HASIL_KUIS)
        Route::get('/kuis/{hasil}', LivewireKuis::class)
            ->name('kuis');

        // Profile (dari Breeze)
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });

    Route::post('/pengajar/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/login');
    })->name('filament.pengajar.auth.logout');

    require __DIR__ . '/auth.php';
