<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Import BelongsTo
use Illuminate\Database\Eloquent\Relations\HasMany;   // Import HasMany

class Bahan extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_bahan',
        'nama_bahan',
        'merk',
        'id_program_studi',
        'id_gudang',
        'jumlah_stock',
        'satuan',
        'minimum_stock',
        'tanggal_kedaluwarsa',
    ];

    /**
     * Casting tipe data untuk atribut tertentu.
     */
    protected $casts = [
        'tanggal_kedaluwarsa' => 'date',
        'jumlah_stock' => 'integer',
        'minimum_stock' => 'integer',
    ];

    /**
     * Relasi ke ProgramStudi (satu bahan dimiliki oleh satu program studi)
     */
    public function programStudi(): BelongsTo
    {
        return $this->belongsTo(ProgramStudi::class, 'id_program_studi');
    }

    /**
     * Relasi ke Gudang (satu bahan disimpan di satu gudang)
     */
    public function gudang(): BelongsTo
    {
        return $this->belongsTo(Gudang::class, 'id_gudang');
    }

    /**
     * Relasi ke Transaksi (satu bahan bisa memiliki banyak transaksi)
     */
    public function transaksis(): HasMany
    {
        return $this->hasMany(Transaksi::class, 'id_bahan');
    }
}