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
        // Ambil tahun aktif terbaru dari data periode stok
        $tahun_aktif = PeriodeStok::max('tahun_periode') ?? date('Y');
        return view('periode.index', compact('tahun_aktif'));
    }

    public function tutupTahun(Request $request)
    {
        $tahun_tutup = (int) $request->tahun_tutup;
        $tahun_baru = $tahun_tutup + 1;

        try {
            DB::transaction(function () use ($tahun_tutup, $tahun_baru) {
                // 1. Ambil semua bahan
                $bahans = Bahan::all();

                foreach ($bahans as $bahan) {
                    // 2. Cari periode aktif untuk bahan ini
                    $periode_lama = $bahan->periodeStoks()->where('tahun_periode', $tahun_tutup)->first();

                    if ($periode_lama && $periode_lama->status === 'aktif') {
                        // 3. Update periode lama: catat stok akhir dan ubah status
                        $periode_lama->update([
                            'stok_akhir' => $bahan->jumlah_stock, // Stok akhir adalah stok real-time saat ini
                            'status' => 'ditutup',
                        ]);

                        // 4. Buat periode baru untuk tahun berikutnya
                        $bahan->periodeStoks()->create([
                            'tahun_periode' => $tahun_baru,
                            'stok_awal' => $bahan->jumlah_stock, // Stok awal tahun baru = stok akhir tahun lama
                            'status' => 'aktif',
                        ]);
                        
                        // Kolom jumlah_stock di tabel `bahans` tidak perlu diubah, karena sudah merepresentasikan
                        // stok akhir tahun lama yang juga menjadi stok awal tahun baru.
                    }
                }
            });
        } catch (\Exception $e) {
            return redirect()->route('periode.index')->with('error', 'Gagal melakukan proses tutup tahun: ' . $e->getMessage());
        }

        return redirect()->route('periode.index')->with('success', 'Proses tutup tahun ' . $tahun_tutup . ' berhasil. Periode aktif sekarang adalah ' . $tahun_baru . '.');
    }
}