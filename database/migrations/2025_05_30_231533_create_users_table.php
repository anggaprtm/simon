<?php

// database/migrations/0001_01_01_000000_create_users_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            // Tambahkan kolom berikut:
            $table->unsignedBigInteger('id_program_studi')->nullable();
            $table->enum('role', ['laboran', 'fakultas', 'superadmin'])->default('laboran');
            $table->rememberToken();
            $table->timestamps();

            // Tambahkan foreign key constraint (setelah tabel program_studis dibuat)
            // Pastikan file migrasi program_studis dijalankan sebelum users, atau definisikan foreign key di migrasi terpisah.
            // Untuk amannya, kita akan buat foreign key di migrasi terpisah setelah semua tabel utama ada.
            // Tapi jika Anda yakin urutannya, bisa ditambahkan di sini.
            // $table->foreign('id_program_studi')->references('id')->on('program_studis')->onDelete('set null');
        });

        // Migrasi bawaan untuk password resets dan personal access tokens
        // mungkin ada di file ini atau file terpisah tergantung versi Laravel Anda.
        // Jika ada, biarkan saja.
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
        // Schema::dropIfExists('password_reset_tokens'); // Jika ada di sini
        // Schema::dropIfExists('sessions'); // Jika ada di sini
    }
};