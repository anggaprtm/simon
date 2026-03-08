{{-- resources/views/gudang/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Gudang') }}
        </h2>
    </x-slot>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
        .gudang-wrap * { font-family: 'Plus Jakarta Sans', sans-serif; }

        .action-btn {
            display: inline-flex; align-items: center; justify-content: center;
            width: 30px; height: 30px; border-radius: 7px;
            transition: all 0.15s ease;
        }
        .action-btn:hover { transform: scale(1.12); }
        .action-btn svg { width: 15px; height: 15px; }

        .top-btn {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 8px 16px; border-radius: 9px; font-size: 13px;
            font-weight: 600; transition: all 0.15s; white-space: nowrap;
        }
        .top-btn:hover { transform: translateY(-1px); filter: brightness(1.05); }

        .table-wrap { border-radius: 14px; border: 1px solid #e5e7eb; overflow: hidden; }
        thead th {
            background: #f8fafc; border-bottom: 1px solid #e5e7eb;
            padding: 11px 16px; font-size: 11px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.06em; color: #6b7280;
        }
        tbody td { padding: 13px 16px; font-size: 13.5px; color: #374151; vertical-align: middle; }
        tbody tr + tr { border-top: 1px solid #f0f0f0; }
        tbody tr:nth-child(odd) { background: #fafafa; }
        tbody tr:hover { background: #f0f4ff !important; transition: background 0.12s; }
    </style>

    <div class="py-10 gudang-wrap">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8 space-y-5">

            {{-- PAGE HEADER --}}
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-extrabold text-gray-800">Daftar Gudang</h1>
                    <p class="text-sm text-gray-400 mt-0.5">Kelola lokasi penyimpanan bahan laboratorium</p>
                </div>
                @can('create-gudang')
                <div class="flex items-center gap-2">
                    <button type="button" id="bulk-delete-gudang-button"
                        class="top-btn bg-red-100 hover:bg-red-200 text-red-700"
                        style="display:none;">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        Hapus Terpilih (<span id="selected-gudang-count">0</span>)
                    </button>
                    <a href="{{ route('gudang.create') }}" class="top-btn bg-indigo-600 hover:bg-indigo-700 text-white shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Tambah Gudang
                    </a>
                </div>
                @endcan
            </div>

            {{-- ALERTS --}}
            @if (session('success'))
            <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl text-sm font-medium">
                <svg class="w-4 h-4 flex-shrink-0 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                {{ session('success') }}
            </div>
            @endif
            @if (session('error'))
            <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl text-sm font-medium">
                <svg class="w-4 h-4 flex-shrink-0 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                {{ session('error') }}
            </div>
            @endif

            {{-- TABLE --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="table-wrap">
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr>
                                    <th class="w-10 text-center">
                                        <input type="checkbox" id="select-all-gudang-checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                    </th>
                                    <th class="text-left">No</th>
                                    <th class="text-left">Nama Gudang</th>
                                    <th>Lokasi</th>
                                    <th>Pemilik / Unit</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($gudangs as $gudang)
                                <tr>
                                    <td class="text-center">
                                        <input type="checkbox" name="selected_gudang[]" class="row-gudang-checkbox rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" value="{{ $gudang->id }}">
                                    </td>
                                    <td class="text-gray-400 font-medium text-xs">{{ $loop->iteration }}</td>
                                    <td class="text-center">
                                        <div class="flex items-center gap-2">
                                            <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center flex-shrink-0">
                                                <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                            </div>
                                            <span class="font-semibold text-gray-800 text-sm">{{ $gudang->nama_gudang }}</span>
                                        </div>
                                    </td>
                                    <td class="text-gray-500 text-sm text-center">{{ $gudang->lokasi }}</td>
                                    <td class="text-center">
                                        @if($gudang->programStudi)
                                        <span class="text-xs font-semibold bg-indigo-50 text-indigo-600 px-2.5 py-1 rounded-md">
                                            {{ $gudang->programStudi->nama_program_studi }}
                                        </span>
                                        @else
                                        <span class="text-xs font-semibold bg-gray-100 text-gray-500 px-2.5 py-1 rounded-md">
                                            Umum / Fakultas
                                        </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="flex items-center justify-center gap-1">
                                            @can('update-gudang', $gudang)
                                            <a href="{{ route('gudang.edit', $gudang->id) }}" title="Edit" class="action-btn bg-indigo-50 text-indigo-500 hover:bg-indigo-100">
                                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125"/></svg>
                                            </a>
                                            @endcan
                                            @can('delete-gudang', $gudang)
                                            <form action="{{ route('gudang.destroy', $gudang->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus gudang ini?');">
                                                @csrf @method('DELETE')
                                                <button type="submit" title="Hapus" class="action-btn bg-red-50 text-red-500 hover:bg-red-100">
                                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12.56 0c.342.052.682.107 1.022.166m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                                                </button>
                                            </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="py-16 text-center">
                                        <div class="flex flex-col items-center gap-2 text-gray-400">
                                            <svg class="w-10 h-10 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                            <p class="text-sm font-medium">Belum ada data gudang</p>
                                            <p class="text-xs">Tambahkan gudang pertama Anda</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const selectAllCheckbox = document.getElementById('select-all-gudang-checkbox');
            const rowCheckboxes = document.querySelectorAll('.row-gudang-checkbox');
            const bulkDeleteButton = document.getElementById('bulk-delete-gudang-button');
            const selectedCountSpan = document.getElementById('selected-gudang-count');

            function updateBulkDeleteButtonState() {
                if (!selectedCountSpan || !bulkDeleteButton) return;
                const selectedRows = document.querySelectorAll('.row-gudang-checkbox:checked');
                selectedCountSpan.textContent = selectedRows.length;
                bulkDeleteButton.style.display = selectedRows.length > 0 ? 'inline-flex' : 'none';
            }

            if (selectAllCheckbox) {
                selectAllCheckbox.addEventListener('change', function () {
                    rowCheckboxes.forEach(cb => { cb.checked = this.checked; });
                    updateBulkDeleteButtonState();
                });
            }

            rowCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function () {
                    if (selectAllCheckbox) {
                        if (!this.checked) { selectAllCheckbox.checked = false; }
                        else {
                            const allChecked = Array.from(rowCheckboxes).every(cb => cb.checked);
                            if (allChecked) selectAllCheckbox.checked = true;
                        }
                    }
                    updateBulkDeleteButtonState();
                });
            });

            if (bulkDeleteButton) {
                bulkDeleteButton.addEventListener('click', function () {
                    const selectedIds = Array.from(document.querySelectorAll('.row-gudang-checkbox:checked')).map(cb => cb.value);
                    if (selectedIds.length === 0) { alert('Pilih setidaknya satu gudang.'); return; }
                    if (confirm(`Hapus ${selectedIds.length} gudang yang dipilih?`)) {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = '{{ route("gudang.bulkDelete") }}';
                        const csrf = document.createElement('input');
                        csrf.type = 'hidden'; csrf.name = '_token'; csrf.value = '{{ csrf_token() }}';
                        form.appendChild(csrf);
                        selectedIds.forEach(id => {
                            const inp = document.createElement('input');
                            inp.type = 'hidden'; inp.name = 'ids[]'; inp.value = id;
                            form.appendChild(inp);
                        });
                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            }
            updateBulkDeleteButtonState();
        });
    </script>
    @endpush
</x-app-layout>