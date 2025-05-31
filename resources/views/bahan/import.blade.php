{{-- resources/views/bahan/import.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Import Data Bahan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-4 p-4 bg-blue-100 border-l-4 border-blue-500 rounded-r-lg">
                        <h3 class="font-bold">Petunjuk Import</h3>
                        <ol class="list-decimal list-inside mt-2 text-sm">
                            <li>Unduh template Excel yang sudah disediakan.</li>
                            <li>Isi data bahan sesuai dengan kolom pada template.</li>
                            <li>Kolom `kode_bahan`, `nama_bahan`, `nama_gudang`, dan `satuan` wajib diisi.</li>
                            <li>Pastikan `nama_gudang` yang Anda masukkan sudah terdaftar di sistem.</li>
                            <li>Simpan file Anda dalam format .xlsx atau .xls.</li>
                            <li>Unggah file yang sudah diisi melalui form di bawah ini.</li>
                        </ol>
                        <div class="mt-4">
                            <a href="{{ asset('templates/template_import_bahan.xlsx') }}" download class="text-sm font-semibold text-white bg-green-600 hover:bg-green-700 px-4 py-2 rounded">
                                Unduh Template
                            </a>
                        </div>
                    </div>

                    {{-- Menampilkan error validasi dari Excel --}}
                    @if(session('import_errors'))
                        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                            <p class="font-bold">Terdapat beberapa error pada file yang Anda unggah:</p>
                            <ul class="list-disc list-inside mt-2">
                                @foreach(session('import_errors') as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('bahan.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div>
                            <x-input-label for="file" :value="__('Pilih File Excel')" />
                            <input type="file" name="file" id="file" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none mt-1" required>
                            <x-input-error :messages="$errors->get('file')" class="mt-2" />
                        </div>
                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('bahan.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">Batal</a>
                            <x-primary-button>
                                {{ __('Mulai Import') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>