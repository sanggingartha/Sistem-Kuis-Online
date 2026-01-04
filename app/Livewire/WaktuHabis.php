<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\HasilKuis;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;

#[Layout('layouts.sidebar')]
class WaktuHabis extends Component
{
    public HasilKuis $hasil;
    public $kuis;
    public $waktuMulai;
    public $waktuSelesai;
    public $durasiPengerjaan;
    public $statusPenilaian;

    public function mount(HasilKuis $hasil)
    {
        // Pastikan user hanya bisa akses hasil kuisnya sendiri
        if ($hasil->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        // Pastikan status memang waktu habis atau selesai
        if (!in_array($hasil->status, ['waktu_habis', 'selesai'])) {
            return redirect()->route('kuis.mulai', ['kode' => $hasil->kuis->kode_kuis]);
        }

        $this->hasil = $hasil;
        $this->kuis = $hasil->kuis;
        $this->waktuMulai = $hasil->waktu_mulai;
        $this->waktuSelesai = $hasil->waktu_selesai;
        $this->durasiPengerjaan = $hasil->durasi_pengerjaan;

        // Cek status penilaian essay
        $this->checkStatusPenilaian();
    }

    protected function checkStatusPenilaian()
    {
        $jawabanEssay = $this->hasil->jawabanEssay;

        if ($jawabanEssay->isEmpty()) {
            $this->statusPenilaian = 'no_essay';
            return;
        }

        $belumDinilai = $jawabanEssay->where('status_penilaian', 'belum_dinilai')->count();
        $sedangProses = $jawabanEssay->where('status_penilaian', 'sedang_proses')->count();
        $sudahDinilai = $jawabanEssay->where('status_penilaian', 'sudah_dinilai')->count();
        $error = $jawabanEssay->where('status_penilaian', 'error')->count();

        if ($sedangProses > 0) {
            $this->statusPenilaian = 'sedang_proses';
        } elseif ($belumDinilai > 0) {
            $this->statusPenilaian = 'belum_dinilai';
        } elseif ($error > 0) {
            $this->statusPenilaian = 'error';
        } elseif ($sudahDinilai == $jawabanEssay->count()) {
            $this->statusPenilaian = 'selesai';
        } else {
            $this->statusPenilaian = 'partial';
        }
    }

    public function refreshStatus()
    {
        $this->hasil->refresh();
        $this->checkStatusPenilaian();
        
        $this->dispatch('status-updated');
    }

    public function lihatHasil()
    {
        return redirect()->route('kuis.result-kuis', ['hasil' => $this->hasil->id]);
    }

    public function kembaliKeBeranda()
    {
        return redirect()->route('kode.kuis');
    }

    public function render()
    {
        return view('livewire.waktu-habis');
    }
}