<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'name',
    'description'
])]
class Category extends Model
{
    use HasFactory;

    // ==========================================
    // RELASI
    // ==========================================

    // Satu kategori memiliki banyak buku
    public function books()
    {
        return $this->hasMany(Book::class);
    }

    // Hanya buku yang tersedia dalam kategori ini
    public function availableBooks()
    {
        return $this->hasMany(Book::class)
            ->where('stock_available', '>', 0);
    }

    // ==========================================
    // ACCESSOR
    // ==========================================

    // Hitung total buku dalam kategori ini
    public function getTotalBooksAttribute(): int
    {
        return $this->books()->count();
    }

    // Hitung total buku yang tersedia
    public function getTotalAvailableAttribute(): int
    {
        return $this->books()
            ->where('stock_available', '>', 0)
            ->count();
    }

    // ==========================================
    // SCOPE
    // ==========================================

    // Filter kategori yang punya buku
    public function scopeHasBooks($query)
    {
        return $query->has('books');
    }

    // Urutkan berdasarkan nama
    public function scopeOrdered($query)
    {
        return $query->orderBy('name');
    }
}
