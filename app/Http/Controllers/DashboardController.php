<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Bahan;
use App\Models\ProgramStudi;
use App\Models\Transaksi;
use App\Models\User;
use App\Models\PengajuanPengadaan;
use App\Models\ArsipLaporan;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $data = [];

        // ========================================================
        // LOGIKA UNTUK ROLE LABORAN & KPS (Level Program Studi)
        // ========================================================
        if (in_array($user->role, ['laboran', 'kps'])) {
            $prodiId = $user->id_program_studi;

            // 1. Statistik Bahan & Stok
            $data['jumlah_bahan'] = Bahan::where('id_program_studi', $prodiId)->count();

            $data['stok_menipis'] = Bahan::where('id_program_studi', $prodiId)
                                         ->whereColumn('jumlah_stock', '<=', 'minimum_stock')
                                         ->where('jumlah_stock', '>', 0)
                                         ->get();

            $data['akan_kedaluwarsa'] = Bahan::where('id_program_studi', $prodiId)
                                             ->whereNotNull('tanggal_kedaluwarsa')
                                             ->where('tanggal_kedaluwarsa', '<=', now()->addDays(60))
                                             ->where('tanggal_kedaluwarsa', '>=', now())
                                             ->orderBy('tanggal_kedaluwarsa', 'asc')
                                             ->get();
            
            // 2. Transaksi Terakhir
            $data['transaksi_terakhir'] = Transaksi::whereHas('bahan', function ($query) use ($prodiId) {
                $query->where('id_program_studi', $prodiId);
            })->with(['bahan', 'user'])->latest()->take(5)->get();

            // 3. Status Pengajuan Pengadaan (Tracking)
            $queryPengajuan = PengajuanPengadaan::where('id_program_studi', $prodiId);
            // Jika laboran, hanya lihat miliknya. Jika KPS, lihat semua di prodinya.
            if ($user->role === 'laboran') {
                $queryPengajuan->where('id_user', $user->id);
            }
            
            $data['pengajuan_draft'] = (clone $queryPengajuan)->where('status', 'Draft')->count();
            $data['pengajuan_diajukan'] = (clone $queryPengajuan)->where('status', 'Diajukan')->count();
            $data['pengajuan_disetujui'] = (clone $queryPengajuan)->where('status', 'Disetujui')->count();

            // 4. Peringatan Operasional (Arsip & Tutup Tahun)
            $bulanSekarang = date('n');
            $tahunSekarang = date('Y');
            
            $data['belum_upload_arsip'] = !ArsipLaporan::where('id_program_studi', $prodiId)
                            ->where('bulan', $bulanSekarang)
                            ->where('tahun', $tahunSekarang)
                            ->exists();
                            
            $data['bisa_tutup_tahun'] = in_array($bulanSekarang, [1, 12]);
        }

        // ========================================================
        // LOGIKA UNTUK ROLE SUPERADMIN & FAKULTAS (Level Global)
        // ========================================================
        if (in_array($user->role, ['superadmin', 'fakultas'])) {
            $data['total_bahan'] = Bahan::count();
            $data['total_user'] = User::count();
            $data['total_prodi'] = ProgramStudi::count();
            $data['ringkasan_prodi'] = ProgramStudi::withCount(['bahans', 'users'])->get();
        }

        return view('dashboard', compact('data'));
    }
}