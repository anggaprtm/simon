<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ProgramStudi; // <-- Import model ProgramStudi
use Illuminate\Support\Facades\DB; // <-- Import DB Facade jika ingin menggunakan query builder

class ProgramStudiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hapus data lama jika ada (opsional, hati-hati jika data sudah produktif)
        // DB::table('program_studis')->truncate(); // Atau ProgramStudi::truncate(); jika tidak ada foreign key constraint

        ProgramStudi::create([
            'nama_program_studi' => 'Rekayasa Nanoteknologi',
            'kode_program_studi' => 'RN',
        ]);

        ProgramStudi::create([
            'nama_program_studi' => 'Teknik Robotika dan Kecerdasan Buatan',
            'kode_program_studi' => 'TRKB',
        ]);

        ProgramStudi::create([
            'nama_program_studi' => 'Teknik Elektro',
            'kode_program_studi' => 'TE',
        ]);

        ProgramStudi::create([
            'nama_program_studi' => 'Teknik Industri',
            'kode_program_studi' => 'TI',
        ]);

        ProgramStudi::create([
            'nama_program_studi' => 'Teknologi Sains Data',
            'kode_program_studi' => 'TSD',
        ]);

        ProgramStudi::create([
            'nama_program_studi' => 'Fakultas Teknologi Maju dan Multidisiplin',
            'kode_program_studi' => 'FTMM',
        ]);

    }
}