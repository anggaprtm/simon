<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('arsip_laporans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_program_studi')->constrained('program_studis')->cascadeOnDelete();
            $table->foreignId('id_user')->constrained('users'); // Siapa yang mengunggah
            $table->enum('jenis_laporan', ['stok', 'transaksi']);
            $table->integer('bulan'); // 1-12
            $table->integer('tahun');
            $table->string('file_path');
            $table->timestamps();
            
            // Memastikan 1 Prodi hanya punya 1 arsip per jenis laporan per bulan dan tahun (mencegah duplikat)
            $table->unique(['id_program_studi', 'jenis_laporan', 'bulan', 'tahun'], 'arsip_unik_bulanan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('arsip_laporans');
    }
};
