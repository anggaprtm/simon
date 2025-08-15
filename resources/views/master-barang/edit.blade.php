<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Master Barang') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('master-barang.update', $masterBarang->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div>
                            <x-input-label for="nama_barang" :value="__('Nama Barang')" />
                            <x-text-input id="nama_barang" class="block mt-1 w-full" type="text" name="nama_barang" :value="old('nama_barang', $masterBarang->nama_barang)" required autofocus />
                            <x-input-error :messages="$errors->get('nama_barang')" class="mt-2" />
                        </div>
                        <div class="mt-4">
                            <x-input-label for="spesifikasi" :value="__('Spesifikasi Default (Opsional)')" />
                            <textarea id="spesifikasi" name="spesifikasi" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">{{ old('spesifikasi', $masterBarang->spesifikasi) }}</textarea>
                            <x-input-error :messages="$errors->get('spesifikasi')" class="mt-2" />
                        </div>
                        <div class="mt-4">
                            <x-input-label for="id_satuan" :value="__('Satuan Default (Opsional)')" />
                            <select id="id_satuan" name="id_satuan" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                <option value="">-- Tidak ada --</option>
                                @foreach ($satuans as $satuan)
                                    <option value="{{ $satuan->id }}" {{ old('id_satuan', $masterBarang->id_satuan) == $satuan->id ? 'selected' : '' }}>
                                        {{ $satuan->nama_satuan }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('id_satuan')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('master-barang.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4 underline">Batal</a>
                            <x-primary-button>
                                {{ __('Update') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>