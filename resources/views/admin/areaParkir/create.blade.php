@extends('components.app')

@section('title', 'Tambah Area Parkir')

@section('content')
<h1 class="text-2xl font-bold mb-6">Tambah Area Parkir</h1>

<div class="bg-white rounded-lg shadow p-6 w-full">
    <form action="{{ url('/admin/areaParkir') }}" method="POST" class="space-y-4">
        @csrf

        <div>
            <label class="block text-sm font-medium mb-1">Nama Area</label>
            <input type="text" name="nama_area"
                   class="w-full border rounded-lg px-3 py-2"
                   value="{{ old('nama_area') }}">
        </div>

        <div>
            <h2 class="font-semibold mb-2">Kapasitas per Tipe Kendaraan</h2>

@foreach ($tipeKendaraans as $tipe)
    <div class="flex items-center gap-4 mb-2">
        <span class="w-48">{{ $tipe->tipe_kendaraan }}</span>

        <input type="number"
               name="kapasitas[{{ $tipe->id }}]"
               class="border rounded px-3 py-1 w-32"
               value="{{ old('kapasitas.'.$tipe->id, 0) }}"
               min="0">
    </div>
@endforeach
        </div>

        <div class="flex gap-2 pt-4">
            <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Simpan
            </button>
            <a href="{{ url('/admin/areaParkir') }}"
               class="bg-gray-300 px-4 py-2 rounded hover:bg-gray-400">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection