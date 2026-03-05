<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Buat Pengajuan Pengadaan Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('pengajuan-pengadaan.store') }}" method="POST">
                        @csrf
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
                        <div class="mb-6 p-4 border border-indigo-200 rounded-lg bg-indigo-50 flex flex-col md:flex-row items-center justify-between gap-4">
                            <div>
                                <h4 class="font-bold text-indigo-800">Import Data dari Excel</h4>
                                <p class="text-sm text-indigo-600">Pastikan urutan kolom: <b>Nama Bahan | Spesifikasi | Jumlah | Satuan | Harga Satuan | Link Referensi</b></p>
                                <a href="{{ route('pengajuan-pengadaan.template-excel') }}" class="text-sm text-blue-600 hover:text-blue-800 underline mt-2 inline-block font-semibold">
                                    ↓ Download Template Excel
                                </a>
                            </div>
                            <div class="flex items-center gap-2 w-full md:w-auto">
                                <input type="file" id="excel-file" accept=".xlsx, .xls, .csv" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-indigo-100 file:text-indigo-700 hover:file:bg-indigo-200 cursor-pointer border border-gray-300 rounded bg-white">
                                <button type="button" id="btn-import-excel" class="bg-indigo-600 hover:bg-indigo-700 text-gray-500 font-bold py-2 px-4 rounded whitespace-nowrap">
                                    Proses Import
                                </button>
                            </div>
                        </div>
                        <h3 class="text-lg font-semibold border-t pt-4 mb-2">Detail Barang</h3>
                        <p class="text-sm text-gray-600 mb-4">Ketik untuk mencari bahan existing atau ketik nama bahan baru. Existing akan menampilkan stok saat ini.</p>

                        <div class="overflow-x-auto pb-2">
                            <table class="min-w-[1450px]" id="items-table">
                                <thead>
                                    <tr>
                                        <th class="px-2 py-2 text-left">Bahan (existing / baru)</th>
                                        <th class="px-2 py-2 text-left">Info Stok</th>
                                        <th class="px-2 py-2 text-left">Spesifikasi</th>
                                        <th class="px-2 py-2 text-left">Jumlah Diajukan</th>
                                        <th class="px-2 py-2 text-left">Satuan</th>
                                        <th class="px-2 py-2 text-left">Harga Satuan (HPS)</th>
                                        <th class="px-2 py-2 text-left">Link Referensi</th>
                                        <th class="px-2 py-2 text-left">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="items-container"></tbody>
                            </table>
                        </div>

                        <button type="button" id="add-item-btn" class="mt-4 bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-4 rounded">
                            + Tambah Barang
                        </button>

                        <div class="flex items-center justify-end mt-6 border-t pt-6">
                            <a href="{{ route('pengajuan-pengadaan.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4 underline">Batal</a>
                            <x-primary-button name="action" value="draft">{{ __('Simpan sebagai Draft') }}</x-primary-button>
                            <x-primary-button name="action" value="submit" class="ml-2 bg-green-600 hover:bg-green-700">{{ __('Ajukan Sekarang') }}</x-primary-button>
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
            const oldItems = @json(old('items', []));
            let itemIndex = 0;

            function satuanOptions(selectedId = '') {
                return satuans.map(s => `<option value="${s.id}" ${String(selectedId)===String(s.id)?'selected':''}>${s.nama_satuan}</option>`).join('');
            }

            function createRow(item = null) {
                const idx = itemIndex++;
                const row = document.createElement('tr');
                row.classList.add('border-t');

                row.innerHTML = `
                    <td class="p-2 align-top">
                        <select name="items[${idx}][item_ref]" class="item-ref select2-bahan w-full" required>
                            <option value="">Ketik / pilih bahan...</option>
                            ${bahanList.map(b => `<option value="${b.id}" data-stock="${b.stock}" data-satuan="${b.satuan}">${b.text}</option>`).join('')}
                        </select>
                        <p class="text-xs mt-1 item-type text-blue-600">Pilih bahan existing atau ketik bahan baru.</p>
                    </td>
                    <td class="p-2 align-top text-sm item-stock text-gray-600">-</td>
                    <td class="p-2 align-top"><input type="text" name="items[${idx}][spesifikasi]" class="w-full border-gray-300 rounded-md shadow-sm" value="${item?.spesifikasi ?? ''}"></td>
                    <td class="p-2 align-top"><input type="number" step="any" min="0.001" name="items[${idx}][jumlah]" class="w-full min-w-[160px] border-gray-300 rounded-md shadow-sm" value="${item?.jumlah ?? ''}" required></td>
                    <td class="p-2 align-top"><select name="items[${idx}][id_satuan]" class="w-full border-gray-300 rounded-md shadow-sm" required>${satuanOptions(item?.id_satuan)}</select></td>
                    <td class="p-2 align-top"><input type="number" min="0" name="items[${idx}][harga_satuan]" class="w-full min-w-[170px] border-gray-300 rounded-md shadow-sm" value="${item?.harga_satuan ?? ''}" required></td>
                    <td class="p-2 align-top"><input type="url" name="items[${idx}][link_referensi]" class="w-full min-w-[220px] border-gray-300 rounded-md shadow-sm" value="${item?.link_referensi ?? ''}"></td>
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
                    const optionExists = Array.from(select.options).some(opt => opt.value === String(item.item_ref));
                    if (!optionExists) {
                        const newOption = new Option(item.item_ref, item.item_ref, true, true);
                        select.add(newOption);
                    }
                    $(select).val(String(item.item_ref)).trigger('change');
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

            if (oldItems.length) {
                oldItems.forEach(item => createRow(item));
            } else {
                createRow();
            }

            addItemBtn.addEventListener('click', () => createRow());

            container.addEventListener('click', function (e) {
                if (e.target.classList.contains('remove-item-btn')) {
                    const row = e.target.closest('tr');
                    $(row.querySelector('.item-ref')).select2('destroy');
                    row.remove();
                }
            });

            // --- BAGIAN IMPORT EXCEL DIMASUKKAN KE DALAM SINI ---
            const excelInput = document.getElementById('excel-file');
            const btnImport = document.getElementById('btn-import-excel');

            if (btnImport && excelInput) {
                btnImport.addEventListener('click', async function () {
                    if (!excelInput.files.length) {
                        alert('Silakan pilih file Excel terlebih dahulu!');
                        return;
                    }

                    const formData = new FormData();
                    formData.append('file', excelInput.files[0]);
                    formData.append('_token', '{{ csrf_token() }}');

                    btnImport.textContent = 'Memproses...';
                    btnImport.disabled = true;

                    try {
                        const response = await fetch('{{ route("pengajuan-pengadaan.parse-excel") }}', {
                            method: 'POST',
                            body: formData,
                            headers: { 'Accept': 'application/json' }
                        });

                        // KITA BACA SEBAGAI TEXT DULU BIAR BISA CEK ERRORNYA
                        const responseText = await response.text(); 
                        
                        let result;
                        try {
                            result = JSON.parse(responseText); // Coba ubah ke JSON
                        } catch (e) {
                            // Kalau gagal jadi JSON, berarti Laravel nampilin halaman error HTML
                            console.error("Respon dari server bukan JSON:", responseText);
                            alert('Backend error 500! Coba tekan F12, buka tab Console untuk lihat detailnya, atau cek storage/logs/laravel.log');
                            return;
                        }

                        if (response.ok && result.status === 'success') {
                            if (result.data.length > 0) {
                                // Hapus row kosong bawaan form
                                container.innerHTML = ''; 
                                
                                // Looping data dari Excel dan render menjadi row
                                result.data.forEach(item => {
                                    createRow(item);
                                });
                                
                                alert(`Berhasil mengimpor ${result.data.length} item. Silakan review kembali sebelum diajukan.`);
                            } else {
                                alert('File Excel kosong atau format tidak sesuai.');
                            }
                        } else {
                            alert(result.message || 'Terjadi kesalahan saat memproses file.');
                        }
                    } catch (error) {
                        console.error("Error Fetch:", error);
                        alert('Terjadi kesalahan koneksi saat mengimpor data.');
                    } finally {
                        btnImport.textContent = 'Proses Import';
                        btnImport.disabled = false;
                        excelInput.value = ''; // Reset input file
                    }
                });
            }
        });
    </script>
    @endpush
</x-app-layout>
