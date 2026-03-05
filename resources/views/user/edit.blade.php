<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit User: {{ $user->name }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('user.update', $user->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="nama_lengkap" :value="__('Nama Lengkap')" />
                                <x-text-input id="nama_lengkap" class="block mt-1 w-full" type="text" name="nama_lengkap" :value="old('nama_lengkap', $user->nama_lengkap)" required autofocus />
                                <x-input-error :messages="$errors->get('nama_lengkap')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="name" :value="__('Username')" />
                                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $user->name)" required />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="email" :value="__('Email')" />
                                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $user->email)" required />
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="nik" :value="__('NIK / NIP (Opsional)')" />
                                <x-text-input id="nik" class="block mt-1 w-full" type="text" name="nik" :value="old('nik', $user->nik)" />
                                <x-input-error :messages="$errors->get('nik')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="role" :value="__('Role')" />
                                <select id="role" name="role" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" required onchange="toggleProdi()">
                                    <option value="laboran" {{ old('role', $user->role) == 'laboran' ? 'selected' : '' }}>Laboran</option>
                                    <option value="kps" {{ old('role', $user->role) == 'kps' ? 'selected' : '' }}>KPS (Koordinator Prodi)</option>
                                    <option value="fakultas" {{ old('role', $user->role) == 'fakultas' ? 'selected' : '' }}>Fakultas / Pimpinan</option>
                                    <option value="superadmin" {{ old('role', $user->role) == 'superadmin' ? 'selected' : '' }}>Superadmin</option>
                                </select>
                                <x-input-error :messages="$errors->get('role')" class="mt-2" />
                            </div>
                            <div id="prodi-wrapper">
                                <x-input-label for="id_program_studi" :value="__('Unit / Program Studi')" />
                                <select id="id_program_studi" name="id_program_studi" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                    <option value="">-- Kosongkan jika bukan Laboran --</option>
                                    @foreach($programStudis as $prodi)
                                        <option value="{{ $prodi->id }}" {{ old('id_program_studi', $user->id_program_studi) == $prodi->id ? 'selected' : '' }}>
                                            {{ $prodi->nama_program_studi }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('id_program_studi')" class="mt-2" />
                            </div>
                            <div class="md:col-span-2">
                                <x-input-label for="password" :value="__('Password Baru (Kosongkan jika tidak ingin mengubah)')" />
                                <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" />
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('user.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">Batal</a>
                            <x-primary-button>Update User</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleProdi() {
            const role = document.getElementById('role').value;
            const prodiWrapper = document.getElementById('prodi-wrapper');
            
            // Tampilkan pilihan prodi jika role adalah laboran atau kps
            if (role === 'laboran' || role === 'kps') {
                prodiWrapper.style.display = 'block';
            } else {
                prodiWrapper.style.display = 'none';
                document.getElementById('id_program_studi').value = '';
            }
        }
        document.addEventListener('DOMContentLoaded', toggleProdi);
    </script>
</x-app-layout>