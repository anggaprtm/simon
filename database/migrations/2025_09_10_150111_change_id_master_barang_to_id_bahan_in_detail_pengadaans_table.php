<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('detail_pengadaans', function (Blueprint $table) {
            // Hapus foreign key dan kolom lama
            $table->dropForeign(['id_master_barang']);
            $table->dropColumn('id_master_barang');

            // Tambah kolom dan foreign key baru
            $table->foreignId('id_bahan')->constrained('bahans')->after('id_pengajuan_pengadaan');
        });
    }

    public function down(): void
    {
        Schema::table('detail_pengadaans', function (Blueprint $table) {
            // Rollback jika diperlukan
            $table->dropForeign(['id_bahan']);
            $table->dropColumn('id_bahan');

            $table->foreignId('id_master_barang')->constrained('master_barangs');
        });
    }
};