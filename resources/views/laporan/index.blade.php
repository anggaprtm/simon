<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pusat Laporan & Analitik') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            
            {{-- BANNER PENGINGAT (Hanya muncul jika belum upload bulan ini) --}}
            @if(isset($belumUpload) && $belumUpload)
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg shadow-sm flex items-start md:items-center justify-between flex-col md:flex-row gap-4">
                <div class="flex items-center gap-3">
                    <svg class="h-8 w-8 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <div>
                        <h3 class="text-red-800 font-bold text-lg">Peringatan Kepatuhan Laporan</h3>
                        <p class="text-red-700 text-sm">Anda belum mengunggah Arsip Laporan (PDF bertandatangan) untuk bulan ini. Segera lakukan Stock Opname rutin dan unggah arsipnya.</p>
                    </div>
                </div>
                <a href="{{ route('laporan.arsip') }}" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded whitespace-nowrap text-sm">
                    Unggah Arsip Sekarang
                </a>
            </div>
            @endif

            {{-- MENU NAVIGASI CEPAT --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <a href="{{ route('laporan.stok') }}" class="flex items-center p-4 bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-md transition-shadow">
                    <div class="p-3 mr-4 bg-blue-100 text-blue-600 rounded-full">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                    </div>
                    <div>
                        <p class="mb-1 text-lg font-bold text-gray-900">Laporan Stok</p>
                        <p class="text-xs text-gray-500">Rekapitulasi stok saat ini</p>
                    </div>
                </a>
                
                <a href="{{ route('laporan.transaksi') }}" class="flex items-center p-4 bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-md transition-shadow">
                    <div class="p-3 mr-4 bg-green-100 text-green-600 rounded-full">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    </div>
                    <div>
                        <p class="mb-1 text-lg font-bold text-gray-900">Riwayat Transaksi</p>
                        <p class="text-xs text-gray-500">Histori mutasi barang</p>
                    </div>
                </a>

                <a href="{{ route('laporan.arsip') }}" class="flex items-center p-4 bg-indigo-50 border border-indigo-200 rounded-xl shadow-sm hover:shadow-md transition-shadow">
                    <div class="p-3 mr-4 bg-indigo-200 text-indigo-700 rounded-full">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <div>
                        <p class="mb-1 text-lg font-bold text-indigo-900">Arsip Bulanan</p>
                        <p class="text-xs text-indigo-600">Unggah & Unduh PDF legal</p>
                    </div>
                </a>
            </div>

            {{-- VISUALISASI DATA SEDERHANA --}}
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Aktivitas Laboratorium Bulan Ini</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-gray-50 p-4 rounded-lg flex items-center justify-between border border-gray-100">
                        <div>
                            <p class="text-sm text-gray-500 font-medium">Total Transaksi Masuk</p>
                            <h4 class="text-2xl font-bold text-gray-900">{{ $totalMasuk ?? 0 }} <span class="text-sm font-normal text-gray-500">Aktivitas</span></h4>
                        </div>
                        <div class="text-green-500 bg-green-100 p-3 rounded-full">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                        </div>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg flex items-center justify-between border border-gray-100">
                        <div>
                            <p class="text-sm text-gray-500 font-medium">Total Transaksi Keluar</p>
                            <h4 class="text-2xl font-bold text-gray-900">{{ $totalKeluar ?? 0 }} <span class="text-sm font-normal text-gray-500">Aktivitas</span></h4>
                        </div>
                        <div class="text-red-500 bg-red-100 p-3 rounded-full">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>