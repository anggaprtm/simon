{{-- resources/views/gudang/create.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Gudang Baru') }}
        </h2>
    </x-slot>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
        .form-wrap * { font-family: 'Plus Jakarta Sans', sans-serif; }

        .form-label {
            display: block;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #6b7280;
            margin-bottom: 6px;
        }
        .form-input {
            display: block;
            width: 100%;
            border: 1.5px solid #e5e7eb;
            border-radius: 9px;
            padding: 10px 14px;
            font-size: 14px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #1f2937;
            background: #fff;
            outline: none;
            transition: border-color 0.15s, box-shadow 0.15s;
        }
        .form-input:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99,102,241,0.08);
        }
        .form-input::placeholder { color: #d1d5db; }
    </style>

    <div class="py-10 form-wrap">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8 space-y-5">

            {{-- PAGE HEADER --}}
            <div class="flex items-center gap-3">
                <a href="{{ route('gudang.index') }}" class="w-9 h-9 rounded-xl bg-white border border-gray-200 shadow-sm flex items-center justify-center text-gray-400 hover:text-gray-700 hover:border-gray-300 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </a>
                <div>
                    <h1 class="text-2xl font-extrabold text-gray-800">Tambah Gudang Baru</h1>
                    <p class="text-sm text-gray-400 mt-0.5">Daftarkan lokasi penyimpanan bahan baru</p>
                </div>
            </div>

            {{-- FORM CARD --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <form action="{{ route('gudang.store') }}" method="POST">
                    @csrf

                    <div class="p-6 md:p-8 space-y-5">

                        {{-- Nama Gudang --}}
                        <div>
                            <label for="nama_gudang" class="form-label">Nama Gudang</label>
                            <input id="nama_gudang" type="text" name="nama_gudang"
                                   value="{{ old('nama_gudang') }}"
                                   placeholder="Contoh: Gudang Kimia Dasar A"
                                   class="form-input" required autofocus>
                            @error('nama_gudang')
                            <p class="mt-1.5 text-xs text-red-500 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01"/></svg>
                                {{ $message }}
                            </p>
                            @enderror
                        </div>

                        {{-- Lokasi --}}
                        <div>
                            <label for="lokasi" class="form-label">Lokasi Gudang</label>
                            <input id="lokasi" type="text" name="lokasi"
                                   value="{{ old('lokasi') }}"
                                   placeholder="Contoh: Gedung B Lantai 2, Ruang 204"
                                   class="form-input" required>
                            @error('lokasi')
                            <p class="mt-1.5 text-xs text-red-500 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01"/></svg>
                                {{ $message }}
                            </p>
                            @enderror
                        </div>

                        {{-- Program Studi (Superadmin only) --}}
                        @if (Auth::user()->role == 'superadmin')
                        <div>
                            <label for="id_program_studi" class="form-label">Milik Program Studi</label>
                            <select id="id_program_studi" name="id_program_studi" class="form-input">
                                <option value="">— Umum / Fakultas —</option>
                                @foreach ($programStudis as $prodi)
                                    <option value="{{ $prodi->id }}" {{ old('id_program_studi') == $prodi->id ? 'selected' : '' }}>
                                        {{ $prodi->nama_program_studi }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_program_studi')
                            <p class="mt-1.5 text-xs text-red-500 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01"/></svg>
                                {{ $message }}
                            </p>
                            @enderror
                        </div>
                        @endif

                    </div>

                    {{-- FOOTER ACTIONS --}}
                    <div class="px-6 md:px-8 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-end gap-3">
                        <a href="{{ route('gudang.index') }}"
                           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-gray-600 bg-white border border-gray-200 hover:bg-gray-100 transition-all">
                            Batal
                        </a>
                        <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold shadow-sm transition-all hover:-translate-y-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Simpan Gudang
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>