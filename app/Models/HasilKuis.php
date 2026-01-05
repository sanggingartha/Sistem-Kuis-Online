<?php
// app/Models/HasilKuis.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

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

    // HELPER METHODS (Existing)
    public function hitungPersentase(): void
    {
        if ($this->total_poin > 0) {
            $this->updateQuietly([
                'persentase' => ($this->poin_diperoleh / $this->total_poin) * 100,
            ]);
        }
    }

    public function hitungDurasi()
    {
        // Pastikan waktu_mulai dan waktu_selesai ada
        if (!$this->waktu_mulai || !$this->waktu_selesai) {
            Log::warning('hitungDurasi dipanggil tapi waktu tidak lengkap', [
                'hasil_kuis_id' => $this->id,
                'waktu_mulai' => $this->waktu_mulai,
                'waktu_selesai' => $this->waktu_selesai
            ]);
            return;
        }

        try {
            // Pastikan keduanya Carbon instance
            $waktuMulai = $this->waktu_mulai instanceof \Carbon\Carbon
                ? $this->waktu_mulai
                : \Carbon\Carbon::parse($this->waktu_mulai);

            $waktuSelesai = $this->waktu_selesai instanceof \Carbon\Carbon
                ? $this->waktu_selesai
                : \Carbon\Carbon::parse($this->waktu_selesai);

            // Hitung durasi dalam detik
            $durasiDetik = $waktuSelesai->diffInSeconds($waktuMulai);

            // Update durasi_pengerjaan
            $this->update([
                'durasi_pengerjaan' => $durasiDetik
            ]);

            Log::info('Durasi pengerjaan berhasil dihitung', [
                'hasil_kuis_id' => $this->id,
                'waktu_mulai' => $waktuMulai->format('Y-m-d H:i:s'),
                'waktu_selesai' => $waktuSelesai->format('Y-m-d H:i:s'),
                'durasi_detik' => $durasiDetik,
                'durasi_menit' => floor($durasiDetik / 60)
            ]);
        } catch (\Exception $e) {
            Log::error('Error menghitung durasi', [
                'hasil_kuis_id' => $this->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    // TAMBAHKAN ACCESSOR untuk format durasi yang mudah dibaca
    public function getDurasiFormatAttribute()
    {
        if (!$this->durasi_pengerjaan) {
            return '-';
        }

        $menit = floor($this->durasi_pengerjaan / 60);
        $detik = $this->durasi_pengerjaan % 60;

        if ($menit > 0) {
            return "{$menit} menit {$detik} detik";
        }

        return "{$detik} detik";
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

    // NEW METHOD: Update Total Nilai (untuk AI)
    /**
     * Update total nilai setelah essay dinilai oleh AI
     * Method ini dipanggil otomatis setelah AI selesai menilai essay
     */
    public function updateTotalNilai(): void
    {
        // Hitung ulang total poin essay yang sudah dinilai
        $poinEssay = $this->jawabanEssay()
            ->where('status_penilaian', 'sudah_dinilai')
            ->sum('poin_diperoleh');

        // Hitung total poin yang diperoleh
        $totalPoinDiperoleh = $this->poin_pilgan + $poinEssay;

        // Hitung persentase
        $persentase = 0;
        if ($this->total_poin > 0) {
            $persentase = ($totalPoinDiperoleh / $this->total_poin) * 100;
        }

        // Update hasil kuis
        $this->updateQuietly([
            'poin_essay' => $poinEssay,
            'poin_diperoleh' => $totalPoinDiperoleh,
            'persentase' => $persentase,
        ]);
    }

    /**
     * Cek apakah semua essay sudah dinilai
     */
    public function isSemuaEssayDinilai(): bool
    {
        $totalEssay = $this->jawabanEssay()->count();

        if ($totalEssay === 0) {
            return true; // Tidak ada essay
        }

        $essayDinilai = $this->jawabanEssay()
            ->where('status_penilaian', 'sudah_dinilai')
            ->count();

        return $totalEssay === $essayDinilai;
    }

    /**
     * Cek apakah ada essay yang error
     */
    public function hasEssayError(): bool
    {
        return $this->jawabanEssay()
            ->where('status_penilaian', 'error')
            ->exists();
    }

    /**
     * Get progress penilaian essay (untuk UI)
     */
    public function getProgressPenilaianEssay(): array
    {
        $total = $this->jawabanEssay()->count();

        if ($total === 0) {
            return [
                'total' => 0,
                'dinilai' => 0,
                'belum_dinilai' => 0,
                'sedang_proses' => 0,
                'error' => 0,
                'persentase' => 100,
            ];
        }

        $dinilai = $this->jawabanEssay()->where('status_penilaian', 'sudah_dinilai')->count();
        $belumDinilai = $this->jawabanEssay()->where('status_penilaian', 'belum_dinilai')->count();
        $sedangProses = $this->jawabanEssay()->where('status_penilaian', 'sedang_proses')->count();
        $error = $this->jawabanEssay()->where('status_penilaian', 'error')->count();

        return [
            'total' => $total,
            'dinilai' => $dinilai,
            'belum_dinilai' => $belumDinilai,
            'sedang_proses' => $sedangProses,
            'error' => $error,
            'persentase' => round(($dinilai / $total) * 100, 2),
        ];
    }
}
