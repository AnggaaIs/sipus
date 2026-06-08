<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'user_id',
    'book_id',
    'borrow_date',
    'due_date',
    'status',
])]
class Borrow extends Model
{
    use HasFactory;

    // Peminjaman milik satu siswa
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Peminjaman untuk satu buku
    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    // Satu peminjaman punya satu pengembalian
    public function return()
    {
        return $this->hasOne(Returns::class);
    }

    // Satu peminjaman punya satu denda (jika terlambat)
    public function fine()
    {
        return $this->hasOne(Fine::class);
    }

    // ==========================================
    // ACCESSOR
    // ==========================================

    // Hitung jumlah hari keterlambatan
    public function getDaysLateAttribute(): int
    {
        // Kalau sudah dikembalikan, pakai tanggal kembali
        // Kalau belum, pakai hari ini
        $returnDate = $this->return?->return_date ?? Carbon::today();

        if ($returnDate->gt($this->due_date)) {
            return $this->due_date->diffInDays($returnDate);
        }

        return 0;
    }

    // Hitung total denda (Rp 1.000 per hari)
    public function getFineAmountAttribute(): int
    {
        return $this->days_late * 1000;
    }

    // Format denda ke Rupiah
    public function getFormattedFineAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->fine_amount, 0, ',', '.');
    }

    // Cek apakah sudah melewati batas kembali
    public function getIsOverdueAttribute(): bool
    {
        return $this->status === 'dipinjam'
            && Carbon::today()->gt($this->due_date);
    }

    // ==========================================
    // SCOPE
    // ==========================================

    // Filter peminjaman yang masih aktif
    public function scopeActive($query)
    {
        return $query->where('status', 'dipinjam');
    }

    // Filter peminjaman yang terlambat
    public function scopeOverdue($query)
    {
        return $query->where('status', 'dipinjam')
            ->where('due_date', '<', Carbon::today());
    }

    // Filter peminjaman yang sudah dikembalikan
    public function scopeReturned($query)
    {
        return $query->where('status', 'dikembalikan');
    }
}
