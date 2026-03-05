<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Menambahkan 'kps' ke dalam ENUM
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('laboran', 'kps', 'fakultas', 'superadmin') NOT NULL DEFAULT 'laboran'");
    }

    public function down(): void
    {
        // Kembalikan ke semula (jika di-rollback)
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('laboran', 'fakultas', 'superadmin') NOT NULL DEFAULT 'laboran'");
    }
};