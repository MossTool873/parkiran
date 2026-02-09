<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KendaraanTipe;
use App\Models\AreaParkir;
use App\Models\Kendaraan;
use App\Models\MetodePembayaran;
use App\Models\TarifTipeKendaraan;
use App\Models\TarifDurasi;
use App\Models\KonfigurasiTarif;

class ViewOnlyController extends Controller
{
    public function tipeKendaraan()
    {
        $tipeKendaraans = KendaraanTipe::whereNull('deleted_at')->get();
        return view('view_only.tipeKendaraan', compact('tipeKendaraans'));
    }

    public function areaParkir(Request $request)
    {
        $search = $request->query('search'); 

        $areaParkirs = AreaParkir::with(['detailKapasitas.tipeKendaraan'])
            ->when($search, function ($query, $search) {
                return $query->where('lokasi', 'like', "%{$search}%")
                             ->orWhere('nama_area', 'like', "%{$search}%");
            })
            ->orderBy('id', 'desc')
            ->paginate(6)
            ->withQueryString();

        return view('view_only.areaParkir', compact('areaParkirs', 'search'));
    }

    public function kendaraan(Request $request)
    {
        $search = $request->query('search'); 

        $kendaraans = Kendaraan::with('tipeKendaraan')
            ->when($search, function ($query, $search) {
                return $query->where('plat_nomor', 'like', "%{$search}%");
            })
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->withQueryString(); 

        return view('view_only.kendaraan', compact('kendaraans', 'search'));
    }

    public function metodePembayaran()
    {
        $metodePembayarans = MetodePembayaran::all();
        return view('view_only.metodePembayaran', compact('metodePembayarans'));
    }

    public function tarifTipeKendaraan()
    {
        $tarifTipeKendaraans = TarifTipeKendaraan::with('tipeKendaraan')->get();
        return view('view_only.tarifTipeKendaraan', compact('tarifTipeKendaraans'));
    }

    public function tarifDurasi()
    {
        $tarifDurasi = TarifDurasi::orderBy('batas_jam')->paginate(10);
        $tarifDasar = TarifTipeKendaraan::with('tipeKendaraan')->get();

        return view('view_only.tarifDurasi', compact('tarifDurasi', 'tarifDasar'));
    }

    public function konfigurasiTarif()
    {
        $config = KonfigurasiTarif::first();

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

        return view('view_only.konfigurasiTarif', compact(
            'config',
            'tarifDasar',
            'durasiTertinggi'
        ));
    }
}
