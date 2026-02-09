@extends('components.app')

@section('title', 'Data Kendaraan')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Data Kendaraan</h1>

    {{-- SEARCH BAR --}}
    <div class="flex justify-end mb-4">
        <form method="GET" action="{{ url('/view-only/kendaraan') }}">
            <div class="flex gap-2 w-full md:w-80">
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari plat nomor..."
                    class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring focus:ring-blue-200">

                <button
                    type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">
                    Cari
                </button>
            </div>
        </form>
    </div>

    {{-- TABLE --}}
    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-100 text-left">
                <tr>
                    <th class="px-4 py-3">No</th>
                    <th class="px-4 py-3">Plat Nomor</th>
                    <th class="px-4 py-3">Warna</th>
                    <th class="px-4 py-3">Tipe Kendaraan</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($kendaraans as $kendaraan)
                    <tr class="border-t">
                        <td class="px-4 py-3">{{ ($kendaraans->currentPage() - 1) * $kendaraans->perPage() + $loop->iteration }}</td>
                        <td class="px-4 py-3">{{ $kendaraan->plat_nomor }}</td>
                        <td class="px-4 py-3">{{ $kendaraan->warna }}</td>
                        <td class="px-4 py-3">{{ $kendaraan->tipeKendaraan->tipe_kendaraan ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-6 text-center text-gray-500">
                            Data kendaraan kosong
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- PAGINATION --}}
    <div class="mt-4 text-center">
        {{ $kendaraans->links() }}
    </div>
@endsection
