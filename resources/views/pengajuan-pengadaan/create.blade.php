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

            // Ambil data input lama dan error dari session Laravel
            const oldItems = @json(old('items')) || [];
            const errors = @json($errors->getMessages());

            function addItemRow(itemData = null, index = null) {
                const currentIndex = index !== null ? index : itemIndex;

                const newRow = document.createElement('tr');
                newRow.classList.add('border-t');
                
                // Cek apakah ada error untuk baris ini
                const hasError = errors && errors[`items.${currentIndex}.id_bahan`];

                newRow.innerHTML = `
                    <td class="p-2 align-top">
                        <select name="items[${currentIndex}][id_bahan]" class="select2-bahan w-full" required>
                            <option value="">Cari dan Pilih Bahan...</option>
                            @foreach($bahans as $bahan)
                                <option value="{{ $bahan->id }}" ${itemData && itemData.id_bahan == {{ $bahan->id }} ? 'selected' : ''}>
                                    {!! $bahan->nama_bahan !!}
                                </option>
                            @endforeach
                        </select>
                        ${generateErrorHtml(`items.${currentIndex}.id_bahan`)}
                    </td>
                    <td class="p-2 align-top">
                        <input type="text" name="items[${currentIndex}][spesifikasi]" class="w-full border-gray-300 rounded-md shadow-sm" value="${itemData?.spesifikasi || ''}">
                        ${generateErrorHtml(`items.${currentIndex}.spesifikasi`)}
                    </td>
                    <td class="p-2 align-top">
                        <input type="number" name="items[${currentIndex}][jumlah]" class="w-24 border-gray-300 rounded-md shadow-sm" step="any" required min="0.001" value="${itemData?.jumlah || ''}">
                        ${generateErrorHtml(`items.${currentIndex}.jumlah`)}
                    </td>
                    <td class="p-2 align-top">
                        <select name="items[${currentIndex}][id_satuan]" class="w-24 border-gray-300 rounded-md shadow-sm" required>
                            @foreach($satuans as $satuan)
                                <option value="{{ $satuan->id }}" ${itemData && itemData.id_satuan == {{ $satuan->id }} ? 'selected' : ''}>
                                    {{ $satuan->nama_satuan }}
                                </option>
                            @endforeach
                        </select>
                        ${generateErrorHtml(`items.${currentIndex}.id_satuan`)}
                    </td>
                    <td class="p-2 align-top">
                        <input type="number" name="items[${currentIndex}][harga_satuan]" class="w-40 border-gray-300 rounded-md shadow-sm" required min="0" value="${itemData?.harga_satuan || ''}">
                        ${generateErrorHtml(`items.${currentIndex}.harga_satuan`)}
                    </td>
                    <td class="p-2 align-top">
                        <input type="text" name="items[${currentIndex}][link_referensi]" placeholder="https://..." class="w-full border-gray-300 rounded-md shadow-sm" value="${itemData?.link_referensi || ''}">
                        ${generateErrorHtml(`items.${currentIndex}.link_referensi`)}
                    </td>
                    <td class="p-2 align-top">
                        <button type="button" class="remove-item-btn text-red-500 hover:text-red-700">Hapus</button>
                    </td>
                `;
                container.appendChild(newRow);

                // Inisialisasi Select2
                $(`select[name="items[${currentIndex}][id_bahan]"]`).select2({
                    placeholder: "Cari bahan...",
                    width: 'resolve'
                });

                if (index === null) {
                    itemIndex++;
                }
            }

            // Fungsi untuk membuat HTML pesan error
            function generateErrorHtml(field) {
                if (errors && errors[field]) {
                    return `<div class="text-sm text-red-600 mt-1">${errors[field][0]}</div>`;
                }
                return '';
            }

            // Jika ada data input lama, render ulang barisnya
            if (oldItems.length > 0) {
                oldItems.forEach((item, index) => {
                    addItemRow(item, index);
                });
                itemIndex = oldItems.length;
            } else {
                // Jika tidak, tambahkan satu baris kosong
                addItemRow();
            }

            addItemBtn.addEventListener('click', () => addItemRow());

            container.addEventListener('click', function (e) {
                if (e.target && e.target.classList.contains('remove-item-btn')) {
                    $(e.target).closest('tr').find('.select2-bahan').select2('destroy');
                    e.target.closest('tr').remove();
                }
            });
        });
    </script>

    <style>
        .select2-container {
            width: 100% !important; /* Pastikan Select2 mengambil lebar penuh dari parent TD */
        }
        /* Jika ada masalah dengan tinggi input Select2, Anda bisa menambahkan ini */
        .select2-container .select2-selection--single {
            height: 38px; /* Sesuaikan dengan tinggi input default Tailwind */
            border-radius: 0.375rem; /* rounded-md */
            border-color: #d1d5db; /* border-gray-300 */
        }
        .select2-container .select2-selection--single .select2-selection__rendered {
            line-height: 38px;
        }
        .select2-container .select2-selection--single .select2-selection__arrow {
            height: 36px;
        }
    </style>
    @endpush
</x-app-layout>