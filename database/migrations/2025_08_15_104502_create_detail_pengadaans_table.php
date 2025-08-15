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
        Schema::create('detail_pengadaans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_pengajuan_pengadaan')->constrained('pengajuan_pengadaans')->onDelete('cascade');
            $table->foreignId('id_master_barang')->constrained('master_barangs')->onDelete('restrict');
            $table->string('merk')->nullable();
            $table->text('spesifikasi')->nullable();
            $table->string('volume')->nullable(); // Cth: "500 gr", "1 L"
            $table->foreignId('id_satuan')->constrained('satuans')->onDelete('restrict');
            $table->unsignedBigInteger('harga_satuan')->default(0); // Harga Perkiraan Sendiri
            $table->integer('jumlah');
            $table->text('link_referensi')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_pengadaans');
    }
};
