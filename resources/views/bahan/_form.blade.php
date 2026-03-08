{{-- resources/views/bahan/_form.blade.php --}}
@csrf

<style>
    .form-label {
        display: block; font-size: 11px; font-weight: 700;
        text-transform: uppercase; letter-spacing: 0.06em;
        color: #6b7280; margin-bottom: 5px;
    }
    .form-input, .form-select {
        display: block; width: 100%;
        border: 1.5px solid #e5e7eb; border-radius: 9px;
        padding: 9px 13px; font-size: 13.5px;
        font-family: 'Plus Jakarta Sans', sans-serif;
        color: #1f2937; background: #fff; outline: none;
        transition: border-color 0.15s, box-shadow 0.15s;
    }
    .form-input:focus, .form-select:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99,102,241,0.08);
    }
    .form-input::placeholder { color: #d1d5db; }
    .form-error { margin-top: 5px; font-size: 11.5px; color: #ef4444; display: flex; align-items: center; gap: 4px; }
    .form-hint { margin-top: 4px; font-size: 11.5px; color: #9ca3af; }
    .section-divider { border-top: 1px solid #f0f0f0; padding-top: 20px; margin-top: 4px; }
    .section-title { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: #9ca3af; margin-bottom: 14px; }
</style>

<div class="space-y-6">

    {{-- SECTION: IDENTITAS BAHAN --}}
    <div>
        <p class="section-title">Identitas Bahan</p>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label for="kode_bahan" class="form-label">Kode Bahan</label>
                <input id="kode_bahan" type="text" name="kode_bahan"
                    value="{{ old('kode_bahan', $bahan->kode_bahan ?? '') }}"
                    class="form-input" required placeholder="Contoh: BH-001">
                @error('kode_bahan')<p class="form-error"><svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01"/></svg>{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="nama_bahan" class="form-label">Nama Bahan</label>
                <input id="nama_bahan" type="text" name="nama_bahan"
                    value="{{ old('nama_bahan', $bahan->nama_bahan ?? '') }}"
                    class="form-input" required placeholder="Nama lengkap bahan">
                @error('nama_bahan')<p class="form-error"><svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01"/></svg>{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="merk" class="form-label">Merk</label>
                <input id="merk" type="text" name="merk"
                    value="{{ old('merk', $bahan->merk ?? '') }}"
                    class="form-input" placeholder="Nama merk / produsen">
                @error('merk')<p class="form-error"><svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01"/></svg>{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="jenis_bahan" class="form-label">Jenis Bahan <span class="normal-case font-normal text-gray-400">(Opsional)</span></label>
                <input id="jenis_bahan" type="text" name="jenis_bahan"
                    value="{{ old('jenis_bahan', $bahan->jenis_bahan ?? '') }}"
                    class="form-input" placeholder="Contoh: Cairan, Padatan, Komponen">
                @error('jenis_bahan')<p class="form-error"><svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01"/></svg>{{ $message }}</p>@enderror
            </div>
        </div>

        {{-- FORMAT KIMIA CHECKBOX --}}
        <label for="format_kimia" class="mt-4 flex items-start gap-3 cursor-pointer group">
            <div class="relative mt-0.5 flex-shrink-0">
                <input type="checkbox" id="format_kimia" name="format_kimia" value="1"
                    class="w-4 h-4 rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 cursor-pointer"
                    {{ old('format_kimia', $bahan->format_kimia ?? false) ? 'checked' : '' }}>
            </div>
            <div>
                <span class="text-sm font-semibold text-gray-700 group-hover:text-indigo-700 transition-colors">Terapkan format kimia (subscript)</span>
                <p class="text-xs text-gray-400 mt-0.5">Aktifkan jika nama bahan mengandung rumus kimia seperti H₂O, NaOH, dll.</p>
            </div>
        </label>
    </div>

    {{-- SECTION: PENYIMPANAN & SATUAN --}}
    <div class="section-divider">
        <p class="section-title">Penyimpanan & Satuan</p>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label for="id_gudang" class="form-label">Lokasi Gudang</label>
                <select id="id_gudang" name="id_gudang" class="form-select" required>
                    <option value="">— Pilih Gudang —</option>
                    @foreach ($gudangs as $gudang)
                        <option value="{{ $gudang->id }}" {{ old('id_gudang', $bahan->id_gudang ?? '') == $gudang->id ? 'selected' : '' }}>
                            {{ $gudang->nama_gudang }} ({{ $gudang->programStudi->nama_program_studi ?? 'Umum' }})
                        </option>
                    @endforeach
                </select>
                @error('id_gudang')<p class="form-error"><svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01"/></svg>{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="id_satuan" class="form-label">Satuan</label>
                <select id="id_satuan" name="id_satuan" class="form-select" required>
                    <option value="">— Pilih Satuan —</option>
                    @foreach ($satuans as $satuan)
                        <option value="{{ $satuan->id }}"
                            {{ old('id_satuan', isset($bahan) ? $bahan->id_satuan : '') == $satuan->id ? 'selected' : '' }}>
                            {{ $satuan->nama_satuan }} ({{ $satuan->keterangan_satuan }})
                        </option>
                    @endforeach
                </select>
                @error('id_satuan')<p class="form-error"><svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01"/></svg>{{ $message }}</p>@enderror
            </div>
        </div>
    </div>

    {{-- SECTION: STOK & KEDALUWARSA --}}
    <div class="section-divider">
        <p class="section-title">Stok & Kedaluwarsa</p>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label for="minimum_stock" class="form-label">Stok Minimum</label>
                <input id="minimum_stock" type="number" name="minimum_stock" step="any"
                    value="{{ old('minimum_stock', fmod($bahan->minimum_stock ?? 0, 1) == 0 ? (int) ($bahan->minimum_stock ?? 0) : number_format($bahan->minimum_stock, 3, '.', '')) }}"
                    class="form-input" required placeholder="0">
                <p class="form-hint">Sistem akan memberi peringatan jika stok di bawah nilai ini</p>
                @error('minimum_stock')<p class="form-error"><svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01"/></svg>{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="tanggal_kedaluwarsa" class="form-label">Tanggal Kedaluwarsa <span class="normal-case font-normal text-gray-400">(Opsional)</span></label>
                <input id="tanggal_kedaluwarsa" type="date" name="tanggal_kedaluwarsa"
                    value="{{ old('tanggal_kedaluwarsa', isset($bahan) && $bahan->tanggal_kedaluwarsa ? \Carbon\Carbon::parse($bahan->tanggal_kedaluwarsa)->format('Y-m-d') : '') }}"
                    class="form-input">
                @error('tanggal_kedaluwarsa')<p class="form-error"><svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01"/></svg>{{ $message }}</p>@enderror
            </div>

            @if(Route::is('bahan.create'))
            <div>
                <label for="jumlah_stock" class="form-label">Jumlah Stok Awal <span class="normal-case font-normal text-gray-400">(Opsional)</span></label>
                <input id="jumlah_stock" type="number" name="jumlah_stock" step="any"
                    value="{{ old('jumlah_stock', 0) }}"
                    class="form-input" placeholder="0">
                <p class="form-hint">Isi jika ingin langsung mencatat stok awal saat pendaftaran</p>
                @error('jumlah_stock')<p class="form-error"><svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01"/></svg>{{ $message }}</p>@enderror
            </div>
            @endif
        </div>
    </div>

</div>