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
                                        <td class="px-6 py-4 font-bold">{{ $bahan->jumlah_stock }} {{ $bahan->satuan }}</td>
                                        <td class="px-6 py-4">{{ $bahan->gudang->nama_gudang }}</td>
                                        <td class="px-6 py-4">{{ $bahan->programStudi->kode_program_studi }}</td>
                                        <td class="px-6 py-4">{{ $bahan->tanggal_kedaluwarsa ? \Carbon\Carbon::parse($bahan->tanggal_kedaluwarsa)->format('d M Y') : '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            @can('update-bahan', $bahan)
                                                <a href="{{ route('bahan.edit', $bahan->id) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                            @endcan
                                            @can('delete-bahan', $bahan)
                                                <form action="{{ route('bahan.destroy', $bahan->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900 ml-4">Hapus</button>
                                                </form>
                                            @endcan
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