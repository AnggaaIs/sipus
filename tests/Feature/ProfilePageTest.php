<?php

use App\Models\User;

test('admin can open the profile page from the admin panel', function () {
    $admin = User::factory()->admin()->make();

    $response = $this
        ->actingAs($admin)
        ->get(route('filament.admin.auth.profile'));

    $response->assertOk()
        ->assertSee('Nama lengkap')
        ->assertSee('Nomor telepon')
        ->assertSee('Foto profil')
        ->assertDontSee('NISN')
        ->assertDontSee('Kelas');
});

test('member can open the profile page from the user panel', function () {
    $member = User::factory()->member()->make();

    $response = $this
        ->actingAs($member)
        ->get(route('filament.user.auth.profile'));

    $response->assertOk()
        ->assertSee('Nama lengkap')
        ->assertSee('NISN')
        ->assertSee('Kelas')
        ->assertSee('Nomor telepon')
        ->assertSee('Foto profil')
        ->assertSee('Nama lengkap hanya dapat diubah oleh admin.')
        ->assertSee('NISN hanya dapat diubah oleh admin.')
        ->assertSee('Kelas hanya dapat diubah oleh admin.');
});
