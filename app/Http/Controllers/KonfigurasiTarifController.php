<?php

namespace App\Http\Controllers;

use App\Models\KonfigurasiTarif;
use App\Models\TarifDurasi;
use App\Models\TarifTipeKendaraan;
use Illuminate\Http\Request;

class KonfigurasiTarifController extends Controller
{
    public function index()
{
    // Ambil 1 baris konfigurasi (single row config)
    $config = KonfigurasiTarif::first();

    // Jika belum ada, buat default
    if (!$config) {
        $config = KonfigurasiTarif::create([
            'persentase_tarif_perjam_lanjutan' => 100,
            'diskon_persen' => 0,
            'diskon_sampai' => null,
            'diskon_aktif' => false,
        ]);
    }

    $tarifDasar = TarifTipeKendaraan::with('tipeKendaraan')->get();

    $durasiTertinggi = TarifDurasi::max('batas_jam');

    return view('admin.konfigurasi_tarif.index', compact(
        'config',
        'tarifDasar',
        'durasiTertinggi'
    ));
}


    public function update(Request $request)
    {
        $request->validate([
            'persentase_tarif_perjam_lanjutan' => 'required|integer|min:0|max:1000',
            'diskon_persen' => 'nullable|integer|min:0|max:100',
            'diskon_sampai' => 'nullable|date',
        ]);

        $config = KonfigurasiTarif::firstOrFail();

        $config->update([
            'persentase_tarif_perjam_lanjutan' => $request->persentase_tarif_perjam_lanjutan,
            'diskon_persen' => $request->diskon_persen ?? 0,
            'diskon_sampai' => $request->diskon_sampai,
            'diskon_aktif' => $request->has('diskon_aktif'),
        ]);

 return back()->with('success', 'Konfigurasi tarif berhasil diperbarui');
    }
}
