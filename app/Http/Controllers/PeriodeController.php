<?php
namespace App\Http\Controllers;

use App\Models\Bahan;
use App\Models\PeriodeStok;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PeriodeController extends Controller
{
    public function index()
    {
        // Cari tahun yang statusnya masih aktif, jangan pakai max() karena rawan salah jika ada data anomali
        $periodeAktif = PeriodeStok::where('status', 'aktif')->first();
        $tahun_aktif = $periodeAktif ? $periodeAktif->tahun_periode : date('Y');

        // Batasi hanya bisa tutup tahun di bulan Desember (12) dan Januari (1)
        $bulan_sekarang = (int) date('n');
        $bisa_tutup = in_array($bulan_sekarang, [12, 1]);

        return view('periode.index', compact('tahun_aktif', 'bisa_tutup'));
    }

    public function tutupTahun(Request $request)
    {
        // Validasi ganda di backend (mencegah user bypass lewat inspect element)
        $bulan_sekarang = (int) date('n');
        if (!in_array($bulan_sekarang, [12, 1])) {
            return redirect()->route('periode.index')->with('error', 'AKSI DITOLAK: Tutup tahun hanya dapat dilakukan pada bulan Desember atau Januari.');
        }

        $tahun_tutup = (int) $request->tahun_tutup;
        $tahun_baru = $tahun_tutup + 1;

        try {
            DB::transaction(function () use ($tahun_tutup, $tahun_baru) {
                // Proses data per 500 baris agar RAM server tidak jebol
                Bahan::chunk(500, function ($bahans) use ($tahun_tutup, $tahun_baru) {
                    foreach ($bahans as $bahan) {
                        // Kunci row ini (lockForUpdate) agar tidak ada yang bisa transaksi saat diproses
                        $periode_lama = PeriodeStok::where('id_bahan', $bahan->id)
                            ->where('tahun_periode', $tahun_tutup)
                            ->where('status', 'aktif')
                            ->lockForUpdate()
                            ->first();

                        if ($periode_lama) {
                            $periode_lama->update([
                                'stok_akhir' => $bahan->jumlah_stock,
                                'status' => 'ditutup',
                            ]);

                            PeriodeStok::create([
                                'id_bahan' => $bahan->id,
                                'tahun_periode' => $tahun_baru,
                                'stok_awal' => $bahan->jumlah_stock,
                                'stok_akhir' => 0,
                                'status' => 'aktif',
                            ]);
                        } else {
                            // EDGE CASE: Jika bahan ada tapi belum punya periode di tahun ini, 
                            // langsung buatkan periode untuk tahun depan biar nggak error/hilang dari laporan.
                            $cekPeriodeBaru = PeriodeStok::where('id_bahan', $bahan->id)->where('tahun_periode', $tahun_baru)->exists();
                            
                            if (!$cekPeriodeBaru) {
                                PeriodeStok::create([
                                    'id_bahan' => $bahan->id,
                                    'tahun_periode' => $tahun_baru,
                                    'stok_awal' => $bahan->jumlah_stock,
                                    'stok_akhir' => 0,
                                    'status' => 'aktif',
                                ]);
                            }
                        }
                    }
                });
            });
        } catch (\Exception $e) {
            return redirect()->route('periode.index')->with('error', 'Gagal melakukan proses tutup tahun: ' . $e->getMessage());
        }

        return redirect()->route('periode.index')->with('success', 'Proses tutup tahun ' . $tahun_tutup . ' berhasil. Periode aktif sekarang adalah ' . $tahun_baru . '.');
    }
}