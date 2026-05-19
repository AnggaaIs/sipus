<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    // Gunakan array standar Laravel
    protected $fillable = [
        'category_id', // Singular
        'isbn',
        'title',
        'author',
        'publisher',
        'publication_year', // Samakan dengan migration
        'stock',
        'stock_available',
        'cover',
        'description',
    ];

    // ==========================================
    // RELASI
    // ==========================================

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // ... sisa kode relasi, accessor, scope, dan helper tetap sama ...
}
