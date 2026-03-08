{{-- resources/views/laporan/arsip.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Arsip Laporan Bulanan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            
            {{-- Alert Messages --}}
            @if (session('success'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative shadow-sm">
                    <span class="block sm:inline font-semibold">{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative shadow-sm">
                    <span class="block sm:inline font-semibold">{{ session('error') }}</span>
                </div>
            @endif

            {{-- FORM UNGGAH ARSIP BARU --}}
            <div class="bg-indigo-50 border border-indigo-100 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-indigo-900 mb-4 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" /></svg>
                        Unggah Arsip Laporan Final
                    </h3>
                    <p class="text-sm text-indigo-700 mb-6">Unggah file PDF laporan (Stok / Transaksi) yang telah ditandatangani. Jika Anda mengunggah laporan untuk bulan & tahun yang sama, file lama akan otomatis tergantikan.</p>

                    <form action="{{ route('laporan.arsip.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
                            
                            @if(in_array(Auth::user()->role, ['superadmin', 'fakultas']))
                            <div>
                                <x-input-label for="id_program_studi" :value="__('Program Studi')" />
                                <select name="id_program_studi" id="id_program_studi" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" required>
                                    <option value="">-- Pilih Prodi --</option>
                                    @foreach($programStudis as $prodi)
                                        <option value="{{ $prodi->id }}">{{ $prodi->nama_program_studi }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @endif

                            <div>
                                <x-input-label for="jenis_laporan" :value="__('Jenis Laporan')" />
                                <select name="jenis_laporan" id="jenis_laporan" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" required>
                                    <option value="stok">Laporan Stok</option>
                                    <option value="transaksi">Laporan Transaksi</option>
                                </select>
                            </div>

                            <div>
                                <x-input-label for="bulan" :value="__('Bulan')" />
                                <select name="bulan" id="bulan" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" required>
                                    @php
                                        $namaBulan = ['1'=>'Januari', '2'=>'Februari', '3'=>'Maret', '4'=>'April', '5'=>'Mei', '6'=>'Juni', '7'=>'Juli', '8'=>'Agustus', '9'=>'September', '10'=>'Oktober', '11'=>'November', '12'=>'Desember'];
                                        $bulanSekarang = date('n');
                                    @endphp
                                    @foreach($namaBulan as $num => $nama)
                                        <option value="{{ $num }}" {{ $bulanSekarang == $num ? 'selected' : '' }}>{{ $nama }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <x-input-label for="tahun" :value="__('Tahun')" />
                                <select name="tahun" id="tahun" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" required>
                                    @foreach($years as $year)
                                        <option value="{{ $year }}" {{ date('Y') == $year ? 'selected' : '' }}>{{ $year }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="{{ in_array(Auth::user()->role, ['superadmin', 'fakultas']) ? 'lg:col-span-2' : 'lg:col-span-1' }}">
                                <x-input-label for="file_laporan" :value="__('File PDF (Maks. 5MB)')" />
                                <input type="file" name="file_laporan" id="file_laporan" accept=".pdf" class="block w-full mt-1 text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-600 file:text-white hover:file:bg-indigo-700 cursor-pointer border border-gray-300 rounded-md bg-white" required>
                                @error('file_laporan')
                                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-4 flex justify-end">
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded shadow-sm">
                                Simpan Arsip
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- DAFTAR ARSIP & FILTER --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
                        <h3 class="text-lg font-bold">Daftar Arsip Laporan</h3>
                        
                        {{-- Form Filter --}}
                        <form method="GET" action="{{ route('laporan.arsip') }}" class="flex items-center gap-2 w-full md:w-auto">
                            @if(in_array(Auth::user()->role, ['superadmin', 'fakultas']))
                                <select name="prodi_id" class="border-gray-300 rounded-md shadow-sm text-sm">
                                    <option value="">Semua Prodi</option>
                                    @foreach($programStudis as $prodi)
                                        <option value="{{ $prodi->id }}" {{ request('prodi_id') == $prodi->id ? 'selected' : '' }}>{{ $prodi->kode_program_studi }}</option>
                                    @endforeach
                                </select>
                            @endif
                            <select name="tahun" class="border-gray-300 rounded-md shadow-sm text-sm">
                                @foreach($years as $year)
                                    <option value="{{ $year }}" {{ $selectedTahun == $year ? 'selected' : '' }}>Tahun {{ $year }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white text-sm font-bold py-2 px-4 rounded">
                                Filter
                            </button>
                        </form>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bulan & Tahun</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Laporan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Program Studi</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Diunggah Oleh</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu Unggah</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($arsips as $arsip)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap font-bold text-gray-900">
                                            {{ $namaBulan[$arsip->bulan] }} {{ $arsip->tahun }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $arsip->jenis_laporan === 'stok' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                                Laporan {{ ucfirst($arsip->jenis_laporan) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            {{ $arsip->programStudi->nama_program_studi ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            {{ $arsip->user->name ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $arsip->updated_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ asset('storage/' . $arsip->file_path) }}" target="_blank" class="text-indigo-600 hover:text-indigo-900 flex items-center gap-1 font-bold">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                                Lihat PDF
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                            Belum ada arsip laporan yang diunggah untuk tahun {{ $selectedTahun }}.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>