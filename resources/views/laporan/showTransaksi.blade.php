@extends('components.app')

@section('title', 'Detail Transaksi')

@section('content')
<div class="max-w-md mx-auto bg-white rounded-lg shadow p-4 text-xs">

    <h1 class="text-center text-lg font-bold mb-4">DETAIL TRANSAKSI</h1>

    {{-- Informasi Kendaraan & Area --}}
    <div class="mb-3">
        <div class="grid grid-cols-2 gap-2 py-1">
            <div class="font-semibold">Kode</div>
            <div class="text-right">{{ $transaksi->kode ?? '-' }}</div>
        </div>
        <div class="grid grid-cols-2 gap-2 py-1">
            <div class="font-semibold">Plat</div>
            <div class="text-right">{{ $transaksi->kendaraan?->plat_nomor ?? '-' }}</div>
        </div>
        <div class="grid grid-cols-2 gap-2 py-1">
            <div class="font-semibold">Tipe Kendaraan</div>
            <div class="text-right">{{ $transaksi->kendaraan?->tipeKendaraan?->tipe_kendaraan ?? '-' }}</div>
        </div>
        <div class="grid grid-cols-2 gap-2 py-1">
            <div class="font-semibold">Area</div>
            <div class="text-right">{{ $transaksi->areaParkir?->nama_area ?? '-' }}</div>
        </div>
    </div>

    {{-- Waktu & Durasi --}}
    <div class="mb-3 border-t border-gray-200 pt-2">
        <div class="grid grid-cols-2 gap-2 py-1">
            <div class="font-semibold">Masuk</div>
            <div class="text-right">{{ $transaksi->waktu_masuk ? \Carbon\Carbon::parse($transaksi->waktu_masuk)->format('d/m/Y H:i') : '-' }}</div>
        </div>
        <div class="grid grid-cols-2 gap-2 py-1">
            <div class="font-semibold">Keluar</div>
            <div class="text-right">{{ $transaksi->waktu_keluar ? \Carbon\Carbon::parse($transaksi->waktu_keluar)->format('d/m/Y H:i') : '-' }}</div>
        </div>
        <div class="grid grid-cols-2 gap-2 py-1">
            <div class="font-semibold">Durasi</div>
            <div class="text-right">{{ $transaksi->durasi_menit !== null ? intdiv($transaksi->durasi_menit,60).' jam '.($transaksi->durasi_menit%60).' menit' : '-' }}</div>
        </div>
        <div class="grid grid-cols-2 gap-2 py-1">
            <div class="font-semibold">Member</div>
            <div class="text-right">{{ $transaksi->membership?->nama ?? '-' }}</div>
        </div>
    </div>

    {{-- Biaya & Diskon --}}
    <div class="mb-3 border-t border-gray-200 pt-2">
        @php
            $biayaAwal = $transaksi->biaya ?? 0;
            $diskonMember = $transaksi->membership?->membershipTier?->diskon ?? 0;
            $nominalDiskonMember = ($biayaAwal * $diskonMember / 100);

            $config = \App\Models\KonfigurasiTarif::first();
            $diskonNonMember = $config?->isDiskonBerlaku() ? ($config->diskon_persen/100) * $biayaAwal : 0;

            $totalDiskon = $diskonNonMember + $nominalDiskonMember;
            $biayaTotal = max(0, $biayaAwal - $totalDiskon);
        @endphp

        <div class="grid grid-cols-2 gap-2 py-1">
            <div class="font-semibold">Tarif Awal</div>
            <div class="text-right">Rp {{ number_format($biayaAwal,0,',','.') }}</div>
        </div>

        <div class="grid grid-cols-2 gap-2 py-1">
            <div class="font-semibold">Diskon</div>
            <div class="text-right">
                {{ $config?->diskon_persen ?? 0 }}% 
                (- Rp {{ number_format($diskonNonMember,0,',','.') }})
            </div>
        </div>

        <div class="grid grid-cols-2 gap-2 py-1">
            <div class="font-semibold">Diskon Member</div>
            <div class="text-right">
                {{ $diskonMember }}% 
                (- Rp {{ number_format($nominalDiskonMember,0,',','.') }})
            </div>
        </div>

        <div class="grid grid-cols-2 gap-2 py-1 border-t border-gray-200 pt-1 font-bold">
            <div>Total</div>
            <div class="text-right">Rp {{ number_format($biayaTotal,0,',','.') }}</div>
        </div>
    </div>

    {{-- Metode Pembayaran --}}
    <div class="mb-3 border-t border-gray-200 pt-2">
        <div class="grid grid-cols-2 gap-2 py-1">
            <div class="font-semibold">Metode Bayar</div>
            <div class="text-right">{{ $transaksi->metodePembayaran?->nama_metode ?? '-' }}</div>
        </div>
    </div>

    {{-- Operator --}}
    <div class="mb-3 border-t border-gray-200 pt-2">
        <div class="grid grid-cols-2 gap-2 py-1">
            <div class="font-semibold">Operator</div>
            <div class="text-right">{{ $transaksi->user?->name ?? '-' }}</div>
        </div>
    </div>

    {{-- Tombol Kembali --}}
    <div class="mt-3">
        <a href="{{ url()->previous() }}" class="block text-center bg-gray-300 hover:bg-gray-400 px-4 py-2 rounded-lg">
            Kembali
        </a>
    </div>

</div>
@endsection
