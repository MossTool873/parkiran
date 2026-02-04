@extends('components.app')

@section('title', 'Edit Tarif')

@section('content')
<h1 class="text-2xl font-bold mb-6">Edit Tarif Tipe Kendaraan</h1>

<div class="bg-white rounded-lg shadow p-6 w-full max-w-xl">
    <form action="{{ url('/admin/tarifTipeKendaraan/'.$tarif->id) }}"
          method="POST"
          class="space-y-4"
          onsubmit="return confirm('Yakin data yang dimasukkan sudah benar?')">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm font-medium mb-1">Tipe Kendaraan</label>

            <input type="hidden"
                   name="tipe_kendaraan_id"
                   value="{{ $tarif->tipe_kendaraan_id }}">

            <input type="text"
                   class="w-full border rounded-lg px-3 py-2 bg-gray-100"
                   value="{{ $tarif->tipeKendaraan->tipe_kendaraan }}"
                   disabled>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Tarif per Jam</label>
            <input type="number"
                   name="tarif_perjam"
                   class="w-full border rounded-lg px-3 py-2"
                   value="{{ old('tarif_perjam', $tarif->tarif_perjam) }}"
                   min="0">
            @error('tarif_perjam')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex gap-2 pt-4">
            <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Update
            </button>

            <a href="{{ url('/admin/tarifTipeKendaraan') }}"
               class="bg-gray-300 px-4 py-2 rounded hover:bg-gray-400">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection