{{-- resources/views/pengajuan-pengadaan/edit.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Edit Pengajuan Pengadaan') }}</h2>
    </x-slot>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
        .pp-wrap * { font-family: 'Plus Jakarta Sans', sans-serif; }

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

        #items-table thead th {
            background: #f8fafc; padding: 10px 12px;
            font-size: 11px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.05em;
            color: #6b7280; white-space: nowrap;
            border-bottom: 1px solid #e5e7eb;
        }
        #items-table tbody td { padding: 10px 10px; vertical-align: top; }
        #items-table tbody tr { border-top: 1px solid #f0f0f0; }
        #items-table tbody tr:hover { background: #fafafa; }

        .cell-input {
            border: 1.5px solid #e5e7eb; border-radius: 7px;
            padding: 7px 10px; font-size: 13px; width: 100%;
            outline: none; background: #fff;
            font-family: 'Plus Jakarta Sans', sans-serif;
            transition: border-color 0.15s;
        }
        .cell-input:focus { border-color: #6366f1; box-shadow: 0 0 0 2px rgba(99,102,241,0.07); }

        .top-btn {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 8px 16px; border-radius: 9px; font-size: 13px;
            font-weight: 600; transition: all 0.15s; white-space: nowrap;
        }
        .top-btn:hover { transform: translateY(-1px); }

        .select2-container .select2-selection--single {
            border: 1.5px solid #e5e7eb !important; border-radius: 7px !important;
            height: 35px !important; font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 33px !important; padding-left: 10px !important; font-size: 13px;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow { height: 33px !important; }
        .select2-container--default.select2-container--focus .select2-selection--single { border-color: #6366f1 !important; }
    </style>

    <div class="py-10 pp-wrap">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8 space-y-5">

            {{-- PAGE HEADER --}}
            <div class="flex items-center gap-3">
                <a href="{{ route('pengajuan-pengadaan.index') }}" class="w-9 h-9 rounded-xl bg-white border border-gray-200 shadow-sm flex items-center justify-center text-gray-400 hover:text-gray-700 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </a>
                <div>
                    <h1 class="text-2xl font-extrabold text-gray-800">Edit Pengajuan</h1>
                    <p class="text-sm text-gray-400 mt-0.5">Perbarui detail pengajuan pengadaan bahan</p>
                </div>
            </div>

            {{-- ERROR ALERT --}}
            @if ($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-800 px-5 py-4 rounded-xl">
                <p class="font-bold text-sm mb-2">Gagal menyimpan! Perbaiki kesalahan berikut:</p>
                <ul class="list-disc list-inside text-xs space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ str_replace('items.', 'Baris item ke-', $error) }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form action="{{ route('pengajuan-pengadaan.update', $pengajuanPengadaan) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- INFO PENGAJUAN --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 md:p-8">
                    <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-4">Informasi Pengajuan</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        <div>
                            <label class="form-label">Tahun Ajaran</label>
                            <input type="text" name="tahun_ajaran" value="{{ old('tahun_ajaran', $pengajuanPengadaan->tahun_ajaran) }}" placeholder="Contoh: 2024/2025" class="form-input" required>
                            @error('tahun_ajaran')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="form-label">Semester</label>
                            <select name="semester" class="form-select" required>
                                <option value="Ganjil" {{ old('semester', $pengajuanPengadaan->semester) == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                                <option value="Genap" {{ old('semester', $pengajuanPengadaan->semester) == 'Genap' ? 'selected' : '' }}>Genap</option>
                            </select>
                            @error('semester')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="form-label">Nomor Surat <span class="normal-case font-normal text-gray-400">(Opsional)</span></label>
                            <input type="text" name="nomor_surat" value="{{ old('nomor_surat', $pengajuanPengadaan->nomor_surat) }}" placeholder="Kosongkan jika belum ada" class="form-input">
                            @error('nomor_surat')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                {{-- DETAIL BARANG --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                        <h3 class="font-bold text-gray-800">Detail Barang</h3>
                        <p class="text-xs text-gray-400 mt-0.5">Cari bahan existing atau ketik nama bahan baru</p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-[1450px] w-full" id="items-table">
                            <thead>
                                <tr>
                                    <th style="min-width:220px">Bahan (existing / baru)</th>
                                    <th style="min-width:160px">Info Stok</th>
                                    <th style="min-width:160px">Spesifikasi</th>
                                    <th style="min-width:130px">Jumlah</th>
                                    <th style="min-width:120px">Satuan</th>
                                    <th style="min-width:160px">Harga Satuan (Rp)</th>
                                    <th style="min-width:200px">Link Referensi</th>
                                    <th style="width:50px"></th>
                                </tr>
                            </thead>
                            <tbody id="items-container"></tbody>
                        </table>
                    </div>
                    <div class="px-5 py-4 border-t border-gray-100">
                        <button type="button" id="add-item-btn" class="top-btn bg-gray-100 hover:bg-gray-200 text-gray-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Tambah Baris
                        </button>
                    </div>
                </div>

                {{-- FOOTER --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm px-6 py-4 flex items-center justify-between">
                    <a href="{{ route('pengajuan-pengadaan.index') }}" class="text-sm text-gray-500 hover:text-gray-700 font-medium">← Batal</a>
                    <div class="flex items-center gap-3">
                        <button type="submit" name="action" value="draft" class="top-btn bg-gray-100 hover:bg-gray-200 text-gray-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            Simpan Perubahan Draft
                        </button>
                        <button type="submit" name="action" value="submit" class="top-btn bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/></svg>
                            Simpan & Ajukan
                        </button>
                    </div>
                </div>

            </form>
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

            function normalizeNumberValue(value) {
                if (value === null || value === undefined || value === '') return '';
                const parsed = Number(value);
                return Number.isFinite(parsed) ? parsed.toString() : value;
            }

            function createRow(item = null) {
                const idx = itemIndex++;
                const row = document.createElement('tr');

                row.innerHTML = `
                    <td>
                        <select name="items[${idx}][item_ref]" class="item-ref w-full" required></select>
                        <p class="text-xs mt-1.5 item-type text-blue-500 font-medium">Pilih existing atau ketik bahan baru</p>
                    </td>
                    <td><p class="item-stock text-xs text-gray-500 leading-relaxed">—</p></td>
                    <td><input type="text" name="items[${idx}][spesifikasi]" class="cell-input" value="${item?.spesifikasi ?? ''}" placeholder="Opsional"></td>
                    <td><input type="number" step="any" min="0.001" name="items[${idx}][jumlah]" class="cell-input" value="${normalizeNumberValue(item?.jumlah)}" required placeholder="0"></td>
                    <td><select name="items[${idx}][id_satuan]" class="cell-input" required>${satuanOptions(item?.id_satuan)}</select></td>
                    <td><input type="number" min="0" name="items[${idx}][harga_satuan]" class="cell-input" value="${normalizeNumberValue(item?.harga_satuan)}" required placeholder="0"></td>
                    <td><input type="url" name="items[${idx}][link_referensi]" class="cell-input" value="${item?.link_referensi ?? ''}" placeholder="https://..."></td>
                    <td class="text-center">
                        <button type="button" class="remove-item-btn w-7 h-7 rounded-lg bg-red-50 text-red-400 hover:bg-red-100 hover:text-red-600 inline-flex items-center justify-center transition-all">
                            <svg class="w-4 h-4 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </td>
                `;

                container.appendChild(row);
                const select = row.querySelector('.item-ref');
                $(select).select2({
                    data: [{id:'', text:'Ketik / pilih bahan...'}, ...bahanList.map(b => ({id: String(b.id), text: b.text}))],
                    tags: true, placeholder: 'Ketik / pilih bahan...', width: '100%'
                });

                if (item?.item_ref) {
                    const value = String(item.item_ref);
                    const optionExists = Array.from(select.options).some(opt => opt.value === value);
                    if (!optionExists) { select.add(new Option(value, value, true, true)); }
                    $(select).val(value).trigger('change');
                }

                $(select).on('change', function () {
                    const selected = bahanList.find(b => String(b.id) === String(this.value));
                    const stockCell = row.querySelector('.item-stock');
                    const typeCell = row.querySelector('.item-type');
                    if (selected) {
                        stockCell.innerHTML = `<span class="text-emerald-700 font-semibold">Stok: ${selected.stock_text ?? (selected.stock + ' ' + selected.satuan)}</span>`;
                        typeCell.textContent = '✓ Bahan existing';
                        typeCell.className = 'text-xs mt-1.5 item-type text-emerald-600 font-medium';
                    } else if (this.value) {
                        stockCell.textContent = 'Bahan baru (belum ada di master)';
                        typeCell.textContent = '+ Bahan baru';
                        typeCell.className = 'text-xs mt-1.5 item-type text-amber-600 font-medium';
                    } else {
                        stockCell.textContent = '—';
                        typeCell.textContent = 'Pilih existing atau ketik bahan baru';
                        typeCell.className = 'text-xs mt-1.5 item-type text-blue-500 font-medium';
                    }
                });
                $(select).trigger('change');
            }

            (oldItems.length ? oldItems : [null]).forEach(item => createRow(item));
            addItemBtn.addEventListener('click', () => createRow());
            container.addEventListener('click', function (e) {
                if (e.target.closest('.remove-item-btn')) {
                    const row = e.target.closest('tr');
                    $(row.querySelector('.item-ref')).select2('destroy');
                    row.remove();
                }
            });
        });
    </script>
    @endpush
</x-app-layout>