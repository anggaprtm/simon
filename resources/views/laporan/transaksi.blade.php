{{-- resources/views/laporan/transaksi.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Laporan Riwayat Transaksi') }}</h2>
    </x-slot>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
        .lp-wrap * { font-family: 'Plus Jakarta Sans', sans-serif; }
        .form-label { display:block; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:#6b7280; margin-bottom:5px; }
        .form-select, .form-input {
            display:block; width:100%; border:1.5px solid #e5e7eb; border-radius:9px;
            padding:9px 13px; font-size:13px; font-family:'Plus Jakarta Sans',sans-serif;
            color:#1f2937; background:#fff; outline:none; transition:border-color .15s,box-shadow .15s;
        }
        .form-select:focus, .form-input:focus { border-color:#6366f1; box-shadow:0 0 0 3px rgba(99,102,241,.08); }
        .top-btn { display:inline-flex; align-items:center; gap:6px; padding:9px 16px; border-radius:9px; font-size:13px; font-weight:600; transition:all .15s; white-space:nowrap; }
        .top-btn:hover { transform:translateY(-1px); }
        thead th { background:#f8fafc; padding:11px 16px; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:#6b7280; white-space:nowrap; border-bottom:1px solid #e5e7eb; text-align:left; }
        tbody td { padding:12px 16px; font-size:13px; color:#374151; vertical-align:middle; }
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
                    <h1 class="text-2xl font-extrabold text-gray-800">Laporan Riwayat Transaksi</h1>
                    <p class="text-sm text-gray-400 mt-0.5">Histori seluruh mutasi stok masuk & keluar</p>
                </div>
            </div>

            {{-- FILTER --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <form method="GET" action="{{ route('laporan.transaksi') }}">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4 items-end">

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
                            <label class="form-label">Tahun</label>
                            <select name="tahun" class="form-select">
                                <option value="">— Semua Tahun —</option>
                                @foreach($availableYears as $year)
                                    <option value="{{ $year }}" {{ $selectedTahun == $year ? 'selected' : '' }}>{{ $year }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="form-label">Bulan</label>
                            <select name="bulan" class="form-select">
                                <option value="">— Semua Bulan —</option>
                                @php $namaBulan = ['1'=>'Januari','2'=>'Februari','3'=>'Maret','4'=>'April','5'=>'Mei','6'=>'Juni','7'=>'Juli','8'=>'Agustus','9'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember']; @endphp
                                @foreach($namaBulan as $num => $nama)
                                    <option value="{{ $num }}" {{ $selectedBulan == $num ? 'selected' : '' }}>{{ $nama }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="form-label">Dari Tanggal</label>
                            <input type="date" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}" class="form-input">
                        </div>

                        <div>
                            <label class="form-label">Sampai Tanggal</label>
                            <input type="date" name="tanggal_selesai" value="{{ request('tanggal_selesai') }}" class="form-input">
                        </div>

                        <div class="flex gap-2">
                            <button type="submit" class="top-btn bg-indigo-600 hover:bg-indigo-700 text-white flex-1 justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
                                Filter
                            </button>
                            <a href="{{ route('laporan.transaksi', request()->all() + ['print' => 'true']) }}" target="_blank"
                               class="top-btn bg-gray-100 hover:bg-gray-200 text-gray-700">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                Cetak
                            </a>
                        </div>

                    </div>
                </form>
            </div>

            {{-- TABLE --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Jenis</th>
                                <th>Nama Bahan</th>
                                <th>Prodi</th>
                                <th>Jumlah</th>
                                <th>Oleh</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transaksis as $transaksi)
                            @php $isMasuk = str_contains($transaksi->jenis_transaksi, 'masuk'); @endphp
                            <tr>
                                <td class="whitespace-nowrap">
                                    <p class="font-medium text-gray-700 text-xs">{{ \Carbon\Carbon::parse($transaksi->tanggal_transaksi)->isoFormat('D MMM Y') }}</p>
                                    <p class="text-gray-400 text-xs">{{ \Carbon\Carbon::parse($transaksi->tanggal_transaksi)->format('H:i') }}</p>
                                </td>
                                <td>
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold
                                        {{ $isMasuk ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $isMasuk ? 'bg-emerald-500' : 'bg-red-500' }}"></span>
                                        {{ ucwords(str_replace('_', ' ', $transaksi->jenis_transaksi)) }}
                                    </span>
                                </td>
                                <td>
                                    {!! $transaksi->bahan?->nama_bahan_html ?? '<span class="text-red-500 italic text-xs font-semibold">Bahan Terhapus</span>' !!}
                                </td>
                                <td>
                                    <span class="text-xs font-semibold bg-indigo-50 text-indigo-600 px-2 py-0.5 rounded-md">
                                        {{ $transaksi->bahan?->programStudi?->kode_program_studi ?? 'N/A' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="font-bold {{ $isMasuk ? 'text-emerald-700' : 'text-red-600' }}">
                                        {{ $isMasuk ? '+' : '-' }}{{ $transaksi->formatted_jumlah }}
                                    </span>
                                </td>
                                <td class="text-gray-500 text-xs">{{ $transaksi->user?->name ?? 'User Terhapus' }}</td>
                                <td class="text-gray-400 text-xs max-w-xs">{{ $transaksi->keterangan ?: '—' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="py-14 text-center">
                                    <div class="flex flex-col items-center gap-2 text-gray-400">
                                        <svg class="w-9 h-9 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                        <p class="text-sm font-medium">Data transaksi tidak ditemukan</p>
                                        <p class="text-xs">Coba ubah filter yang dipilih</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($transaksis->hasPages())
                <div class="px-5 py-4 border-t border-gray-100 bg-gray-50">
                    {{ $transaksis->links() }}
                </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>