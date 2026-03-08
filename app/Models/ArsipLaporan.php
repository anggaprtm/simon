<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArsipLaporan extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_program_studi', 'id_user', 'jenis_laporan', 'bulan', 'tahun', 'file_path'
    ];

    public function programStudi()
    {
        return $this->belongsTo(ProgramStudi::class, 'id_program_studi');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}