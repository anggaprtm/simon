{{-- resources/views/bahan/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Bahan Habis Pakai') }}
        </h2>
    </x-slot>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
        .bahan-wrap * { font-family: 'Plus Jakarta Sans', sans-serif; }

        .action-btn { display: inline-flex; align-items: center; justify-content: center; width: 30px; height: 30px; border-radius: 7px; transition: all 0.15s ease; }
        .action-btn:hover { transform: scale(1.12); }
        .action-btn svg { width: 15px; height: 15px; }

        .th-sort { display: flex; align-items: center; gap: 4px; cursor: pointer; user-select: none; }
        .th-sort:hover { color: #4F46E5; }

        .row-kritis { background: #fff5f5; }
        .row-kritis:hover { background: #fee2e2 !important; }
        .row-normal:nth-child(odd) { background: #fafafa; }
        .row-normal:hover { background: #f0f4ff !important; }

        .badge-jenis { display: inline-block; padding: 2px 10px; border-radius: 99px; font-size: 11px; font-weight: 600; background: #e0e7ff; color: #3730a3; }

        .stock-badge { display: inline-flex; align-items: center; gap: 4px; padding: 3px 10px; border-radius: 99px; font-size: 12px; font-weight: 700; }
        .stock-ok   { background: #dcfce7; color: #15803d; }
        .stock-warn { background: #fee2e2; color: #b91c1c; }

        .table-container { border-radius: 14px; border: 1px solid #e5e7eb; overflow: hidden; }

        thead th { background: #f8fafc; border-bottom: 1px solid #e5e7eb; padding: 11px 16px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: #6b7280; white-space: nowrap; }
        tbody td { padding: 13px 16px; font-size: 13.5px; color: #374151; vertical-align: middle; }
        tbody tr { transition: background 0.12s; }
        tbody tr + tr { border-top: 1px solid #f0f0f0; }

        .search-input { border: 1.5px solid #e5e7eb; border-radius: 9px; padding: 9px 14px; font-size: 13.5px; width: 100%; outline: none; transition: border-color 0.15s; font-family: 'Plus Jakarta Sans', sans-serif; }
        .search-input:focus { border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,0.08); }

        .select-input { border: 1.5px solid #e5e7eb; border-radius: 9px; padding: 9px 14px; font-size: 13.5px; width: 100%; outline: none; background: white; transition: border-color 0.15s; }
        .select-input:focus { border-color: #6366f1; }

        .top-btn { display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; border-radius: 9px; font-size: 13px; font-weight: 600; transition: all 0.15s; white-space: nowrap; }
        .top-btn:hover { transform: translateY(-1px); filter: brightness(1.05); }

        /* Styling Highlight Pencarian */
        mark.highlight {
            background-color: #fef08a; /* Kuning Tailwind */
            color: #854d0e;
            padding: 0 2px;
            border-radius: 4px;
            font-weight: 700;
        }

        /* Animasi Transisi Seamless */
        .loading-state {
            opacity: 0.5;
            pointer-events: none;
            filter: grayscale(20%);
        }
    </style>

    <div class="py-10 bahan-wrap">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8 space-y-5">

            {{-- PAGE HEADER ROW --}}
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-extrabold text-gray-800">Daftar Bahan Habis Pakai</h1>
                    <p class="text-sm text-gray-400 mt-0.5">Kelola inventaris bahan kimia dan laboratorium</p>
                </div>
                @can('create-bahan')
                <div class="flex items-center gap-2 flex-wrap">
                    <button type="button" id="bulk-delete-button"
                        class="top-btn bg-red-100 hover:bg-red-200 text-red-700"
                        style="display:none;">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        Hapus Terpilih (<span id="selected-count">0</span>)
                    </button>
                    <a href="{{ route('bahan.showImportForm') }}" class="top-btn bg-emerald-100 hover:bg-emerald-200 text-emerald-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                        Import Bahan
                    </a>
                    <a href="{{ route('bahan.create') }}" class="top-btn bg-indigo-600 hover:bg-indigo-700 text-white shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Tambah Bahan
                    </a>
                </div>
                @endcan
            </div>

            {{-- SESSION ALERTS --}}
            @if (session('success'))
            <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl text-sm font-medium">
                <svg class="w-4 h-4 flex-shrink-0 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                {{ session('success') }}
            </div>
            @endif
            @if (session('error'))
            <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl text-sm font-medium">
                <svg class="w-4 h-4 flex-shrink-0 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                {{ session('error') }}
            </div>
            @endif

            {{-- SEARCH & FILTER CARD --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <form id="search-form" method="GET" action="{{ route('bahan.index') }}">
                    <input type="hidden" name="sort_by" value="{{ $sortBy }}">
                    <input type="hidden" name="direction" value="{{ $direction }}">

                    @if(in_array(Auth::user()->role, ['superadmin', 'fakultas']))
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                        <div class="md:col-span-3">
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Filter Unit/Prodi</label>
                            <select name="prodi_id" class="select-input auto-submit">
                                <option value="">— Semua Unit —</option>
                                @foreach($programStudis as $prodi)
                                    <option value="{{ $prodi->id }}" {{ $selectedProdiId == $prodi->id ? 'selected' : '' }}>
                                        {{ $prodi->nama_program_studi }}
                                    </option> 
                                @endforeach
                            </select>
                        </div>
                        <div class="md:col-span-6">
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Cari Bahan</label>
                            <div class="relative">
                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
                                <input type="text" id="search-input" name="search" value="{{ $search ?? '' }}" placeholder="Ketik Kode, nama, merk, jenis..." class="search-input !pl-10" autocomplete="off">
                            </div>
                        </div>
                        <div class="md:col-span-3 flex gap-2">
                            <a href="{{ route('bahan.index') }}" class="top-btn bg-gray-100 text-gray-600 justify-center w-full">Reset Pencarian</a>
                        </div>
                    </div>
                    @else
                    <div class="flex flex-col md:flex-row gap-3 items-end">
                        <div class="flex-grow">
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Pencarian Cepat</label>
                            <div class="relative w-full">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/>
                                    </svg>
                                </div>
                                <input type="text" id="search-input" name="search" value="{{ $search ?? '' }}" placeholder="Mulai mengetik kode, nama, merk, atau jenis bahan..." class="block w-full !pl-10 pr-3 py-2 border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" autocomplete="off">
                            </div>
                        </div>
                        <div class="flex gap-2 shrink-0">
                            <a href="{{ route('bahan.index') }}" class="top-btn bg-gray-100 text-gray-600">Reset</a>
                        </div>
                    </div>
                    @endif
                </form>
            </div>

            {{-- WRAPPER UNTUK SEAMLESS AJAX --}}
            <div id="data-wrapper" class="transition-all duration-300 ease-in-out">
                
                {{-- TABLE CARD --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-5">
                    <div class="table-container border-0 rounded-none">
                        <div class="overflow-x-auto">
                            <table class="min-w-full">
                                <thead>
                                    <tr>
                                        <th class="w-10 text-center">
                                            <input type="checkbox" id="select-all-checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                        </th>
                                        <th>
                                            <a href="{{ route('bahan.index', array_merge(request()->query(), ['sort_by' => 'kode_bahan', 'direction' => ($sortBy == 'kode_bahan' && $direction == 'asc') ? 'desc' : 'asc'])) }}" class="th-sort">
                                                Kode
                                                @if($sortBy == 'kode_bahan')<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="{{ $direction == 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"/></svg>@endif
                                            </a>
                                        </th>
                                        <th>
                                            <a href="{{ route('bahan.index', array_merge(request()->query(), ['sort_by' => 'nama_bahan', 'direction' => ($sortBy == 'nama_bahan' && $direction == 'asc') ? 'desc' : 'asc'])) }}" class="th-sort">
                                                Nama Bahan
                                                @if($sortBy == 'nama_bahan')<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="{{ $direction == 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"/></svg>@endif
                                            </a>
                                        </th>
                                        <th>
                                            <a href="{{ route('bahan.index', array_merge(request()->query(), ['sort_by' => 'jenis_bahan', 'direction' => ($sortBy == 'jenis_bahan' && $direction == 'asc') ? 'desc' : 'asc'])) }}" class="th-sort">
                                                Jenis
                                                @if($sortBy == 'jenis_bahan')<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="{{ $direction == 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"/></svg>@endif
                                            </a>
                                        </th>
                                        <th>
                                            <a href="{{ route('bahan.index', array_merge(request()->query(), ['sort_by' => 'jumlah_stock', 'direction' => ($sortBy == 'jumlah_stock' && $direction == 'asc') ? 'desc' : 'asc'])) }}" class="th-sort">
                                                Stok
                                                @if($sortBy == 'jumlah_stock')<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="{{ $direction == 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"/></svg>@endif
                                            </a>
                                        </th>
                                        <th>Gudang</th>
                                        <th>Unit</th>
                                        <th>
                                            <a href="{{ route('bahan.index', array_merge(request()->query(), ['sort_by' => 'tanggal_kedaluwarsa', 'direction' => ($sortBy == 'tanggal_kedaluwarsa' && $direction == 'asc') ? 'desc' : 'asc'])) }}" class="th-sort">
                                                Kedaluwarsa
                                                @if($sortBy == 'tanggal_kedaluwarsa')<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="{{ $direction == 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"/></svg>@endif
                                            </a>
                                        </th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($bahans as $bahan)
                                    @php $isKritis = $bahan->jumlah_stock <= $bahan->minimum_stock && $bahan->jumlah_stock > 0; @endphp
                                    <tr class="{{ $isKritis ? 'row-kritis' : 'row-normal' }}">
                                        <td class="text-center">
                                            <input type="checkbox" name="selected_bahan[]" class="row-checkbox rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" value="{{ $bahan->id }}">
                                        </td>
                                        <td>
                                            <span class="text-xs font-mono font-semibold bg-gray-100 text-gray-600 px-2 py-1 rounded-md">{{ $bahan->kode_bahan }}</span>
                                        </td>
                                        <td>
                                            <div class="font-semibold text-gray-800 text-sm leading-tight highlightable">{!! $bahan->nama_bahan_html !!}</div>
                                            <div class="text-xs text-gray-400 mt-0.5 highlightable">{{ $bahan->merk }}</div>
                                        </td>
                                        <td>
                                            @if($bahan->jenis_bahan)
                                            <span class="badge-jenis highlightable">{{ $bahan->jenis_bahan }}</span>
                                            @else
                                            <span class="text-gray-300">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="stock-badge {{ $isKritis ? 'stock-warn' : 'stock-ok' }}">
                                                @if($isKritis)
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01"/></svg>
                                                @endif
                                                {{ $bahan->formatted_stock }}
                                            </span>
                                        </td>
                                        <td class="text-gray-500 text-xs text-center">{{ $bahan->gudang->nama_gudang }}</td>
                                        <td class="text-center">
                                            <span class="text-xs font-semibold bg-indigo-50 text-indigo-600 px-2 py-1 rounded-md">{{ $bahan->programStudi->kode_program_studi }}</span>
                                        </td>
                                        <td>
                                            @if($bahan->tanggal_kedaluwarsa)
                                            @php
                                                $exp = \Carbon\Carbon::parse($bahan->tanggal_kedaluwarsa);
                                                $soon = $exp->diffInDays(now()) <= 60 && $exp->isFuture();
                                                $past = $exp->isPast();
                                            @endphp
                                            <span class="text-xs font-semibold {{ $past ? 'text-red-600' : ($soon ? 'text-amber-600' : 'text-gray-500') }}">
                                                {{ $exp->format('d M Y') }}
                                            </span>
                                            @if($soon && !$past)
                                            <div class="text-xs text-amber-500 mt-0.5">{{ $exp->diffForHumans() }}</div>
                                            @elseif($past)
                                            <div class="text-xs text-red-500 mt-0.5">Sudah kedaluwarsa</div>
                                            @endif
                                            @else
                                            <span class="text-gray-300 text-xs">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="flex items-center justify-center gap-1">
                                                @can('update-bahan', $bahan)
                                                <a href="{{ route('transaksi.createMasuk', $bahan->id) }}" title="Stok Masuk" class="action-btn bg-emerald-50 text-emerald-600 hover:bg-emerald-100">
                                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                                                </a>
                                                <a href="{{ route('transaksi.createKeluar', $bahan->id) }}" title="Stok Keluar" class="action-btn bg-red-50 text-red-500 hover:bg-red-100">
                                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                                                </a>
                                                <a href="{{ route('penyesuaian.create', $bahan->id) }}" title="Penyesuaian" class="action-btn bg-orange-50 text-orange-500 hover:bg-orange-100">
                                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75"/></svg>
                                                </a>
                                                @endcan

                                                <a href="{{ route('transaksi.history', $bahan->id) }}" title="Riwayat" class="action-btn bg-blue-50 text-blue-500 hover:bg-blue-100">
                                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25"/></svg>
                                                </a>

                                                @can('update-bahan', $bahan)
                                                <a href="{{ route('bahan.edit', $bahan->id) }}" title="Edit" class="action-btn bg-indigo-50 text-indigo-500 hover:bg-indigo-100">
                                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125"/></svg>
                                                </a>
                                                @endcan

                                                @can('delete-bahan', $bahan)
                                                <form action="{{ route('bahan.destroy', $bahan->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus bahan ini?');" class="inline">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" title="Hapus" class="action-btn bg-red-50 text-red-500 hover:bg-red-100">
                                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12.56 0c.342.052.682.107 1.022.166m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                                                    </button>
                                                </form>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="9" class="py-16 text-center">
                                            <div class="flex flex-col items-center gap-2 text-gray-400">
                                                <svg class="w-10 h-10 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                                                <p class="text-sm font-medium">Data bahan tidak ditemukan</p>
                                                <p class="text-xs">Coba ubah kata kunci pencarian atau reset filter</p>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- PAGINATION --}}
                    <div class="px-5 py-4 border-t border-gray-100 bg-white">
                        {{ $bahans->appends(request()->query())->links() }}
                    </div>
                </div>
            </div> {{-- Akhir dari wrapper seamless --}}
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // === 1. LOGIC SEAMLESS SEARCH & HIGHLIGHT ===
            const searchForm = document.getElementById('search-form');
            const searchInput = document.getElementById('search-input');
            const dataWrapper = document.getElementById('data-wrapper');
            const autoSubmitSelects = document.querySelectorAll('.auto-submit');
            let typingTimer;

            // Fungsi ambil data via AJAX
            function performSearch() {
                const formData = new FormData(searchForm);
                const params = new URLSearchParams(formData).toString();
                const url = '{{ route("bahan.index") }}?' + params;

                // Visual efek loading ringan
                dataWrapper.classList.add('loading-state');

                fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(response => response.text())
                .then(html => {
                    // Parse HTML balikan dari server
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    
                    // Ganti isi tabel & pagination yang lama dengan yang baru
                    const newDataWrapper = doc.getElementById('data-wrapper');
                    if (newDataWrapper) {
                        dataWrapper.innerHTML = newDataWrapper.innerHTML;
                    }
                    
                    // Selesai loading
                    dataWrapper.classList.remove('loading-state');
                    
                    // Ubah URL di atas browser (supaya kalau di-refresh tetap jalan)
                    window.history.pushState({}, '', url);

                    // Jalankan ulang fungsi highlight & checkbox (karena DOM baru diganti)
                    highlightKeyword(searchInput.value);
                    bindCheckboxes();
                })
                .catch(error => {
                    console.error('Error fetching data:', error);
                    dataWrapper.classList.remove('loading-state');
                });
            }

            // Memicu pencarian tiap kali user mengetik (jeda 500ms)
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    clearTimeout(typingTimer);
                    typingTimer = setTimeout(performSearch, 500);
                });
            }

            // Jika Dropdown Filter Prodi diubah (Khusus Superadmin) langsung cari
            autoSubmitSelects.forEach(select => {
                select.addEventListener('change', performSearch);
            });

            // Cegah form reload saat tekan Enter di kotak pencarian
            searchForm.addEventListener('submit', function(e) {
                e.preventDefault();
                clearTimeout(typingTimer);
                performSearch();
            });

            // === FUNGSI HIGHLIGHT TEKS (AMAN UNTUK HTML SUB) ===
            function highlightKeyword(keyword) {
                if (!keyword || keyword.trim() === '') return;
                
                const tableBody = document.querySelector('#data-wrapper tbody');
                if (!tableBody) return;

                // Escape karakter aneh agar regex tidak error
                const escapedKeyword = keyword.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
                const regex = new RegExp(`(${escapedKeyword})`, 'gi');

                // Menggunakan TreeWalker untuk HANYA menyeleksi teks murni, bukan menyeleksi tag HTML seperti <sub>
                const walker = document.createTreeWalker(tableBody, NodeFilter.SHOW_TEXT, null, false);
                const nodesToReplace = [];

                let node;
                while (node = walker.nextNode()) {
                    // Jangan sorot teks yang sudah berada di dalam tag <mark> dan pastikan cocok dengan keyword
                    if (node.parentNode.nodeName !== 'MARK' && node.nodeValue.match(regex)) {
                        nodesToReplace.push(node);
                    }
                }

                // Ganti teks dengan tambahan span + mark HTML
                nodesToReplace.forEach(n => {
                    const span = document.createElement('span');
                    span.innerHTML = n.nodeValue.replace(regex, '<mark class="highlight">$1</mark>');
                    while (span.firstChild) {
                        n.parentNode.insertBefore(span.firstChild, n);
                    }
                    n.parentNode.removeChild(n);
                });
            }

            // Panggil highlight pertama kali halaman diload (jika ada sisa pencarian di URL)
            if (searchInput && searchInput.value) {
                highlightKeyword(searchInput.value);
            }

            // === 2. LOGIC CHECKBOX BULK DELETE ===
            function bindCheckboxes() {
                const selectAllCheckbox = document.getElementById('select-all-checkbox');
                const rowCheckboxes = document.querySelectorAll('.row-checkbox');
                const bulkDeleteButton = document.getElementById('bulk-delete-button');
                const selectedCountSpan = document.getElementById('selected-count');

                function updateBulkDeleteButtonState() {
                    const selectedRows = document.querySelectorAll('.row-checkbox:checked');
                    if (selectedCountSpan) selectedCountSpan.textContent = selectedRows.length;
                    if (bulkDeleteButton) bulkDeleteButton.style.display = selectedRows.length > 0 ? 'inline-flex' : 'none';
                }

                if (selectAllCheckbox) {
                    selectAllCheckbox.addEventListener('change', function () {
                        rowCheckboxes.forEach(cb => { cb.checked = this.checked; });
                        updateBulkDeleteButtonState();
                    });
                }

                rowCheckboxes.forEach(checkbox => {
                    checkbox.addEventListener('change', function () {
                        if (!this.checked) { 
                            if(selectAllCheckbox) selectAllCheckbox.checked = false; 
                        } else {
                            const allChecked = Array.from(rowCheckboxes).every(cb => cb.checked);
                            if (allChecked && selectAllCheckbox) selectAllCheckbox.checked = true;
                        }
                        updateBulkDeleteButtonState();
                    });
                });

                // Hapus Event Listener lama agar tidak ganda saat AJAX
                if (bulkDeleteButton) {
                    const newBtn = bulkDeleteButton.cloneNode(true);
                    bulkDeleteButton.parentNode.replaceChild(newBtn, bulkDeleteButton);
                    
                    newBtn.addEventListener('click', function () {
                        const selectedIds = Array.from(document.querySelectorAll('.row-checkbox:checked')).map(cb => cb.value);
                        if (selectedIds.length === 0) { alert('Pilih setidaknya satu bahan.'); return; }
                        if (confirm(`Hapus ${selectedIds.length} bahan yang dipilih?`)) {
                            const form = document.createElement('form');
                            form.method = 'POST';
                            form.action = '{{ route("bahan.bulkDelete") }}';
                            const csrf = document.createElement('input');
                            csrf.type = 'hidden'; csrf.name = '_token'; csrf.value = '{{ csrf_token() }}';
                            form.appendChild(csrf);
                            selectedIds.forEach(id => {
                                const inp = document.createElement('input');
                                inp.type = 'hidden'; inp.name = 'ids[]'; inp.value = id;
                                form.appendChild(inp);
                            });
                            document.body.appendChild(form);
                            form.submit();
                        }
                    });
                }
            }

            // Jalankan bindCheckbox pada render pertama
            bindCheckboxes();
        });
    </script>
    @endpush
</x-app-layout>