<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeriodeStok extends Model
{
    use HasFactory;
    protected $table = 'periode_stoks';
    protected $fillable = [
        'id_bahan',
        'tahun_periode',
        'stok_awal',
        'stok_akhir',
        'status',
    ];

    // Relasi ke Bahan
    public function bahan()
    {
        return $this->belongsTo(Bahan::class, 'id_bahan');
    }
}