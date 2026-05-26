<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('register page can be rendered', function () {
    $response = $this->get(route('register'));

    $response->assertOk()
        ->assertSee('Buat akun siswa')
        ->assertSee('Sudah punya akun?');
});

test('student can register and account starts as pending', function () {
    $response = $this->post(route('register.store'), [
        'full_name' => 'Andi Saputra',
        'nisn' => '1234567890',
        'email' => 'andi@example.com',
        'class' => 'XI IPA 2',
        'phone' => '081234567890',
        'password' => 'Password123',
        'password_confirmation' => 'Password123',
    ]);

    $response->assertRedirect(route('login'))
        ->assertSessionHas('status');

    $user = User::query()->where('email', 'andi@example.com')->first();

    expect($user)->not->toBeNull()
        ->and($user->full_name)->toBe('Andi Saputra')
        ->and($user->name)->toBe('Andi Saputra')
        ->and($user->nisn)->toBe('1234567890')
        ->and($user->role)->toBe('user')
        ->and($user->account_status)->toBe('pending');
});

test('register validates unique student identity fields', function () {
    User::factory()->create([
        'email' => 'siswa@example.com',
        'nisn' => '1234567890',
    ]);

    $response = $this->from(route('register'))->post(route('register.store'), [
        'full_name' => 'Siswa Baru',
        'nisn' => '1234567890',
        'email' => 'siswa@example.com',
        'class' => 'X IPA 1',
        'phone' => '081200000000',
        'password' => 'Password123',
        'password_confirmation' => 'Password123',
    ]);

    $response->assertRedirect(route('register'))
        ->assertSessionHasErrors(['nisn', 'email']);
});
