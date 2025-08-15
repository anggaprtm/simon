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
        Schema::create('master_barangs', function (Blueprint $table) {
            $table->id();
            $table->string('nama_barang')->unique(); // Nama barang harus unik, cth: "Asam Klorida (HCl)"
            $table->text('spesifikasi')->nullable(); // Spesifikasi default jika ada
            $table->foreignId('id_satuan')->nullable()->constrained('satuans')->onDelete('set null'); // Satuan default
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_barangs');
    }
};
