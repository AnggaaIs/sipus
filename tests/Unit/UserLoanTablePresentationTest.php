<?php

use App\Filament\User\Resources\Loans\Tables\LoansTable;
use App\Models\Loan;
use App\Models\LoanItem;
use Illuminate\Support\Collection;
use Tests\TestCase;

uses(TestCase::class);

test('returned loan condition presentation reflects lost and damaged states', function () {
    $loan = new Loan([
        'status' => 'returned',
        'due_date' => now()->subDay(),
    ]);
    $loan->setRelation('loanItems', new Collection([
        new LoanItem(['condition_on_return' => 'lost']),
        new LoanItem(['condition_on_return' => 'damaged']),
    ]));

    expect(LoansTable::conditionLabel($loan))->toBe('Hilang, Rusak')
        ->and(LoansTable::conditionColor($loan))->toBe('danger')
        ->and(LoansTable::conditionIcon($loan))->toBe('heroicon-m-x-circle');
});

test('borrowed loan presentation shows pending return state', function () {
    $loan = new Loan([
        'status' => 'borrowed',
        'due_date' => now()->addDays(2),
    ]);
    $loan->setRelation('loanItems', new Collection([
        new LoanItem(['condition_on_return' => null]),
    ]));

    expect(LoansTable::conditionLabel($loan))->toBe('Menunggu pengembalian')
        ->and(LoansTable::conditionColor($loan))->toBe('gray')
        ->and(LoansTable::conditionIcon($loan))->toBe('heroicon-m-arrow-path')
        ->and(LoansTable::isDueSoon($loan))->toBeTrue()
        ->and(LoansTable::dueDateColor($loan))->toBe('warning')
        ->and(LoansTable::dueDateIcon($loan))->toBe('heroicon-m-clock')
        ->and(LoansTable::dueDateDescription($loan))->toBe('Jatuh tempo hampir tiba')
        ->and(LoansTable::loanCodeDescription($loan))->toContain('Segera jatuh tempo');
});

test('status presentation maps labels colors and icons correctly', function () {
    expect(LoansTable::statusLabel('borrowed'))->toBe('Dipinjam')
        ->and(LoansTable::statusColor('borrowed'))->toBe('info')
        ->and(LoansTable::statusIcon('borrowed'))->toBe('heroicon-m-book-open')
        ->and(LoansTable::statusLabel('returned'))->toBe('Dikembalikan')
        ->and(LoansTable::statusColor('returned'))->toBe('success')
        ->and(LoansTable::statusIcon('returned'))->toBe('heroicon-m-check-circle')
        ->and(LoansTable::statusLabel('overdue'))->toBe('Terlambat')
        ->and(LoansTable::statusColor('overdue'))->toBe('danger')
        ->and(LoansTable::statusIcon('overdue'))->toBe('heroicon-m-exclamation-triangle');
});
