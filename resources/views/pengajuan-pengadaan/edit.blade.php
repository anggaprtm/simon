<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Pengajuan Pengadaan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('pengajuan-pengadaan.update', $pengajuanPengadaan->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        {{-- Bagian Header Pengajuan (dengan data yang sudah terisi) --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <x-input-label for="tahun_ajaran" :value="__('Tahun Ajaran')" />
                                <x-text-input id="tahun_ajaran" class="block mt-1 w-full" type="text" name="tahun_ajaran" :value="old('tahun_ajaran', $pengajuanPengadaan->tahun_ajaran)" required placeholder="Contoh: 2024/2025" />
                            </div>
                            <div>
                                <x-input-label for="semester" :value="__('Semester')" />
                                <select id="semester" name="semester" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" required>
                                    <option value="Ganjil" {{ old('semester', $pengajuanPengadaan->semester) == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                                    <option value="Genap" {{ old('semester', $pengajuanPengadaan->semester) == 'Genap' ? 'selected' : '' }}>Genap</option>
                                </select>
                            </div>
                        </div>

                        {{-- Bagian Detail Item (dengan data yang sudah terisi) --}}
                        <h3 class="text-lg font-semibold border-t pt-4 mb-4">Detail Barang</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full" id="items-table">
                                <thead>
                                    <tr>
                                        <th class="w-1/4 px-2 py-2 text-left">Nama Barang</th>
                                        <th class="px-2 py-2 text-left">Spesifikasi</th>
                                        <th class="px-2 py-2 text-left">Jumlah</th>
                                        <th class="px-2 py-2 text-left">Satuan</th>
                                        <th class="px-2 py-2 text-left">Harga Satuan (Rp)</th>
                                        <th class="px-2 py-2 text-left">Link Referensi</th>
                                        <th class="px-2 py-2 text-left">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="items-container">
                                    {{-- Loop untuk menampilkan item yang sudah ada --}}
                                    @foreach(old('items', $pengajuanPengadaan->details->toArray()) as $index => $item)
                                    <tr class="border-t">
                                        <td class="p-2">
                                            <select name="items[{{ $index }}][id_master_barang]" class="w-full border-gray-300 rounded-md shadow-sm" required>
                                                <option value="">Pilih Barang</option>
                                                @foreach($masterBarangs as $barang)
                                                    <option value="{{ $barang->id }}" {{ ($item['id_master_barang'] ?? '') == $barang->id ? 'selected' : '' }}>{{ $barang->nama_barang }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="p-2"><input type="text" name="items[{{ $index }}][spesifikasi]" value="{{ $item['spesifikasi'] ?? '' }}" class="w-full border-gray-300 rounded-md shadow-sm"></td>
                                        <td class="p-2"><input type="number" name="items[{{ $index }}][jumlah]" value="{{ $item['jumlah'] ?? '' }}" class="w-24 border-gray-300 rounded-md shadow-sm" required min="1"></td>
                                        <td class="p-2">
                                            <select name="items[{{ $index }}][id_satuan]" class="w-full border-gray-300 rounded-md shadow-sm" required>
                                                @foreach($satuans as $satuan)
                                                    <option value="{{ $satuan->id }}" {{ ($item['id_satuan'] ?? '') == $satuan->id ? 'selected' : '' }}>{{ $satuan->nama_satuan }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="p-2"><input type="number" name="items[{{ $index }}][harga_satuan]" value="{{ $item['harga_satuan'] ?? '' }}" class="w-40 border-gray-300 rounded-md shadow-sm" required min="0"></td>
                                        <td class="p-2"><input type="url" name="items[{{ $index }}][link_referensi]" value="{{ $item['link_referensi'] ?? '' }}" class="w-full border-gray-300 rounded-md shadow-sm"></td>
                                        <td class="p-2">
                                            <button type="button" class="remove-item-btn text-red-500 hover:text-red-700">Hapus</button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <button type="button" id="add-item-btn" class="mt-4 bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-4 rounded">
                            + Tambah Barang
                        </button>
                        
                        <div class="flex items-center justify-end mt-6 border-t pt-6">
                            <a href="{{ route('pengajuan-pengadaan.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4 underline">Batal</a>
                            <x-primary-button name="action" value="draft">
                                {{ __('Simpan Perubahan Draft') }}
                            </x-primary-button>
                            <x-primary-button name="action" value="submit" class="ml-2 bg-green-600 hover:bg-green-700">
                                {{ __('Simpan & Ajukan') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        {{-- JavaScript di sini sama persis seperti di create.blade.php, hanya perlu penyesuaian kecil untuk itemIndex --}}
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const container = document.getElementById('items-container');
                const addItemBtn = document.getElementById('add-item-btn');
                // Mulai index dari jumlah item yang sudah ada
                let itemIndex = {{ count(old('items', $pengajuanPengadaan->details->toArray())) }};

                function addItemRow() {
                    // ... (Fungsi addItemRow sama persis seperti di create.blade.php) ...
                }

                // ... (Event listener untuk add dan remove sama persis seperti di create.blade.php) ...
            });
        </script>
    @endpush
</x-app-layout>