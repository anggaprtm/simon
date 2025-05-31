{{-- resources/views/transaksi/history.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Riwayat Transaksi') }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-4 p-4 bg-gray-100 rounded-lg">
                        <h3 class="font-bold text-lg">{{ $bahan->nama_bahan }} ({{ $bahan->merk }})</h3>
                        <p>Kode: {{ $bahan->kode_bahan }}</p>
                        <p>Stok Saat Ini: <span class="font-bold text-blue-600">{{ $bahan->jumlah_stock }} {{ $bahan->satuan }}</span></p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jenis</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jumlah</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stok Akhir</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Oleh</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($transaksis as $transaksi)
                                    <tr>
                                        <td class="px-6 py-4">{{ \Carbon\Carbon::parse($transaksi->tanggal_transaksi)->isoFormat('D MMM YYYY, HH:mm') }}</td>
                                        <td class="px-6 py-4">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ str_contains($transaksi->jenis_transaksi, 'masuk') ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ str_replace('_', ' ', $transaksi->jenis_transaksi) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">{{ $transaksi->jumlah }}</td>
                                        <td class="px-6 py-4 font-bold">{{ $transaksi->stock_sesudah }}</td>
                                        <td class="px-6 py-4">{{ $transaksi->user->name }}</td>
                                        <td class="px-6 py-4">{{ $transaksi->keterangan }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">Belum ada riwayat transaksi.</td>
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