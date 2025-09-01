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
        'jenis_bahan',
        'format_kimia',
        'id_program_studi',
        'id_gudang',
        'id_satuan',
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
        'jumlah_stock' => 'decimal:3',
        'minimum_stock' => 'decimal:3',
        'format_kimia' => 'boolean',
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

    public function satuanRel() 
    {
        return $this->belongsTo(Satuan::class, 'id_satuan');
    }

    public function getNamaBahanHtmlAttribute(): string
    {
        $namaBahanAsli = $this->attributes['nama_bahan'] ?? '';
        if ($this->format_kimia) {
            return preg_replace('/(?<=[A-Za-z\)])(\d+)/', '<sub>$1</sub>', $namaBahanAsli);
        }

        return $namaBahanAsli;
    }

    public function getFormattedStockAttribute(): string
    {
        $jumlah = $this->attributes['jumlah_stock'] ?? 0;
        $namaSatuan = $this->satuanRel->nama_satuan ?? '';
        $namaSatuanLower = strtolower($namaSatuan);

        // Logika konversi unit (contoh untuk ml -> L dan gr -> Kg)
        if ($namaSatuanLower === 'ml' && $jumlah >= 1000) {
            $jumlahKonversi = $jumlah / 1000;
            // Format dengan 1-3 desimal, hapus .0 jika bilangan bulat
            $formatted = number_format($jumlahKonversi, 3, ',', '.');
            return rtrim(rtrim($formatted, '0'), ',') . ' L';
        } elseif ($namaSatuanLower === 'gr' && $jumlah >= 1000) {
            $jumlahKonversi = $jumlah / 1000;
            $formatted = number_format($jumlahKonversi, 3, ',', '.');
            return rtrim(rtrim($formatted, '0'), ',') . ' Kg';
        }

        // Jika tidak ada konversi, format angka asli dan tambahkan satuan aslinya
        $formattedJumlah = '';
        if (fmod($jumlah, 1) == 0) {
            // Jika bilangan bulat
            $formattedJumlah = number_format($jumlah, 0, ',', '.');
        } else {
            // Jika desimal, hapus angka 0 di akhir
            $formattedJumlah = rtrim(rtrim(number_format($jumlah, 3, ',', '.'), '0'), ',');
        }

        return trim($formattedJumlah . ' ' . $namaSatuan);
    }

    public function periodeStoks()
    {
        return $this->hasMany(PeriodeStok::class, 'id_bahan');
    }

    // Relasi untuk mendapatkan periode stok yang sedang aktif
    public function periodeAktif()
    {
        return $this->hasOne(PeriodeStok::class, 'id_bahan')->where('status', 'aktif');
    }
}