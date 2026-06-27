<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

#[Fillable([
    'name',
    'full_name',
    'nisn',
    'email',
    'password',
    'role',
    'account_status',
    'rejection_reason',
    'approved_at',
    'approved_by',
    'class',
    'phone',
    'avatar',
    'is_active',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser, HasAvatar, MustVerifyEmailContract
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(self::class, 'approved_by');
    }

    public function approvedUsers(): HasMany
    {
        return $this->hasMany(self::class, 'approved_by');
    }

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }

    public function fines(): HasMany
    {
        return $this->hasMany(Fine::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isMember(): bool
    {
        return $this->role === 'user';
    }

    public function isApproved(): bool
    {
        return $this->account_status === 'active';
    }

    public function canBorrowBooks(): bool
    {
        return $this->isMember() && $this->isApproved() && $this->is_active;
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return match ($panel->getId()) {
            'admin' => $this->isAdmin() && $this->isApproved() && $this->is_active,
            'user' => $this->canBorrowBooks(),
            default => false,
        };
    }

    public function getFilamentAvatarUrl(): ?string
    {
        if (! filled($this->avatar)) {
            return null;
        }

        if (filter_var($this->avatar, FILTER_VALIDATE_URL)) {
            return $this->avatar;
        }

        return Storage::disk('public')->url($this->avatar);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'approved_at' => 'datetime',
            'email_verified_at' => 'datetime',
            'is_active' => 'boolean',
            'password' => 'hashed',
        ];
    }
}
