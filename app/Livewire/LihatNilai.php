<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\HasilKuis;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;

#[Layout('layouts.sidebar')]
class LihatNilai extends Component
{
    public HasilKuis $hasilKuis;
    public $jawabanPG;
    public $jawabanEssay;
    public $progressPenilaian;

    public function mount($hasil)
    {
        $this->hasilKuis = HasilKuis::with([
            'kuis',
            'jawabanPilihanGanda.soal.opsi',
            'jawabanPilihanGanda.opsi',
            'jawabanEssay.soal'
        ])->findOrFail($hasil);

        // Pastikan hanya pemilik yang bisa akses
        if ($this->hasilKuis->user_id !== Auth::id()) {
            abort(403, 'Akses ditolak');
        }

        // Load data jawaban
        $this->jawabanPG = $this->hasilKuis->jawabanPilihanGanda;
        $this->jawabanEssay = $this->hasilKuis->jawabanEssay;
        $this->progressPenilaian = $this->hasilKuis->getProgressPenilaianEssay();
    }

    public function getGradeColor()
    {
        $persentase = $this->hasilKuis->persentase;
        
        if ($persentase >= 80) return 'green';
        if ($persentase >= 70) return 'blue';
        if ($persentase >= 60) return 'yellow';
        return 'red';
    }

    public function getGradeText()
    {
        $persentase = $this->hasilKuis->persentase;
        
        if ($persentase >= 80) return 'Sangat Baik';
        if ($persentase >= 70) return 'Baik';
        if ($persentase >= 60) return 'Cukup';
        return 'Perlu Peningkatan';
    }

    public function render()
    {
        return view('livewire.lihat-nilai');
    }
}