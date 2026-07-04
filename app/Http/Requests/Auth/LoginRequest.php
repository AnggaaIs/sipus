<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
            'remember' => ['sometimes', 'boolean'],
        ];
    }

    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $login = $this->string('login')->trim()->toString();
        $normalizedLogin = Str::lower($login);

        $user = User::query()
            ->where('email', $normalizedLogin)
            ->orWhere('nisn', $login)
            ->first();

        if ($user instanceof User && Hash::check($this->string('password')->toString(), $user->password)) {
            if (! $user->is_active) {
                RateLimiter::hit($this->throttleKey());

                throw ValidationException::withMessages([
                    'login' => 'Akun ini sedang dinonaktifkan. Silakan hubungi petugas perpustakaan.',
                ]);
            }

            if (! $user->isApproved()) {
                RateLimiter::hit($this->throttleKey());

                throw ValidationException::withMessages([
                    'login' => $user->account_status === 'rejected' && filled($user->rejection_reason)
                        ? $user->rejection_reason
                        : 'Akun Anda belum aktif. Silakan tunggu persetujuan dari petugas perpustakaan.',
                ]);
            }
        }

        if (! $user instanceof User || ! Hash::check($this->string('password')->toString(), $user->password)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'login' => 'Email, NIS, atau kata sandi yang Anda masukkan tidak sesuai.',
            ]);
        }

        Auth::login($user, $this->boolean('remember'));
        RateLimiter::clear($this->throttleKey());
    }

    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('login')->toString()).'|'.$this->ip());
    }

    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'login' => "Terlalu banyak percobaan masuk. Coba lagi dalam {$seconds} detik.",
        ]);
    }
}
