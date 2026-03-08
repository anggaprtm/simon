{{-- resources/views/laporan/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Pusat Laporan & Analitik') }}</h2>
    </x-slot>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
        .lp-wrap * { font-family: 'Plus Jakarta Sans', sans-serif; }
        .nav-card { display:flex; align-items:center; gap:16px; padding:18px 20px; background:#fff; border:1.5px solid #e5e7eb; border-radius:16px; transition:all .18s; }
        .nav-card:hover { border-color:#6366f1; box-shadow:0 6px 20px -6px rgba(99,102,241,.18); transform:translateY(-2px); }
        .nav-card.indigo-active { background:#eef2ff; border-color:#6366f1; }
    </style>

    <div class="py-10 lp-wrap">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8 space-y-6">

            <div>
                <h1 class="text-2xl font-extrabold text-gray-800">Pusat Laporan & Analitik</h1>
                <p class="text-sm text-gray-400 mt-0.5">Pantau aktivitas dan ekspor laporan laboratorium</p>
            </div>

            @if(isset($belumUpload) && $belumUpload)
            <div class="flex items-start md:items-center justify-between gap-4 bg-red-50 border border-red-200 rounded-2xl p-5 flex-col md:flex-row">
                <div class="flex items-start gap-3">
                    <div class="w-9 h-9 rounded-xl bg-red-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <div>
                        <p class="font-bold text-red-800 text-sm">Peringatan Kepatuhan Laporan</p>
                        <p class="text-red-600 text-xs mt-0.5">Anda belum mengunggah Arsip Laporan (PDF bertandatangan) untuk bulan ini. Segera lakukan Stock Opname rutin dan unggah arsipnya.</p>
                    </div>
                </div>
                <a href="{{ route('laporan.arsip') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-red-600 hover:bg-red-700 text-white text-xs font-bold transition-all flex-shrink-0 whitespace-nowrap">
                    Unggah Arsip Sekarang
                </a>
            </div>
            @endif

            {{-- STATISTIK BULAN INI --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-1">Transaksi Masuk Bulan Ini</p>
                        <p class="text-3xl font-extrabold text-gray-800">{{ $totalMasuk ?? 0 }}</p>
                        <p class="text-xs text-gray-400 mt-1">aktivitas stok masuk</p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    </div>
                </div>
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-1">Transaksi Keluar Bulan Ini</p>
                        <p class="text-3xl font-extrabold text-gray-800">{{ $totalKeluar ?? 0 }}</p>
                        <p class="text-xs text-gray-400 mt-1">aktivitas stok keluar</p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-red-50 flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    </div>
                </div>
            </div>

            {{-- MENU NAVIGASI --}}
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Menu Laporan</p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                    <a href="{{ route('laporan.stok') }}" class="nav-card">
                        <div class="w-11 h-11 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-bold text-gray-800">Laporan Stok</p>
                            <p class="text-xs text-gray-400 mt-0.5">Rekapitulasi stok awal & akhir per periode</p>
                        </div>
                        <svg class="w-4 h-4 text-gray-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>

                    <a href="{{ route('laporan.transaksi') }}" class="nav-card">
                        <div class="w-11 h-11 rounded-xl bg-emerald-50 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-bold text-gray-800">Riwayat Transaksi</p>
                            <p class="text-xs text-gray-400 mt-0.5">Histori mutasi stok masuk & keluar</p>
                        </div>
                        <svg class="w-4 h-4 text-gray-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>

                    <a href="{{ route('laporan.arsip') }}" class="nav-card indigo-active">
                        <div class="w-11 h-11 rounded-xl bg-indigo-100 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-bold text-indigo-800">Arsip Bulanan</p>
                            <p class="text-xs text-indigo-500 mt-0.5">Unggah & unduh PDF bertandatangan</p>
                        </div>
                        <svg class="w-4 h-4 text-indigo-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>