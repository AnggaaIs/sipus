<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Buat akun admin default
        User::create([
            'name'        => 'Admin SIPUS',
            'email'       => 'admin@sipus.com',
            'password'    => Hash::make('password'),
            'role'        => 'admin',
            'is_approved' => true,
        ]);

        // Buat beberapa dummy siswa untuk testing
        User::create([
            'name'        => 'Budi Santoso',
            'email'       => 'budi@siswa.com',
            'password'    => Hash::make('password'),
            'nis'         => '2021001',
            'role'        => 'siswa',
            'is_approved' => true,
        ]);

        User::create([
            'name'        => 'Siti Rahayu',
            'email'       => 'siti@siswa.com',
            'password'    => Hash::make('password'),
            'nis'         => '2021002',
            'role'        => 'siswa',
            'is_approved' => true,
        ]);

        // Siswa yang belum diapprove (untuk testing UC-17)
        User::create([
            'name'        => 'Andi Pratama',
            'email'       => 'andi@siswa.com',
            'password'    => Hash::make('password'),
            'nis'         => '2021003',
            'role'        => 'siswa',
            'is_approved' => false,
        ]);
    }
}
