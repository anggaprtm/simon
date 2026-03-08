<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Bahan;
use App\Models\ProgramStudi;
use App\Models\Transaksi;
use App\Models\PeriodeStok;
use App\Models\ArsipLaporan;
use Illuminate\Support\Facades\Storage;


class LaporanController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $bulanSekarang = date('n');
        $tahunSekarang = date('Y');

        // 1. Cek Pengingat Arsip Laporan
        $belumUpload = false;
        if (in_array($user->role, ['laboran', 'kps'])) {
            $sudahUpload = \App\Models\ArsipLaporan::where('id_program_studi', $user->id_program_studi)
                            ->where('bulan', $bulanSekarang)
                            ->where('tahun', $tahunSekarang)
                            ->exists();
            $belumUpload = !$sudahUpload;
        }

        // 2. Data Sederhana untuk Grafik/Statistik Bulan Ini
        // Mengambil total transaksi masuk & keluar bulan ini (untuk Prodi user atau Semua Prodi)
        $queryTransaksi = \App\Models\Transaksi::whereMonth('tanggal_transaksi', $bulanSekarang)
                                               ->whereYear('tanggal_transaksi', $tahunSekarang);
        
        if (in_array($user->role, ['laboran', 'kps'])) {
            $queryTransaksi->whereHas('bahan', function($q) use ($user) {
                $q->where('id_program_studi', $user->id_program_studi);
            });
        }

        $totalMasuk = (clone $queryTransaksi)->whereIn('jenis_transaksi', ['masuk', 'penyesuaian_masuk'])->count();
        $totalKeluar = (clone $queryTransaksi)->whereIn('jenis_transaksi', ['keluar', 'penyesuaian_keluar'])->count();

        return view('laporan.index', compact('belumUpload', 'bulanSekarang', 'tahunSekarang', 'totalMasuk', 'totalKeluar'));
    }

    public function stok(Request $request)
    {
        $user = Auth::user();

        $availableYears = \App\Models\PeriodeStok::select('tahun_periode')->distinct()->orderBy('tahun_periode', 'desc')->pluck('tahun_periode');
        $tahunAktif = $availableYears->first() ?? date('Y'); 

        // Gunakan exists() agar aman dari bug URL kosong
        $selectedTahun = $request->exists('tahun') ? $request->input('tahun') : $tahunAktif;
        $selectedBulan = $request->input('bulan');        
        $selectedProdiId = $request->input('prodi_id');

        $programStudis = [];
        if (in_array($user->role, ['superadmin', 'fakultas'])) {
            $programStudis = \App\Models\ProgramStudi::orderBy('nama_program_studi')->get();
        }

        if ($selectedTahun == $tahunAktif) {
            // PERIODE AKTIF (Tabel Bahan)
            $query = \App\Models\Bahan::with(['programStudi', 'gudang', 'satuanRel', 'periodeAktif']);
            
            if (in_array($user->role, ['superadmin', 'fakultas']) && $selectedProdiId) {
                $query->where('id_program_studi', $selectedProdiId);
            } elseif (in_array($user->role, ['laboran', 'kps'])) { 
                $query->where('id_program_studi', $user->id_program_studi);
            }
            
            $query->orderBy('nama_bahan'); // Hanya siapkan query, jangan dieksekusi dulu

        } else {
            // PERIODE LAMA (Tabel PeriodeStok)
            $query = \App\Models\PeriodeStok::with(['bahan.programStudi', 'bahan.gudang', 'bahan.satuanRel'])
                                            ->where('tahun_periode', $selectedTahun);
                                            
            if (in_array($user->role, ['superadmin', 'fakultas']) && $selectedProdiId) {
                $query->whereHas('bahan', function ($q) use ($selectedProdiId) {
                    $q->where('id_program_studi', $selectedProdiId);
                });
            } elseif (in_array($user->role, ['laboran', 'kps'])) {
                $prodiId = $user->id_program_studi;
                $query->whereHas('bahan', function ($q) use ($prodiId) {
                    $q->where('id_program_studi', $prodiId);
                });
            }
        }

        // ==========================================
        // EKSEKUSI QUERY DIBEDAKAN (PRINT VS WEB)
        // ==========================================
        
        if ($request->has('print')) {
            // Jika Print: Ambil SEMUA data tanpa batasan 15 baris
            $laporanData = $query->get(); 
            return view('laporan.print.stok', compact('laporanData', 'selectedTahun', 'selectedBulan', 'tahunAktif', 'programStudis', 'selectedProdiId'));
        }

        // Jika Web: Gunakan Paginate & Appends (Cegah bug filter hilang)
        $laporanData = $query->paginate(15)->appends([
            'prodi_id' => $selectedProdiId ?? '',
            'tahun'    => $selectedTahun ?? '',
            'bulan'    => $selectedBulan ?? '',
        ]);

        return view('laporan.stok', compact('laporanData', 'selectedTahun', 'selectedBulan', 'tahunAktif', 'programStudis', 'availableYears', 'selectedProdiId'));
    }

    public function transaksi(Request $request)
    {
        $user = Auth::user();

        // 1. Ambil semua tahun unik untuk filter
        $availableYears = \App\Models\PeriodeStok::select('tahun_periode')->distinct()->orderBy('tahun_periode', 'desc')->pluck('tahun_periode');
        
        $selectedBulan = $request->input('bulan'); 
        
        // FIX: Gunakan exists() daripada has(). exists() mengecek apakah parameter ada di URL, meskipun isinya kosong/null.
        $selectedTahun = $request->exists('tahun') ? $request->input('tahun') : date('Y'); 

        $selectedProdiId = $request->input('prodi_id');
        $tanggalMulai = $request->input('tanggal_mulai');
        $tanggalSelesai = $request->input('tanggal_selesai');

        $query = \App\Models\Transaksi::with(['bahan.programStudi', 'user']);

        // 3. Filter Tanggal
        if ($request->filled('tanggal_mulai') && $request->filled('tanggal_selesai')) {
            $query->whereBetween('tanggal_transaksi', [$tanggalMulai, $tanggalSelesai . ' 23:59:59']);
        } else {
            if ($selectedTahun) {
                $query->whereYear('tanggal_transaksi', $selectedTahun);
            }
            if ($selectedBulan) {
                $query->whereMonth('tanggal_transaksi', $selectedBulan);
            }
        }

        // 4. Filter Otoritas Prodi
        $programStudis = [];
        if (in_array($user->role, ['superadmin', 'fakultas'])) {
            $programStudis = \App\Models\ProgramStudi::orderBy('nama_program_studi')->get();
            if ($request->filled('prodi_id')) {
                $query->whereHas('bahan', function ($q) use ($request) {
                    $q->where('id_program_studi', $request->prodi_id);
                });
            }
        } elseif (in_array($user->role, ['laboran', 'kps'])) {
            $prodiId = $user->id_program_studi;
            $query->whereHas('bahan', function ($q) use ($prodiId) {
                $q->where('id_program_studi', $prodiId);
            });
        }

        $query->orderBy('tanggal_transaksi', 'desc')->orderBy('id', 'desc');

        // 5. Eksekusi Query
        if ($request->has('print')) {
            $transaksis = $query->get();
            return view('laporan.print.transaksi', compact('transaksis', 'programStudis', 'selectedProdiId', 'selectedBulan', 'selectedTahun', 'tanggalMulai', 'tanggalSelesai'));
        }

        // FIX UTAMA: Gunakan ( ?? '' ) untuk memaksa null menjadi string kosong, 
        // sehingga Laravel TIDAK membuang key dari URL pagination-nya!
        $transaksis = $query->paginate(15)->appends([
            'prodi_id'      => $selectedProdiId ?? '',
            'bulan'         => $selectedBulan ?? '',
            'tahun'         => $selectedTahun ?? '',
            'tanggal_mulai' => $tanggalMulai ?? '',
            'tanggal_selesai'=> $tanggalSelesai ?? ''
        ]);

        return view('laporan.transaksi', compact('transaksis', 'programStudis', 'availableYears', 'selectedBulan', 'selectedTahun', 'selectedProdiId', 'tanggalMulai', 'tanggalSelesai'));
    }

    public function arsip(Request $request)
    {
        $user = Auth::user();
        $selectedTahun = $request->input('tahun', date('Y'));
        
        $query = ArsipLaporan::with(['programStudi', 'user'])
                    ->where('tahun', $selectedTahun);

        // Filter berdasarkan akses
        if (in_array($user->role, ['superadmin', 'fakultas'])) {
            $programStudis = ProgramStudi::orderBy('nama_program_studi')->get();
            if ($request->filled('prodi_id')) {
                $query->where('id_program_studi', $request->prodi_id);
            }
        } else {
            $programStudis = [];
            $query->where('id_program_studi', $user->id_program_studi);
        }

        $arsips = $query->orderBy('bulan', 'desc')->get();
        
        // Buat daftar tahun (5 tahun terakhir) untuk dropdown
        $years = range(date('Y'), date('Y') - 5);

        return view('laporan.arsip', compact('arsips', 'programStudis', 'selectedTahun', 'years'));
    }

    public function storeArsip(Request $request)
    {
        $request->validate([
            'jenis_laporan' => 'required|in:stok,transaksi',
            'bulan' => 'required|integer|min:1|max:12',
            'tahun' => 'required|integer',
            'file_laporan' => 'required|file|mimes:pdf|max:5120', // Maks 5MB
        ]);

        $user = Auth::user();
        $prodiId = $user->role === 'laboran' || $user->role === 'kps' ? $user->id_program_studi : $request->id_program_studi;

        if (!$prodiId) {
            return back()->with('error', 'Gagal: Program Studi tidak ditemukan.');
        }

        // Cek apakah arsip untuk bulan & tahun ini sudah ada
        $existingArsip = ArsipLaporan::where([
            'id_program_studi' => $prodiId,
            'jenis_laporan' => $request->jenis_laporan,
            'bulan' => $request->bulan,
            'tahun' => $request->tahun,
        ])->first();

        // Upload file baru
        $path = $request->file('file_laporan')->store('arsip_laporan', 'public');

        if ($existingArsip) {
            // Hapus file lama jika ada
            if (Storage::disk('public')->exists($existingArsip->file_path)) {
                Storage::disk('public')->delete($existingArsip->file_path);
            }
            // Update data
            $existingArsip->update([
                'file_path' => $path,
                'id_user' => $user->id
            ]);
            $pesan = 'Arsip laporan berhasil diperbarui.';
        } else {
            // Buat data baru
            ArsipLaporan::create([
                'id_program_studi' => $prodiId,
                'id_user' => $user->id,
                'jenis_laporan' => $request->jenis_laporan,
                'bulan' => $request->bulan,
                'tahun' => $request->tahun,
                'file_path' => $path,
            ]);
            $pesan = 'Arsip laporan berhasil diunggah.';
        }

        return back()->with('success', $pesan);
    }
}