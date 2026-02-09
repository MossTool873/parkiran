@extends('components.app')

@section('title', 'Area Parkir')

@section('content')

    {{-- HEADER --}}
    <h1 class="text-2xl font-bold mb-4">Area Parkir</h1>

    {{-- SEARCH BAR --}}
    <div class="flex justify-end mb-6">
        <form method="GET" action="{{ url('/show-data/area-parkir') }}">
            <div class="flex gap-2 w-full md:w-80">
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari lokasi atau nama area..."
                    class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring focus:ring-blue-200">

                <button
                    type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">
                    Cari
                </button>
            </div>
        </form>
    </div>

    {{-- GRID CARD --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        @forelse ($areaParkirs as $area)
            <div class="bg-white rounded-xl shadow border">

                {{-- HEADER CARD --}}
                <div class="px-6 py-4 border-b">
                    <h2 class="text-lg font-semibold">{{ $area->nama_area }}</h2>
                    <p class="text-sm text-gray-500">
                        Lokasi: <span class="font-medium">{{ $area->lokasi }}</span>
                    </p>
                    <p class="text-sm text-gray-600">
                        Total Kapasitas:
                        <span class="font-medium">{{ $area->total_kapasitas }}</span>
                    </p>
                </div>

                {{-- BODY CARD --}}
                <div class="px-6 py-4">

                    {{-- HEADER TABLE --}}
                    <div class="grid grid-cols-2 font-semibold text-sm text-gray-600 border-b pb-2">
                        <div>Tipe</div>
                        <div class="text-center">Kapasitas</div>
                    </div>

                    <div class="divide-y">

                        @foreach ($area->detailKapasitas as $detail)
                            @php
                                $tipe = $detail->tipeKendaraan;
                            @endphp

                            @if ($detail->kapasitas > 0 && $tipe)
                                <div class="grid grid-cols-2 py-2 text-sm items-center">
                                    <div>{{ $tipe->tipe_kendaraan }}</div>
                                    <div class="text-center font-medium">{{ $detail->kapasitas }}</div>
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

    {{-- PAGINATION --}}
    <div class="mt-8 text-center">
        {{ $areaParkirs->onEachSide(2)->links() }}
    </div>

@endsection
