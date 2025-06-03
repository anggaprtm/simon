<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Satuan;

class SatuanSeeder extends Seeder
{
    public function run(): void
    {
        Satuan::create(['nama_satuan' => 'ml', 'keterangan_satuan' => 'Mililiter']);
        Satuan::create(['nama_satuan' => 'L', 'keterangan_satuan' => 'Liter']);
        Satuan::create(['nama_satuan' => 'gr', 'keterangan_satuan' => 'Gram']);
        Satuan::create(['nama_satuan' => 'Kg', 'keterangan_satuan' => 'Kilogram']);
        Satuan::create(['nama_satuan' => 'pcs', 'keterangan_satuan' => 'Pieces']);
        Satuan::create(['nama_satuan' => 'buah', 'keterangan_satuan' => 'Buah']);
        Satuan::create(['nama_satuan' => 'unit', 'keterangan_satuan' => 'Unit']);
        Satuan::create(['nama_satuan' => 'roll', 'keterangan_satuan' => 'Roll']);
        Satuan::create(['nama_satuan' => 'botol', 'keterangan_satuan' => 'Botol']);
    }
}