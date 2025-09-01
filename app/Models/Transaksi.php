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

    public function getFormattedJumlahAttribute(): string
    {
        $jumlah = $this->attributes['jumlah'] ?? 0;
        $namaSatuan = $this->bahan->satuanRel->nama_satuan ?? ''; // ambil dari relasi bahan
        $namaSatuanLower = strtolower($namaSatuan);

        // Konversi ml -> L dan gr -> Kg
        if ($namaSatuanLower === 'ml' && $jumlah >= 1000) {
            $jumlahKonversi = $jumlah / 1000;
            $formatted = number_format($jumlahKonversi, 3, ',', '.');
            return rtrim(rtrim($formatted, '0'), ',') . ' L';
        } elseif ($namaSatuanLower === 'gr' && $jumlah >= 1000) {
            $jumlahKonversi = $jumlah / 1000;
            $formatted = number_format($jumlahKonversi, 3, ',', '.');
            return rtrim(rtrim($formatted, '0'), ',') . ' Kg';
        }

        // Default tampilkan jumlah + satuan asli
        if (fmod($jumlah, 1) == 0) {
            // Bilangan bulat
            $formattedJumlah = number_format($jumlah, 0, ',', '.');
        } else {
            // Bilangan desimal
            $formattedJumlah = rtrim(rtrim(number_format($jumlah, 3, ',', '.'), '0'), ',');
        }

        return trim($formattedJumlah . ' ' . $namaSatuan);
    }

    public function getFormattedStockSesudahAttribute(): string
    {
        $jumlah = $this->attributes['stock_sesudah'] ?? 0;
        $namaSatuan = $this->bahan->satuanRel->nama_satuan ?? '';
        $namaSatuanLower = strtolower($namaSatuan);

        // Konversi ml -> L dan gr -> Kg
        if ($namaSatuanLower === 'ml' && $jumlah >= 1000) {
            $jumlahKonversi = $jumlah / 1000;
            $formatted = number_format($jumlahKonversi, 3, ',', '.');
            return rtrim(rtrim($formatted, '0'), ',') . ' L';
        } elseif ($namaSatuanLower === 'gr' && $jumlah >= 1000) {
            $jumlahKonversi = $jumlah / 1000;
            $formatted = number_format($jumlahKonversi, 3, ',', '.');
            return rtrim(rtrim($formatted, '0'), ',') . ' Kg';
        }

        // Default tampilkan jumlah + satuan asli
        if (fmod($jumlah, 1) == 0) {
            $formattedJumlah = number_format($jumlah, 0, ',', '.');
        } else {
            $formattedJumlah = rtrim(rtrim(number_format($jumlah, 3, ',', '.'), '0'), ',');
        }

        return trim($formattedJumlah . ' ' . $namaSatuan);
    }


}