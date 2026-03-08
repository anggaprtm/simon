{{-- resources/views/satuan/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Satuan Bahan') }}
        </h2>
    </x-slot>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
        .satuan-wrap * { font-family: 'Plus Jakarta Sans', sans-serif; }

        .table-container { border-radius: 14px; border: 1px solid #e5e7eb; overflow: hidden; }
        thead th {
            background: #f8fafc;
            border-bottom: 1px solid #e5e7eb;
            padding: 11px 16px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #6b7280;
            white-space: nowrap;
        }
        tbody td { padding: 13px 16px; font-size: 13.5px; color: #374151; vertical-align: middle; }
        tbody tr { transition: background 0.12s; }
        tbody tr:nth-child(odd) { background: #fafafa; }
        tbody tr:hover { background: #f0f4ff !important; }
        tbody tr + tr { border-top: 1px solid #f0f0f0; }

        .action-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            border-radius: 7px;
            transition: all 0.15s ease;
        }
        .action-btn:hover { transform: scale(1.12); }
        .action-btn svg { width: 15px; height: 15px; }

        .top-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 9px;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.15s;
            white-space: nowrap;
        }
        .top-btn:hover { transform: translateY(-1px); filter: brightness(1.05); }
    </style>

    <div class="py-10 satuan-wrap">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-5">

            {{-- PAGE HEADER ROW --}}
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-extrabold text-gray-800">Daftar Satuan</h1>
                    <p class="text-sm text-gray-400 mt-0.5">Kelola satuan takaran untuk bahan habis pakai</p>
                </div>
                <div class="flex items-center gap-2 flex-wrap">
                    <a href="{{ route('satuan.create') }}" class="top-btn bg-indigo-600 hover:bg-indigo-700 text-white shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Tambah Satuan
                    </a>
                </div>
            </div>

            {{-- SESSION ALERTS --}}
            @if (session('success'))
            <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl text-sm font-medium">
                <svg class="w-4 h-4 flex-shrink-0 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                {{ session('success') }}
            </div>
            @endif
            @if (session('error'))
            <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl text-sm font-medium">
                <svg class="w-4 h-4 flex-shrink-0 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                {{ session('error') }}
            </div>
            @endif

            {{-- TABLE CARD --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="table-container border-0 rounded-none">
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr>
                                    <th class="w-12 text-center">No</th>
                                    <th>Nama Satuan</th>
                                    <th>Keterangan</th>
                                    <th class="text-center w-28">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($satuans as $satuan)
                                <tr>
                                    <td class="text-center font-medium text-gray-500">{{ $loop->iteration }}</td>
                                    <td>
                                        <span class="text-sm font-bold bg-indigo-50 text-indigo-600 px-2.5 py-1 rounded-md">{{ $satuan->nama_satuan }}</span>
                                    </td>
                                    <td>
                                        <div class="text-gray-600 text-sm">{{ $satuan->keterangan_satuan ?: '—' }}</div>
                                    </td>
                                    <td>
                                        <div class="flex items-center justify-center gap-2">
                                            <a href="{{ route('satuan.edit', $satuan->id) }}" title="Edit" class="action-btn bg-indigo-50 text-indigo-500 hover:bg-indigo-100">
                                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125"/></svg>
                                            </a>

                                            <form action="{{ route('satuan.destroy', $satuan->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus satuan ini?');" class="inline">
                                                @csrf @method('DELETE')
                                                <button type="submit" title="Hapus" class="action-btn bg-red-50 text-red-500 hover:bg-red-100">
                                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12.56 0c.342.052.682.107 1.022.166m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="py-16 text-center">
                                        <div class="flex flex-col items-center gap-2 text-gray-400">
                                            <svg class="w-10 h-10 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                                            <p class="text-sm font-medium">Data satuan tidak ditemukan</p>
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