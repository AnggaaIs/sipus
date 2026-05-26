<?php

use App\Filament\Pages\Auth\EditProfile;
use App\Models\User;
use Tests\TestCase;

uses(TestCase::class);

test('member cannot override protected profile fields through mutated payload', function () {
    $member = new User([
        'role' => 'user',
        'full_name' => 'User Asli',
        'name' => 'user-asli',
        'nisn' => '1234567890',
        'class' => 'XI IPA 1',
        'email' => 'user@example.com',
        'phone' => '08123456789',
        'account_status' => 'active',
        'is_active' => true,
    ]);

    $page = new class($member) extends EditProfile
    {
        public function __construct(private User $fakeUser) {}

        protected function getUser(): User
        {
            return $this->fakeUser;
        }

        /**
         * @param  array<string, mixed>  $data
         * @return array<string, mixed>
         */
        public function mutateForTest(array $data): array
        {
            return $this->mutateFormDataBeforeSave($data);
        }
    };

    $result = $page->mutateForTest([
        'full_name' => 'Nama Hasil Burp',
        'nisn' => '9999999999',
        'class' => 'XII IPS 9',
        'name' => 'nama-tampilan-baru',
        'email' => 'USER@EXAMPLE.COM',
        'phone' => '0812 0000 0000',
    ]);

    expect($result['full_name'])->toBe('User Asli')
        ->and($result['nisn'])->toBe('1234567890')
        ->and($result['class'])->toBe('XI IPA 1')
        ->and($result['name'])->toBe('nama-tampilan-baru')
        ->and($result['email'])->toBe('user@example.com');
});
