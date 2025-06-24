{{-- resources/views/laporan/print/stok.blade.php --}}

{{-- Menggunakan layout print yang sudah kita sempurnakan --}}
@extends('layouts.print')

{{-- Mengatur judul laporan secara dinamis berdasarkan tahun yang dipilih --}}
@section('title', 'Laporan Stok Bahan Tahun ' . $selectedTahun)

{{-- Mendefinisikan konten laporan --}}
@section('content')
    {{-- Informasi Filter yang Digunakan (untuk konteks di hasil cetak) --}}
    <div class="mb-4 text-sm text-gray-700">
        <div class="grid grid-cols-2">
            <div>
                @if($selectedProdiId && $programStudis->find($selectedProdiId))
                    <p><strong>Program Studi:</strong> {{ $programStudis->find($selectedProdiId)->nama_program_studi }}</p>
                @else
                    <p><strong>Program Studi:</strong> Semua</p>
                @endif
            </div>
            <div class="text-right">
                <p><strong>Tahun Periode:</strong> {{ $selectedTahun }}</p>
                <p><strong>Status Periode:</strong> <span class="font-semibold {{ $selectedTahun == $tahunAktif ? 'text-green-600' : 'text-red-600' }}">{{ $selectedTahun == $tahunAktif ? 'Aktif' : 'Ditutup' }}</span></p>
            </div>
        </div>
    </div>

    {{-- Tabel Laporan --}}
    <table class="w-full text-sm text-left rtl:text-right text-gray-500">
        <thead class="text-xs text-gray-700 uppercase bg-gray-100">
            <tr>
                <th scope="col" class="px-4 py-3 border">Kode</th>
                <th scope="col" class="px-4 py-3 border">Nama Bahan</th>
                <th scope="col" class="px-4 py-3 border">Jenis</th>
                
                {{-- Kolom header dinamis tergantung periode --}}
                @if($selectedTahun == $tahunAktif)
                    <th scope="col" class="px-4 py-3 border">Stok Awal Periode</th>
                    <th scope="col" class="px-4 py-3 border">Stok Saat Ini</th>
                @else
                    <th scope="col" class="px-4 py-3 border">Stok Awal</th>
                    <th scope="col" class="px-4 py-3 border">Stok Akhir</th>
                @endif
                
                <th scope="col" class="px-4 py-3 border">Satuan</th>
                <th scope="col" class="px-4 py-3 border">Gudang</th>
                <th scope="col" class="px-4 py-3 border">Prodi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($laporanData as $item)
                <tr class="bg-white border-b">
                    {{-- Tampilan data dinamis tergantung periode --}}
                    @if($selectedTahun == $tahunAktif)
                        {{-- Tampilan untuk periode aktif, dimana $item adalah model Bahan --}}
                        <td class="px-4 py-2 border">{{ $item->kode_bahan }}</td>
                        <td class="px-4 py-2 border">{!! $item->nama_bahan_html !!}</td>
                        <td class="px-4 py-2 border">{{ $item->jenis_bahan ?? '-' }}</td>
                        <td class="px-4 py-2 border">{{ $item->periodeAktif->stok_awal ?? 'N/A' }}</td>
                        <td class="px-4 py-2 border font-bold">{{ $item->jumlah_stock }}</td>
                        <td class="px-4 py-2 border">{{ $item->satuanRel->nama_satuan ?? '-' }}</td>
                        <td class="px-4 py-2 border">{{ $item->gudang->nama_gudang ?? '-' }}</td>
                        <td class="px-4 py-2 border">{{ $item->programStudi->kode_program_studi ?? '-' }}</td>
                    @else
                        {{-- Tampilan untuk periode tertutup, dimana $item adalah model PeriodeStok --}}
                        <td class="px-4 py-2 border">{{ $item->bahan->kode_bahan }}</td>
                        <td class="px-4 py-2 border">{!! $item->bahan->nama_bahan_html !!}</td>
                        <td class="px-4 py-2 border">{{ $item->bahan->jenis_bahan ?? '-' }}</td>
                        <td class="px-4 py-2 border">{{ $item->stok_awal }}</td>
                        <td class="px-4 py-2 border font-bold">{{ $item->stok_akhir }}</td>
                        <td class="px-4 py-2 border">{{ $item->bahan->satuanRel->nama_satuan ?? '-' }}</td>
                        <td class="px-4 py-2 border">{{ $item->bahan->gudang->nama_gudang ?? '-' }}</td>
                        <td class="px-4 py-2 border">{{ $item->bahan->programStudi->kode_program_studi ?? '-' }}</td>
                    @endif
                </tr>
            @empty
                <tr>
                    {{-- Pastikan colspan sesuai dengan jumlah total kolom --}}
                    <td colspan="8" class="px-6 py-4 text-center border">
                        Data tidak ditemukan untuk filter yang dipilih.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection