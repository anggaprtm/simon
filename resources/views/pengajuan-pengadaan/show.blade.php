<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center gap-3">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Detail Pengajuan Pengadaan') }}</h2>
            <div class="flex items-center gap-2">
                @if(Auth::id() === $pengajuanPengadaan->id_user && $pengajuanPengadaan->status === 'Draft')
                    <a href="{{ route('pengajuan-pengadaan.edit', $pengajuanPengadaan) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">Edit Draft</a>
                    <form action="{{ route('pengajuan-pengadaan.ajukanFinal', $pengajuanPengadaan) }}" method="POST" onsubmit="return confirm('Ajukan draft ini untuk direview Fakultas?');" class="inline">
                        @csrf
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">Ajukan Final</button>
                    </form>
                @endif


                @if(Auth::id() === $pengajuanPengadaan->id_user && $pengajuanPengadaan->status === 'Disetujui')
                    <a href="{{ route('pengajuan-pengadaan.realisasiForm', $pengajuanPengadaan) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded inline-flex items-center">Input Stok Masuk</a>
                @endif

                <a href="{{ route('pengajuan-pengadaan.cetakNota', $pengajuanPengadaan->id) }}" target="_blank" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded inline-flex items-center">
                    {{ $pengajuanPengadaan->status === 'Draft' ? 'Preview Draft Nota' : 'Cetak Nota Dinas' }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-6 bg-indigo-50 p-4 rounded-lg border border-indigo-100 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Tahun Ajaran: <span class="font-semibold text-gray-900">{{ $pengajuanPengadaan->tahun_ajaran }} (Semester {{ $pengajuanPengadaan->semester }})</span></p>
                            <p class="text-sm text-gray-600 mb-1">Nomor Surat: 
                                <span class="font-bold {{ $pengajuanPengadaan->nomor_surat ? 'text-indigo-700' : 'text-red-500' }}">
                                    {{ $pengajuanPengadaan->nomor_surat ?? 'Belum diinput' }}
                                </span>
                            </p>
                            <p class="text-sm text-gray-600 mb-1">Tanggal Surat: 
                                <span class="font-bold text-gray-900">
                                    {{ $pengajuanPengadaan->tanggal_nota_dinas ? $pengajuanPengadaan->tanggal_nota_dinas->isoFormat('D MMMM YYYY') : $pengajuanPengadaan->created_at->isoFormat('D MMMM YYYY') . ' (Default)' }}
                                </span>
                            </p>
                            <p class="text-sm text-gray-600">Arsip Final: 
                                @if($pengajuanPengadaan->file_nota_dinas)
                                    <a href="{{ asset('storage/' . $pengajuanPengadaan->file_nota_dinas) }}" target="_blank" class="text-green-600 font-bold hover:underline flex items-center inline-flex gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                        Lihat Dokumen
                                    </a>
                                @else
                                    <span class="text-amber-600 font-semibold text-xs bg-amber-100 px-2 py-0.5 rounded">Belum Ada File</span>
                                @endif
                            </p>
                        </div>
                        
                        <div class="flex flex-col gap-2 w-full md:w-auto">
                            @if((Auth::id() === $pengajuanPengadaan->id_user || Auth::user()->role === 'kps') && in_array($pengajuanPengadaan->status, ['Draft', 'Diajukan', 'Disetujui', 'Selesai']))
                                <form action="{{ route('pengajuan-pengadaan.uploadNota', $pengajuanPengadaan->id) }}" method="POST" enctype="multipart/form-data" class="flex flex-col gap-2">
                                    @csrf
                                    <div class="flex items-center gap-2">
                                        <input type="file" name="file_nota_dinas" accept=".pdf" class="block w-full text-xs text-gray-500 file:mr-2 file:py-1.5 file:px-3 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-white file:text-indigo-700 hover:file:bg-indigo-100 border border-gray-300 rounded cursor-pointer" required>
                                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold py-2 px-3 rounded shadow-sm whitespace-nowrap">
                                            Unggah Arsip
                                        </button>
                                    </div>
                                    @error('file_nota_dinas')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </form>
                            @endif

                            @if(Auth::id() === $pengajuanPengadaan->id_user && in_array($pengajuanPengadaan->status, ['Draft', 'Diajukan']))
                                <button type="button" onclick="editInfoSurat()" class="bg-white border border-indigo-600 text-indigo-700 hover:bg-indigo-50 text-xs font-bold py-2 px-3 rounded shadow-sm flex items-center justify-center gap-2">
                                    Update Info Surat
                                </button>
                            @endif
                            
                            <form id="form-update-surat" action="{{ route('pengajuan-pengadaan.updateAtributSurat', $pengajuanPengadaan->id) }}" method="POST" class="hidden">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="nomor_surat" id="input-nomor-surat">
                                <input type="hidden" name="tanggal_nota_dinas" id="input-tanggal-surat">
                            </form>
                        </div>
                    </div>

                    <form id="form-update-nomor" action="{{ route('pengajuan-pengadaan.updateNomor', $pengajuanPengadaan->id) }}" method="POST" class="hidden">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="nomor_surat" id="input-nomor-surat">
                    </form>
                    @if (session('success'))
                        <div class="mb-4 rounded border border-green-300 bg-green-50 p-3 text-green-700">{{ session('success') }}</div>
                    @endif
                    @if (session('error'))
                        <div class="mb-4 rounded border border-red-300 bg-red-50 p-3 text-red-700">{{ session('error') }}</div>
                    @endif

                    <div class="grid grid-cols-2 gap-4 mb-6 border-b pb-4">
                        <div><p class="text-sm text-gray-500">Program Studi</p><p class="font-semibold">{{ $pengajuanPengadaan->programStudi->nama_program_studi }}</p></div>
                        <div><p class="text-sm text-gray-500">Periode</p><p class="font-semibold">{{ $pengajuanPengadaan->tahun_ajaran }} - {{ $pengajuanPengadaan->semester }}</p></div>
                        <div><p class="text-sm text-gray-500">Diajukan oleh</p><p class="font-semibold">{{ $pengajuanPengadaan->user->name }}</p></div>
                        <div><p class="text-sm text-gray-500">Status</p><p class="font-semibold">{{ $pengajuanPengadaan->status }}</p></div>
                    </div>

                    <h3 class="text-lg font-semibold mb-4">Daftar Barang yang Diajukan</h3>

                    @can('manage-pengajuan')
                        @if($pengajuanPengadaan->status === 'Diajukan')
                            <form action="{{ route('pengajuan-pengadaan.setujui', $pengajuanPengadaan) }}" method="POST">
                                @csrf
                        @endif
                    @endcan

                    @php
                        $formatQty = function ($value) {
                            $formatted = number_format((float) ($value ?? 0), 3, ',', '.');
                            return rtrim(rtrim($formatted, '0'), ',');
                        };
                        $formatRupiah = function ($value) {
                            return 'Rp ' . number_format((float) ($value ?? 0), 0, ',', '.');
                        };
                    @endphp

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Barang</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Keterangan</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Diajukan</th>
                                    @if($pengajuanPengadaan->status !== 'Draft')
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Disetujui</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status Item</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Catatan</th>
                                    @endif
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Harga Satuan (HPS)</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Harga Total (HPS)</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Link Referensi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @php $grandTotal = 0; @endphp
                                @foreach ($pengajuanPengadaan->details as $detail)
                                    @php
                                        $isExisting = !is_null($detail->id_bahan);
                                        $stokSaatIni = $isExisting ? $detail->bahan?->formatted_stock : null;
                                        // Gunakan jumlah disetujui jika ada, jika tidak gunakan jumlah diajukan
                                        $jumlahFinal = $detail->approved_jumlah ?? $detail->jumlah;
                                        $totalHarga = ($detail->harga_satuan ?? 0) * ($jumlahFinal ?? 0);
                                        $grandTotal += $totalHarga;
                                    @endphp
                                    <tr>
                                        <td class="px-4 py-3">{{ $loop->iteration }}</td>
                                        <td class="px-4 py-3">
                                            <div class="font-medium">{{ $detail->display_nama_barang }}</div>
                                            @if(!$isExisting)
                                                <span class="text-xs bg-amber-100 text-amber-700 px-2 py-1 rounded mt-1 inline-block">Bahan baru</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-600">
                                            <div>{{ $detail->spesifikasi ?: '-' }}</div>
                                            @if($isExisting)
                                                <div class="mt-1 text-green-700">Stok saat ini: {{ $stokSaatIni }}</div>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3">{{ $formatQty($detail->jumlah) }} {{ $detail->satuan->nama_satuan }}</td>
                                        @if($pengajuanPengadaan->status !== 'Draft')
                                            <td class="px-4 py-3">
                                                @can('manage-pengajuan')
                                                    @if($pengajuanPengadaan->status === 'Diajukan')
                                                        <input type="number" name="approval_items[{{ $detail->id }}][approved_jumlah]" step="any" min="0" max="{{ $detail->jumlah }}" value="{{ $detail->jumlah }}" class="w-28 border-gray-300 rounded-md shadow-sm">
                                                    @else
                                                        {{ $formatQty($detail->approved_jumlah) }} {{ $detail->satuan->nama_satuan }}
                                                    @endif
                                                @else
                                                    {{ $formatQty($detail->approved_jumlah) }} {{ $detail->satuan->nama_satuan }}
                                                @endcan
                                            </td>
                                            <td class="px-4 py-3">
                                                @can('manage-pengajuan')
                                                    @if($pengajuanPengadaan->status === 'Diajukan')
                                                        <select name="approval_items[{{ $detail->id }}][status_item]" class="border-gray-300 rounded-md shadow-sm">
                                                            <option value="disetujui">Setujui Penuh</option>
                                                            <option value="disetujui_sebagian">Setujui Sebagian/Revisi</option>
                                                            <option value="ditolak">Tolak Item</option>
                                                        </select>
                                                    @else
                                                        {{ ucfirst(str_replace('_', ' ', $detail->status_item)) }}
                                                    @endif
                                                @else
                                                    {{ ucfirst(str_replace('_', ' ', $detail->status_item)) }}
                                                @endcan
                                            </td>
                                            <td class="px-4 py-3">
                                                @can('manage-pengajuan')
                                                    @if($pengajuanPengadaan->status === 'Diajukan')
                                                        <input type="text" name="approval_items[{{ $detail->id }}][catatan_revisi]" class="w-full border-gray-300 rounded-md shadow-sm" value="{{ $detail->catatan_revisi }}" placeholder="Opsional">
                                                    @else
                                                        {{ $detail->catatan_revisi ?: '-' }}
                                                    @endif
                                                @else
                                                    {{ $detail->catatan_revisi ?: '-' }}
                                                @endcan
                                            </td>
                                        @endif
                                        <td class="px-4 py-3">{{ $formatRupiah($detail->harga_satuan) }}</td>
                                        <td class="px-4 py-3 font-medium text-gray-900">{{ $formatRupiah($totalHarga) }}</td>
                                        <td class="px-4 py-3">
                                            @if($detail->link_referensi)
                                                <a href="{{ $detail->link_referensi }}" target="_blank" class="text-blue-600 hover:text-blue-900 underline break-all text-sm">Buka Link</a>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-50 border-t-2 border-gray-200">
                                <tr>
                                    <td colspan="{{ $pengajuanPengadaan->status !== 'Draft' ? '8' : '5' }}" class="px-4 py-4 text-right font-bold text-gray-700">TOTAL KESELURUHAN (HPS)</td>
                                    <td class="px-4 py-4 font-bold text-gray-900">{{ $formatRupiah($grandTotal) }}</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    @can('manage-pengajuan')
                        @if($pengajuanPengadaan->status === 'Diajukan')
                            <div class="mt-6 pt-6 border-t flex items-center justify-end space-x-3">
                                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">Simpan Keputusan Approval</button>
                            </form>

                            <form action="{{ route('pengajuan-pengadaan.tolak', $pengajuanPengadaan) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menolak seluruh pengajuan ini?');">
                                @csrf
                                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">Tolak Semua</button>
                            </form>
                            </div>
                        @endif
                    @endcan
                </div>
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
                    <label class="block text-sm font-medium text-gray-700">Nomor Surat</label>
                    <input id="swal-nomor" class="swal2-input !w-full !m-0 !mt-1" style="height: 2.5rem;" placeholder="Kosongkan jika belum ada" value="{{ $pengajuanPengadaan->nomor_surat ?? '' }}">
                </div>
                <div class="text-left mb-2">
                    <label class="block text-sm font-medium text-gray-700">Tanggal Nota Dinas</label>
                    <input id="swal-tanggal" type="date" class="swal2-input !w-full !m-0 !mt-1" style="height: 2.5rem;" value="{{ $pengajuanPengadaan->tanggal_nota_dinas ? $pengajuanPengadaan->tanggal_nota_dinas->format('Y-m-d') : $pengajuanPengadaan->created_at->format('Y-m-d') }}">
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'Simpan',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#4f46e5',
            preConfirm: () => {
                return {
                    nomor: document.getElementById('swal-nomor').value,
                    tanggal: document.getElementById('swal-tanggal').value
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('input-nomor-surat').value = result.value.nomor;
                document.getElementById('input-tanggal-surat').value = result.value.tanggal;
                document.getElementById('form-update-surat').submit();
            }
        });
    }
    </script>
    @endpush
</x-app-layout>
