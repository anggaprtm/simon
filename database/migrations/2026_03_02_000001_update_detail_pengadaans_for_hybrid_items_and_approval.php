<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('detail_pengadaans', function (Blueprint $table) {
            $table->unsignedBigInteger('id_bahan')->nullable()->change();
            $table->string('nama_barang_input')->nullable()->after('id_bahan');
            $table->decimal('approved_jumlah', 15, 3)->nullable()->after('jumlah');
            $table->enum('status_item', ['diajukan', 'disetujui', 'disetujui_sebagian', 'ditolak'])->default('diajukan')->after('approved_jumlah');
            $table->text('catatan_revisi')->nullable()->after('status_item');
        });

        DB::table('detail_pengadaans')->update([
            'approved_jumlah' => DB::raw('jumlah'),
            'status_item' => 'diajukan',
        ]);
    }

    public function down(): void
    {
        Schema::table('detail_pengadaans', function (Blueprint $table) {
            $table->dropColumn(['nama_barang_input', 'approved_jumlah', 'status_item', 'catatan_revisi']);
            $table->unsignedBigInteger('id_bahan')->nullable(false)->change();
        });
    }
};
