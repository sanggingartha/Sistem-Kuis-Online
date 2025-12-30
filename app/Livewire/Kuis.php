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
use App\Services\GeminiService; // ✅ TAMBAHKAN INI
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; // ✅ TAMBAHKAN INI
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
                'total_poin' => $this->kuis->total_poin, // ✅ TAMBAHKAN INI
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
        // Simpan jawaban sekarang
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
                    'poin_maksimal' => $soal['poin_maksimal'] ?? 20, // ✅ TAMBAHKAN INI
                    'status_penilaian' => 'belum_dinilai', // ✅ TAMBAHKAN INI
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

        // ✅ MODIFIKASI BAGIAN INI - Jika semua selesai
        if ($this->currentType === 'essay' && $this->currentIndex >= count($this->soalEssay)) {
            // Update status kuis
            $this->hasilKuis->update([
                'status' => 'selesai',
                'waktu_selesai' => now(), // ✅ TAMBAHKAN INI
            ]);

            // ✅ PROSES PENILAIAN OTOMATIS
            $this->prosesSelesaiKuis();

            session()->flash('success', 'Kuis berhasil dikumpulkan! Jawaban essay sedang dinilai oleh AI.');
            return redirect()->route('kode.kuis');
        }
    }

    // ✅ METHOD BARU 1: Proses setelah kuis selesai
    protected function prosesSelesaiKuis()
    {
        try {
            // 1. Hitung poin pilihan ganda
            $this->hitungPoinPilihanGanda();

            // 2. Trigger AI untuk nilai essay
            $this->nilaiEssayDenganAI();

        } catch (\Exception $e) {
            Log::error('Error proses selesai kuis', [
                'hasil_kuis_id' => $this->hasilKuis->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    // ✅ METHOD BARU 2: Hitung poin PG
    protected function hitungPoinPilihanGanda()
    {
        try {
            $jawabanPG = JawabanPilihanGanda::where('hasil_kuis_id', $this->hasilKuis->id)
                ->with(['opsi', 'soal'])
                ->get();

            $totalPoinPG = 0;

            foreach ($jawabanPG as $jawaban) {
                if ($jawaban->opsi && $jawaban->opsi->opsi_benar) {
                    $poin = $jawaban->soal->poin ?? 0;
                    
                    $jawaban->update([
                        'poin_diperoleh' => $poin,
                        'benar' => true,
                    ]);
                    
                    $totalPoinPG += $poin;
                }
            }

            // Update poin PG di hasil kuis
            $this->hasilKuis->update([
                'poin_pilgan' => $totalPoinPG,
                'poin_diperoleh' => $totalPoinPG, // Sementara, akan ditambah poin essay nanti
            ]);

            Log::info('Poin PG dihitung', [
                'hasil_kuis_id' => $this->hasilKuis->id,
                'total_poin_pg' => $totalPoinPG
            ]);

        } catch (\Exception $e) {
            Log::error('Error hitung poin PG', [
                'hasil_kuis_id' => $this->hasilKuis->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    // ✅ METHOD BARU 3: Nilai essay dengan AI
    protected function nilaiEssayDenganAI()
    {
        try {
            $geminiService = app(GeminiService::class);
            
            $jawabanEssays = JawabanEssay::where('hasil_kuis_id', $this->hasilKuis->id)
                ->where('status_penilaian', 'belum_dinilai')
                ->get();

            if ($jawabanEssays->isEmpty()) {
                Log::info('Tidak ada essay untuk dinilai', [
                    'hasil_kuis_id' => $this->hasilKuis->id
                ]);
                return;
            }

            Log::info('Mulai penilaian AI untuk essay', [
                'hasil_kuis_id' => $this->hasilKuis->id,
                'jumlah_essay' => $jawabanEssays->count()
            ]);

            foreach ($jawabanEssays as $jawaban) {
                try {
                    // Panggil AI untuk menilai
                    $result = $geminiService->nilaiJawabanEssay($jawaban);
                    
                    if ($result['success']) {
                        Log::info('Essay dinilai AI', [
                            'jawaban_id' => $jawaban->id,
                            'skor' => $result['skor'],
                            'waktu_proses' => $result['waktu_proses'] ?? 0
                        ]);
                    } else {
                        Log::warning('AI gagal nilai essay', [
                            'jawaban_id' => $jawaban->id,
                            'error' => $result['error'] ?? 'Unknown error'
                        ]);
                    }
                    
                } catch (\Exception $e) {
                    Log::error('Exception saat nilai essay', [
                        'jawaban_id' => $jawaban->id,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }

            // ✅ IMPORTANT: Refresh hasil kuis untuk ambil poin terbaru
            $this->hasilKuis->refresh();
            
            Log::info('Penilaian AI selesai', [
                'hasil_kuis_id' => $this->hasilKuis->id,
                'poin_diperoleh' => $this->hasilKuis->poin_diperoleh,
                'persentase' => $this->hasilKuis->persentase
            ]);

        } catch (\Exception $e) {
            Log::error('Error AI Grading Service', [
                'hasil_kuis_id' => $this->hasilKuis->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
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