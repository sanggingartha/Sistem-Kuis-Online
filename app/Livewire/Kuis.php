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

        // Setup timer
        $this->waktuMulai = $this->hasilKuis->waktu_mulai;
        $this->waktuSelesai = $this->hasilKuis->waktu_mulai->copy()->addMinutes($this->kuis->waktu_pengerjaan);
        
        // Hitung sisa waktu dalam detik
        $sekarang = now();
        $this->sisaWaktu = max(0, $sekarang->diffInSeconds($this->waktuSelesai, false));
        
        // Jika waktu habis, langsung selesaikan
        if ($this->sisaWaktu <= 0) {
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

    public function lanjutKeEssay()
    {
        // Validasi waktu
        if (!$this->validasiWaktu()) {
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
        if (!$this->validasiWaktu()) {
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
            $this->redirect(route('kuis.result-kuis', ['hasil' => $this->hasilKuis->id]));
            return false;
        }
        
        // Hitung sisa waktu
        $waktuSelesai = $this->hasilKuis->waktu_mulai->copy()->addMinutes($this->kuis->waktu_pengerjaan);
        
        if ($sekarang->greaterThanOrEqualTo($waktuSelesai)) {
            $this->waktuHabis();
            return false;
        }
        
        return true;
    }

    public function waktuHabis()
    {
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

        session()->flash('warning', 'Waktu kuis telah habis! Jawaban Anda sudah disimpan.');
        return redirect()->route('kuis.result-kuis', ['hasil' => $this->hasilKuis->id]);
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