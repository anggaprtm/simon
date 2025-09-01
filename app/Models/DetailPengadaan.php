<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPengadaan extends Model
{
    use HasFactory;
    protected $table = 'detail_pengadaans';
    protected $fillable = [
        'id_pengajuan_pengadaan', 'id_master_barang', 'merk', 'spesifikasi',
        'volume', 'id_satuan', 'harga_satuan', 'jumlah', 'link_referensi'
    ];

    protected $casts = [
        'jumlah' => 'decimal:3', // <-- TAMBAHKAN INI
        'harga_satuan' => 'integer',
    ];

    public function pengajuan()
    {
        return $this->belongsTo(PengajuanPengadaan::class, 'id_pengajuan_pengadaan');
    }

    public function masterBarang()
    {
        return $this->belongsTo(MasterBarang::class, 'id_master_barang');
    }

    public function satuan()
    {
        return $this->belongsTo(Satuan::class, 'id_satuan');
    }
}