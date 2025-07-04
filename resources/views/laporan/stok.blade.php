{{-- resources/views/laporan/stok.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Laporan Stok Bahan') }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8"> {{-- Dibuat lebih lebar --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{-- Form Filter dengan filter tahun --}}
                    <form method="GET" action="{{ route('laporan.stok') }}" class="mb-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
                            {{-- Filter Program Studi --}}
                            @if(in_array(Auth::user()->role, ['superadmin', 'fakultas']))
                                <div>
                                    <label for="prodi_id" class="block text-sm font-medium text-gray-700">Program Studi</label>
                                    <select name="prodi_id" id="prodi_id" class="mt-1 block w-full ...">
                                        <option value="">-- Semua Prodi --</option>
                                        @foreach($programStudis as $prodi)
                                            <option value="{{ $prodi->id }}" {{ $selectedProdiId == $prodi->id ? 'selected' : '' }}>
                                                {{ $prodi->nama_program_studi }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                            
                             <div>
                                <label for="tahun" class="block text-sm font-medium text-gray-700">Tahun Periode</label>
                                <select name="tahun" id="tahun" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                    <option value="">-- Semua Tahun --</option>
                                    @foreach($availableYears as $year)
                                        <option value="{{ $year }}" {{ $selectedTahun == $year ? 'selected' : '' }}>
                                            {{ $year }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            {{-- Tombol Aksi --}}
                            <div class="flex items-center space-x-2">
                                <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-md shadow-sm hover:bg-blue-700">Filter</button>
                                <a href="{{ route('laporan.stok', request()->all() + ['print' => 'true']) }}" target="_blank" class="w-full text-center bg-gray-600 text-white px-4 py-2 rounded-md shadow-sm hover:bg-gray-700">Cetak</a>
                            </div>
                        </div>
                    </form>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    {{-- Kolom header dinamis --}}
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="text-align: center">Kode</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="text-align: center">Nama Bahan</th>
                                    @if($selectedTahun == $tahunAktif)
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="text-align: center">Stok Awal Periode</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="text-align: center">Stok Saat Ini</th>
                                    @else
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="text-align: center">Stok Awal</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="text-align: center">Stok Akhir</th>
                                    @endif
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="text-align: center">Satuan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="text-align: center">Gudang</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="text-align: center">Prodi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($laporanData as $item)
                                    <tr>
                                        {{-- Data dinamis tergantung periode --}}
                                        @if($selectedTahun == $tahunAktif)
                                            {{-- Tampilan untuk periode aktif, $item adalah model Bahan --}}
                                            <td style="text-align: center">{{ $item->kode_bahan }}</td>
                                            <td>{!! $item->nama_bahan_html !!}</td>
                                            <td style="text-align: center">{{ $item->periodeAktif->stok_awal ?? 'N/A' }}</td>
                                            <td class="font-bold" style="text-align: center">{{ $item->jumlah_stock }}</td>
                                            <td style="text-align: center">{{ $item->satuanRel->nama_satuan ?? '-' }}</td>
                                            <td style="text-align: center">{{ $item->gudang->nama_gudang ?? '-' }}</td>
                                            <td style="text-align: center">{{ $item->programStudi->kode_program_studi ?? '-' }}</td>
                                        @else
                                            {{-- Tampilan untuk periode tertutup, $item adalah model PeriodeStok --}}
                                            <td>{{ $item->bahan->kode_bahan }}</td>
                                            <td>{!! $item->bahan->nama_bahan_html !!}</td>
                                            <td>{{ $item->stok_awal }}</td>
                                            <td class="font-bold">{{ $item->stok_akhir }}</td>
                                            <td>{{ $item->bahan->satuanRel->nama_satuan ?? '-' }}</td>
                                            <td>{{ $item->bahan->gudang->nama_gudang ?? '-' }}</td>
                                            <td>{{ $item->bahan->programStudi->kode_program_studi ?? '-' }}</td>
                                        @endif
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">Data tidak ditemukan.</td>
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