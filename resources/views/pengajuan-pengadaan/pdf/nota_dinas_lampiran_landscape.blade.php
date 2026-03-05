<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Lampiran - {{ $pengajuan->programStudi->nama_program_studi }}</title>
    <style>
        @page { size: A4 landscape; margin: 2cm; }
        body { font-family: 'Times New Roman', Times, serif; font-size: 11px; }
        .text-center { text-align: center; }
        .text-bold { font-weight: bold; }
        .mt-4 { margin-top: 1rem; }
        .lampiran-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .lampiran-table th, .lampiran-table td { border: 1px solid black; padding: 5px; text-align: left; vertical-align: top; }
        .lampiran-table th { background-color: #f7caac; text-align: center; vertical-align: middle; }
        .text-right { text-align: right; }

        /* Link panjang biar patah rapi */
        .wrap { word-break: break-word; }
    </style>
</head>
<body>
    <p>Lampiran Nota Dinas Permohonan Usulan Bahan Habis Pakai Laboratorium {{ $pengajuan->programStudi->nama_program_studi }}</p>
    <table>
        <tr>
            <td>Nomor</td>
            <td>: {{ $pengajuan->nomor_surat ?? '.................................' . $pengajuan->created_at->year }}</td>
        </tr>
        <tr>
            <td>Tanggal</td>
            <td>: {{ $pengajuan->created_at->isoFormat('D MMMM YYYY') }}</td>
        </tr>
    </table>

    <h3 class="text-center text-bold mt-4">USULAN PENGAJUAN BAHAN HABIS PAKAI</h3>
    <h3 class="text-center text-bold" style="margin-top:-10px;">LABORATORIUM {{ strtoupper($pengajuan->programStudi->nama_program_studi) }}</h3>

    <table class="lampiran-table">
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th>Nama Bahan</th>
                <th>Spesifikasi</th>
                <th style="width: 80px;">Stok Saat Ini</th>
                <th style="width: 70px;">Jumlah Diajukan</th>
                <th style="width: 60px;">Satuan</th>
                <th style="width: 100px;">Harga Satuan (HPS)</th>
                <th style="width: 110px;">Harga Total</th>
                <th style="width: 200px;">Link Referensi</th>
            </tr>
        </thead>
        <tbody>
            @php $totalKeseluruhan = 0; @endphp
            @foreach ($pengajuan->details as $detail)
                @php
                    // Ambil info stok jika bahan existing, jika baru tampilkan strip (-)
                    $isExisting = !is_null($detail->id_bahan);
                    $stokTeks = $isExisting && $detail->bahan ? ($detail->bahan->formatted_stock ?? ($detail->bahan->jumlah_stock + 0)) : '-';
                    $jumlahFinal = $detail->approved_jumlah ?? $detail->jumlah;
                @endphp
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>
                        {{ $detail->display_nama_barang }}
                        @if(!$isExisting)
                            <br><small style="color: #d97706;">(Bahan baru)</small>
                        @endif
                    </td>
                    <td>{{ $detail->spesifikasi ?: '-' }}</td>
                    <td class="text-center">{{ $stokTeks }}</td>
                    <td class="text-center">{{ $jumlahFinal + 0 }}</td>
                    <td class="text-center">{{ $detail->satuan->nama_satuan ?? '-' }}</td>
                    <td class="text-right">Rp{{ number_format($detail->harga_satuan, 0, ',', '.') }}</td>
                    <td class="text-right">Rp{{ number_format($jumlahFinal * $detail->harga_satuan, 0, ',', '.') }}</td>
                    <td class="wrap">
                        @if($detail->link_referensi)
                            <a href="{{ $detail->link_referensi }}" style="color: blue; text-decoration: underline;">
                                {{ $detail->link_referensi }}
                            </a>
                        @else
                            -
                        @endif
                    </td>
                </tr>
                @php $totalKeseluruhan += ($jumlahFinal * $detail->harga_satuan); @endphp
            @endforeach
            <tr>
                <td colspan="7" class="text-right text-bold">TOTAL KESELURUHAN</td>
                <td class="text-right text-bold">Rp{{ number_format($totalKeseluruhan, 0, ',', '.') }}</td>
                <td></td>
            </tr>
        </tbody>
    </table>
</body>
</html>