@extends('components.app')

@section('title', 'Tambah Tarif')

@section('content')
    <h1 class="text-2xl font-bold mb-6">Tambah Tarif Tipe Kendaraan</h1>

    <div class="bg-white rounded-lg shadow p-6 w-full max-w-xl">
        <form action="{{ url('/admin/tarifTipeKendaraan') }}" method="POST" class="space-y-4" onsubmit="return confirm('Yakin data yang dimasukkan sudah benar?')">
            @csrf

            <div>
                <label class="block text-sm font-medium mb-1">Tipe Kendaraan</label>
                <select name="tipe_kendaraan_id"
                        class="w-full border rounded-lg px-3 py-2">
                    <option value="">-- Pilih Tipe Kendaraan --</option>
                    @foreach ($tipeKendaraans as $tipe)
                        <option value="{{ $tipe->id }}"
                            {{ in_array($tipe->id, $tipeTerpakai) ? 'disabled' : '' }}>
                            {{ $tipe->tipe_kendaraan }}
                            {{ in_array($tipe->id, $tipeTerpakai) ? '(Sudah ada tarif)' : '' }}
                        </option>
                    @endforeach
                </select>
                @error('tipe_kendaraan_id')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Tarif per Jam</label>
                <input type="number"
                       name="tarif_perjam"
                       class="w-full border rounded-lg px-3 py-2"
                       value="{{ old('tarif_perjam') }}"
                       min="0">
                @error('tarif_perjam')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-2 pt-4">
                <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Simpan
                </button>
                <a href="{{ url('/admin/tarifTipeKendaraan') }}"
                   class="bg-gray-300 px-4 py-2 rounded hover:bg-gray-400">
                    Batal
                </a>
            </div>
        </form>
    </div>
@endsection