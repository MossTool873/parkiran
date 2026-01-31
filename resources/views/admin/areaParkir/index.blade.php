@extends('components.app')

@section('title', 'Area Parkir')

@section('content')
    {{-- HEADER --}}
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Area Parkir</h1>

        <a href="{{ url('/admin/areaParkir/create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
            + Tambah Area Parkir
        </a>
    </div>

    {{-- ALERT --}}
    @if (session('success'))
        <div class="mb-4 bg-green-100 border border-green-300 text-green-700 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
    @endif

<div class="space-y-6">

    @forelse ($areaParkirs as $area)
        <div class="bg-white rounded-lg shadow border">

            {{-- ================= HEADER CARD ================= --}}
            <div class="flex justify-between items-center px-6 py-4 border-b">
                <div class="space-y-1">
                    <h2 class="text-lg font-semibold">
                        {{ $area->nama_area }}
                    </h2>
                    <p class="text-sm text-gray-600">
                        Total Kapasitas: <span class="font-medium">{{ $area->total_kapasitas }}</span>
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

            {{-- ================= BODY CARD ================= --}}
            <div class="px-6 py-4">
                <div class="grid grid-cols-3 font-semibold text-sm text-gray-600 border-b pb-2">
                    <div>Tipe Kendaraan</div>
                    <div>Kapasitas</div>
                    <div>Terisi</div>
                </div>

                <div class="divide-y">
                    @foreach ($area->detailKapasitas as $detail)
                        <div class="grid grid-cols-3 py-2 text-sm">
                            <div>
                                {{ $detail->tipeKendaraan->tipe_kendaraan }}
                            </div>
                            <div>
                                {{ $detail->kapasitas }}
                            </div>
                            <div>
                                {{ $detail->terisi ?? 0 }}
                            </div>
                        </div>
                    @endforeach
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
