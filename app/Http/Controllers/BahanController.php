<?php

namespace App\Http\Controllers;

use App\Models\Bahan;
use App\Models\Gudang;
use App\Models\ProgramStudi;
use App\Models\Transaksi;
use App\Models\Satuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Imports\BahanImport;
use Maatwebsite\Excel\Facades\Excel; // Import facade Excel
use Illuminate\Support\Facades\Log;

class BahanController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('view-any-bahan');

        $user = Auth::user();
        $search = $request->input('search'); // Ambil input pencarian
        $selectedProdiId = $request->input('prodi_id');
        $query = Bahan::with(['programStudi', 'gudang']);

        // Filter untuk Superadmin & Fakultas
        $programStudis = [];
        if (in_array($user->role, ['superadmin', 'fakultas'])) {
            $programStudis = ProgramStudi::orderBy('nama_program_studi')->get();
            if ($selectedProdiId) { // Jika ada filter prodi_id
                $query->where('id_program_studi', $selectedProdiId);
            }
        } else {
            // Filter otomatis untuk Laboran
            $query->where('id_program_studi', $user->id_program_studi);
        }

        // Terapkan kondisi pencarian jika ada input search
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_bahan', 'like', '%' . $search . '%')
                  ->orWhere('kode_bahan', 'like', '%' . $search . '%')
                  ->orWhere('merk', 'like', '%' . $search . '%')
                  ->orWhere('jenis_bahan', 'like', '%' . $search . '%');
            });
        }

        $bahans = $query->orderByRaw("SUBSTRING_INDEX(kode_bahan, '-', 1) ASC")
                  ->orderByRaw("CAST(SUBSTRING_INDEX(kode_bahan, '-', -1) AS UNSIGNED) ASC")
                  ->paginate(10);
                  
        return view('bahan.index', compact('bahans', 'programStudis', 'selectedProdiId', 'search'));
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

        $satuans = Satuan::orderBy('nama_satuan')->get();

        return view('bahan.create', compact('gudangs', 'satuans'));
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
            'id_satuan' => 'required|exists:satuans,id',
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
                'id_satuan' => $request->id_satuan,
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

        $satuans = Satuan::orderBy('nama_satuan')->get();

        return view('bahan.edit', compact('bahan', 'gudangs', 'satuans'));
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
            'id_satuan' => 'required|exists:satuans,id',
            // ... validasi lain seperti di store, kecuali jumlah_stock ...
        ]);
        
        $dataToUpdate = $request->except(['jumlah_stock', '_token', '_method']);
        $dataToUpdate['id_satuan'] = $request->id_satuan; // Pastikan id_satuan diupdate

        $bahan->update($dataToUpdate);

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
                // $failure->row(); // Baris error
                // $failure->attribute(); // Kolom error (misal: 'nama_gudang')
                // $failure->errors(); // Array pesan error untuk atribut tsb
                // $failure->values(); // Nilai data di baris tersebut
                    $errors = implode(', ', $failure->errors()); 
                    $errorMessages[] = "Baris " . $failure->row() . " (Kolom: " . $failure->attribute() ."): " . $errors . " [Nilai: " . ($failure->values()[$failure->attribute()] ?? 'N/A') ."]";
             }
             return redirect()->route('bahan.showImportForm')->with('import_errors', $errorMessages);
        } catch (\Exception $e) {
        // Menangkap error umum lainnya
        Log::error("Error umum saat import: " . $e->getMessage());
        return redirect()->route('bahan.showImportForm')->with('error', 'Terjadi kesalahan saat proses import: ' . $e->getMessage());
    }

        return redirect()->route('bahan.index')->with('success', 'Data bahan berhasil diimpor.');
    }

    public function bulkDelete(Request $request)
    {
        $user = Auth::user();

        // Validasi input: pastikan 'ids' ada dan berupa array
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:bahans,id', // Pastikan setiap ID ada di tabel bahans
        ]);

        $selectedIds = $request->ids;
        $deletedCount = 0;
        $errorMessages = [];

        foreach ($selectedIds as $id) {
            $bahan = Bahan::find($id);

            if ($bahan) {
                // Otorisasi: Hanya laboran yang bisa hapus bahan prodinya
                if ($user->role === 'laboran' && $user->id_program_studi === $bahan->id_program_studi) {
                    // Kondisi hapus: Stok harus 0 (sama seperti di method destroy)
                    if ($bahan->jumlah_stock > 0) {
                        $errorMessages[] = "Bahan '{$bahan->nama_bahan}' ({$bahan->kode_bahan}) tidak dapat dihapus karena masih memiliki stok.";
                    } else {
                        $bahan->delete();
                        $deletedCount++;
                    }
                } else {
                    // Jika bukan laboran yang berhak, catat sebagai error otorisasi
                    // (meskipun idealnya mereka tidak akan bisa mengirim ID ini)
                    $errorMessages[] = "Anda tidak berhak menghapus bahan '{$bahan->nama_bahan}' ({$bahan->kode_bahan}).";
                }
            }
        }

        $message = $deletedCount . " bahan berhasil dihapus.";
        if (!empty($errorMessages)) {
            $message .= " Beberapa bahan tidak dapat dihapus: " . implode(', ', $errorMessages);
            return redirect()->route('bahan.index')->with('error', $message);
        }

        return redirect()->route('bahan.index')->with('success', $message);
    }
}