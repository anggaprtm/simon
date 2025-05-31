<?php

namespace App\Http\Controllers;

use App\Models\Gudang;
use App\Models\ProgramStudi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class GudangController extends Controller
{
    public function index()
    {
        $this->authorize('view-any-gudang');

        $user = Auth::user();
        if ($user->role === 'laboran') {
            // Laboran melihat gudang umum dan gudang prodinya
            $gudangs = Gudang::whereNull('id_program_studi')
                             ->orWhere('id_program_studi', $user->id_program_studi)
                             ->with('programStudi')
                             ->orderBy('nama_gudang')
                             ->get();
        } else {
            // Superadmin dan Fakultas melihat semua gudang
            $gudangs = Gudang::with('programStudi')->orderBy('nama_gudang')->get();
        }

        return view('gudang.index', compact('gudangs'));
    }

    public function create()
    {
        $this->authorize('create-gudang');
        
        $programStudis = [];
        if (Auth::user()->role === 'superadmin') {
            $programStudis = ProgramStudi::orderBy('nama_program_studi')->get();
        }

        return view('gudang.create', compact('programStudis'));
    }

    public function store(Request $request)
    {
        $this->authorize('create-gudang');

        $request->validate([
            'nama_gudang' => 'required|string|max:255',
            'lokasi' => 'required|string|max:255',
            'id_program_studi' => 'nullable|exists:program_studis,id',
        ]);

        $data = $request->all();
        // Jika laboran yang membuat, paksa id_program_studi sesuai dengan prodinya
        if (Auth::user()->role === 'laboran') {
            $data['id_program_studi'] = Auth::user()->id_program_studi;
        }

        Gudang::create($data);

        return redirect()->route('gudang.index')->with('success', 'Gudang berhasil ditambahkan.');
    }

    public function edit(Gudang $gudang)
    {
        $this->authorize('update-gudang', $gudang);
        
        $programStudis = [];
        if (Auth::user()->role === 'superadmin') {
            $programStudis = ProgramStudi::orderBy('nama_program_studi')->get();
        }

        return view('gudang.edit', compact('gudang', 'programStudis'));
    }

    public function update(Request $request, Gudang $gudang)
    {
        $this->authorize('update-gudang', $gudang);

        $request->validate([
            'nama_gudang' => 'required|string|max:255',
            'lokasi' => 'required|string|max:255',
            'id_program_studi' => 'nullable|exists:program_studis,id',
        ]);
        
        $data = $request->all();
        // Cegah laboran memindahkan gudang ke prodi lain
        if (Auth::user()->role === 'laboran') {
            $data['id_program_studi'] = Auth::user()->id_program_studi;
        }

        $gudang->update($data);

        return redirect()->route('gudang.index')->with('success', 'Gudang berhasil diperbarui.');
    }

    public function destroy(Gudang $gudang)
    {
        $this->authorize('delete-gudang', $gudang);

        if ($gudang->bahans()->exists()) {
            return redirect()->route('gudang.index')
                             ->with('error', 'Gudang tidak dapat dihapus karena masih ada bahan yang tersimpan di dalamnya.');
        }

        $gudang->delete();

        return redirect()->route('gudang.index')->with('success', 'Gudang berhasil dihapus.');
    }
}