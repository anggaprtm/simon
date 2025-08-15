<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detail Pengajuan Pengadaan') }}
            </h2>
            {{-- Tombol Cetak akan ada di sini --}}
            <a href="{{ route('pengajuan-pengadaan.cetakNota', $pengajuanPengadaan->id) }}" 
            target="_blank" 
            class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded inline-flex items-center">

                <!-- Icon Printer -->
                <svg xmlns="http://www.w3.org/2000/svg" 
                    fill="none" 
                    viewBox="0 0 24 24" 
                    stroke-width="1.5" 
                    stroke="currentColor" 
                    class="w-5 h-5 mr-2">
                    <path stroke-linecap="round" 
                        stroke-linejoin="round" 
                        d="M6 9V4h12v5M6 18h12v2H6v-2zM6 14h12a2 2 0 0 0 2-2V9H4v3a2 2 0 0 0 2 2z" />
                </svg>

                Cetak Nota Dinas
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{-- Detail Header Pengajuan --}}
                    <div class="grid grid-cols-2 gap-4 mb-6 border-b pb-4">
                        <div>
                            <p class="text-sm text-gray-500">Program Studi</p>
                            <p class="font-semibold">{{ $pengajuanPengadaan->programStudi->nama_program_studi }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Periode</p>
                            <p class="font-semibold">{{ $pengajuanPengadaan->tahun_ajaran }} - {{ $pengajuanPengadaan->semester }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Diajukan oleh</p>
                            <p class="font-semibold">{{ $pengajuanPengadaan->user->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Status</p>
                            <p class="font-semibold">{{ $pengajuanPengadaan->status }}</p>
                        </div>
                    </div>

                    {{-- Tabel Detail Item --}}
                    <h3 class="text-lg font-semibold mb-4">Daftar Barang yang Diajukan</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Barang</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Spesifikasi</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Harga Satuan (HPS)</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Harga (HPS)</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Link Referensi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($pengajuanPengadaan->details as $detail)
                                    <tr>
                                        <td class="px-6 py-4">{{ $loop->iteration }}</td>
                                        <td class="px-6 py-4">{{ $detail->masterBarang->nama_barang }}</td>
                                        <td class="px-6 py-4">{{ $detail->spesifikasi }}</td>
                                        <td class="px-6 py-4">{{ $detail->jumlah }} {{ $detail->satuan->nama_satuan }}</td>
                                        <td class="px-6 py-4">Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}</td>
                                        <td class="px-6 py-4">Rp {{ number_format($detail->jumlah * $detail->harga_satuan, 0, ',', '.') }}</td>
                                        <td class="px-6 py-4">
                                            @if($detail->link_referensi)
                                                <a href="{{ $detail->link_referensi }}" target="_blank" class="text-blue-600 hover:underline">
                                                    Lihat Link
                                                </a>
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @can('manage-pengajuan')
                            @if($pengajuanPengadaan->status == 'Diajukan')
                                <div class="mt-6 pt-6 border-t flex items-center justify-end space-x-4">
                                    <h3 class="text-sm font-medium text-gray-700 mr-4">Aksi Persetujuan:</h3>

                                    {{-- Tombol Tolak --}}
                                    <form action="{{ route('pengajuan-pengadaan.tolak', $pengajuanPengadaan->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin MENOLAK pengajuan ini?');">
                                        @csrf
                                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                            Tolak
                                        </button>
                                    </form>

                                    {{-- Tombol Setujui --}}
                                    <form action="{{ route('pengajuan-pengadaan.setujui', $pengajuanPengadaan->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin MENYETUJUI pengajuan ini?');">
                                        @csrf
                                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                            Setujui
                                        </button>
                                    </form>
                                </div>
                            @endif
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>