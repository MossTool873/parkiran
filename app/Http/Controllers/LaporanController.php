<?php

namespace App\Http\Controllers;

use App\Models\AreaParkir;
use App\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\Http\Request;
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

        return view('laporan.laporan-harian', compact(
            'totalTransaksi',
            'totalPendapatan',
            'breakdownTipeKendaraan',
            'breakdownMetodePembayaran'
        ));
    }

    public function laporanPeriode(Request $request)
    {
        if (!$request->filled(['tanggal_awal', 'tanggal_akhir'])) {
            return view('laporan.laporan-periode', [
                'tanggalAwal' => null,
                'tanggalAkhir' => null,
                'totalTransaksi' => 0,
                'totalPendapatan' => 0,
                'breakdownTipeKendaraan' => collect(),
                'breakdownMetodePembayaran' => collect(),
            ]);
        }

        $request->validate([
            'tanggal_awal'  => 'required|date',
            'tanggal_akhir' => 'required|date|after_or_equal:tanggal_awal',
        ]);

        $tanggalAwal  = Carbon::parse($request->tanggal_awal)->startOfDay();
        $tanggalAkhir = Carbon::parse($request->tanggal_akhir)->endOfDay();

        $transaksiPeriode = Transaksi::whereBetween('waktu_keluar', [$tanggalAwal, $tanggalAkhir])
            ->where('status', 'keluar');

        $totalTransaksi  = (clone $transaksiPeriode)->count();
        $totalPendapatan = (clone $transaksiPeriode)->sum('biaya_total');

        $breakdownTipeKendaraan = Transaksi::select(
            'kendaraan_tipe.tipe_kendaraan',
            DB::raw('COUNT(transaksi.id) as total'),
            DB::raw('SUM(transaksi.biaya_total) as total_pendapatan')
        )
            ->join('kendaraan', 'transaksi.kendaraan_id', '=', 'kendaraan.id')
            ->join('kendaraan_tipe', 'kendaraan.tipe_kendaraan_id', '=', 'kendaraan_tipe.id')
            ->whereBetween('transaksi.waktu_keluar', [$tanggalAwal, $tanggalAkhir])
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
            ->whereBetween('transaksi.waktu_keluar', [$tanggalAwal, $tanggalAkhir])
            ->where('transaksi.status', 'keluar')
            ->groupBy('metode_pembayaran.nama_metode')
            ->orderBy('metode_pembayaran.nama_metode')
            ->get();

        return view('laporan.laporan-periode', compact(
            'tanggalAwal',
            'tanggalAkhir',
            'totalTransaksi',
            'totalPendapatan',
            'breakdownTipeKendaraan',
            'breakdownMetodePembayaran'
        ));
    }

    public function occupancy(Request $request)
    {
        $query = AreaParkir::with('detailKapasitas.tipeKendaraan');

        if ($request->has('search') && $request->search != '') {
            $query->where('nama_area', 'like', '%' . $request->search . '%');
        }

        $areaParkirs = $query->paginate(6);

        return view('laporan.occupancy', compact('areaParkirs'));
    }

    public function riwayatTransaksi(Request $request)
    {
        $query = Transaksi::with([
            'kendaraan.tipeKendaraan',
            'areaParkir'
        ]);

        if ($request->filled('plat_nomor')) {
            $query->whereHas('kendaraan', function ($q) use ($request) {
                $q->where('plat_nomor', 'like', '%' . $request->plat_nomor . '%');
            });
        }

        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('waktu_masuk', '>=', $request->tanggal_mulai);
        }

        if ($request->filled('tanggal_selesai')) {
            $query->whereDate('waktu_masuk', '<=', $request->tanggal_selesai);
        }

        $transaksis = $query
            ->orderBy('waktu_masuk', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('laporan.riwayatTransaksi', compact('transaksis'));
    }
}
