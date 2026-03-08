{{-- resources/views/laporan/print/stok.blade.php --}}

{{-- Menggunakan layout print yang sudah kita sempurnakan --}}
@extends('layouts.print')

{{-- Mengatur judul laporan secara dinamis --}}
@section('title', 'Laporan Stok Bahan Laboratorium')

{{-- Mendefinisikan konten laporan --}}
@section('content')

    @php
        // Logika Penentuan Teks Bulan & Tahun
        $daftarBulan = [
            '1' => 'Januari', '2' => 'Februari', '3' => 'Maret',
            '4' => 'April', '5' => 'Mei', '6' => 'Juni',
            '7' => 'Juli', '8' => 'Agustus', '9' => 'September',
            '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
        ];
        $namaBulan = !empty($selectedBulan) ? $daftarBulan[$selectedBulan] : 'Semua Bulan';
        
        // Fungsi formatter angka
        $formatStock = function ($value) {
            if ($value === null || $value === '') {
                return '0';
            }
            $formatted = number_format((float) $value, 3, ',', '.');
            return rtrim(rtrim($formatted, '0'), ',');
        };
    @endphp

    {{-- KOP INFORMASI LAPORAN --}}
    <div class="mb-6 text-sm text-gray-800">
        <table class="w-full md:w-1/2">
            <tr>
                <td class="py-1 w-32 font-bold">Program Studi</td>
                <td class="py-1 w-4">:</td>
                <td class="py-1">
                    @if(in_array(Auth::user()->role, ['superadmin', 'fakultas']) && $selectedProdiId)
                        {{ $programStudis->firstWhere('id', $selectedProdiId)->nama_program_studi ?? 'Semua Prodi' }}
                    @elseif(in_array(Auth::user()->role, ['laboran', 'kps']))
                        {{ Auth::user()->programStudi->nama_program_studi ?? '-' }}
                    @else
                        Semua Program Studi
                    @endif
                </td>
            </tr>
            <tr>
                <td class="py-1 font-bold">Periode Laporan</td>
                <td class="py-1">:</td>
                <td class="py-1">Bulan {{ $namaBulan }} Tahun {{ $selectedTahun }}</td>
            </tr>
            <tr>
                <td class="py-1 font-bold">Status Tahun</td>
                <td class="py-1">:</td>
                <td class="py-1 font-semibold {{ $selectedTahun == $tahunAktif ? 'text-green-700' : 'text-red-700' }}">
                    {{ $selectedTahun == $tahunAktif ? 'Aktif (Berjalan)' : 'Tutup Buku' }}
                </td>
            </tr>
        </table>
    </div>

    {{-- TABEL LAPORAN --}}
    <table class="w-full text-sm text-left rtl:text-right text-gray-800 border-collapse border border-gray-400">
        <thead class="text-xs uppercase bg-gray-100 text-center">
            <tr>
                <th scope="col" class="px-4 py-3 border border-gray-400 w-12">No</th>
                <th scope="col" class="px-4 py-3 border border-gray-400">Kode</th>
                <th scope="col" class="px-4 py-3 border border-gray-400 w-64">Nama Bahan</th>
                <th scope="col" class="px-4 py-3 border border-gray-400">Jenis</th>
                
                @if($selectedTahun == $tahunAktif)
                    <th scope="col" class="px-4 py-3 border border-gray-400 bg-gray-200">Stok Awal Periode</th>
                    <th scope="col" class="px-4 py-3 border border-gray-400 bg-gray-200">Stok Saat Ini</th>
                @else
                    <th scope="col" class="px-4 py-3 border border-gray-400 bg-gray-200">Stok Awal</th>
                    <th scope="col" class="px-4 py-3 border border-gray-400 bg-gray-200">Stok Akhir</th>
                @endif
                
                <th scope="col" class="px-4 py-3 border border-gray-400">Satuan</th>
                <th scope="col" class="px-4 py-3 border border-gray-400">Gudang</th>
                <th scope="col" class="px-4 py-3 border border-gray-400">Prodi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($laporanData as $index => $item)
                <tr class="bg-white hover:bg-gray-50">
                    <td class="px-4 py-2 border border-gray-400 text-center">{{ $index + 1 }}</td>
                    
                    @if($selectedTahun == $tahunAktif)
                        {{-- TAMPILAN PERIODE AKTIF ($item adalah Model Bahan) --}}
                        <td class="px-4 py-2 border border-gray-400 text-center">{{ $item->kode_bahan }}</td>
                        <td class="px-4 py-2 border border-gray-400">{!! $item->nama_bahan_html ?? '<span class="italic text-gray-500">Bahan Terhapus</span>' !!}</td>
                        <td class="px-4 py-2 border border-gray-400 text-center">{{ ucwords(str_replace('_', ' ', $item->jenis_bahan ?? '-')) }}</td>
                        
                        {{-- Pengaman Nullsafe untuk periodeAktif --}}
                        <td class="px-4 py-2 border border-gray-400 text-center">{{ $formatStock($item->periodeAktif?->stok_awal) }}</td>
                        <td class="px-4 py-2 border border-gray-400 text-center font-bold text-gray-900">{{ $formatStock($item->jumlah_stock) }}</td>
                        
                        <td class="px-4 py-2 border border-gray-400 text-center">{{ $item->satuanRel?->nama_satuan ?? '-' }}</td>
                        <td class="px-4 py-2 border border-gray-400 text-center">{{ $item->gudang?->nama_gudang ?? '-' }}</td>
                        <td class="px-4 py-2 border border-gray-400 text-center">{{ $item->programStudi?->kode_program_studi ?? '-' }}</td>
                        
                    @else
                        {{-- TAMPILAN PERIODE TERTUTUP ($item adalah Model PeriodeStok) --}}
                        <td class="px-4 py-2 border border-gray-400 text-center">{{ $item->bahan?->kode_bahan ?? '-' }}</td>
                        <td class="px-4 py-2 border border-gray-400">{!! $item->bahan?->nama_bahan_html ?? '<span class="italic text-red-500">Bahan Terhapus</span>' !!}</td>
                        <td class="px-4 py-2 border border-gray-400 text-center">{{ ucwords(str_replace('_', ' ', $item->bahan?->jenis_bahan ?? '-')) }}</td>
                        
                        <td class="px-4 py-2 border border-gray-400 text-center">{{ $formatStock($item->stok_awal) }}</td>
                        <td class="px-4 py-2 border border-gray-400 text-center font-bold text-gray-900">{{ $formatStock($item->stok_akhir) }}</td>
                        
                        <td class="px-4 py-2 border border-gray-400 text-center">{{ $item->bahan?->satuanRel?->nama_satuan ?? '-' }}</td>
                        <td class="px-4 py-2 border border-gray-400 text-center">{{ $item->bahan?->gudang?->nama_gudang ?? '-' }}</td>
                        <td class="px-4 py-2 border border-gray-400 text-center">{{ $item->bahan?->programStudi?->kode_program_studi ?? '-' }}</td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="px-4 py-8 text-center border border-gray-400 italic text-gray-500">
                        Data stok tidak ditemukan untuk filter yang dipilih.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection