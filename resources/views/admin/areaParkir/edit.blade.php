@extends('components.app')

@section('title', 'Edit Area Parkir')

@section('content')

<h1 class="text-2xl font-bold mb-6">Edit Area Parkir</h1>

<div class="bg-white rounded-lg shadow p-6 w-full">
    @if ($errors->any())
    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
        <ul class="list-disc list-inside">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif


    <form action="{{ url('/admin/areaParkir/'.$areaParkir->id) }}"
          method="POST"
          class="space-y-6"
          onsubmit="return confirm('Yakin data yang dimasukkan sudah benar?')">

        @csrf
        @method('PUT')

        {{-- NAMA AREA --}}
        <div>
            <label class="block text-sm font-medium mb-1">Nama Area</label>
            <input type="text"
                   name="nama_area"
                   class="w-full border rounded-lg px-3 py-2"
                   value="{{ old('nama_area', $areaParkir->nama_area) }}"
                   required>
        </div>

        {{-- LOKASI --}}
        <div>
            <label class="block text-sm font-medium mb-1">Lokasi</label>
            <input type="text"
                   name="lokasi"
                   class="w-full border rounded-lg px-3 py-2"
                   value="{{ old('lokasi', $areaParkir->lokasi) }}"
                   required>
        </div>

        {{-- KAPASITAS --}}
        <div>
            <h2 class="font-semibold mb-3">
                Kapasitas per Tipe Kendaraan
            </h2>

            <div class="space-y-3">
                @foreach ($tipeKendaraans as $tipe)
                    @php
                        $detail = $areaParkir->detailKapasitas
                            ->firstWhere('tipe_kendaraan_id', $tipe->id);
                    @endphp

                    <div class="flex items-center gap-4">
                        
                        <label class="w-48 text-sm">
                            {{ $tipe->tipe_kendaraan }}
                        </label>

                        <input type="number"
                               name="kapasitas[{{ $tipe->id }}]"
                               class="border rounded px-3 py-2 w-32"
                               value="{{ old('kapasitas.'.$tipe->id, $detail->kapasitas ?? 0) }}"
                               min="0">
                    </div>
                @endforeach
            </div>
        </div>

        {{-- BUTTON --}}
        <div class="flex gap-2 pt-4">
            <button type="submit"
                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Update
            </button>

            <a href="{{ url('/admin/areaParkir') }}"
               class="bg-gray-300 px-4 py-2 rounded hover:bg-gray-400">
                Batal
            </a>
        </div>

    </form>
</div>

@endsection
