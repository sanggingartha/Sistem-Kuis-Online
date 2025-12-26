<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class Kuis extends Model
{
    protected $table = 'kuis';

    protected $fillable = [
        'user_id',
        'nama_kuis',
        'deskripsi',
        'kode_kuis',
        'barcode_path',
        'waktu_pengerjaan',
        'acak_soal',
        'acak_opsi',
        'total_poin',
        'poin_pilgan',
        'poin_essay',
        'status',
        'mulai_dari',
        'berakhir_pada',
    ];

    protected function casts(): array
    {
        return [
            'acak_soal' => 'boolean',
            'acak_opsi' => 'boolean',
            'mulai_dari' => 'datetime',
            'berakhir_pada' => 'datetime',
        ];
    }

    // Boot method
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($kuis) {
            if (empty($kuis->kode_kuis)) {
                $kuis->kode_kuis = self::generateKodeKuis();
            }
        });

        static::created(function ($kuis) {
            $kuis->generateQRCode();
        });

        static::updating(function ($kuis) {
            if ($kuis->isDirty('kode_kuis')) {
                $kuis->generateQRCode();
            }
        });

        static::deleting(function ($kuis) {
            $kuis->deleteQRCode();
        });
    }

    // Generate kode kuis unik
    public static function generateKodeKuis(): string
    {
        do {
            $letters = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 3);
            $numbers = str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT);
            $lastLetter = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 1);
            $kode = $letters . $numbers . $lastLetter;
        } while (self::where('kode_kuis', $kode)->exists());

        return $kode;
    }

    public function generateQRCode(): void
    {
        $this->deleteQRCode();

        $url = route('kode.kuis', [
            'kode' => $this->kode_kuis,
        ]);

        $qrCode = QrCode::format('svg')
            ->size(300)
            ->generate($url);

        $filename = 'qrcodes/' . $this->kode_kuis . '.svg';

        Storage::disk('public')->put($filename, $qrCode);

        $this->forceFill([
            'barcode_path' => $filename,
        ])->saveQuietly();
    }

    // Delete QR Code
    public function deleteQRCode(): void
    {
        if ($this->barcode_path && Storage::disk('public')->exists($this->barcode_path)) {
            Storage::disk('public')->delete($this->barcode_path);
        }
    }

    // Get QR Code URL
    public function getQRCodeUrlAttribute(): ?string
    {
        if (!$this->barcode_path) {
            return null;
        }

        // return Storage::disk('public')->url($this->barcode_path);
        return asset('storage/' . $this->barcode_path);
    }

    // Get QR Code Path
    public function getQRCodePathAttribute(): ?string
    {
        if (!$this->barcode_path) {
            return null;
        }

        return storage_path('app/public/' . $this->barcode_path);
    }

    // Relasi
    public function pengajar()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function soalPilihanGanda()
    {
        return $this->hasMany(SoalPilihanGanda::class)->orderBy('urutan');
    }

    public function soalEssay()
    {
        return $this->hasMany(SoalEssay::class)->orderBy('urutan');
    }

    public function hasilKuis()
    {
        return $this->hasMany(HasilKuis::class);
    }

    // Scopes
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeTersedia($query)
    {
        return $query->where('status', 'aktif')
            ->where(function ($q) {
                $q->whereNull('mulai_dari')
                    ->orWhere('mulai_dari', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('berakhir_pada')
                    ->orWhere('berakhir_pada', '>=', now());
            });
    }

    public function hitungTotalPoin(): void
    {
        $poinPilgan = $this->soalPilihanGanda()->sum('poin');
        $poinEssay  = $this->soalEssay()->sum('poin_maksimal');

        $this->forceFill([
            'poin_pilgan' => (int) $poinPilgan,
            'poin_essay'  => (int) $poinEssay,
            'total_poin'  => (int) ($poinPilgan + $poinEssay),
        ])->saveQuietly();
    }


    public function isTersedia(): bool
    {
        if ($this->status !== 'aktif') {
            return false;
        }

        if ($this->mulai_dari && $this->mulai_dari > now()) {
            return false;
        }

        if ($this->berakhir_pada && $this->berakhir_pada < now()) {
            return false;
        }

        return true;
    }

    public function hasQRCode(): bool
    {
        return !empty($this->barcode_path) && Storage::disk('public')->exists($this->barcode_path);
    }

    // Regenerate QR Code manually
    public function regenerateQRCode(): void
    {
        $this->generateQRCode();
    }
}
