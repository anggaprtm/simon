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
        Schema::table('pengajuan_pengadaans', function (Blueprint $table) {
            $table->string('file_nota_dinas')->nullable()->after('nomor_surat');
        });
    }

    public function down(): void
    {
        Schema::table('pengajuan_pengadaans', function (Blueprint $table) {
            $table->dropColumn('file_nota_dinas');
        });
    }
};
