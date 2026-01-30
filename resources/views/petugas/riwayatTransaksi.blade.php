@extends('components.app')

@section('title', 'Riwayat Transaksi')

@section('content')
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
                <button class="bg-blue-600 text-white px-4 py-2 rounded">
                    Cari
                </button>
                <a href="{{ url()->current() }}" class="bg-gray-300 px-4 py-2 rounded">
                    Reset
                </a>
            </div>
        </form>
    </div>

    {{-- 📋 TABEL --}}
    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="w-full border-collapse">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border px-4 py-2">No</th>
                    <th class="border px-4 py-2">Plat Nomor</th>
                    <th class="border px-4 py-2">Tipe Kendaraan</th>
                    <th class="border px-4 py-2">Area Parkir</th>
                    <th class="border px-4 py-2">Waktu Masuk</th>
                    <th class="border px-4 py-2">Waktu Keluar</th>
                    <th class="border px-4 py-2">Durasi</th>
                    <th class="border px-4 py-2">Biaya</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($transaksis as $trx)
                    <tr>
                        <td class="border px-4 py-2 text-center">
                            {{ $loop->iteration + ($transaksis->currentPage() - 1) * $transaksis->perPage() }}
                        </td>
                        <td class="border px-4 py-2">
                            {{ $trx->kendaraan->plat_nomor ?? '-' }}
                        </td>
                        <td class="border px-4 py-2">
                            {{ $trx->kendaraan->tipeKendaraan->tipe_kendaraan ?? '-' }}
                        </td>
                        <td class="border px-4 py-2">
                            {{ $trx->areaParkir->nama_area ?? '-' }}
                        </td>
                        <td class="border px-4 py-2">
                            {{ $trx->waktu_masuk }}
                        </td>
                        <td class="border px-4 py-2">
                            {{ $trx->waktu_keluar ?? 'Masih Parkir' }}
                        </td>
                        <td class="border px-4 py-2 text-center">
                            {{ $trx->durasi_jam ?? '-' }}
                        </td>
                        <td class="border px-4 py-2 text-right">
                            Rp {{ number_format($trx->biaya_total ?? 0, 0, ',', '.') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="border px-4 py-6 text-center text-gray-500">
                            Data transaksi tidak ditemukan
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
