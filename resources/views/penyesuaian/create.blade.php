<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Form Penyesuaian Stok (Stok Opname)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    @if (session('error'))
                        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded" role="alert">
                            <strong class="font-bold">Terjadi Kesalahan!</strong>
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif
                <div class="p-6 text-gray-900">
                    <div class="mb-4 p-4 bg-yellow-100 border-l-4 border-yellow-500 rounded-r-lg">
                        <h3 class="font-bold text-lg">{{ $bahan->nama_bahan }} ({{ $bahan->merk }})</h3>
                        <p>Kode: {{ $bahan->kode_bahan }}</p>
                        <p>Stok Menurut Sistem: <span class="font-bold text-xl text-blue-600">{{ $bahan->jumlah_stock }} {{ $bahan->satuan }}</span></p>
                    </div>

                    <form action="{{ route('penyesuaian.store', $bahan->id) }}" method="POST">
                        @csrf
                        <div>
                            <x-input-label for="stok_fisik" class="font-bold text-md" :value="__('Masukkan Jumlah Stok Fisik Aktual')" />
                            <p class="text-sm text-gray-600 mb-2">Masukkan jumlah hasil perhitungan manual Anda di gudang.</p>
                            <x-text-input id="stok_fisik" class="block mt-1 w-full" type="number" name="stok_fisik" :value="old('stok_fisik', $bahan->jumlah_stock)" min="0" required autofocus />
                            <x-input-error :messages="$errors->get('stok_fisik')" class="mt-2" />
                        </div>
                        
                        <div class="mt-4">
                            <x-input-label for="keterangan" :value="__('Keterangan')" />
                            <textarea id="keterangan" name="keterangan" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" required>{{ old('keterangan', 'Hasil Stok Opname Fisik per ' . now()->isoFormat('D MMMM Y')) }}</textarea>
                            <x-input-error :messages="$errors->get('keterangan')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('bahan.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">Batal</a>
                            <x-primary-button class="bg-orange-600 hover:bg-orange-700">
                                {{ __('Simpan Penyesuaian') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>