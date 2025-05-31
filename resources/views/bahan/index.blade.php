{{-- resources/views/bahan/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Bahan Laboratorium') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{-- Tombol Tambah hanya untuk yang berhak --}}
                    @can('create-bahan')
                        <div class="flex justify-end mb-4">
                            <a href="{{ route('bahan.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                + Tambah Bahan
                            </a>
                        </div>
                    @endcan

                    {{-- Filter untuk Superadmin & Fakultas --}}
                    @if(in_array(Auth::user()->role, ['superadmin', 'fakultas']))
                        <form method="GET" action="{{ route('bahan.index') }}" class="mb-4">
                            <div class="flex items-center">
                                <select name="prodi_id" class="border-gray-300 rounded-md shadow-sm">
                                    <option value="">-- Semua Program Studi --</option>
                                    @foreach($programStudis as $prodi)
                                        <option value="{{ $prodi->id }}" {{ request('prodi_id') == $prodi->id ? 'selected' : '' }}>
                                            {{ $prodi->nama_program_studi }}
                                        </option>
                                    @endforeach
                                </select>
                                <button type="submit" class="ml-2 bg-gray-700 text-white font-bold py-2 px-4 rounded">Filter</button>
                            </div>
                        </form>
                    @endif

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
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kode</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Bahan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jenis</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stok</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Gudang</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Prodi</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kedaluwarsa</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($bahans as $bahan)
                                    <tr class="{{ $bahan->jumlah_stock <= $bahan->minimum_stock ? 'bg-red-100' : '' }}">
                                        <td class="px-6 py-4">{{ $bahan->kode_bahan }}</td>
                                        <td class="px-6 py-4">{{ $bahan->nama_bahan }} ({{ $bahan->merk }})</td>
                                        <td class="px-6 py-4">{{ $bahan->jenis_bahan ?? '-' }}</td>
                                        <td class="px-6 py-4 font-bold">{{ $bahan->jumlah_stock }} {{ $bahan->satuan }}</td>
                                        <td class="px-6 py-4">{{ $bahan->gudang->nama_gudang }}</td>
                                        <td class="px-6 py-4">{{ $bahan->programStudi->kode_program_studi }}</td>
                                        <td class="px-6 py-4">{{ $bahan->tanggal_kedaluwarsa ? \Carbon\Carbon::parse($bahan->tanggal_kedaluwarsa)->format('d M Y') : '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <x-dropdown align="left" width="48">
                                                <x-slot name="trigger">
                                                    <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                                        <div>Aksi</div>
                                                        <div class="ms-1">
                                                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                            </svg>
                                                        </div>
                                                    </button>
                                                </x-slot>

                                                <x-slot name="content">
                                                    {{-- Tombol Stok Masuk & Keluar hanya untuk Laboran --}}
                                                    @can('update-bahan', $bahan)
                                                        <x-dropdown-link :href="route('transaksi.createMasuk', $bahan->id)">
                                                            {{ __('Stok Masuk') }}
                                                        </x-dropdown-link>
                                                        <x-dropdown-link :href="route('transaksi.createKeluar', $bahan->id)">
                                                            {{ __('Stok Keluar') }}
                                                        </x-dropdown-link>
                                                    @endcan

                                                    {{-- Tombol Riwayat untuk semua yang bisa melihat --}}
                                                    <x-dropdown-link :href="route('transaksi.history', $bahan->id)">
                                                        {{ __('Riwayat') }}
                                                    </x-dropdown-link>

                                                    {{-- Tombol Edit & Hapus Metadata Bahan --}}
                                                    @can('update-bahan', $bahan)
                                                        <div class="border-t border-gray-200"></div>
                                                        <x-dropdown-link :href="route('bahan.edit', $bahan->id)">
                                                            {{ __('Edit Info Bahan') }}
                                                        </x-dropdown-link>
                                                    @endcan
                                                    @can('delete-bahan', $bahan)
                                                        <form action="{{ route('bahan.destroy', $bahan->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <x-dropdown-link :href="route('bahan.destroy', $bahan->id)" onclick="event.preventDefault(); this.closest('form').submit();">
                                                                {{ __('Hapus Bahan') }}
                                                            </x-dropdown-link>
                                                        </form>
                                                    @endcan
                                                </x-slot>
                                            </x-dropdown>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                                            Data bahan tidak ditemukan.
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
</x-app-layout>