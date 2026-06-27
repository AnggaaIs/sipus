<?php

use App\Filament\Pages\Auth\EditProfile;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin panel exposes the custom edit profile page', function () {
    $admin = User::factory()->admin()->create();
    $panel = Filament::getPanel('admin');

    expect($panel->hasProfile())->toBeTrue()
        ->and($panel->getProfilePage())->toBe(EditProfile::class);

    $this->actingAs($admin)
        ->get($panel->getProfileUrl())
        ->assertOk()
        ->assertSeeText('Nama lengkap')
        ->assertSeeText('Nama tampilan')
        ->assertSeeText('Wajib diisi untuk menyimpan perubahan profil apa pun.')
        ->assertSeeText('Email')
        ->assertSeeText('Kata sandi')
        ->assertDontSeeText('Foto profil');
});

test('user panel exposes the custom edit profile page with member restrictions', function () {
    $member = User::factory()->member()->create([
        'full_name' => 'Budi Santoso',
        'nisn' => '1234567890',
        'class' => 'XI IPA 2',
    ]);

    $panel = Filament::getPanel('user');

    expect($panel->hasProfile())->toBeTrue()
        ->and($panel->getProfilePage())->toBe(EditProfile::class);

    $this->actingAs($member)
        ->get($panel->getProfileUrl())
        ->assertOk()
        ->assertSeeText('Wajib diisi untuk menyimpan perubahan profil apa pun.')
        ->assertSeeText('Jika ingin mengubah nama, email, NISN, kelas, atau nomor telepon, silakan hubungi admin.')
        ->assertSeeText('Kata sandi')
        ->assertDontSeeText('Foto profil');
});
