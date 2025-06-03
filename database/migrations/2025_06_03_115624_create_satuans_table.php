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
        Schema::create('satuans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_satuan', 50)->unique(); // e.g., "ml", "L", "gr", "Kg", "pcs"
            $table->string('keterangan_satuan', 100)->nullable(); // e.g., "Mililiter", "Liter", "Pieces"
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('satuans');
    }
};
