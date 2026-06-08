<?php

namespace App\Models;

use App\Models\Author;
use Database\Factories\BookFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

#[Fillable([
    'isbn',
    'title',
    'slug',
    'category_id',
    'ddc_id',
    'publisher_id',
    'description',
    'publish_year',
    'pages',
    'language',
    'cover',
    'total_copies',
    'available_copies',
])]
class Book extends Model
{
    /** @use HasFactory<BookFactory> */
    use HasFactory, SoftDeletes;

    public function authors(): BelongsToMany
    {
        return $this->belongsToMany(Author::class, 'book_author');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function ddc(): BelongsTo
    {
        return $this->belongsTo(Ddc::class);
    }

    public function publisher(): BelongsTo
    {
        return $this->belongsTo(Publisher::class);
    }

    public function loanItems(): HasMany
    {
        return $this->hasMany(LoanItem::class);
    }

    public function getCoverUrlAttribute(): ?string
    {
        if (blank($this->cover)) {
            return null;
        }

        if (Str::startsWith($this->cover, ['http://', 'https://'])) {
            return $this->cover;
        }

        $baseUrl = rtrim((string) config('filesystems.disks.covers.url', asset('cover')), '/');

        return $baseUrl . '/' . ltrim($this->cover, '/');
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'available_copies' => 'integer',
            'pages' => 'integer',
            'publish_year' => 'integer',
            'total_copies' => 'integer',
        ];
    }
}
