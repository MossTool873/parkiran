@extends('components.app')

@section('title', 'Tambah Kendaraan')

@section('content')
    <h1 class="text-2xl font-bold mb-6">Tambah Kendaraan</h1>

    <div class="bg-white rounded-lg shadow p-6 w-full">
        <form action="{{ url('/admin/kendaraan') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium mb-1">Plat Nomor</label>
                <input type="text"
                       name="plat_nomor"
                       class="w-full border rounded-lg px-3 py-2"
                       value="{{ old('plat_nomor') }}">
                @error('plat_nomor')
                    <p class="text-red-600 text-sm">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Warna</label>
                <input type="text"
                       name="warna"
                       class="w-full border rounded-lg px-3 py-2"
                       value="{{ old('warna') }}">
                @error('warna')
                    <p class="text-red-600 text-sm">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Tipe Kendaraan</label>
                <select name="tipe_kendaraan_id"
                        class="w-full border rounded-lg px-3 py-2">
                    <option value="">-- Pilih Tipe Kendaraan --</option>
                    @foreach ($tipeKendaraans as $tipe)
                        <option value="{{ $tipe->id }}"
                            {{ old('tipe_kendaraan_id') == $tipe->id ? 'selected' : '' }}>
                            {{ $tipe->tipe_kendaraan }}
                        </option>
                    @endforeach
                </select>
                @error('tipe_kendaraan_id')
                    <p class="text-red-600 text-sm">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-2 pt-4">
                <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Simpan
                </button>
                <a href="{{ url('/admin/kendaraan') }}"
                   class="bg-gray-300 px-4 py-2 rounded hover:bg-gray-400">
                    Batal
                </a>
            </div>
        </form>
    </div>
@endsection