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
            ]);
            
            Log::info('Membuat hasil kuis baru', [
                'hasil_kuis_id' => $this->hasilKuis->id,
                'waktu_mulai' => $this->hasilKuis->waktu_mulai,
                'percobaan_ke' => $percobaanKe
            ]);
        }

        // Setup timer - PERBAIKAN UTAMA
        $this->initializeTimer();

        // Cek apakah waktu sudah habis dari awal
        if ($this->sisaWaktu <= 0) {
            Log::warning('Waktu sudah habis saat mount', [
                'hasil_kuis_id' => $this->hasilKuis->id,
                'sisa_waktu' => $this->sisaWaktu
            ]);
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

    /**
     * Initialize timer dengan perhitungan yang benar
     */
    protected function initializeTimer(): void
    {
        $this->waktuMulai = $this->hasilKuis->waktu_mulai;
        $this->waktuSelesai = $this->hasilKuis->waktu_mulai->copy()->addMinutes($this->kuis->waktu_pengerjaan);
        
        $sekarang = now();
        
        // Hitung sisa waktu dengan benar
        if ($this->waktuSelesai->greaterThan($sekarang)) {
            // Waktu masih tersisa
            $this->sisaWaktu = $this->waktuSelesai->diffInSeconds($sekarang);
        } else {
            // Waktu sudah habis
            $this->sisaWaktu = 0;
        }
        
        Log::info('Timer initialized', [
            'waktu_mulai' => $this->waktuMulai->format('Y-m-d H:i:s'),
            'waktu_selesai' => $this->waktuSelesai->format('Y-m-d H:i:s'),
            'sekarang' => $sekarang->format('Y-m-d H:i:s'),
            'sisa_waktu_detik' => $this->sisaWaktu,
            'sisa_waktu_menit' => round($this->sisaWaktu / 60, 2)
        ]);
    }

    /**
     * Method untuk sync timer dari frontend (dipanggil setiap beberapa detik)
     */
    public function syncTimer()
    {
        if (!$this->validasiWaktu()) {
            return [
                'status' => 'expired',
                'sisaWaktu' => 0
            ];
        }

        return [
            'status' => 'active',
            'sisaWaktu' => $this->sisaWaktu
        ];
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
        if (!$this->validasiWaktu()) {
            return false;
        }
        return true;
    }

    public function lanjutKeEssay()
    {
        // Validasi waktu
        if (!$this->checkTimer()) {
            return;
        }

        $this->simpanJawabanPG();

        if (!empty($this->soalEssay)) {
            $this->currentType = 'essay';
            $this->currentIndex = 0;
            $this->jawabanSekarang = '';
            $this->loadExistingAnswer();
        } else {
            $this->selesaikanKuis();
        }
    }

    public function next()
    {
        // Validasi waktu
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
        $sekarang = now();
        
        // Refresh hasil kuis dari database
        $this->hasilKuis->refresh();
        
        // Cek apakah sudah selesai
        if ($this->hasilKuis->status !== 'sedang_mengerjakan') {
            session()->flash('error', 'Kuis sudah selesai.');
            return false;
        }
        
        // Hitung ulang waktu selesai berdasarkan waktu_mulai dari database
        $waktuSelesai = $this->hasilKuis->waktu_mulai->copy()->addMinutes($this->kuis->waktu_pengerjaan);
        
        // Cek apakah waktu sudah habis
        if ($sekarang->greaterThanOrEqualTo($waktuSelesai)) {
            Log::info('Waktu habis terdeteksi di validasi', [
                'sekarang' => $sekarang->format('Y-m-d H:i:s'),
                'waktu_selesai' => $waktuSelesai->format('Y-m-d H:i:s')
            ]);
            
            // Langsung panggil waktuHabis jika waktu sudah lewat
            $this->waktuHabis();
            return false;
        }
        
        // Update sisa waktu untuk frontend
        $this->sisaWaktu = $waktuSelesai->diffInSeconds($sekarang);
        
        return true;
    }

    public function waktuHabis()
    {
        try {
            // Cek apakah sudah diproses sebelumnya
            $this->hasilKuis->refresh();
            if ($this->hasilKuis->status !== 'sedang_mengerjakan') {
                return redirect()->route('kuis.waktu-habis', ['hasil' => $this->hasilKuis->id]);
            }

            // Simpan jawaban yang ada
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

            Log::info('Waktu habis diproses', [
                'hasil_kuis_id' => $this->hasilKuis->id,
                'user_id' => Auth::id()
            ]);

            return redirect()->route('kuis.waktu-habis', ['hasil' => $this->hasilKuis->id]);
        } catch (\Exception $e) {
            Log::error('Error waktu habis', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            session()->flash('error', 'Terjadi kesalahan saat memproses waktu habis.');
            return redirect()->route('kode.kuis');
        }
    }

    protected function simpanJawabanPG()
    {
        if (empty($this->soalPG)) return;

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

        session()->flash('success', 'Kuis berhasil dikumpulkan! Jawaban essay sedang dinilai oleh AI.');
        return redirect()->route('kuis.result-kuis', ['hasil' => $this->hasilKuis->id]);
    }

    protected function prosesSelesaiKuis()
    {
        try {
            $this->hitungPoinPilihanGanda();
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

            $this->hasilKuis->update([
                'poin_pilgan' => $totalPoinPG,
                'poin_diperoleh' => $totalPoinPG,
            ]);

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
                    $jawaban->update(['status_penilaian' => 'sedang_proses']);

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