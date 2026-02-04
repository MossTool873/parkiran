@extends('components.app')

@section('title', 'Edit Tipe Kendaraan')

@section('content')
<h1 class="text-2xl font-bold mb-6">Edit Tipe Kendaraan</h1>

<div class="bg-white rounded-lg shadow p-6 w-full">
    <form
        action="{{ url('/admin/tipeKendaraan/'.$tipeKendaraan->id) }}"
        method="POST"
        class="space-y-6"
        onsubmit="return confirm('Yakin data yang dimasukkan sudah benar?')"
    >
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm font-medium mb-1">
                Tipe Kendaraan
            </label>
            <input
                type="text"
                name="tipe_kendaraan"
                class="w-full border rounded-lg px-4 py-2 focus:ring focus:ring-blue-200"
                value="{{ old('tipe_kendaraan', $tipeKendaraan->tipe_kendaraan) }}"
            >
            @error('tipe_kendaraan')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex gap-2 pt-4">
            <button
                type="submit"
                class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700"
            >
                Update
            </button>

            <a
                href="{{ url('/admin/tipeKendaraan') }}"
                class="bg-gray-300 px-6 py-2 rounded hover:bg-gray-400"
            >
                Batal
            </a>
        </div>
    </form>
</div>
@endsection