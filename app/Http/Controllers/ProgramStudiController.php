<?php

namespace App\Http\Controllers;

use App\Models\ProgramStudi;
use Illuminate\Http\Request;

class ProgramStudiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $programStudis = ProgramStudi::orderBy('nama_program_studi', 'asc')->get();
        return view('program-studi.index', compact('programStudis'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('program-studi.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_program_studi' => 'required|string|max:255|unique:program_studis',
            'kode_program_studi' => 'nullable|string|max:50|unique:program_studis',
        ]);

        ProgramStudi::create($request->all());

        return redirect()->route('program-studi.index')
                         ->with('success', 'Program Studi berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     * (Tidak kita gunakan untuk CRUD sederhana, bisa dihapus)
     */
    public function show(ProgramStudi $programStudi)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProgramStudi $programStudi)
    {
        return view('program-studi.edit', compact('programStudi'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProgramStudi $programStudi)
    {
        $request->validate([
            'nama_program_studi' => 'required|string|max:255|unique:program_studis,nama_program_studi,' . $programStudi->id,
            'kode_program_studi' => 'nullable|string|max:50|unique:program_studis,kode_program_studi,' . $programStudi->id,
        ]);

        $programStudi->update($request->all());

        return redirect()->route('program-studi.index')
                         ->with('success', 'Program Studi berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProgramStudi $programStudi)
    {
        // Tambahan: Cek jika ada relasi yang menghalangi penghapusan
        if ($programStudi->users()->count() > 0 || $programStudi->bahans()->count() > 0) {
            return redirect()->route('program-studi.index')
                             ->with('error', 'Program Studi tidak dapat dihapus karena masih memiliki data user atau bahan terkait.');
        }

        $programStudi->delete();

        return redirect()->route('program-studi.index')
                         ->with('success', 'Program Studi berhasil dihapus.');
    }
}