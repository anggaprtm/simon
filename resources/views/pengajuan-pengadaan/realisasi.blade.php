<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Realisasi Stok dari Pengajuan Disetujui') }}</h2>
            <a href="{{ route('pengajuan-pengadaan.show', $pengajuanPengadaan) }}" class="text-sm text-gray-600 underline">Kembali ke Detail</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if (session('success'))
                        <div class="mb-4 rounded border border-green-300 bg-green-50 p-3 text-green-700">{{ session('success') }}</div>
                    @endif
                    @if (session('error'))
                        <div class="mb-4 rounded border border-red-300 bg-red-50 p-3 text-red-700">{{ session('error') }}</div>
                    @endif

                    <p class="mb-4 text-sm text-gray-600">Proses realisasi dilakukan per item untuk mencegah salah satuan. Jika satuan pengajuan berbeda dengan satuan bahan existing, isi nilai konversi (contoh: 1 botol = 5 L).</p>

                    <div class="space-y-5">
                        @foreach($pengajuanPengadaan->details as $detail)
                            @php
                                $eligible = in_array($detail->status_item, ['disetujui', 'disetujui_sebagian']) && (float)$detail->approved_jumlah > 0;
                                $isExisting = !is_null($detail->id_bahan);
                                $needConversion = $isExisting && ((int)$detail->id_satuan !== (int)($detail->bahan->id_satuan ?? 0));
                            @endphp

                            <div class="border rounded-lg p-4 {{ $detail->is_direalisasi ? 'bg-green-50 border-green-200' : '' }}">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <h3 class="font-semibold">{{ $detail->display_nama_barang }}</h3>
                                        <p class="text-sm text-gray-600">Disetujui: {{ rtrim(rtrim(number_format((float)$detail->approved_jumlah, 3, ',', '.'), '0'), ',') }} {{ $detail->satuan->nama_satuan }}</p>
                                        <p class="text-sm text-gray-600">Status item: {{ ucfirst(str_replace('_', ' ', $detail->status_item)) }}</p>
                                        @if($isExisting)
                                            <p class="text-sm text-blue-700">Bahan existing (satuan stok: {{ $detail->bahan->satuanRel->nama_satuan ?? '-' }})</p>
                                        @else
                                            <p class="text-sm text-amber-700">Bahan baru (belum ada di master bahan)</p>
                                        @endif
                                    </div>
                                    @if($detail->is_direalisasi)
                                        <span class="text-xs bg-green-200 text-green-800 px-2 py-1 rounded">Sudah direalisasi</span>
                                    @endif
                                </div>

                                @if($eligible && ! $detail->is_direalisasi)
                                    <form action="{{ route('pengajuan-pengadaan.realisasiItem', [$pengajuanPengadaan, $detail]) }}" method="POST" class="mt-4 space-y-3">
                                        @csrf

                                        @if($isExisting)
                                            @if($needConversion)
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700">Nilai konversi ke satuan stok (wajib)</label>
                                                    <input type="number" name="konversi_nilai" step="any" min="0.000001" class="mt-1 border-gray-300 rounded-md shadow-sm w-64" placeholder="Contoh: 5">
                                                </div>
                                            @else
                                                <p class="text-sm text-green-700">Satuan sama, konversi otomatis = 1.</p>
                                            @endif
                                        @else
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                                <div>
                                                    <label class="block text-sm">Nama Bahan Baru</label>
                                                    <input type="text" name="nama_bahan_baru" value="{{ $detail->display_nama_barang }}" class="mt-1 border-gray-300 rounded-md shadow-sm w-full" required>
                                                </div>
                                                <div>
                                                    <label class="block text-sm">Merk</label>
                                                    <input type="text" name="merk_baru" class="mt-1 border-gray-300 rounded-md shadow-sm w-full">
                                                </div>
                                                <div>
                                                    <label class="block text-sm">Jenis Bahan</label>
                                                    <input type="text" name="jenis_bahan_baru" class="mt-1 border-gray-300 rounded-md shadow-sm w-full">
                                                </div>
                                                <div>
                                                    <label class="block text-sm">Gudang</label>
                                                    <select name="id_gudang" class="mt-1 border-gray-300 rounded-md shadow-sm w-full" required>
                                                        <option value="">Pilih Gudang</option>
                                                        @foreach($gudangs as $g)
                                                            <option value="{{ $g->id }}">{{ $g->nama_gudang }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div>
                                                    <label class="block text-sm">Satuan Bahan Baru</label>
                                                    <select name="id_satuan_baru" class="mt-1 border-gray-300 rounded-md shadow-sm w-full" required>
                                                        <option value="">Pilih Satuan</option>
                                                        @foreach($satuans as $satuan)
                                                            <option value="{{ $satuan->id }}">{{ $satuan->nama_satuan }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div>
                                                    <label class="block text-sm">Minimum Stock</label>
                                                    <input type="number" name="minimum_stock_baru" min="0" step="any" class="mt-1 border-gray-300 rounded-md shadow-sm w-full" value="0">
                                                </div>
                                                <div>
                                                    <label class="block text-sm">Qty Realisasi Masuk</label>
                                                    <input type="number" name="qty_realisasi" step="any" min="0.000001" class="mt-1 border-gray-300 rounded-md shadow-sm w-full" value="{{ $detail->approved_jumlah }}" required>
                                                </div>
                                            </div>
                                        @endif

                                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Proses Realisasi Item</button>
                                    </form>
                                @elseif(!$eligible)
                                    <p class="mt-3 text-sm text-gray-500">Item ini tidak memenuhi syarat realisasi (bukan item disetujui / jumlah disetujui 0).</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
