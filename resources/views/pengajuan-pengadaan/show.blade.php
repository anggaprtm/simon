{{-- resources/views/pengajuan-pengadaan/show.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Detail Pengajuan Pengadaan') }}</h2>
    </x-slot>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
        .pp-wrap * { font-family: 'Plus Jakarta Sans', sans-serif; }

        .top-btn {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 8px 15px; border-radius: 9px; font-size: 12.5px;
            font-weight: 600; transition: all 0.15s; white-space: nowrap;
        }
        .top-btn:hover { transform: translateY(-1px); }

        .info-label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: #9ca3af; }
        .info-value { font-size: 14px; font-weight: 600; color: #1f2937; margin-top: 3px; }

        .status-badge {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 4px 11px; border-radius: 99px;
            font-size: 12px; font-weight: 700;
        }
        .status-dot { width: 6px; height: 6px; border-radius: 50%; }

        .detail-th {
            background: #f8fafc; padding: 10px 14px;
            font-size: 11px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.05em;
            color: #6b7280; white-space: nowrap;
            border-bottom: 1px solid #e5e7eb;
        }
        .detail-td { padding: 13px 14px; font-size: 13px; color: #374151; vertical-align: top; border-top: 1px solid #f0f0f0; }
        tbody tr:hover td { background: #fafafa; }

        .approval-input {
            border: 1.5px solid #e5e7eb; border-radius: 7px;
            padding: 6px 10px; font-size: 13px; width: 100%;
            outline: none; font-family: 'Plus Jakarta Sans', sans-serif;
            transition: border-color 0.15s;
        }
        .approval-input:focus { border-color: #6366f1; }
        .approval-select {
            border: 1.5px solid #e5e7eb; border-radius: 7px;
            padding: 6px 10px; font-size: 12.5px; width: 100%;
            outline: none; background: white;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>

    <div class="py-10 pp-wrap">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8 space-y-5">

            {{-- PAGE HEADER --}}
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center gap-3">
                    <a href="{{ route('pengajuan-pengadaan.index') }}" class="w-9 h-9 rounded-xl bg-white border border-gray-200 shadow-sm flex items-center justify-center text-gray-400 hover:text-gray-700 transition-all flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </a>
                    <div>
                        <h1 class="text-2xl font-extrabold text-gray-800">Detail Pengajuan</h1>
                        <p class="text-sm text-gray-400 mt-0.5">{{ $pengajuanPengadaan->tahun_ajaran }} · Semester {{ $pengajuanPengadaan->semester }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2 flex-wrap">
                    @if(Auth::id() === $pengajuanPengadaan->id_user && $pengajuanPengadaan->status === 'Draft')
                    <a href="{{ route('pengajuan-pengadaan.edit', $pengajuanPengadaan) }}" class="top-btn bg-indigo-100 text-indigo-700 hover:bg-indigo-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125"/></svg>
                        Edit Draft
                    </a>
                    <form action="{{ route('pengajuan-pengadaan.ajukanFinal', $pengajuanPengadaan) }}" method="POST" onsubmit="return confirm('Ajukan draft ini untuk direview Fakultas?');" class="inline">
                        @csrf
                        <button type="submit" class="top-btn bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/></svg>
                            Ajukan Final
                        </button>
                    </form>
                    @endif

                    @if(Auth::id() === $pengajuanPengadaan->id_user && $pengajuanPengadaan->status === 'Disetujui')
                    <a href="{{ route('pengajuan-pengadaan.realisasiForm', $pengajuanPengadaan) }}" class="top-btn bg-blue-600 hover:bg-blue-700 text-white shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Input Stok Masuk
                    </a>
                    @endif

                    @if(in_array($pengajuanPengadaan->status, ['Disetujui', 'Selesai']))
                    <a href="{{ route('pengajuan-pengadaan.cetakApproval', $pengajuanPengadaan->id) }}" target="_blank" class="top-btn bg-emerald-100 text-emerald-800 hover:bg-emerald-200 shadow-sm border border-emerald-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Cetak Hasil Approval
                    </a>
                    @endif

                    <a href="{{ route('pengajuan-pengadaan.cetakNota', $pengajuanPengadaan->id) }}" target="_blank" class="top-btn bg-red-600 hover:bg-red-700 text-white shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        {{ $pengajuanPengadaan->status === 'Draft' ? 'Preview Nota' : 'Cetak Nota Dinas' }}
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

            {{-- INFO CARDS ROW --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

                {{-- METADATA --}}
                <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">Informasi Pengajuan</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-5">
                        <div>
                            <p class="info-label">Program Studi</p>
                            <p class="info-value">{{ $pengajuanPengadaan->programStudi->nama_program_studi }}</p>
                        </div>
                        <div>
                            <p class="info-label">Periode</p>
                            <p class="info-value">{{ $pengajuanPengadaan->tahun_ajaran }}</p>
                            <p class="text-xs text-gray-400">Sem. {{ $pengajuanPengadaan->semester }}</p>
                        </div>
                        <div>
                            <p class="info-label">Diajukan Oleh</p>
                            <p class="info-value">{{ $pengajuanPengadaan->user->name }}</p>
                        </div>
                        <div>
                            <p class="info-label">Status</p>
                            @php
                                $statusMap = [
                                    'Draft'     => ['bg' => '#f3f4f6', 'color' => '#374151', 'dot' => '#9ca3af'],
                                    'Diajukan'  => ['bg' => '#eff6ff', 'color' => '#1d4ed8', 'dot' => '#3b82f6'],
                                    'Disetujui' => ['bg' => '#f0fdf4', 'color' => '#15803d', 'dot' => '#22c55e'],
                                    'Ditolak'   => ['bg' => '#fef2f2', 'color' => '#b91c1c', 'dot' => '#ef4444'],
                                    'Selesai'   => ['bg' => '#ecfdf5', 'color' => '#065f46', 'dot' => '#10b981'],
                                ];
                                $s = $statusMap[$pengajuanPengadaan->status] ?? $statusMap['Draft'];
                            @endphp
                            <span class="status-badge mt-1" style="background:{{ $s['bg'] }}; color:{{ $s['color'] }};">
                                <span class="status-dot" style="background:{{ $s['dot'] }};"></span>
                                {{ $pengajuanPengadaan->status }}
                            </span>
                        </div>
                        <div>
                            <p class="info-label">Nomor Surat</p>
                            <p class="info-value {{ !$pengajuanPengadaan->nomor_surat ? 'text-red-400 font-normal' : '' }}">
                                {{ $pengajuanPengadaan->nomor_surat ?? 'Belum diinput' }}
                            </p>
                        </div>
                        <div>
                            <p class="info-label">Tanggal Surat</p>
                            <p class="info-value">
                                {{ $pengajuanPengadaan->tanggal_nota_dinas
                                    ? $pengajuanPengadaan->tanggal_nota_dinas->isoFormat('D MMM YYYY')
                                    : $pengajuanPengadaan->created_at->isoFormat('D MMM YYYY') }}
                            </p>
                        </div>
                        <div class="md:col-span-2">
                            <p class="info-label">Arsip Final</p>
                            @if($pengajuanPengadaan->file_nota_dinas)
                            <a href="{{ asset('storage/' . $pengajuanPengadaan->file_nota_dinas) }}" target="_blank"
                               class="inline-flex items-center gap-1.5 text-sm font-semibold text-emerald-600 hover:text-emerald-800 mt-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                Lihat Dokumen
                            </a>
                            @else
                            <span class="inline-block mt-1 text-xs font-semibold bg-amber-100 text-amber-600 px-2.5 py-1 rounded-full">Belum Ada File</span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- UNGGAH & UPDATE SURAT --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 flex flex-col gap-4">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Kelola Dokumen</h3>

                    @if((Auth::id() === $pengajuanPengadaan->id_user || Auth::user()->role === 'kps' || Auth::user()->role === 'superadmin') && in_array($pengajuanPengadaan->status, ['Draft', 'Diajukan', 'Disetujui', 'Selesai']))
                    <form action="{{ route('pengajuan-pengadaan.uploadNota', $pengajuanPengadaan->id) }}" method="POST" enctype="multipart/form-data" class="space-y-2">
                        @csrf
                        <label class="text-xs font-semibold text-gray-500 block">Unggah Arsip PDF</label>
                        <input type="file" name="file_nota_dinas" accept=".pdf"
                            class="block w-full text-xs text-gray-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 border border-gray-200 rounded-xl cursor-pointer p-1 bg-white" required>
                        @error('file_nota_dinas')<p class="text-xs text-red-500">{{ $message }}</p>@enderror
                        <button type="submit" class="top-btn bg-indigo-600 hover:bg-indigo-700 text-white w-full justify-center text-xs py-2">
                            Unggah Arsip
                        </button>
                    </form>
                    @endif

                    @if(Auth::id() === $pengajuanPengadaan->id_user && in_array($pengajuanPengadaan->status, ['Draft', 'Diajukan']))
                    <button type="button" onclick="editInfoSurat()"
                        class="top-btn bg-white border border-indigo-200 text-indigo-700 hover:bg-indigo-50 w-full justify-center text-xs py-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125"/></svg>
                        Update Info Surat
                    </button>
                    <form id="form-update-surat" action="{{ route('pengajuan-pengadaan.updateAtributSurat', $pengajuanPengadaan->id) }}" method="POST" class="hidden">
                        @csrf @method('PATCH')
                        <input type="hidden" name="nomor_surat" id="input-nomor-surat">
                        <input type="hidden" name="tanggal_nota_dinas" id="input-tanggal-surat">
                    </form>
                    @endif
                </div>
            </div>

            {{-- DAFTAR BARANG --}}
            @can('manage-pengajuan')
                @if($pengajuanPengadaan->status === 'Diajukan')
                <form action="{{ route('pengajuan-pengadaan.setujui', $pengajuanPengadaan) }}" method="POST">
                    @csrf
                @endif
            @endcan

            @php
                $formatQty = fn($v) => rtrim(rtrim(number_format((float)($v??0),3,',','.'), '0'), ',');
                $formatRupiah = fn($v) => 'Rp ' . number_format((float)($v??0), 0, ',', '.');
            @endphp

            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
                    <h3 class="font-bold text-gray-800">Daftar Barang yang Diajukan</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr>
                                <th class="detail-th w-8">No</th>
                                <th class="detail-th text-left">Nama Barang</th>
                                <th class="detail-th text-left">Keterangan / Stok</th>
                                <th class="detail-th text-left">Diajukan</th>
                                @if($pengajuanPengadaan->status !== 'Draft')
                                <th class="detail-th text-left">Disetujui</th>
                                <th class="detail-th text-left">Status Item</th>
                                <th class="detail-th text-left">Catatan</th>
                                @endif
                                <th class="detail-th text-left">Harga Satuan</th>
                                <th class="detail-th text-left">Harga Total</th>
                                <th class="detail-th text-left">Referensi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white">
                            @php $grandTotal = 0; @endphp
                            @foreach ($pengajuanPengadaan->details as $detail)
                            @php
                                $isExisting = !is_null($detail->id_bahan);
                                $stokSaatIni = $isExisting ? $detail->bahan?->formatted_stock : null;
                                $jumlahFinal = $detail->approved_jumlah ?? $detail->jumlah;
                                $totalHarga = ($detail->harga_satuan ?? 0) * ($jumlahFinal ?? 0);
                                $grandTotal += $totalHarga;
                            @endphp
                            <tr>
                                <td class="detail-td text-xs text-gray-400 font-medium">{{ $loop->iteration }}</td>
                                <td class="detail-td">
                                    <p class="font-semibold text-gray-800 text-sm">
                                        @if($isExisting)
                                            {!! $detail->bahan->nama_bahan_html !!}
                                        @else
                                            {{ $detail->display_nama_barang }}
                                        @endif
                                    </p>
                                    @if(!$isExisting)
                                    <span class="inline-block mt-1 text-xs font-semibold bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full">Bahan baru</span>
                                    @endif
                                </td>
                                <td class="detail-td text-sm">
                                    <p class="text-gray-500">{{ $detail->spesifikasi ?: '—' }}</p>
                                    @if($isExisting && $stokSaatIni)
                                    <p class="text-emerald-600 font-semibold text-xs mt-1">Stok: {{ $stokSaatIni }}</p>
                                    @endif
                                </td>
                                <td class="detail-td font-medium text-sm">{{ floatval($detail->jumlah) }} {{ $detail->satuan->nama_satuan }}</td>

                                @if($pengajuanPengadaan->status !== 'Draft')
                                <td class="detail-td">
                                    @can('manage-pengajuan')
                                        @if($pengajuanPengadaan->status === 'Diajukan')
                                        <div class="relative">
                                            <input type="number" name="approval_items[{{ $detail->id }}][approved_jumlah]"
                                                step="any" min="0" max="{{ floatval($detail->jumlah) }}"
                                                value="{{ floatval($detail->jumlah) }}"
                                                class="approval-input w-24 qty-input"
                                                data-id="{{ $detail->id }}"
                                                data-max="{{ floatval($detail->jumlah) }}"
                                                data-satuan="{{ $detail->satuan->nama_satuan }}">
                                            {{-- Indikator Selisih --}}
                                            <div id="diff-{{ $detail->id }}" class="text-xs font-semibold mt-1.5 hidden transition-all"></div>
                                        </div>
                                        @else
                                        <span class="font-medium text-sm">{{ floatval($detail->approved_jumlah) }} {{ $detail->satuan->nama_satuan }}</span>
                                        @endif
                                    @else
                                        <span class="text-sm">{{ floatval($detail->approved_jumlah) }} {{ $detail->satuan->nama_satuan }}</span>
                                    @endcan
                                </td>
                                <td class="detail-td">
                                    @can('manage-pengajuan')
                                        @if($pengajuanPengadaan->status === 'Diajukan')
                                        <select name="approval_items[{{ $detail->id }}][status_item]" 
                                            id="status-{{ $detail->id }}" 
                                            class="approval-select status-select">
                                            <option value="disetujui">Setujui Penuh</option>
                                            <option value="disetujui_sebagian">Setujui Sebagian</option>
                                            <option value="ditolak">Tolak Item</option>
                                        </select>
                                        @else
                                        @php
                                            $siMap = ['disetujui'=>['bg'=>'#f0fdf4','c'=>'#15803d'],'disetujui_sebagian'=>['bg'=>'#fffbeb','c'=>'#b45309'],'ditolak'=>['bg'=>'#fef2f2','c'=>'#b91c1c']];
                                            $si = $siMap[$detail->status_item] ?? ['bg'=>'#f3f4f6','c'=>'#6b7280'];
                                        @endphp
                                        <span class="text-xs font-bold px-2.5 py-1 rounded-full border" style="background:{{ $si['bg'] }};color:{{ $si['c'] }};border-color:{{ $si['c'] }}40;">
                                            {{ ucfirst(str_replace('_', ' ', $detail->status_item)) }}
                                        </span>
                                        @endif
                                    @else
                                        <span class="text-sm text-gray-500">{{ ucfirst(str_replace('_', ' ', $detail->status_item)) }}</span>
                                    @endcan
                                </td>
                                <td class="detail-td">
                                    @can('manage-pengajuan')
                                        @if($pengajuanPengadaan->status === 'Diajukan')
                                        <input type="text" name="approval_items[{{ $detail->id }}][catatan_revisi]"
                                            class="approval-input" value="{{ $detail->catatan_revisi }}" placeholder="Opsional">
                                        @else
                                        <span class="text-sm text-gray-500">{{ $detail->catatan_revisi ?: '—' }}</span>
                                        @endif
                                    @else
                                        <span class="text-sm text-gray-500">{{ $detail->catatan_revisi ?: '—' }}</span>
                                    @endcan
                                </td>
                                @endif

                                <td class="detail-td text-sm font-medium text-gray-700">{{ $formatRupiah($detail->harga_satuan) }}</td>
                                <td class="detail-td font-bold text-gray-900 text-sm">{{ $formatRupiah($totalHarga) }}</td>
                                <td class="detail-td">
                                    @if($detail->link_referensi)
                                    <a href="{{ $detail->link_referensi }}" target="_blank"
                                       class="inline-flex items-center gap-1 text-xs font-semibold text-blue-600 hover:text-blue-800">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                        Buka Link
                                    </a>
                                    @else
                                    <span class="text-gray-300 text-sm">—</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-gray-50 border-t-2 border-gray-200">
                                <td colspan="{{ $pengajuanPengadaan->status !== 'Draft' ? 8 : 5 }}"
                                    class="px-5 py-4 text-right text-sm font-bold text-gray-600">
                                    TOTAL KESELURUHAN (HPS)
                                </td>
                                <td class="px-5 py-4 text-sm font-extrabold text-gray-900">{{ $formatRupiah($grandTotal) }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                {{-- APPROVAL ACTIONS --}}
                @can('manage-pengajuan')
                    @if($pengajuanPengadaan->status === 'Diajukan')
                    <div class="px-6 py-4 bg-white border-t border-gray-100 flex items-center justify-end gap-3 rounded-b-2xl">
                        <button type="submit" class="top-btn bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Simpan Keputusan
                        </button>
                    </form> {{-- Penutup form persetujuan (Simpan Keputusan) --}}

                    <form action="{{ route('pengajuan-pengadaan.tolak', $pengajuanPengadaan) }}" method="POST"
                          onsubmit="return confirm('Tolak seluruh pengajuan ini?');" class="m-0">
                        @csrf
                        <button type="submit" class="top-btn bg-red-100 text-red-700 hover:bg-red-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            Tolak Semua
                        </button>
                    </form>
                    </div>
                    @endif
                @endcan
            </div>

        </div>
    </div>

    @push('scripts')
    <script>
        function editInfoSurat() {
            Swal.fire({
                title: 'Update Info Surat',
                html: `
                    <div class="text-left mb-3 mt-4">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Nomor Surat</label>
                        <input id="swal-nomor" class="swal2-input !w-full !m-0 !mt-1" style="height:2.5rem;border-radius:9px;" placeholder="Kosongkan jika belum ada" value="{{ $pengajuanPengadaan->nomor_surat ?? '' }}">
                    </div>
                    <div class="text-left mb-2">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Tanggal Nota Dinas</label>
                        <input id="swal-tanggal" type="date" class="swal2-input !w-full !m-0 !mt-1" style="height:2.5rem;border-radius:9px;" value="{{ $pengajuanPengadaan->tanggal_nota_dinas ? $pengajuanPengadaan->tanggal_nota_dinas->format('Y-m-d') : $pengajuanPengadaan->created_at->format('Y-m-d') }}">
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Simpan',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#4f46e5',
                preConfirm: () => ({
                    nomor: document.getElementById('swal-nomor').value,
                    tanggal: document.getElementById('swal-tanggal').value
                })
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('input-nomor-surat').value = result.value.nomor;
                    document.getElementById('input-tanggal-surat').value = result.value.tanggal;
                    document.getElementById('form-update-surat').submit();
                }
            });
        }
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 1. Logic ketika Input Angka diubah
            const qtyInputs = document.querySelectorAll('.qty-input');
            
            qtyInputs.forEach(input => {
                input.addEventListener('input', function() {
                    const id = this.getAttribute('data-id');
                    const max = parseFloat(this.getAttribute('data-max'));
                    const satuan = this.getAttribute('data-satuan');
                    let value = parseFloat(this.value);

                    // Handle jika input kosong
                    if (isNaN(value)) value = 0; 
                    
                    // Mencegah input lebih dari jumlah yang diajukan
                    if (value > max) {
                        value = max;
                        this.value = max;
                    }

                    const diffDiv = document.getElementById('diff-' + id);
                    const statusSelect = document.getElementById('status-' + id);

                    if (value === max) {
                        statusSelect.value = 'disetujui';
                        diffDiv.classList.add('hidden');
                    } else if (value > 0 && value < max) {
                        statusSelect.value = 'disetujui_sebagian';
                        const diff = max - value;
                        // Format menghindari koma panjang misal 0.3333333
                        const formattedDiff = parseFloat(diff.toFixed(3)); 
                        diffDiv.innerHTML = `<span class="text-amber-600 bg-amber-50 px-2 py-0.5 rounded border border-amber-200">- ${formattedDiff} ${satuan}</span>`;
                        diffDiv.classList.remove('hidden');
                    } else if (value === 0) {
                        statusSelect.value = 'ditolak';
                        diffDiv.innerHTML = `<span class="text-red-600 bg-red-50 px-2 py-0.5 rounded border border-red-200">Ditolak</span>`;
                        diffDiv.classList.remove('hidden');
                    }
                });
                
                // Trigger 1x saat page load untuk memastikan state awal sudah benar
                input.dispatchEvent(new Event('input'));
            });

            // 2. Logic ketika Dropdown Status diubah secara manual
            const statusSelects = document.querySelectorAll('.status-select');
            statusSelects.forEach(select => {
                select.addEventListener('change', function() {
                    const id = this.id.replace('status-', '');
                    const qtyInput = document.querySelector(`.qty-input[data-id="${id}"]`);
                    const max = parseFloat(qtyInput.getAttribute('data-max'));
                    
                    if (this.value === 'ditolak') {
                        qtyInput.value = 0;
                    } else if (this.value === 'disetujui') {
                        qtyInput.value = max;
                    }
                    
                    // Panggil event input agar indikator selisihnya ter-update
                    qtyInput.dispatchEvent(new Event('input'));
                });
            });
        });
    </script>
    @endpush
</x-app-layout>