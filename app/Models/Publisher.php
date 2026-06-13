<?php

namespace App\Models;

use Database\Factories\PublisherFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'name',
    'city',
])]
class Publisher extends Model
{
    /** @use HasFactory<PublisherFactory> */
    use HasFactory;

    public function books(): HasMany
    {
        return $this->hasMany(Book::class);
    }
}
