<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('detail_pengadaans', function (Blueprint $table) {
            $table->boolean('is_direalisasi')->default(false)->after('catatan_revisi');
            $table->decimal('realisasi_qty', 15, 3)->nullable()->after('is_direalisasi');
            $table->decimal('konversi_nilai', 15, 6)->nullable()->after('realisasi_qty');
        });
    }

    public function down(): void
    {
        Schema::table('detail_pengadaans', function (Blueprint $table) {
            $table->dropColumn(['is_direalisasi', 'realisasi_qty', 'konversi_nilai']);
        });
    }
};
