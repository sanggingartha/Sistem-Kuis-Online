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
    public $currentType = 'pg';

    public $jawabanSekarang = [];
    
    // Timer properties
    public $waktuMulai;
    public $waktuSelesai;
    public $sisaWaktu; // dalam detik
    public $timerActive = true;

    public function mount(string $kode)
    {
        $this->kuis = KuisModel::where('kode_kuis', $kode)->firstOrFail();

        // Validasi status & waktu
        if ($this->kuis->status !== 'aktif') {
            abort(403, 'Kuis belum aktif');
        }
        
        $now = now();
        if ($this->kuis->mulai_dari && $now->lt($this->kuis->mulai_dari)) {
            abort(403, 'Kuis belum dimulai');
        }
        
        if ($this->kuis->berakhir_pada && $now->gt($this->kuis->berakhir_pada)) {
            abort(403, 'Kuis sudah berakhir');
        }

        $userId = Auth::id();

        // Cek percobaan terakhir
        $lastHasil = HasilKuis::where('kuis_id', $this->kuis->id)
            ->where('user_id', $userId)
            ->latest('percobaan_ke')
            ->first();

        if ($lastHasil && $lastHasil->status === 'sedang_mengerjakan') {
            $this->hasilKuis = $lastHasil;
            
            Log::info('Melanjutkan kuis yang ada', [
                'hasil_kuis_id' => $lastHasil->id,
                'waktu_mulai' => $lastHasil->waktu_mulai,
                'percobaan_ke' => $lastHasil->percobaan_ke
            ]);
        } else {
            $percobaanKe = $lastHasil ? $lastHasil->percobaan_ke + 1 : 1;
            
            $this->hasilKuis = HasilKuis::create([
                'kuis_id' => $this->kuis->id,
                'user_id' => $userId,
                'percobaan_ke' => $percobaanKe,
                'total_poin' => $this->kuis->total_poin,
                'waktu_mulai' => now(),
                'status' => 'sedang_mengerjakan',
            ]);
            
            Log::info('Membuat hasil kuis baru', [
                'hasil_kuis_id' => $this->hasilKuis->id,
                'waktu_mulai' => $this->hasilKuis->waktu_mulai,
                'percobaan_ke' => $percobaanKe,
                'status' => $this->hasilKuis->status
            ]);
        }

        // Setup timer
        $this->initializeTimer();

        Log::info('Timer details after init', [
            'waktu_mulai' => $this->waktuMulai->format('Y-m-d H:i:s'),
            'waktu_selesai' => $this->waktuSelesai->format('Y-m-d H:i:s'),
            'sekarang' => now()->format('Y-m-d H:i:s'),
            'sisa_waktu' => $this->sisaWaktu,
            'status' => $this->hasilKuis->status
        ]);

        // Cek apakah waktu sudah habis
        if ($this->sisaWaktu <= 0) {
            Log::warning('Waktu sudah habis saat mount');
            $this->waktuHabis();
            return;
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

        // Initialize jawabanSekarang
        if ($this->currentType === 'pg') {
            $this->jawabanSekarang = [];
        } else {
            $this->jawabanSekarang = '';
        }

        // Load jawaban sebelumnya
        $this->loadExistingAnswer();
    }

    protected function initializeTimer(): void
    {
        if (!$this->hasilKuis->waktu_mulai) {
            Log::error('waktu_mulai is NULL!');
            $this->hasilKuis->waktu_mulai = now();
            $this->hasilKuis->save();
        }

        $this->waktuMulai = $this->hasilKuis->waktu_mulai;
        $this->waktuSelesai = $this->waktuMulai->copy()->addMinutes($this->kuis->waktu_pengerjaan);
        
        $sekarang = now();
        
        if ($this->waktuSelesai->gt($sekarang)) {
            $this->sisaWaktu = (int) $sekarang->diffInSeconds($this->waktuSelesai, false);
            
            if ($this->sisaWaktu < 0) {
                Log::warning('Sisa waktu negatif, set ke 0');
                $this->sisaWaktu = 0;
            }
        } else {
            $this->sisaWaktu = 0;
        }
        
        Log::info('Timer initialized', [
            'waktu_mulai' => $this->waktuMulai->format('Y-m-d H:i:s'),
            'waktu_selesai' => $this->waktuSelesai->format('Y-m-d H:i:s'),
            'sekarang' => $sekarang->format('Y-m-d H:i:s'),
            'sisa_waktu_detik' => $this->sisaWaktu,
            'sisa_waktu_menit' => round($this->sisaWaktu / 60, 2),
        ]);
    }

    public function syncTimer()
    {
        try {
            $this->hasilKuis->refresh();
            
            if ($this->hasilKuis->status !== 'sedang_mengerjakan') {
                return [
                    'status' => 'completed',
                    'sisaWaktu' => 0
                ];
            }
            
            $waktuSelesai = $this->hasilKuis->waktu_mulai->copy()->addMinutes($this->kuis->waktu_pengerjaan);
            $sekarang = now();
            
            if ($waktuSelesai->gt($sekarang)) {
                $sisaWaktu = (int) $sekarang->diffInSeconds($waktuSelesai, false);
                $sisaWaktu = max(0, $sisaWaktu);
                
                return [
                    'status' => 'active',
                    'sisaWaktu' => $sisaWaktu
                ];
            } else {
                return [
                    'status' => 'expired',
                    'sisaWaktu' => 0
                ];
            }
        } catch (\Exception $e) {
            Log::error('Error in syncTimer', [
                'error' => $e->getMessage()
            ]);
            
            return [
                'status' => 'error',
                'sisaWaktu' => 0
            ];
        }
    }

    protected function loadExistingAnswer()
    {
        if ($this->currentType === 'pg' && !empty($this->soalPG)) {
            $jawabanPGExisting = JawabanPilihanGanda::where('hasil_kuis_id', $this->hasilKuis->id)
                ->get()
                ->keyBy('soal_id');

            foreach ($this->soalPG as $soal) {
                if (isset($jawabanPGExisting[$soal['id']]) && $jawabanPGExisting[$soal['id']]->opsi_id) {
                    $this->jawabanSekarang[$soal['id']] = $jawabanPGExisting[$soal['id']]->opsi_id;
                }
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

    public function checkTimer()
    {
        return $this->validasiWaktu();
    }

    public function lanjutKeEssay()
    {
        try {
            Log::info('=== LANJUT KE ESSAY CALLED ===', [
                'currentType' => $this->currentType,
                'soalEssay_count' => count($this->soalEssay),
            ]);
            
            // Validasi waktu
            if (!$this->checkTimer()) {
                Log::warning('Timer validation failed');
                session()->flash('error', 'Waktu sudah habis.');
                return;
            }

            // Simpan jawaban PG
            $this->simpanJawabanPG();
            
            Log::info('Jawaban PG disimpan');

            if (!empty($this->soalEssay)) {
                $this->currentType = 'essay';
                $this->currentIndex = 0;
                $this->jawabanSekarang = '';
                $this->loadExistingAnswer();
                
                Log::info('=== SUCCESSFULLY SWITCHED TO ESSAY ===');
                
                session()->flash('success', 'Berhasil beralih ke soal essay.');
                
                // Dispatch event untuk memberitahu frontend
                $this->dispatch('switched-to-essay');
            } else {
                Log::info('No essay, finishing quiz');
                $this->selesaikanKuis();
            }
        } catch (\Exception $e) {
            Log::error('Error in lanjutKeEssay', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function next()
    {
        if (!$this->checkTimer()) {
            return;
        }

        if ($this->currentType === 'essay') {
            $this->simpanJawabanEssay();

            $this->currentIndex++;

            if ($this->currentIndex < count($this->soalEssay)) {
                $this->jawabanSekarang = '';
                $this->loadExistingAnswer();
            } else {
                $this->selesaikanKuis();
            }
        }
    }

    protected function validasiWaktu(): bool
    {
        try {
            $sekarang = now();
            
            $this->hasilKuis->refresh();
            
            if ($this->hasilKuis->status !== 'sedang_mengerjakan') {
                Log::warning('Status bukan sedang_mengerjakan');
                return false;
            }
            
            $waktuSelesai = $this->hasilKuis->waktu_mulai->copy()->addMinutes($this->kuis->waktu_pengerjaan);
            
            if ($sekarang->gte($waktuSelesai)) {
                Log::info('Waktu habis terdeteksi');
                $this->waktuHabis();
                return false;
            }
            
            // Update sisa waktu
            $this->sisaWaktu = (int) $sekarang->diffInSeconds($waktuSelesai, false);
            $this->sisaWaktu = max(0, $this->sisaWaktu);
            
            return true;
        } catch (\Exception $e) {
            Log::error('Error validasi waktu', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    public function waktuHabis()
    {
        try {
            Log::info('=== WAKTU HABIS DIPANGGIL ===');
            
            $this->hasilKuis->refresh();
            
            if ($this->hasilKuis->status !== 'sedang_mengerjakan') {
                Log::info('Status sudah berubah, redirect');
                return redirect()->route('kuis.waktu-habis', ['hasil' => $this->hasilKuis->id]);
            }

            // Simpan jawaban
            if ($this->currentType === 'pg') {
                $this->simpanJawabanPG();
            } elseif ($this->currentType === 'essay') {
                $this->simpanJawabanEssay();
            }

            // Update status
            $this->hasilKuis->update([
                'status' => 'waktu_habis',
                'waktu_selesai' => now(),
            ]);

            $this->hasilKuis->hitungDurasi();
            $this->prosesSelesaiKuis();

            $this->timerActive = false;

            Log::info('Waktu habis berhasil diproses');

            return redirect()->route('kuis.waktu-habis', ['hasil' => $this->hasilKuis->id]);
        } catch (\Exception $e) {
            Log::error('Error waktu habis', [
                'error' => $e->getMessage()
            ]);
            
            session()->flash('error', 'Terjadi kesalahan.');
            return redirect()->route('kode.kuis');
        }
    }

    protected function simpanJawabanPG()
    {
        if (empty($this->soalPG)) return;

        Log::info('Simpan jawaban PG', [
            'jumlah_soal' => count($this->soalPG),
            'jumlah_jawaban' => count($this->jawabanSekarang)
        ]);

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
        $this->hasilKuis->update([
            'status' => 'selesai',
            'waktu_selesai' => now(),
        ]);

        $this->hasilKuis->hitungDurasi();
        $this->prosesSelesaiKuis();

        $this->timerActive = false;

        session()->flash('success', 'Kuis berhasil dikumpulkan!');
        return redirect()->route('kuis.result-kuis', ['hasil' => $this->hasilKuis->id]);
    }

    protected function prosesSelesaiKuis()
    {
        try {
            $this->hitungPoinPilihanGanda();
            $this->nilaiEssayDenganAI();
        } catch (\Exception $e) {
            Log::error('Error proses selesai kuis', [
                'error' => $e->getMessage()
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

            $this->hasilKuis->update([
                'poin_pilgan' => $totalPoinPG,
                'poin_diperoleh' => $totalPoinPG,
            ]);

            $this->hasilKuis->hitungPersentase();
        } catch (\Exception $e) {
            Log::error('Error hitung poin PG', [
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
                return;
            }

            foreach ($jawabanEssays as $jawaban) {
                try {
                    $jawaban->update(['status_penilaian' => 'sedang_proses']);
                    $result = $geminiService->nilaiJawabanEssay($jawaban);

                    if (!$result['success']) {
                        $jawaban->update(['status_penilaian' => 'error']);
                    }
                } catch (\Exception $e) {
                    $jawaban->update(['status_penilaian' => 'error']);
                }
            }

            $this->hasilKuis->refresh();
        } catch (\Exception $e) {
            Log::error('Error AI Grading', [
                'error' => $e->getMessage()
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