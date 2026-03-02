<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Edit Pengajuan Pengadaan') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('pengajuan-pengadaan.update', $pengajuanPengadaan) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <x-input-label for="tahun_ajaran" :value="__('Tahun Ajaran')" />
                                <x-text-input id="tahun_ajaran" class="block mt-1 w-full" type="text" name="tahun_ajaran" :value="old('tahun_ajaran', $pengajuanPengadaan->tahun_ajaran)" required />
                            </div>
                            <div>
                                <x-input-label for="semester" :value="__('Semester')" />
                                <select id="semester" name="semester" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" required>
                                    <option value="Ganjil" {{ old('semester', $pengajuanPengadaan->semester) == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                                    <option value="Genap" {{ old('semester', $pengajuanPengadaan->semester) == 'Genap' ? 'selected' : '' }}>Genap</option>
                                </select>
                            </div>
                        </div>

                        <h3 class="text-lg font-semibold border-t pt-4 mb-2">Detail Barang</h3>
                        <p class="text-sm text-gray-600 mb-4">Ketik untuk memilih bahan existing, atau tulis bahan baru.</p>

                        <div class="overflow-x-auto">
                            <table class="min-w-full">
                                <thead>
                                    <tr>
                                        <th class="px-2 py-2 text-left">Bahan (existing / baru)</th>
                                        <th class="px-2 py-2 text-left">Info Stok</th>
                                        <th class="px-2 py-2 text-left">Spesifikasi</th>
                                        <th class="px-2 py-2 text-left">Jumlah Diajukan</th>
                                        <th class="px-2 py-2 text-left">Satuan</th>
                                        <th class="px-2 py-2 text-left">Harga Satuan (Rp)</th>
                                        <th class="px-2 py-2 text-left">Link Referensi</th>
                                        <th class="px-2 py-2 text-left">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="items-container"></tbody>
                            </table>
                        </div>

                        <button type="button" id="add-item-btn" class="mt-4 bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-4 rounded">+ Tambah Barang</button>

                        <div class="flex items-center justify-end mt-6 border-t pt-6">
                            <a href="{{ route('pengajuan-pengadaan.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4 underline">Batal</a>
                            <x-primary-button name="action" value="draft">{{ __('Simpan Perubahan Draft') }}</x-primary-button>
                            <x-primary-button name="action" value="submit" class="ml-2 bg-green-600 hover:bg-green-700">{{ __('Simpan & Ajukan') }}</x-primary-button>
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
            const bahanList = @json($bahanOptions);
            const satuans = @json($satuans);
            const oldItems = @json(old('items', $detailItemsForJs));
            let itemIndex = 0;

            function satuanOptions(selectedId = '') {
                return satuans.map(s => `<option value="${s.id}" ${String(selectedId)===String(s.id)?'selected':''}>${s.nama_satuan}</option>`).join('');
            }

            function createRow(item = null) {
                const idx = itemIndex++;
                const row = document.createElement('tr');
                row.classList.add('border-t');

                row.innerHTML = `
                    <td class="p-2 align-top"><select name="items[${idx}][item_ref]" class="item-ref select2-bahan w-full" required></select><p class="text-xs mt-1 item-type text-blue-600">Pilih bahan existing atau ketik bahan baru.</p></td>
                    <td class="p-2 align-top text-sm item-stock text-gray-600">-</td>
                    <td class="p-2 align-top"><input type="text" name="items[${idx}][spesifikasi]" class="w-full border-gray-300 rounded-md shadow-sm" value="${item?.spesifikasi ?? ''}"></td>
                    <td class="p-2 align-top"><input type="number" step="any" min="0.001" name="items[${idx}][jumlah]" class="w-28 border-gray-300 rounded-md shadow-sm" value="${item?.jumlah ?? ''}" required></td>
                    <td class="p-2 align-top"><select name="items[${idx}][id_satuan]" class="w-full border-gray-300 rounded-md shadow-sm" required>${satuanOptions(item?.id_satuan)}</select></td>
                    <td class="p-2 align-top"><input type="number" min="0" name="items[${idx}][harga_satuan]" class="w-36 border-gray-300 rounded-md shadow-sm" value="${item?.harga_satuan ?? ''}" required></td>
                    <td class="p-2 align-top"><input type="url" name="items[${idx}][link_referensi]" class="w-full border-gray-300 rounded-md shadow-sm" value="${item?.link_referensi ?? ''}"></td>
                    <td class="p-2 align-top"><button type="button" class="remove-item-btn text-red-600 hover:underline">Hapus</button></td>
                `;

                container.appendChild(row);
                const select = row.querySelector('.item-ref');
                $(select).select2({
                    data: [{id:'', text:'Ketik / pilih bahan...'}, ...bahanList.map(b => ({id: String(b.id), text: b.text}))],
                    tags: true,
                    placeholder: 'Ketik / pilih bahan...',
                    width: '100%'
                });

                if (item?.item_ref) {
                    const value = String(item.item_ref);
                    const optionExists = Array.from(select.options).some(opt => opt.value === value);
                    if (!optionExists) {
                        select.add(new Option(value, value, true, true));
                    }
                    $(select).val(value).trigger('change');
                }

                $(select).on('change', function () {
                    const selected = bahanList.find(b => String(b.id) === String(this.value));
                    const stockCell = row.querySelector('.item-stock');
                    const typeCell = row.querySelector('.item-type');
                    if (selected) {
                        stockCell.textContent = `Stok saat ini: ${selected.stock_text ?? (selected.stock + ' ' + selected.satuan)}`;
                        typeCell.textContent = 'Bahan existing';
                        typeCell.className = 'text-xs mt-1 item-type text-green-600';
                    } else if (this.value) {
                        stockCell.textContent = 'Pengajuan bahan baru (belum ada di master bahan).';
                        typeCell.textContent = 'Bahan baru';
                        typeCell.className = 'text-xs mt-1 item-type text-amber-600';
                    } else {
                        stockCell.textContent = '-';
                        typeCell.textContent = 'Pilih bahan existing atau ketik bahan baru.';
                        typeCell.className = 'text-xs mt-1 item-type text-blue-600';
                    }
                });

                $(select).trigger('change');
            }

            (oldItems.length ? oldItems : [null]).forEach(item => createRow(item));
            addItemBtn.addEventListener('click', () => createRow());

            container.addEventListener('click', function (e) {
                if (e.target.classList.contains('remove-item-btn')) {
                    const row = e.target.closest('tr');
                    $(row.querySelector('.item-ref')).select2('destroy');
                    row.remove();
                }
            });
        });
    </script>
    @endpush
</x-app-layout>
