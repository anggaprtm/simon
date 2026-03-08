{{-- resources/views/pengajuan-pengadaan/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Daftar Pengajuan Pengadaan Bahan') }}
        </h2>
    </x-slot>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
        .pengajuan-wrap * { font-family: 'Plus Jakarta Sans', sans-serif; }

        .action-btn {
            display: inline-flex; align-items: center; justify-content: center;
            width: 30px; height: 30px; border-radius: 7px;
            transition: all 0.15s ease;
        }
        .action-btn:hover { transform: scale(1.12); }
        .action-btn svg { width: 15px; height: 15px; }

        .top-btn {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 8px 16px; border-radius: 9px; font-size: 13px;
            font-weight: 600; transition: all 0.15s; white-space: nowrap;
        }
        .top-btn:hover { transform: translateY(-1px); filter: brightness(1.05); }

        .table-wrap { border-radius: 14px; border: 1px solid #e5e7eb; overflow: hidden; }
        thead th {
            background: #f8fafc; border-bottom: 1px solid #e5e7eb;
            padding: 11px 16px; font-size: 11px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.06em; color: #6b7280;
            white-space: nowrap;
        }
        tbody td { padding: 13px 16px; font-size: 13.5px; color: #374151; vertical-align: middle; }
        tbody tr + tr { border-top: 1px solid #f0f0f0; }
        tbody tr:nth-child(odd) { background: #fafafa; }
        tbody tr:hover { background: #f0f4ff !important; transition: background 0.12s; }

        .status-badge {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 4px 10px; border-radius: 99px;
            font-size: 11.5px; font-weight: 700;
        }
        .status-dot { width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0; }
    </style>

    <div class="py-10 pengajuan-wrap">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8 space-y-5">

            {{-- PAGE HEADER --}}
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-extrabold text-gray-800">Pengajuan Pengadaan</h1>
                    <p class="text-sm text-gray-400 mt-0.5">Kelola dan pantau status pengajuan bahan laboratorium</p>
                </div>
                @can('create-pengajuan')
                <a href="{{ route('pengajuan-pengadaan.create') }}" class="top-btn bg-indigo-600 hover:bg-indigo-700 text-white shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Buat Pengajuan Baru
                </a>
                @endcan
            </div>

            {{-- ALERT --}}
            @if (session('success'))
            <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl text-sm font-medium">
                <svg class="w-4 h-4 flex-shrink-0 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                {{ session('success') }}
            </div>
            @endif

            {{-- TABLE --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="table-wrap">
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Periode</th>
                                    <th>Unit / Prodi</th>
                                    <th>Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($pengajuans as $pengajuan)
                                <tr>
                                    <td class="text-gray-500 text-sm whitespace-nowrap text-center">
                                        {{ $pengajuan->created_at->isoFormat('D MMM Y') }}
                                    </td>
                                    <td class="text-center">
                                        <div class="text-sm font-semibold text-gray-800">{{ $pengajuan->tahun_ajaran }}</div>
                                        <div class="text-xs text-gray-400">{{ $pengajuan->semester }}</div>
                                    </td>
                                    <td class="text-center">
                                        <span class="text-xs font-semibold bg-indigo-50 text-indigo-600 px-2.5 py-1 rounded-md">
                                            {{ $pengajuan->programStudi->kode_program_studi }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $statusMap = [
                                                'Draft'     => ['bg' => '#f3f4f6', 'color' => '#374151', 'dot' => '#9ca3af'],
                                                'Diajukan'  => ['bg' => '#eff6ff', 'color' => '#1d4ed8', 'dot' => '#3b82f6'],
                                                'Disetujui' => ['bg' => '#f0fdf4', 'color' => '#15803d', 'dot' => '#22c55e'],
                                                'Ditolak'   => ['bg' => '#fef2f2', 'color' => '#b91c1c', 'dot' => '#ef4444'],
                                                'Selesai'   => ['bg' => '#ecfdf5', 'color' => '#065f46', 'dot' => '#10b981'],
                                            ];
                                            $s = $statusMap[$pengajuan->status] ?? $statusMap['Draft'];
                                        @endphp
                                        <span class="status-badge" style="background:{{ $s['bg'] }}; color:{{ $s['color'] }};">
                                            <span class="status-dot" style="background:{{ $s['dot'] }};"></span>
                                            {{ $pengajuan->status }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="flex items-center justify-center gap-1">
                                            {{-- Lihat Detail --}}
                                            <a href="{{ route('pengajuan-pengadaan.show', $pengajuan->id) }}" title="Lihat Detail" class="action-btn bg-blue-50 text-blue-500 hover:bg-blue-100">
                                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/></svg>
                                            </a>

                                            @if($pengajuan->status == 'Draft')
                                            {{-- Edit --}}
                                            <a href="{{ route('pengajuan-pengadaan.edit', $pengajuan->id) }}" title="Edit" class="action-btn bg-indigo-50 text-indigo-500 hover:bg-indigo-100">
                                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125"/></svg>
                                            </a>

                                            {{-- Ajukan --}}
                                            @if(Auth::id() === $pengajuan->id_user)
                                            <form action="{{ route('pengajuan-pengadaan.ajukanFinal', $pengajuan->id) }}" method="POST" class="inline" onsubmit="return confirm('Ajukan draft ini untuk direview Fakultas?');">
                                                @csrf
                                                <button type="submit" title="Ajukan ke Fakultas" class="action-btn bg-emerald-50 text-emerald-600 hover:bg-emerald-100">
                                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/></svg>
                                                </button>
                                            </form>
                                            @endif

                                            {{-- Hapus --}}
                                            <form action="{{ route('pengajuan-pengadaan.destroy', $pengajuan->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus pengajuan ini?');">
                                                @csrf @method('DELETE')
                                                <button type="submit" title="Hapus" class="action-btn bg-red-50 text-red-500 hover:bg-red-100">
                                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12.56 0c.342.052.682.107 1.022.166m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                                                </button>
                                            </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="py-16 text-center">
                                        <div class="flex flex-col items-center gap-2 text-gray-400">
                                            <svg class="w-10 h-10 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                            <p class="text-sm font-medium">Belum ada pengajuan</p>
                                            <p class="text-xs">Buat pengajuan pengadaan pertama Anda</p>
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