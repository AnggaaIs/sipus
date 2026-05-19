<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable([
    'name',
    'email',
    'password',
    'nis',
    'role',
    'is_approved',
    'photo',
])]
class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;

    protected $fillable = [];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_approved'       => 'boolean',
        ];
    }

    // ==========================================
    // FILAMENT
    // ==========================================

    // Hanya admin yang boleh akses panel Filament
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->role === 'admin';
    }

    // ==========================================
    // RELASI
    // ==========================================

    // Siswa memiliki banyak riwayat peminjaman
    public function borrows()
    {
        return $this->hasMany(Borrow::class, 'user_id');
    }

    // Peminjaman yang masih aktif milik siswa
    public function activeBorrows()
    {
        return $this->hasMany(Borrow::class, 'user_id')
            ->where('status', 'dipinjam');
    }

    // ==========================================
    // HELPER METHOD
    // ==========================================

    // Cek apakah user adalah admin
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    // Cek apakah user adalah siswa
    public function isSiswa(): bool
    {
        return $this->role === 'siswa';
    }

    // Cek apakah siswa sudah diapprove
    public function isApproved(): bool
    {
        return $this->is_approved === true;
    }

    // Cek apakah siswa masih punya pinjaman aktif
    public function hasBorrows(): bool
    {
        return $this->activeBorrows()->exists();
    }

    // Cek apakah siswa punya denda belum lunas
    public function hasUnpaidFines(): bool
    {
        return $this->borrows()
            ->whereHas('fine', fn($q) => $q->where('is_paid', false))
            ->exists();
    }

    // ==========================================
    // ACCESSOR
    // ==========================================

    // URL foto profil
    public function getPhotoUrlAttribute(): string
    {
        return $this->photo
            ? asset('storage/' . $this->photo)
            : asset('images/default-avatar.png');
    }

    // ==========================================
    // SCOPE
    // ==========================================

    // Filter semua siswa
    public function scopeSiswa($query)
    {
        return $query->where('role', 'siswa');
    }

    // Filter siswa yang sudah diapprove
    public function scopeApproved($query)
    {
        return $query->where('role', 'siswa')
            ->where('is_approved', true);
    }

    // Filter siswa yang belum diapprove (UC-17)
    public function scopePending($query)
    {
        return $query->where('role', 'siswa')
            ->where('is_approved', false);
    }

    // Filter semua admin
    public function scopeAdmin($query)
    {
        return $query->where('role', 'admin');
    }
}
