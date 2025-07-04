<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Tambahkan kolom 'nama_lengkap' setelah kolom 'email'
            $table->string('nama_lengkap')->nullable()->unique()->after('email');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Hapus kolom 'nama_lengkap' jika migrasi di-rollback
            $table->dropColumn('nama_lengkap');
        });
    }
};