@extends('components.app')

@section('title', 'Konfigurasi Tarif')

@section('content')
<div class="w-full px-6 py-6">

    {{-- HEADER --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold">Konfigurasi Tarif Parkir</h1>
    </div>

    {{-- GRID KIRI KANAN --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">

        {{-- CARD KIRI: INFO TARIF --}}
        <div class="bg-white rounded-xl shadow p-6 space-y-6">

            {{-- TARIF LANJUTAN --}}
            <div>
                <label class="block text-sm font-medium mb-2">
                    Persentase Tarif Per Jam Lanjutan
                </label>
                <div class="flex items-center gap-3">
                    <span class="px-4 py-2 border rounded-lg bg-gray-100">
                        {{ $config->persentase_tarif_perjam_lanjutan }} %
                    </span>
                </div>
                <p class="text-xs text-gray-500 mt-2">
                    Berlaku setelah jam pertama (tarif dasar)
                </p>
            </div>

            {{-- DISKON --}}
            <div class="border-t pt-6 space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="font-semibold text-lg">Diskon / Event</h2>
                    <span class="px-3 py-1 rounded-full text-sm font-medium {{ $config->diskon_aktif ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                        {{ $config->diskon_aktif ? 'Aktif' : 'Tidak Aktif' }}
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium mb-1">Diskon (%)</label>
                        <span class="px-3 py-2 border rounded-lg bg-gray-100">
                            {{ $config->diskon_persen }} %
                        </span>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Berlaku Sampai</label>
                        <span class="px-3 py-2 border rounded-lg bg-gray-100">
                            {{ optional($config->diskon_sampai)->format('d-m-Y') ?? '-' }}
                        </span>
                    </div>
                </div>
            </div>

        </div>

        {{-- CARD KANAN: ESTIMASI TARIF --}}
        <div class="bg-white rounded-xl shadow p-6">

            <h2 class="text-lg font-semibold mb-4">Estimasi Tarif</h2>

            <div class="mt-4 text-xs text-gray-600 space-y-1">
                <div>
                    <strong>Catatan:</strong>
                    Diskon
                    <span class="font-semibold">{{ $config->diskon_persen }}%</span>
                    {{ $config->diskon_aktif ? 'sedang aktif' : 'tidak aktif' }},
                    diskon <u>tidak dihitung</u> dalam estimasi ini.
                </div>

                <div>
                    Tarif per jam lanjutan dihitung dari durasi berlebih
                    setelah durasi tertinggi
                    <span class="font-semibold">
                        ({{ $durasiTertinggi ?? 0 }} jam)
                    </span>.
                </div>
            </div>

            <br>

            {{-- TABEL ESTIMASI --}}
            <div class="overflow-x-auto">
                <table class="w-full text-sm border border-gray-200 rounded-lg">
                    <thead class="bg-gray-100 text-left">
                        <tr>
                            <th class="px-4 py-3">Tipe Kendaraan</th>
                            <th class="px-4 py-3">Tarif Dasar / Jam</th>
                            <th class="px-4 py-3">Tarif Lanjutan / Jam</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($tarifDasar as $tarif)
                            @php
                                $dasar = $tarif->tarif_perjam;
                                $lanjutan = $dasar * ($config->persentase_tarif_perjam_lanjutan / 100);
                            @endphp

                            <tr class="border-t hover:bg-gray-50">
                                <td class="px-4 py-3 font-medium">
                                    {{ $tarif->tipeKendaraan->tipe_kendaraan }}
                                </td>

                                <td class="px-4 py-3">
                                    Rp {{ number_format($dasar, 0, ',', '.') }}
                                </td>

                                <td class="px-4 py-3">
                                    Rp {{ number_format($lanjutan, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>

    </div>
</div>
@endsection
