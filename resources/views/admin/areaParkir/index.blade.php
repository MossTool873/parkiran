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

    {{-- GRID CARD --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        @forelse ($areaParkirs as $area)
            <div class="bg-white rounded-lg shadow border">

                {{-- HEADER CARD --}}
                <div class="flex justify-between items-center px-6 py-4 border-b">
                    <div>
                        <h2 class="text-lg font-semibold">{{ $area->nama_area }}</h2>
                        <p class="text-sm text-gray-600">
                            Total Kapasitas:
                            <span class="font-medium">{{ $area->total_kapasitas }}</span>
                        </p>
                    </div>

                    <div class="flex gap-2">
                        <a href="{{ url('/admin/areaParkir/' . $area->id . '/edit') }}"
                           class="px-4 py-1.5 text-sm border border-blue-500 text-blue-600 rounded hover:bg-blue-50">
                            Edit
                        </a>

                        <form action="{{ url('/admin/areaParkir/' . $area->id) }}"
                              method="POST"
                              onsubmit="return confirm('Hapus area parkir ini?')">
                            @csrf
                            @method('DELETE')
                            <button
                                class="px-4 py-1.5 text-sm border border-red-500 text-red-600 rounded hover:bg-red-50">
                                Hapus
                            </button>
                        </form>
                    </div>
                </div>

                {{-- BODY CARD --}}
                <div class="px-6 py-4">
                    {{-- HEADER TABEL --}}
                    <div class="grid grid-cols-4 font-semibold text-sm text-gray-600 border-b pb-2">
                        <div>Tipe</div>
                        <div>Kapasitas</div>
                        <div>Terisi</div>
                        <div class="text-center">%</div>
                    </div>

                    <div class="divide-y">
@foreach ($area->detailKapasitas as $detail)
    @php
        $tipe = $detail->tipeKendaraan;
    @endphp

    @if ($detail->kapasitas > 0 && $tipe)
        @php
            $terisi = $detail->terisi ?? 0;
            $kapasitas = $detail->kapasitas;
            $persen = $kapasitas > 0
                ? round(($terisi / $kapasitas) * 100)
                : 0;
        @endphp

        <div class="grid grid-cols-4 py-2 text-sm items-center">
            <div>{{ $tipe->tipe_kendaraan }}</div>
            <div>{{ $kapasitas }}</div>
            <div>{{ $terisi }}</div>

            {{-- PERSENTASE --}}
            <div class="text-center">
                <span class="inline-block px-2 py-1 rounded text-xs font-semibold
                    {{ $persen >= 90 ? 'bg-red-100 text-red-700' :
                       ($persen >= 70 ? 'bg-yellow-100 text-yellow-700' :
                       'bg-green-100 text-green-700') }}">
                    {{ $persen }}%
                </span>
            </div>
        </div>
    @endif
@endforeach

                    </div>
                </div>

            </div>
        @empty
            <div class="col-span-2 bg-white rounded-lg shadow p-6 text-center text-gray-500">
                Data area parkir kosong
            </div>
        @endforelse

    </div>

    {{-- PAGINATION (MAX 5 ANGKA) --}}
    <div class="mt-8 flex justify-center">
        {{ $areaParkirs->onEachSide(2)->links() }}
    </div>

@endsection
