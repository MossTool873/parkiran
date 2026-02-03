<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    public function laporanHariIni()
    {
        $today = Carbon::today();

        $transaksiHariIni = Transaksi::whereDate('waktu_keluar', $today)->where('status', 'keluar');

        $totalTransaksi = (clone $transaksiHariIni)->count();
        $totalPendapatan = (clone $transaksiHariIni)->sum('biaya_total');

        $breakdownTipeKendaraan = Transaksi::select(
                'kendaraan_tipe.tipe_kendaraan',
                DB::raw('COUNT(transaksi.id) as total'),
                DB::raw('SUM(transaksi.biaya_total) as total_pendapatan')
            )
            ->join('kendaraan', 'transaksi.kendaraan_id', '=', 'kendaraan.id')
            ->join('kendaraan_tipe', 'kendaraan.tipe_kendaraan_id', '=', 'kendaraan_tipe.id')
            ->whereDate('transaksi.waktu_keluar', $today)
            ->where('transaksi.status', 'keluar')
            ->groupBy('kendaraan_tipe.tipe_kendaraan')
            ->orderBy('kendaraan_tipe.tipe_kendaraan')
            ->get();

        $breakdownMetodePembayaran = Transaksi::select(
                'metode_pembayaran.nama_metode',
                DB::raw('COUNT(transaksi.id) as total'),
                DB::raw('SUM(transaksi.biaya_total) as total_pendapatan')
            )
            ->join('metode_pembayaran', 'transaksi.metode_pembayaran_id', '=', 'metode_pembayaran.id')
            ->whereDate('transaksi.waktu_keluar', $today)
            ->where('transaksi.status', 'keluar')
            ->groupBy('metode_pembayaran.nama_metode')
            ->orderBy('metode_pembayaran.nama_metode')
            ->get();

        return view('laporan.transaksi-harian', compact(
            'totalTransaksi',
            'totalPendapatan',
            'breakdownTipeKendaraan',
            'breakdownMetodePembayaran'
        ));
    }

    
}
