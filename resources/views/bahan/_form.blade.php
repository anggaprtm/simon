{{-- resources/views/bahan/_form.blade.php --}}
@csrf
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div>
        <x-input-label for="kode_bahan" :value="__('Kode Bahan')" />
        <x-text-input id="kode_bahan" class="block mt-1 w-full" type="text" name="kode_bahan" :value="old('kode_bahan', $bahan->kode_bahan ?? '')" required />
        <x-input-error :messages="$errors->get('kode_bahan')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="nama_bahan" :value="__('Nama Bahan')" />
        <x-text-input id="nama_bahan" class="block mt-1 w-full" type="text" name="nama_bahan" :value="old('nama_bahan', $bahan->nama_bahan ?? '')" required />
        <x-input-error :messages="$errors->get('nama_bahan')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="merk" :value="__('Merk')" />
        <x-text-input id="merk" class="block mt-1 w-full" type="text" name="merk" :value="old('merk', $bahan->merk ?? '')" />
        <x-input-error :messages="$errors->get('merk')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="jenis_bahan" :value="__('Jenis Bahan (Opsional)')" />
        <x-text-input id="jenis_bahan" class="block mt-1 w-full" type="text" name="jenis_bahan" :value="old('jenis_bahan', $bahan->jenis_bahan ?? '')" placeholder="Contoh: Cairan, Padatan, Komponen"/>
        <x-input-error :messages="$errors->get('jenis_bahan')" class="mt-2" />
    </div>
    <div class="md:col-span-2 flex items-center space-x-2 pt-4">
        <input type="checkbox" id="format_kimia" name="format_kimia" value="1" 
            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
            {{-- Logika untuk menangani old input dan nilai saat edit --}}
            {{ old('format_kimia', $bahan->format_kimia ?? false) ? 'checked' : '' }}
        >
        <label for="format_kimia" class="text-sm font-medium text-gray-700">
            Terapkan format penulisan kimia (subscript) pada nama bahan ini?
        </label>
    </div>
    <div>
        <x-input-label for="id_gudang" :value="__('Lokasi Gudang')" />
        <select id="id_gudang" name="id_gudang" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" required>
            <option value="">-- Pilih Gudang --</option>
            @foreach ($gudangs as $gudang)
                <option value="{{ $gudang->id }}" {{ old('id_gudang', $bahan->id_gudang ?? '') == $gudang->id ? 'selected' : '' }}>
                    {{ $gudang->nama_gudang }} ({{ $gudang->programStudi->nama_program_studi ?? 'Umum' }})
                </option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('id_gudang')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="id_satuan" :value="__('Satuan')" />
        <select id="id_satuan" name="id_satuan" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
            <option value="">-- Pilih Satuan --</option>
            @foreach ($satuans as $satuan)
                <option value="{{ $satuan->id }}" 
                    {{ old('id_satuan', isset($bahan) ? $bahan->id_satuan : '') == $satuan->id ? 'selected' : '' }}>
                    {{ $satuan->nama_satuan }} ({{ $satuan->keterangan_satuan }})
                </option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('id_satuan')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="minimum_stock" :value="__('Stok Minimum')" />
        <x-text-input id="minimum_stock" class="block mt-1 w-full" type="number" name="minimum_stock" :value="old('minimum_stock', fmod($bahan->minimum_stock ?? 0, 1) == 0 ? (int) $bahan->minimum_stock : number_format($bahan->minimum_stock, 3, '.', ''))" step="any" required />
        <x-input-error :messages="$errors->get('minimum_stock')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="tanggal_kedaluwarsa" :value="__('Tanggal Kedaluwarsa (Opsional)')" />
        <x-text-input id="tanggal_kedaluwarsa" class="block mt-1 w-full" type="date" name="tanggal_kedaluwarsa" :value="old('tanggal_kedaluwarsa', isset($bahan) && $bahan->tanggal_kedaluwarsa ? \Carbon\Carbon::parse($bahan->tanggal_kedaluwarsa)->format('Y-m-d') : '')" />
        <x-input-error :messages="$errors->get('tanggal_kedaluwarsa')" class="mt-2" />
    </div>

    {{-- Field Stok Awal hanya ada di halaman Create --}}
    @if(Route::is('bahan.create'))
    <div>
        <x-input-label for="jumlah_stock" :value="__('Jumlah Stok Awal (Opsional)')" />
        <x-text-input id="jumlah_stock" class="block mt-1 w-full" type="number" name="jumlah_stock" :value="old('jumlah_stock', 0)" step="any" />
        <x-input-error :messages="$errors->get('jumlah_stock')" class="mt-2" />
    </div>
    @endif
</div>