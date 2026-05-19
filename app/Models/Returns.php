<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'borrow_id',
    'days_late',
    'amount',
    'is_paid',
    'paid_at',
    'condition'
])]
class Returns extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'is_paid' => 'boolean',
            'paid_at' => 'date',
        ];
    }

    // ==========================================
    // RELASI
    // ==========================================

    // Denda milik satu peminjaman
    public function borrow()
    {
        return $this->belongsTo(Borrow::class);
    }

    // Lewat borrow, bisa akses data siswa
    public function user()
    {
        return $this->borrow->user;
    }

    // Lewat borrow, bisa akses data buku
    public function book()
    {
        return $this->borrow->book;
    }

    // ==========================================
    // ACCESSOR
    // ==========================================

    // Format nominal denda ke Rupiah
    public function getFormattedAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }

    // Label status denda
    public function getStatusLabelAttribute(): string
    {
        return $this->is_paid ? 'Lunas' : 'Belum Lunas';
    }

    // Warna status untuk badge di tampilan
    public function getStatusColorAttribute(): string
    {
        return $this->is_paid ? 'green' : 'red';
    }

    // ==========================================
    // SCOPE
    // ==========================================

    // Filter denda yang belum dibayar
    public function scopeUnpaid($query)
    {
        return $query->where('is_paid', false);
    }

    // Filter denda yang sudah dibayar
    public function scopePaid($query)
    {
        return $query->where('is_paid', true);
    }

    // ==========================================
    // HELPER METHOD
    // ==========================================

    // Tandai denda sudah lunas
    public function markAsPaid(): void
    {
        $this->update([
            'is_paid' => true,
            'paid_at' => now(),
        ]);
    }
}
