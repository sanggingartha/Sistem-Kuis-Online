<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'email_verified_at',
        'is_email_verified'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'is_email_verified' => 'boolean',
            'password' => 'hashed',
        ];
    }

    // Relasi
    public function verifikasiEmail()
    {
        return $this->hasMany(VerifikasiEmail::class);
    }

    public function kuisSebagaiPengajar()
    {
        return $this->hasMany(Kuis::class, 'user_id');
    }

    public function hasilKuis()
    {
        return $this->hasMany(HasilKuis::class, 'user_id');
    }

    // Scopes
    public function scopePengajar($query)
    {
        return $query->where('role', 'pengajar');
    }

    public function scopeSiswa($query)
    {
        return $query->where('role', 'siswa');
    }

    // Helper Methods
    public function isPengajar(): bool
    {
        return $this->role === 'pengajar';
    }

    public function isSiswa(): bool
    {
        return $this->role === 'siswa';
    }
}
