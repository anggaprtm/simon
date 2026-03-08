<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="icon" href="{{ asset('images/favicon.png') }}" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="font-sans antialiased bg-gray-50">

    {{-- Tambahkan flex flex-col agar footer bisa menempel di bawah --}}
    <div class="min-h-screen flex flex-col">
        @include('layouts.navigation')

        {{-- Page Content (Tambahkan flex-grow agar mengisi sisa ruang kosong) --}}
        <main class="flex-grow">
            {{ $slot }}
        </main>

        {{-- Footer --}}
        <footer class="bg-white border-t border-gray-200 mt-auto">
            <div class="max-w-7xl mx-auto py-5 px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                    <div class="text-sm text-gray-500 text-center md:text-left">
                        &copy; {{ date('Y') }} <span class="font-bold text-gray-700">SiMon</span> - USI FTMM.
                    </div>
                    
                    <div class="text-sm text-gray-500 text-center md:text-right flex items-center justify-center gap-1.5 font-medium">
                        Dibuat dengan 
                        <span class="text-red-500 animate-pulse">❤️</span> 
                        untuk Fakultas Teknologi Maju dan Multidisiplin
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    @stack('scripts')
    {{-- Floating Bug Reporter (Kucing Ngintip) --}}
    <div class="fixed bottom-0 right-10 z-50 group drop-shadow-xl">
        {{-- Tooltip Balon Teks --}}
        <div class="absolute -top-16 right-0 transform translate-x-4 opacity-0 group-hover:opacity-100 transition-all duration-300 pointer-events-none mb-2">
            <div class="bg-white text-gray-800 text-xs font-extrabold px-4 py-3 rounded-2xl shadow-lg border border-gray-100 whitespace-nowrap flex items-center gap-2">
                <span class="text-xl animate-bounce">🐛</span>
                <div>
                    <span class="block text-indigo-600">Ada Bug atau Error?</span>
                    <span class="text-gray-500 font-medium">Lapor ke Mimin yuk!</span>
                </div>
                {{-- Panah bawah balon --}}
                <div class="absolute -bottom-2 right-12 w-4 h-4 bg-white border-b border-r border-gray-100 transform rotate-45 rounded-sm"></div>
            </div>
        </div>

        {{-- Tombol/Link WA --}}
        {{-- Ganti nomor WA di bawah ini (gunakan format 628...) --}}
        <a href="https://wa.me/6285155309409?text=Halo%20Admin%20SIMON,%20saya%20mau%20lapor%20bug/error%20di%20aplikasi..." 
           target="_blank" 
           class="block transform translate-y-10 group-hover:translate-y-1 transition-transform duration-300 ease-out cursor-pointer">
            
            {{-- SVG Karakter Kucing Biru/Indigo --}}
            <svg class="w-24 h-24 text-indigo-900" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M20 50 L 15 15 L 45 35" fill="#312E81" stroke="#312E81" stroke-width="2" stroke-linejoin="round"/>
                <path d="M22 45 L 20 25 L 38 35" fill="#FCA5A5"/>
                
                <path d="M80 50 L 85 15 L 55 35" fill="#312E81" stroke="#312E81" stroke-width="2" stroke-linejoin="round"/>
                <path d="M78 45 L 80 25 L 62 35" fill="#FCA5A5"/>

                <path d="M15 100 V 50 C 15 25, 85 25, 85 50 V 100" fill="#3730A3" />
                
                <ellipse cx="35" cy="55" rx="6" ry="8" fill="#FFFFFF"/>
                <circle cx="36" cy="56" r="3" fill="#111827"/>
                <circle cx="38" cy="54" r="1" fill="#FFFFFF"/> <ellipse cx="65" cy="55" rx="6" ry="8" fill="#FFFFFF"/>
                <circle cx="64" cy="56" r="3" fill="#111827"/>
                <circle cx="62" cy="54" r="1" fill="#FFFFFF"/> <path d="M50 66 L 46 61 L 54 61 Z" fill="#FCA5A5" stroke="#F87171" stroke-width="1" stroke-linejoin="round"/>
                
                <path d="M42 68 Q 46 73 50 68 Q 54 73 58 68" stroke="#FFFFFF" stroke-width="2" fill="none" stroke-linecap="round"/>
                
                <path d="M25 60 L 10 58 M 25 65 L 10 65 M 25 70 L 12 74" stroke="#FFFFFF" stroke-width="1.5" stroke-linecap="round" opacity="0.5"/>
                <path d="M75 60 L 90 58 M 75 65 L 90 65 M 75 70 L 88 74" stroke="#FFFFFF" stroke-width="1.5" stroke-linecap="round" opacity="0.5"/>

                <path d="M20 100 Q 20 85 32 85 Q 44 85 44 100" fill="#4F46E5" stroke="#312E81" stroke-width="2"/>
                <path d="M26 85 V 95 M 32 85 V 95 M 38 85 V 95" stroke="#312E81" stroke-width="2" stroke-linecap="round"/> <path d="M80 100 Q 80 85 68 85 Q 56 85 56 100" fill="#4F46E5" stroke="#312E81" stroke-width="2"/>
                <path d="M74 85 V 95 M 68 85 V 95 M 62 85 V 95" stroke="#312E81" stroke-width="2" stroke-linecap="round"/> </svg>
        </a>
    </div>
</body>
</html>