@extends('components.app')

@section('title', 'Tracking Kendaraan')

@section('content')

{{-- HEADER --}}
<div class="flex flex-col md:flex-row md:justify-between md:items-center gap-3 mb-4">
    <h1 class="text-2xl font-bold">
        Tracking Kendaraan Parkir
    </h1>

    {{-- SEARCH --}}
    <form method="GET">
        <div class="flex gap-2 w-full md:w-80">
            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Cari area parkir..."
                class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring focus:ring-blue-200">

            <button
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">
                Cari
            </button>
        </div>
    </form>
</div>

{{-- GRID CARD (SATU-SATU) --}}
<div class="grid grid-cols-1 gap-6">

    @forelse ($areaParkirs as $area)
        <div class="bg-white rounded-lg shadow border">

            {{-- HEADER CARD --}}
            <div class="flex justify-between items-center px-6 py-4 border-b">
                <div>
                    <h2 class="text-lg font-semibold">
                        {{ $area->nama_area }}
                    </h2>
                    <p class="text-sm text-gray-600">
                        Total Kendaraan:
                        <span class="font-medium">
                            {{ $area->kendaraan->count() }}
                        </span>
                    </p>
                </div>
            </div>

            {{-- BODY CARD --}}
            <div class="px-6 py-4">

                {{-- HEADER TABEL --}}
                <div class="grid grid-cols-6 font-semibold text-sm text-gray-600 border-b pb-2">
                    <div>Plat Nomor</div>
                    <div>Tipe</div>
                    <div>Warna</div>
                    <div>Member</div>
                    <div>Tanggal</div>
                    <div>Jam Masuk</div>
                </div>

                <div class="divide-y">

                    @forelse ($area->kendaraan as $kendaraan)
                        <div class="grid grid-cols-6 py-2 text-sm items-center">
                            <div class="font-medium">
                                {{ $kendaraan->plat_nomor }}
                            </div>

                            <div>
                                {{ $kendaraan->tipeKendaraan->tipe_kendaraan ?? '-' }}
                            </div>

                            <div>
                                {{ $kendaraan->warna ?? '-' }}
                            </div>

                            <div>
                               {{ $kendaraan->membershipAktif->membership->nama ?? '-' }}
                            </div>

                            <div class="text-gray-500">
                                {{ $kendaraan->created_at->format('d-m-Y') }}
                            </div>

                            <div class="text-gray-500">
                                {{ $kendaraan->created_at->format('H:i') }}
                            </div>
                        </div>
                    @empty
                        <div class="py-4 text-sm text-gray-400 italic">
                            Tidak ada kendaraan di area ini
                        </div>
                    @endforelse

                </div>
            </div>

        </div>
    @empty
        <div class="bg-white rounded-lg shadow p-6 text-center text-gray-500">
            Data area parkir kosong
        </div>
    @endforelse

</div>

@endsection
