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
                    
                    @if (session('success'))
                        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif

                    <h3 class="text-lg font-bold">Tutup Periode Tahun Ini ({{ $tahun_aktif }})</h3>
                    <p class="mt-2 text-sm text-gray-600">
                        Proses ini akan mengunci semua transaksi di tahun {{ $tahun_aktif }}, mencatat stok akhir, dan membuat periode baru untuk tahun {{ $tahun_aktif + 1 }} dengan stok awal yang sesuai.
                        <br>
                        <strong class="text-red-600">Peringatan: Proses ini tidak dapat dibatalkan. Pastikan semua transaksi dan penyesuaian stok untuk tahun {{ $tahun_aktif }} sudah selesai.</strong>
                    </p>
                    
                    <div class="mt-6 p-4 border rounded-md {{ $bisa_tutup ? 'bg-gray-50' : 'bg-amber-50 border-amber-300' }}">
                        @if(!$bisa_tutup)
                            <div class="mb-4 text-amber-800 font-semibold text-sm flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                                Fitur Tutup Tahun hanya terbuka pada bulan Desember dan Januari untuk menghindari kesalahan pembukuan.
                            </div>
                        @endif

                        <form action="{{ route('periode.tutup') }}" method="POST" onsubmit="return confirm('APAKAH ANDA YAKIN INGIN MENUTUP PERIODE TAHUN {{ $tahun_aktif }}? PROSES INI TIDAK DAPAT DIBATALKAN!');">
                            @csrf
                            <input type="hidden" name="tahun_tutup" value="{{ $tahun_aktif }}">
                            <button type="submit" class="font-bold py-2 px-4 rounded {{ $bisa_tutup ? 'bg-red-600 hover:bg-red-700 text-white' : 'bg-gray-300 text-gray-500 cursor-not-allowed' }}" {{ !$bisa_tutup ? 'disabled' : '' }}>
                                Lakukan Tutup Tahun {{ $tahun_aktif }}
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>