@extends('components.app')

@section('title', 'Riwayat Transaksi')

@section('content')
    <div class="max-w-7xl mx-auto">

        <h1 class="text-2xl font-bold mb-6">Riwayat Transaksi Parkir</h1>

        {{-- 🔍 FORM FILTER --}}
        <div class="bg-white rounded-lg shadow p-4 mb-6">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">

                <div>
                    <label class="text-sm font-medium">Plat Nomor</label>
                    <input type="text" name="plat_nomor" value="{{ request('plat_nomor') }}"
                        class="w-full border rounded px-3 py-2" placeholder="B 1234 XYZ">
                </div>

                <div>
                    <label class="text-sm font-medium">Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}"
                        class="w-full border rounded px-3 py-2">
                </div>

                <div>
                    <label class="text-sm font-medium">Tanggal Selesai</label>
                    <input type="date" name="tanggal_selesai" value="{{ request('tanggal_selesai') }}"
                        class="w-full border rounded px-3 py-2">
                </div>

                <div class="flex items-end gap-2">
                    <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                        Cari
                    </button>
                    <a href="{{ url()->current() }}" class="bg-gray-300 hover:bg-gray-400 px-4 py-2 rounded-lg">
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
                        <th class="px-4 py-3 w-14">No</th>
                        <th class="px-4 py-3">Kode</th>
                        <th class="px-4 py-3">Plat</th>
                        <th class="px-4 py-3">Tipe</th>
                        <th class="px-4 py-3">Area</th>
                        <th class="px-4 py-3">Masuk</th>
                        <th class="px-4 py-3">Keluar</th>
                        <th class="px-4 py-3 text-center">Durasi</th>
                        <th class="px-4 py-3 text-right">Total</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($transaksis as $i => $t)
                        <tr class="border-t hover:bg-gray-50">
                            <td class="px-4 py-3">
                                {{ $transaksis->firstItem() + $i }}
                            </td>

                            <td class="px-4 py-3 font-semibold">
                                {{ $t->kode }}
                            </td>

                            <td class="px-4 py-3">
                                {{ $t->kendaraan->plat_nomor ?? '-' }}
                            </td>

                            <td class="px-4 py-3">
                                {{ $t->kendaraan->tipeKendaraan->tipe_kendaraan ?? '-' }}
                            </td>

                            <td class="px-4 py-3">
                                {{ $t->areaParkir->nama_area ?? '-' }}
                            </td>

                            <td class="px-4 py-3">
                                {{ $t->waktu_masuk ? \Carbon\Carbon::parse($t->waktu_masuk)->format('d/m/Y H:i') : '-' }}
                            </td>

                            <td class="px-4 py-3">
                                {{ $t->waktu_keluar ? \Carbon\Carbon::parse($t->waktu_keluar)->format('d/m/Y H:i') : '-' }}
                            </td>

                            <td class="px-4 py-3 text-center">
                                {{ $t->durasi_menit !== null ? intdiv($t->durasi_menit, 60) . ' jam ' . $t->durasi_menit % 60 . ' menit' : '-' }}
                            </td>
                            <td class="px-4 py-3 text-right font-medium">
                                {{ $t->biaya_total ? 'Rp ' . number_format($t->biaya_total, 0, ',', '.') : '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-6 text-center text-gray-500">
                                Data transaksi tidak ditemukan
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- 📄 PAGINATION --}}
        <div class="mt-4">
            {{ $transaksis->links() }}
        </div>

    </div>
@endsection
