@extends('components.app')

@section('title', 'Area Parkir')

@section('content')

    {{-- HEADER: judul & tombol Tambah --}}
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-4">
        <h1 class="text-2xl font-bold">Area Parkir</h1>

        <a href="{{ url('/admin/areaParkir/create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-center">
            + Tambah Area Parkir
        </a>
    </div>

    {{-- SEARCH BAR --}}
    <div class="flex justify-end mb-6">
        <form method="GET" action="{{ url('/admin/areaParkir') }}">
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

    {{-- ALERT --}}
    @if (session('success'))
        <div class="mb-4 bg-green-100 border border-green-300 text-green-700 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    {{-- GRID CARD --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        @forelse ($areaParkirs as $area)
            <div class="bg-white rounded-xl shadow border">

                {{-- HEADER CARD --}}
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 px-6 py-4 border-b">

                    <div>
                        <h2 class="text-lg font-semibold">{{ $area->nama_area }}</h2>

                        <p class="text-sm text-gray-500">
                            Lokasi: <span class="font-medium">{{ $area->lokasi }}</span>
                        </p>

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
