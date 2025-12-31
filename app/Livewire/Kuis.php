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
use App\Services\GeminiService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;

#[Layout('layouts.sidebar')]
class Kuis extends Component
{
    public KuisModel $kuis;
    public HasilKuis $hasilKuis;

    public $soalPG = [];
    public $soalEssay = [];
    public $currentIndex = 0;
    public $currentType = 'pg'; // 'pg' atau 'essay'
    
    // Array untuk menyimpan jawaban PG (key = soal_id, value = opsi_id)
    public $jawabanSekarang = [];

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
                'total_poin' => $this->kuis->total_poin,
            ]);
        }

        // Ambil soal PG dan Essay
        $this->soalPG = SoalPilihanGanda::with('opsi')
            ->where('kuis_id', $this->kuis->id)
            ->orderBy('urutan')
            ->get()
            ->toArray();
            
        $this->soalEssay = SoalEssay::where('kuis_id', $this->kuis->id)
            ->orderBy('urutan')
            ->get()
            ->toArray();

        // Tentukan tipe soal pertama
        $this->currentType = !empty($this->soalPG) ? 'pg' : 'essay';
        $this->currentIndex = 0;
        
        // Initialize jawabanSekarang sebagai array untuk PG atau string untuk essay
        if ($this->currentType === 'pg') {
            $this->jawabanSekarang = [];
        } else {
            $this->jawabanSekarang = '';
        }
        
        // Load jawaban sebelumnya jika ada (untuk resume)
        $this->loadExistingAnswer();
    }

    protected function loadExistingAnswer()
    {
        if ($this->currentType === 'pg' && !empty($this->soalPG)) {
            // Load semua jawaban PG yang sudah ada (hanya jika sudah pernah dijawab)
            $jawabanPGExisting = JawabanPilihanGanda::where('hasil_kuis_id', $this->hasilKuis->id)
                ->get()
                ->keyBy('soal_id');
            
            // Hanya load jawaban yang memang sudah pernah disimpan di database
            foreach ($this->soalPG as $soal) {
                if (isset($jawabanPGExisting[$soal['id']]) && $jawabanPGExisting[$soal['id']]->opsi_id) {
                    $this->jawabanSekarang[$soal['id']] = $jawabanPGExisting[$soal['id']]->opsi_id;
                }
                // Jika belum ada jawaban, tidak perlu set apapun (biarkan kosong/null)
            }
        } elseif ($this->currentType === 'essay' && isset($this->soalEssay[$this->currentIndex])) {
            $soal = $this->soalEssay[$this->currentIndex];
            $jawaban = JawabanEssay::where('hasil_kuis_id', $this->hasilKuis->id)
                ->where('soal_id', $soal['id'])
                ->first();
            
            if ($jawaban) {
                $this->jawabanSekarang = $jawaban->jawaban_siswa;
            } else {
                $this->jawabanSekarang = '';
            }
        }
    }

    // Method untuk tombol "Lanjutkan ke Essay"
    public function lanjutKeEssay()
    {
        // Simpan semua jawaban PG
        $this->simpanJawabanPG();

        // Pindah ke essay jika ada, jika tidak ada langsung selesai
        if (!empty($this->soalEssay)) {
            $this->currentType = 'essay';
            $this->currentIndex = 0;
            $this->jawabanSekarang = '';
            $this->loadExistingAnswer();
        } else {
            // Jika tidak ada essay, langsung selesai
            $this->selesaikanKuis();
        }
    }

    public function next()
    {
        // Essay
        if ($this->currentType === 'essay') {
            $this->simpanJawabanEssay();
            
            $this->currentIndex++;
            
            // Jika masih ada essay
            if ($this->currentIndex < count($this->soalEssay)) {
                $this->jawabanSekarang = '';
                $this->loadExistingAnswer();
            } else {
                // Semua selesai
                $this->selesaikanKuis();
            }
        }
    }

    protected function simpanJawabanPG()
    {
        if (empty($this->soalPG)) return;
        
        // Simpan semua jawaban PG yang sudah dipilih
        foreach ($this->soalPG as $soal) {
            if (isset($this->jawabanSekarang[$soal['id']])) {
                JawabanPilihanGanda::updateOrCreate(
                    [
                        'hasil_kuis_id' => $this->hasilKuis->id,
                        'soal_id' => $soal['id'],
                    ],
                    [
                        'opsi_id' => $this->jawabanSekarang[$soal['id']],
                        'dijawab_pada' => now(),
                    ]
                );
            }
        }
    }

    protected function simpanJawabanEssay()
    {
        if (!isset($this->soalEssay[$this->currentIndex])) return;
        
        $soal = $this->soalEssay[$this->currentIndex];
        
        JawabanEssay::updateOrCreate(
            [
                'hasil_kuis_id' => $this->hasilKuis->id,
                'soal_id' => $soal['id'],
            ],
            [
                'jawaban_siswa' => $this->jawabanSekarang,
                'poin_maksimal' => $soal['poin_maksimal'] ?? 20,
                'status_penilaian' => 'belum_dinilai',
                'dijawab_pada' => now(),
            ]
        );
    }

    protected function selesaikanKuis()
    {
        // Update status kuis
        $this->hasilKuis->update([
            'status' => 'selesai',
            'waktu_selesai' => now(),
        ]);

        // Hitung durasi
        $this->hasilKuis->hitungDurasi();

        // Proses penilaian
        $this->prosesSelesaiKuis();

        // Redirect ke halaman terima kasih
        session()->flash('success', 'Kuis berhasil dikumpulkan! Jawaban essay sedang dinilai oleh AI.');
        return redirect()->route('kuis.result-kuis', ['hasil' => $this->hasilKuis->id]);
    }

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
                } else {
                    $jawaban->update([
                        'poin_diperoleh' => 0,
                        'benar' => false,
                    ]);
                }
            }

            // Update poin PG di hasil kuis
            $this->hasilKuis->update([
                'poin_pilgan' => $totalPoinPG,
                'poin_diperoleh' => $totalPoinPG,
            ]);
            
            // Hitung persentase sementara
            $this->hasilKuis->hitungPersentase();

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
                    // Update status menjadi sedang proses
                    $jawaban->update(['status_penilaian' => 'sedang_proses']);
                    
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
                        
                        $jawaban->update(['status_penilaian' => 'error']);
                    }

                } catch (\Exception $e) {
                    Log::error('Exception saat nilai essay', [
                        'jawaban_id' => $jawaban->id,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    
                    $jawaban->update(['status_penilaian' => 'error']);
                }
            }

            // Refresh hasil kuis untuk ambil poin terbaru
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
        if ($this->currentType === 'essay' && isset($this->soalEssay[$this->currentIndex])) {
            $soalSekarang = $this->soalEssay[$this->currentIndex];
        }

        return view('livewire.kuis', [
            'soalSekarang' => $soalSekarang
        ]);
    }
}