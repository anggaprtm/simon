<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-g">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased font-sans">
    <div class="bg-gray-50 text-black/50 dark:bg-black dark:text-white/50">
        <div class="relative min-h-screen flex flex-col items-center justify-center selection:bg-red-500 selection:text-white">
            <div class="relative w-full max-w-2xl px-6 lg:max-w-7xl">
                <header class="grid grid-cols-2 items-center gap-2 py-10 lg:grid-cols-3">
                    <div class="flex lg:justify-center lg:col-start-2">
                       <img src="{{ asset('images/logo-ftmm.png') }}" alt="Logo FTMM" class="h-12 w-auto">
                       <!-- <svg class="h-12 w-auto text-red-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                           <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m5.231 13.481L15 17.25m-4.5-15H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                       </svg> -->
                    </div>
                    <nav class="-mx-3 flex flex-1 justify-end">

                         <a
                            href="{{ route('register') }}"
                            class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                        >
                            Register
                        </a>
                    </nav>
                </header>

                <main class="mt-6">
                    <div class="text-center">
                        <h1 class="text-4xl md:text-5xl font-extrabold text-gray-800 dark:text-white">
                            SiMon
                        </h1>
                        <h2 class="text-3xl md:text-4xl font-extrabold text-gray-800 dark:text-white">
                            Sistem Informasi Monitoring & Opname
                        </h2>
                        <p class="mt-4 text-lg text-gray-600 dark:text-gray-400">
                            Manajemen inventaris bahan laboratorium dan habis pakai menjadi lebih mudah, efisien, dan terpusat.
                        </p>
                        <div class="mt-8">
                            <a href="{{ route('login') }}" class="inline-block rounded-lg bg-red-600 px-8 py-3 text-lg font-semibold text-white shadow-lg hover:bg-red-500 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-50">
                                Login
                            </a>
                        </div>
                    </div>
                </main>

                <footer class="py-16 text-center text-sm text-black/50 dark:text-white/50">
                    Copyright &copy; {{ date('Y') }} Fakultas Teknologi Maju dan Multidisiplin. All rights reserved.
                </footer>
            </div>
        </div>
    </div>
</body>
</html>