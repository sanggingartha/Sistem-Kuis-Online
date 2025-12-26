<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HasilKuis extends Model
{
    protected $table = 'hasil_kuis';

    protected $fillable = [
        'kuis_id',
        'user_id',
        'waktu_mulai',
        'waktu_selesai',
        'durasi_pengerjaan',
        'total_poin',
        'poin_diperoleh',
        'poin_pilgan',
        'poin_essay',
        'persentase',
        'status',
        'percobaan_ke',
    ];

    protected $casts = [
        'waktu_mulai' => 'datetime',
        'waktu_selesai' => 'datetime',
        'persentase' => 'decimal:2',
    ];

    // RELASI
    public function kuis()
    {
        return $this->belongsTo(Kuis::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function jawabanPilihanGanda()
    {
        return $this->hasMany(JawabanPilihanGanda::class);
    }

    public function jawabanEssay()
    {
        return $this->hasMany(JawabanEssay::class);
    }

    // HELPER
    public function hitungPersentase(): void
    {
        if ($this->total_poin > 0) {
            $this->updateQuietly([
                'persentase' => ($this->poin_diperoleh / $this->total_poin) * 100,
            ]);
        }
    }

    public function hitungDurasi(): void
    {
        if ($this->waktu_selesai) {
            $this->updateQuietly([
                'durasi_pengerjaan' =>
                $this->waktu_selesai->diffInSeconds($this->waktu_mulai),
            ]);
        }
    }

    public function selesaikan(): void
    {
        $this->updateQuietly([
            'status' => 'selesai',
            'waktu_selesai' => now(),
        ]);

        $this->hitungDurasi();
        $this->hitungPersentase();
    }
}
