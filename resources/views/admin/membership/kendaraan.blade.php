@extends('components.app')

@section('title', 'Membership Kendaraan')

@section('content')
<div class="max-w-6xl mx-auto bg-white rounded-lg shadow p-4 text-sm">

    <h1 class="text-lg font-semibold mb-4">Data Membership Kendaraan</h1>

    <div class="overflow-x-auto">
        <table class="w-full border border-gray-200">
            <thead class="bg-gray-100">
                <tr class="text-left">
                    <th class="border px-3 py-2">No</th>
                    <th class="border px-3 py-2">Plat Nomor</th>
                    <th class="border px-3 py-2">Member</th>
                    <th class="border px-3 py-2">Area Parkir</th>
                    <th class="border px-3 py-2">Status Parkir</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($kendaraanMemberships as $i => $mk)
                    @php
                        // cek apakah kendaraan masih parkir
                        $sedangParkir = \App\Models\Transaksi::where('kendaraan_id', $mk->kendaraan_id)
                            ->whereNull('waktu_keluar')
                            ->exists();
                    @endphp

                    <tr class="hover:bg-gray-50">
                        <td class="border px-3 py-2">{{ $i + 1 }}</td>

                        <td class="border px-3 py-2">
                            {{ $mk->kendaraan?->plat_nomor ?? '-' }}
                        </td>

                        <td class="border px-3 py-2">
                            {{ $mk->membership?->nama ?? '-' }}
                        </td>

                        <td class="border px-3 py-2">
                            @if($mk->areaParkir)
                                {{ $mk->areaParkir->nama_area }}
                            @else
                                <span class="text-gray-400 italic">Kosong</span>
                            @endif
                        </td>

                        <td class="border px-3 py-2">
                            @if($sedangParkir)
                                <span class="px-2 py-1 text-xs rounded bg-green-100 text-green-700">
                                    Sedang Parkir
                                </span>
                            @else
                                <span class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-600">
                                    Tidak Parkir
                                </span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-gray-500">
                            Data tidak tersedia
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection
