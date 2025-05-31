<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Laporan Stok Bahan') }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{-- Form Filter --}}
                    <form method="GET" action="{{ route('laporan.stok') }}" class="mb-6 flex flex-wrap items-center gap-4">
                        @if(in_array(Auth::user()->role, ['superadmin', 'fakultas']))
                            <div class="flex-1 min-w-[200px]">
                                <label for="prodi_id" class="block text-sm font-medium text-gray-700">Program Studi</label>
                                <select name="prodi_id" id="prodi_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                    <option value="">-- Semua Prodi --</option>
                                    @foreach($programStudis as $prodi)
                                        <option value="{{ $prodi->id }}" {{ request('prodi_id') == $prodi->id ? 'selected' : '' }}>
                                            {{ $prodi->nama_program_studi }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        <div class="pt-6">
                            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md shadow-sm hover:bg-blue-700">Filter</button>
                            {{-- Tombol Print akan membuka tab baru dengan URL yang sama + parameter 'print=true' --}}
                            <a href="{{ route('laporan.stok', request()->all() + ['print' => 'true']) }}" target="_blank" class="bg-gray-600 text-white px-4 py-2 rounded-md shadow-sm hover:bg-gray-700">Cetak Laporan</a>
                        </div>
                    </form>
                    
                    {{-- Tabel Laporan --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            {{-- ...thead dan tbody sama seperti di bahan/index.blade.php... --}}
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>