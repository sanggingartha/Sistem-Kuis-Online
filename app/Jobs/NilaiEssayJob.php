<?php
// app/Jobs/NilaiEssayJob.php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use App\Models\JawabanEssay;
use App\Services\GeminiService;

class NilaiEssayJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 2;
    public int $timeout = 120;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $jawabanEssayId
    ) {}

    /**
     * Execute the job.
     */
    public function handle(GeminiService $gemini): void
    {
        $jawabanEssay = JawabanEssay::find($this->jawabanEssayId);
        
        if (!$jawabanEssay) {
            Log::warning("Jawaban essay tidak ditemukan", ['id' => $this->jawabanEssayId]);
            return;
        }

        if (in_array($jawabanEssay->status_penilaian, ['belum_dinilai', 'error'])) {
            $result = $gemini->nilaiJawabanEssay($jawabanEssay);
            
            Log::info('Penilaian AI selesai', [
                'jawaban_id' => $this->jawabanEssayId,
                'success' => $result['success'] ?? false
            ]);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Job NilaiEssay gagal', [
            'jawaban_id' => $this->jawabanEssayId,
            'error' => $exception->getMessage()
        ]);
    }
}