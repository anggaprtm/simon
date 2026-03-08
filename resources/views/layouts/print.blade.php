{{-- resources/views/layouts/print.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Cetak Laporan')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body { -webkit-print-color-adjust: exact; }
            .no-print { display: none; }
            /* Memastikan blok tanda tangan tidak terpotong ke halaman baru */
            .signature-section { page-break-inside: avoid; }
        }
    </style>
</head>
<body class="bg-white p-8 font-sans text-gray-900">
    
    {{-- HEADER KOP LAPORAN --}}
    <div class="text-center mb-8 border-b-2 border-gray-800 pb-4">
        <h1 class="text-2xl font-bold uppercase">@yield('title', 'Laporan')</h1>
        <p class="text-lg font-semibold">Sistem Informasi Stok Opname Laboratorium</p>
        <p class="text-sm text-gray-600">Dicetak pada: {{ now()->isoFormat('D MMMM YYYY, HH:mm:ss') }}</p>
    </div>

    {{-- KONTEN UTAMA DARI MASING-MASING LAPORAN --}}
    @yield('content')

    {{-- ====================================== --}}
    {{-- ==>       BLOK TANDA TANGAN        <== --}}
    {{-- ====================================== --}}
    <div class="signature-section mt-12 pt-8 text-sm w-full">
        
        {{-- Baris Pertama: Operator Persediaan (Kiri) & Laboran (Kanan) --}}
        <div class="flex justify-between w-full">
            <div class="w-1/3 text-center">
                <p class="mb-24">Operator Persediaan,</p>
                <p class="font-bold underline">Yuliana Ariandini Ayuningtiyas, S.IIP</p>
                <p>NIK. 198907282022113201</p>
            </div>

            <div class="w-1/3 text-center">
                <p>Surabaya, {{ now()->isoFormat('D MMMM YYYY') }}</p>
                @if(Auth::check() && Auth::user()->role === 'laboran' && Auth::user()->programStudi)
                    <p class="mb-20">Laboran {{ Auth::user()->programStudi->nama_program_studi }},</p>
                @else
                    <p class="mb-20">Operator Bahan,</p> 
                @endif
                
                {{-- Fallback: Cek nama_lengkap atau name, dan nik atau nip --}}
                <p class="font-bold underline">
                    {{ Auth::check() ? (Auth::user()->nama_lengkap ?? Auth::user()->name) : '_________________________' }}
                </p>
                <p>
                    NIK. {{ Auth::check() && (Auth::user()->nik ?? Auth::user()->nip) ? (Auth::user()->nik ?? Auth::user()->nip) : '...............................' }}
                </p>
            </div>
        </div>

        {{-- Baris Kedua: Kasubag Sarpras (Tengah) --}}
        <div class="flex justify-center mt-12 w-full">
            <div class="w-1/3 text-center">
                <p>Mengetahui,</p>
                <p class="mb-24">Kasubag Sarana dan Prasarana,</p>
                <p class="font-bold underline">Boedi Rahardjo, S.Sos</p>
                <p>NIK. 196907301990031002</p>
            </div>
        </div>

    </div>

    {{-- SCRIPT AUTO PRINT --}}
    <script>
        window.onload = function() {
            // Beri jeda 500ms agar font dan class Tailwind via CDN termuat sempurna sebelum dialog print muncul
            setTimeout(function() {
                window.print();
            }, 500);
        }
    </script>
</body>
</html>