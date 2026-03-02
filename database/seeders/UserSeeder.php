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

        
        User::updateOrCreate(
            ['email' => 'admin@ftmm.unair.ac.id'], // Kriteria unik untuk mencari
            [
                'name' => 'Super Admin',
                'password' => Hash::make('un41r@fTMm'),
                'role' => 'superadmin',
                'id_program_studi' => null,
                'email_verified_at' => now(),
            ]
        );

        // 2. Buat User Fakultas
        User::updateOrCreate(
            ['email' => 'fakultas@example.com'], // Kriteria unik
            [
                'name' => 'Fakultas',
                'password' => Hash::make('password'),
                'role' => 'fakultas',
                'id_program_studi' => null,
                'email_verified_at' => now(),
            ]
        );

        User::create([
            'name' => 'Kabag TU',
            'email' => 'kabag@ftmm.unair.ac.id',
            'password' => Hash::make('un41r@fTMm'), // Ganti dengan password yang kuat
            'role' => 'superadmin',
            'id_program_studi' => null, // Superadmin tidak terikat prodi tertentu
            'email_verified_at' => now(), // Anggap email sudah terverifikasi
        ]);

        // Anda bisa menambahkan user lain sesuai kebutuhan
    }
}