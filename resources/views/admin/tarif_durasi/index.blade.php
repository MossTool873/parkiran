@extends('components.app')

@section('title', 'Tarif Durasi')

@section('content')

<div x-data="tarifDurasiUI()">

{{-- ================= HEADER ================= --}}
<div class="flex justify-between items-center mb-4">
    <h1 class="text-2xl font-bold">Tarif Durasi</h1>

    <a href="{{ url('/admin/tarif-durasi/create') }}"
       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
        + Tambah Tarif
    </a>
</div>

{{-- ================= TABLE ================= --}}
<div class="bg-white rounded-lg shadow overflow-x-auto relative">
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

            <tr class="border-t hover:bg-gray-50">
                <td class="px-4 py-3">
                    {{ $loop->iteration + ($tarifDurasi->firstItem() - 1) }}
                </td>

                <td class="px-4 py-3">
                    {{ $prevBatas }} – {{ $item->batas_jam }} Jam
                </td>

                {{-- TARIF BUTTON --}}
                <td class="px-4 py-3">
                    <button
                        @click="open({{ $item->id }}, {{ $item->persentase }}, $event)"
                        class="text-blue-600 font-semibold flex items-center gap-1 hover:underline">

                        {{ $item->persentase }}%

                        <svg class="w-4 h-4"
                             fill="none" stroke="currentColor" stroke-width="2"
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
                        <button class="text-red-600 hover:underline">
                            Hapus
                        </button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

{{-- ================= FLOATING PANEL ================= --}}
<div
    x-show="show"
    x-transition
    @click.outside="close"
    :style="{ top: y + 'px', left: x + 'px' }"
    class="fixed z-50 bg-white border rounded-lg shadow-lg w-80 p-4">

    <div class="flex justify-between items-center mb-2">
        <h3 class="font-semibold text-gray-700">
            Estimasi Harga (<span x-text="persen"></span>%)
        </h3>
        <button @click="close" class="text-gray-400 hover:text-gray-600"></button>
    </div>

    <ul class="text-sm space-y-1">
        @foreach($tarifDasar as $tarif)
            <li class="flex justify-between">
                <span>{{ $tarif->tipeKendaraan->tipe_kendaraan }}</span>
                <span class="font-mono">
                    Rp {{ number_format($tarif->tarif_perjam,0,',','.') }}
                </span>
            </li>
        @endforeach
    </ul>
</div>

</div>

{{-- ================= SCRIPT ================= --}}
<script>
function tarifDurasiUI() {
    return {
        show: false,
        x: 0,
        y: 0,
        persen: 0,

        open(id, persen, event) {
            const rect = event.target.getBoundingClientRect();
            this.x = rect.left;
            this.y = rect.bottom + 8;
            this.persen = persen;
            this.show = true;
        },

        close() {
            this.show = false;
        }
    }
}
</script>

@endsection
