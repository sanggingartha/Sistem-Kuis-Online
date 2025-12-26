<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SoalPilihanGanda extends Model
{
    protected $table = 'soal_pilihan_ganda';

    protected $fillable = [
        'kuis_id',
        'pertanyaan',
        'urutan',
        'poin',
        'gambar_url',
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

    public function opsi()
    {
        return $this->hasMany(OpsiPilihanGanda::class, 'soal_id')->orderBy('urutan');
    }

    public function jawaban()
    {
        return $this->hasMany(JawabanPilihanGanda::class, 'soal_id');
    }

    // Helper Methods
    public function getOpsiBenar()
    {
        return $this->opsi()->where('opsi_benar', true)->first();
    }

    public function hasGambar(): bool
    {
        return !empty($this->gambar_url);
    }
}
