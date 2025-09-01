<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Import BelongsTo

class Transaksi extends Model
{
    use HasFactory;

    protected $table = 'transaksis'; // Eksplisit jika ada keraguan dengan pluralisasi otomatis

    protected $fillable = [
        'id_bahan',
        'id_user',
        'jenis_transaksi',
        'jumlah',
        'tanggal_transaksi',
        'keterangan',
        'stock_sebelum',
        'stock_sesudah',
    ];

    /**
     * Casting tipe data untuk atribut tertentu.
     */
    protected $casts = [
        'tanggal_transaksi' => 'datetime',
        'jumlah' => 'decimal:3',
        'stock_sebelum' => 'decimal:3',
        'stock_sesudah' => 'decimal:3',
    ];

    /**
     * Relasi ke Bahan (satu transaksi terkait dengan satu bahan)
     */
    public function bahan(): BelongsTo
    {
        return $this->belongsTo(Bahan::class, 'id_bahan');
    }

    /**
     * Relasi ke User (satu transaksi dilakukan oleh satu user)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}