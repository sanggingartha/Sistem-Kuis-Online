<?php
// app/Models/PenilaianAi.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenilaianAi extends Model
{
    protected $table = 'penilaian_ai';

    protected $fillable = [
        'jawaban_essay_id',
        'prompt_dikirim',
        'respon_ai',
        'waktu_proses',
        'status_request',
        'error_message',
        'model_versi',
        'token_digunakan',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relasi
    public function jawabanEssay()
    {
        return $this->belongsTo(JawabanEssay::class);
    }
}