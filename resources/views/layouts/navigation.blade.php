<nav x-data="{ open: false, activeDropdown: null }" class="bg-white border-b border-gray-100 sticky top-0 z-50 shadow-sm">
    <style>
        .nav-item {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 6px 11px; border-radius: 8px;
            font-size: 13px; font-weight: 600;
            color: #6b7280; transition: all 0.15s;
            white-space: nowrap; font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .nav-item:hover { background: #f3f4f6; color: #1f2937; }
        .nav-item.active { background: #eef2ff; color: #4f46e5; }
        .nav-item svg { width: 15px; height: 15px; }

        .nav-dropdown-item {
            display: flex; align-items: center; gap: 8px;
            padding: 8px 14px; font-size: 13px; font-weight: 500;
            color: #374151; border-radius: 8px; transition: all 0.12s;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .nav-dropdown-item:hover { background: #f3f4f6; color: #4f46e5; }
        .nav-dropdown-item.active { background: #eef2ff; color: #4f46e5; font-weight: 700; }
    </style>

    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-14 items-center">

            {{-- LOGO + LINKS --}}
            <div class="flex items-center gap-1">
                <a href="{{ route('dashboard') }}" class="shrink-0 flex items-center mr-3">
                    <img src="{{ asset('images/logo-nav.png') }}" alt="Logo" class="h-8 w-auto">
                </a>

                {{-- DESKTOP NAV --}}
                <div class="hidden sm:flex items-center gap-0.5">

                    {{-- Dashboard (laboran only) --}}
                    @if(Auth::user()->role === 'laboran')
                    <a href="{{ route('dashboard') }}"
                       class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        Dashboard
                    </a>
                    @endif

                    {{-- Bahan --}}
                    <a href="{{ route('bahan.index') }}"
                       class="nav-item {{ request()->routeIs('bahan.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                        Bahan
                    </a>

                    {{-- Gudang (laboran only) --}}
                    @if(Auth::user()->role === 'laboran')
                    <a href="{{ route('gudang.index') }}"
                       class="nav-item {{ request()->routeIs('gudang.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                        Gudang
                    </a>
                    @endif

                    {{-- Pengajuan --}}
                    <a href="{{ route('pengajuan-pengadaan.index') }}"
                       class="nav-item {{ request()->routeIs('pengajuan-pengadaan.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Pengadaan
                    </a>

                    {{-- Laporan --}}
                    <a href="{{ route('laporan.index') }}"
                       class="nav-item {{ request()->routeIs('laporan.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        Laporan
                    </a>

                    {{-- SUPERADMIN DROPDOWN --}}
                    @if(Auth::user()->role === 'superadmin')
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" @click.outside="open = false"
                            class="nav-item {{ request()->routeIs('program-studi.*', 'master-barang.*', 'satuan.*', 'periode.*', 'user.*') ? 'active' : '' }}">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            Master Data
                            <svg class="ml-0.5 transition-transform duration-150" :class="open ? 'rotate-180' : ''" style="width:12px;height:12px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                        </button>

                        <div x-show="open" x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                             class="absolute left-0 mt-2 w-52 bg-white rounded-xl border border-gray-100 shadow-xl p-1.5 z-50"
                             style="display:none">

                            <a href="{{ route('program-studi.index') }}" class="nav-dropdown-item {{ request()->routeIs('program-studi.*') ? 'active' : '' }}">
                                <svg style="width:15px;height:15px;flex-shrink:0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                Unit / Program Studi
                            </a>
                            <a href="{{ route('master-barang.index') }}" class="nav-dropdown-item {{ request()->routeIs('master-barang.*') ? 'active' : '' }}">
                                <svg style="width:15px;height:15px;flex-shrink:0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                                Master Barang
                            </a>
                            <a href="{{ route('satuan.index') }}" class="nav-dropdown-item {{ request()->routeIs('satuan.*') ? 'active' : '' }}">
                                <svg style="width:15px;height:15px;flex-shrink:0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                Master Satuan
                            </a>
                            <div class="my-1 border-t border-gray-100"></div>
                            <a href="{{ route('periode.index') }}" class="nav-dropdown-item {{ request()->routeIs('periode.*') ? 'active' : '' }}">
                                <svg style="width:15px;height:15px;flex-shrink:0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                Manajemen Periode
                            </a>
                            <a href="{{ route('user.index') }}" class="nav-dropdown-item {{ request()->routeIs('user.*') ? 'active' : '' }}">
                                <svg style="width:15px;height:15px;flex-shrink:0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                Manajemen User
                            </a>
                        </div>
                    </div>
                    @endif

                </div>
            </div>

            {{-- RIGHT — USER DROPDOWN --}}
            <div class="hidden sm:flex items-center gap-2">
                {{-- Role badge --}}
                <span class="text-xs font-semibold px-2.5 py-1 rounded-full
                    @if(Auth::user()->role === 'superadmin') bg-red-100 text-red-700
                    @elseif(Auth::user()->role === 'laboran') bg-indigo-100 text-indigo-700
                    @elseif(Auth::user()->role === 'fakultas') bg-amber-100 text-amber-700
                    @else bg-emerald-100 text-emerald-700 @endif">
                    {{ ucfirst(Auth::user()->role) }}
                </span>

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="flex items-center gap-2 pl-2 pr-3 py-1.5 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 transition-all text-sm font-semibold text-gray-700 focus:outline-none">
                            <div class="w-7 h-7 rounded-lg bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-xs flex-shrink-0">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                            <span class="max-w-[120px] truncate">{{ Auth::user()->name }}</span>
                            <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <div class="px-4 py-3 border-b border-gray-100">
                            <p class="text-xs font-bold text-gray-800 truncate">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-gray-400 truncate">{{ Auth::user()->email }}</p>
                        </div>
                        <x-dropdown-link :href="route('profile.edit')">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                Profil
                            </div>
                        </x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                <div class="flex items-center gap-2 text-red-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                    Keluar
                                </div>
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            {{-- HAMBURGER --}}
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = !open" class="inline-flex items-center justify-center p-2 rounded-xl text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition duration-150">
                    <svg class="h-5 w-5" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': !open}" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        <path :class="{'hidden': !open, 'inline-flex': open}" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- RESPONSIVE MENU --}}
    <div :class="{'block': open, 'hidden': !open}" class="hidden sm:hidden border-t border-gray-100 bg-white">
        <div class="px-4 pt-3 pb-2 space-y-1">

            @if(Auth::user()->role === 'laboran')
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                Dashboard
            </x-responsive-nav-link>
            @endif

            <x-responsive-nav-link :href="route('bahan.index')" :active="request()->routeIs('bahan.*')">
                Bahan
            </x-responsive-nav-link>

            @if(Auth::user()->role === 'laboran')
            <x-responsive-nav-link :href="route('gudang.index')" :active="request()->routeIs('gudang.*')">
                Gudang
            </x-responsive-nav-link>
            @endif

            @if(Auth::user()->role === 'superadmin')
            <x-responsive-nav-link :href="route('program-studi.index')" :active="request()->routeIs('program-studi.*')">
                Unit / Program Studi
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('master-barang.index')" :active="request()->routeIs('master-barang.*')">
                Master Barang
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('satuan.index')" :active="request()->routeIs('satuan.*')">
                Master Satuan
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('periode.index')" :active="request()->routeIs('periode.*')">
                Manajemen Periode
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('user.index')" :active="request()->routeIs('user.*')">
                Manajemen User
            </x-responsive-nav-link>
            @endif

            <x-responsive-nav-link :href="route('pengajuan-pengadaan.index')" :active="request()->routeIs('pengajuan-pengadaan.*')">
                Pengajuan Pengadaan
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('laporan.index')" :active="request()->routeIs('laporan.*')">
                Laporan
            </x-responsive-nav-link>
        </div>

        <div class="pt-3 pb-4 border-t border-gray-100">
            <div class="px-4 mb-2">
                <p class="font-bold text-sm text-gray-800">{{ Auth::user()->name }}</p>
                <p class="text-xs text-gray-400">{{ Auth::user()->email }}</p>
            </div>
            <div class="space-y-1 px-2">
                <x-responsive-nav-link :href="route('profile.edit')">Profil</x-responsive-nav-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                        Keluar
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>