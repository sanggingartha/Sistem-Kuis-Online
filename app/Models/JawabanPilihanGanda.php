<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JawabanPilihanGanda extends Model
{
    protected $table = 'jawaban_pilihan_ganda';

    protected $fillable = [
        'hasil_kuis_id',
        'soal_id',
        'opsi_id',
        'poin_diperoleh',
        'benar',
        'dijawab_pada',
    ];

    protected function casts(): array
    {
        return [
            'benar' => 'boolean',
            'dijawab_pada' => 'datetime',
        ];
    }

    // Relasi
    public function hasilKuis()
    {
        return $this->belongsTo(HasilKuis::class);
    }

    public function soal()
    {
        return $this->belongsTo(SoalPilihanGanda::class, 'soal_id');
    }

    public function opsi()
    {
        return $this->belongsTo(OpsiPilihanGanda::class, 'opsi_id');
    }

    // Helper Methods
    public function periksaJawaban()
    {
        if (!$this->opsi) {
            $this->benar = false;
            $this->poin_diperoleh = 0;
            $this->save();
            return;
        }

        $this->benar = $this->opsi->opsi_benar;
        $this->poin_diperoleh = $this->benar ? $this->opsi->poin : 0;
        $this->save();
    }

    public function isBenar(): bool
    {
        return $this->benar;
    }
}
