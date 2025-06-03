<?php

namespace App\Http\Controllers;

use App\Models\Satuan;
use Illuminate\Http\Request; // Pastikan ini di-import
use Illuminate\Validation\Rule; // Pastikan ini di-import untuk validasi unique saat update

class SatuanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $satuans = Satuan::orderBy('nama_satuan', 'asc')->get();
        return view('satuan.index', compact('satuans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('satuan.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_satuan' => 'required|string|max:50|unique:satuans,nama_satuan',
            'keterangan_satuan' => 'nullable|string|max:100',
        ]);

        Satuan::create($request->all());

        return redirect()->route('satuan.index')
                         ->with('success', 'Satuan berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     * (Method show() biasanya tidak kita gunakan untuk CRUD master data sederhana seperti ini)
     */
    public function show(Satuan $satuan)
    {
        // Jika tidak digunakan, bisa dikosongkan atau redirect ke index/edit
        return redirect()->route('satuan.edit', $satuan);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Satuan $satuan)
    {
        return view('satuan.edit', compact('satuan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Satuan $satuan)
    {
        $request->validate([
            'nama_satuan' => [
                'required',
                'string',
                'max:50',
                Rule::unique('satuans', 'nama_satuan')->ignore($satuan->id),
            ],
            'keterangan_satuan' => 'nullable|string|max:100',
        ]);

        $satuan->update($request->all());

        return redirect()->route('satuan.index')
                         ->with('success', 'Satuan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Satuan $satuan)
    {
        // Pengecekan apakah satuan sedang digunakan oleh data bahan
        if ($satuan->bahans()->exists()) {
            return redirect()->route('satuan.index')
                             ->with('error', 'Satuan "'.$satuan->nama_satuan.'" tidak dapat dihapus karena masih digunakan oleh data bahan.');
        }

        $satuan->delete();

        return redirect()->route('satuan.index')
                         ->with('success', 'Satuan berhasil dihapus.');
    }
}