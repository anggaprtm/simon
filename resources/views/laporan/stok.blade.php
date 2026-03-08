{{-- resources/views/laporan/stok.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Laporan Stok Bahan') }}</h2>
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
        thead th { background:#f8fafc; padding:11px 16px; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:#6b7280; white-space:nowrap; border-bottom:1px solid #e5e7eb; text-align:center; }
        thead th:nth-child(2) { text-align:left; }
        tbody td { padding:12px 16px; font-size:13px; color:#374151; vertical-align:middle; text-align:center; }
        tbody td:nth-child(2) { text-align:left; }
        tbody tr+tr { border-top:1px solid #f0f0f0; }
        tbody tr:nth-child(odd) { background:#fafafa; }
        tbody tr:hover { background:#f0f4ff !important; transition:background .1s; }
    </style>

    <div class="py-10 lp-wrap">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8 space-y-5">

            {{-- PAGE HEADER --}}
            <div class="flex items-center gap-3">
                <a href="{{ route('laporan.index') }}" class="w-9 h-9 rounded-xl bg-white border border-gray-200 shadow-sm flex items-center justify-center text-gray-400 hover:text-gray-700 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </a>
                <div>
                    <h1 class="text-2xl font-extrabold text-gray-800">Laporan Stok Bahan</h1>
                    <p class="text-sm text-gray-400 mt-0.5">Rekapitulasi stok awal & akhir per periode</p>
                </div>
            </div>

            {{-- FILTER --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <form method="GET" action="{{ route('laporan.stok') }}">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 items-end">

                        @if(in_array(Auth::user()->role, ['superadmin', 'fakultas']))
                        <div>
                            <label class="form-label">Program Studi</label>
                            <select name="prodi_id" class="form-select">
                                <option value="">— Semua Prodi —</option>
                                @foreach($programStudis as $prodi)
                                    <option value="{{ $prodi->id }}" {{ $selectedProdiId == $prodi->id ? 'selected' : '' }}>{{ $prodi->nama_program_studi }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif

                        <div>
                            <label class="form-label">Tahun Periode</label>
                            <select name="tahun" class="form-select">
                                <option value="">— Semua Tahun —</option>
                                @foreach($availableYears as $year)
                                    <option value="{{ $year }}" {{ $selectedTahun == $year ? 'selected' : '' }}>{{ $year }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex gap-2">
                            <button type="submit" class="top-btn bg-indigo-600 hover:bg-indigo-700 text-white flex-1 justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
                                Filter
                            </button>
                            <a href="{{ route('laporan.stok', request()->all() + ['print' => 'true']) }}" target="_blank"
                               class="top-btn bg-gray-100 hover:bg-gray-200 text-gray-700">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                Cetak
                            </a>
                        </div>

                    </div>
                </form>
            </div>

            {{-- TABLE --}}
            @php
                $formatStock = function($value) {
                    if ($value === null || $value === '') return 'N/A';
                    $formatted = number_format((float) $value, 3, ',', '.');
                    return rtrim(rtrim($formatted, '0'), ',');
                };
            @endphp

            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Nama Bahan</th>
                                <th>{{ $selectedTahun == $tahunAktif ? 'Stok Awal Periode' : 'Stok Awal' }}</th>
                                <th>{{ $selectedTahun == $tahunAktif ? 'Stok Saat Ini' : 'Stok Akhir' }}</th>
                                <th>Satuan</th>
                                <th>Gudang</th>
                                <th>Prodi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($laporanData as $item)
                            <tr>
                                @if($selectedTahun == $tahunAktif)
                                <td><span class="font-mono text-xs font-semibold bg-gray-100 text-gray-600 px-2 py-1 rounded">{{ $item->kode_bahan }}</span></td>
                                <td>
                                    <p class="font-semibold text-gray-800 text-sm">{!! $item->nama_bahan_html ?? '<span class="text-red-500 italic text-xs">Terhapus</span>' !!}</p>
                                </td>
                                <td class="text-gray-500">{{ $formatStock($item->periodeAktif?->stok_awal) }}</td>
                                <td><span class="font-bold text-gray-800">{{ $formatStock($item->jumlah_stock) }}</span></td>
                                <td class="text-gray-400 text-xs">{{ $item->satuanRel?->nama_satuan ?? '—' }}</td>
                                <td class="text-gray-400 text-xs">{{ $item->gudang?->nama_gudang ?? '—' }}</td>
                                <td><span class="text-xs font-semibold bg-indigo-50 text-indigo-600 px-2 py-0.5 rounded-md">{{ $item->programStudi?->kode_program_studi ?? '—' }}</span></td>
                                @else
                                <td><span class="font-mono text-xs font-semibold bg-gray-100 text-gray-600 px-2 py-1 rounded">{{ $item->bahan?->kode_bahan ?? '—' }}</span></td>
                                <td>
                                    <p class="font-semibold text-gray-800 text-sm">{!! $item->bahan?->nama_bahan_html ?? '<span class="text-red-500 italic text-xs">Terhapus</span>' !!}</p>
                                </td>
                                <td class="text-gray-500">{{ $formatStock($item->stok_awal) }}</td>
                                <td><span class="font-bold text-gray-800">{{ $formatStock($item->stok_akhir) }}</span></td>
                                <td class="text-gray-400 text-xs">{{ $item->bahan?->satuanRel?->nama_satuan ?? '—' }}</td>
                                <td class="text-gray-400 text-xs">{{ $item->bahan?->gudang?->nama_gudang ?? '—' }}</td>
                                <td><span class="text-xs font-semibold bg-indigo-50 text-indigo-600 px-2 py-0.5 rounded-md">{{ $item->bahan?->programStudi?->kode_program_studi ?? '—' }}</span></td>
                                @endif
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="py-14 text-center">
                                    <div class="flex flex-col items-center gap-2 text-gray-400">
                                        <svg class="w-9 h-9 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                                        <p class="text-sm font-medium">Data stok tidak ditemukan</p>
                                        <p class="text-xs">Coba ubah filter yang dipilih</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($laporanData->hasPages())
                <div class="px-5 py-4 border-t border-gray-100 bg-gray-50">
                    {{ $laporanData->links() }}
                </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>