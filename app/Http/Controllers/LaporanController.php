<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Bahan;
use App\Models\ProgramStudi;
use App\Models\Transaksi;
// ... model lain

class LaporanController extends Controller
{
    public function index()
    {
        return view('laporan.index');
    }

    public function stok(Request $request)
    {
        $user = Auth::user();
        $query = Bahan::with(['programStudi', 'gudang']);

        // Filter untuk Superadmin & Fakultas
        $programStudis = [];
        if (in_array($user->role, ['superadmin', 'fakultas'])) {
            $programStudis = ProgramStudi::orderBy('nama_program_studi')->get();
            if ($request->filled('prodi_id')) {
                $query->where('id_program_studi', $request->prodi_id);
            }
        } else {
            // Filter otomatis untuk Laboran
            $query->where('id_program_studi', $user->id_program_studi);
        }

        $bahans = $query->orderBy('nama_bahan')->get();

        // Jika ada permintaan cetak, gunakan layout print
        if ($request->has('print')) {
            return view('laporan.print.stok', compact('bahans', 'programStudis'));
        }

        return view('laporan.stok', compact('bahans', 'programStudis'));
    }

   public function transaksi(Request $request)
    {
        $user = Auth::user();
        $query = Transaksi::with(['bahan.programStudi', 'user']);

        // Filter berdasarkan rentang tanggal
        if ($request->filled('tanggal_mulai') && $request->filled('tanggal_selesai')) {
            $query->whereBetween('tanggal_transaksi', [$request->tanggal_mulai, $request->tanggal_selesai . ' 23:59:59']);
        }

        // Filter untuk Superadmin & Fakultas
        $programStudis = [];
        if (in_array($user->role, ['superadmin', 'fakultas'])) {
            $programStudis = ProgramStudi::orderBy('nama_program_studi')->get();
            if ($request->filled('prodi_id')) {
                $prodiId = $request->prodi_id;
                $query->whereHas('bahan', function ($q) use ($prodiId) {
                    $q->where('id_program_studi', $prodiId);
                });
            }
        } else {
            // Filter otomatis untuk Laboran
            $prodiId = $user->id_program_studi;
            $query->whereHas('bahan', function ($q) use ($prodiId) {
                $q->where('id_program_studi', $prodiId);
            });
        }

        $transaksis = $query->latest('tanggal_transaksi')->get();

        // Jika ada permintaan cetak, gunakan layout print
        if ($request->has('print')) {
            return view('laporan.print.transaksi', compact('transaksis', 'programStudis'));
        }

        return view('laporan.transaksi', compact('transaksis', 'programStudis'));
    }
}