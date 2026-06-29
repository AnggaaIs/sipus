<?php

use App\Filament\Pages\Auth\EditProfile;
use App\Models\User;
use App\Notifications\PasswordChangedNotification;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Black-Box Testing
|--------------------------------------------------------------------------
| Fokus: perilaku halaman profil admin dan user dari sisi pemakai, termasuk
| validasi current password dan pembatasan perubahan identitas.
*/

test('halaman profil admin tersedia melalui route profil filament kustom', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('filament.admin.auth.profile'))
        ->assertOk()
        ->assertSeeText('Nama lengkap')
        ->assertSeeText('Nama tampilan')
        ->assertSeeText('Wajib diisi untuk menyimpan perubahan profil apa pun.');
});

test('halaman profil user menampilkan pembatasan anggota dengan jelas', function () {
    $member = User::factory()->member()->create([
        'full_name' => 'Budi Santoso',
        'nisn' => '1234567890',
        'class' => 'XI IPA 2',
    ]);

    $this->actingAs($member)
        ->get(route('filament.user.auth.profile'))
        ->assertOk()
        ->assertSeeText('Wajib diisi untuk menyimpan perubahan profil apa pun.')
        ->assertSeeText('Jika ingin mengubah nama, email, NISN, kelas, atau nomor telepon, silakan hubungi admin.')
        ->assertDontSeeText('Foto profil');
});

test('perubahan profil mewajibkan kata sandi saat ini', function () {
    $member = User::factory()->member()->create();

    Filament::setCurrentPanel(Filament::getPanel('user'));

    Livewire::actingAs($member)
        ->test(EditProfile::class)
        ->set('data.password', 'PasswordBaru123')
        ->set('data.passwordConfirmation', 'PasswordBaru123')
        ->call('save')
        ->assertHasErrors(['data.currentPassword']);
});

test('mengganti kata sandi mengirim notifikasi perubahan password', function () {
    Notification::fake();

    $member = User::factory()->member()->create([
        'phone' => '081111111111',
    ]);

    Filament::setCurrentPanel(Filament::getPanel('user'));

    Livewire::actingAs($member)
        ->test(EditProfile::class)
        ->set('data.password', 'PasswordBaru123')
        ->set('data.passwordConfirmation', 'PasswordBaru123')
        ->set('data.currentPassword', 'password')
        ->call('save')
        ->assertHasNoErrors();

    expect(Hash::check('PasswordBaru123', $member->fresh()->password))->toBeTrue();

    Notification::assertSentTo($member, PasswordChangedNotification::class);
});

test('profil anggota mengabaikan perubahan manual pada field identitas', function () {
    $member = User::factory()->member()->create([
        'name' => 'Budi',
        'full_name' => 'Budi Santoso',
        'email' => 'budi@sipus.test',
        'phone' => '081111111111',
        'nisn' => '1234567890',
        'class' => 'XI IPA 2',
    ]);

    Filament::setCurrentPanel(Filament::getPanel('user'));

    Livewire::actingAs($member)
        ->test(EditProfile::class)
        ->set('data.name', 'Nama Diubah')
        ->set('data.full_name', 'Nama Lengkap Diubah')
        ->set('data.email', 'baru@sipus.test')
        ->set('data.phone', '082222222222')
        ->set('data.nisn', '9999999999')
        ->set('data.class', 'XII IPS 3')
        ->set('data.currentPassword', 'password')
        ->call('save')
        ->assertHasNoErrors();

    $member->refresh();

    expect($member->name)->toBe('Budi')
        ->and($member->full_name)->toBe('Budi Santoso')
        ->and($member->email)->toBe('budi@sipus.test')
        ->and($member->phone)->toBe('081111111111')
        ->and($member->nisn)->toBe('1234567890')
        ->and($member->class)->toBe('XI IPA 2');
});
