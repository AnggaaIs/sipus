<?php

use App\Filament\Pages\Auth\EditProfile;
use App\Models\User;
use App\Notifications\PasswordChangedNotification;
use Filament\Auth\Notifications\NoticeOfEmailChangeRequest;
use Filament\Auth\Notifications\VerifyEmailChange;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('profile updates require the current password even for password changes', function () {
    $member = User::factory()->member()->create([
        'phone' => '081111111111',
    ]);

    Filament::setCurrentPanel(Filament::getPanel('user'));

    Livewire::actingAs($member)
        ->test(EditProfile::class)
        ->set('data.password', 'PasswordBaru123')
        ->set('data.passwordConfirmation', 'PasswordBaru123')
        ->call('save')
        ->assertHasErrors(['data.currentPassword']);
});

test('changing the password sends a password changed email notification', function () {
    Notification::fake();

    $member = User::factory()->member()->create([
        'phone' => '081234567890',
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

test('admin email changes keep the current address until verification and send verification notifications', function () {
    Notification::fake();

    $admin = User::factory()->admin()->create([
        'email' => 'lama@sipus.com',
        'phone' => '081234567890',
    ]);

    Filament::setCurrentPanel(Filament::getPanel('admin'));

    Livewire::actingAs($admin)
        ->test(EditProfile::class)
        ->set('data.email', 'baru@sipus.com')
        ->set('data.currentPassword', 'password')
        ->call('save')
        ->assertHasNoErrors();

    expect($admin->fresh()->email)->toBe('lama@sipus.com');

    Notification::assertSentTo(
        $admin,
        NoticeOfEmailChangeRequest::class,
        fn (NoticeOfEmailChangeRequest $notification): bool => $notification->newEmail === 'baru@sipus.com',
    );

    Notification::assertSentOnDemand(
        VerifyEmailChange::class,
        fn (VerifyEmailChange $notification, array $channels, object $notifiable): bool => $notifiable->routeNotificationFor('mail', $notification) === 'baru@sipus.com',
    );
});

test('member profile ignores non-password field changes even if submitted manually', function () {
    Notification::fake();

    $member = User::factory()->member()->create([
        'name' => 'Budi',
        'full_name' => 'Budi Santoso',
        'email' => 'budi@sipus.com',
        'phone' => '081111111111',
        'nisn' => '1234567890',
        'class' => 'XI IPA 2',
    ]);

    Filament::setCurrentPanel(Filament::getPanel('user'));

    Livewire::actingAs($member)
        ->test(EditProfile::class)
        ->set('data.name', 'Hacker')
        ->set('data.full_name', 'Nama Baru')
        ->set('data.email', 'baru@sipus.com')
        ->set('data.phone', '082222222222')
        ->set('data.nisn', '9999999999')
        ->set('data.class', 'XII IPS 3')
        ->set('data.currentPassword', 'password')
        ->call('save')
        ->assertHasNoErrors();

    $member->refresh();

    expect($member->name)->toBe('Budi')
        ->and($member->full_name)->toBe('Budi Santoso')
        ->and($member->email)->toBe('budi@sipus.com')
        ->and($member->phone)->toBe('081111111111')
        ->and($member->nisn)->toBe('1234567890')
        ->and($member->class)->toBe('XI IPA 2');

    Notification::assertNothingSent();
});
