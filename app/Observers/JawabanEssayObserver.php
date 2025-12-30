<?php

namespace App\Observers;

use App\Models\JawabanEssay;
use App\Jobs\NilaiJawabanEssayJob;

class JawabanEssayObserver
{
    /**
     * Handle the JawabanEssay "created" event.
     */
    public function created(JawabanEssay $jawabanEssay): void
    {
        // Otomatis trigger penilaian AI setelah jawaban dibuat
        // Gunakan dispatch untuk menjalankan di background (queue)
        NilaiJawabanEssayJob::dispatch($jawabanEssay)
            ->delay(now()->addSeconds(5)); // Delay 5 detik
    }

    /**
     * Handle the JawabanEssay "updated" event.
     */
    public function updated(JawabanEssay $jawabanEssay): void
    {
        //
    }

    /**
     * Handle the JawabanEssay "deleted" event.
     */
    public function deleted(JawabanEssay $jawabanEssay): void
    {
        //
    }
}