{{-- resources/views/penyesuaian/create.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Form Penyesuaian Stok (Stok Opname)') }}
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
            border-color: #f97316;
            box-shadow: 0 0 0 3px rgba(249,115,22,0.08);
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
                    <h1 class="text-2xl font-extrabold text-gray-800">Penyesuaian Stok</h1>
                    <p class="text-sm text-gray-400 mt-0.5">Sesuaikan stok sistem dengan hasil hitungan fisik di gudang</p>
                </div>
            </div>

            {{-- ERROR ALERT --}}
            @if (session('error'))
            <div class="flex items-start gap-3 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl text-sm">
                <svg class="w-4 h-4 flex-shrink-0 mt-0.5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                <div><strong class="font-bold">Terjadi Kesalahan!</strong> {{ session('error') }}</div>
            </div>
            @endif

            {{-- BAHAN INFO CARD --}}
            <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-amber-900">{!! $bahan->nama_bahan_html !!}</p>
                        <p class="text-xs text-amber-700 mt-0.5">
                            {{ $bahan->merk }} · Kode: <span class="font-mono font-semibold">{{ $bahan->kode_bahan }}</span>
                        </p>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <p class="text-xs text-amber-600 font-semibold uppercase tracking-wider">Stok Sistem</p>
                        <p class="text-2xl font-extrabold text-amber-700 leading-tight">{{ $bahan->formatted_stock }}</p>
                    </div>
                </div>

                {{-- PERINGATAN --}}
                <div class="mt-4 pt-4 border-t border-amber-200 flex items-start gap-2">
                    <svg class="w-4 h-4 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <p class="text-xs text-amber-700 leading-relaxed">
                        Penyesuaian akan langsung mengubah stok sistem menjadi nilai yang Anda masukkan dan mencatat selisihnya sebagai transaksi koreksi.
                    </p>
                </div>
            </div>

            {{-- FORM CARD --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <form action="{{ route('penyesuaian.store', $bahan->id) }}" method="POST">
                    @csrf
                    <div class="p-6 md:p-8 space-y-5">

                        {{-- STOK FISIK --}}
                        <div>
                            <label for="stok_fisik" class="form-label">
                                Jumlah Stok Fisik Aktual
                                <span class="ml-1 normal-case font-normal text-gray-400">({{ $bahan->satuanRel->nama_satuan ?? '-' }})</span>
                            </label>
                            <p class="text-xs text-gray-400 mb-2">Masukkan jumlah hasil perhitungan manual di gudang.</p>
                            <input id="stok_fisik" type="number" name="stok_fisik"
                                value="{{ old('stok_fisik', fmod($bahan->jumlah_stock, 1) == 0 ? (int) $bahan->jumlah_stock : number_format($bahan->jumlah_stock, 3, '.', '')) }}"
                                min="0" step="any" required autofocus
                                class="form-input text-lg font-bold">

                            {{-- SELISIH PREVIEW --}}
                            <div id="selisih-preview" class="mt-2 hidden">
                                <p class="text-xs font-semibold" id="selisih-text"></p>
                            </div>

                            @error('stok_fisik')
                            <p class="mt-1.5 text-xs text-red-500 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01"/></svg>
                                {{ $message }}
                            </p>
                            @enderror
                        </div>

                        {{-- KETERANGAN --}}
                        <div>
                            <label for="keterangan" class="form-label">Keterangan</label>
                            <textarea id="keterangan" name="keterangan" required
                                class="form-input">{{ old('keterangan', 'Hasil Stok Opname Fisik per ' . now()->isoFormat('D MMMM Y')) }}</textarea>
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
                            class="inline-flex items-center gap-2 px-5 py-2 rounded-lg bg-orange-500 hover:bg-orange-600 text-white text-sm font-bold shadow-sm transition-all hover:-translate-y-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75"/></svg>
                            Simpan Penyesuaian
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>

    @push('scripts')
    <script>
        const stokSistem = {{ fmod($bahan->jumlah_stock, 1) == 0 ? (int)$bahan->jumlah_stock : $bahan->jumlah_stock }};
        const satuanNama = "{{ $bahan->satuanRel->nama_satuan ?? '' }}";
        const input = document.getElementById('stok_fisik');
        const preview = document.getElementById('selisih-preview');
        const previewText = document.getElementById('selisih-text');

        function updatePreview() {
            const fisik = parseFloat(input.value);
            if (isNaN(fisik)) { preview.classList.add('hidden'); return; }

            const selisih = fisik - stokSistem;
            preview.classList.remove('hidden');

            if (selisih === 0) {
                previewText.textContent = '✓ Stok sama dengan sistem, tidak ada perubahan.';
                previewText.className = 'text-xs font-semibold text-emerald-600';
            } else if (selisih > 0) {
                previewText.textContent = `▲ Selisih +${selisih.toFixed(3).replace(/\.?0+$/, '')} ${satuanNama} — stok akan bertambah.`;
                previewText.className = 'text-xs font-semibold text-emerald-600';
            } else {
                previewText.textContent = `▼ Selisih ${selisih.toFixed(3).replace(/\.?0+$/, '')} ${satuanNama} — stok akan berkurang.`;
                previewText.className = 'text-xs font-semibold text-red-500';
            }
        }

        input.addEventListener('input', updatePreview);
        updatePreview();
    </script>
    @endpush
</x-app-layout>