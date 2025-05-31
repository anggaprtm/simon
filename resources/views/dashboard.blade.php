<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("Selamat Datang Kembali,") }} <span class="font-bold">{{ Auth::user()->name }}!</span>
                </div>
            </div>

            {{-- ============================================= --}}
            {{--           TAMPILAN UNTUK SUPERADMIN           --}}
            {{-- ============================================= --}}
            @if(in_array(Auth::user()->role, ['superadmin', 'fakultas']))
            <div class="mt-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <h3 class="font-bold text-lg">Total Jenis Bahan</h3>
                        <p class="text-3xl font-bold text-blue-600">{{ $data['total_bahan'] }}</p>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <h3 class="font-bold text-lg">Total Unit</h3>
                        <p class="text-3xl font-bold text-green-600">{{ $data['total_prodi'] }}</p>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <h3 class="font-bold text-lg">Total Pengguna</h3>
                        <p class="text-3xl font-bold text-indigo-600">{{ $data['total_user'] }}</p>
                    </div>
                </div>

                <div class="mt-8">
                    <h3 class="text-xl font-bold mb-4">Ringkasan per Unit</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach($data['ringkasan_prodi'] as $prodi)
                        <div class="bg-white p-4 rounded-lg shadow-md">
                            <h4 class="font-bold text-lg">{{ $prodi->nama_program_studi }}</h4>
                            <div class="mt-2 flex justify-between text-sm">
                                <span>Jumlah Bahan: <span class="font-semibold">{{ $prodi->bahans_count }}</span></span>
                                <span>Jumlah Petugas/Laboran: <span class="font-semibold">{{ $prodi->users_count }}</span></span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            {{-- ============================================= --}}
            {{--             TAMPILAN UNTUK LABORAN            --}}
            {{-- ============================================= --}}
            @if(Auth::user()->role == 'laboran')
            <div class="mt-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-white p-6 rounded-lg shadow-md col-span-1">
                        <h3 class="font-bold text-lg">Jumlah Jenis Bahan</h3>
                        <p class="text-3xl font-bold text-blue-600">{{ $data['jumlah_bahan'] }}</p>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow-md col-span-2">
                        <h3 class="font-bold text-lg">Unit/Program Studi</h3>
                        <p class="text-3xl font-bold text-gray-700">{{ Auth::user()->programStudi->nama_program_studi }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <h3 class="font-bold text-lg text-red-600 flex items-center"> {{-- Tambah class flex & items-center --}}
                            {{-- Ikon Peringatan Segitiga (Merah) --}}
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 mr-2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                            </svg>
                            Daftar Bahan Stok Menipis {{-- Teks Baru Sesuai Permintaan --}}
                        </h3>
                        <ul class="mt-2 list-disc list-inside">
                            @forelse($data['stok_menipis'] as $bahan)
                            <li>
                                <a href="{{ route('transaksi.history', $bahan->id) }}" class="text-blue-500 hover:underline">
                                    {{ $bahan->nama_bahan }}
                                </a> - Sisa <span class="font-bold">{{ $bahan->jumlah_stock }} {{ $bahan->satuan }}</span>
                            </li>
                            @empty
                            <p class="text-gray-500">Tidak ada bahan yang stoknya menipis.</p>
                            @endforelse
                        </ul>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <h3 class="font-bold text-lg text-yellow-600 flex items-center"> {{-- Tambah class flex & items-center --}}
                            {{-- Ikon Peringatan Segitiga (Kuning/Orange) --}}
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 mr-2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                            </svg>
                            Daftar Bahan Akan Kedaluwarsa {{-- Teks Baru yang Disesuaikan --}}
                        </h3>
                        <ul class="mt-2 list-disc list-inside">
                            @forelse($data['akan_kedaluwarsa'] as $bahan)
                            <li>
                                <a href="{{ route('transaksi.history', $bahan->id) }}" class="text-blue-500 hover:underline">
                                    {{ $bahan->nama_bahan }}
                                </a> - ED: <span class="font-bold">{{ \Carbon\Carbon::parse($bahan->tanggal_kedaluwarsa)->isoFormat('D MMM YY') }}</span>
                            </li>
                            @empty
                            <p class="text-gray-500">Tidak ada bahan yang akan kedaluwarsa dalam 60 hari ke depan.</p>
                            @endforelse
                        </ul>
                    </div>
                </div>

                <div class="mt-8 bg-white p-6 rounded-lg shadow-md">
                    <h3 class="text-xl font-bold mb-4">5 Aktivitas Terakhir di Unit/Prodi Anda</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <tbody>
                                @foreach($data['transaksi_terakhir'] as $trx)
                                <tr class="border-b">
                                    <td class="py-2">{{ \Carbon\Carbon::parse($trx->tanggal_transaksi)->diffForHumans() }}</td>
                                    <td class="py-2">{{ $trx->user->name }} mencatat
                                        <span class="font-semibold {{ str_contains($trx->jenis_transaksi, 'masuk') ? 'text-green-600' : 'text-red-600' }}">{{ $trx->jenis_transaksi }}</span>
                                        sebanyak {{ $trx->jumlah }} {{ $trx->bahan->satuan }} untuk bahan {{ $trx->bahan->nama_bahan }}.
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

        </div>
    </div>
</x-app-layout>