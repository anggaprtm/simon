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
    
    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>