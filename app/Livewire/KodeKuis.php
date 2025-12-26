<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Kuis;
use App\Models\HasilKuis;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class KodeKuis extends Component
{
    public string $kode = '';

    public function mulaiKuis()
    {
        $user = Auth::user();

        $this->validate([
            'kode' => 'required|min:4',
        ]);

        $kuis = Kuis::aktif()
            ->tersedia()
            ->where('kode_kuis', strtoupper($this->kode))
            ->firstOrFail();

        $percobaanKe = HasilKuis::where('kuis_id', $kuis->id)
            ->where('siswa_id', $user->id)
            ->count() + 1;

        $hasil = HasilKuis::create([
            'kuis_id' => $kuis->id,
            'siswa_id' => $user->id,
            'waktu_mulai' => now(),
            'total_poin' => $kuis->total_poin,
            'status' => 'sedang_mengerjakan',
            'percobaan_ke' => $percobaanKe,
        ]);

        return redirect()->route('kuis', $hasil->id);
    }

    public function render()
    {
        return view('livewire.kode-kuis');
    }
}
