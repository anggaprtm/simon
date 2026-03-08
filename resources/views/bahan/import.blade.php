{{-- resources/views/bahan/import.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Import Data Bahan') }}
        </h2>
    </x-slot>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
        .form-wrap * { font-family: 'Plus Jakarta Sans', sans-serif; }
        .form-label {
            display: block; font-size: 11px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.06em;
            color: #6b7280; margin-bottom: 5px;
        }
    </style>

    <div class="py-10 form-wrap">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-5">

            {{-- PAGE HEADER --}}
            <div class="flex items-center gap-3">
                <a href="{{ route('bahan.index') }}" class="w-9 h-9 rounded-xl bg-white border border-gray-200 shadow-sm flex items-center justify-center text-gray-400 hover:text-gray-700 hover:border-gray-300 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </a>
                <div>
                    <h1 class="text-2xl font-extrabold text-gray-800">Import Data Bahan</h1>
                    <p class="text-sm text-gray-400 mt-0.5">Unggah file Excel untuk menambahkan bahan secara massal</p>
                </div>
            </div>

            {{-- IMPORT ERRORS --}}
            @if(session('import_errors'))
            <div class="bg-red-50 border border-red-200 rounded-2xl p-5">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-lg bg-red-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </div>
                    <div class="flex-1">
                        <p class="font-bold text-red-800 text-sm">Terdapat error pada file yang diunggah:</p>
                        <ul class="mt-2 space-y-1">
                            @foreach(session('import_errors') as $error)
                            <li class="text-xs text-red-600 flex items-start gap-1.5">
                                <span class="mt-0.5 w-1.5 h-1.5 rounded-full bg-red-400 flex-shrink-0"></span>
                                {{ $error }}
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            @endif

            {{-- PETUNJUK --}}
            <div class="bg-indigo-50 border border-indigo-200 rounded-2xl p-5">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-lg bg-indigo-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-bold text-indigo-800 text-sm mb-2">Petunjuk Import</h3>
                        <ol class="space-y-1.5">
                            @foreach([
                                'Unduh template Excel yang sudah disediakan.',
                                'Isi data bahan sesuai kolom pada template.',
                                'Kolom <strong>kode_bahan</strong>, <strong>nama_bahan</strong>, <strong>nama_gudang</strong>, dan <strong>nama_satuan</strong> wajib diisi.',
                                'Pastikan <strong>nama_gudang</strong> yang dimasukkan sudah terdaftar di sistem.',
                                'Simpan file dalam format <strong>.xlsx</strong> atau <strong>.xls</strong>.',
                                'Unggah file melalui form di bawah ini.',
                            ] as $i => $step)
                            <li class="flex items-start gap-2 text-xs text-indigo-700">
                                <span class="w-4 h-4 rounded-full bg-indigo-200 text-indigo-700 text-[10px] font-bold flex items-center justify-center flex-shrink-0 mt-0.5">{{ $i + 1 }}</span>
                                <span>{!! $step !!}</span>
                            </li>
                            @endforeach
                        </ol>
                        <a href="{{ asset('templates/template_import_bahan.xlsx') }}" download
                           class="mt-4 inline-flex items-center gap-2 text-xs font-bold bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition-all hover:-translate-y-0.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Unduh Template Excel
                        </a>
                    </div>
                </div>
            </div>

            {{-- UPLOAD FORM --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                    <h3 class="font-bold text-gray-700 text-sm">Unggah File Excel</h3>
                </div>
                <form action="{{ route('bahan.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="p-6 md:p-8">
                        <label class="form-label">Pilih File Excel</label>

                        {{-- DRAG DROP AREA --}}
                        <label for="file"
                            class="flex flex-col items-center justify-center w-full h-36 border-2 border-dashed border-gray-300 rounded-xl bg-gray-50 hover:bg-indigo-50 hover:border-indigo-400 cursor-pointer transition-all group">
                            <div class="flex flex-col items-center gap-2 text-gray-400 group-hover:text-indigo-500 transition-colors">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                <p class="text-sm font-semibold">Klik untuk memilih file</p>
                                <p class="text-xs">Format: .xlsx atau .xls</p>
                            </div>
                            <input type="file" id="file" name="file" accept=".xlsx,.xls" class="hidden" required
                                onchange="document.getElementById('file-name').textContent = this.files[0]?.name ?? ''">
                        </label>
                        <p id="file-name" class="mt-2 text-xs text-indigo-600 font-semibold text-center"></p>

                        @error('file')<p class="mt-2 text-xs text-red-500 flex items-center gap-1"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01"/></svg>{{ $message }}</p>@enderror
                    </div>

                    {{-- FOOTER --}}
                    <div class="px-6 md:px-8 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-end gap-3">
                        <a href="{{ route('bahan.index') }}"
                           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-gray-600 bg-white border border-gray-200 hover:bg-gray-100 transition-all">
                            Batal
                        </a>
                        <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold shadow-sm transition-all hover:-translate-y-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                            Mulai Import
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>