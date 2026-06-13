<?php

namespace App\Models;

use Database\Factories\FineFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'loan_id',
    'user_id',
    'overdue_days',
    'amount_per_day',
    'total_amount',
    'status',
    'paid_at',
])]
class Fine extends Model
{
    /** @use HasFactory<FineFactory> */
    use HasFactory;

    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount_per_day' => 'decimal:2',
            'overdue_days' => 'integer',
            'paid_at' => 'datetime',
            'total_amount' => 'decimal:2',
        ];
    }
}
