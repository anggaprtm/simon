{{-- resources/views/gudang/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Gudang') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @can('create-gudang')
                        <div class="flex justify-end mb-4 space-x-2">
                            <button type="button" id="bulk-delete-gudang-button" 
                                class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded" 
                                style="display:none;">
                                Hapus Terpilih (<span id="selected-gudang-count">0</span>)
                            </button>
                            <a href="{{ route('gudang.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                + Tambah Gudang
                            </a>
                        </div>
                    @endcan
                    
                    @if (session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                 <th class="px-6 py-3 text-left">
                                    <input type="checkbox" id="select-all-gudang-checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Gudang</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Lokasi</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pemilik</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($gudangs as $gudang)
                                <tr>
                                    <td class="px-6 py-4">
                                        {{-- Beri class unik untuk checkbox baris gudang --}}
                                        <input type="checkbox" name="selected_gudang[]" class="row-gudang-checkbox rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" value="{{ $gudang->id }}">
                                    </td>
                                    <td class="px-6 py-4">{{ $loop->iteration }}</td>
                                    <td class="px-6 py-4">{{ $gudang->nama_gudang }}</td>
                                    <td class="px-6 py-4">{{ $gudang->lokasi }}</td>
                                    <td class="px-6 py-4">
                                        {{ $gudang->programStudi->nama_program_studi ?? 'Umum / Fakultas' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        @can('update-gudang', $gudang)
                                            <a href="{{ route('gudang.edit', $gudang->id) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                        @endcan
                                        @can('delete-gudang', $gudang)
                                            <form action="{{ route('gudang.destroy', $gudang->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900 ml-4">Hapus</button>
                                            </form>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                        Data gudang tidak ditemukan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
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
                bulkDeleteButton.style.display = selectedRows.length > 0 ? 'inline-block' : 'none';
            }

            if (selectAllCheckbox) {
                selectAllCheckbox.addEventListener('change', function () {
                    rowCheckboxes.forEach(checkbox => {
                        checkbox.checked = this.checked;
                    });
                    updateBulkDeleteButtonState();
                });
            }

            rowCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function () {
                    if (selectAllCheckbox) {
                        if (!this.checked) {
                            selectAllCheckbox.checked = false;
                        } else {
                            const allChecked = Array.from(rowCheckboxes).every(cb => cb.checked);
                            if (allChecked) {
                                selectAllCheckbox.checked = true;
                            }
                        }
                    }
                    updateBulkDeleteButtonState();
                });
            });

            if (bulkDeleteButton) {
                bulkDeleteButton.addEventListener('click', function () {
                    const selectedIds = Array.from(document.querySelectorAll('.row-gudang-checkbox:checked'))
                                            .map(cb => cb.value);

                    if (selectedIds.length === 0) {
                        alert('Pilih setidaknya satu gudang untuk dihapus.');
                        return;
                    }

                    if (confirm(`Apakah Anda yakin ingin menghapus ${selectedIds.length} gudang yang terpilih?`)) {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = '{{ route("gudang.bulkDelete") }}'; // Action ke route bulk delete gudang

                        const csrfTokenInput = document.createElement('input');
                        csrfTokenInput.type = 'hidden';
                        csrfTokenInput.name = '_token';
                        csrfTokenInput.value = '{{ csrf_token() }}';
                        form.appendChild(csrfTokenInput);

                        selectedIds.forEach(id => {
                            const idInput = document.createElement('input');
                            idInput.type = 'hidden';
                            idInput.name = 'ids[]';
                            idInput.value = id;
                            form.appendChild(idInput);
                        });

                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            }
            updateBulkDeleteButtonState(); // Panggil di awal
        });
    </script>
    @endpush
</x-app-layout>