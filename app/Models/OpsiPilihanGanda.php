<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OpsiPilihanGanda extends Model
{
    protected $table = 'opsi_pilihan_ganda';

    protected $fillable = [
        'soal_id',
        'teks_opsi',
        'opsi_benar',
        'poin',
        'urutan',
    ];

    protected function casts(): array
    {
        return [
            'opsi_benar' => 'boolean',
        ];
    }

    // Relasi
    public function soal()
    {
        return $this->belongsTo(SoalPilihanGanda::class, 'soal_id');
    }

    public function jawaban()
    {
        return $this->hasMany(JawabanPilihanGanda::class, 'opsi_id');
    }

    // Scopes
    public function scopeBenar($query)
    {
        return $query->where('opsi_benar', true);
    }

    // Helper Methods
    public function isBenar(): bool
    {
        return $this->opsi_benar;
    }
}
