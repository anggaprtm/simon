{{-- resources/views/program-studi/create.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Program Studi Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('program-studi.store') }}" method="POST">
                        @csrf
                        <div>
                            <x-input-label for="nama_program_studi" :value="__('Nama Program Studi')" />
                            <x-text-input id="nama_program_studi" class="block mt-1 w-full" type="text" name="nama_program_studi" :value="old('nama_program_studi')" required autofocus />
                            <x-input-error :messages="$errors->get('nama_program_studi')" class="mt-2" />
                        </div>
                        <div class="mt-4">
                            <x-input-label for="kode_program_studi" :value="__('Kode Program Studi')" />
                            <x-text-input id="kode_program_studi" class="block mt-1 w-full" type="text" name="kode_program_studi" :value="old('kode_program_studi')" />
                            <x-input-error :messages="$errors->get('kode_program_studi')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('program-studi.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">
                                {{ __('Batal') }}
                            </a>
                            <x-primary-button>
                                {{ __('Simpan') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>