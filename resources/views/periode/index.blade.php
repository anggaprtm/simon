<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Periode Stok Opname') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold">Tutup Periode Tahun Ini ({{ $tahun_aktif }})</h3>
                    <p class="mt-2 text-sm text-gray-600">
                        Proses ini akan mengunci semua transaksi di tahun {{ $tahun_aktif }}, mencatat stok akhir, dan membuat periode baru untuk tahun {{ $tahun_aktif + 1 }} dengan stok awal yang sesuai.
                        <br>
                        <strong class="text-red-600">Peringatan: Proses ini tidak dapat dibatalkan. Pastikan semua transaksi dan penyesuaian stok untuk tahun {{ $tahun_aktif }} sudah selesai.</strong>
                    </p>
                    <div class="mt-4">
                        <form action="{{ route('periode.tutup') }}" method="POST" onsubmit="return confirm('APAKAH ANDA YAKIN INGIN MENUTUP PERIODE TAHUN {{ $tahun_aktif }}? PROSES INI TIDAK DAPAT DIBATALKAN!');">
                            @csrf
                            <input type="hidden" name="tahun_tutup" value="{{ $tahun_aktif }}">
                            <x-primary-button class="bg-red-700 hover:bg-red-800">
                                Lakukan Tutup Tahun {{ $tahun_aktif }}
                            </x-primary-button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>