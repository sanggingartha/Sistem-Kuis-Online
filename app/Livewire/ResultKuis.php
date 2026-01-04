<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\HasilKuis;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;

#[Layout('layouts.sidebar')]
class ResultKuis extends Component
{
    public HasilKuis $hasilKuis;

    public function mount($hasil)
    {
        $this->hasilKuis = HasilKuis::with(['kuis', 'jawabanPilihanGanda', 'jawabanEssay'])
            ->findOrFail($hasil);

        // Pastikan hanya pemilik yang bisa akses
        if ($this->hasilKuis->user_id !== Auth::id()) {
            abort(403, 'Akses ditolak');
        }
    }

    public function render()
    {
        return view('livewire.result-kuis');
    }
}