{{-- resources/views/bahan/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Bahan Habis Pakai') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{-- Tombol Tambah hanya untuk yang berhak --}}
                    @can('create-bahan')
                        <div class="flex justify-end mb-4 space-x-2">
                            <button type="button" id="bulk-delete-button" 
                                    class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded" 
                                    style="display:none;">
                                Hapus Terpilih (<span id="selected-count">0</span>)
                            </button>
                            <a href="{{ route('bahan.showImportForm') }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                Import Bahan
                            </a>
                            <a href="{{ route('bahan.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                + Tambah Bahan
                            </a>
                        </div>
                    @endcan

                    <form method="GET" action="{{ route('bahan.index') }}" class="mb-6">
                        @if(in_array(Auth::user()->role, ['superadmin', 'fakultas']))
                            {{-- Layout untuk Superadmin/Fakultas dengan filter prodi & search --}}
                            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                                <div class="md:col-span-4 lg:col-span-3">
                                    <label for="prodi_id" class="block text-sm font-medium text-gray-700">Filter Unit/Prodi</label>
                                    <select name="prodi_id" id="prodi_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="">-- Semua Unit --</option>
                                        @foreach($programStudis as $prodi)
                                            <option value="{{ $prodi->id }}" {{ $selectedProdiId == $prodi->id ? 'selected' : '' }}>
                                                {{ $prodi->nama_program_studi }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="md:col-span-5 lg:col-span-6">
                                    <label for="search" class="block text-sm font-medium text-gray-700">Cari Bahan</label>
                                    <input type="text" name="search" id="search" value="{{ $search ?? '' }}" placeholder="Kode, Nama, Merk, Jenis..." class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                                <div class="md:col-span-3 lg:col-span-3 flex items-center space-x-2">
                                    <button type="submit" class="w-full md:w-auto bg-blue-600 text-white px-4 py-2 rounded-md shadow-sm hover:bg-blue-700 flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-1 hidden md:inline-block">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                                        </svg>
                                        Cari/Filter
                                    </button>
                                    <a href="{{ route('bahan.index') }}" class="w-full md:w-auto bg-gray-300 text-gray-700 px-4 py-2 rounded-md shadow-sm hover:bg-gray-400 text-center">
                                        Reset
                                    </a>
                                </div>
                            </div>
                        @else
                            {{-- Layout untuk Laboran (tanpa filter prodi, search lebih dominan dengan Flexbox) --}}
                            <div class="flex flex-col md:flex-row md:items-end gap-3">
                                <div class="flex-grow"> {{-- Div ini akan mengambil sisa ruang yang tersedia --}}
                                    <label for="search" class="block text-sm font-medium text-gray-700">Cari Bahan</label>
                                    <input type="text" name="search" id="search" value="{{ $search ?? '' }}" placeholder="Masukkan kode, nama, merk, atau jenis bahan..." class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                                <div class="flex items-center space-x-2 mt-3 md:mt-0 shrink-0"> {{-- Tombol tidak akan mengecilkan input --}}
                                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md shadow-sm hover:bg-blue-700 flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-1 hidden sm:inline-block"> {{-- Ubah md jadi sm --}}
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                                        </svg>
                                        Cari
                                    </button>
                                    <a href="{{ route('bahan.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md shadow-sm hover:bg-gray-400 text-center">
                                        Reset
                                    </a>
                                </div>
                            </div>
                        @endif
                    </form>

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

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>

                                    <th class="px-6 py-3 text-left">
                                        <input type="checkbox" id="select-all-checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Bahan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jenis</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stok</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Gudang</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Unit/Prodi</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kedaluwarsa</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($bahans as $bahan)
                                    <tr class=" @if ($bahan->jumlah_stock <= $bahan->minimum_stock && $bahan->jumlah_stock > 0)
                                                bg-red-100 hover:bg-red-200 {{-- Warna untuk stok menipis --}}
                                            @elseif ($loop->odd)
                                                bg-gray-50 hover:bg-gray-100 {{-- Warna untuk baris ganjil --}}
                                            @else
                                                bg-white hover:bg-gray-100 {{-- Warna untuk baris genap (atau default) --}}
                                            @endif
                                            transition-colors duration-150 ease-in-out {{-- Efek transisi halus untuk hover --}}
                                    ">
                                        <td class="px-6 py-4">
                                            <input type="checkbox" name="selected_bahan[]" class="row-checkbox rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" value="{{ $bahan->id }}">
                                        </td>
                                        <td class="px-6 py-4">{{ $bahan->kode_bahan }}</td>
                                        <td class="px-6 py-4">{{ $bahan->nama_bahan }} ({{ $bahan->merk }})</td>
                                        <td class="px-6 py-4">{{ $bahan->jenis_bahan ?? '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap font-bold">{{ $bahan->jumlah_stock }} {{ $bahan->satuanRel->nama_satuan ?? '-' }}</td>
                                        <td class="px-6 py-4">{{ $bahan->gudang->nama_gudang }}</td>
                                        <td class="px-6 py-4">{{ $bahan->programStudi->kode_program_studi }}</td>
                                        <td class="px-6 py-4">{{ $bahan->tanggal_kedaluwarsa ? \Carbon\Carbon::parse($bahan->tanggal_kedaluwarsa)->format('d M Y') : '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex items-center space-x-2"> 
                                                {{-- Tombol Stok Masuk & Keluar hanya untuk Laboran --}}
                                                @can('update-bahan', $bahan)
                                                    <a href="{{ route('transaksi.createMasuk', $bahan->id) }}" title="Stok Masuk" class="text-green-600 hover:text-green-900">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                                        </svg>
                                                    </a>
                                                    <a href="{{ route('transaksi.createKeluar', $bahan->id) }}" title="Stok Keluar" class="text-red-600 hover:text-red-900">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                                        </svg>
                                                    </a>
                                                    <a href="{{ route('penyesuaian.create', $bahan->id) }}" title="Penyesuaian Stok" class="text-orange-600 hover:text-orange-900">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75" />
                                                        </svg>
                                                    </a>
                                                @endcan

                                                {{-- Tombol Riwayat untuk semua yang bisa melihat --}}
                                                <a href="{{ route('transaksi.history', $bahan->id) }}" title="Riwayat" class="text-blue-600 hover:text-blue-900">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                                                    </svg>
                                                </a>

                                                {{-- Tombol Edit & Hapus Metadata Bahan --}}
                                                @can('update-bahan', $bahan)
                                                    <a href="{{ route('bahan.edit', $bahan->id) }}" title="Edit Info Bahan" class="text-indigo-600 hover:text-indigo-900">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                                                        </svg>
                                                    </a>
                                                @endcan
                                                @can('delete-bahan', $bahan)
                                                    <form action="{{ route('bahan.destroy', $bahan->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus?');" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" title="Hapus Bahan" class="text-red-600 hover:text-red-900">
                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12.56 0c.342.052.682.107 1.022.166m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                            </svg>
                                                        </button>
                                                    </form>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">
                                            Data bahan tidak ditemukan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-6">
                        {{ $bahans->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const selectAllCheckbox = document.getElementById('select-all-checkbox');
            const rowCheckboxes = document.querySelectorAll('.row-checkbox');
            const bulkDeleteButton = document.getElementById('bulk-delete-button');
            const selectedCountSpan = document.getElementById('selected-count');

            function updateBulkDeleteButtonState() {
                const selectedRows = document.querySelectorAll('.row-checkbox:checked');
                selectedCountSpan.textContent = selectedRows.length;
                if (selectedRows.length > 0) {
                    bulkDeleteButton.style.display = 'inline-block';
                } else {
                    bulkDeleteButton.style.display = 'none';
                }
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
                    if (!this.checked) {
                        selectAllCheckbox.checked = false;
                    } else {
                        // Cek apakah semua row-checkbox terpilih
                        const allChecked = Array.from(rowCheckboxes).every(cb => cb.checked);
                        if (allChecked && selectAllCheckbox) {
                            selectAllCheckbox.checked = true;
                        }
                    }
                    updateBulkDeleteButtonState();
                });
            });

            if (bulkDeleteButton) {
                bulkDeleteButton.addEventListener('click', function () {
                    const selectedIds = Array.from(document.querySelectorAll('.row-checkbox:checked'))
                                            .map(cb => cb.value);

                    if (selectedIds.length === 0) {
                        alert('Pilih setidaknya satu bahan untuk dihapus.');
                        return;
                    }

                    if (confirm(`Apakah Anda yakin ingin menghapus ${selectedIds.length} bahan yang terpilih?`)) {
                        // Buat form dinamis untuk mengirim data
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = '{{ route("bahan.bulkDelete") }}';

                        const csrfTokenInput = document.createElement('input');
                        csrfTokenInput.type = 'hidden';
                        csrfTokenInput.name = '_token';
                        csrfTokenInput.value = '{{ csrf_token() }}'; // Ambil CSRF token dari Blade
                        form.appendChild(csrfTokenInput);

                        selectedIds.forEach(id => {
                            const idInput = document.createElement('input');
                            idInput.type = 'hidden';
                            idInput.name = 'ids[]'; // Kirim sebagai array
                            idInput.value = id;
                            form.appendChild(idInput);
                        });

                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            }
            // Panggil sekali di awal untuk set state tombol
            updateBulkDeleteButtonState();
        });
    </script>
    @endpush
</x-app-layout>