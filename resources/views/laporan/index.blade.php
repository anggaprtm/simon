{{-- resources/views/laporan/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pusat Laporan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <a href="{{ route('laporan.stok') }}" class="block p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-100">
                    <h3 class="mb-2 text-2xl font-bold tracking-tight text-gray-900">Laporan Stok Bahan</h3>
                    <p class="font-normal text-gray-700">Lihat rekapitulasi jumlah stok semua bahan laboratorium pada saat ini. Dapat difilter berdasarkan program studi.</p>
                </a>
                
                <a href="{{ route('laporan.transaksi') }}" class="block p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-100">
                    <h3 class="mb-2 text-2xl font-bold tracking-tight text-gray-900">Laporan Riwayat Transaksi</h3>
                    <p class="font-normal text-gray-700">Lihat semua catatan aktivitas barang masuk dan keluar. Dapat difilter berdasarkan program studi dan rentang tanggal.</p>
                </a>
            </div>
        </div>
    </div>
</x-app-layout>