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
        Schema::create('pengajuan_pengadaans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_user')->constrained('users')->onDelete('cascade'); // Siapa yang mengajukan
            $table->foreignId('id_program_studi')->constrained('program_studis')->onDelete('cascade');
            $table->string('tahun_ajaran'); // Cth: "2024/2025"
            $table->enum('semester', ['Ganjil', 'Genap']);
            $table->string('nomor_surat')->nullable()->unique(); // Nomor surat nota dinas
            $table->enum('status', ['Draft', 'Diajukan', 'Disetujui', 'Ditolak', 'Selesai'])->default('Draft');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengajuan_pengadaans');
    }
};
