<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

test('registration page shows registration copy instead of login copy', function () {
    $response = $this->get(route('register'));

    $response->assertOk()
        ->assertSeeText('Daftarkan akun Anda')
        ->assertSeeText('Lengkapi data siswa untuk mengajukan akun SIPUS.')
        ->assertDontSeeText('Masuk ke akun Anda');
});

test('guest registration creates a pending member account and redirects to login', function () {
    $csrfToken = 'test-csrf-token';

    $response = $this->withSession(['_token' => $csrfToken])->post(route('register.store'), [
        '_token' => $csrfToken,
        'full_name' => 'Budi Santoso',
        'nisn' => '1234567890',
        'class' => 'XI IPA 2',
        'email' => 'budi.santoso@example.com',
        'phone' => '081234567890',
        'password' => 'Password123',
        'password_confirmation' => 'Password123',
    ]);

    $response->assertRedirect(route('login'))
        ->assertSessionHas('status', 'Pendaftaran berhasil. Akun Anda sedang menunggu persetujuan petugas perpustakaan.');

    $user = User::query()->where('email', 'budi.santoso@example.com')->first();

    expect($user)->not->toBeNull()
        ->and($user->full_name)->toBe('Budi Santoso')
        ->and($user->name)->toBe('Budi Santoso')
        ->and($user->nisn)->toBe('1234567890')
        ->and($user->role)->toBe('user')
        ->and($user->account_status)->toBe('pending')
        ->and($user->approved_at)->toBeNull()
        ->and($user->approved_by)->toBeNull()
        ->and(Hash::check('Password123', $user->password))->toBeTrue();
});
