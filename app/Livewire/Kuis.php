<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Kuis as KuisModel;
use App\Models\SoalPilihanGanda;
use App\Models\OpsiPilihanGanda;
use App\Models\JawabanPilihanGanda;
use App\Models\SoalEssay;
use App\Models\JawabanEssay;
use App\Models\HasilKuis;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Kuis extends Component
{
    public KuisModel $kuis;
    public HasilKuis $hasilKuis;

    public $soalPG = [];
    public $soalEssay = [];
    public $currentIndex = 0;
    public $currentType = 'pg'; // 'pg' atau 'essay'
    public $jawabanSekarang = null;

    public function mount(string $kode)
    {
        $this->kuis = KuisModel::where('kode_kuis', $kode)->firstOrFail();

        // Validasi status & waktu
        if ($this->kuis->status !== 'aktif') abort(403, 'Kuis belum aktif');
        $now = now();
        if (! $now->between($this->kuis->mulai_dari, $this->kuis->berakhir_pada)) {
            abort(403, 'Kuis belum dimulai atau sudah selesai');
        }

        $userId = Auth::id();

        // Cek percobaan terakhir
        $lastHasil = HasilKuis::where('kuis_id', $this->kuis->id)
            ->where('user_id', $userId)
            ->latest('percobaan_ke')
            ->first();

        if ($lastHasil && $lastHasil->status === 'sedang_mengerjakan') {
            $this->hasilKuis = $lastHasil;
        } else {
            $percobaanKe = $lastHasil ? $lastHasil->percobaan_ke + 1 : 1;
            $this->hasilKuis = HasilKuis::create([
                'kuis_id' => $this->kuis->id,
                'user_id' => $userId,
                'percobaan_ke' => $percobaanKe,
            ]);
        }

        // Ambil soal PG dan Essay
        $this->soalPG = SoalPilihanGanda::with('opsi')->where('kuis_id', $this->kuis->id)->orderBy('urutan')->get()->toArray();
        $this->soalEssay = SoalEssay::where('kuis_id', $this->kuis->id)->orderBy('urutan')->get()->toArray();

        $this->currentType = !empty($this->soalPG) ? 'pg' : 'essay';
        $this->currentIndex = 0;
        $this->jawabanSekarang = '';
    }

    public function next()
    {
        if ($this->currentType === 'pg') {
            $soal = $this->soalPG[$this->currentIndex];
            JawabanPilihanGanda::updateOrCreate(
                [
                    'hasil_kuis_id' => $this->hasilKuis->id,
                    'soal_id' => $soal['id'],
                ],
                [
                    'opsi_id' => $this->jawabanSekarang,
                ]
            );
        } else {
            $soal = $this->soalEssay[$this->currentIndex];
            JawabanEssay::updateOrCreate(
                [
                    'hasil_kuis_id' => $this->hasilKuis->id,
                    'soal_id' => $soal['id'],
                ],
                [
                    'jawaban_siswa' => $this->jawabanSekarang,
                ]
            );
        }

        $this->currentIndex++;
        $this->jawabanSekarang = '';

        // Jika PG habis, pindah ke Essay
        if ($this->currentType === 'pg' && $this->currentIndex >= count($this->soalPG)) {
            $this->currentIndex = 0;
            $this->currentType = 'essay';
        }

        // Jika semua selesai
        if ($this->currentType === 'essay' && $this->currentIndex >= count($this->soalEssay)) {
            session()->flash('success', 'Jawaban berhasil disimpan!');
            $this->hasilKuis->update(['status' => 'selesai']);
            return redirect()->route('kode.kuis');
        }
    }

    public function render()
    {
        $soalSekarang = null;
        if ($this->currentType === 'pg') {
            $soalSekarang = $this->soalPG[$this->currentIndex] ?? null;
        } else {
            $soalSekarang = $this->soalEssay[$this->currentIndex] ?? null;
        }

        return view('livewire.kuis', [
            'soalSekarang' => $soalSekarang
        ]);
    }
}
