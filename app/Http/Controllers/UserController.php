<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ProgramStudi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        // Ambil data user beserta relasi program studinya
        $users = User::with('programStudi')->latest()->get();
        return view('user.index', compact('users'));
    }

    public function create()
    {
        $programStudis = ProgramStudi::orderBy('nama_program_studi')->get();
        return view('user.create', compact('programStudis'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'name' => 'required|string|max:255|unique:users,name',
            'email' => 'required|string|email|max:255|unique:users,email',
            'nik' => 'nullable|string|max:50',
            'role' => 'required|in:laboran,fakultas,superadmin',
            'id_program_studi' => 'nullable|exists:program_studis,id',
            'password' => 'required|string|min:8',
        ]);

        User::create([
            'nama_lengkap' => $request->nama_lengkap,
            'name' => $request->name,
            'email' => $request->email,
            'nik' => $request->nik,
            'role' => $request->role,
            'id_program_studi' => $request->id_program_studi,
            'password' => Hash::make($request->password), // Password di-hash otomatis sebenarnya bisa karena modelmu pakai cast, tapi ini lebih eksplisit dan aman
        ]);

        return redirect()->route('user.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        $programStudis = ProgramStudi::orderBy('nama_program_studi')->get();
        return view('user.edit', compact('user', 'programStudis'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'name' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'nik' => 'nullable|string|max:50',
            'role' => 'required|in:laboran,fakultas,superadmin',
            'id_program_studi' => 'nullable|exists:program_studis,id',
            'password' => 'nullable|string|min:8', // Password opsional saat update
        ]);

        $data = [
            'nama_lengkap' => $request->nama_lengkap,
            'name' => $request->name,
            'email' => $request->email,
            'nik' => $request->nik,
            'role' => $request->role,
            'id_program_studi' => $request->id_program_studi,
        ];

        // Jika password diisi, maka update passwordnya
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('user.index')->with('success', 'Data user berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        // Proteksi agar user tidak menghapus akunnya sendiri
        if (auth()->id() === $user->id) {
            return redirect()->route('user.index')->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user->delete();
        return redirect()->route('user.index')->with('success', 'User berhasil dihapus.');
    }
}