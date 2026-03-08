<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Bahan;
use App\Models\ProgramStudi;
use App\Models\Transaksi;
use App\Models\PeriodeStok;

class LaporanController extends Controller
{
    public function index()
    {
        return view('laporan.index');
    }

    public function stok(Request $request)
    {
        $user = Auth::user();

        $availableYears = PeriodeStok::select('tahun_periode')->distinct()->orderBy('tahun_periode', 'desc')->pluck('tahun_periode');
        $tahunAktif = $availableYears->first(); // Tahun aktif adalah tahun terbaru

        // Default ke tahun aktif jika tidak ada tahun yang dipilih
        $selectedTahun = $request->input('tahun', $tahunAktif);
        
        $selectedProdiId = $request->input('prodi_id');

        // Ambil data Program Studi untuk filter
        $programStudis = [];
        if (in_array($user->role, ['superadmin', 'fakultas'])) {
            $programStudis = ProgramStudi::orderBy('nama_program_studi')->get();
        }
        
        $laporanData = collect(); // Inisialisasi collection kosong

        if ($selectedTahun == $tahunAktif) {
            // JIKA MELIHAT PERIODE AKTIF, data diambil dari tabel 'bahans' (real-time)
            $query = Bahan::with(['programStudi', 'gudang', 'satuanRel', 'periodeAktif']);
            // Terapkan filter prodi
            if (in_array($user->role, ['superadmin', 'fakultas']) && $selectedProdiId) {
                $query->where('id_program_studi', $selectedProdiId);
            } elseif ($user->role === 'laboran') {
                $query->where('id_program_studi', $user->id_program_studi);
            }
            $laporanData = $query->orderBy('nama_bahan')->get();

        } else {
            // JIKA MELIHAT PERIODE LAMA (SUDAH DITUTUP), data diambil dari tabel 'periode_stoks'
            $query = PeriodeStok::with(['bahan.programStudi', 'bahan.gudang', 'bahan.satuanRel'])
                                ->where('tahun_periode', $selectedTahun);
            // Terapkan filter prodi
            if (in_array($user->role, ['superadmin', 'fakultas']) && $selectedProdiId) {
                $prodiId = $selectedProdiId;
                $query->whereHas('bahan', function ($q) use ($prodiId) {
                    $q->where('id_program_studi', $prodiId);
                });
            } elseif ($user->role === 'laboran') {
                $prodiId = $user->id_program_studi;
                $query->whereHas('bahan', function ($q) use ($prodiId) {
                    $q->where('id_program_studi', $prodiId);
                });
            }
            $laporanData = $query->get();
        }


        if ($request->has('print')) {
            return view('laporan.print.stok', compact('laporanData', 'selectedTahun', 'tahunAktif', 'programStudis', 'selectedProdiId'));
        }

        return view('laporan.stok', compact('laporanData', 'selectedTahun', 'tahunAktif', 'programStudis', 'availableYears', 'selectedProdiId'));
    }

    public function transaksi(Request $request)
    {
        $user = Auth::user();

        // 1. Ambil semua tahun unik yang ada di catatan periode untuk filter dropdown
        $availableYears = PeriodeStok::select('tahun_periode')->distinct()->orderBy('tahun_periode', 'desc')->pluck('tahun_periode');
        
        // 2. Ambil input dari filter form
        $selectedProdiId = $request->input('prodi_id');
        $selectedTahun = $request->input('tahun');
        $tanggalMulai = $request->input('tanggal_mulai');
        $tanggalSelesai = $request->input('tanggal_selesai');

        $query = Transaksi::with(['bahan.programStudi', 'user']);

        // 3. Filter berdasarkan tahun periode jika dipilih
        // Filter berdasarkan rentang tanggal
        if ($request->filled('tanggal_mulai') && $request->filled('tanggal_selesai')) {
            $query->whereBetween('tanggal_transaksi', [$request->tanggal_mulai, $request->tanggal_selesai . ' 23:59:59']);
        }

        if ($selectedTahun) {
            $query->whereYear('tanggal_transaksi', $selectedTahun);
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
            return view('laporan.print.transaksi', compact('transaksis', 'programStudis', 'selectedProdiId', 'selectedTahun', 'tanggalMulai', 'tanggalSelesai'));
        }

        return view('laporan.transaksi', compact('transaksis', 'programStudis', 'availableYears', 'selectedTahun', 'selectedProdiId', 'tanggalMulai', 'tanggalSelesai'));
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