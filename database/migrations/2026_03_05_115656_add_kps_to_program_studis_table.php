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
        Schema::table('program_studis', function (Blueprint $table) {
            $table->string('nama_kps')->nullable()->after('nama_program_studi');
            $table->string('nip_kps')->nullable()->after('nama_kps');
        });
    }

    public function down(): void
    {
        Schema::table('program_studis', function (Blueprint $table) {
            $table->dropColumn(['nama_kps', 'nip_kps']);
        });
    }
};
