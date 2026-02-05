@extends('components.app')

@section('title', 'Occupancy Area Parkir')

@section('content')
    {{-- HEADER --}}
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Occupancy Area Parkir</h1>

        {{-- SEARCH BAR --}}
        <form method="GET" action="{{ url('laporan/occupancy') }}" class="flex items-center space-x-2 w-64 md:w-80">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Cari area..."
                   class="w-full px-3 py-2 border rounded-lg text-sm focus:outline-none focus:ring focus:border-blue-300"
                   autocomplete="off">

            <button type="submit"
                    class="bg-blue-600 text-white px-3 py-2 rounded-lg text-sm hover:bg-blue-700">
                Cari
            </button>
        </form>
    </div>

    {{-- GRID CARD --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @forelse ($areaParkirs as $area)
            @php
                $totalTerisi = $area->detailKapasitas->sum('terisi');
                $totalKapasitas = $area->total_kapasitas ?? 0;
                $progress = $totalKapasitas > 0 ? round(($totalTerisi / $totalKapasitas) * 100) : 0;
            @endphp

            <div class="bg-white rounded-lg shadow border">
                {{-- HEADER CARD --}}
                <div class="px-6 py-4 border-b">
                    <div class="flex justify-between items-center mb-2">
                        <div>
                            <h2 class="text-lg font-semibold">{{ $area->nama_area }}</h2>
                            <p class="text-sm text-gray-600">
                                Total Terisi: <span class="font-medium">{{ $totalTerisi }}</span> /
                                <span class="font-medium">{{ $totalKapasitas }}</span>
                            </p>
                        </div>
                    </div>

                    {{-- PROGRESS BAR --}}
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-500 h-2 rounded-full" style="width: {{ $progress }}%"></div>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">{{ $progress }}% Terisi</p>
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

    {{-- PAGINATION --}}
    <div class="mt-8 text-center">
        {{ $areaParkirs->onEachSide(2)->links() }}
    </div>

@endsection
