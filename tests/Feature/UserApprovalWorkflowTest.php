<?php

use App\Filament\Admin\Resources\Users\UserResource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

uses(RefreshDatabase::class);

test('active account approval is stamped with the current admin', function () {
    $this->travelTo(Carbon::parse('2026-06-27 14:00:00'));

    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);

    $data = UserResource::normalizeApprovalData([
        'account_status' => 'active',
        'rejection_reason' => 'should be cleared',
    ]);

    expect($data['approved_by'])->toBe($admin->getKey())
        ->and($data['approved_at']->format('Y-m-d H:i:s'))->toBe('2026-06-27 14:00:00')
        ->and($data['rejection_reason'])->toBeNull();
});

test('rejecting an account clears approval metadata and keeps rejection reason', function () {
    $approvedUser = User::factory()->member()->create([
        'account_status' => 'active',
        'approved_at' => Carbon::parse('2026-06-20 08:00:00'),
        'approved_by' => User::factory()->admin()->create()->getKey(),
    ]);

    $data = UserResource::normalizeApprovalData([
        'account_status' => 'rejected',
        'rejection_reason' => 'Dokumen belum lengkap.',
    ], $approvedUser);

    expect($data['approved_at'])->toBeNull()
        ->and($data['approved_by'])->toBeNull()
        ->and($data['rejection_reason'])->toBe('Dokumen belum lengkap.');
});

test('editing an already active account preserves the original approval stamp', function () {
    $originalAdmin = User::factory()->admin()->create();
    $currentAdmin = User::factory()->admin()->create();

    $approvedUser = User::factory()->member()->create([
        'account_status' => 'active',
        'approved_at' => Carbon::parse('2026-06-21 09:30:00'),
        'approved_by' => $originalAdmin->getKey(),
    ]);

    $this->actingAs($currentAdmin);

    $data = UserResource::normalizeApprovalData([
        'account_status' => 'active',
        'full_name' => 'Nama Baru',
    ], $approvedUser);

    expect($data['approved_by'])->toBe($originalAdmin->getKey())
        ->and($data['approved_at']->format('Y-m-d H:i:s'))->toBe('2026-06-21 09:30:00');
});
