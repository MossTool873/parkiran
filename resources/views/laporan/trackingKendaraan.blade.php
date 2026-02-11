@extends('components.app')

@section('title', 'Tracking Kendaraan')

@section('content')

{{-- HEADER --}}
<div class="flex flex-col md:flex-row md:justify-between md:items-center gap-3 mb-6">
    <h1 class="text-2xl font-bold">
        Tracking Kendaraan Parkir (Sedang Parkir)
    </h1>

    {{-- SEARCH --}}
    <form method="GET">
        <div class="flex gap-2 w-full md:w-80">
            <input
                type="text"
                name="search"
                value="{{ $search }}"
                placeholder="Cari plat atau nama member..."
                class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring focus:ring-blue-200">

            <button
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">
                Cari
            </button>
        </div>
    </form>
</div>

{{-- TABLE --}}
<div class="bg-white rounded-lg shadow border overflow-x-auto">

    <table class="min-w-full text-sm">

        <thead class="bg-gray-50 border-b text-gray-600 uppercase text-xs tracking-wide">
            <tr>
                <th class="px-6 py-3 text-left">Member</th>
                <th class="px-6 py-3 text-left">Tipe</th>
                <th class="px-6 py-3 text-left">Plat Nomor</th>
                <th class="px-6 py-3 text-left">Warna</th>
                <th class="px-6 py-3 text-left">Waktu Masuk</th>
                <th class="px-6 py-3 text-left">Area Parkir</th>
            </tr>
        </thead>

        <tbody class="divide-y">

            @forelse ($transaksis as $trx)
                <tr class="hover:bg-gray-50 transition">

                    {{-- MEMBER --}}
                    <td class="px-6 py-3">
                        {{ $trx->kendaraan->membershipAktif->membership->nama  ?? '-' }}
                    </td>

                    {{-- TIPE --}}
                    <td class="px-6 py-3">
                        {{ $trx->kendaraan->tipeKendaraan->tipe_kendaraan ?? '-' }}
                    </td>

                    {{-- PLAT --}}
                    <td class="px-6 py-3 font-medium">
                        {{ $trx->kendaraan->plat_nomor ?? '-' }}
                    </td>

                    {{-- WARNA --}}
                    <td class="px-6 py-3">
                        {{ $trx->kendaraan->warna ?? '-' }}
                    </td>

                    {{-- WAKTU MASUK (AMBIL DARI TRANSAKSI) --}}
                    <td class="px-6 py-3 text-gray-500">
                        {{ \Carbon\Carbon::parse($trx->waktu_masuk)->format('d-m-Y H:i') }}
                    </td>

                    {{-- AREA PARKIR --}}
                    <td class="px-6 py-3">
                        {{ $trx->areaParkir->nama_area ?? '-' }}
                    </td>

                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-6 text-center text-gray-400 italic">
                        Tidak ada kendaraan yang sedang parkir
                    </td>
                </tr>
            @endforelse

        </tbody>
    </table>
</div>

{{-- PAGINATION --}}
<div class="mt-6">
    {{ $transaksis->withQueryString()->links() }}
</div>

@endsection
