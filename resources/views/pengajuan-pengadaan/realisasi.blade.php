{{-- resources/views/pengajuan-pengadaan/realisasi.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Realisasi Stok dari Pengajuan Disetujui') }}
        </h2>
    </x-slot>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
        .realisasi-wrap * { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>

    <div class="py-10 realisasi-wrap">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- PAGE HEADER --}}
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-extrabold text-gray-800">Realisasi Stok Barang</h1>
                        <p class="text-sm text-gray-500 mt-0.5">Proses input stok dari pengajuan pengadaan yang disetujui</p>
                    </div>
                </div>
                <a href="{{ route('pengajuan-pengadaan.show', $pengajuanPengadaan) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-gray-600 bg-white border border-gray-200 hover:bg-gray-50 hover:text-gray-900 shadow-sm transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Kembali ke Detail
                </a>
            </div>

            {{-- SESSION ALERTS --}}
            @if (session('success'))
            <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl text-sm font-medium shadow-sm">
                <svg class="w-5 h-5 flex-shrink-0 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                {{ session('success') }}
            </div>
            @endif
            @if (session('error'))
            <div class="flex items-start gap-3 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl text-sm font-medium shadow-sm">
                <svg class="w-5 h-5 flex-shrink-0 mt-0.5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div>{{ session('error') }}</div>
            </div>
            @endif

            {{-- INFORMASI --}}
            <div class="flex items-start gap-3 bg-blue-50 border border-blue-200 text-blue-800 px-5 py-4 rounded-2xl text-sm shadow-sm">
                <svg class="w-5 h-5 flex-shrink-0 mt-0.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div class="leading-relaxed">
                    <strong class="font-bold block mb-1">Panduan Realisasi:</strong>
                    Proses realisasi dilakukan per item untuk mencegah kesalahan pencatatan satuan. Jika satuan pengajuan berbeda dengan satuan bahan di gudang (existing), silakan isi nilai konversinya <span class="font-semibold bg-blue-100 px-1.5 py-0.5 rounded text-blue-700">(contoh: 1 botol = 5 L)</span>.
                </div>
            </div>

            {{-- DAFTAR ITEM --}}
            <div class="space-y-5">
                @foreach($pengajuanPengadaan->details as $detail)
                    @php
                        $eligible = in_array($detail->status_item, ['disetujui', 'disetujui_sebagian']) && (float)$detail->approved_jumlah > 0;
                        $isExisting = !is_null($detail->id_bahan);
                        $needConversion = $isExisting && ((int)$detail->id_satuan !== (int)($detail->bahan->id_satuan ?? 0));
                    @endphp

                    <div class="bg-white border rounded-2xl shadow-sm overflow-hidden transition-all {{ $detail->is_direalisasi ? 'border-emerald-200 bg-emerald-50/30' : 'border-gray-200' }}">
                        
                        {{-- CARD HEADER INFO --}}
                        <div class="p-5 md:p-6 border-b {{ $detail->is_direalisasi ? 'border-emerald-100' : 'border-gray-100 bg-gray-50/50' }} flex flex-col md:flex-row md:items-start justify-between gap-4">
                            <div>
                                <h3 class="font-extrabold text-lg text-gray-800">{{ $detail->display_nama_barang }}</h3>
                                <div class="mt-2 flex flex-col sm:flex-row gap-2 sm:gap-4 text-sm">
                                    <div class="flex items-center gap-1.5 text-gray-600">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        Disetujui: <strong class="text-gray-900">{{ rtrim(rtrim(number_format((float)$detail->approved_jumlah, 3, ',', '.'), '0'), ',') }} {{ $detail->satuan->nama_satuan }}</strong>
                                    </div>
                                    <div class="flex items-center gap-1.5 text-gray-600">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                        Status: <span class="font-semibold">{{ ucfirst(str_replace('_', ' ', $detail->status_item)) }}</span>
                                    </div>
                                </div>
                                
                                <div class="mt-2">
                                    @if($isExisting)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-bold bg-blue-100 text-blue-700">
                                            Bahan Existing (Satuan Stok: {{ $detail->bahan->satuanRel->nama_satuan ?? '-' }})
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-bold bg-amber-100 text-amber-700">
                                            Bahan Baru (Belum ada di Master Bahan)
                                        </span>
                                    @endif
                                </div>
                            </div>
                            
                            @if($detail->is_direalisasi)
                                <div class="shrink-0">
                                    <span class="inline-flex items-center gap-1.5 bg-emerald-100 text-emerald-700 border border-emerald-200 px-3 py-1.5 rounded-lg text-sm font-bold shadow-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        Sudah Direalisasi
                                    </span>
                                </div>
                            @endif
                        </div>

                        {{-- CARD BODY (FORM) --}}
                        <div class="p-5 md:p-6">
                            @if($eligible && !$detail->is_direalisasi)
                                <form action="{{ route('pengajuan-pengadaan.realisasiItem', [$pengajuanPengadaan, $detail]) }}" method="POST">
                                    @csrf

                                    @if($isExisting)
                                        @if($needConversion)
                                            <div class="bg-amber-50 border border-amber-200 p-4 rounded-xl max-w-xl">
                                                <label class="block text-sm font-bold text-gray-800 mb-1.5">Nilai konversi ke satuan stok <span class="text-red-500">*</span></label>
                                                <p class="text-xs text-amber-700 mb-3">Satuan pengajuan berbeda dengan satuan di gudang. Masukkan nilai konversinya.</p>
                                                <input type="number" name="konversi_nilai" step="any" min="0.000001" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 transition-all sm:text-sm" placeholder="Contoh: 5" required>
                                            </div>
                                        @else
                                            <div class="flex items-center gap-2 text-emerald-600 bg-emerald-50 px-4 py-3 rounded-xl border border-emerald-100 font-medium text-sm max-w-xl">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                Satuan sama dengan Master Bahan, konversi otomatis (1:1).
                                            </div>
                                        @endif
                                    @else
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nama Bahan Baru <span class="text-red-500">*</span></label>
                                                <input type="text" name="nama_bahan_baru" value="{{ $detail->display_nama_barang }}" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-all sm:text-sm" required>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Merk (Opsional)</label>
                                                <input type="text" name="merk_baru" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-all sm:text-sm">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Jenis Bahan (Opsional)</label>
                                                <input type="text" name="jenis_bahan_baru" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-all sm:text-sm">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Gudang Penyimpanan <span class="text-red-500">*</span></label>
                                                <select name="id_gudang" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-all sm:text-sm" required>
                                                    <option value="">-- Pilih Gudang --</option>
                                                    @foreach($gudangs as $g)
                                                        <option value="{{ $g->id }}">{{ $g->nama_gudang }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Satuan Bahan Baru <span class="text-red-500">*</span></label>
                                                <select name="id_satuan_baru" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-all sm:text-sm" required>
                                                    <option value="">-- Pilih Satuan --</option>
                                                    @foreach($satuans as $satuan)
                                                        <option value="{{ $satuan->id }}">{{ $satuan->nama_satuan }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Minimum Stok</label>
                                                <input type="number" name="minimum_stock_baru" min="0" step="any" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-all sm:text-sm" value="0">
                                            </div>
                                            
                                            <div class="md:col-span-2 pt-2 border-t border-gray-100 mt-2">
                                                <label class="block text-sm font-bold text-gray-800 mb-1.5">Qty Realisasi Masuk <span class="text-red-500">*</span></label>
                                                <input type="number" name="qty_realisasi" step="any" min="0.000001" class="block w-full md:w-1/2 rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-all sm:text-sm font-semibold bg-indigo-50 text-indigo-700" value="{{ floatval($detail->approved_jumlah) }}" required>
                                                <p class="text-xs text-gray-500 mt-1.5">Jumlah riil yang masuk ke gudang saat ini. Default-nya adalah jumlah yang disetujui.</p>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="mt-6 pt-5 flex items-center">
                                        <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold shadow-sm transition-all hover:-translate-y-0.5">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                                            Proses Realisasi Item
                                        </button>
                                    </div>
                                </form>
                            @elseif(!$eligible)
                                <div class="flex items-center gap-2 text-gray-500 bg-gray-50 border border-gray-200 px-4 py-3 rounded-xl text-sm">
                                    <svg class="w-5 h-5 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                    Item ini tidak memenuhi syarat realisasi (Bukan item disetujui / Jumlah disetujui 0).
                                </div>
                            @elseif($detail->is_direalisasi)
                                <div class="text-sm text-gray-500 italic">
                                    Tidak ada tindakan lebih lanjut yang diperlukan untuk item ini.
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    </div>
</x-app-layout>