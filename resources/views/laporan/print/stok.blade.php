@extends('layouts.print')

@section('content')
    <table class="w-full text-sm text-left rtl:text-right text-gray-500">
        <thead class="text-xs text-gray-700 uppercase bg-gray-100">
            <tr>
                <th scope="col" class="px-6 py-3">Kode</th>
                <th scope="col" class="px-6 py-3">Nama Bahan</th>
                {{-- ... header kolom lainnya ... --}}
            </tr>
        </thead>
        <tbody>
            @foreach ($bahans as $bahan)
            <tr class="bg-white border-b">
                <td class="px-6 py-4">{{ $bahan->kode_bahan }}</td>
                <td class="px-6 py-4">{{ $bahan->nama_bahan }}</td>
                {{-- ... data kolom lainnya ... --}}
            </tr>
            @endforeach
        </tbody>
    </table>
@endsection