<?php

// database/migrations/YYYY_MM_DD_HHMMSS_add_foreign_keys_to_tables.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Pastikan kolom id_program_studi sudah ada
            if (Schema::hasColumn('users', 'id_program_studi')) {
                 $table->foreign('id_program_studi')
                       ->references('id')->on('program_studis')
                       ->onDelete('set null'); // Jika prodi dihapus, user jadi tidak terafiliasi (atau 'restrict')
            }
        });

        Schema::table('gudangs', function (Blueprint $table) {
            if (Schema::hasColumn('gudangs', 'id_program_studi')) {
                $table->foreign('id_program_studi')
                      ->references('id')->on('program_studis')
                      ->onDelete('set null'); // Jika prodi dihapus, gudang jadi umum (atau 'cascade' jika gudang prodi harus ikut terhapus)
            }
        });

        Schema::table('bahans', function (Blueprint $table) {
            if (Schema::hasColumns('bahans', ['id_program_studi', 'id_gudang'])) {
                $table->foreign('id_program_studi')
                      ->references('id')->on('program_studis')
                      ->onDelete('cascade'); // Jika prodi dihapus, bahan miliknya ikut terhapus

                $table->foreign('id_gudang')
                      ->references('id')->on('gudangs')
                      ->onDelete('cascade'); // Jika gudang dihapus, bahan di dalamnya ikut terhapus (atau 'restrict')
            }
        });

        Schema::table('transaksis', function (Blueprint $table) {
            if (Schema::hasColumns('transaksis', ['id_bahan', 'id_user'])) {
                $table->foreign('id_bahan')
                      ->references('id')->on('bahans')
                      ->onDelete('cascade'); // Jika bahan dihapus, transaksinya ikut terhapus

                $table->foreign('id_user')
                      ->references('id')->on('users')
                      ->onDelete('restrict'); // Jangan hapus user jika masih punya transaksi (atau 'set null')
            }
        });
    }

    public function down(): void
    {
        // Hapus foreign keys dalam urutan terbalik atau dengan try-catch jika ada ketergantungan
        // Pastikan nama constraint-nya benar (Laravel biasanya menamakannya: nama_tabel_nama_kolom_foreign)
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'id_program_studi')) {
                 $table->dropForeign(['id_program_studi']); // Atau $table->dropForeign('users_id_program_studi_foreign');
            }
        });

        Schema::table('gudangs', function (Blueprint $table) {
            if (Schema::hasColumn('gudangs', 'id_program_studi')) {
                $table->dropForeign(['id_program_studi']);
            }
        });

        Schema::table('bahans', function (Blueprint $table) {
            if (Schema::hasColumns('bahans', ['id_program_studi', 'id_gudang'])) {
                $table->dropForeign(['id_program_studi']);
                $table->dropForeign(['id_gudang']);
            }
        });

        Schema::table('transaksis', function (Blueprint $table) {
             if (Schema::hasColumns('transaksis', ['id_bahan', 'id_user'])) {
                $table->dropForeign(['id_bahan']);
                $table->dropForeign(['id_user']);
            }
        });
    }
};