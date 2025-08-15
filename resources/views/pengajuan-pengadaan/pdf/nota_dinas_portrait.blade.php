<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Nota Dinas - {{ $pengajuan->programStudi->nama_program_studi }}</title>
    <style>
        @page { size: A4 portrait; margin: 2.5cm 2cm; }
        body { font-family: 'Times New Roman', Times, serif; font-size: 14px; line-height: 1.5; }
        .text-center{ text-align:center } .text-bold{ font-weight:bold } .underline{ text-decoration:underline }
        .mt-4{ margin-top:1rem } .mt-8{ margin-top:2rem } .mb-4{ margin-bottom:1rem }
        .kop-surat{ text-align:center; border-bottom:2px solid #000; padding-bottom:10px; position:relative }
        .kop-surat img{ width:80px; height:80px; position:absolute; top:20px; left:20px }
        .kop-surat .kop-text{ margin-left:100px; text-align:center }
        .kop-surat h1{ font-size:16px; font-weight:bold; margin:0 }
        .kop-surat h2{ font-size:14px; font-weight:bold; margin:0 }
        .kop-surat p{ font-size:10px; margin:2px 0 }
        .meta-table{ width:100%; margin-top:20px } .meta-table td{ vertical-align:top; padding:2px 0 }
        .meta-table td:first-child{ width:80px } .meta-table td:nth-child(2){ width:10px }
        .signature-section{ margin-top:40px }
    </style>
</head>
<body>
    <div>
        <div class="kop-surat">
            <img src="images/logo.png" alt="Logo Universitas" width="100" style="margin-top:-28px;">
            <div class="kop-text">
                <h1>UNIVERSITAS AIRLANGGA</h1>
                <h2>FAKULTAS TEKNOLOGI MAJU DAN MULTIDISIPLIN</h2>
                <p>Gedung Nano Kampus C Mulyorejo Surabaya 60115 Telp. (031) 59182123, 0881036000830</p>
                <p>Laman: https://ftmm.unair.ac.id, e-mail: info@ftmm.unair.ac.id</p>
            </div>
        </div>

        <h3 class="text-center text-bold mt-3 mb-4">NOTA DINAS</h3>
        <p class="text-center" style="margin-top:-17px;">
            Nomor: {{ $pengajuan->nomor_surat ?? '.........../B/UN3.FTMM/RN/PL.00/' . $pengajuan->created_at->year }}
        </p>

        <table class="meta-table">
            <tr><td><strong>Yth</strong></td><td>:</td><td>Dekan</td></tr>
            <tr><td><strong>Dari</strong></td><td>:</td><td>KPS {{ $pengajuan->programStudi->nama_program_studi }}</td></tr>
            <tr><td><strong>Lampiran</strong></td><td>:</td><td>{{ $jumlah_lampiran }} lembar</td></tr>
            <tr><td><strong>Hal</strong></td><td>:</td><td>Permohonan Usulan Bahan Habis Pakai Laboratorium {{ $pengajuan->programStudi->nama_program_studi }}</td></tr>
        </table>

        <p class="mt-8">
            Sehubungan dengan dilaksanakannya Praktikum di Program Studi {{ $pengajuan->programStudi->nama_program_studi }}
            yang akan diselenggarakan pada Semester {{ $pengajuan->semester }} {{ $pengajuan->tahun_ajaran }}, maka bersama ini kami
            menyampaikan permohonan usulan kebutuhan bahan habis pakai sebagaimana terlampir.
        </p>
        <p>Demikian atas perhatiannya, kami sampaikan terima kasih.</p>

        <div class="signature-section" style="width:50%; float:right; text-align:center;">
            <p>{{ $pengajuan->created_at->isoFormat('D MMMM YYYY') }}</p>
            <p>Koordinator Program Studi,</p>
            <div style="height:80px;"></div>
            <p class="text-bold underline">(.................................................)</p>
            <p>NIP. .........................................</p>
        </div>
    </div>
</body>
</html>
