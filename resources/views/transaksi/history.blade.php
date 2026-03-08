{{-- resources/views/transaksi/history.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Riwayat Transaksi') }}
        </h2>
    </x-slot>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
        .trx-wrap * { font-family: 'Plus Jakarta Sans', sans-serif; }

        .table-wrap { border-radius: 14px; border: 1px solid #e5e7eb; overflow: hidden; }
        thead th {
            background: #f8fafc; padding: 11px 16px;
            font-size: 11px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.06em;
            color: #6b7280; white-space: nowrap;
            border-bottom: 1px solid #e5e7eb;
        }
        tbody td { padding: 13px 16px; font-size: 13px; color: #374151; vertical-align: middle; }
        tbody tr + tr { border-top: 1px solid #f0f0f0; }
        tbody tr:nth-child(odd) { background: #fafafa; }
        tbody tr:hover { background: #f0f4ff !important; transition: background 0.1s; }
    </style>

    <div class="py-10 trx-wrap">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8 space-y-5">

            {{-- PAGE HEADER --}}
            <div class="flex items-center gap-3">
                <a href="{{ route('bahan.index') }}" class="w-9 h-9 rounded-xl bg-white border border-gray-200 shadow-sm flex items-center justify-center text-gray-400 hover:text-gray-700 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </a>
                <div>
                    <h1 class="text-2xl font-extrabold text-gray-800">Riwayat Transaksi</h1>
                    <p class="text-sm text-gray-400 mt-0.5">Seluruh aktivitas stok masuk & keluar</p>
                </div>
            </div>

            {{-- BAHAN INFO CARD --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <div class="flex flex-col md:flex-row md:items-center gap-4">
                    <div class="w-11 h-11 rounded-xl bg-indigo-50 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-gray-800">{!! $bahan->nama_bahan_html !!}</p>
                        <p class="text-xs text-gray-400 mt-0.5">
                            {{ $bahan->merk }} ·
                            Kode: <span class="font-mono font-semibold text-gray-600">{{ $bahan->kode_bahan }}</span> ·
                            Gudang: <span class="font-semibold text-gray-600">{{ $bahan->gudang->nama_gudang }}</span>
                        </p>
                    </div>
                    <div class="flex items-center gap-6 flex-shrink-0">
                        <div class="text-center">
                            <p class="text-xs text-gray-400 font-medium">Stok Saat Ini</p>
                            <p class="text-xl font-extrabold text-indigo-600">{{ $bahan->formatted_stock }}</p>
                        </div>
                        <div class="text-center">
                            <p class="text-xs text-gray-400 font-medium">Stok Min.</p>
                            <p class="text-xl font-extrabold text-gray-500">{{ floatval($bahan->minimum_stock) }}</p>
                        </div>
                        @can('update-bahan', $bahan)
                        <div class="flex gap-2">
                            <a href="{{ route('transaksi.createMasuk', $bahan->id) }}"
                               class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg bg-emerald-100 hover:bg-emerald-200 text-emerald-700 text-xs font-bold transition-all">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                                Masuk
                            </a>
                            <a href="{{ route('transaksi.createKeluar', $bahan->id) }}"
                               class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg bg-red-100 hover:bg-red-200 text-red-600 text-xs font-bold transition-all">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                                Keluar
                            </a>
                        </div>
                        @endcan
                    </div>
                </div>
            </div>

            {{-- TABLE --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="table-wrap">
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr>
                                    <th class="text-left">Tanggal & Waktu</th>
                                    <th class="text-left">Jenis Transaksi</th>
                                    <th class="text-left">Jumlah</th>
                                    <th class="text-left">Stok Akhir</th>
                                    <th class="text-left">Dicatat Oleh</th>
                                    <th class="text-left">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($transaksis as $transaksi)
                                @php $isMasuk = str_contains($transaksi->jenis_transaksi, 'masuk'); @endphp
                                <tr>
                                    <td class="whitespace-nowrap">
                                        <p class="font-medium text-gray-700">{{ \Carbon\Carbon::parse($transaksi->tanggal_transaksi)->isoFormat('D MMM YYYY') }}</p>
                                        <p class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($transaksi->tanggal_transaksi)->format('H:i') }}</p>
                                    </td>
                                    <td>
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold
                                            {{ $isMasuk ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $isMasuk ? 'bg-emerald-500' : 'bg-red-500' }}"></span>
                                            {{ ucwords(str_replace('_', ' ', $transaksi->jenis_transaksi)) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="font-bold {{ $isMasuk ? 'text-emerald-700' : 'text-red-600' }}">
                                            {{ $isMasuk ? '+' : '-' }}{{ $transaksi->formatted_jumlah }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="font-bold text-gray-800">{{ $transaksi->formatted_stock_sesudah }}</span>
                                    </td>
                                    <td class="text-gray-500 text-sm">{{ $transaksi->user->name }}</td>
                                    <td class="text-gray-500 text-sm max-w-xs">
                                        {{ $transaksi->keterangan ?: '—' }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="py-16 text-center">
                                        <div class="flex flex-col items-center gap-2 text-gray-400">
                                            <svg class="w-10 h-10 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                            <p class="text-sm font-medium">Belum ada riwayat transaksi</p>
                                        </div>
                                    </td>
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