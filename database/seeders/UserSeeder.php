<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;         // <-- Import model User
use App\Models\ProgramStudi; // <-- Import model ProgramStudi
use Illuminate\Support\Facades\Hash; // <-- Import Hash Facade
use Illuminate\Support\Facades\DB;   // <-- Import DB Facade

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hapus data user lama (opsional dan hati-hati)
        // User::truncate();

        // 1. Buat User Superadmin
        User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@example.com',
            'password' => Hash::make('password'), // Ganti dengan password yang kuat
            'role' => 'superadmin',
            'id_program_studi' => null, // Superadmin tidak terikat prodi tertentu
            'email_verified_at' => now(), // Anggap email sudah terverifikasi
        ]);

        // 2. Buat User Fakultas (opsional, untuk contoh)
        User::create([
            'name' => 'Fakultas',
            'email' => 'fakultas@example.com',
            'password' => Hash::make('password'), // Ganti dengan password yang kuat
            'role' => 'fakultas',
            'id_program_studi' => null, // Role fakultas juga tidak terikat prodi tertentu
            'email_verified_at' => now(),
        ]);

        // Anda bisa menambahkan user lain sesuai kebutuhan
    }
}