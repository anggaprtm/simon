<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("Selamat Datang Kembali,") }} <span class="font-bold">{{ Auth::user()->name }}!</span>
                </div>
            </div>

            {{-- ============================================= --}}
            {{--           TAMPILAN UNTUK SUPERADMIN           --}}
            {{-- ============================================= --}}
            @if(in_array(Auth::user()->role, ['superadmin', 'fakultas']))
            <div class="mt-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <h3 class="font-bold text-lg">Total Jenis Bahan</h3>
                        <p class="text-3xl font-bold text-blue-600">{{ $data['total_bahan'] }}</p>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <h3 class="font-bold text-lg">Total Unit</h3>
                        <p class="text-3xl font-bold text-green-600">{{ $data['total_prodi'] }}</p>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <h3 class="font-bold text-lg">Total Pengguna</h3>
                        <p class="text-3xl font-bold text-indigo-600">{{ $data['total_user'] }}</p>
                    </div>
                </div>

                <div class="mt-8">
                    <h3 class="text-xl font-bold mb-4">Ringkasan per Unit</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach($data['ringkasan_prodi'] as $prodi)
                        <div class="bg-white p-4 rounded-lg shadow-md">
                            <h4 class="font-bold text-lg">{{ $prodi->nama_program_studi }}</h4>
                            <div class="mt-2 flex justify-between text-sm">
                                <span>Jumlah Bahan: <span class="font-semibold">{{ $prodi->bahans_count }}</span></span>
                                <span>Jumlah Petugas/Laboran: <span class="font-semibold">{{ $prodi->users_count }}</span></span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            {{-- ============================================= --}}
            {{--           TAMPILAN UNTUK LABORAN & KPS        --}}
            {{-- ============================================= --}}
            @if(in_array(Auth::user()->role, ['laboran', 'kps']))
            <div class="mt-6 space-y-6">
                
                {{-- BANNER PERINGATAN KRITIS --}}
                @if($data['belum_upload_arsip'])
                <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg shadow-sm flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <svg class="h-6 w-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                        <div>
                            <p class="text-red-800 font-bold">Peringatan Kepatuhan!</p>
                            <p class="text-red-700 text-sm">Anda belum mengunggah Arsip Laporan fisik (PDF bertandatangan) untuk bulan ini.</p>
                        </div>
                    </div>
                    <a href="{{ route('laporan.arsip') }}" class="text-sm bg-red-600 hover:bg-red-700 text-white py-2 px-4 rounded font-semibold transition-colors">Unggah Sekarang</a>
                </div>
                @endif

                @if($data['bisa_tutup_tahun'] && Auth::user()->role === 'kps')
                <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded-lg shadow-sm flex items-center gap-3">
                    <svg class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                    <div>
                        <p class="text-yellow-800 font-bold">Waktu Tutup Tahun Buku</p>
                        <p class="text-yellow-700 text-sm">Sistem mendeteksi saat ini adalah akhir/awal tahun. Pastikan semua transaksi selesai sebelum KPS melakukan <a href="{{ route('periode.index') }}" class="underline font-bold">Tutup Tahun</a>.</p>
                    </div>
                </div>
                @endif

                {{-- SHORTCUT QUICK ACTIONS (1 Baris, Tombol Horizontal) --}}
                <div class="mt-8">
                    <h3 class="font-bold text-gray-700 text-lg mb-3">Aksi Cepat</h3>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <a href="{{ route('bahan.index') }}" class="flex items-center p-3 bg-white border border-gray-200 rounded-lg shadow-sm hover:bg-blue-50 hover:border-blue-300 transition-all group">
                            <div class="p-2 bg-blue-100 text-blue-600 rounded-lg group-hover:scale-110 transition-transform"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg></div>
                            <span class="ml-3 text-sm font-bold text-gray-700">Barang Masuk</span>
                        </a>
                        <a href="{{ route('bahan.index') }}" class="flex items-center p-3 bg-white border border-gray-200 rounded-lg shadow-sm hover:bg-red-50 hover:border-red-300 transition-all group">
                            <div class="p-2 bg-red-100 text-red-600 rounded-lg group-hover:scale-110 transition-transform"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg></div>
                            <span class="ml-3 text-sm font-bold text-gray-700">Barang Keluar</span>
                        </a>
                        <a href="{{ route('pengajuan-pengadaan.create') }}" class="flex items-center p-3 bg-white border border-gray-200 rounded-lg shadow-sm hover:bg-purple-50 hover:border-purple-300 transition-all group">
                            <div class="p-2 bg-purple-100 text-purple-600 rounded-lg group-hover:scale-110 transition-transform"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg></div>
                            <span class="ml-3 text-sm font-bold text-gray-700">Buat Pengajuan</span>
                        </a>
                        <a href="{{ route('laporan.index') }}" class="flex items-center p-3 bg-white border border-gray-200 rounded-lg shadow-sm hover:bg-gray-50 hover:border-gray-300 transition-all group">
                            <div class="p-2 bg-gray-200 text-gray-700 rounded-lg group-hover:scale-110 transition-transform"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg></div>
                            <span class="ml-3 text-sm font-bold text-gray-700">Pusat Laporan</span>
                        </a>
                    </div>
                </div>

                {{-- STATISTIK & STATUS PENGAJUAN (1 Baris, 4 Kotak Seragam) --}}
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-8">
                    {{-- Kotak 1: Total Bahan --}}
                    <div class="bg-white p-5 rounded-lg shadow-sm border border-gray-100 flex items-center justify-between">
                        <div>
                            <p class="text-xs text-gray-500 font-bold uppercase tracking-wider mb-1">Total Bahan</p>
                            <p class="text-2xl font-bold text-gray-800">{{ $data['jumlah_bahan'] }}</p>
                        </div>
                        <div class="bg-blue-50 p-3 rounded-lg text-blue-500"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg></div>
                    </div>
                    
                    {{-- Kotak 2: Draft Pengajuan --}}
                    <div class="bg-white p-5 rounded-lg shadow-sm border border-gray-100 flex items-center justify-between">
                        <div>
                            <p class="text-xs text-gray-500 font-bold uppercase tracking-wider mb-1">Pengajuan Draft</p>
                            <p class="text-2xl font-bold text-gray-600">{{ $data['pengajuan_draft'] }}</p>
                        </div>
                        <div class="bg-gray-100 p-3 rounded-lg text-gray-500"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg></div>
                    </div>

                    {{-- Kotak 3: Menunggu Review --}}
                    <div class="bg-white p-5 rounded-lg shadow-sm border border-blue-200 flex items-center justify-between">
                        <div>
                            <p class="text-xs text-blue-500 font-bold uppercase tracking-wider mb-1">Menunggu Review</p>
                            <p class="text-2xl font-bold text-blue-600">{{ $data['pengajuan_diajukan'] }}</p>
                        </div>
                        <div class="bg-blue-50 p-3 rounded-lg text-blue-500"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></div>
                    </div>

                    {{-- Kotak 4: Disetujui --}}
                    <div class="bg-white p-5 rounded-lg shadow-sm border border-green-200 flex items-center justify-between">
                        <div>
                            <p class="text-xs text-green-600 font-bold uppercase tracking-wider mb-1">Disetujui / Siap</p>
                            <p class="text-2xl font-bold text-green-600">{{ $data['pengajuan_disetujui'] }}</p>
                        </div>
                        <div class="bg-green-50 p-3 rounded-lg text-green-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></div>
                    </div>
                </div>

                {{-- DAFTAR PERHATIAN (STOK & ED) --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-8">
                    {{-- Stok Menipis --}}
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden flex flex-col h-full">
                        <div class="bg-red-50 px-5 py-4 border-b border-red-100 flex items-center gap-2">
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path></svg>
                            <h3 class="font-bold text-red-800">Stok Menipis / Kritis</h3>
                        </div>
                        <div class="p-0 overflow-y-auto" style="max-height: 250px;">
                            <ul class="divide-y divide-gray-100">
                                @forelse($data['stok_menipis'] as $bahan)
                                <li class="p-4 hover:bg-gray-50 flex justify-between items-center transition-colors">
                                    <div>
                                        <a href="{{ route('transaksi.history', $bahan->id) }}" class="text-sm font-bold text-gray-800 hover:text-blue-600 block">{{ $bahan->nama_bahan }}</a>
                                        <span class="text-xs text-gray-500">Min. Stok: {{ floatval($bahan->minimum_stock) }} {{ $bahan->satuanRel->nama_satuan ?? '' }}</span>
                                    </div>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Sisa: {{ $bahan->formatted_stock }}
                                    </span>
                                </li>
                                @empty
                                <li class="p-6 text-center text-sm text-gray-500 italic">Kondisi aman, tidak ada stok yang kritis.</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>

                    {{-- Expired Date --}}
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden flex flex-col h-full">
                        <div class="bg-yellow-50 px-5 py-4 border-b border-yellow-100 flex items-center gap-2">
                            <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <h3 class="font-bold text-yellow-800">Mendekati Kedaluwarsa (60 Hari)</h3>
                        </div>
                        <div class="p-0 overflow-y-auto" style="max-height: 250px;">
                            <ul class="divide-y divide-gray-100">
                                @forelse($data['akan_kedaluwarsa'] as $bahan)
                                <li class="p-4 hover:bg-gray-50 flex justify-between items-center transition-colors">
                                    <div>
                                        <a href="{{ route('transaksi.history', $bahan->id) }}" class="text-sm font-bold text-gray-800 hover:text-blue-600 block">{{ $bahan->nama_bahan }}</a>
                                        <span class="text-xs text-gray-500">Sisa Stok: {{ $bahan->formatted_stock }}</span>
                                    </div>
                                    <div class="text-right">
                                        <span class="block text-xs font-bold text-red-600">{{ \Carbon\Carbon::parse($bahan->tanggal_kedaluwarsa)->isoFormat('D MMM YY') }}</span>
                                        <span class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($bahan->tanggal_kedaluwarsa)->diffForHumans() }}</span>
                                    </div>
                                </li>
                                @empty
                                <li class="p-6 text-center text-sm text-gray-500 italic">Aman, tidak ada bahan yang hampir kedaluwarsa.</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- TRANSAKSI TERAKHIR --}}
                <div class="mt-8 bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                        <h3 class="font-bold text-gray-800">5 Aktivitas Transaksi Terakhir</h3>
                        <a href="{{ route('laporan.transaksi') }}" class="text-sm text-blue-600 hover:text-blue-800 font-semibold">Lihat Semua &rarr;</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <tbody class="bg-white divide-y divide-gray-100">
                                @forelse($data['transaksi_terakhir'] as $trx)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-5 py-4 whitespace-nowrap text-sm text-gray-500 w-32">
                                        {{ \Carbon\Carbon::parse($trx->tanggal_transaksi)->diffForHumans() }}
                                    </td>
                                    <td class="px-5 py-4 text-sm text-gray-700">
                                        <span class="font-bold">{{ $trx->user?->name ?? 'User' }}</span> mencatat
                                        <span class="font-bold px-2 py-0.5 rounded {{ str_contains($trx->jenis_transaksi, 'masuk') ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                            {{ ucwords(str_replace('_', ' ', $trx->jenis_transaksi)) }}
                                        </span>
                                        sebanyak <span class="font-bold">{{ $trx->formatted_jumlah }}</span> untuk bahan <a href="{{ route('transaksi.history', $trx->bahan?->id) }}" class="text-blue-600 hover:underline font-semibold">{{ $trx->bahan?->nama_bahan ?? 'Bahan Terhapus' }}</a>.
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="2" class="px-5 py-6 text-center text-gray-500 text-sm italic">Belum ada aktivitas transaksi di prodi ini.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
            @endif

        </div>
    </div>
</x-app-layout>