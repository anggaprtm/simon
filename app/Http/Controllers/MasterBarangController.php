<?php

namespace App\Http\Controllers;

use App\Models\MasterBarang;
use App\Models\Satuan; // Import model Satuan
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MasterBarangController extends Controller
{
    public function index()
    {
        $masterBarangs = MasterBarang::with('satuan')->orderBy('nama_barang')->get();
        return view('master-barang.index', compact('masterBarangs'));
    }

    public function create()
    {
        $satuans = Satuan::orderBy('nama_satuan')->get();
        return view('master-barang.create', compact('satuans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_barang' => 'required|string|max:255|unique:master_barangs,nama_barang',
            'spesifikasi' => 'nullable|string',
            'id_satuan' => 'nullable|exists:satuans,id',
        ]);

        MasterBarang::create($request->all());

        return redirect()->route('master-barang.index')->with('success', 'Master barang berhasil ditambahkan.');
    }

    public function edit(MasterBarang $masterBarang)
    {
        $satuans = Satuan::orderBy('nama_satuan')->get();
        return view('master-barang.edit', compact('masterBarang', 'satuans'));
    }

    public function update(Request $request, MasterBarang $masterBarang)
    {
        $request->validate([
            'nama_barang' => ['required', 'string', 'max:255', Rule::unique('master_barangs')->ignore($masterBarang->id)],
            'spesifikasi' => 'nullable|string',
            'id_satuan' => 'nullable|exists:satuans,id',
        ]);

        $masterBarang->update($request->all());

        return redirect()->route('master-barang.index')->with('success', 'Master barang berhasil diperbarui.');
    }

    public function destroy(MasterBarang $masterBarang)
    {
        // TODO: Tambahkan pengecekan relasi ke detail_pengadaans saat sudah ada
        // if ($masterBarang->detailPengadaans()->exists()) {
        //     return redirect()->route('master-barang.index')->with('error', 'Master barang tidak dapat dihapus karena sudah digunakan dalam pengajuan.');
        // }

        $masterBarang->delete();

        return redirect()->route('master-barang.index')->with('success', 'Master barang berhasil dihapus.');
    }
}