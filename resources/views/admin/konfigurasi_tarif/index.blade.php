@extends('components.app')

@section('title', 'Konfigurasi Tarif')

@section('content')
<div class="w-full px-6 py-6">

    {{-- ================= HEADER ================= --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold">Konfigurasi Tarif Parkir</h1>
        <p class="text-sm text-gray-500">
            Pengaturan tarif lanjutan & diskon event
        </p>
    </div>

    {{-- ================= ALERT ================= --}}
    @if (session('success'))
        <div class="mb-6 bg-green-100 text-green-700 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    {{-- ================= GRID KIRI KANAN ================= --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">

        {{-- ================= CARD KIRI : FORM ================= --}}
        <div class="bg-white rounded-xl shadow p-6">
            <form method="POST" action="{{ url('/admin/konfigurasi-tarif') }}" class="space-y-8" onsubmit="return confirm('Yakin data yang dimasukkan sudah benar?')>
                @csrf

                {{-- TARIF LANJUTAN --}}
                <div>
                    <label class="block text-sm font-medium mb-2">
                        Persentase Tarif Per Jam Lanjutan
                    </label>

                    <div class="flex items-center gap-3">
                        <input
                            type="number"
                            name="persentase_tarif_perjam_lanjutan"
                            value="{{ old('persentase_tarif_perjam_lanjutan', $config->persentase_tarif_perjam_lanjutan) }}"
                            class="w-40 border rounded-lg px-4 py-2 focus:ring focus:ring-blue-200"
                            min="0"
                        >
                        <span class="text-lg font-semibold">%</span>
                    </div>

                    <p class="text-xs text-gray-500 mt-2">
                        Berlaku setelah jam pertama (tarif dasar)
                    </p>
                </div>

{{-- DISKON --}}
<div class="border-t pt-6">
    <div class="flex items-center justify-between mb-4">
        <h2 class="font-semibold text-lg">Diskon / Event</h2>

        {{-- TOGGLE GESER --}}
        <label class="relative inline-flex items-center cursor-pointer">
            <input type="checkbox" name="diskon_aktif" class="sr-only peer" {{ $config->diskon_aktif ? 'checked' : '' }}>

            <div class="w-12 h-6 bg-gray-500 rounded-full peer peer-checked:bg-green-500 transition-colors duration-300"></div>
            <div class="absolute left-0 top-0 w-6 h-6 bg-white rounded-full shadow transform transition-transform duration-300 peer-checked:translate-x-6"></div>
        </label>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div>
            <label class="block text-sm font-medium mb-1">
                Diskon (%)
            </label>
            <input
                type="number"
                name="diskon_persen"
                value="{{ old('diskon_persen', $config->diskon_persen) }}"
                class="w-full border rounded-lg px-3 py-2"
                min="0"
                max="100"
            >
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">
                Berlaku Sampai
            </label>
            <input
                type="date"
                name="diskon_sampai"
                value="{{ optional($config->diskon_sampai)->format('Y-m-d') }}"
                class="w-full border rounded-lg px-3 py-2"
            >
        </div>
    </div>
</div>


                {{-- SUBMIT --}}
                <div class="flex justify-end">
                    <button
                        type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-2 rounded-lg">
                        Simpan Konfigurasi
                    </button>
                </div>
            </form>
        </div>

        {{-- ================= CARD KANAN : ESTIMASI ================= --}}
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="text-lg font-semibold mb-4">Estimasi Tarif</h2>

           {{-- CATATAN --}}
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
