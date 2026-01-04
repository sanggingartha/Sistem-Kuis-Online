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
use Carbon\Carbon;

#[Layout('layouts.sidebar')]
class Kuis extends Component
{
    public KuisModel $kuis;
    public HasilKuis $hasilKuis;

    public $soalPG = [];
    public $soalEssay = [];
    public $currentType = 'pg';

    public $jawabanPG = [];
    public $jawabanEssay = [];

    // Timer properties - HARUS PUBLIC untuk bisa diakses di view
    public $waktuSelesai = 0; // Simpan sebagai timestamp
    public $sisaWaktu = 0;
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
                'waktu_mulai_raw' => $lastHasil->getRawOriginal('waktu_mulai'),
                'percobaan_ke' => $lastHasil->percobaan_ke
            ]);
        } else {
            $percobaanKe = $lastHasil ? $lastHasil->percobaan_ke + 1 : 1;

            // PENTING: Set waktu_mulai secara eksplisit
            $waktuMulaiNow = now();

            $this->hasilKuis = HasilKuis::create([
                'kuis_id' => $this->kuis->id,
                'user_id' => $userId,
                'percobaan_ke' => $percobaanKe,
                'total_poin' => $this->kuis->total_poin,
                'waktu_mulai' => $waktuMulaiNow, // Set eksplisit
                'status' => 'sedang_mengerjakan',
            ]);

            // Refresh untuk memastikan data dari database
            $this->hasilKuis->refresh();

            Log::info('Membuat hasil kuis baru', [
                'hasil_kuis_id' => $this->hasilKuis->id,
                'waktu_mulai_set' => $waktuMulaiNow->format('Y-m-d H:i:s'),
                'waktu_mulai_db' => $this->hasilKuis->waktu_mulai,
                'waktu_mulai_raw' => $this->hasilKuis->getRawOriginal('waktu_mulai'),
                'percobaan_ke' => $percobaanKe
            ]);
        }

        // Setup timer - Hitung waktu selesai sebagai timestamp
        $this->setupTimer();

        // Cek apakah waktu sudah habis
        if ($this->sisaWaktu <= 0 && $this->timerActive) {
            Log::warning('Waktu sudah habis saat mount');
            $this->waktuHabis();
            return;
        }

        // Ambil soal PG dan Essay
        $this->loadSoal();

        // Load jawaban sebelumnya
        $this->loadExistingAnswer();
    }

    protected function setupTimer(): void
    {
        try {
            // Refresh dari database untuk data terbaru
            $this->hasilKuis->refresh();

            // Validasi waktu_mulai
            if (!$this->hasilKuis->waktu_mulai) {
                Log::error('waktu_mulai is NULL, setting now');
                $this->hasilKuis->waktu_mulai = now();
                $this->hasilKuis->save();
                $this->hasilKuis->refresh();
            }

            // Parse waktu mulai - PENTING: pastikan jadi Carbon instance
            $waktuMulai = $this->hasilKuis->waktu_mulai instanceof \Carbon\Carbon
                ? $this->hasilKuis->waktu_mulai
                : Carbon::parse($this->hasilKuis->waktu_mulai);

            // Hitung waktu selesai
            $waktuSelesaiCarbon = $waktuMulai->copy()->addMinutes($this->kuis->waktu_pengerjaan);

            // PENTING: Cast ke integer timestamp
            $this->waktuSelesai = (int) $waktuSelesaiCarbon->timestamp;

            // Hitung sisa waktu
            $sekarang = now();
            $sekarangTimestamp = (int) $sekarang->timestamp;

            if ($sekarangTimestamp >= $this->waktuSelesai) {
                $this->sisaWaktu = 0;
                $this->timerActive = false;
            } else {
                $this->sisaWaktu = (int) ($this->waktuSelesai - $sekarangTimestamp);
            }

            Log::info('✅ Timer setup SUCCESS', [
                'hasil_kuis_id' => $this->hasilKuis->id,
                'waktu_mulai' => $waktuMulai->format('Y-m-d H:i:s'),
                'waktu_mulai_timestamp' => $waktuMulai->timestamp,
                'waktu_selesai' => $waktuSelesaiCarbon->format('Y-m-d H:i:s'),
                'waktu_selesai_timestamp' => $this->waktuSelesai,
                'waktu_selesai_type' => gettype($this->waktuSelesai),
                'sekarang' => $sekarang->format('Y-m-d H:i:s'),
                'sekarang_timestamp' => $sekarangTimestamp,
                'durasi_kuis_menit' => $this->kuis->waktu_pengerjaan,
                'sisa_waktu_detik' => $this->sisaWaktu,
                'timer_active' => $this->timerActive,
                'selisih_check' => ($this->waktuSelesai - $sekarangTimestamp)
            ]);

            // VALIDASI FINAL
            if (!is_int($this->waktuSelesai) || $this->waktuSelesai <= 0) {
                throw new \Exception("Invalid waktuSelesai: " . var_export($this->waktuSelesai, true));
            }

            if ($this->waktuSelesai < $sekarangTimestamp) {
                Log::warning('⚠️ waktuSelesai sudah lewat!');
            }
        } catch (\Exception $e) {
            Log::error('❌ Error setupTimer', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->sisaWaktu = 0;
            $this->timerActive = false;
            $this->waktuSelesai = 0;
        }
    }

    protected function loadSoal(): void
    {
        $this->soalPG = SoalPilihanGanda::with('opsi')
            ->where('kuis_id', $this->kuis->id)
            ->orderBy('urutan')
            ->get()
            ->toArray();

        $this->soalEssay = SoalEssay::where('kuis_id', $this->kuis->id)
            ->orderBy('urutan')
            ->get()
            ->toArray();

        $this->currentType = !empty($this->soalPG) ? 'pg' : 'essay';

        $this->jawabanPG = [];
        $this->jawabanEssay = [];
    }

    // Method untuk sync timer dari JavaScript
    public function getTimerData()
    {
        try {
            $now = now()->timestamp;
            $sisaWaktu = max(0, $this->waktuSelesai - $now);

            // Cek apakah waktu sudah habis
            if ($sisaWaktu <= 0 && $this->timerActive) {
                $this->timerActive = false;
                Log::info('Timer expired during sync');
                return [
                    'status' => 'expired',
                    'sisaWaktu' => 0,
                    'waktuSelesai' => $this->waktuSelesai
                ];
            }

            return [
                'status' => 'active',
                'sisaWaktu' => $sisaWaktu,
                'waktuSelesai' => $this->waktuSelesai,
                'serverTime' => $now
            ];
        } catch (\Exception $e) {
            Log::error('Error getTimerData', ['error' => $e->getMessage()]);
            return [
                'status' => 'error',
                'sisaWaktu' => 0,
                'waktuSelesai' => $this->waktuSelesai ?? now()->timestamp
            ];
        }
    }

    protected function loadExistingAnswer()
    {
        // Load jawaban PG
        if (!empty($this->soalPG)) {
            $jawabanPGExisting = JawabanPilihanGanda::where('hasil_kuis_id', $this->hasilKuis->id)
                ->get()
                ->keyBy('soal_id');

            foreach ($this->soalPG as $soal) {
                $this->jawabanPG[$soal['id']] = $jawabanPGExisting[$soal['id']]->opsi_id ?? null;
            }
        }

        // Load jawaban Essay
        if (!empty($this->soalEssay)) {
            $jawabanEssayExisting = JawabanEssay::where('hasil_kuis_id', $this->hasilKuis->id)
                ->get()
                ->keyBy('soal_id');

            foreach ($this->soalEssay as $soal) {
                $this->jawabanEssay[$soal['id']] = $jawabanEssayExisting[$soal['id']]->jawaban_siswa ?? '';
            }
        }
    }

    protected function validasiWaktu(): bool
    {
        try {
            $now = now()->timestamp;
            $sisaWaktu = max(0, $this->waktuSelesai - $now);

            if ($sisaWaktu <= 0 && $this->timerActive) {
                Log::info('Waktu habis terdeteksi di validasi');
                $this->waktuHabis();
                return false;
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Error validasi waktu', ['error' => $e->getMessage()]);
            return false;
        }
    }

    public function lanjutKeEssay()
    {
        try {
            Log::info('=== LANJUT KE ESSAY ===');

            if (!$this->validasiWaktu()) {
                session()->flash('error', 'Waktu sudah habis.');
                return;
            }

            $this->simpanJawabanPG();

            if (!empty($this->soalEssay)) {
                $this->currentType = 'essay';
                $this->loadExistingAnswer();

                Log::info('Berhasil beralih ke essay');
                session()->flash('success', 'Berhasil beralih ke soal essay.');
            } else {
                $this->selesaikanKuis();
            }
        } catch (\Exception $e) {
            Log::error('Error lanjutKeEssay', ['error' => $e->getMessage()]);
            session()->flash('error', 'Terjadi kesalahan.');
        }
    }

    public function waktuHabis()
    {
        try {
            Log::info('=== WAKTU HABIS ===');

            $this->hasilKuis->refresh();

            if ($this->hasilKuis->status !== 'sedang_mengerjakan') {
                $this->redirectRoute('kuis.waktu-habis', ['hasil' => $this->hasilKuis->id]);
                return;
            }

            // Simpan jawaban
            if ($this->currentType === 'pg') {
                $this->simpanJawabanPG();
            } else {
                $this->simpanSemuaJawabanEssay();
            }

            $this->hasilKuis->update([
                'status' => 'waktu_habis',
                'waktu_selesai' => now(),
            ]);

            $this->hasilKuis->hitungDurasi();
            $this->prosesSelesaiKuis();

            $this->timerActive = false;

            // Redirect Livewire
            $this->redirectRoute('kuis.waktu-habis', ['hasil' => $this->hasilKuis->id]);
        } catch (\Exception $e) {
            Log::error('Error waktuHabis', ['error' => $e->getMessage()]);
            session()->flash('error', 'Terjadi kesalahan.');
            $this->redirectRoute('kode.kuis');
        }
    }

    protected function simpanJawabanPG()
    {
        if (empty($this->soalPG)) return;

        foreach ($this->soalPG as $soal) {
            if (isset($this->jawabanPG[$soal['id']]) && $this->jawabanPG[$soal['id']] !== null) {
                JawabanPilihanGanda::updateOrCreate(
                    [
                        'hasil_kuis_id' => $this->hasilKuis->id,
                        'soal_id' => $soal['id'],
                    ],
                    [
                        'opsi_id' => $this->jawabanPG[$soal['id']],
                        'dijawab_pada' => now(),
                    ]
                );
            }
        }
    }

    protected function simpanSemuaJawabanEssay()
    {
        if (empty($this->soalEssay)) return;

        foreach ($this->soalEssay as $soal) {
            if (isset($this->jawabanEssay[$soal['id']]) && !empty(trim($this->jawabanEssay[$soal['id']]))) {
                JawabanEssay::updateOrCreate(
                    [
                        'hasil_kuis_id' => $this->hasilKuis->id,
                        'soal_id' => $soal['id'],
                    ],
                    [
                        'jawaban_siswa' => $this->jawabanEssay[$soal['id']],
                        'poin_maksimal' => $soal['poin_maksimal'] ?? 20,
                        'status_penilaian' => 'belum_dinilai',
                        'dijawab_pada' => now(),
                    ]
                );
            }
        }
    }

    public function selesaikanKuis()
    {
        if (!$this->validasiWaktu()) {
            session()->flash('error', 'Waktu sudah habis.');
            return;
        }

        if ($this->currentType === 'pg') {
            $this->simpanJawabanPG();
        }

        $this->simpanSemuaJawabanEssay();

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
            Log::error('Error proses selesai kuis', ['error' => $e->getMessage()]);
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
            Log::error('Error hitung poin PG', ['error' => $e->getMessage()]);
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
            Log::error('Error AI Grading', ['error' => $e->getMessage()]);
        }
    }

    public function render()
    {
        // Hitung sisa waktu real-time sebelum render
        $now = now()->timestamp;
        $this->sisaWaktu = max(0, $this->waktuSelesai - $now);

        Log::info('Render view', [
            'waktuSelesai' => $this->waktuSelesai,
            'sisaWaktu' => $this->sisaWaktu,
            'timerActive' => $this->timerActive,
            'now' => $now
        ]);

        return view('livewire.kuis', [
            'waktuSelesai' => $this->waktuSelesai,
            'sisaWaktu' => $this->sisaWaktu,
            'timerActive' => $this->timerActive,
        ]);
    }

    // Method untuk debugging dari browser console
    public function debugTimer()
    {
        return [
            'waktuSelesai' => $this->waktuSelesai,
            'waktuSelesaiDate' => date('Y-m-d H:i:s', $this->waktuSelesai),
            'sisaWaktu' => $this->sisaWaktu,
            'timerActive' => $this->timerActive,
            'now' => now()->format('Y-m-d H:i:s'),
            'nowTimestamp' => now()->timestamp,
        ];
    }
}
