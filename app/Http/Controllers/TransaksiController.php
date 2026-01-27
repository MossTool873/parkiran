<?php

namespace App\Http\Controllers;

use App\Models\Kendaraan;
use App\Models\KendaraanTipe;
use App\Models\TarifTipeKendaraan;
use App\Models\Transaksi;
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

        $kendaraan = Kendaraan::firstOrCreate(
            ['plat_nomor' => $request->plat_nomor],
            [
                'warna' => $request->warna,
                'tipe_kendaraan_id' => $request->tipe_kendaraan_id,
            ]
        );

        $aktif = Transaksi::where('kendaraan_id', $kendaraan->id)
            ->whereNull('waktu_keluar')
            ->exists();

        if ($aktif) {
            return back()->withErrors('Kendaraan masih parkir');
        }

        $tarif = TarifTipeKendaraan::where('tipe_kendaraan_id',$request->tipe_kendaraan_id)->first();

        if (!$tarif) {
            return back()->withErrors('Tarif kendaraan belum tersedia');
        }

        DB::transaction(function () use ($kendaraan, $userId, $tarif) {
            Transaksi::create([
                'kode' => 'xxx',
                'kendaraan_id' => $kendaraan->id,
                'waktu_masuk' => now(),
                'tarif_tipe_kendaraan_id' => $tarif->id,
                'area_parkir_id' => 1,
                'user_id' => $userId,
            ]);
        });

        return redirect('/petugas/transaksi')
            ->with('success', 'Kendaraan berhasil masuk');
    }
}