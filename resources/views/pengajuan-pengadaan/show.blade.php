<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center gap-3">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Detail Pengajuan Pengadaan') }}</h2>
            <div class="flex items-center gap-2">
                @if(Auth::id() === $pengajuanPengadaan->id_user && $pengajuanPengadaan->status === 'Draft')
                    <a href="{{ route('pengajuan-pengadaan.edit', $pengajuanPengadaan) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">Edit Draft</a>
                    <form action="{{ route('pengajuan-pengadaan.ajukanFinal', $pengajuanPengadaan) }}" method="POST" onsubmit="return confirm('Ajukan draft ini untuk direview Fakultas?');" class="inline">
                        @csrf
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">Ajukan Final</button>
                    </form>
                @endif


                @if(Auth::id() === $pengajuanPengadaan->id_user && $pengajuanPengadaan->status === 'Disetujui')
                    <a href="{{ route('pengajuan-pengadaan.realisasiForm', $pengajuanPengadaan) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded inline-flex items-center">Input Stok Masuk</a>
                @endif

                @if($pengajuanPengadaan->status !== 'Draft')
                    <a href="{{ route('pengajuan-pengadaan.cetakNota', $pengajuanPengadaan->id) }}" target="_blank" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded inline-flex items-center">Cetak Nota Dinas</a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if (session('success'))
                        <div class="mb-4 rounded border border-green-300 bg-green-50 p-3 text-green-700">{{ session('success') }}</div>
                    @endif
                    @if (session('error'))
                        <div class="mb-4 rounded border border-red-300 bg-red-50 p-3 text-red-700">{{ session('error') }}</div>
                    @endif

                    <div class="grid grid-cols-2 gap-4 mb-6 border-b pb-4">
                        <div><p class="text-sm text-gray-500">Program Studi</p><p class="font-semibold">{{ $pengajuanPengadaan->programStudi->nama_program_studi }}</p></div>
                        <div><p class="text-sm text-gray-500">Periode</p><p class="font-semibold">{{ $pengajuanPengadaan->tahun_ajaran }} - {{ $pengajuanPengadaan->semester }}</p></div>
                        <div><p class="text-sm text-gray-500">Diajukan oleh</p><p class="font-semibold">{{ $pengajuanPengadaan->user->name }}</p></div>
                        <div><p class="text-sm text-gray-500">Status</p><p class="font-semibold">{{ $pengajuanPengadaan->status }}</p></div>
                    </div>

                    <h3 class="text-lg font-semibold mb-4">Daftar Barang yang Diajukan</h3>

                    @can('manage-pengajuan')
                        @if($pengajuanPengadaan->status === 'Diajukan')
                            <form action="{{ route('pengajuan-pengadaan.setujui', $pengajuanPengadaan) }}" method="POST">
                                @csrf
                        @endif
                    @endcan

                    @php
                        $formatQty = function ($value) {
                            $formatted = number_format((float) ($value ?? 0), 3, ',', '.');
                            return rtrim(rtrim($formatted, '0'), ',');
                        };
                        $formatRupiah = function ($value) {
                            return 'Rp ' . number_format((float) ($value ?? 0), 0, ',', '.');
                        };
                    @endphp

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Barang</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Keterangan</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Diajukan</th>
                                    @if($pengajuanPengadaan->status !== 'Draft')
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Disetujui</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status Item</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Catatan</th>
                                    @endif
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Harga Satuan</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($pengajuanPengadaan->details as $detail)
                                    @php
                                        $isExisting = !is_null($detail->id_bahan);
                                        $stokSaatIni = $isExisting ? $detail->bahan?->formatted_stock : null;
                                    @endphp
                                    <tr>
                                        <td class="px-4 py-3">{{ $loop->iteration }}</td>
                                        <td class="px-4 py-3">
                                            <div class="font-medium">{{ $detail->display_nama_barang }}</div>
                                            @if(!$isExisting)
                                                <span class="text-xs bg-amber-100 text-amber-700 px-2 py-1 rounded">Pengajuan bahan baru</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-600">
                                            <div>{{ $detail->spesifikasi ?: '-' }}</div>
                                            @if($isExisting)
                                                <div class="mt-1 text-green-700">Stok saat ini: {{ $stokSaatIni }}</div>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3">{{ $formatQty($detail->jumlah) }} {{ $detail->satuan->nama_satuan }}</td>
                                        @if($pengajuanPengadaan->status !== 'Draft')
                                            <td class="px-4 py-3">
                                                @can('manage-pengajuan')
                                                    @if($pengajuanPengadaan->status === 'Diajukan')
                                                        <input type="number" name="approval_items[{{ $detail->id }}][approved_jumlah]" step="any" min="0" max="{{ $detail->jumlah }}" value="{{ $detail->jumlah }}" class="w-28 border-gray-300 rounded-md shadow-sm">
                                                    @else
                                                        {{ $formatQty($detail->approved_jumlah) }} {{ $detail->satuan->nama_satuan }}
                                                    @endif
                                                @else
                                                    {{ $formatQty($detail->approved_jumlah) }} {{ $detail->satuan->nama_satuan }}
                                                @endcan
                                            </td>
                                            <td class="px-4 py-3">
                                                @can('manage-pengajuan')
                                                    @if($pengajuanPengadaan->status === 'Diajukan')
                                                        <select name="approval_items[{{ $detail->id }}][status_item]" class="border-gray-300 rounded-md shadow-sm">
                                                            <option value="disetujui">Setujui Penuh</option>
                                                            <option value="disetujui_sebagian">Setujui Sebagian/Revisi</option>
                                                            <option value="ditolak">Tolak Item</option>
                                                        </select>
                                                    @else
                                                        {{ ucfirst(str_replace('_', ' ', $detail->status_item)) }}
                                                    @endif
                                                @else
                                                    {{ ucfirst(str_replace('_', ' ', $detail->status_item)) }}
                                                @endcan
                                            </td>
                                            <td class="px-4 py-3">
                                                @can('manage-pengajuan')
                                                    @if($pengajuanPengadaan->status === 'Diajukan')
                                                        <input type="text" name="approval_items[{{ $detail->id }}][catatan_revisi]" class="w-full border-gray-300 rounded-md shadow-sm" value="{{ $detail->catatan_revisi }}" placeholder="Opsional">
                                                    @else
                                                        {{ $detail->catatan_revisi ?: '-' }}
                                                    @endif
                                                @else
                                                    {{ $detail->catatan_revisi ?: '-' }}
                                                @endcan
                                            </td>
                                        @endif
                                        <td class="px-4 py-3">{{ $formatRupiah($detail->harga_satuan) }}</td>
                                        <td class="px-4 py-3 font-medium">{{ $formatRupiah(($detail->harga_satuan ?? 0) * ($detail->jumlah ?? 0)) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @can('manage-pengajuan')
                        @if($pengajuanPengadaan->status === 'Diajukan')
                            <div class="mt-6 pt-6 border-t flex items-center justify-end space-x-3">
                                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">Simpan Keputusan Approval</button>
                            </form>

                            <form action="{{ route('pengajuan-pengadaan.tolak', $pengajuanPengadaan) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menolak seluruh pengajuan ini?');">
                                @csrf
                                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">Tolak Semua</button>
                            </form>
                            </div>
                        @endif
                    @endcan
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
