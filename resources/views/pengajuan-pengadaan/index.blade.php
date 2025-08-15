<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Daftar Pengajuan Pengadaan Bahan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @can('create-pengajuan') {{-- Kita akan buat Gate ini nanti --}}
                        <div class="flex justify-end mb-4">
                            <a href="{{ route('pengajuan-pengadaan.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                + Buat Pengajuan Baru
                            </a>
                        </div>
                    @endcan
                    
                    @if (session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Periode</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Prodi</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($pengajuans as $pengajuan)
                                    <tr>
                                        <td class="px-6 py-4">{{ $pengajuan->created_at->isoFormat('D MMM Y') }}</td>
                                        <td class="px-6 py-4">{{ $pengajuan->tahun_ajaran }} - {{ $pengajuan->semester }}</td>
                                        <td class="px-6 py-4">{{ $pengajuan->programStudi->kode_program_studi }}</td>
                                        <td class="px-6 py-4">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                @if($pengajuan->status == 'Draft') bg-gray-100 text-gray-800 @endif
                                                @if($pengajuan->status == 'Diajukan') bg-blue-100 text-blue-800 @endif
                                                @if($pengajuan->status == 'Disetujui') bg-green-100 text-green-800 @endif
                                                @if($pengajuan->status == 'Ditolak') bg-red-100 text-red-800 @endif
                                            ">
                                                {{ $pengajuan->status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('pengajuan-pengadaan.show', $pengajuan->id) }}" class="text-blue-600 hover:text-blue-900">Lihat</a>
                                            @if($pengajuan->status == 'Draft')
                                                <a href="{{ route('pengajuan-pengadaan.edit', $pengajuan->id) }}" class="text-indigo-600 hover:text-indigo-900 ml-2">Edit</a>
                                                <form action="{{ route('pengajuan-pengadaan.destroy', $pengajuan->id) }}" method="POST" class="inline ml-2" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengajuan ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                            Belum ada data pengajuan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>