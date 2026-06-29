<?php

use App\Models\Book;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| White-Box Testing
|--------------------------------------------------------------------------
| Fokus: modul App\Models\Loan.
| Suite ini dipakai untuk statement coverage dan branch coverage karena
| langsung menutup cabang logika internal lifecycle peminjaman.
*/

test('pembuatan peminjaman menyimpan nilai default lifecycle yang wajar', function () {
    $this->travelTo(Carbon::parse('2026-06-28 08:15:30'));

    $loan = Loan::query()->create([
        'user_id' => User::factory()->member()->create()->getKey(),
        'loan_code' => 'SIPUS-20260628-ABCDE',
    ]);

    expect($loan->loan_date->format('Y-m-d H:i:s'))->toBe('2026-06-28 08:15:30')
        ->and($loan->due_date->toDateString())->toBe('2026-07-05')
        ->and($loan->status)->toBe(Loan::STATUS_BORROWED)
        ->and($loan->returned_at)->toBeNull();
});

test('status terhitung mengembalikan kondisi dipinjam terlambat dan dikembalikan dengan benar', function () {
    $this->travelTo(Carbon::parse('2026-06-28 10:00:00'));

    $borrowedLoan = Loan::factory()->create([
        'loan_date' => now()->subDay(),
        'due_date' => today()->addDay()->toDateString(),
        'returned_at' => null,
    ]);

    $overdueLoan = Loan::factory()->create([
        'loan_date' => now()->subDays(5),
        'due_date' => today()->subDay()->toDateString(),
        'returned_at' => null,
    ]);

    $returnedLoan = Loan::factory()->returned()->create();

    expect($borrowedLoan->resolvedStatus())->toBe(Loan::STATUS_BORROWED)
        ->and($overdueLoan->resolvedStatus())->toBe(Loan::STATUS_OVERDUE)
        ->and($returnedLoan->resolvedStatus())->toBe(Loan::STATUS_RETURNED);
});

test('perhitungan hari terlambat memakai hari ini untuk pinjaman aktif dan returned at untuk pinjaman selesai', function () {
    $this->travelTo(Carbon::parse('2026-06-28 10:00:00'));

    $activeLoan = Loan::factory()->create([
        'loan_date' => now()->subDays(4),
        'due_date' => today()->subDay()->toDateString(),
        'returned_at' => null,
    ]);

    $returnedLoan = Loan::factory()->create([
        'loan_date' => now()->subDays(6),
        'due_date' => today()->subDays(3)->toDateString(),
        'returned_at' => now()->subDay(),
    ]);

    $noDueDateLoan = Loan::factory()->create([
        'due_date' => null,
    ]);

    expect($activeLoan->overdueDays())->toBe(1)
        ->and($returnedLoan->overdueDays())->toBe(2)
        ->and($noDueDateLoan->overdueDays())->toBe(0);
});

test('sinkronisasi denda membuat denda baru untuk pinjaman yang terlambat', function () {
    $this->travelTo(Carbon::parse('2026-06-28 10:00:00'));

    $loan = Loan::factory()->create([
        'loan_date' => now()->subDays(5),
        'due_date' => today()->subDay()->toDateString(),
    ]);

    $loan->syncFine();
    $loan->refresh()->load('fine');

    expect($loan->fine)->not->toBeNull()
        ->and($loan->fine->status)->toBe('unpaid')
        ->and($loan->fine->overdue_days)->toBe(1);
});

test('sinkronisasi denda mempertahankan denda lunas saat pinjaman tidak lagi terlambat', function () {
    $this->travelTo(Carbon::parse('2026-06-28 10:00:00'));

    $loan = Loan::factory()->create([
        'loan_date' => now()->subDays(5),
        'due_date' => today()->subDay()->toDateString(),
    ]);

    $loan->syncFine();
    $loan->refresh()->load('fine');

    $loan->settleFine();
    $loan->refresh()->load('fine');

    expect($loan->fine)->not->toBeNull()
        ->and($loan->fine->status)->toBe('paid')
        ->and($loan->fine->paid_at)->not->toBeNull();

    $loan->syncFine();
    $loan->refresh()->load('fine');

    expect($loan->fine)->not->toBeNull()
        ->and($loan->fine->status)->toBe('paid');

    $loan->update([
        'due_date' => today()->addDay()->toDateString(),
    ]);

    $loan->syncFine();
    $loan->refresh()->load('fine');

    expect($loan->fine)->not->toBeNull()
        ->and($loan->fine->status)->toBe('paid');
});

test('sinkronisasi denda menghapus denda belum lunas saat pinjaman tidak lagi terlambat', function () {
    $this->travelTo(Carbon::parse('2026-06-28 10:00:00'));

    $unpaidLoan = Loan::factory()->create([
        'loan_date' => now()->subDays(5),
        'due_date' => today()->subDay()->toDateString(),
    ]);

    $unpaidLoan->syncFine();
    $unpaidLoan->refresh()->load('fine');

    expect($unpaidLoan->fine)->not->toBeNull()
        ->and($unpaidLoan->fine->status)->toBe('unpaid');

    $unpaidLoan->update([
        'due_date' => today()->addDay()->toDateString(),
    ]);

    $unpaidLoan->syncFine();
    $unpaidLoan->refresh()->load('fine');

    expect($unpaidLoan->fine)->toBeNull();
});

test('checkout mengurangi stok ketika jumlah buku mencukupi', function () {
    $availableBook = Book::factory()->create([
        'total_copies' => 5,
        'available_copies' => 5,
    ]);

    $loan = Loan::factory()->create();
    $loan->loanItems()->create([
        'book_id' => $availableBook->getKey(),
        'quantity' => 2,
    ]);

    $loan->checkoutBooks();

    expect($availableBook->fresh()->available_copies)->toBe(3);
});

test('checkout menolak transaksi ketika stok buku tidak cukup', function () {
    $insufficientBook = Book::factory()->create([
        'total_copies' => 1,
        'available_copies' => 1,
    ]);

    $insufficientLoan = Loan::factory()->create();
    $insufficientLoan->loanItems()->create([
        'book_id' => $insufficientBook->getKey(),
        'quantity' => 2,
    ]);

    expect(fn () => $insufficientLoan->checkoutBooks())
        ->toThrow(ValidationException::class);

    expect($insufficientBook->fresh()->available_copies)->toBe(1);
});

test('checkout dan pengembalian tidak melakukan apa apa untuk pinjaman yang sudah dikembalikan', function () {
    $book = Book::factory()->create([
        'total_copies' => 4,
        'available_copies' => 4,
    ]);

    $loan = Loan::factory()->returned()->create([
        'returned_at' => now(),
    ]);

    $loan->loanItems()->create([
        'book_id' => $book->getKey(),
        'quantity' => 1,
    ]);

    $loan->checkoutBooks();
    $loan->returnBooks();

    expect($book->fresh()->available_copies)->toBe(4)
        ->and($loan->fresh()->status)->toBe(Loan::STATUS_RETURNED);
});

test('pengembalian buku yang terlambat mewajibkan denda dilunasi lebih dulu', function () {
    $this->travelTo(Carbon::parse('2026-06-28 10:00:00'));

    $book = Book::factory()->create([
        'total_copies' => 2,
        'available_copies' => 2,
    ]);

    $loan = Loan::factory()->create([
        'loan_date' => now()->subDays(5),
        'due_date' => today()->subDay()->toDateString(),
    ]);

    $loan->loanItems()->create([
        'book_id' => $book->getKey(),
        'quantity' => 1,
    ]);

    $loan->checkoutBooks();

    expect($book->fresh()->available_copies)->toBe(1);

    expect(fn () => $loan->fresh()->returnBooks())
        ->toThrow(ValidationException::class);

    expect($loan->fresh()->returned_at)->toBeNull()
        ->and($book->fresh()->available_copies)->toBe(1);
});

test('pengembalian buku yang dendanya sudah dilunasi mengembalikan stok dan menutup pinjaman', function () {
    $this->travelTo(Carbon::parse('2026-06-28 10:00:00'));

    $book = Book::factory()->create([
        'total_copies' => 2,
        'available_copies' => 2,
    ]);

    $loan = Loan::factory()->create([
        'loan_date' => now()->subDays(5),
        'due_date' => today()->subDay()->toDateString(),
    ]);

    $loan->loanItems()->create([
        'book_id' => $book->getKey(),
        'quantity' => 1,
    ]);

    $loan->checkoutBooks();

    $loan->fresh()->settleFine();
    $loan->fresh()->returnBooks();

    $loan->refresh()->load('fine', 'loanItems');

    expect($book->fresh()->available_copies)->toBe(2)
        ->and($loan->status)->toBe(Loan::STATUS_RETURNED)
        ->and($loan->returned_at)->not->toBeNull()
        ->and($loan->fine)->not->toBeNull()
        ->and($loan->fine->status)->toBe('paid')
        ->and($loan->loanItems->every(fn ($loanItem): bool => $loanItem->returned_at !== null))->toBeTrue();
});

test('pengembalian buku melewati item pinjaman yang sudah ditandai kembali', function () {
    $this->travelTo(Carbon::parse('2026-06-28 11:00:00'));

    $alreadyReturnedBook = Book::factory()->create([
        'total_copies' => 5,
        'available_copies' => 2,
    ]);

    $activeBook = Book::factory()->create([
        'total_copies' => 4,
        'available_copies' => 1,
    ]);

    $loan = Loan::factory()->create([
        'loan_date' => now(),
        'due_date' => today()->addDay()->toDateString(),
    ]);

    $existingReturnedAt = now()->subMinute();

    $loan->loanItems()->create([
        'book_id' => $alreadyReturnedBook->getKey(),
        'quantity' => 2,
        'returned_at' => $existingReturnedAt,
        'condition_on_return' => 'good',
    ]);

    $loan->loanItems()->create([
        'book_id' => $activeBook->getKey(),
        'quantity' => 1,
    ]);

    $loan->returnBooks('good');
    $loan->refresh()->load('loanItems');

    expect($alreadyReturnedBook->fresh()->available_copies)->toBe(2)
        ->and($activeBook->fresh()->available_copies)->toBe(2)
        ->and($loan->loanItems->sortBy('id')->first()->returned_at->format('Y-m-d H:i:s'))->toBe($existingReturnedAt->format('Y-m-d H:i:s'))
        ->and($loan->loanItems->sortByDesc('id')->first()->returned_at)->not->toBeNull();
});
