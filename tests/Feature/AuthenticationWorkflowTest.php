<?php

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Black-Box Testing
|--------------------------------------------------------------------------
| Fokus: alur autentikasi dari sisi pengguna, meliputi login, registrasi,
| lupa password, dan reset password.
*/

test('anggota yang sudah disetujui bisa login memakai email dan diarahkan ke panel user', function () {
    $member = User::factory()->member()->create([
        'email' => 'anggota@sipus.test',
        'nisn' => '1234567890',
    ]);

    $response = $this->post(route('login.store'), [
        'login' => $member->email,
        'password' => 'password',
    ]);

    $response->assertRedirect(route('filament.user.pages.dashboard'));
    $this->assertAuthenticatedAs($member);
});

test('admin yang aktif bisa login memakai nisn dan diarahkan ke panel admin', function () {
    $admin = User::factory()->admin()->create([
        'email' => 'admin@sipus.test',
        'nisn' => '9999999999',
    ]);

    $response = $this->post(route('login.store'), [
        'login' => $admin->nisn,
        'password' => 'password',
    ]);

    $response->assertRedirect(route('filament.admin.pages.dashboard'));
    $this->assertAuthenticatedAs($admin);
});

test('akun pending ditolak dan nonaktif menampilkan pesan login yang sesuai', function () {
    $pendingMember = User::factory()->pendingApproval()->create([
        'email' => 'pending@sipus.test',
    ]);

    $inactiveMember = User::factory()->member()->create([
        'email' => 'inactive@sipus.test',
        'is_active' => false,
    ]);

    $rejectedMember = User::factory()->create([
        'email' => 'rejected@sipus.test',
        'account_status' => 'rejected',
        'is_active' => false,
        'rejection_reason' => 'Data NISN belum sesuai dengan data sekolah.',
    ]);

    $this->post(route('login.store'), [
        'login' => $pendingMember->email,
        'password' => 'password',
    ])->assertSessionHasErrors([
        'login' => 'Akun Anda belum aktif. Silakan tunggu persetujuan dari petugas perpustakaan.',
    ]);

    $this->post(route('login.store'), [
        'login' => $inactiveMember->email,
        'password' => 'password',
    ])->assertSessionHasErrors([
        'login' => 'Akun ini sedang dinonaktifkan. Silakan hubungi petugas perpustakaan.',
    ]);

    $this->post(route('login.store'), [
        'login' => $rejectedMember->email,
        'password' => 'password',
    ])->assertSessionHasErrors([
        'login' => 'Pendaftaran akun Anda ditolak: Data NISN belum sesuai dengan data sekolah.',
    ]);

    $this->assertGuest();
});

test('registrasi pengunjung membuat akun anggota dengan status pending', function () {
    $response = $this->post(route('register.store'), [
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
        ->and(Hash::check('Password123', $user->password))->toBeTrue();
});

test('permintaan lupa password mengirim notifikasi reset untuk email yang terdaftar', function () {
    Notification::fake();

    $member = User::factory()->member()->create([
        'email' => 'reset@sipus.test',
    ]);

    $this->get(route('password.request'))
        ->assertOk()
        ->assertSeeText('Kirim tautan atur ulang kata sandi');

    $this->post(route('password.email'), [
        'email' => $member->email,
    ])->assertSessionHas('status', 'Tautan atur ulang kata sandi sudah dikirim ke email Anda.');

    Notification::assertSentTo($member, ResetPasswordNotification::class);
});

test('halaman reset password membaca identity terenkripsi dan kata sandi bisa diganti', function () {
    $member = User::factory()->member()->create([
        'email' => 'ganti@sipus.test',
    ]);

    $token = Password::createToken($member);

    $this->get(route('password.reset', [
        'token' => $token,
        'identity' => Crypt::encryptString($member->email),
    ]))
        ->assertOk()
        ->assertSeeText('Buat kata sandi baru')
        ->assertSee('type="hidden" name="email" value="ganti@sipus.test"', false)
        ->assertSee('id="reset-email" type="email" value="ganti@sipus.test" readonly', false);
});

test('pengguna bisa mengirim kata sandi baru dari halaman reset password', function () {
    $member = User::factory()->member()->create([
        'email' => 'ganti@sipus.test',
    ]);

    $token = Password::createToken($member);

    $this->post(route('password.update'), [
        'token' => $token,
        'email' => $member->email,
        'password' => 'PasswordBaru123',
        'password_confirmation' => 'PasswordBaru123',
    ])
        ->assertRedirect(route('login'))
        ->assertSessionHas('status', 'Kata sandi berhasil diganti. Silakan masuk kembali.');

    expect(Hash::check('PasswordBaru123', $member->fresh()->password))->toBeTrue();
});
