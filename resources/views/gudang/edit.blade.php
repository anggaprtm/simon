{{-- resources/views/gudang/edit.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Gudang') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('gudang.update', $gudang->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div>
                            <x-input-label for="nama_gudang" :value="__('Nama Gudang')" />
                            <x-text-input id="nama_gudang" class="block mt-1 w-full" type="text" name="nama_gudang" :value="old('nama_gudang', $gudang->nama_gudang)" required autofocus />
                            <x-input-error :messages="$errors->get('nama_gudang')" class="mt-2" />
                        </div>
                        <div class="mt-4">
                            <x-input-label for="lokasi" :value="__('Lokasi Gudang')" />
                            <x-text-input id="lokasi" class="block mt-1 w-full" type="text" name="lokasi" :value="old('lokasi', $gudang->lokasi)" required />
                            <x-input-error :messages="$errors->get('lokasi')" class="mt-2" />
                        </div>
                        
                        @if (Auth::user()->role == 'superadmin')
                            <div class="mt-4">
                                <x-input-label for="id_program_studi" :value="__('Milik Program Studi')" />
                                <select id="id_program_studi" name="id_program_studi" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">-- Umum / Fakultas --</option>
                                    @foreach ($programStudis as $prodi)
                                        <option value="{{ $prodi->id }}" {{ old('id_program_studi', $gudang->id_program_studi) == $prodi->id ? 'selected' : '' }}>
                                            {{ $prodi->nama_program_studi }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('id_program_studi')" class="mt-2" />
                            </div>
                        @endif

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('gudang.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">Batal</a>
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