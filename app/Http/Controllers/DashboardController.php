<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Bahan;
use App\Models\ProgramStudi;
use App\Models\Transaksi;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $data = [];

        // Logika untuk role Laboran
        if ($user->role === 'laboran') {
            $prodiId = $user->id_program_studi;

            // Jumlah jenis bahan di prodi ini
            $data['jumlah_bahan'] = Bahan::where('id_program_studi', $prodiId)->count();

            // Bahan yang stoknya menipis
            $data['stok_menipis'] = Bahan::where('id_program_studi', $prodiId)
                                         ->whereColumn('jumlah_stock', '<=', 'minimum_stock')
                                         ->where('jumlah_stock', '>', 0) // Hanya tampilkan yang stoknya belum 0
                                         ->get();

            // Bahan yang akan kedaluwarsa (misal, dalam 60 hari ke depan)
            $data['akan_kedaluwarsa'] = Bahan::where('id_program_studi', $prodiId)
                                             ->whereNotNull('tanggal_kedaluwarsa')
                                             ->where('tanggal_kedaluwarsa', '<=', now()->addDays(60))
                                             ->where('tanggal_kedaluwarsa', '>=', now())
                                             ->orderBy('tanggal_kedaluwarsa', 'asc')
                                             ->get();
            
            // 5 Transaksi terakhir di prodi ini
            $data['transaksi_terakhir'] = Transaksi::whereHas('bahan', function ($query) use ($prodiId) {
                $query->where('id_program_studi', $prodiId);
            })->with(['bahan', 'user'])->latest()->take(5)->get();
        }

        // Logika untuk role Superadmin & Fakultas
        if (in_array($user->role, ['superadmin', 'fakultas'])) {
            // Statistik Global
            $data['total_bahan'] = Bahan::count();
            $data['total_user'] = User::count();
            $data['total_prodi'] = ProgramStudi::count();
            
            // Ringkasan per prodi
            $data['ringkasan_prodi'] = ProgramStudi::withCount(['bahans', 'users'])->get();
        }

        return view('dashboard', compact('data'));
    }
}