<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany; // Import HasMany

class ProgramStudi extends Model
{
    use HasFactory;

    protected $table = 'program_studis'; // Opsional jika nama model sudah sesuai konvensi (plural snake_case)

    protected $fillable = [
        'nama_program_studi',
        'kode_program_studi',
    ];

    /**
     * Relasi ke User (satu program studi memiliki banyak user laboran)
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'id_program_studi');
    }

    /**
     * Relasi ke Bahan (satu program studi memiliki banyak bahan)
     */
    public function bahans(): HasMany
    {
        return $this->hasMany(Bahan::class, 'id_program_studi');
    }

    /**
     * Relasi ke Gudang (satu program studi bisa memiliki banyak gudang spesifik)
     */
    public function gudangs(): HasMany
    {
        return $this->hasMany(Gudang::class, 'id_program_studi');
    }
}