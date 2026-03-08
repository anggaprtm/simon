{{-- resources/views/laporan/print/transaksi.blade.php --}}

{{-- Menggunakan layout print --}}
@extends('layouts.print')

{{-- Mengatur judul laporan --}}
@section('title', 'Berita Acara Riwayat Transaksi')

{{-- Mendefinisikan konten laporan --}}
@section('content')

    @php
        // Logika Penentuan Teks Periode
        $daftarBulan = [
            '1' => 'Januari', '2' => 'Februari', '3' => 'Maret',
            '4' => 'April', '5' => 'Mei', '6' => 'Juni',
            '7' => 'Juli', '8' => 'Agustus', '9' => 'September',
            '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
        ];
        
       $teksPeriode = '';
        if (!empty($tanggalMulai) && !empty($tanggalSelesai)) {
            $teksPeriode = \Carbon\Carbon::parse($tanggalMulai)->isoFormat('D MMMM Y') . ' s.d ' . \Carbon\Carbon::parse($tanggalSelesai)->isoFormat('D MMMM Y');
        } else {
            if (!empty($selectedBulan)) {
                $teksPeriode = "Bulan " . $daftarBulan[$selectedBulan] . " Tahun " . $selectedTahun;
            } else {
                $teksPeriode = "Tahun " . $selectedTahun . " (Semua Bulan)";
            }
        }
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
                <td class="py-1">{{ $teksPeriode }}</td>
            </tr>
        </table>
    </div>
    
    {{-- TABEL DATA TRANSAKSI --}}
    <table class="w-full text-sm text-left rtl:text-right text-gray-800 border-collapse border border-gray-400">
        <thead class="text-xs uppercase bg-gray-100 text-center">
            <tr>
                <th class="border border-gray-400 px-4 py-3">Tanggal</th>
                <th class="border border-gray-400 px-4 py-3">Jenis</th>
                <th class="border border-gray-400 px-4 py-3">Nama Bahan</th>
                <th class="border border-gray-400 px-4 py-3">Prodi</th>
                <th class="border border-gray-400 px-4 py-3">Jumlah</th>
                <th class="border border-gray-400 px-4 py-3">Oleh</th>
                <th class="border border-gray-400 px-4 py-3">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($transaksis as $transaksi)
                <tr class="bg-white">
                    <td class="border border-gray-400 px-4 py-2 whitespace-nowrap">{{ \Carbon\Carbon::parse($transaksi->tanggal_transaksi)->isoFormat('D MMM Y, HH:mm') }}</td>
                    <td class="border border-gray-400 px-4 py-2 text-center whitespace-nowrap">
                        <span class="font-semibold {{ str_contains($transaksi->jenis_transaksi, 'masuk') ? 'text-green-700' : 'text-red-700' }}">
                            {{ ucwords(str_replace('_', ' ', $transaksi->jenis_transaksi)) }}
                        </span>
                    </td>
                    {{-- IMPLEMENTASI NULLSAFE ?-> --}}
                    <td class="border border-gray-400 px-4 py-2">{!! $transaksi->bahan?->nama_bahan_html ?? '<span class="italic text-gray-500">Bahan Terhapus</span>' !!}</td>
                    <td class="border border-gray-400 px-4 py-2 text-center whitespace-nowrap">{{ $transaksi->bahan?->programStudi?->kode_program_studi ?? '-' }}</td>
                    <td class="border border-gray-400 px-4 py-2 whitespace-nowrap">{{ $transaksi->formatted_jumlah }}</td>
                    <td class="border border-gray-400 px-4 py-2 whitespace-nowrap">{{ $transaksi->user?->name ?? 'User Terhapus' }}</td>
                    <td class="border border-gray-400 px-4 py-2">{{ $transaksi->keterangan ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-6 text-center border border-gray-400 italic text-gray-500">
                        Data transaksi tidak ditemukan untuk periode ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection