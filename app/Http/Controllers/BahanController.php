<?php

namespace App\Http\Controllers;

use App\Models\Bahan;
use App\Models\Gudang;
use App\Models\ProgramStudi;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Imports\BahanImport;
use Maatwebsite\Excel\Facades\Excel; // Import facade Excel

class BahanController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('view-any-bahan');

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

        return view('bahan.index', compact('bahans', 'programStudis'));
    }

    public function create()
    {
        $this->authorize('create-bahan');
        
        $user = Auth::user();
        // Laboran hanya bisa memilih gudang umum atau gudang prodinya
        $gudangs = Gudang::whereNull('id_program_studi')
                         ->orWhere('id_program_studi', $user->id_program_studi)
                         ->orderBy('nama_gudang')
                         ->get();

        return view('bahan.create', compact('gudangs'));
    }

    public function store(Request $request)
    {
        $this->authorize('create-bahan');

        $user = Auth::user();

        $request->validate([
            'kode_bahan' => [
                'required', 'string', 'max:50',
                Rule::unique('bahans')->where(function ($query) use ($user) {
                    return $query->where('id_program_studi', $user->id_program_studi);
                }),
            ],
            'nama_bahan' => 'required|string|max:255',
            'merk' => 'nullable|string|max:255',
            'id_gudang' => [
                'required',
                Rule::exists('gudangs', 'id')->where(function ($query) use ($user) {
                    return $query->whereNull('id_program_studi')->orWhere('id_program_studi', $user->id_program_studi);
                }),
            ],
            'jenis_bahan' => 'nullable|string|max:100',
            'satuan' => 'required|string|max:50',
            'minimum_stock' => 'required|integer|min:0',
            'jumlah_stock' => 'nullable|integer|min:0',
            'tanggal_kedaluwarsa' => 'nullable|date',
        ]);

        try {
            DB::beginTransaction();

            $bahan = Bahan::create([
                'kode_bahan' => $request->kode_bahan,
                'nama_bahan' => $request->nama_bahan,
                'merk' => $request->merk,
                'id_program_studi' => $user->id_program_studi, // Otomatis
                'id_gudang' => $request->id_gudang,
                'satuan' => $request->satuan,
                'minimum_stock' => $request->minimum_stock,
                'jumlah_stock' => $request->jumlah_stock ?? 0, // Set stok awal
                'tanggal_kedaluwarsa' => $request->tanggal_kedaluwarsa,
            ]);

            // Jika ada stok awal, catat sebagai transaksi pertama
            if ($bahan->jumlah_stock > 0) {
                Transaksi::create([
                    'id_bahan' => $bahan->id,
                    'id_user' => $user->id,
                    'jenis_transaksi' => 'masuk',
                    'jumlah' => $bahan->jumlah_stock,
                    'tanggal_transaksi' => now(),
                    'keterangan' => 'Stok awal',
                    'stock_sebelum' => 0,
                    'stock_sesudah' => $bahan->jumlah_stock,
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menyimpan data bahan: ' . $e->getMessage())->withInput();
        }
        
        return redirect()->route('bahan.index')->with('success', 'Bahan berhasil ditambahkan.');
    }

    public function edit(Bahan $bahan)
    {
        $this->authorize('update-bahan', $bahan);
        
        $user = Auth::user();
        $gudangs = Gudang::whereNull('id_program_studi')
                         ->orWhere('id_program_studi', $user->id_program_studi)
                         ->orderBy('nama_gudang')
                         ->get();

        return view('bahan.edit', compact('bahan', 'gudangs'));
    }

    public function update(Request $request, Bahan $bahan)
    {
        $this->authorize('update-bahan', $bahan);

        $user = Auth::user();

        $request->validate([
            'kode_bahan' => [
                'required', 'string', 'max:50',
                Rule::unique('bahans')->where(function ($query) use ($user) {
                    return $query->where('id_program_studi', $user->id_program_studi);
                })->ignore($bahan->id),
            ],
            'nama_bahan' => 'required|string|max:255',
            'jenis_bahan' => 'nullable|string|max:100',
            'id_gudang' => [
                'required',
                Rule::exists('gudangs', 'id')->where(function ($query) use ($user) {
                    return $query->whereNull('id_program_studi')->orWhere('id_program_studi', $user->id_program_studi);
                }),
            ],
            // ... validasi lain seperti di store, kecuali jumlah_stock ...
        ]);
        
        // Stok tidak diupdate dari sini, hanya metadata
        $bahan->update($request->except('jumlah_stock'));

        return redirect()->route('bahan.index')->with('success', 'Data bahan berhasil diperbarui.');
    }

    public function destroy(Bahan $bahan)
    {
        $this->authorize('delete-bahan', $bahan);

        // Sebaiknya jangan hapus bahan jika masih ada stok
        if ($bahan->jumlah_stock > 0) {
            return redirect()->route('bahan.index')
                             ->with('error', 'Bahan tidak dapat dihapus karena masih memiliki stok. Habiskan stok terlebih dahulu melalui transaksi barang keluar.');
        }

        $bahan->delete();

        return redirect()->route('bahan.index')->with('success', 'Bahan berhasil dihapus.');
    }

    // Menampilkan halaman form import
    public function showImportForm()
    {
        $this->authorize('create-bahan');
        return view('bahan.import');
    }

    // Memproses file yang di-upload
    public function import(Request $request)
    {
        $this->authorize('create-bahan');

        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        try {
            // Saat mengimpor, kita juga kirim data user yg login ke class import
            Excel::import(new BahanImport(Auth::user()), $request->file('file'));
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            // Jika ada error validasi dari file excel, kumpulkan dan tampilkan
             $failures = $e->failures();
             $errorMessages = [];
             foreach ($failures as $failure) {
                 $errorMessages[] = "Baris " . $failure->row() . ": " . implode(', ', $failure->errors());
             }
             return redirect()->route('bahan.showImportForm')->with('import_errors', $errorMessages);
        }

        return redirect()->route('bahan.index')->with('success', 'Data bahan berhasil diimpor.');
    }
}