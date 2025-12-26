<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\HasilKuis;
use App\Models\JawabanPilihanGanda;
use App\Models\JawabanEssay;
use App\Models\OpsiPilihanGanda;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Kuis extends Component
{
    public HasilKuis $hasil;
    public int $sisaDetik = 0;

    public array $jawabanPG = [];
    public array $jawabanEssay = [];

    protected $listeners = ['autoSubmit' => 'selesaikanKuis'];

    public function mount($hasil)
    {
        $this->hasil = HasilKuis::with([
            'kuis.soalPilihanGanda.opsi',
            'kuis.soalEssay'
        ])->findOrFail($hasil);

        abort_if($this->hasil->siswa_id !== Auth::id(), 403);
        abort_if($this->hasil->status !== 'sedang_mengerjakan', 403);

        $this->hitungSisaWaktu();
    }

    public function hitungSisaWaktu()
    {
        $selesai = $this->hasil->waktu_mulai
            ->addMinutes($this->hasil->kuis->waktu_pengerjaan);

        $this->sisaDetik = now()->diffInSeconds($selesai, false);

        if ($this->sisaDetik <= 0) {
            $this->selesaikanKuis();
        }
    }

    public function pilihOpsi($soalId, $opsiId)
    {
        $this->jawabanPG[$soalId] = $opsiId;
    }

    public function selesaikanKuis()
    {
        if ($this->hasil->status !== 'sedang_mengerjakan') return;

        $totalPG = 0;

        foreach ($this->jawabanPG as $soalId => $opsiId) {
            $opsi = OpsiPilihanGanda::find($opsiId);
            $poin = ($opsi && $opsi->opsi_benar) ? $opsi->poin : 0;

            JawabanPilihanGanda::updateOrCreate(
                [
                    'hasil_kuis_id' => $this->hasil->id,
                    'soal_id' => $soalId,
                ],
                [
                    'opsi_id' => $opsiId,
                    'poin_diperoleh' => $poin,
                    'benar' => $poin > 0,
                ]
            );

            $totalPG += $poin;
        }

        foreach ($this->jawabanEssay as $soalId => $jawaban) {
            JawabanEssay::updateOrCreate(
                [
                    'hasil_kuis_id' => $this->hasil->id,
                    'soal_id' => $soalId,
                ],
                [
                    'jawaban_siswa' => $jawaban,
                ]
            );
        }

        $this->hasil->update([
            'poin_pilgan' => $totalPG,
            'poin_diperoleh' => $totalPG,
            'status' => 'selesai',
            'waktu_selesai' => now(),
        ]);

        $this->hasil->hitungDurasi();
        $this->hasil->hitungPersentase();

        return redirect()->route('kode.kuis')
            ->with('success', 'Kuis selesai');
    }

    public function render()
    {
        return view('livewire.kuis');
    }
}
