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
        .lampiran-table th, .lampiran-table td { border: 1px solid black; padding: 5px; text-align: left; }
        .lampiran-table th { background-color: #f7caac; text-align: center; }
        .text-right { text-align: right; }

        /* Link panjang biar patah rapi */
        .wrap { word-break: break-word; }

        /* Spasi heading */
        .mt-4{ margin-top:1rem; }
    </style>
</head>
<body>
    <p>Lampiran Nota Dinas Permohonan Usulan Bahan Habis Pakai Laboratorium {{ $pengajuan->programStudi->nama_program_studi }}</p>
    <table>
        <tr>
            <td">Nomor</td>
            <td>: {{ $pengajuan->nomor_surat ?? '.........../B/UN3.FTMM/RN/PL.00/' . $pengajuan->created_at->year }}</td>
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
                <th style="width:28px;">No</th>
                <th>Nama Bahan</th>
                <th style="width:110px;">Merek/Type</th>
                <th>Spesifikasi</th>
                <th style="width:50px;">Vol</th>
                <th style="width:90px;">Jumlah</th>
                <th style="width:110px;">Harga Satuan (HPS)</th>
                <th style="width:120px;">Jumlah Harga (HPS)</th>
                <th style="width:200px;">Link Referensi</th>
            </tr>
        </thead>
        <tbody>
            @php $totalKeseluruhan = 0; @endphp
            @foreach ($pengajuan->details as $detail)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ $detail->masterBarang->nama_barang }}</td>
                    <td>{{ $detail->merk }}</td>
                    <td>{{ $detail->spesifikasi }}</td>
                    <td class="text-center">{{ $detail->jumlah }}</td>
                    <td class="text-center">{{ $detail->jumlah }} {{ $detail->satuan->nama_satuan }}</td>
                    <td class="text-right">Rp{{ number_format($detail->harga_satuan, 0, ',', '.') }}</td>
                    <td class="text-right">Rp{{ number_format($detail->jumlah * $detail->harga_satuan, 0, ',', '.') }}</td>
                    <td class="wrap">
                        <a href="{{ $detail->link_referensi }}" style="color: blue; text-decoration: underline;">
                            {{ $detail->link_referensi }}
                        </a>
                    </td>
                </tr>
                @php $totalKeseluruhan += ($detail->jumlah * $detail->harga_satuan); @endphp
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
