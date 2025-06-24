{{-- resources/views/laporan/print/transaksi.blade.php --}}

{{-- Menggunakan layout print --}}
@extends('layouts.print')

{{-- Mengatur judul laporan --}}
@section('title', 'Laporan Riwayat Transaksi')

{{-- Mendefinisikan konten laporan --}}
@section('content')

    <div class="mb-4 text-sm text-gray-600">
        @if($selectedProdiId && $programStudis->find($selectedProdiId))
            <p><strong>Program Studi:</strong> {{ $programStudis->find($selectedProdiId)->nama_program_studi }}</p>
        @endif
        @if($selectedTahun)
            <p><strong>Tahun Periode:</strong> {{ $selectedTahun }}</p>
        @elseif($tanggalMulai && $tanggalSelesai)
            <p><strong>Periode Tanggal:</strong> {{ \Carbon\Carbon::parse($tanggalMulai)->isoFormat('D MMM Y') }} - {{ \Carbon\Carbon::parse($tanggalSelesai)->isoFormat('D MMM Y') }}</p>
        @endif
    </div>
    
    <table class="w-full text-sm text-left rtl:text-right text-gray-500">
        <thead class="text-xs text-gray-700 uppercase bg-gray-100">
            <tr>
                <th scope="col" class="px-4 py-3 border">Tanggal</th>
                <th scope="col" class="px-4 py-3 border">Jenis</th>
                <th scope="col" class="px-4 py-3 border">Nama Bahan</th>
                <th scope="col" class="px-4 py-3 border">Unit/Prodi</th>
                <th scope="col" class="px-4 py-3 border">Jumlah</th>
                <th scope="col" class="px-4 py-3 border">Oleh</th>
                <th scope="col" class="px-4 py-3 border">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($transaksis as $transaksi)
                <tr class="bg-white border-b">
                    <td class="px-4 py-2 border whitespace-nowrap">{{ \Carbon\Carbon::parse($transaksi->tanggal_transaksi)->isoFormat('D/M/YY HH:mm') }}</td>
                    <td class="px-4 py-2 border whitespace-nowrap">{{ ucwords(str_replace('_', ' ', $transaksi->jenis_transaksi)) }}</td>
                    <td class="px-4 py-2 border">{{ $transaksi->bahan->nama_bahan ?? 'N/A' }}</td>
                    <td class="px-4 py-2 border">{{ $transaksi->bahan->programStudi->kode_program_studi ?? 'N/A' }}</td>
                    <td class="px-4 py-2 border whitespace-nowrap">{{ $transaksi->jumlah }} {{ $transaksi->bahan->satuanRel->nama_satuan ?? '' }}</td>
                    <td class="px-4 py-2 border">{{ $transaksi->user->name ?? 'N/A' }}</td>
                    <td class="px-4 py-2 border">{{ $transaksi->keterangan }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-6 py-4 text-center border">
                        Data tidak ditemukan.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection