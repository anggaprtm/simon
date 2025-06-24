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
                        {{-- Tambahkan dropdown 'tahun' di sini, mirip seperti di laporan transaksi --}}
                    </form>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    {{-- Kolom header dinamis --}}
                                    <th class="px-6 py-3 text-left ...">Kode</th>
                                    <th class="px-6 py-3 text-left ...">Nama Bahan</th>
                                    @if($selectedTahun == $tahunAktif)
                                        <th class="px-6 py-3 text-left ...">Stok Awal Periode</th>
                                        <th class="px-6 py-3 text-left ...">Stok Saat Ini</th>
                                    @else
                                        <th class="px-6 py-3 text-left ...">Stok Awal</th>
                                        <th class="px-6 py-3 text-left ...">Stok Akhir</th>
                                    @endif
                                    <th class="px-6 py-3 text-left ...">Satuan</th>
                                    <th class="px-6 py-3 text-left ...">Gudang</th>
                                    <th class="px-6 py-3 text-left ...">Prodi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($laporanData as $item)
                                    <tr>
                                        {{-- Data dinamis tergantung periode --}}
                                        @if($selectedTahun == $tahunAktif)
                                            {{-- Tampilan untuk periode aktif, $item adalah model Bahan --}}
                                            <td>{{ $item->kode_bahan }}</td>
                                            <td>{!! $item->nama_bahan_html !!}</td>
                                            <td>{{ $item->periodeAktif->stok_awal ?? 'N/A' }}</td>
                                            <td class="font-bold">{{ $item->jumlah_stock }}</td>
                                            <td>{{ $item->satuanRel->nama_satuan ?? '-' }}</td>
                                            <td>{{ $item->gudang->nama_gudang ?? '-' }}</td>
                                            <td>{{ $item->programStudi->kode_program_studi ?? '-' }}</td>
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