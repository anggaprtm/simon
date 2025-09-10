<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Buat Pengajuan Pengadaan Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('pengajuan-pengadaan.store') }}" method="POST">
                        @csrf
                        {{-- Bagian Header Pengajuan --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <x-input-label for="tahun_ajaran" :value="__('Tahun Ajaran')" />
                                <x-text-input id="tahun_ajaran" class="block mt-1 w-full" type="text" name="tahun_ajaran" :value="old('tahun_ajaran')" required placeholder="Contoh: 2024/2025" />
                                <x-input-error :messages="$errors->get('tahun_ajaran')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="semester" :value="__('Semester')" />
                                <select id="semester" name="semester" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" required>
                                    <option value="Ganjil" {{ old('semester') == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                                    <option value="Genap" {{ old('semester') == 'Genap' ? 'selected' : '' }}>Genap</option>
                                </select>
                                <x-input-error :messages="$errors->get('semester')" class="mt-2" />
                            </div>
                        </div>

                        {{-- Bagian Detail Item --}}
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
                                    {{-- Baris item akan ditambahkan di sini oleh JavaScript --}}
                                </tbody>
                            </table>
                        </div>
                        <button type="button" id="add-item-btn" class="mt-4 bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-4 rounded">
                            + Tambah Barang
                        </button>
                        
                        <div class="flex items-center justify-end mt-6 border-t pt-6">
                            <a href="{{ route('pengajuan-pengadaan.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4 underline">Batal</a>
                            <x-primary-button name="action" value="draft">
                                {{ __('Simpan sebagai Draft') }}
                            </x-primary-button>
                            <x-primary-button name="action" value="submit" class="ml-2 bg-green-600 hover:bg-green-700">
                                {{ __('Ajukan Sekarang') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const container = document.getElementById('items-container');
            const addItemBtn = document.getElementById('add-item-btn');
            let itemIndex = 0;

            // Fungsi untuk menambah baris item baru
            function addItemRow() {
                const newRow = document.createElement('tr');
                newRow.classList.add('border-t');
                newRow.innerHTML = `
                    <td class="p-2">
                        <select name="items[${itemIndex}][id_master_barang]" class="w-full border-gray-300 rounded-md shadow-sm" required>
                            <option value="">Pilih Barang</option>
                            @foreach($masterBarangs as $barang)
                                <option value="{{ $barang->id }}">{{ $barang->nama_barang }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td class="p-2"><input type="text" name="items[${itemIndex}][spesifikasi]" class="w-full border-gray-300 rounded-md shadow-sm"></td>
                    <td class="p-2"><input type="number" name="items[${itemIndex}][jumlah]" class="w-24 border-gray-300 rounded-md shadow-sm" step="any" required min="0.001"></td>
                    <td class="p-2">
                        <select name="items[${itemIndex}][id_satuan]" class="w-24 border-gray-300 rounded-md shadow-sm" required>
                            @foreach($satuans as $satuan)
                                <option value="{{ $satuan->id }}">{{ $satuan->nama_satuan }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td class="p-2"><input type="number" name="items[${itemIndex}][harga_satuan]" class="w-40 border-gray-300 rounded-md shadow-sm" required min="0"></td>
                    <td class="p-2"><input type="url" name="items[${itemIndex}][link_referensi]" placeholder="https://..." class="w-full border-gray-300 rounded-md shadow-sm"></td>
                    <td class="p-2">
                        <button type="button" class="remove-item-btn text-red-500 hover:text-red-700">Hapus</button>
                    </td>
                `;
                container.appendChild(newRow);
                itemIndex++;
            }

            // Tambah baris pertama saat halaman dimuat
            addItemRow();

            // Event listener untuk tombol tambah
            addItemBtn.addEventListener('click', addItemRow);

            // Event listener untuk tombol hapus (menggunakan event delegation)
            container.addEventListener('click', function (e) {
                if (e.target && e.target.classList.contains('remove-item-btn')) {
                    e.target.closest('tr').remove();
                }
            });
        });
    </script>
    @endpush
</x-app-layout>