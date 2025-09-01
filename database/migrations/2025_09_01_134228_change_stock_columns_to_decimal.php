<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Mengubah tabel 'bahans'
        Schema::table('bahans', function (Blueprint $table) {
            // DECIMAL(total_digit, jumlah_digit_di_belakang_koma)
            $table->decimal('jumlah_stock', 15, 3)->default(0)->change();
            $table->decimal('minimum_stock', 15, 3)->default(0)->change();
        });

        // Mengubah tabel 'transaksis'
        Schema::table('transaksis', function (Blueprint $table) {
            $table->decimal('jumlah', 15, 3)->change();
            $table->decimal('stock_sebelum', 15, 3)->change();
            $table->decimal('stock_sesudah', 15, 3)->change();
        });

        // Mengubah tabel 'periode_stoks'
        Schema::table('periode_stoks', function (Blueprint $table) {
            $table->decimal('stok_awal', 15, 3)->default(0)->change();
            $table->decimal('stok_akhir', 15, 3)->nullable()->change();
        });

        // Mengubah tabel 'detail_pengadaans'
        Schema::table('detail_pengadaans', function (Blueprint $table) {
            $table->decimal('jumlah', 15, 3)->change();
        });
    }

    public function down(): void
    {
        // Kode untuk mengembalikan perubahan jika di-rollback
        Schema::table('bahans', function (Blueprint $table) {
            $table->integer('jumlah_stock')->default(0)->change();
            $table->integer('minimum_stock')->default(0)->change();
        });

        Schema::table('transaksis', function (Blueprint $table) {
            $table->integer('jumlah')->change();
            $table->integer('stock_sebelum')->change();
            $table->integer('stock_sesudah')->change();
        });

        Schema::table('periode_stoks', function (Blueprint $table) {
            $table->integer('stok_awal')->default(0)->change();
            $table->integer('stok_akhir')->nullable()->change();
        });

        Schema::table('detail_pengadaans', function (Blueprint $table) {
            $table->integer('jumlah')->change();
        });
    }
};
