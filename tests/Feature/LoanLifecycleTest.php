<?php

use App\Models\Book;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

test('loan creation stores the current timestamp and sensible defaults', function () {
    $this->travelTo(Carbon::parse('2026-06-27 09:15:30'));

    $loan = Loan::query()->create([
        'user_id' => User::factory()->member()->create()->getKey(),
        'loan_code' => 'SIPUS-20260627-ABCDE',
    ]);

    expect($loan->loan_date->format('Y-m-d H:i:s'))->toBe('2026-06-27 09:15:30')
        ->and($loan->due_date->toDateString())->toBe('2026-07-04')
        ->and($loan->status)->toBe(Loan::STATUS_BORROWED)
        ->and($loan->returned_at)->toBeNull();
});

test('checkout and return keep stock and loan lifecycle in sync', function () {
    $this->travelTo(Carbon::parse('2026-06-27 10:00:00'));

    $book = Book::factory()->create([
        'total_copies' => 5,
        'available_copies' => 5,
    ]);

    $loan = Loan::query()->create([
        'user_id' => User::factory()->member()->create()->getKey(),
        'loan_code' => 'SIPUS-20260627-FGHIJ',
        'loan_date' => now(),
        'due_date' => today()->addDays(7)->toDateString(),
    ]);

    $loan->loanItems()->create([
        'book_id' => $book->getKey(),
        'quantity' => 2,
    ]);

    $loan->checkoutBooks();

    expect($book->fresh()->available_copies)->toBe(3)
        ->and($loan->fresh()->status)->toBe(Loan::STATUS_BORROWED);

    $loan->fresh()->returnBooks();

    $loan->refresh();

    expect($book->fresh()->available_copies)->toBe(5)
        ->and($loan->status)->toBe(Loan::STATUS_RETURNED)
        ->and($loan->returned_at)->not->toBeNull();
});

test('overdue loans must settle the fine before they can be returned', function () {
    $this->travelTo(Carbon::parse('2026-06-27 10:00:00'));

    $book = Book::factory()->create([
        'total_copies' => 2,
        'available_copies' => 2,
    ]);

    $loan = Loan::query()->create([
        'user_id' => User::factory()->member()->create()->getKey(),
        'loan_code' => 'SIPUS-20260627-KLMNO',
        'loan_date' => now()->subDays(5),
        'due_date' => today()->subDay()->toDateString(),
    ]);

    $loan->loanItems()->create([
        'book_id' => $book->getKey(),
        'quantity' => 1,
    ]);

    expect($loan->fresh()->status)->toBe(Loan::STATUS_OVERDUE);

    $loan->fresh()->syncFine();
    $loan->refresh()->load('fine');

    expect($loan->fine)->not->toBeNull()
        ->and($loan->fine->overdue_days)->toBe(1)
        ->and($loan->fine->status)->toBe('unpaid');

    $loan->checkoutBooks();

    expect($book->fresh()->available_copies)->toBe(1);

    expect(fn () => $loan->fresh()->returnBooks())
        ->toThrow(ValidationException::class);

    expect($loan->fresh()->returned_at)->toBeNull()
        ->and($book->fresh()->available_copies)->toBe(1);

    $loan->fresh()->settleFine();

    $loan->fresh()->returnBooks();
    $loan->refresh()->load('fine');

    expect($book->fresh()->available_copies)->toBe(2)
        ->and($loan->status)->toBe(Loan::STATUS_RETURNED)
        ->and($loan->fine)->not->toBeNull()
        ->and($loan->fine->overdue_days)->toBe(1)
        ->and($loan->fine->status)->toBe('paid');
});
