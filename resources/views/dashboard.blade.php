<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

        .dashboard-wrap * {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .stat-card {
            position: relative;
            overflow: hidden;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 28px -8px rgba(0,0,0,0.12);
        }
        .stat-card::before {
            content: '';
            position: absolute;
            top: -24px;
            right: -24px;
            width: 80px;
            height: 80px;
            border-radius: 50%;
            opacity: 0.08;
        }
        .stat-blue::before   { background: #2563EB; }
        .stat-gray::before   { background: #6B7280; }
        .stat-indigo::before { background: #4F46E5; }
        .stat-green::before  { background: #16A34A; }

        .quick-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 14px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.15s ease;
            white-space: nowrap;
        }
        .quick-btn:hover { transform: translateY(-1px); }

        .scroll-list {
            max-height: 240px;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: #e5e7eb transparent;
        }
        .scroll-list::-webkit-scrollbar { width: 4px; }
        .scroll-list::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 4px; }

        .badge-masuk  { background:#dcfce7; color:#15803d; }
        .badge-keluar { background:#fee2e2; color:#b91c1c; }

        .section-card {
            background: #fff;
            border-radius: 14px;
            border: 1px solid #f0f0f0;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            overflow: hidden;
        }
    </style>

    <div class="py-10 dashboard-wrap">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- WELCOME --}}
            <div class="bg-white px-6 py-4 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-0.5">Selamat Datang Kembali</p>
                    <h1 class="text-xl font-bold text-gray-800">{{ Auth::user()->nama_lengkap }} 👋🏻</h1>
                </div>
                <span class="text-xs text-gray-400 hidden md:block">{{ now()->isoFormat('dddd, D MMMM YYYY') }}</span>
            </div>

            {{-- ========== SUPERADMIN / FAKULTAS ========== --}}
            @if(in_array(Auth::user()->role, ['superadmin', 'fakultas']))

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="stat-card stat-blue bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                    <p class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-2">Total Jenis Bahan</p>
                    <p class="text-4xl font-extrabold text-blue-600">{{ $data['total_bahan'] }}</p>
                    <p class="text-xs text-gray-400 mt-1">bahan terdaftar</p>
                </div>
                <div class="stat-card stat-green bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                    <p class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-2">Total Unit</p>
                    <p class="text-4xl font-extrabold text-green-600">{{ $data['total_prodi'] }}</p>
                    <p class="text-xs text-gray-400 mt-1">unit / prodi aktif</p>
                </div>
                <div class="stat-card stat-indigo bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                    <p class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-2">Total Pengguna</p>
                    <p class="text-4xl font-extrabold text-indigo-600">{{ $data['total_user'] }}</p>
                    <p class="text-xs text-gray-400 mt-1">akun terdaftar</p>
                </div>
            </div>

            <div class="section-card">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                    <h3 class="font-bold text-gray-700">Ringkasan per Unit</h3>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-5">
                    @foreach($data['ringkasan_prodi'] as $prodi)
                    <div class="bg-gray-50 hover:bg-blue-50 border border-gray-100 hover:border-blue-200 rounded-xl p-4 transition-colors">
                        <h4 class="font-bold text-gray-800 mb-2">{{ $prodi->nama_program_studi }}</h4>
                        <div class="flex gap-4 text-sm text-gray-500">
                            <span>Bahan: <strong class="text-gray-700">{{ $prodi->bahans_count }}</strong></span>
                            <span>Laboran: <strong class="text-gray-700">{{ $prodi->users_count }}</strong></span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- ========== LABORAN / KPS ========== --}}
            @if(in_array(Auth::user()->role, ['laboran', 'kps']))

            {{-- ALERT BANNERS --}}
            @if($data['belum_upload_arsip'])
            <div class="flex items-start gap-3 bg-red-50 border border-red-200 rounded-xl p-4">
                <div class="mt-0.5 flex-shrink-0 w-9 h-9 rounded-lg bg-red-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                </div>
                <div class="flex-1">
                    <p class="font-bold text-red-800 text-sm">Peringatan Kepatuhan</p>
                    <p class="text-red-600 text-xs mt-0.5">Anda belum mengunggah Arsip Laporan fisik (PDF bertandatangan) untuk bulan ini.</p>
                </div>
                <a href="{{ route('laporan.arsip') }}" class="quick-btn bg-red-600 hover:bg-red-700 text-white text-xs flex-shrink-0">Unggah Sekarang</a>
            </div>
            @endif

            @if($data['bisa_tutup_tahun'] && Auth::user()->role === 'kps')
            <div class="flex items-start gap-3 bg-amber-50 border border-amber-200 rounded-xl p-4">
                <div class="mt-0.5 flex-shrink-0 w-9 h-9 rounded-lg bg-amber-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                </div>
                <div>
                    <p class="font-bold text-amber-800 text-sm">Waktu Tutup Tahun Buku</p>
                    <p class="text-amber-700 text-xs mt-0.5">Sistem mendeteksi ini adalah akhir/awal tahun. Pastikan semua transaksi selesai sebelum <a href="{{ route('periode.index') }}" class="underline font-bold">Tutup Tahun</a>.</p>
                </div>
            </div>
            @endif

            {{-- TOP ROW: STATS + QUICK ACTIONS --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

                {{-- STATS: 4 cards in 2x2 --}}
                <div class="lg:col-span-2 grid grid-cols-2 gap-4">
                    <div class="stat-card stat-blue bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-1">Total Bahan</p>
                                <p class="text-3xl font-extrabold text-gray-800">{{ $data['jumlah_bahan'] }}</p>
                                <p class="text-xs text-gray-400 mt-1">jenis bahan kimia</p>
                            </div>
                            <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card stat-gray bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-1">Pengajuan Draft</p>
                                <p class="text-3xl font-extrabold text-gray-600">{{ $data['pengajuan_draft'] }}</p>
                                <p class="text-xs text-gray-400 mt-1">belum diajukan</p>
                            </div>
                            <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card bg-white rounded-2xl border border-blue-100 shadow-sm p-5">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-xs font-bold uppercase tracking-widest text-blue-400 mb-1">Menunggu Review</p>
                                <p class="text-3xl font-extrabold text-blue-600">{{ $data['pengajuan_diajukan'] }}</p>
                                <p class="text-xs text-gray-400 mt-1">sedang diproses</p>
                            </div>
                            <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card bg-white rounded-2xl border border-emerald-100 shadow-sm p-5">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-xs font-bold uppercase tracking-widest text-emerald-500 mb-1">Disetujui / Siap</p>
                                <p class="text-3xl font-extrabold text-emerald-600">{{ $data['pengajuan_disetujui'] }}</p>
                                <p class="text-xs text-gray-400 mt-1">siap direalisasi</p>
                            </div>
                            <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- QUICK ACTIONS --}}
                <div class="section-card flex flex-col">
                    <div class="px-5 py-4 border-b border-gray-100 bg-gray-50">
                        <h3 class="font-bold text-gray-700 text-sm">⚡ Aksi Cepat</h3>
                    </div>
                    <div class="p-5 flex flex-col gap-3 flex-1 justify-center">
                        <a href="{{ route('bahan.index') }}" class="quick-btn bg-blue-600 hover:bg-blue-700 text-white w-full justify-center text-sm py-2.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            Barang Masuk
                        </a>
                        <a href="{{ route('bahan.index') }}" class="quick-btn bg-red-100 hover:bg-red-200 text-red-700 w-full justify-center text-sm py-2.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                            Barang Keluar
                        </a>
                        <a href="{{ route('pengajuan-pengadaan.create') }}" class="quick-btn bg-purple-100 hover:bg-purple-200 text-purple-700 w-full justify-center text-sm py-2.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                            Buat Pengajuan
                        </a>
                        <a href="{{ route('laporan.index') }}" class="quick-btn bg-gray-100 hover:bg-gray-200 text-gray-700 w-full justify-center text-sm py-2.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            Pusat Laporan
                        </a>
                    </div>
                </div>
            </div>

            {{-- STOK MENIPIS & KEDALUWARSA --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

                {{-- Stok Menipis --}}
                <div class="section-card">
                    <div class="px-5 py-4 border-b border-red-100 bg-red-50 flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg bg-red-100 flex items-center justify-center">
                            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path></svg>
                        </div>
                        <h3 class="font-bold text-red-800 text-sm">Stok Menipis / Kritis</h3>
                        @if(count($data['stok_menipis']) > 0)
                        <span class="ml-auto text-xs font-bold bg-red-100 text-red-700 px-2 py-0.5 rounded-full">{{ count($data['stok_menipis']) }}</span>
                        @endif
                    </div>
                    <div class="scroll-list">
                        @forelse($data['stok_menipis'] as $bahan)
                        <div class="px-5 py-3.5 border-b border-gray-50 hover:bg-gray-50 flex items-center justify-between gap-3 transition-colors">
                            <div class="min-w-0">
                                <a href="{{ route('transaksi.history', $bahan->id) }}" class="text-sm font-semibold text-gray-800 hover:text-blue-600 truncate block">{{ $bahan->nama_bahan }}</a>
                                <span class="text-xs text-gray-400">Min. {{ floatval($bahan->minimum_stock) }} {{ $bahan->satuanRel->nama_satuan ?? '' }}</span>
                            </div>
                            <span class="flex-shrink-0 text-xs font-bold bg-red-100 text-red-700 px-2.5 py-1 rounded-full">
                                Sisa {{ $bahan->formatted_stock }}
                            </span>
                        </div>
                        @empty
                        <div class="px-5 py-8 text-center">
                            <div class="w-10 h-10 rounded-full bg-green-50 flex items-center justify-center mx-auto mb-2">
                                <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <p class="text-sm text-gray-400 italic">Semua stok dalam kondisi aman</p>
                        </div>
                        @endforelse
                    </div>
                </div>

                {{-- Mendekati Kedaluwarsa --}}
                <div class="section-card">
                    <div class="px-5 py-4 border-b border-amber-100 bg-amber-50 flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg bg-amber-100 flex items-center justify-center">
                            <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <h3 class="font-bold text-amber-800 text-sm">Mendekati Kedaluwarsa <span class="font-normal text-amber-600">(60 Hari)</span></h3>
                        @if(count($data['akan_kedaluwarsa']) > 0)
                        <span class="ml-auto text-xs font-bold bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full">{{ count($data['akan_kedaluwarsa']) }}</span>
                        @endif
                    </div>
                   <div class="scroll-list">
                        @forelse($data['akan_kedaluwarsa'] as $bahan)
                        <div class="px-5 py-3.5 border-b border-gray-50 hover:bg-gray-50 flex items-center justify-between gap-3 transition-colors">
                            <div class="min-w-0">
                                <a href="{{ route('transaksi.history', $bahan->id) }}" class="text-sm font-semibold text-gray-800 hover:text-blue-600 truncate block">{{ $bahan->nama_bahan }}</a>
                                <span class="text-xs text-gray-400">Sisa stok: {{ $bahan->formatted_stock }}</span>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <span class="block text-xs font-bold text-red-600">{{ \Carbon\Carbon::parse($bahan->tanggal_kedaluwarsa)->isoFormat('D MMM YY') }}</span>
                                <span class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($bahan->tanggal_kedaluwarsa)->diffForHumans() }}</span>
                            </div>
                        </div>
                        @empty
                        {{-- Ubah div di bawah ini dengan penambahan flexbox dan min-height --}}
                        <div class="px-5 py-8 text-center flex flex-col items-center justify-center h-full min-h-[200px]">
                            <div class="w-10 h-10 rounded-full bg-green-50 flex items-center justify-center mb-2">
                                <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <p class="text-sm text-gray-400">Tidak ada bahan yang hampir kedaluwarsa</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- RIWAYAT TRANSAKSI --}}
            <div class="section-card">
                <div class="px-5 py-4 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
                    <h3 class="font-bold text-gray-700 text-sm">📋 5 Aktivitas Transaksi Terakhir</h3>
                    <a href="{{ route('laporan.transaksi') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-800 transition-colors">Lihat Semua →</a>
                </div>
                <div class="divide-y divide-gray-50">
                    @forelse($data['transaksi_terakhir'] as $trx)
                    <div class="px-5 py-4 hover:bg-gray-50 flex items-start gap-4 transition-colors">
                        <span class="flex-shrink-0 mt-0.5 text-xs text-gray-400 w-24">{{ \Carbon\Carbon::parse($trx->tanggal_transaksi)->diffForHumans() }}</span>
                        <p class="text-sm text-gray-700 leading-relaxed">
                            <span class="font-semibold text-gray-800">{{ $trx->user?->name ?? 'User' }}</span> mencatat
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold mx-0.5 {{ str_contains($trx->jenis_transaksi, 'masuk') ? 'badge-masuk' : 'badge-keluar' }}">
                                {{ ucwords(str_replace('_', ' ', $trx->jenis_transaksi)) }}
                            </span>
                            <span class="font-semibold">{{ $trx->formatted_jumlah }}</span> untuk
                            <a href="{{ route('transaksi.history', $trx->bahan?->id) }}" class="text-blue-600 hover:underline font-semibold">{{ $trx->bahan?->nama_bahan ?? 'Bahan Terhapus' }}</a>
                        </p>
                    </div>
                    @empty
                    <div class="px-5 py-8 text-center text-sm text-gray-400 italic">Belum ada aktivitas transaksi di prodi ini.</div>
                    @endforelse
                </div>
            </div>

            @endif

        </div>
    </div>
</x-app-layout>