@extends('components.app')

@section('title', 'Riwayat Transaksi')

@section('content')
    <h1 class="text-2xl font-bold mb-6">Riwayat Transaksi Parkir</h1>

    {{-- 🔍 FORM FILTER --}}
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="text-sm font-medium">Plat Nomor</label>
                <input
                    type="text"
                    name="plat_nomor"
                    value="{{ request('plat_nomor') }}"
                    class="w-full border rounded px-3 py-2"
                    placeholder="B 1234 XYZ"
                >
            </div>

            <div>
                <label class="text-sm font-medium">Tanggal Mulai</label>
                <input
                    type="date"
                    name="tanggal_mulai"
                    value="{{ request('tanggal_mulai') }}"
                    class="w-full border rounded px-3 py-2"
                >
            </div>

            <div>
                <label class="text-sm font-medium">Tanggal Selesai</label>
                <input
                    type="date"
                    name="tanggal_selesai"
                    value="{{ request('tanggal_selesai') }}"
                    class="w-full border rounded px-3 py-2"
                >
            </div>

            <div class="flex items-end gap-2">
                <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                    Cari
                </button>
                <a href="{{ url()->current() }}" class="bg-gray-300 px-4 py-2 rounded-lg">
                    Reset
                </a>
            </div>
        </form>
    </div>

    {{-- 📋 TABEL --}}
    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-100 text-left">
                <tr>
                    <th class="px-4 py-3 w-16">No</th>
                    <th class="px-4 py-3">Kode Transaksi</th>
                    <th class="px-4 py-3">Plat Nomor</th>
                    <th class="px-4 py-3">Tipe Kendaraan</th>
                    <th class="px-4 py-3">Area Parkir</th>
                    <th class="px-4 py-3">Waktu Masuk</th>
                    <th class="px-4 py-3">Waktu Keluar</th>
                    <th class="px-4 py-3 w-28">Durasi (Jam)</th>
                    <th class="px-4 py-3">Total Biaya</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($transaksis as $i => $t)
                    <tr class="border-t">
                        <td class="px-4 py-3">
                            {{ $transaksis->firstItem() + $i }}
                        </td>
                        <td class="px-4 py-3 font-semibold">
                            {{ $t->kode }}
                        </td>
                        <td class="px-4 py-3">
                            {{ $t->kendaraan->plat_nomor }}
                        </td>
                        <td class="px-4 py-3">
                            {{ $t->kendaraan->tipeKendaraan->tipe_kendaraan ?? '-' }}
                        </td>
                        <td class="px-4 py-3">
                            {{ $t->areaParkir->nama_area ?? '-' }}
                        </td>
                        <td class="px-4 py-3">
                            {{ $t->waktu_masuk }}
                        </td>
                        <td class="px-4 py-3">
                            {{ $t->waktu_keluar ?? '-' }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            {{ $t->durasi_jam ?? '-' }}
                        </td>
                        <td class="px-4 py-3">
                            @if ($t->biaya_total)
                                Rp {{ number_format($t->biaya_total, 0, ',', '.') }}
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-4 py-6 text-center text-gray-500">
                            Data transaksi kosong
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- 📄 PAGINATION --}}
    <div class="mt-4">
        {{ $transaksis->withQueryString()->links() }}
    </div>
@endsection
