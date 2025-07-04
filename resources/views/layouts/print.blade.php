{{-- resources/views/layouts/print.blade.php (Disempurnakan) --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{-- Judul halaman dinamis --}}
    <title>@yield('title', 'Cetak Laporan')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body { -webkit-print-color-adjust: exact; }
            .no-print { display: none; }
        }
    </style>
</head>
<body class="bg-white p-8 font-sans">
    <div class="text-center mb-8">
        {{-- Judul laporan dinamis --}}
        <h1 class="text-2xl font-bold">@yield('title', 'Laporan')</h1>
        <p>Sistem Informasi Stok Opname Laboratorium</p>
        <p class="text-sm">Dicetak pada: {{ now()->isoFormat('D MMMM YYYY, HH:mm:ss') }}</p>
    </div>

    @yield('content')

    {{-- ====================================== --}}
    {{-- ==>       BLOK TANDA TANGAN        <== --}}
    {{-- ====================================== --}}
    <div class="signature-section mt-8 pt-4 text-sm">
        <div class="flex justify-between">
            <div class="w-1/3 text-center">
                <p class="mb-20">Operator Persediaan</p>
                <p class="font-bold underline">Yuliana Ariandini Ayuningtiyas, S.IIP</p>
                <p>NIK. 198907282022113201</p>
            </div>

            <div class="w-1/3 text-center">
            @if(Auth::check() && Auth::user()->role === 'laboran' && Auth::user()->programStudi)
                <p class="mb-20">Laboran {{ Auth::user()->programStudi->nama_program_studi }}</p>
             @else
                <p class="mb-20">Operator Bahan</p> 
            @endif
                <p class="font-bold underline">
                    {{ Auth::check() ? Auth::user()->nama_lengkap : '_________________________' }}
                </p>
                <p>
                    NIK. {{ Auth::check() && Auth::user()->nik ? Auth::user()->nik : '...............................' }}
                </p>
            </div>
        </div>

        <div class="flex justify-center mt-16">
            <div class="w-1/3 text-center">
                <p>Mengetahui,</p>
                <p class="mb-20">Kasubag Sarana dan Prasarana</p>
                <p class="font-bold underline">Boedi Rahardjo, S.Sos</p>
                <p>NIK. 196907301990031002 </p>
            </div>
        </div>
    </div>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>