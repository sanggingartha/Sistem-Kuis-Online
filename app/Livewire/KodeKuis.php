<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Kuis;
use Illuminate\Http\Request;
use Livewire\Attributes\Layout;

#[Layout('layouts.sidebar')]
class KodeKuis extends Component
{
    public ?Kuis $kuis = null;
    public $kode_input = '';

    public function mount(Request $request)
    {
        // QR scan
        if ($request->filled('kode')) {
            $this->kode_input = $request->kode;
            $this->loadKuis();
        }
    }

    public function loadKuis()
    {
        $this->kuis = Kuis::where('kode_kuis', $this->kode_input)->first();
        if (!$this->kuis) {
            session()->flash('error', 'Kode kuis tidak valid');
        }
    }

    public function mulaiKuis()
    {
        if (!$this->kuis) {
            session()->flash('error', 'Kode kuis tidak valid');
            return;
        }

        // Validasi status & waktu
        if ($this->kuis->status !== 'aktif') {
            session()->flash('error', 'Kuis belum aktif');
            return;
        }

        if (! now()->between($this->kuis->mulai_dari, $this->kuis->berakhir_pada)) {
            session()->flash('error', 'Kuis belum dimulai atau sudah selesai');
            return;
        }

        return redirect()->route('kuis.mulai', $this->kuis->kode_kuis);
    }

    public function render()
    {
        return view('livewire.kode-kuis');
    }
}
