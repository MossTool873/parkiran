<?php

namespace App\Http\Controllers;

use App\Models\Kendaraan;
use App\Models\KendaraanTipe;
use App\Models\TarifTipeKendaraan;
use App\Models\Transaksi;
use App\Models\AreaParkirDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransaksiController extends Controller
{
    public function index()
    {
        $tipeKendaraans = KendaraanTipe::all();
        return view('petugas.transaksi', compact('tipeKendaraans'));
    }

    public function masuk(Request $request)
    {
        $request->validate([
            'plat_nomor' => 'required',
            'warna' => 'required',
            'tipe_kendaraan_id' => 'required|exists:kendaraan_tipe,id',
        ]);

        $userId = auth()->id();
        if (!$userId) {
            return redirect('/login');
        }

        try {
            DB::transaction(function () use ($request, $userId) {

                $kendaraan = Kendaraan::where('plat_nomor', $request->plat_nomor)->first();

                if ($kendaraan) {
                    if (
                        $kendaraan->warna !== $request->warna ||
                        $kendaraan->tipe_kendaraan_id != $request->tipe_kendaraan_id
                    ) {
                        throw new \Exception('Plat nomor sudah terdaftar dengan data berbeda');
                    }
                } else {
                    $kendaraan = Kendaraan::create([
                        'plat_nomor' => $request->plat_nomor,
                        'warna' => $request->warna,
                        'tipe_kendaraan_id' => $request->tipe_kendaraan_id,
                    ]);
                }

                $aktif = Transaksi::where('kendaraan_id', $kendaraan->id)
                    ->whereNull('waktu_keluar')
                    ->exists();

                if ($aktif) {
                    throw new \Exception('Kendaraan masih parkir');
                }

                $tarif = TarifTipeKendaraan::where(
                    'tipe_kendaraan_id',
                    $request->tipe_kendaraan_id
                )->first();

                if (!$tarif) {
                    throw new \Exception('Tarif kendaraan belum tersedia');
                }

                $areaDetail = AreaParkirDetail::where('tipe_kendaraan_id', $request->tipe_kendaraan_id)
                    ->whereColumn('kapasitas', '>', 'terisi')
                    ->lockForUpdate()
                    ->first();

                if (!$areaDetail) {
                    throw new \Exception('Area parkir penuh');
                }

                Transaksi::create([
                    'kode' => 'TRX-' . time(),
                    'kendaraan_id' => $kendaraan->id,
                    'waktu_masuk' => now(),
                    'tarif_tipe_kendaraan_id' => $tarif->id,
                    'area_parkir_id' => $areaDetail->area_parkir_id,
                    'user_id' => $userId,
                ]);

                $areaDetail->increment('terisi');
            });
        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => $e->getMessage()])
                ->withInput();
        }

        return redirect('/petugas/transaksi')
            ->with('success', 'Kendaraan berhasil masuk');
    }

    public function keluar(Request $request)
    {
        $request->validate([
            'plat_nomor' => 'required',
        ]);

        try {
            DB::transaction(function () use ($request) {

                $kendaraan = Kendaraan::where('plat_nomor', $request->plat_nomor)->first();

                if (!$kendaraan) {
                    throw new \Exception('Kendaraan tidak ditemukan');
                }

                $transaksi = Transaksi::where('kendaraan_id', $kendaraan->id)
                    ->whereNull('waktu_keluar')
                    ->lockForUpdate()
                    ->first();

                if (!$transaksi) {
                    throw new \Exception('Kendaraan tidak sedang parkir');
                }

                $tarif = TarifTipeKendaraan::find($transaksi->tarif_tipe_kendaraan_id);

                if (!$tarif) {
                    throw new \Exception('Tarif tidak ditemukan');
                }

                $waktuMasuk  = Carbon::parse($transaksi->waktu_masuk);
                $waktuKeluar = now();

                $durasiJam = max(1, $waktuMasuk->diffInHours($waktuKeluar));
                $biayaTotal = $durasiJam * $tarif->tarif_perjam;

                $transaksi->update([
                    'waktu_keluar' => $waktuKeluar,
                    'durasi_jam' => $durasiJam,
                    'biaya_total' => $biayaTotal,
                ]);

                $areaDetail = AreaParkirDetail::where('area_parkir_id', $transaksi->area_parkir_id)
                    ->where('tipe_kendaraan_id', $kendaraan->tipe_kendaraan_id)
                    ->lockForUpdate()
                    ->first();

                if ($areaDetail && $areaDetail->terisi > 0) {
                    $areaDetail->decrement('terisi');
                }
            });
        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => $e->getMessage()])
                ->withInput();
        }

        return redirect('/petugas/transaksi')
            ->with('success', 'Kendaraan berhasil keluar');
    }

    public function aktif(Request $request)
    {
        return Transaksi::whereNull('waktu_keluar')
            ->whereHas('kendaraan', function ($q) use ($request) {
                $q->where('plat_nomor', 'like', '%' . $request->q . '%');
            })
            ->with('kendaraan')
            ->get()
            ->map(function ($t) {
                return [
                    'plat_nomor' => $t->kendaraan->plat_nomor,
                    'waktu_masuk' => $t->waktu_masuk,
                ];
            });
    }

    public function riwayat(Request $request)
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

        return view('petugas.riwayatTransaksi', compact('transaksis'));
    }
}