<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::factory()->admin()->create([
            'name' => 'Admin SIPUS',
            'full_name' => 'Administrator SIPUS',
            'email' => 'admin@sipus.com',
            'password' => Hash::make('password'),
        ]);

        $demoMembers = [
            ['name' => 'Budi Santoso', 'full_name' => 'Budi Santoso', 'nisn' => '2026000001', 'email' => 'budi.santoso@sipus.com', 'class' => 'XI RPL 1'],
            ['name' => 'Siti Rahmawati', 'full_name' => 'Siti Rahmawati', 'nisn' => '2026000002', 'email' => 'siti.rahmawati@sipus.com', 'class' => 'XI RPL 1'],
            ['name' => 'Andi Pratama', 'full_name' => 'Andi Pratama', 'nisn' => '2026000003', 'email' => 'andi.pratama@sipus.com', 'class' => 'XII TKJ 2'],
            ['name' => 'Nabila Putri', 'full_name' => 'Nabila Putri', 'nisn' => '2026000004', 'email' => 'nabila.putri@sipus.com', 'class' => 'X IPA 3'],
        ];

        foreach ($demoMembers as $member) {
            User::factory()->member()->create([
                ...$member,
                'password' => Hash::make('password'),
                'approved_by' => $admin->getKey(),
            ]);
        }

        User::factory(20)->member()->create([
            'approved_by' => $admin->getKey(),
        ]);

        User::factory(5)->pendingApproval()->create();

        User::factory()->create([
            'name' => 'Raka Maulana',
            'full_name' => 'Raka Maulana',
            'nisn' => '2026000005',
            'email' => 'raka.rejected@sipus.com',
            'account_status' => 'rejected',
            'rejection_reason' => 'Data NISN belum sesuai dengan data sekolah.',
            'approved_at' => null,
            'approved_by' => $admin->getKey(),
            'is_active' => false,
        ]);

        User::factory()->create([
            'name' => 'Dimas Saputra',
            'full_name' => 'Dimas Saputra',
            'nisn' => '2026000006',
            'email' => 'dimas.suspended@sipus.com',
            'account_status' => 'suspended',
            'approved_at' => $admin->approved_at,
            'approved_by' => $admin->getKey(),
            'is_active' => false,
        ]);
    }
}
