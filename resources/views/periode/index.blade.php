<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Periode Stok Opname') }}
        </h2>
    </x-slot>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
        .periode-wrap * { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>

    <div class="py-10 periode-wrap">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-5">

            {{-- PAGE HEADER --}}
            <div>
                <h1 class="text-2xl font-extrabold text-gray-800">Manajemen Periode Stok Opname</h1>
                <p class="text-sm text-gray-400 mt-0.5">Kelola penutupan periode tahunan untuk inventaris Anda</p>
            </div>

            {{-- SESSION ALERTS --}}
            @if (session('success'))
            <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl text-sm font-medium">
                <svg class="w-5 h-5 flex-shrink-0 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                {{ session('success') }}
            </div>
            @endif

            @if (session('error'))
            <div class="flex items-start gap-3 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl text-sm font-medium">
                <svg class="w-5 h-5 flex-shrink-0 mt-0.5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                <div>{{ session('error') }}</div>
            </div>
            @endif

            {{-- MAIN CARD --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="p-6 md:p-8 space-y-6">
                    
                    {{-- Informasi Periode --}}
                    <div>
                        <h3 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                            <svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            Tutup Periode Tahun Ini ({{ $tahun_aktif }})
                        </h3>
                        <p class="mt-3 text-sm text-gray-600 leading-relaxed">
                            Proses ini akan mengunci semua transaksi di tahun <strong>{{ $tahun_aktif }}</strong>, mencatat stok akhir, dan membuat periode baru untuk tahun <strong>{{ $tahun_aktif + 1 }}</strong> dengan membawa sisa stok sebagai stok awal di periode berikutnya.
                        </p>
                    </div>

                    {{-- Box Peringatan --}}
                    <div class="flex items-start gap-3 bg-red-50 border border-red-200 text-red-800 px-4 py-4 rounded-xl text-sm">
                        <svg class="w-6 h-6 flex-shrink-0 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <div>
                            <strong class="font-bold block text-base mb-1">Peringatan Penting!</strong>
                            Proses tutup tahun ini <span class="underline font-semibold">tidak dapat dibatalkan</span>. Pastikan semua transaksi (Stok Masuk, Stok Keluar) dan Penyesuaian Stok/Opname untuk tahun {{ $tahun_aktif }} sudah benar-benar selesai dilakukan.
                        </div>
                    </div>

                    {{-- Kondisi Tidak Bisa Tutup (Bulan Selain Des/Jan) --}}
                    @if(!$bisa_tutup)
                    <div class="flex items-start gap-3 bg-amber-50 border border-amber-200 text-amber-800 px-4 py-3 rounded-xl text-sm font-medium mt-4">
                        <svg class="w-5 h-5 flex-shrink-0 mt-0.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <div>
                            Fitur Tutup Tahun hanya terbuka pada bulan <strong>Desember</strong> dan <strong>Januari</strong> untuk menghindari kesalahan pembukuan pada pertengahan periode.
                        </div>
                    </div>
                    @endif

                </div>

                {{-- ACTION FOOTER --}}
                <div class="px-6 md:px-8 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-end">
                    <form action="{{ route('periode.tutup') }}" method="POST" onsubmit="return confirm('APAKAH ANDA YAKIN INGIN MENUTUP PERIODE TAHUN {{ $tahun_aktif }}?\n\nPROSES INI TIDAK DAPAT DIBATALKAN!');" class="w-full sm:w-auto">
                        @csrf
                        <input type="hidden" name="tahun_tutup" value="{{ $tahun_aktif }}">
                        
                        <button type="submit" 
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-lg text-sm font-bold shadow-sm transition-all
                            {{ $bisa_tutup ? 'bg-red-600 hover:bg-red-700 text-white hover:-translate-y-0.5 focus:ring-4 focus:ring-red-200' : 'bg-gray-200 text-gray-400 cursor-not-allowed' }}" 
                            {{ !$bisa_tutup ? 'disabled' : '' }}>
                            
                            {{-- Ikon gembok untuk menandakan Lock/Tutup --}}
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            
                            Lakukan Tutup Tahun {{ $tahun_aktif }}
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>