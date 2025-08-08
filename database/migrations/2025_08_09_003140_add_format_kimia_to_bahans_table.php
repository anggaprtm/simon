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
            // Tambahkan kolom boolean baru, defaultnya false (tidak diformat)
            $table->boolean('format_kimia')->default(false)->after('jenis_bahan');
        });
    }

    public function down(): void
    {
        Schema::table('bahans', function (Blueprint $table) {
            $table->dropColumn('format_kimia');
        });
    }
};
