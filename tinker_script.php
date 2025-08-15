<?php

// tinker_script.php

use App\Models\Bahan;
use App\Models\MasterBarang;

// 1. Tentukan nama prodi yang datanya ingin diambil
$namaProdi = ['Rekayasa Nanoteknologi', 'Teknik Elektro'];

// 2. Ambil semua NAMA BAHAN yang unik dari kedua prodi tersebut
$namaBahanUnik = Bahan::whereHas('programStudi', function ($query) use ($namaProdi) {
    $query->whereIn('nama_program_studi', $namaProdi);
})->pluck('nama_bahan')->unique();

// 3. Ambil semua nama yang sudah ada di master_barangs untuk perbandingan
$namaMasterSudahAda = MasterBarang::pluck('nama_barang')->toArray();

$jumlahDitambahkan = 0;

// 4. Loop melalui setiap nama bahan unik yang ditemukan
foreach ($namaBahanUnik as $nama) {
    // 5. Cek apakah nama ini BELUM ADA di master_barangs
    if (!in_array($nama, $namaMasterSudahAda)) {
        // 6. Jika belum ada, buat record baru
        MasterBarang::create(['nama_barang' => $nama]);
        $jumlahDitambahkan++;
    }
}

// 7. Tampilkan hasilnya
echo "Proses selesai. Berhasil menambahkan " . $jumlahDitambahkan . " item baru ke Master Barang.\n";