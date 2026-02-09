@extends('components.app')

@section('title', 'Tarif Tipe Kendaraan')

@section('content')

    {{-- HEADER --}}
    <h1 class="text-2xl font-bold mb-4">Tarif Tipe Kendaraan</h1>

    {{-- TABLE --}}
    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-100 text-left">
                <tr>
                    <th class="px-4 py-3 w-16">No</th>
                    <th class="px-4 py-3">Tipe Kendaraan</th>
                    <th class="px-4 py-3">Tarif / Jam</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($tarifTipeKendaraans as $tarif)
                    <tr class="border-t">
                        <td class="px-4 py-3">{{ $loop->iteration }}</td>
                        <td class="px-4 py-3">
                            {{ $tarif->tipeKendaraan->tipe_kendaraan ?? "-" }}
                        </td>
                        <td class="px-4 py-3">
                            Rp {{ number_format($tarif->tarif_perjam, 0, ',', '.') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-4 py-6 text-center text-gray-500">
                            Data tarif kosong
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

@endsection
