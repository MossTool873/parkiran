@extends('components.app')

@section('title', 'Tipe Kendaraan')

@section('content')
    {{-- HEADER --}}
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Data Tipe Kendaraan</h1>

        <a href="{{ url('/admin/tipeKendaraan/create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
            + Tambah Tipe Kendaraan
        </a>
    </div>

    {{-- ALERT --}}
    @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-300 text-green-700 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    {{-- TABLE --}}
    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-100 text-left">
                <tr>
                    <th class="px-4 py-3 w-16">No</th>
                    <th class="px-4 py-3">Tipe Kendaraan</th>
                    <th class="px-4 py-3 w-40">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($tipeKendaraans as $tipe)
                    <tr class="border-t">
                        <td class="px-4 py-3">{{ $loop->iteration }}</td>
                        <td class="px-4 py-3">{{ $tipe->tipe_kendaraan }}</td>
                        <td class="px-4 py-3 flex gap-2">
                            <a href="{{ url('/admin/tipeKendaraan/'.$tipe->id.'/edit') }}"
                               class="text-blue-600 hover:underline">
                                Edit
                            </a>

                            <form action="{{ url('/admin/tipeKendaraan/'.$tipe->id) }}"
                                  method="POST"
                                  onsubmit="return confirm('Yakin hapus data ini?')">
                                @csrf
                                @method('DELETE')
                                <button class="text-red-600 hover:underline">
                                    Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-4 py-6 text-center text-gray-500">
                            Data tipe kendaraan kosong
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection