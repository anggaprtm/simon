<?php

// database/migrations/YYYY_MM_DD_HHMMSS_create_gudangs_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gudangs', function (Blueprint $table) {
            $table->id();
            $table->string('nama_gudang');
            $table->text('lokasi')->nullable();
            $table->unsignedBigInteger('id_program_studi')->nullable(); // Bisa NULL untuk gudang umum
            $table->timestamps();

            // $table->foreign('id_program_studi')->references('id')->on('program_studis')->onDelete('set null');
            // Sama seperti users, foreign key bisa ditambahkan di sini atau di migrasi terpisah.
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gudangs');
    }
};