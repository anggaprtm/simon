<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profil Pengguna') }}
        </h2>
    </x-slot>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
        .profile-wrap * { font-family: 'Plus Jakarta Sans', sans-serif; }
        
        /* Opsional: Sedikit penyesuaian untuk input bawaan Laravel di dalam partials agar match */
        .profile-wrap input[type="text"],
        .profile-wrap input[type="email"],
        .profile-wrap input[type="password"] {
            border-radius: 0.75rem; /* rounded-xl */
            border-color: #d1d5db;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            transition: all 0.15s ease;
        }
        .profile-wrap input:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }
        .profile-wrap primary-button, .profile-wrap button[type="submit"] {
            border-radius: 0.5rem;
        }
    </style>

    <div class="py-10 profile-wrap">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- PAGE HEADER --}}
            <div class="mb-4">
                <h1 class="text-2xl font-extrabold text-gray-800">Pengaturan Profil</h1>
                <p class="text-sm text-gray-400 mt-0.5">Kelola informasi data diri, kata sandi, dan keamanan akun Anda</p>
            </div>

            {{-- UPDATE PROFILE INFO CARD --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="p-6 md:p-8">
                    <div class="max-w-2xl">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </div>
            </div>

            {{-- UPDATE PASSWORD CARD --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="p-6 md:p-8">
                    <div class="max-w-2xl">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>
            </div>

            {{-- DELETE USER CARD --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="p-6 md:p-8">
                    <div class="max-w-2xl">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>