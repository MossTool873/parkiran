@extends('components.app')

@section('title', 'Edit Kendaraan')

@section('content')
    <h1 class="text-2xl font-bold mb-6">Edit Kendaraan</h1>

    <div class="bg-white rounded-lg shadow p-6 w-full">
        <form action="{{ url('/admin/kendaraan/'.$kendaraan->id) }}"
              method="POST"
              class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium mb-1">Plat Nomor</label>
                <input type="text"
                       name="plat_nomor"
                       class="w-full border rounded-lg px-3 py-2"
                       value="{{ old('plat_nomor', $kendaraan->plat_nomor) }}">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Warna</label>
                <input type="text"
                       name="warna"
                       class="w-full border rounded-lg px-3 py-2"
                       value="{{ old('warna', $kendaraan->warna) }}">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Tipe Kendaraan</label>
                <select name="tipe_kendaraan_id"
                        class="w-full border rounded-lg px-3 py-2">
                    @foreach ($tipeKendaraans as $tipe)
                        <option value="{{ $tipe->id }}"
                            {{ $kendaraan->tipe_kendaraan_id == $tipe->id ? 'selected' : '' }}>
                            {{ $tipe->tipe_kendaraan }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-2 pt-4">
                <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Update
                </button>
                <a href="{{ url('/admin/kendaraan') }}"
                   class="bg-gray-300 px-4 py-2 rounded hover:bg-gray-400">
                    Batal
                </a>
            </div>
        </form>
    </div>
@endsection