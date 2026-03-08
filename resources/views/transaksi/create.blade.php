{{-- resources/views/transaksi/create.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Form Stok {{ $jenis === 'masuk' ? 'Masuk' : 'Keluar' }}
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
        .form-input {
            display: block; width: 100%;
            border: 1.5px solid #e5e7eb; border-radius: 9px;
            padding: 9px 13px; font-size: 13.5px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #1f2937; background: #fff; outline: none;
            transition: border-color 0.15s, box-shadow 0.15s;
        }
        .form-input:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99,102,241,0.08);
        }
        .form-input::placeholder { color: #d1d5db; }
        textarea.form-input { resize: vertical; min-height: 90px; }
    </style>

    <div class="py-10 form-wrap">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8 space-y-5">

            {{-- PAGE HEADER --}}
            <div class="flex items-center gap-3">
                <a href="{{ route('bahan.index') }}" class="w-9 h-9 rounded-xl bg-white border border-gray-200 shadow-sm flex items-center justify-center text-gray-400 hover:text-gray-700 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </a>
                <div>
                    <h1 class="text-2xl font-extrabold text-gray-800">Stok {{ $jenis === 'masuk' ? 'Masuk' : 'Keluar' }}</h1>
                    <p class="text-sm text-gray-400 mt-0.5">Catat transaksi {{ $jenis === 'masuk' ? 'penerimaan' : 'pengeluaran' }} bahan</p>
                </div>
            </div>

            {{-- BAHAN INFO CARD --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex items-center gap-4">
                <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0 {{ $jenis === 'masuk' ? 'bg-emerald-50' : 'bg-red-50' }}">
                    @if($jenis === 'masuk')
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                    @else
                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-bold text-gray-800 truncate">{!! $bahan->nama_bahan_html !!}</p>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $bahan->merk }} · Kode: <span class="font-mono font-semibold text-gray-600">{{ $bahan->kode_bahan }}</span></p>
                </div>
                <div class="text-right flex-shrink-0">
                    <p class="text-xs text-gray-400 font-medium">Stok Saat Ini</p>
                    <p class="text-lg font-extrabold text-indigo-600">{{ $bahan->formatted_stock }}</p>
                </div>
            </div>

            {{-- FORM CARD --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <form action="{{ $jenis === 'masuk' ? route('transaksi.storeMasuk', $bahan->id) : route('transaksi.storeKeluar', $bahan->id) }}" method="POST">
                    @csrf
                    <div class="p-6 md:p-8 space-y-5">

                        {{-- JUMLAH --}}
                        <div>
                            <label for="jumlah" class="form-label">
                                Jumlah
                                <span class="ml-1 normal-case font-normal text-gray-400">(satuan: {{ $bahan->satuanRel->nama_satuan ?? '-' }})</span>
                            </label>
                            <input id="jumlah" type="number" name="jumlah"
                                value="{{ old('jumlah') }}"
                                min="0.001" step="any" required autofocus
                                placeholder="Contoh: 1.5"
                                class="form-input">
                            @error('jumlah')
                            <p class="mt-1.5 text-xs text-red-500 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01"/></svg>
                                {{ $message }}
                            </p>
                            @enderror
                        </div>

                        {{-- TANGGAL --}}
                        <div>
                            <label for="tanggal_transaksi" class="form-label">Tanggal & Waktu Transaksi</label>
                            <input id="tanggal_transaksi" type="datetime-local" name="tanggal_transaksi"
                                value="{{ old('tanggal_transaksi', now()->format('Y-m-d\TH:i')) }}"
                                required class="form-input">
                            @error('tanggal_transaksi')
                            <p class="mt-1.5 text-xs text-red-500 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01"/></svg>
                                {{ $message }}
                            </p>
                            @enderror
                        </div>

                        {{-- KETERANGAN --}}
                        <div>
                            <label for="keterangan" class="form-label">
                                Keterangan
                                <span class="ml-1 normal-case font-normal text-gray-400">(Opsional)</span>
                            </label>
                            <textarea id="keterangan" name="keterangan" class="form-input"
                                placeholder="Contoh: Pembelian dari Merck, No. PO: 12345">{{ old('keterangan') }}</textarea>
                            @error('keterangan')
                            <p class="mt-1.5 text-xs text-red-500 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01"/></svg>
                                {{ $message }}
                            </p>
                            @enderror
                        </div>

                    </div>

                    {{-- FOOTER --}}
                    <div class="px-6 md:px-8 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-end gap-3">
                        <a href="{{ route('bahan.index') }}"
                           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-gray-600 bg-white border border-gray-200 hover:bg-gray-100 transition-all">
                            Batal
                        </a>
                        <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2 rounded-lg text-white text-sm font-bold shadow-sm transition-all hover:-translate-y-0.5
                                {{ $jenis === 'masuk' ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-red-500 hover:bg-red-600' }}">
                            @if($jenis === 'masuk')
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                            @else
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                            @endif
                            Simpan Stok {{ $jenis === 'masuk' ? 'Masuk' : 'Keluar' }}
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>