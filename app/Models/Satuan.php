<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Satuan extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_satuan',
        'keterangan_satuan',
    ];

    // Relasi: Satu satuan bisa dimiliki oleh banyak bahan
    public function bahans()
    {
        return $this->hasMany(Bahan::class, 'id_satuan');
    }
}