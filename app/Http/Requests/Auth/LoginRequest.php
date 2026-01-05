<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'email',
                'regex:/^[A-Za-z0-9@._]+$/'
            ],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * AUTH + BRUTE FORCE + LOCK 2 JAM
     */
    public function authenticate(): void
    {
        $user = User::where('email', $this->email)->first();

        // Cek akun terkunci
        if ($user && $user->locked_until && now()->lessThan($user->locked_until)) {
            throw ValidationException::withMessages([
                'email' => 'Akun terkunci sampai ' . $user->locked_until->format('d M Y H:i'),
            ]);
        }

        if (! Auth::attempt($this->only('email', 'password'))) {

            if ($user) {
                $user->increment('failed_attempts');

                if ($user->failed_attempts >= 5) {
                    $user->update([
                        'locked_until' => now()->addHours(2),
                        'failed_attempts' => 0,
                    ]);
                }
            }

            throw ValidationException::withMessages([
                'email' => 'Email atau password salah.',
            ]);
        }

        // Reset saat sukses
        if ($user) {
            $user->update([
                'failed_attempts' => 0,
                'locked_until' => null,
            ]);
        }
    }

    /**
     * RATE LIMIT CHECK
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => "Terlalu banyak percobaan login. Coba lagi dalam {$seconds} detik.",
        ]);
    }

    /**
     * KEY = email + IP
     */
    public function throttleKey(): string
    {
        return Str::lower($this->string('email')) . '|' . $this->ip();
    }
}
