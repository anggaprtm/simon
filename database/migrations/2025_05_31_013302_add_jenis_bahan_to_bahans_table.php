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
        // Perintah untuk mengubah tabel 'bahans'
        Schema::table('bahans', function (Blueprint $table) {
            // Tambahkan kolom baru setelah kolom 'merk'
            // ->nullable() berarti kolom ini boleh kosong (opsional)
            $table->string('jenis_bahan', 100)->nullable()->after('merk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bahans', function (Blueprint $table) {
            // Perintah untuk menghapus kolom jika migrasi di-rollback
            $table->dropColumn('jenis_bahan');
        });
    }
};