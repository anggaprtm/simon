{{-- resources/views/user/edit.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit User') }}
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
                <a href="{{ route('user.index') }}" class="w-9 h-9 rounded-xl bg-white border border-gray-200 shadow-sm flex items-center justify-center text-gray-400 hover:text-gray-700 hover:border-gray-300 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </a>
                <div>
                    <h1 class="text-2xl font-extrabold text-gray-800">Edit User</h1>
                    <p class="text-sm text-gray-400 mt-0.5">Perbarui informasi akun: <span class="font-semibold text-gray-600">{{ $user->name }}</span></p>
                </div>
            </div>

            {{-- FORM CARD --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <form action="{{ route('user.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="p-6 md:p-8 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            {{-- Nama Lengkap --}}
                            <div>
                                <x-input-label for="nama_lengkap" :value="__('Nama Lengkap')" class="text-gray-700 font-semibold" />
                                <x-text-input id="nama_lengkap" class="block mt-1.5 w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-all" type="text" name="nama_lengkap" :value="old('nama_lengkap', $user->nama_lengkap)" required autofocus />
                                <x-input-error :messages="$errors->get('nama_lengkap')" class="mt-2" />
                            </div>

                            {{-- Username --}}
                            <div>
                                <x-input-label for="name" :value="__('Username')" class="text-gray-700 font-semibold" />
                                <x-text-input id="name" class="block mt-1.5 w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-all" type="text" name="name" :value="old('name', $user->name)" required />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            {{-- Email --}}
                            <div>
                                <x-input-label for="email" :value="__('Email')" class="text-gray-700 font-semibold" />
                                <x-text-input id="email" class="block mt-1.5 w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-all" type="email" name="email" :value="old('email', $user->email)" required />
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>

                            {{-- NIK / NIP --}}
                            <div>
                                <x-input-label for="nik" :value="__('NIK / NIP (Opsional)')" class="text-gray-700 font-semibold" />
                                <x-text-input id="nik" class="block mt-1.5 w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-all" type="text" name="nik" :value="old('nik', $user->nik)" />
                                <x-input-error :messages="$errors->get('nik')" class="mt-2" />
                            </div>

                            {{-- Role --}}
                            <div>
                                <x-input-label for="role" :value="__('Role')" class="text-gray-700 font-semibold" />
                                <select id="role" name="role" class="block mt-1.5 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm transition-all text-sm" required onchange="toggleProdi()">
                                    <option value="laboran" {{ old('role', $user->role) == 'laboran' ? 'selected' : '' }}>Laboran</option>
                                    <option value="kps" {{ old('role', $user->role) == 'kps' ? 'selected' : '' }}>KPS (Koordinator Prodi)</option>
                                    <option value="fakultas" {{ old('role', $user->role) == 'fakultas' ? 'selected' : '' }}>Fakultas / Pimpinan</option>
                                    <option value="superadmin" {{ old('role', $user->role) == 'superadmin' ? 'selected' : '' }}>Superadmin</option>
                                </select>
                                <x-input-error :messages="$errors->get('role')" class="mt-2" />
                            </div>

                            {{-- Unit / Program Studi --}}
                            <div id="prodi-wrapper">
                                <x-input-label for="id_program_studi" :value="__('Unit / Program Studi')" class="text-gray-700 font-semibold" />
                                <select id="id_program_studi" name="id_program_studi" class="block mt-1.5 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm transition-all text-sm">
                                    <option value="">-- Kosongkan jika bukan Laboran/KPS --</option>
                                    @foreach($programStudis as $prodi)
                                        <option value="{{ $prodi->id }}" {{ old('id_program_studi', $user->id_program_studi) == $prodi->id ? 'selected' : '' }}>
                                            {{ $prodi->nama_program_studi }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('id_program_studi')" class="mt-2" />
                            </div>

                            {{-- Password --}}
                            <div class="md:col-span-2">
                                <x-input-label for="password" :value="__('Password Baru (Kosongkan jika tidak ingin mengubah)')" class="text-gray-700 font-semibold" />
                                <x-text-input id="password" class="block mt-1.5 w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-all" type="password" name="password" />
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>

                        </div>
                    </div>

                    {{-- FOOTER ACTIONS --}}
                    <div class="px-6 md:px-8 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-end gap-3">
                        <a href="{{ route('user.index') }}"
                           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-gray-600 bg-white border border-gray-200 hover:bg-gray-100 transition-all">
                            Batal
                        </a>
                        <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold shadow-sm transition-all hover:-translate-y-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Update User
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>

    @push('scripts')
    <script>
        function toggleProdi() {
            const role = document.getElementById('role').value;
            const prodiWrapper = document.getElementById('prodi-wrapper');
            
            if (role === 'laboran' || role === 'kps') {
                prodiWrapper.style.display = 'block';
            } else {
                prodiWrapper.style.display = 'none';
                document.getElementById('id_program_studi').value = '';
            }
        }
        document.addEventListener('DOMContentLoaded', toggleProdi);
    </script>
    @endpush
</x-app-layout>