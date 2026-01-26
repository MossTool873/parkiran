@extends('components.app')

@section('title', 'Area Parkir')

@section('content')
    {{-- HEADER --}}
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Area Parkir</h1>

        <a href="{{ url('/admin/areaParkir/create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
            + Tambah Area Parkir
        </a>
    </div>

    {{-- ALERT --}}
    @if (session('success'))
        <div class="mb-4 bg-green-100 border border-green-300 text-green-700 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    {{-- TABLE --}}
    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-100 text-left">
                <tr>
                    <th class="px-4 py-3">No</th>
                    <th class="px-4 py-3">Nama Area</th>
                    <th class="px-4 py-3">Total Kapasitas</th>
                    <th class="px-4 py-3">Detail Kapasitas</th>
                    <th class="px-4 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($areaParkirs as $area)
                    <tr class="border-t">
                        <td class="px-4 py-3">{{ $loop->iteration }}</td>
                        <td class="px-4 py-3">{{ $area->nama_area }}</td>
                        <td class="px-4 py-3">{{ $area->total_kapasitas }}</td>
                        <td class="px-4 py-3">
                            <ul class="list-none ml-4">
                                @foreach ($area->detailKapasitas as $detail)
                                    <li>
                                        {{ $detail->tipeKendaraan->tipe_kendaraan }} :
                                        {{ $detail->kapasitas }}
                                    </li>
                                @endforeach
                            </ul>
                        </td>
                        <td class="px-4 py-3 flex gap-2">
                            <a href="{{ url('/admin/areaParkir/'.$area->id.'/edit') }}"
                               class="text-blue-600 hover:underline">
                                Edit
                            </a>

                            <form action="{{ url('/admin/areaParkir/'.$area->id) }}"
                                  method="POST"
                                  onsubmit="return confirm('Hapus area parkir ini?')">
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
                        <td colspan="5" class="px-4 py-6 text-center text-gray-500">
                            Data area parkir kosong
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection