<?php

use App\Models\Book;
use App\Models\Fine;
use App\Models\Loan;
use App\Models\LoanItem;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('seeder presentasi membuat data katalog pengguna dan sirkulasi yang konsisten', function () {
    $this->seed(DatabaseSeeder::class);

    expect(User::query()->where('role', 'admin')->count())->toBe(1)
        ->and(User::query()->where('account_status', 'pending')->count())->toBe(5)
        ->and(User::query()->where('account_status', 'rejected')->count())->toBe(1)
        ->and(User::query()->where('account_status', 'suspended')->count())->toBe(1)
        ->and(Book::query()->count())->toBe(30)
        ->and(Loan::query()->count())->toBe(4)
        ->and(Loan::query()->where('status', Loan::STATUS_BORROWED)->count())->toBe(1)
        ->and(Loan::query()->where('status', Loan::STATUS_OVERDUE)->count())->toBe(1)
        ->and(Loan::query()->where('status', Loan::STATUS_RETURNED)->count())->toBe(2)
        ->and(Fine::query()->where('status', 'unpaid')->count())->toBe(1)
        ->and(Fine::query()->where('status', 'paid')->count())->toBe(1)
        ->and(LoanItem::query()->count())->toBe(6)
        ->and(Book::query()->whereColumn('available_copies', '<', 'total_copies')->count())->toBe(3);

    expect(Fine::query()->with('loan')->get()->every(
        fn (Fine $fine): bool => $fine->user_id === $fine->loan->user_id,
    ))->toBeTrue();
});
