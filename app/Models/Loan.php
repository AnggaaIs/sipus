<?php

namespace App\Models;

use Database\Factories\LoanFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

#[Fillable([
    'user_id',
    'loan_code',
    'loan_date',
    'due_date',
    'returned_at',
    'status',
    'notes',
])]
class Loan extends Model
{
    /** @use HasFactory<LoanFactory> */
    use HasFactory;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function loanItems(): HasMany
    {
        return $this->hasMany(LoanItem::class);
    }

    public function fine(): HasOne
    {
        return $this->hasOne(Fine::class);
    }

    public function checkoutBooks(): void
    {
        if ($this->status !== 'borrowed') {
            return;
        }

        DB::transaction(function (): void {
            $this->loadMissing('loanItems.book');

            foreach ($this->loanItems as $loanItem) {
                $book = Book::query()
                    ->whereKey($loanItem->book_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($book->available_copies < $loanItem->quantity) {
                    throw ValidationException::withMessages([
                        'loanItems' => "Stok buku {$book->title} tidak cukup.",
                    ]);
                }
            }

            foreach ($this->loanItems as $loanItem) {
                Book::query()
                    ->whereKey($loanItem->book_id)
                    ->decrement('available_copies', $loanItem->quantity);
            }
        });
    }

    public function returnBooks(string $conditionOnReturn = 'good', int $amountPerDay = 1000): void
    {
        if ($this->status === 'returned') {
            return;
        }

        DB::transaction(function () use ($conditionOnReturn, $amountPerDay): void {
            $returnedAt = now();
            $this->loadMissing('loanItems.book');

            foreach ($this->loanItems as $loanItem) {
                if ($loanItem->returned_at !== null) {
                    continue;
                }

                Book::query()
                    ->whereKey($loanItem->book_id)
                    ->lockForUpdate()
                    ->increment('available_copies', $loanItem->quantity);

                $loanItem->update([
                    'returned_at' => $returnedAt,
                    'condition_on_return' => $conditionOnReturn,
                ]);
            }

            $dueDate = $this->due_date->copy()->startOfDay();
            $returnedDate = $returnedAt->copy()->startOfDay();

            $overdueDays = $returnedDate->greaterThan($dueDate)
                ? $dueDate->diffInDays($returnedDate)
                : 0;

            $this->update([
                'returned_at' => $returnedAt,
                'status' => 'returned',
            ]);

            if ($overdueDays > 0) {
                $this->fine()->updateOrCreate(
                    ['user_id' => $this->user_id],
                    [
                        'overdue_days' => $overdueDays,
                        'amount_per_day' => $amountPerDay,
                        'total_amount' => $overdueDays * $amountPerDay,
                        'status' => 'unpaid',
                    ],
                );
            }
        });
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'loan_date' => 'date',
            'returned_at' => 'datetime',
        ];
    }
}
