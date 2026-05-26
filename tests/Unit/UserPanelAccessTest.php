<?php

use App\Models\User;
use Filament\Panel;

function makePanel(string $id): Panel
{
    $panel = Mockery::mock(Panel::class);
    $panel->shouldReceive('getId')->andReturn($id);

    return $panel;
}

test('active admin can only access the admin panel', function () {
    $user = new User([
        'role' => 'admin',
        'account_status' => 'active',
        'is_active' => true,
    ]);

    expect($user->canAccessPanel(makePanel('admin')))->toBeTrue()
        ->and($user->canAccessPanel(makePanel('user')))->toBeFalse();
});

test('active member can only access the user panel', function () {
    $user = new User([
        'role' => 'user',
        'account_status' => 'active',
        'is_active' => true,
    ]);

    expect($user->canAccessPanel(makePanel('user')))->toBeTrue()
        ->and($user->canAccessPanel(makePanel('admin')))->toBeFalse()
        ->and($user->canBorrowBooks())->toBeTrue();
});

test('inactive or unapproved users cannot access any panel', function () {
    $inactiveUser = new User([
        'role' => 'user',
        'account_status' => 'active',
        'is_active' => false,
    ]);

    $pendingAdmin = new User([
        'role' => 'admin',
        'account_status' => 'pending',
        'is_active' => true,
    ]);

    expect($inactiveUser->canAccessPanel(makePanel('user')))->toBeFalse()
        ->and($pendingAdmin->canAccessPanel(makePanel('admin')))->toBeFalse();
});
