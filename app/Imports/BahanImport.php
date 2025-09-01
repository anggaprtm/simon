<?php

namespace App\Imports;

use App\Models\Bahan;
use App\Models\Gudang;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Validators\ValidationException;
use App\Models\Satuan;
use Illuminate\Validation\Rule;

class BahanImport implements ToCollection, WithHeadingRow, WithValidation
{
    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            try {
                DB::transaction(function () use ($row) {
                    // Karena sudah lolos validasi rules(), kita bisa lebih percaya diri mencari gudang
                    $gudang = Gudang::where('nama_gudang', $row['nama_gudang'])
                        ->where(function ($query) {
                            $query->whereNull('id_program_studi')
                                  ->orWhere('id_program_studi', $this->user->id_program_studi);
                        })->first();
                    
                    // Seharusnya $gudang selalu ditemukan karena sudah divalidasi oleh rules()
                    // Namun, sebagai pengaman tambahan jika ada kasus aneh:
                    if (!$gudang) { 
                        // Ini idealnya tidak akan pernah terpanggil jika rules() bekerja
                        Log::error("CRITICAL: Gudang '{$row['nama_gudang']}' tidak ditemukan MESKIPUN LOLOS VALIDASI untuk kode bahan '{$row['kode_bahan']}'.");
                        return; 
                    }

                    $satuanModel = Satuan::where('nama_satuan', $row['nama_satuan'])->first();
                    if (!$satuanModel) {
                        // Ini idealnya tidak terjadi jika validasi di rules() bekerja
                        Log::error("CRITICAL: Satuan '{$row['nama_satuan']}' tidak ditemukan MESKIPUN LOLOS VALIDASI untuk kode bahan '{$row['kode_bahan']}'.");
                        return;
                    }

                    $stokAwal = $row['jumlah_stock_awal'] ?? 0;

                    $bahan = Bahan::create([
                        'kode_bahan'        => $row['kode_bahan'],
                        'nama_bahan'        => $row['nama_bahan'],
                        'merk'              => $row['merk'],
                        'jenis_bahan'       => $row['jenis_bahan'],
                        'id_gudang'         => $gudang->id,
                        'id_satuan'         => $satuanModel->id,
                        'minimum_stock'     => $row['minimum_stock'] ?? 0,
                        'jumlah_stock'      => $stokAwal,
                        'tanggal_kedaluwarsa' => $row['tanggal_kedaluwarsa'],
                        'id_program_studi'  => $this->user->id_program_studi,
                    ]);
                    
                    $bahan->periodeStoks()->create([
                        'tahun_periode' => date('Y'), // Tahun saat ini
                        'stok_awal' => $stokAwal,
                        'status' => 'aktif',
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
            } catch (\Exception $e) {
                // Jika ada error lain yang tidak terduga saat proses simpan per baris
                Log::error("Gagal menyimpan baris dari Excel dengan kode bahan: " . ($row['kode_bahan'] ?? 'UNKNOWN') . ". Error: " . $e->getMessage());
                // Pertimbangkan apakah ingin mengumpulkan pesan error ini untuk ditampilkan ke user,
                // atau biarkan Laravel Excel menangani ini jika error berasal dari validasi awal.
                // Untuk saat ini, kita log saja dan biarkan proses berlanjut untuk baris lain jika memungkinkan,
                // TAPI jika WithValidation bekerja, error ini seharusnya jarang terjadi.
            }
        }
    }

    public function rules(): array
    {
        return [
            // 'baris_excel.*.kolom_excel'
            '*.kode_bahan' => [
                'required', 
                'string', 
                'max:50',
                // Kode bahan harus unik untuk prodi user yang sedang login
                Rule::unique('bahans', 'kode_bahan')->where('id_program_studi', $this->user->id_program_studi)
            ],
            '*.nama_bahan' => 'required|string|max:255',
            '*.merk' => 'nullable|string|max:255',
            '*.jenis_bahan' => 'nullable|string|max:100',
            '*.nama_satuan' => 'required|string|max:50|exists:satuans,nama_satuan',
            '*.nama_gudang' => [
                'required',
                'string',
                // Validasi bahwa nama_gudang ada di tabel gudangs DAN
                // gudang tersebut adalah gudang umum (id_program_studi IS NULL) ATAU
                // gudang tersebut milik prodi user yang sedang login.
                Rule::exists('gudangs', 'nama_gudang')->where(function ($query) {
                    return $query->whereNull('id_program_studi')
                                 ->orWhere('id_program_studi', $this->user->id_program_studi);
                })
            ],
            '*.minimum_stock' => 'nullable|numeric|min:0',
            '*.jumlah_stock_awal' => 'nullable|numeric|min:0',
            '*.tanggal_kedaluwarsa' => 'nullable|date_format:Y-m-d', // Pastikan format tanggal YYYY-MM-DD
        ];
    }

    // Pesan error kustom untuk validasi (opsional tapi sangat membantu pengguna)
    public function customValidationMessages()
    {
        return [
            '*.kode_bahan.required' => 'Kode bahan wajib diisi.',
            '*.kode_bahan.unique' => 'Kode bahan sudah ada untuk untuk unit Anda.',
            '*.nama_bahan.required' => 'Nama bahan wajib diisi.',
            '*.nama_gudang.required' => 'Nama gudang wajib diisi.',
            '*.nama_gudang.exists' => 'Nama gudang tidak terdaftar (silahkan cek daftar gudang).',
            '*.tanggal_kedaluwarsa.date_format' => 'Format tanggal kedaluwarsa harus YYYY-MM-DD.',
            '*.nama_satuan.required' => 'Nama satuan wajib diisi.',
            '*.nama_satuan.exists' => 'Nama satuan tidak terdaftar di master satuan.',
        ];
    }

}