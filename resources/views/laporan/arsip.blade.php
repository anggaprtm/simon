{{-- resources/views/laporan/arsip.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Arsip Laporan Bulanan') }}</h2>
    </x-slot>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
        .lp-wrap * { font-family: 'Plus Jakarta Sans', sans-serif; }
        .form-label { display:block; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:#6b7280; margin-bottom:5px; }
        .form-select {
            display:block; width:100%; border:1.5px solid #e5e7eb; border-radius:9px;
            padding:9px 13px; font-size:13px; font-family:'Plus Jakarta Sans',sans-serif;
            color:#1f2937; background:#fff; outline:none; transition:border-color .15s,box-shadow .15s;
        }
        .form-select:focus { border-color:#6366f1; box-shadow:0 0 0 3px rgba(99,102,241,.08); }
        .top-btn { display:inline-flex; align-items:center; gap:6px; padding:9px 16px; border-radius:9px; font-size:13px; font-weight:600; transition:all .15s; white-space:nowrap; }
        .top-btn:hover { transform:translateY(-1px); }
        thead th { background:#f8fafc; padding:11px 16px; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:#6b7280; white-space:nowrap; border-bottom:1px solid #e5e7eb; }
        tbody td { padding:13px 16px; font-size:13px; color:#374151; vertical-align:middle; }
        tbody tr+tr { border-top:1px solid #f0f0f0; }
        tbody tr:nth-child(odd) { background:#fafafa; }
        tbody tr:hover { background:#f0f4ff !important; transition:background .1s; }
    </style>

    <div class="py-10 lp-wrap">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- PAGE HEADER --}}
            <div class="flex items-center gap-3">
                <a href="{{ route('laporan.index') }}" class="w-9 h-9 rounded-xl bg-white border border-gray-200 shadow-sm flex items-center justify-center text-gray-400 hover:text-gray-700 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </a>
                <div>
                    <h1 class="text-2xl font-extrabold text-gray-800">Arsip Laporan Bulanan</h1>
                    <p class="text-sm text-gray-400 mt-0.5">Unggah dan kelola arsip laporan PDF bertandatangan</p>
                </div>
            </div>

            {{-- ALERTS --}}
            @if(session('success'))
            <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl text-sm font-medium">
                <svg class="w-4 h-4 flex-shrink-0 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                {{ session('success') }}
            </div>
            @endif
            @if(session('error'))
            <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl text-sm font-medium">
                <svg class="w-4 h-4 flex-shrink-0 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                {{ session('error') }}
            </div>
            @endif

            {{-- FORM UNGGAH --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-indigo-50 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-indigo-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-indigo-900 text-sm">Unggah Arsip Laporan Final</h3>
                        <p class="text-xs text-indigo-600 mt-0.5">Jika laporan bulan & tahun yang sama sudah ada, file lama akan otomatis tergantikan.</p>
                    </div>
                </div>
                <form action="{{ route('laporan.arsip.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="p-6 md:p-7">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 items-end">

                            @if(in_array(Auth::user()->role, ['superadmin', 'fakultas']))
                            <div>
                                <label class="form-label">Program Studi</label>
                                <select name="id_program_studi" class="form-select" required>
                                    <option value="">— Pilih Prodi —</option>
                                    @foreach($programStudis as $prodi)
                                        <option value="{{ $prodi->id }}">{{ $prodi->nama_program_studi }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @endif

                            <div>
                                <label class="form-label">Jenis Laporan</label>
                                <select name="jenis_laporan" class="form-select" required>
                                    <option value="stok">Laporan Stok</option>
                                    <option value="transaksi">Laporan Transaksi</option>
                                </select>
                            </div>

                            <div>
                                <label class="form-label">Bulan</label>
                                <select name="bulan" class="form-select" required>
                                    @php $namaBulan = ['1'=>'Januari','2'=>'Februari','3'=>'Maret','4'=>'April','5'=>'Mei','6'=>'Juni','7'=>'Juli','8'=>'Agustus','9'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember']; @endphp
                                    @foreach($namaBulan as $num => $nama)
                                        <option value="{{ $num }}" {{ date('n') == $num ? 'selected' : '' }}>{{ $nama }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="form-label">Tahun</label>
                                <select name="tahun" class="form-select" required>
                                    @foreach($years as $year)
                                        <option value="{{ $year }}" {{ date('Y') == $year ? 'selected' : '' }}>{{ $year }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="{{ in_array(Auth::user()->role, ['superadmin', 'fakultas']) ? 'lg:col-span-1' : 'lg:col-span-1' }}">
                                <label class="form-label">File PDF <span class="normal-case font-normal text-gray-400">(Maks. 5MB)</span></label>
                                <label for="file_laporan" class="flex items-center gap-2 border-2 border-dashed border-gray-300 rounded-xl px-3 py-2.5 cursor-pointer hover:border-indigo-400 hover:bg-indigo-50 transition-all group">
                                    <svg class="w-4 h-4 text-gray-400 group-hover:text-indigo-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                    <span id="file-label-text" class="text-xs text-gray-400 group-hover:text-indigo-600 truncate">Pilih file PDF...</span>
                                    <input type="file" id="file_laporan" name="file_laporan" accept=".pdf" class="hidden" required
                                        onchange="document.getElementById('file-label-text').textContent = this.files[0]?.name ?? 'Pilih file PDF...'">
                                </label>
                                @error('file_laporan')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                            </div>

                        </div>
                    </div>
                    <div class="px-6 md:px-7 py-4 bg-gray-50 border-t border-gray-100 flex justify-end">
                        <button type="submit" class="top-btn bg-indigo-600 hover:bg-indigo-700 text-white shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Simpan Arsip
                        </button>
                    </div>
                </form>
            </div>

            {{-- DAFTAR ARSIP --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex flex-col md:flex-row md:items-center justify-between gap-3">
                    <h3 class="font-bold text-gray-800 text-sm">Daftar Arsip Laporan</h3>
                    <form method="GET" action="{{ route('laporan.arsip') }}" class="flex items-center gap-2">
                        @if(in_array(Auth::user()->role, ['superadmin', 'fakultas']))
                        <select name="prodi_id" style="border:1.5px solid #e5e7eb; border-radius:8px; padding:6px 12px; font-size:12px; font-family:'Plus Jakarta Sans',sans-serif; outline:none; background:#fff; color:#374151;">
                            <option value="">Semua Prodi</option>
                            @foreach($programStudis as $prodi)
                                <option value="{{ $prodi->id }}" {{ request('prodi_id') == $prodi->id ? 'selected' : '' }}>{{ $prodi->kode_program_studi }}</option>
                            @endforeach
                        </select>
                        @endif
                        <select name="tahun" style="border:1.5px solid #e5e7eb; border-radius:8px; padding:6px 12px; font-size:12px; font-family:'Plus Jakarta Sans',sans-serif; outline:none; background:#fff; color:#374151;">
                            @foreach($years as $year)
                                <option value="{{ $year }}" {{ $selectedTahun == $year ? 'selected' : '' }}>Tahun {{ $year }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="top-btn bg-gray-800 hover:bg-gray-900 text-white" style="padding:6px 14px; font-size:12px;">
                            Filter
                        </button>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr>
                                <th style="text-align:left">Bulan & Tahun</th>
                                <th style="text-align:left">Jenis Laporan</th>
                                <th style="text-align:left">Program Studi</th>
                                <th style="text-align:left">Diunggah Oleh</th>
                                <th style="text-align:left">Waktu Unggah</th>
                                <th style="text-align:center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($arsips as $arsip)
                            <tr>
                                <td class="font-bold text-gray-800 whitespace-nowrap">{{ $namaBulan[$arsip->bulan] }} {{ $arsip->tahun }}</td>
                                <td>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold
                                        {{ $arsip->jenis_laporan === 'stok' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700' }}">
                                        Lap. {{ ucfirst($arsip->jenis_laporan) }}
                                    </span>
                                </td>
                                <td class="text-gray-500">{{ $arsip->programStudi->nama_program_studi ?? '—' }}</td>
                                <td class="text-gray-500">{{ $arsip->user->name ?? '—' }}</td>
                                <td>
                                    <p class="font-medium text-gray-700 text-xs">{{ $arsip->updated_at->format('d M Y') }}</p>
                                    <p class="text-gray-400 text-xs">{{ $arsip->updated_at->format('H:i') }}</p>
                                </td>
                                <td style="text-align:center">
                                    <a href="{{ asset('storage/' . $arsip->file_path) }}" target="_blank"
                                       class="inline-flex items-center gap-1.5 text-xs font-bold text-indigo-600 hover:text-indigo-800 bg-indigo-50 hover:bg-indigo-100 px-3 py-1.5 rounded-lg transition-all">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        Lihat PDF
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="py-14 text-center">
                                    <div class="flex flex-col items-center gap-2 text-gray-400">
                                        <svg class="w-9 h-9 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        <p class="text-sm font-medium">Belum ada arsip untuk tahun {{ $selectedTahun }}</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>