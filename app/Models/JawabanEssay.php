<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JawabanEssay extends Model
{
    protected $table = 'jawaban_essay';

    protected $fillable = [
        'hasil_kuis_id',
        'soal_id',
        'jawaban_siswa',
        'poin_maksimal',
        'feedback_ai',
        'skor_ai',
        'poin_diperoleh',
        'status_penilaian',
        'nilai_oleh',
        'dijawab_pada',
        'dinilai_pada',
    ];

    protected function casts(): array
    {
        return [
            'skor_ai' => 'decimal:2',
            'dijawab_pada' => 'datetime',
            'dinilai_pada' => 'datetime',
        ];
    }

    // Relasi
    public function hasilKuis()
    {
        return $this->belongsTo(HasilKuis::class);
    }

    public function soal()
    {
        return $this->belongsTo(SoalEssay::class, 'soal_id');
    }

    // Scopes
    public function scopeBelumDinilai($query)
    {
        return $query->where('status_penilaian', 'belum_dinilai');
    }

    public function scopeSudahDinilai($query)
    {
        return $query->where('status_penilaian', 'sudah_dinilai');
    }

    // Helper Methods
    public function nilaiOlehAI(float $skor, string $feedback)
    {
        $this->update([
            'skor_ai' => $skor,
            'feedback_ai' => $feedback,
            'poin_diperoleh' => round(($skor / 100) * $this->poin_maksimal),
            'status_penilaian' => 'sudah_dinilai',
            'nilai_oleh' => 'AI',
            'dinilai_pada' => now(),
        ]);
    }

    public function isSudahDinilai(): bool
    {
        return $this->status_penilaian === 'sudah_dinilai';
    }

    public function isBelumDinilai(): bool
    {
        return $this->status_penilaian === 'belum_dinilai';
    }
}
