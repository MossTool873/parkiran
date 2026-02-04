<?php

namespace App\Http\Controllers;

use App\Models\Kendaraan;
use App\Models\KendaraanTipe;
use App\Models\TarifTipeKendaraan;
use App\Models\Transaksi;
use App\Models\AreaParkirDetail;
use App\Models\Membership;
use App\Models\MembershipKendaraan;
use App\Models\MetodePembayaran;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransaksiController extends Controller
{
    public function index()
    {
        $metodePembayarans = MetodePembayaran::all();
        $tipeKendaraans = KendaraanTipe::all();
        return view('petugas.transaksi', compact('tipeKendaraans', 'metodePembayarans'));
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

                // ===============================
                // GENERATE KODE: TRX-YYYYMMDD-XXXX
                // ===============================
                $tanggal = now()->format('Ymd');

                $lastTransaksi = Transaksi::whereDate('waktu_masuk', now()->toDateString())
                    ->orderBy('id', 'desc')
                    ->lockForUpdate()
                    ->first();

                $nomorUrut = 1;
                if ($lastTransaksi && preg_match('/TRX-\d{8}-(\d+)/', $lastTransaksi->kode, $match)) {
                    $nomorUrut = intval($match[1]) + 1;
                }

                $kodeTransaksi = 'TRX-' . $tanggal . '-' . str_pad($nomorUrut, 4, '0', STR_PAD_LEFT);
                // ===============================

                $transaksi = Transaksi::create([
                    'kode' => $kodeTransaksi,
                    'kendaraan_id' => $kendaraan->id,
                    'waktu_masuk' => now(),
                    'tarif_tipe_kendaraan_id' => $tarif->id,
                    'area_parkir_id' => $areaDetail->area_parkir_id,
                    'user_id' => $userId,
                ]);

                $areaDetail->increment('terisi');

                session()->flash('struk_masuk', [
                    'kode'   => $transaksi->kode,
                    'area'   => $areaDetail->areaParkir->nama_area ?? 'Area',
                    'waktu'  => $transaksi->waktu_masuk->format('H:i'),
                    'plat'   => $kendaraan->plat_nomor,
                    'tipe'   => $kendaraan->tipeKendaraan->tipe_kendaraan ?? '-',
                ]);
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
            'metode_pembayaran_id' => 'required',
        ]);

        try {
            DB::transaction(function () use ($request) {

                // ===================== KENDARAAN =====================
                $kendaraan = Kendaraan::where('plat_nomor', $request->plat_nomor)->first();

                if (!$kendaraan) {
                    throw new \Exception('Kendaraan tidak ditemukan');
                }

                // ===================== TRANSAKSI AKTIF =====================
                $transaksi = Transaksi::where('kendaraan_id', $kendaraan->id)
                    ->whereNull('waktu_keluar')
                    ->lockForUpdate()
                    ->first();

                if (!$transaksi) {
                    throw new \Exception('Kendaraan tidak sedang parkir');
                }

                // ===================== TARIF =====================
                $tarif = TarifTipeKendaraan::find($transaksi->tarif_tipe_kendaraan_id);

                if (!$tarif) {
                    throw new \Exception('Tarif tidak ditemukan');
                }

                // ===================== WAKTU & BIAYA =====================
                $waktuMasuk  = Carbon::parse($transaksi->waktu_masuk);
                $waktuKeluar = now();

                $durasiJamTarif = max(1, $waktuMasuk->diffInHours($waktuKeluar));
                $durasiJam =  $waktuMasuk->diffInHours($waktuKeluar);
                $durasiMenit = $waktuMasuk->diffInMinutes($waktuKeluar);
                $biayaAwal = $durasiJamTarif * $tarif->tarif_perjam;

                // ===================== CEK MEMBER =====================
                $member         = null;
                $diskonPersen   = 0;
                $diskonNominal  = 0;

                if (
                    $kendaraan->membershipKendaraan &&
                    $kendaraan->membershipKendaraan->membership &&
                    $kendaraan->membershipKendaraan->membership->membershipTier
                ) {
                    $member = $kendaraan->membershipKendaraan->membership;

                    $diskonPersen  = $member->membershipTier->diskon;
                    $diskonNominal = ($biayaAwal * $diskonPersen) / 100;
                }

                // ===================== METODE PEMBAYARAN =====================
                $metodePembayaran = MetodePembayaran::find($request->metode_pembayaran_id);

                if (!$metodePembayaran) {
                    throw new \Exception('Metode pembayaran tidak ditemukan');
                }

                // ===================== UPDATE TRANSAKSI =====================
                $transaksi->update([
                    'waktu_keluar'           => $waktuKeluar,
                    'durasi_menit'           => $durasiMenit,
                    'biaya'                  => $biayaAwal, 
                    'status'                 => 'keluar',
                    'biaya_total'            => $biayaAwal - $diskonNominal, 
                    'membership_id'              => $member ? $member->id : null,
                    'metode_pembayaran_id'   => $metodePembayaran->id,
                ]);

                // ===================== UPDATE AREA PARKIR =====================
                $areaDetail = AreaParkirDetail::where('area_parkir_id', $transaksi->area_parkir_id)
                    ->where('tipe_kendaraan_id', $kendaraan->tipe_kendaraan_id)
                    ->lockForUpdate()
                    ->first();

                if ($areaDetail && $areaDetail->terisi > 0) {
                    $areaDetail->decrement('terisi');
                }

                // ===================== STRUK KELUAR =====================
                session()->flash('struk_keluar', [
                    'kode'          => $transaksi->kode,
                    'plat'          => $kendaraan->plat_nomor,
                    'jam_masuk'     => $waktuMasuk->format('H:i'),
                    'jam_keluar'    => $waktuKeluar->format('H:i'),
                    'durasi'        => $durasiJam . ' jam '.+$durasiMenit.'menit',
                    'tarif'         => $tarif->tarif_perjam,
                    'metode'        => $metodePembayaran->nama_metode,
                    'biaya_awal'    => $biayaAwal,
                    'diskon_persen' => $diskonPersen,
                    'diskon'        => $diskonNominal,
                    'total'         => $biayaAwal - $diskonNominal,
                    'tanggal'       => $waktuKeluar->format('d-m-Y'),
                    'operator'      => auth()->user()->name ?? '-',
                ]);
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

    


    public function formKeluar(Request $request)
    {
        $request->validate([
            'plat_nomor' => 'required',
        ]);

        $kendaraan = Kendaraan::where('plat_nomor', $request->plat_nomor)->first();
        if (!$kendaraan) {
            return back()->withErrors(['error' => 'Kendaraan tidak ditemukan']);
        }

        $transaksi = Transaksi::where('kendaraan_id', $kendaraan->id)
            ->whereNull('waktu_keluar')
            ->first();

        if (!$transaksi) {
            return back()->withErrors(['error' => 'Kendaraan tidak sedang parkir']);
        }

        $metodePembayarans = MetodePembayaran::all();

        return view('petugas.keluar', compact(
            'kendaraan',
            'transaksi',
            'metodePembayarans'
        ));
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
