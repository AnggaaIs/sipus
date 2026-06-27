<?php

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;

uses(RefreshDatabase::class);

test('reset password page shows the account email from encrypted identity without editable input', function () {
    $response = $this->get(route('password.reset', [
        'token' => 'reset-token-contoh',
        'identity' => Crypt::encryptString('budi.santoso@sipus.com'),
    ]));

    $response->assertOk()
        ->assertSeeText('Buat kata sandi baru')
        ->assertSeeText('Email')
        ->assertSee('type="hidden" name="email" value="budi.santoso@sipus.com"', false)
        ->assertSee('id="reset-email" type="email" value="budi.santoso@sipus.com" readonly', false)
        ->assertSee('id="reset-email"', false)
        ->assertSee('readonly', false)
        ->assertDontSeeText('Masukkan email akun Anda');
});

test('password reset notification link uses encrypted identity instead of plain email query', function () {
    $user = User::factory()->member()->make([
        'email' => 'budi.santoso@sipus.com',
    ]);

    $mailMessage = (new ResetPasswordNotification('reset-token-contoh'))->toMail($user);
    $actionUrl = $mailMessage->actionUrl;

    parse_str((string) parse_url($actionUrl, PHP_URL_QUERY), $query);

    expect($actionUrl)->toContain('/reset-password/reset-token-contoh')
        ->and($actionUrl)->toContain('identity=')
        ->and($actionUrl)->not->toContain('email=')
        ->and($actionUrl)->not->toContain('budi.santoso%40sipus.com')
        ->and($query['identity'] ?? null)->not->toBeNull()
        ->and(Crypt::decryptString($query['identity']))->toBe('budi.santoso@sipus.com');
});

test('reset password page still supports legacy links with plain email query', function () {
    $response = $this->get(route('password.reset', [
        'token' => 'reset-token-lama',
        'email' => 'legacy@sipus.com',
    ]));

    $response->assertOk()
        ->assertSee('id="reset-email" type="email" value="legacy@sipus.com" readonly', false)
        ->assertSee('type="hidden" name="email" value="legacy@sipus.com"', false);
});
