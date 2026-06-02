<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, object|string>>
     */
    public function rules(): array
    {
        return [
            'full_name' => ['required', 'string', 'max:255'],
            'nisn' => ['required', 'digits:10', 'unique:users,nisn'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'class' => ['required', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'full_name' => 'nama lengkap',
            'nisn' => 'NISN',
            'email' => 'email',
            'class' => 'kelas',
            'phone' => 'nomor telepon',
            'password' => 'kata sandi',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'full_name' => trim((string) $this->input('full_name')),
            'nisn' => preg_replace('/\D+/', '', (string) $this->input('nisn')),
            'email' => Str::lower(trim((string) $this->input('email'))),
            'class' => trim((string) $this->input('class')),
            'phone' => filled($this->input('phone')) ? trim((string) $this->input('phone')) : null,
        ]);
    }

    public function createUser(): User
    {
        return User::query()->create([
            'name' => $this->string('full_name')->toString(),
            'full_name' => $this->string('full_name')->toString(),
            'nisn' => $this->string('nisn')->toString(),
            'email' => $this->string('email')->toString(),
            'password' => $this->string('password')->toString(),
            'role' => 'user',
            'account_status' => 'pending',
            'rejection_reason' => null,
            'approved_at' => null,
            'approved_by' => null,
            'class' => $this->string('class')->toString(),
            'phone' => $this->input('phone'),
            'avatar' => null,
            'is_active' => true,
        ]);
    }
}
