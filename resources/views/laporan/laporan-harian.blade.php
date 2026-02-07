@extends('components.app')

@section('title', 'Laporan Transaksi Harian')

@section('content')

{{-- ================= PRINT AREA ================= --}}
<div class="print-area">

    {{-- ================= HEADER ================= --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold">Laporan Transaksi Harian</h1>
            <p class="text-lg font-semibold text-gray-600">
                {{ now()->translatedFormat('d F Y') }}
            </p>
        </div>

        <button onclick="window.print()"
            class="bg-gray-800 hover:bg-gray-900 text-white px-4 py-2 rounded-lg print:hidden">
            🖨️ Print
        </button>
    </div>

    <div class="space-y-6">

        {{-- ================= TOTALS SEJARAH ================= --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            {{-- Total Transaksi --}}
            <div class="bg-white rounded-lg border shadow-sm">
                <div class="px-6 py-4">
                    <h2 class="text-lg font-semibold mb-2">Total Transaksi</h2>
                    <p class="text-2xl font-bold">{{ $totalTransaksi }}</p>
                </div>
            </div>

            {{-- Total Pendapatan --}}
            <div class="bg-white rounded-lg border shadow-sm">
                <div class="px-6 py-4">
                    <h2 class="text-lg font-semibold mb-2">Total Pendapatan</h2>
                    <p class="text-2xl font-bold">
                        Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>

        {{-- ================= BREAKDOWN TIPE KENDARAAN ================= --}}
        <div class="bg-white rounded-lg border shadow-sm">
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
        <div class="bg-white rounded-lg border shadow-sm">
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

@endsection
