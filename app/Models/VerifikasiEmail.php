<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VerifikasiEmail extends Model
{
    public $timestamps = false;

    protected $table = 'verifikasi_email';

    protected $fillable = [
        'user_id',
        'kode',
        'kadaluwarsa_pada',
        'terverifikasi',
    ];

    protected function casts(): array
    {
        return [
            'kadaluwarsa_pada' => 'datetime',
            'terverifikasi' => 'boolean',
            'created_at' => 'datetime',
        ];
    }

    // Relasi
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeValid($query)
    {
        return $query->where('terverifikasi', false)
            ->where('kadaluwarsa_pada', '>', now());
    }

    public function scopeExpired($query)
    {
        return $query->where('kadaluwarsa_pada', '<', now());
    }

    // Helper Methods
    public function isExpired(): bool
    {
        return $this->kadaluwarsa_pada < now();
    }

    public function isValid(): bool
    {
        return !$this->terverifikasi && !$this->isExpired();
    }
}
