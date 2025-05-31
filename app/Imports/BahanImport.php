<?php

namespace App\Imports;

use App\Models\Bahan;
use App\Models\Gudang;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Validators\ValidationException;

class BahanImport implements ToCollection, WithHeadingRow
{
    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function collection(Collection $rows)
    {
        $validator = Validator::make($rows->toArray(), [
            '*.kode_bahan' => 'required|string',
            '*.nama_bahan' => 'required|string',
            '*.nama_gudang' => 'required|string',
            '*.satuan' => 'required|string',
            '*.minimum_stock' => 'nullable|integer',
            '*.jumlah_stock_awal' => 'nullable|integer',
            '*.tanggal_kedaluwarsa' => 'nullable|date_format:Y-m-d',
        ])->validate();

        foreach ($rows as $row) 
        {
            DB::transaction(function () use ($row) {
                // Cari gudang berdasarkan nama, hanya di gudang umum atau milik prodi user
                $gudang = Gudang::where('nama_gudang', $row['nama_gudang'])
                    ->where(function ($query) {
                        $query->whereNull('id_program_studi')
                              ->orWhere('id_program_studi', $this->user->id_program_studi);
                    })->first();

                // Jika gudang tidak ditemukan, lewati baris ini (atau bisa di-handle dengan error)
                if (!$gudang) {
                    // Bisa ditambahkan logika untuk mengumpulkan error
                    return;
                }

                // Cek duplikasi kode_bahan untuk prodi ini
                $existingBahan = Bahan::where('kode_bahan', $row['kode_bahan'])
                                      ->where('id_program_studi', $this->user->id_program_studi)
                                      ->exists();
                if($existingBahan) {
                    // Bisa ditambahkan logika untuk mengumpulkan error
                    return;
                }
                
                $stokAwal = $row['jumlah_stock_awal'] ?? 0;

                $bahan = Bahan::create([
                    'kode_bahan'        => $row['kode_bahan'],
                    'nama_bahan'        => $row['nama_bahan'],
                    'merk'              => $row['merk'],
                    'jenis_bahan'       => $row['jenis_bahan'],
                    'id_gudang'         => $gudang->id,
                    'satuan'            => $row['satuan'],
                    'minimum_stock'     => $row['minimum_stock'] ?? 0,
                    'jumlah_stock'      => $stokAwal,
                    'tanggal_kedaluwarsa' => $row['tanggal_kedaluwarsa'],
                    'id_program_studi'  => $this->user->id_program_studi,
                ]);

                if ($stokAwal > 0) {
                    Transaksi::create([
                        'id_bahan' => $bahan->id,
                        'id_user' => $this->user->id,
                        'jenis_transaksi' => 'masuk',
                        'jumlah' => $stokAwal,
                        'stock_sebelum' => 0,
                        'stock_sesudah' => $stokAwal,
                        'tanggal_transaksi' => now(),
                        'keterangan' => 'Stok awal dari import Excel',
                    ]);
                }
            });
        }
    }
}