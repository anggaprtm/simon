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
        Schema::table('bahans', function (Blueprint $table) {
            // Hapus kolom 'satuan' yang lama jika ada
            if (Schema::hasColumn('bahans', 'satuan')) {
                $table->dropColumn('satuan');
            }

            // Tambahkan kolom baru 'id_satuan' sebagai foreign key
            // Pastikan kolom ini ditambahkan setelah kolom lain yang relevan, misal 'jenis_bahan'
            // Kita buat nullable dulu, lalu bisa diubah jadi not nullable jika semua data lama sudah dimigrasi
            // atau jika memang boleh ada bahan tanpa satuan (tidak disarankan).
            // Untuk saat ini, kita anggap semua bahan HARUS punya satuan.
            $table->unsignedBigInteger('id_satuan')->after('jenis_bahan')->nullable(false); // Defaultnya NOT NULL

            // Tambahkan foreign key constraint
            $table->foreign('id_satuan')->references('id')->on('satuans')->onDelete('restrict');
            // onDelete('restrict') berarti satuan tidak bisa dihapus jika masih digunakan oleh bahan.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bahans', function (Blueprint $table) {
            // Hapus foreign key constraint dulu
            $table->dropForeign(['id_satuan']);

            // Hapus kolom 'id_satuan'
            $table->dropColumn('id_satuan');

            // Tambahkan kembali kolom 'satuan' yang lama (string) jika di-rollback
            // Sesuaikan tipe datanya jika sebelumnya berbeda
            $table->string('satuan', 50)->nullable()->after('jenis_bahan'); 
        });
    }
};