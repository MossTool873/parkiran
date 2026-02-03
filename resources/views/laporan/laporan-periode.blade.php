@extends('components.app')

@section('title', 'Laporan Transaksi Periode')

@section('content')

{{-- ================= FILTER TANGGAL ================= --}}
<form method="GET" action="{{ route('laporan.transaksi.periode') }}"
      class="flex flex-wrap items-end gap-4 mb-6 print:hidden">

    <div>
        <label class="text-sm font-semibold">Tanggal Mulai</label>
        <input type="date" name="tanggal_mulai"
               value="{{ request('tanggal_mulai') }}"
               class="border rounded px-3 py-2">
    </div>

    <div>
        <label class="text-sm font-semibold">Tanggal Akhir</label>
        <input type="date" name="tanggal_akhir"
               value="{{ request('tanggal_akhir') }}"
               class="border rounded px-3 py-2">
    </div>

    <button class="bg-blue-600 text-white px-4 py-2 rounded">
        Tampilkan
    </button>
</form>

@if(request('tanggal_mulai') && request('tanggal_akhir'))

<div class="print-area">

    {{-- ================= HEADER ================= --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold">Laporan Transaksi Periode</h1>
            <p class="text-gray-600">
                {{ $tanggalMulai->translatedFormat('d F Y') }}
                –
                {{ $tanggalAkhir->translatedFormat('d F Y') }}
            </p>
        </div>

        <button onclick="window.print()"
            class="bg-gray-800 text-white px-4 py-2 rounded print:hidden">
            🖨️ Print
        </button>
    </div>

    {{-- ================= RINGKASAN ================= --}}
    <div class="bg-white border rounded mb-6">
        <div class="px-6 py-4 border-b font-semibold">Ringkasan</div>
        <div class="px-6 py-4 grid grid-cols-2 gap-6">
            <div>
                <p>Total Transaksi</p>
                <p class="text-xl font-bold">{{ $totalTransaksi }}</p>
            </div>
            <div>
                <p>Total Pendapatan</p>
                <p class="text-xl font-bold">
                    Rp {{ number_format($totalPendapatan,0,',','.') }}
                </p>
            </div>
        </div>
    </div>

    {{-- ================= BREAKDOWN TIPE KENDARAAN ================= --}}
    <div class="bg-white border rounded mb-6">
        <div class="px-6 py-4 border-b font-semibold">Breakdown Tipe Kendaraan</div>
        <div class="px-6 py-4">
            <div class="grid grid-cols-3 font-semibold border-b pb-2">
                <div>Tipe</div><div>Jumlah</div><div>Pendapatan</div>
            </div>
            @foreach($breakdownTipeKendaraan as $item)
                <div class="grid grid-cols-3 py-2">
                    <div>{{ $item->tipe_kendaraan }}</div>
                    <div>{{ $item->total }}</div>
                    <div>Rp {{ number_format($item->total_pendapatan,0,',','.') }}</div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- ================= BREAKDOWN METODE PEMBAYARAN ================= --}}
    <div class="bg-white border rounded">
        <div class="px-6 py-4 border-b font-semibold">Breakdown Metode Pembayaran</div>
        <div class="px-6 py-4">
            <div class="grid grid-cols-3 font-semibold border-b pb-2">
                <div>Metode</div><div>Jumlah</div><div>Pendapatan</div>
            </div>
            @foreach($breakdownMetodePembayaran as $item)
                <div class="grid grid-cols-3 py-2">
                    <div>{{ $item->nama_metode }}</div>
                    <div>{{ $item->total }}</div>
                    <div>Rp {{ number_format($item->total_pendapatan,0,',','.') }}</div>
                </div>
            @endforeach
        </div>
    </div>

</div>
@endif

{{-- ================= PRINT STYLE ================= --}}
<style>
@media print {
    body * { visibility: hidden; }
    .print-area, .print-area * { visibility: visible; }
    .print-area {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }
}
</style>

@endsection
