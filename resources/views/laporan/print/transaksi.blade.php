{{-- resources/views/laporan/print/transaksi.blade.php --}}

{{-- Menggunakan layout print --}}
@extends('layouts.print')

{{-- Mengatur judul laporan --}}
@section('title', 'Berita Acara Riwayat Transaksi')

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
        <thead class="text-xs text-gray-700 uppercase bg-gray-100" style="text-align: center;">
            <tr>
                <th class="border border-gray-300 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                <th class="border border-gray-300 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis</th>
                <th class="border border-gray-300 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Bahan</th>
                <th class="border border-gray-300 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prodi</th>
                <th class="border border-gray-300 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                <th class="border border-gray-300 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Oleh</th>
                <th class="border border-gray-300 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($transaksis as $transaksi)
                <tr class="bg-white border-b">
                    <td class="border border-gray-300 px-6 py-4 whitespace-nowrap">{{ \Carbon\Carbon::parse($transaksi->tanggal_transaksi)->isoFormat('D MMM Y, HH:mm') }}</td>
                    <td class="border border-gray-300 px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ str_contains($transaksi->jenis_transaksi, 'masuk') ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ str_replace('_', ' ', $transaksi->jenis_transaksi) }}
                        </span>
                    </td>
                    <td class="border border-gray-300 px-6 py-4 whitespace-nowrap">{!! $transaksi->bahan->nama_bahan_html !!}</td>
                    <td class="border border-gray-300 px-6 py-4 whitespace-nowrap">{{ $transaksi->bahan->programStudi->kode_program_studi ?? 'N/A' }}</td>
                    <td class="border border-gray-300 px-6 py-4 whitespace-nowrap">{{ $transaksi->jumlah }} {{ $transaksi->bahan->satuanRel->nama_satuan ?? '' }}</td>
                    <td class="border border-gray-300 px-6 py-4 whitespace-nowrap">{{ $transaksi->user->name ?? 'N/A' }}</td>
                    <td class="border border-gray-300 px-6 py-4">{{ $transaksi->keterangan }}</td>
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