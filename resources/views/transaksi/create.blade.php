{{-- resources/views/transaksi/create.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{-- Judul dinamis tergantung jenis transaksi --}}
            Form Stok {{ $jenis === 'masuk' ? 'Masuk' : 'Keluar' }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-4 p-4 bg-gray-100 rounded-lg">
                        <h3 class="font-bold text-lg">{{ $bahan->nama_bahan }} ({{ $bahan->merk }})</h3>
                        <p>Kode: {{ $bahan->kode_bahan }}</p>
                        <p>Stok Saat Ini: <span class="font-bold text-blue-600">{{ $bahan->jumlah_stock }} {{ $bahan->satuan }}</span></p>
                    </div>

                    <form action="{{ $jenis === 'masuk' ? route('transaksi.storeMasuk', $bahan->id) : route('transaksi.storeKeluar', $bahan->id) }}" method="POST">
                        @csrf
                        <div>
                            <x-input-label for="jumlah" :value="__('Jumlah')" />
                            <x-text-input id="jumlah" class="block mt-1 w-full" type="number" name="jumlah" :value="old('jumlah')" min="1" step="any" required autofocus />
                            <x-input-error :messages="$errors->get('jumlah')" class="mt-2" />
                        </div>
                        <div class="mt-4">
                            <x-input-label for="tanggal_transaksi" :value="__('Tanggal & Waktu Transaksi')" />
                            {{-- Ubah type menjadi "datetime-local" dan sesuaikan format value --}}
                            <x-text-input id="tanggal_transaksi" class="block mt-1 w-full" type="datetime-local" name="tanggal_transaksi" :value="old('tanggal_transaksi', now()->format('Y-m-d\TH:i'))" required />
                            <x-input-error :messages="$errors->get('tanggal_transaksi')" class="mt-2" />
                        </div>
                        <div class="mt-4">
                            <x-input-label for="keterangan" :value="__('Keterangan (Opsional)')" />
                            <textarea id="keterangan" name="keterangan" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">{{ old('keterangan') }}</textarea>
                            <x-input-error :messages="$errors->get('keterangan')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('bahan.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">Batal</a>
                            <x-primary-button>
                                {{ __('Simpan Transaksi') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>