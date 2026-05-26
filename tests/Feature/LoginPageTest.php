<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('login page can be rendered', function () {
    $response = $this->get(route('login'));

    $response->assertOk()
        ->assertSee('Masuk ke akun Anda')
        ->assertSee('Ingat saya')
        ->assertDontSee('Navigasi');
});

test('member can log in from the public login page', function () {
    $user = User::factory()->member()->create([
        'email' => 'anggota@example.com',
    ]);

    $response = $this->post(route('login.store'), [
        'login' => $user->email,
        'password' => 'password',
    ]);

    $response->assertRedirect(route('filament.user.pages.dashboard'));
    $this->assertAuthenticatedAs($user);
});

test('admin can log in from the public login page', function () {
    $user = User::factory()->admin()->create([
        'email' => 'admin@example.com',
    ]);

    $response = $this->post(route('login.store'), [
        'login' => $user->email,
        'password' => 'password',
    ]);

    $response->assertRedirect(route('filament.admin.pages.dashboard'));
    $this->assertAuthenticatedAs($user);
});

test('admin redirect is not overridden by stale intended url', function () {
    $admin = User::factory()->admin()->create([
        'email' => 'stale-admin@example.com',
    ]);

    $adminResponse = $this->withSession([
        'url.intended' => route('filament.user.pages.dashboard'),
    ])->post(route('login.store'), [
        'login' => $admin->email,
        'password' => 'password',
    ]);

    $adminResponse->assertRedirect(route('filament.admin.pages.dashboard'));
});

test('member redirect is not overridden by stale intended url', function () {
    $member = User::factory()->member()->create([
        'email' => 'stale-member@example.com',
    ]);

    $memberResponse = $this->withSession([
        'url.intended' => route('filament.admin.pages.dashboard'),
    ])->post(route('login.store'), [
        'login' => $member->email,
        'password' => 'password',
    ]);

    $memberResponse->assertRedirect(route('filament.user.pages.dashboard'));
});

test('inactive or pending accounts cannot log in', function () {
    $inactiveUser = User::factory()->member()->create([
        'email' => 'inactive@example.com',
        'is_active' => false,
    ]);

    $pendingUser = User::factory()->pendingApproval()->create([
        'email' => 'pending@example.com',
    ]);

    $inactiveResponse = $this->from(route('login'))->post(route('login.store'), [
        'login' => $inactiveUser->email,
        'password' => 'password',
    ]);

    $inactiveResponse->assertRedirect(route('login'))
        ->assertSessionHasErrors('login');

    $this->assertGuest();

    $pendingResponse = $this->from(route('login'))->post(route('login.store'), [
        'login' => $pendingUser->email,
        'password' => 'password',
    ]);

    $pendingResponse->assertRedirect(route('login'))
        ->assertSessionHasErrors('login');

    $this->assertGuest();
});

test('member can log in using nisn', function () {
    $user = User::factory()->member()->create([
        'nisn' => '1234567890',
    ]);

    $response = $this->post(route('login.store'), [
        'login' => $user->nisn,
        'password' => 'password',
    ]);

    $response->assertRedirect(route('filament.user.pages.dashboard'));
    $this->assertAuthenticatedAs($user);
});
