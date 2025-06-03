<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Satuan Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('satuan.store') }}" method="POST">
                        @csrf
                        {{-- Nama Satuan --}}
                        <div>
                            <x-input-label for="nama_satuan" :value="__('Nama Satuan (Simbol, cth: ml, Kg, pcs)')" />
                            <x-text-input id="nama_satuan" class="block mt-1 w-full" type="text" name="nama_satuan" :value="old('nama_satuan')" required autofocus />
                            <x-input-error :messages="$errors->get('nama_satuan')" class="mt-2" />
                        </div>

                        {{-- Keterangan Satuan --}}
                        <div class="mt-4">
                            <x-input-label for="keterangan_satuan" :value="__('Keterangan (Opsional, cth: Mililiter, Kilogram)')" />
                            <x-text-input id="keterangan_satuan" class="block mt-1 w-full" type="text" name="keterangan_satuan" :value="old('keterangan_satuan')" />
                            <x-input-error :messages="$errors->get('keterangan_satuan')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('satuan.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4 underline">
                                {{ __('Batal') }}
                            </a>
                            <x-primary-button>
                                {{ __('Simpan Satuan') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>