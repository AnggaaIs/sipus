<?php

use App\Filament\Admin\Resources\Users\UserResource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Black-Box Testing
|--------------------------------------------------------------------------
| Fokus: aturan bisnis approval akun dan akses panel sebagai perilaku sistem
| yang terlihat dari luar.
*/

test('mengaktifkan akun memberi cap admin yang menyetujui saat ini', function () {
    $this->travelTo(Carbon::parse('2026-06-28 09:00:00'));

    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);

    $data = UserResource::normalizeApprovalData([
        'account_status' => 'active',
        'rejection_reason' => 'harus dibersihkan',
    ]);

    expect($data['approved_by'])->toBe($admin->getKey())
        ->and($data['approved_at']->format('Y-m-d H:i:s'))->toBe('2026-06-28 09:00:00')
        ->and($data['rejection_reason'])->toBeNull();
});

test('menolak akun mengosongkan metadata approval dan menyimpan alasan penolakan', function () {
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
