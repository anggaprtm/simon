{{-- resources/views/laporan/transaksi.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Laporan Riwayat Transaksi') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{-- ======================= --}}
                    {{--      FORM FILTER        --}}
                    {{-- ======================= --}}
                    <form method="GET" action="{{ route('laporan.transaksi') }}" class="mb-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
                            {{-- Filter Program Studi (hanya untuk superadmin/fakultas) --}}
                            @if(in_array(Auth::user()->role, ['superadmin', 'fakultas']))
                                <div>
                                    <label for="prodi_id" class="block text-sm font-medium text-gray-700">Unit</label>
                                    <select name="prodi_id" id="prodi_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                        <option value="">-- Semua Unit --</option>
                                        @foreach($programStudis as $prodi)
                                            <option value="{{ $prodi->id }}" {{ request('prodi_id') == $prodi->id ? 'selected' : '' }}>
                                                {{ $prodi->nama_program_studi }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            {{-- Filter Tanggal Mulai --}}
                            <div>
                                <label for="tanggal_mulai" class="block text-sm font-medium text-gray-700">Dari Tanggal</label>
                                <input type="date" name="tanggal_mulai" id="tanggal_mulai" value="{{ request('tanggal_mulai') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            </div>

                            {{-- Filter Tanggal Selesai --}}
                            <div>
                                <label for="tanggal_selesai" class="block text-sm font-medium text-gray-700">Sampai Tanggal</label>
                                <input type="date" name="tanggal_selesai" id="tanggal_selesai" value="{{ request('tanggal_selesai') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            </div>

                            {{-- Tombol Aksi --}}
                            <div class="flex items-center space-x-2">
                                <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-md shadow-sm hover:bg-blue-700">Filter</button>
                                <a href="{{ route('laporan.transaksi', request()->all() + ['print' => 'true']) }}" target="_blank" class="w-full text-center bg-gray-600 text-white px-4 py-2 rounded-md shadow-sm hover:bg-gray-700">Cetak</a>
                            </div>
                        </div>
                    </form>

                    {{-- ======================= --}}
                    {{--     TABEL LAPORAN       --}}
                    {{-- ======================= --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Bahan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prodi</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Oleh</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($transaksis as $transaksi)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ \Carbon\Carbon::parse($transaksi->tanggal_transaksi)->isoFormat('D MMM Y, HH:mm') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ str_contains($transaksi->jenis_transaksi, 'masuk') ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ str_replace('_', ' ', $transaksi->jenis_transaksi) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $transaksi->bahan->nama_bahan ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $transaksi->bahan->programStudi->kode_program_studi ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $transaksi->jumlah }} {{ $transaksi->bahan->satuanRel->nama_satuan ?? '' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $transaksi->user->name ?? 'N/A' }}</td>
                                        <td class="px-6 py-4">{{ $transaksi->keterangan }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                            Data transaksi tidak ditemukan untuk filter yang dipilih.
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