<?php

namespace App\Models;

use Database\Factories\LoanFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
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

    public const STATUS_BORROWED = 'borrowed';

    public const STATUS_RETURNED = 'returned';

    public const STATUS_OVERDUE = 'overdue';

    public const DEFAULT_FINE_AMOUNT_PER_DAY = 1000;

    protected static function booted(): void
    {
        static::saving(function (Loan $loan): void {
            $loan->normalizeLifecycleState();
        });
    }

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

    /**
     * @return array<string, string>
     */
    public static function statusOptions(): array
    {
        return [
            self::STATUS_BORROWED => 'Dipinjam',
            self::STATUS_RETURNED => 'Dikembalikan',
            self::STATUS_OVERDUE => 'Terlambat',
        ];
    }

    public static function statusLabel(string $state): string
    {
        return self::statusOptions()[$state] ?? $state;
    }

    /**
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNull('returned_at');
    }

    /**
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    public function scopeReturned(Builder $query): Builder
    {
        return $query->whereNotNull('returned_at');
    }

    /**
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    public function scopeCurrentlyBorrowed(Builder $query): Builder
    {
        return $query
            ->active()
            ->whereDate('due_date', '>=', today());
    }

    /**
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    public function scopeCurrentlyOverdue(Builder $query): Builder
    {
        return $query
            ->active()
            ->whereDate('due_date', '<', today());
    }

    public function resolvedStatus(): string
    {
        if ($this->returned_at !== null) {
            return self::STATUS_RETURNED;
        }

        $dueDate = $this->due_date?->copy() ?? today()->addDays(7);

        return $dueDate->endOfDay()->isPast()
            ? self::STATUS_OVERDUE
            : self::STATUS_BORROWED;
    }

    public function isReturned(): bool
    {
        return $this->resolvedStatus() === self::STATUS_RETURNED;
    }

    public function isOverdue(): bool
    {
        return $this->resolvedStatus() === self::STATUS_OVERDUE;
    }

    public function overdueDays(): int
    {
        if ($this->returned_at !== null) {
            $comparisonDate = $this->returned_at->copy()->startOfDay();
        } else {
            $comparisonDate = today()->startOfDay();
        }

        $dueDate = $this->due_date?->copy()?->startOfDay();

        if ($dueDate === null || $comparisonDate->lessThanOrEqualTo($dueDate)) {
            return 0;
        }

        return $dueDate->diffInDays($comparisonDate);
    }

    public function syncFine(int $amountPerDay = self::DEFAULT_FINE_AMOUNT_PER_DAY): void
    {
        $overdueDays = $this->overdueDays();
        $fine = $this->fine()->first();
        $totalAmount = $overdueDays * $amountPerDay;

        if ($overdueDays <= 0) {
            if ($fine !== null && $fine->status === 'unpaid') {
                $fine->delete();
            }

            return;
        }

        $isCurrentFineAlreadyPaid = $fine !== null
            && $fine->status === 'paid'
            && $fine->overdue_days === $overdueDays
            && (float) $fine->total_amount === (float) $totalAmount;

        $this->fine()->updateOrCreate(
            [],
            [
                'user_id' => $this->user_id,
                'overdue_days' => $overdueDays,
                'amount_per_day' => $amountPerDay,
                'total_amount' => $totalAmount,
                'status' => $isCurrentFineAlreadyPaid ? 'paid' : 'unpaid',
                'paid_at' => $isCurrentFineAlreadyPaid ? $fine?->paid_at : null,
            ],
        );
    }

    public static function syncOverdueFines(int $amountPerDay = self::DEFAULT_FINE_AMOUNT_PER_DAY): void
    {
        static::query()
            ->with('fine')
            ->where(function (Builder $query): void {
                $query->currentlyOverdue()->orWhereNotNull('returned_at');
            })
            ->chunkById(100, function ($loans) use ($amountPerDay): void {
                foreach ($loans as $loan) {
                    $loan->syncFine($amountPerDay);
                }
            });
    }

    public function hasOutstandingFine(): bool
    {
        $this->loadMissing('fine');

        return $this->fine !== null && $this->fine->status === 'unpaid';
    }

    public function settleFine(int $amountPerDay = self::DEFAULT_FINE_AMOUNT_PER_DAY): void
    {
        $this->syncFine($amountPerDay);
        $this->refresh()->load('fine');

        if ($this->fine === null) {
            return;
        }

        $this->fine->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);
    }

    public function checkoutBooks(): void
    {
        if ($this->isReturned()) {
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

    public function returnBooks(string $conditionOnReturn = 'good', int $amountPerDay = self::DEFAULT_FINE_AMOUNT_PER_DAY): void
    {
        if ($this->isReturned()) {
            return;
        }

        $this->syncFine($amountPerDay);
        $this->refresh()->load('fine');

        if ($this->hasOutstandingFine()) {
            throw ValidationException::withMessages([
                'fine' => 'Denda keterlambatan harus dilunasi sebelum pengembalian diproses.',
            ]);
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

            $this->update([
                'returned_at' => $returnedAt,
                'status' => self::STATUS_RETURNED,
            ]);

            $this->refresh();
            $this->syncFine($amountPerDay);
        });
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'loan_date' => 'datetime',
            'returned_at' => 'datetime',
        ];
    }

    protected function normalizeLifecycleState(): void
    {
        $loanDate = $this->loan_date?->copy() ?? now();

        $this->loan_date = $loanDate;
        $this->due_date ??= $loanDate->copy()->addDays(7)->toDateString();

        if ($this->returned_at !== null) {
            $this->status = self::STATUS_RETURNED;

            return;
        }

        $this->status = $this->resolvedStatus();
    }
}
