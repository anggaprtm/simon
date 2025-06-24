<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('periode_stoks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_bahan')->constrained('bahans')->onDelete('cascade');
            $table->year('tahun_periode');
            $table->integer('stok_awal')->default(0);
            $table->integer('stok_akhir')->nullable(); // Diisi saat periode ditutup
            $table->enum('status', ['aktif', 'ditutup'])->default('aktif');
            $table->timestamps();

            // Kombinasi bahan dan tahun harus unik
            $table->unique(['id_bahan', 'tahun_periode']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('periode_stoks');
    }
};