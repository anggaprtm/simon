<?php

// database/migrations/YYYY_MM_DD_HHMMSS_create_bahans_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bahans', function (Blueprint $table) {
            $table->id();
            $table->string('kode_bahan');
            $table->string('nama_bahan');
            $table->string('merk')->nullable();
            $table->unsignedBigInteger('id_program_studi');
            $table->unsignedBigInteger('id_gudang');
            $table->integer('jumlah_stock')->default(0);
            $table->string('satuan');
            $table->integer('minimum_stock')->default(0);
            $table->date('tanggal_kedaluwarsa')->nullable();
            $table->timestamps();

            // Composite unique key
            $table->unique(['kode_bahan', 'id_program_studi']);

            // Foreign keys
            // $table->foreign('id_program_studi')->references('id')->on('program_studis')->onDelete('cascade');
            // $table->foreign('id_gudang')->references('id')->on('gudangs')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bahans');
    }
};