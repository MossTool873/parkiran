@extends('components.app')

@section('title', 'Tarif Durasi')

@section('content')

<div x-data="{ openRow: null }">

    {{-- ================= HEADER ================= --}}
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Tarif Durasi</h1>

        <a href="{{ url('/admin/tarif-durasi/create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
            + Tambah Tarif
        </a>
    </div>

    {{-- ================= TABLE ================= --}}
    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-100 text-left">
                <tr>
                    <th class="px-4 py-3 w-16">No</th>
                    <th class="px-4 py-3">Rentang Jam</th>
                    <th class="px-4 py-3">Tarif</th>
                    <th class="px-4 py-3 w-40">Aksi</th>
                </tr>
            </thead>

            <tbody>
            @foreach ($tarifDurasi as $item)
                @php
                    $prevBatas = $loop->first ? 0 : $tarifDurasi[$loop->index - 1]->batas_jam + 1;
                @endphp

                {{-- BARIS UTAMA --}}
                <tr class="border-t hover:bg-gray-50">
                    <td class="px-4 py-3">
                        {{ $loop->iteration + ($tarifDurasi->firstItem() - 1) }}
                    </td>

                    <td class="px-4 py-3">
                        {{ $prevBatas }} – {{ $item->batas_jam }} Jam
                    </td>

                    <td class="px-4 py-3">
                        <button type="button"
                                @click="openRow === {{ $item->id }} ? openRow = null : openRow = {{ $item->id }}"
                                class="text-blue-600 font-semibold flex items-center gap-1 hover:underline">

                            {{ $item->persentase }}%

                            <svg class="w-4 h-4 transition-transform duration-200"
                                 :class="{ 'rotate-180': openRow === {{ $item->id }} }"
                                 fill="none"
                                 stroke="currentColor"
                                 stroke-width="2"
                                 viewBox="0 0 24 24">
                                <path d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                    </td>

                    <td class="px-4 py-3 flex gap-2">
                        <a href="{{ url('/admin/tarif-durasi/'.$item->id.'/edit') }}"
                           class="text-blue-600 hover:underline">
                            Edit
                        </a>

                        <form action="{{ url('/admin/tarif-durasi/'.$item->id) }}"
                              method="POST"
                              onsubmit="return confirm('Yakin hapus?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="text-red-600 hover:underline">
                                Hapus
                            </button>
                        </form>
                    </td>
                </tr>

                {{-- BARIS DETAIL --}}
                <tr x-show="openRow === {{ $item->id }}"
                    x-transition
                    x-cloak
                    class="bg-gray-50">

                    <td colspan="4" class="px-6 py-4">

                        <div class="bg-white border rounded-lg p-4 shadow-sm">
                            <h3 class="font-semibold text-gray-700 mb-3">
                                Estimasi Harga ({{ $item->persentase }}%)
                            </h3>

                            <div class="grid md:grid-cols-2 gap-4">
                                @foreach($tarifDasar as $tarif)
                                    <div class="flex justify-between border-b pb-1">
                                        <span>{{ $tarif->tipeKendaraan->tipe_kendaraan }}</span>
                                        <span>
                                            Rp {{ number_format(($tarif->tarif_perjam * $item->persentase) / 100, 0, ',', '.') }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                    </td>
                </tr>

            @endforeach
            </tbody>
        </table>
    </div>

</div>

@endsection
