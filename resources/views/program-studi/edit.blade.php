{{-- resources/views/program-studi/edit.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Program Studi') }}
        </h2>
    </x-slot>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
        .form-wrap * { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>

    <div class="py-10 form-wrap">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-5">

            {{-- PAGE HEADER --}}
            <div class="flex items-center gap-3">
                <a href="{{ route('program-studi.index') }}" class="w-9 h-9 rounded-xl bg-white border border-gray-200 shadow-sm flex items-center justify-center text-gray-400 hover:text-gray-700 hover:border-gray-300 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </a>
                <div>
                    <h1 class="text-2xl font-extrabold text-gray-800">Edit Program Studi</h1>
                    <p class="text-sm text-gray-400 mt-0.5">Perbarui informasi unit/prodi: <span class="font-semibold text-gray-600">{{ $programStudi->nama_program_studi }}</span></p>
                </div>
            </div>

            {{-- FORM CARD --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <form action="{{ route('program-studi.update', $programStudi->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="p-6 md:p-8 space-y-6">
                        <div>
                            <x-input-label for="nama_program_studi" :value="__('Nama Program Studi/Unit')" class="text-gray-700 font-semibold" />
                            <x-text-input id="nama_program_studi" class="block mt-1.5 w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-all" type="text" name="nama_program_studi" :value="old('nama_program_studi', $programStudi->nama_program_studi)" required autofocus />
                            <x-input-error :messages="$errors->get('nama_program_studi')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="kode_program_studi" :value="__('Kode Program Studi')" class="text-gray-700 font-semibold" />
                            <x-text-input id="kode_program_studi" class="block mt-1.5 w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-all" type="text" name="kode_program_studi" :value="old('kode_program_studi', $programStudi->kode_program_studi)" />
                            <x-input-error :messages="$errors->get('kode_program_studi')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="nama_kps" :value="__('Nama Koordinator Program Studi (Beserta Gelar)')" class="text-gray-700 font-semibold" />
                            <x-text-input id="nama_kps" class="block mt-1.5 w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-all" type="text" name="nama_kps" :value="old('nama_kps', $programStudi->nama_kps)" placeholder="Contoh: Dr. Eng. Fulan, S.T., M.T." />
                            <x-input-error :messages="$errors->get('nama_kps')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="nip_kps" :value="__('NIP KPS')" class="text-gray-700 font-semibold" />
                            <x-text-input id="nip_kps" class="block mt-1.5 w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-all" type="text" name="nip_kps" :value="old('nip_kps', $programStudi->nip_kps)" placeholder="Contoh: 198001012005011001" />
                            <x-input-error :messages="$errors->get('nip_kps')" class="mt-2" />
                        </div>
                    </div>

                    {{-- FOOTER ACTIONS --}}
                    <div class="px-6 md:px-8 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-end gap-3">
                        <a href="{{ route('program-studi.index') }}"
                           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-gray-600 bg-white border border-gray-200 hover:bg-gray-100 transition-all">
                            Batal
                        </a>
                        <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold shadow-sm transition-all hover:-translate-y-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>