<?php

use App\Models\Book;
use App\Models\Loan;
use App\Models\User;
use App\Policies\BookPolicy;
use App\Policies\LoanPolicy;
use App\Policies\UserPolicy;

test('only active approved admins can manage books', function () {
    $policy = new BookPolicy;

    $admin = new User([
        'role' => 'admin',
        'account_status' => 'active',
        'is_active' => true,
    ]);

    $member = new User([
        'role' => 'user',
        'account_status' => 'active',
        'is_active' => true,
    ]);

    $pendingAdmin = new User([
        'role' => 'admin',
        'account_status' => 'pending',
        'is_active' => true,
    ]);

    expect($policy->viewAny($admin))->toBeTrue()
        ->and($policy->create($admin))->toBeTrue()
        ->and($policy->update($admin, new Book))->toBeTrue()
        ->and($policy->viewAny($member))->toBeFalse()
        ->and($policy->viewAny($pendingAdmin))->toBeFalse();
});

test('only active approved admins can manage users without deleting themselves', function () {
    $policy = new UserPolicy;

    $admin = new User([
        'role' => 'admin',
        'account_status' => 'active',
        'is_active' => true,
    ]);
    $admin->id = 1;

    $otherUser = new User([
        'role' => 'user',
        'account_status' => 'active',
        'is_active' => true,
    ]);
    $otherUser->id = 2;

    $member = new User([
        'role' => 'user',
        'account_status' => 'active',
        'is_active' => true,
    ]);

    expect($policy->viewAny($admin))->toBeTrue()
        ->and($policy->create($admin))->toBeTrue()
        ->and($policy->update($admin, $otherUser))->toBeTrue()
        ->and($policy->delete($admin, $otherUser))->toBeTrue()
        ->and($policy->delete($admin, $admin))->toBeFalse()
        ->and($policy->viewAny($member))->toBeFalse();
});

test('borrowed loans cannot be deleted before return', function () {
    $policy = new LoanPolicy;

    $admin = new User([
        'role' => 'admin',
        'account_status' => 'active',
        'is_active' => true,
    ]);

    $borrowedLoan = new Loan([
        'status' => 'borrowed',
    ]);

    $returnedLoan = new Loan([
        'status' => 'returned',
    ]);

    expect($policy->delete($admin, $borrowedLoan))->toBeFalse()
        ->and($policy->delete($admin, $returnedLoan))->toBeTrue();
});
