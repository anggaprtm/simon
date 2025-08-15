<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengajuanPengadaan extends Model
{
    use HasFactory;
    protected $table = 'pengajuan_pengadaans';
    protected $fillable = ['id_user', 'id_program_studi', 'tahun_ajaran', 'semester', 'nomor_surat', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function programStudi()
    {
        return $this->belongsTo(ProgramStudi::class, 'id_program_studi');
    }

    // Satu pengajuan memiliki banyak item detail
    public function details()
    {
        return $this->hasMany(DetailPengadaan::class, 'id_pengajuan_pengadaan');
    }
}