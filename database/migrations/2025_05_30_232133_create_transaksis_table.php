<?php

// database/migrations/YYYY_MM_DD_HHMMSS_create_transaksis_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksis', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_bahan');
            $table->unsignedBigInteger('id_user');
            $table->enum('jenis_transaksi', ['masuk', 'keluar', 'penyesuaian_masuk', 'penyesuaian_keluar']);
            $table->integer('jumlah');
            $table->timestamp('tanggal_transaksi')->useCurrent();
            $table->text('keterangan')->nullable();
            $table->integer('stock_sebelum');
            $table->integer('stock_sesudah');
            $table->timestamps();

            // Foreign keys
            // $table->foreign('id_bahan')->references('id')->on('bahans')->onDelete('cascade');
            // $table->foreign('id_user')->references('id')->on('users')->onDelete('restrict'); // atau cascade sesuai kebutuhan
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksis');
    }
};