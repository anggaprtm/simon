<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPengadaan extends Model
{
    use HasFactory;

    protected $table = 'detail_pengadaans';

    protected $fillable = [
        'id_pengajuan_pengadaan',
        'id_bahan',
        'nama_barang_input',
        'merk',
        'spesifikasi',
        'volume',
        'id_satuan',
        'harga_satuan',
        'jumlah',
        'approved_jumlah',
        'status_item',
        'catatan_revisi',
        'is_direalisasi',
        'realisasi_qty',
        'konversi_nilai',
        'link_referensi',
    ];

    protected $casts = [
        'jumlah' => 'decimal:3',
        'approved_jumlah' => 'decimal:3',
        'realisasi_qty' => 'decimal:3',
        'konversi_nilai' => 'decimal:6',
        'is_direalisasi' => 'boolean',
        'harga_satuan' => 'integer',
    ];

    public function pengajuan()
    {
        return $this->belongsTo(PengajuanPengadaan::class, 'id_pengajuan_pengadaan');
    }

    public function bahan()
    {
        return $this->belongsTo(Bahan::class, 'id_bahan');
    }

    public function satuan()
    {
        return $this->belongsTo(Satuan::class, 'id_satuan');
    }

    public function getDisplayNamaBarangAttribute(): string
    {
        return $this->bahan?->nama_bahan ?? ($this->nama_barang_input ?? '-');
    }
}
