<?php

namespace App\Http\Controllers;

use App\Models\Bahan;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class TransaksiStokController extends Controller
{
    // Menampilkan form stok masuk
    public function createMasuk(Bahan $bahan)
    {
        Gate::authorize('update-bahan', $bahan);
        $jenis = 'masuk';
        return view('transaksi.create', compact('bahan', 'jenis'));
    }

    // Menampilkan form stok keluar
    public function createKeluar(Bahan $bahan)
    {
        Gate::authorize('update-bahan', $bahan);
        $jenis = 'keluar';
        return view('transaksi.create', compact('bahan', 'jenis'));
    }

    // Menyimpan transaksi stok masuk
    public function storeMasuk(Request $request, Bahan $bahan)
    {
        Gate::authorize('update-bahan', $bahan);

        $request->validate([
            'jumlah' => 'required|integer|min:1',
            'tanggal_transaksi' => 'required|date',
            'keterangan' => 'nullable|string',
        ]);

        try {
            DB::transaction(function () use ($request, $bahan) {
                // Kunci baris bahan untuk mencegah race condition
                $bahan = Bahan::where('id', $bahan->id)->lockForUpdate()->firstOrFail();

                $stok_sebelum = $bahan->jumlah_stock;
                $stok_sesudah = $stok_sebelum + $request->jumlah;

                // 1. Catat Transaksi
                Transaksi::create([
                    'id_bahan' => $bahan->id,
                    'id_user' => Auth::id(),
                    'jenis_transaksi' => 'masuk',
                    'jumlah' => $request->jumlah,
                    'stock_sebelum' => $stok_sebelum,
                    'stock_sesudah' => $stok_sesudah,
                    'tanggal_transaksi' => $request->tanggal_transaksi,
                    'keterangan' => $request->keterangan,
                ]);

                // 2. Update Stok di Tabel Bahan
                $bahan->update(['jumlah_stock' => $stok_sesudah]);
            });
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menyimpan transaksi: ' . $e->getMessage())->withInput();
        }

        return redirect()->route('bahan.index')->with('success', 'Transaksi stok masuk berhasil dicatat.');
    }

    // Menyimpan transaksi stok keluar
    public function storeKeluar(Request $request, Bahan $bahan)
    {
        Gate::authorize('update-bahan', $bahan);
        
        // Validasi agar jumlah keluar tidak melebihi stok yang ada
        $request->validate([
            'jumlah' => 'required|integer|min:1|max:' . $bahan->jumlah_stock,
            'tanggal_transaksi' => 'required|date',
            'keterangan' => 'nullable|string',
        ]);

        try {
            DB::transaction(function () use ($request, $bahan) {
                $bahan = Bahan::where('id', $bahan->id)->lockForUpdate()->firstOrFail();

                $stok_sebelum = $bahan->jumlah_stock;
                $stok_sesudah = $stok_sebelum - $request->jumlah;

                // 1. Catat Transaksi
                Transaksi::create([
                    'id_bahan' => $bahan->id,
                    'id_user' => Auth::id(),
                    'jenis_transaksi' => 'keluar',
                    'jumlah' => $request->jumlah,
                    'stock_sebelum' => $stok_sebelum,
                    'stock_sesudah' => $stok_sesudah,
                    'tanggal_transaksi' => $request->tanggal_transaksi,
                    'keterangan' => $request->keterangan,
                ]);

                // 2. Update Stok di Tabel Bahan
                $bahan->update(['jumlah_stock' => $stok_sesudah]);
            });
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menyimpan transaksi: ' . $e->getMessage())->withInput();
        }

        return redirect()->route('bahan.index')->with('success', 'Transaksi stok keluar berhasil dicatat.');
    }

    // Menampilkan riwayat transaksi
    public function history(Bahan $bahan)
    {
        // Di sini kita perlu Gate baru untuk 'view'
        Gate::authorize('view-bahan', $bahan);

        $transaksis = Transaksi::where('id_bahan', $bahan->id)
                                ->with('user')
                                ->orderBy('tanggal_transaksi', 'desc')
                                ->orderBy('id', 'desc')
                                ->get();
                                
        return view('transaksi.history', compact('bahan', 'transaksis'));
    }

     // Method untuk menampilkan form penyesuaian
    public function createPenyesuaian(Bahan $bahan)
    {
        Gate::authorize('update-bahan', $bahan);
        return view('penyesuaian.create', compact('bahan'));
    }

    // Method untuk memproses dan menyimpan penyesuaian
    public function storePenyesuaian(Request $request, Bahan $bahan)
    {
        Gate::authorize('update-bahan', $bahan);

        $request->validate([
            'stok_fisik' => 'required|integer|min:0',
            'keterangan' => 'required|string',
        ]);

        try {
            DB::transaction(function () use ($request, $bahan) {
                // Kunci baris agar tidak ada proses lain yang mengubah stok saat ini
                $bahan = Bahan::where('id', $bahan->id)->lockForUpdate()->firstOrFail();
                
                $stok_sistem = $bahan->jumlah_stock;
                $stok_fisik = (int) $request->stok_fisik;
                
                // Hitung selisihnya
                $selisih = $stok_fisik - $stok_sistem;

                // Jika tidak ada selisih, tidak perlu lakukan apa-apa
                if ($selisih == 0) {
                    // Kita bisa redirect dengan pesan 'info' (opsional)
                    // Untuk saat ini, kita anggap tidak terjadi apa-apa
                    return;
                }

                $jenis_transaksi = $selisih > 0 ? 'penyesuaian_masuk' : 'penyesuaian_keluar';
                
                // 1. Catat Transaksi Penyesuaian
                Transaksi::create([
                    'id_bahan' => $bahan->id,
                    'id_user' => Auth::id(),
                    'jenis_transaksi' => $jenis_transaksi,
                    'jumlah' => abs($selisih), // Jumlah transaksi selalu positif
                    'stock_sebelum' => $stok_sistem,
                    'stock_sesudah' => $stok_fisik,
                    'tanggal_transaksi' => now(),
                    'keterangan' => $request->keterangan,
                ]);

                // 2. Update stok di tabel bahan menjadi sama dengan stok fisik
                $bahan->update(['jumlah_stock' => $stok_fisik]);
            });

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menyimpan penyesuaian: ' . $e->getMessage())->withInput();
        }
        
        return redirect()->route('bahan.index')->with('success', 'Stok bahan berhasil disesuaikan.');
    }
}