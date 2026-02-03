@extends('components.app')

@section('title', 'Laporan Transaksi Periode')

@section('content')

{{-- ================= FILTER TANGGAL ================= --}}
<div class="bg-white border rounded-lg px-6 py-4 mb-4">
    <form method="GET" action="/laporan/periode"
          class="flex flex-wrap items-end gap-4">

        <div>
            <label class="block text-sm font-medium mb-1">Tanggal Awal</label>
            <input type="date"
                   name="tanggal_awal"
                   value="{{ request('tanggal_awal') }}"
                   class="border rounded px-3 py-2">
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Tanggal Akhir</label>
            <input type="date"
                   name="tanggal_akhir"
                   value="{{ request('tanggal_akhir') }}"
                   class="border rounded px-3 py-2">
        </div>

        <button type="submit"
                class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 rounded">
            Tampilkan
        </button>

        {{-- ================= TOMBOL PRINT ================= --}}
        <button type="button"
                onclick="window.print()"
                @if(!$tanggalAwal || !$tanggalAkhir)
                    disabled
                    class="bg-gray-300 text-gray-600 px-4 py-2 rounded cursor-not-allowed"
                @else
                    class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 rounded print:hidden"
                @endif
        >
            🖨️ Print
        </button>

    </form>
</div>

{{-- ================= PERINGATAN KECIL ================= --}}
@if (!$tanggalAwal || !$tanggalAkhir)
    <div class="mb-6 text-sm text-yellow-700 bg-yellow-100 border border-yellow-200 px-4 py-3 rounded">
        Silakan pilih <strong>tanggal awal</strong> dan <strong>tanggal akhir</strong>
        untuk menampilkan laporan transaksi.
    </div>
@else

{{-- ================= PRINT AREA ================= --}}
<div class="print-area">

    {{-- ================= HEADER ================= --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold">Laporan Transaksi Periode</h1>
        <p class="text-sm font-semibold text-gray-600">
            {{ $tanggalAwal->translatedFormat('d F Y') }}
            –
            {{ $tanggalAkhir->translatedFormat('d F Y') }}
        </p>
    </div>

    <div class="space-y-6">

        {{-- ================= SUMMARY CARD ================= --}}
        <div class="bg-white rounded-lg border">
            <div class="px-6 py-4 border-b">
                <h2 class="text-lg font-semibold">Ringkasan</h2>
            </div>

            <div class="px-6 py-4 grid grid-cols-2 gap-6">
                <div>
                    <p class="text-sm">Total Transaksi</p>
                    <p class="text-xl font-bold">{{ $totalTransaksi }}</p>
                </div>

                <div>
                    <p class="text-sm">Total Pendapatan</p>
                    <p class="text-xl font-bold">
                        Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>

        {{-- ================= BREAKDOWN TIPE KENDARAAN ================= --}}
        <div class="bg-white rounded-lg border">
            <div class="px-6 py-4 border-b">
                <h2 class="text-lg font-semibold">Breakdown Tipe Kendaraan</h2>
            </div>

            <div class="px-6 py-4">
                <div class="grid grid-cols-3 font-semibold border-b pb-2">
                    <div>Tipe Kendaraan</div>
                    <div>Jumlah</div>
                    <div>Total Pendapatan</div>
                </div>

                @forelse ($breakdownTipeKendaraan as $item)
                    <div class="grid grid-cols-3 py-2">
                        <div>{{ $item->tipe_kendaraan }}</div>
                        <div class="font-semibold">{{ $item->total }}</div>
                        <div class="font-semibold">
                            Rp {{ number_format($item->total_pendapatan, 0, ',', '.') }}
                        </div>
                    </div>
                @empty
                    <div class="py-4 text-center text-gray-500">
                        Data tidak tersedia
                    </div>
                @endforelse
            </div>
        </div>

        {{-- ================= BREAKDOWN METODE PEMBAYARAN ================= --}}
        <div class="bg-white rounded-lg border">
            <div class="px-6 py-4 border-b">
                <h2 class="text-lg font-semibold">Breakdown Metode Pembayaran</h2>
            </div>

            <div class="px-6 py-4">
                <div class="grid grid-cols-3 font-semibold border-b pb-2">
                    <div>Metode</div>
                    <div>Jumlah</div>
                    <div>Total Pendapatan</div>
                </div>

                @forelse ($breakdownMetodePembayaran as $item)
                    <div class="grid grid-cols-3 py-2">
                        <div>{{ $item->nama_metode }}</div>
                        <div class="font-semibold">{{ $item->total }}</div>
                        <div class="font-semibold">
                            Rp {{ number_format($item->total_pendapatan, 0, ',', '.') }}
                        </div>
                    </div>
                @empty
                    <div class="py-4 text-center text-gray-500">
                        Data tidak tersedia
                    </div>
                @endforelse
            </div>
        </div>

    </div>
</div>

{{-- ================= PRINT STYLE ================= --}}
<style>
@media print {
    body * {
        visibility: hidden;
    }

    .print-area, .print-area * {
        visibility: visible;
    }

    .print-area {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }

    body {
        background: white !important;
    }
}
</style>

@endif
@endsection
