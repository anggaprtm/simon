<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Import BelongsTo
use Illuminate\Database\Eloquent\Relations\HasMany;   // Import HasMany

class Gudang extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_gudang',
        'lokasi',
        'id_program_studi', // Penting untuk diisi jika gudang milik prodi tertentu
    ];

    /**
     * Relasi ke ProgramStudi (sebuah gudang bisa jadi milik prodi tertentu)
     * Jika id_program_studi NULL, maka gudang ini umum.
     */
    public function programStudi(): BelongsTo
    {
        return $this->belongsTo(ProgramStudi::class, 'id_program_studi');
    }

    /**
     * Relasi ke Bahan (satu gudang bisa menyimpan banyak bahan)
     */
    public function bahans(): HasMany
    {
        return $this->hasMany(Bahan::class, 'id_gudang');
    }
}