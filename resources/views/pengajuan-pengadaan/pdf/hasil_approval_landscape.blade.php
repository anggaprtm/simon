{{-- resources/views/pengajuan-pengadaan/pdf/hasil_approval_landscape.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Hasil Approval Pengadaan - {{ $pengajuan->programStudi->nama_program_studi }}</title>
    <style>
        @page { size: A4 landscape; margin: 2cm; }
        body { font-family: 'Times New Roman', Times, serif; font-size: 11px; }
        .text-center { text-align: center; }
        .text-bold { font-weight: bold; }
        .mt-4 { margin-top: 1rem; }
        .lampiran-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .lampiran-table th, .lampiran-table td { border: 1px solid black; padding: 5px; text-align: left; vertical-align: top; }
        .lampiran-table th { background-color: #e2e8f0; text-align: center; vertical-align: middle; font-weight: bold; }
        .text-right { text-align: right; }
        .wrap { word-break: break-word; }
    </style>
</head>
<body>
    <h2 class="text-center text-bold mt-4">HASIL APPROVAL PENGADAAN BAHAN</h2>
    <h3 class="text-center text-bold" style="margin-top:-10px;">LABORATORIUM {{ strtoupper($pengajuan->programStudi->nama_program_studi) }}</h3>

    <table style="margin-top: 15px; width: 100%;">
        <tr>
            <td style="width: 80px;">Nomor Surat Dinas</td>
            <td>: {{ $pengajuan->nomor_surat ?? '-' }}</td>
        </tr>
        <tr>
            <td>Periode</td>
            <td>: Semester {{ $pengajuan->semester }} Tahun Ajaran {{ $pengajuan->tahun_ajaran }}</td>
        </tr>
    </table>

    <table class="lampiran-table">
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th>Nama Bahan</th>
                <th>Spesifikasi</th>
                <th style="width: 60px;">Diajukan</th>
                <th style="width: 60px;">Disetujui</th>
                <th style="width: 60px;">Satuan</th>
                <th style="width: 90px;">Harga Satuan</th>
                <th style="width: 90px;">Total Disetujui</th>
                <th style="width: 90px;">Status</th>
                <th style="width: 130px;">Catatan Revisi</th>
            </tr>
        </thead>
        <tbody>
            @php $totalKeseluruhan = 0; @endphp
            @foreach ($pengajuan->details as $detail)
                @php
                    $isExisting = !is_null($detail->id_bahan);
                    $jumlahFinal = $detail->approved_jumlah ?? 0;
                    $totalHarga = $jumlahFinal * $detail->harga_satuan;
                    $totalKeseluruhan += $totalHarga;
                    
                    $statusLabel = ucwords(str_replace('_', ' ', $detail->status_item));
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
                    <td class="text-center">{{ floatval($detail->jumlah) }}</td>
                    <td class="text-center text-bold">{{ floatval($jumlahFinal) }}</td>
                    <td class="text-center">{{ $detail->satuan->nama_satuan ?? '-' }}</td>
                    <td class="text-right">Rp{{ number_format($detail->harga_satuan, 0, ',', '.') }}</td>
                    <td class="text-right text-bold">Rp{{ number_format($totalHarga, 0, ',', '.') }}</td>
                    <td class="text-center">{{ $statusLabel }}</td>
                    <td>{{ $detail->catatan_revisi ?: '-' }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="7" class="text-right text-bold">TOTAL KESELURUHAN (HPS DISETUJUI)</td>
                <td class="text-right text-bold">Rp{{ number_format($totalKeseluruhan, 0, ',', '.') }}</td>
                <td colspan="2"></td>
            </tr>
        </tbody>
    </table>
</body>
</html>