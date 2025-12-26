<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SoalEssay extends Model
{
    protected $table = 'soal_essay';

    protected $fillable = [
        'kuis_id',
        'pertanyaan',
        'gambar_url',
        'jawaban_acuan',
        'rubrik_penilaian',
        'urutan',
        'poin_maksimal',
    ];

    protected static function booted()
    {
        static::saved(function ($soal) {
            $soal->kuis?->hitungTotalPoin();
        });

        static::deleted(function ($soal) {
            $soal->kuis?->hitungTotalPoin();
        });
    }

    // Relasi
    public function kuis()
    {
        return $this->belongsTo(Kuis::class);
    }

    public function jawaban()
    {
        return $this->hasMany(JawabanEssay::class, 'soal_id');
    }

    // Helper Methods
    public function hasGambar(): bool
    {
        return !empty($this->gambar_url);
    }

    public function hasJawabanAcuan(): bool
    {
        return !empty($this->jawaban_acuan);
    }
}
