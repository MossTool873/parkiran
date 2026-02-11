<?php

namespace App\Http\Controllers;

use App\Models\AreaParkir;
use App\Models\Kendaraan;
use App\Models\KendaraanTipe;
use App\Models\TarifTipeKendaraan;
use App\Models\Transaksi;
use App\Models\AreaParkirDetail;
use App\Models\KonfigurasiTarif;
use App\Models\Membership;
use App\Models\MembershipKendaraan;
use App\Models\MetodePembayaran;
use App\Models\TarifDurasi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransaksiController extends Controller
{
    public function index()
    {
        $metodePembayarans = MetodePembayaran::all();
        $tipeKendaraans = KendaraanTipe::all();
        $areaParkirs = AreaParkir::all();
        return view('petugas.transaksi', compact('tipeKendaraans', 'metodePembayarans', 'areaParkirs'));
    }

    public function masuk(Request $request)
    {
        $request->validate([
            'plat_nomor' => 'required',
            'warna' => 'required',
            'tipe_kendaraan_id' => 'required|exists:kendaraan_tipe,id',
            'area_parkir_id' => 'required|exists:area_parkir,id', // tambah validasi dropdown
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

                $areaDetail = AreaParkirDetail::where('area_parkir_id', $request->area_parkir_id)
                    ->where('tipe_kendaraan_id', $request->tipe_kendaraan_id)
                    ->lockForUpdate()
                    ->first();

                if (!$areaDetail) {
                    throw new \Exception('Area parkir tidak tersedia untuk tipe kendaraan ini');
                }

                if ($areaDetail->terisi >= $areaDetail->kapasitas) {
                    throw new \Exception('Area parkir penuh');
                }


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
                $kendaraan->update([
                    'area_parkir_id' => $areaDetail->area_parkir_id
                ]);


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

        logAktivitas('Transaksi Masuk', [
            'kode_transaksi' => $transaksi->kode,
            'kendaraan_id'   => $kendaraan->id,
            'plat_nomor'     => $kendaraan->plat_nomor,
            'area_parkir_id' => $areaDetail->area_parkir_id,
            'waktu_masuk'    => $transaksi->waktu_masuk->toDateTimeString(),
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
        ]);

        try {
            $input = $request->plat_nomor;

            $transaksi = Transaksi::whereNull('waktu_keluar')
                ->where(function ($q) use ($input) {
                    $q->where('kode', $input)
                        ->orWhereHas('kendaraan', fn($q2) => $q2->where('plat_nomor', $input));
                })
                ->first();

            if (!$transaksi) throw new \Exception('Transaksi aktif tidak ditemukan');

            $kendaraan = $transaksi->kendaraan;

            $tarif = TarifTipeKendaraan::find($transaksi->tarif_tipe_kendaraan_id);
            if (!$tarif) throw new \Exception('Tarif tidak ditemukan');

            $waktuMasuk = Carbon::parse($transaksi->waktu_masuk);
            $waktuKeluar = now();

            $durasiMenit = $waktuMasuk->diffInMinutes($waktuKeluar);
            $durasiJam = max(1, ceil($durasiMenit / 60));

            // ================= TARIF =================
            $tarifDurasiList = TarifDurasi::orderBy('batas_jam')->get();
            $config = KonfigurasiTarif::first();

            $biayaAwal = 0;

            // cari tarif durasi tertinggi yang <= durasiJam
            $tarifDurasiTinggi = $tarifDurasiList->filter(fn($td) => $td->batas_jam <= $durasiJam)->last();

            if ($tarifDurasiTinggi) {
                // gunakan tarif durasi untuk batas jam tertinggi
                $biayaAwal += $tarif->tarif_perjam * ($tarifDurasiTinggi->persentase / 100);
                $jamSisa = $durasiJam - $tarifDurasiTinggi->batas_jam;
            } else {
                // kalau durasi lebih kecil dari batas tarif durasi pertama
                $tarifPertama = $tarifDurasiList->first();
                $biayaAwal += $tarif->tarif_perjam * ($tarifPertama->persentase / 100) * $durasiJam;
                $jamSisa = 0;
            }

            // hitung durasi sisa dengan tarif lanjutan
            if ($jamSisa > 0) {
                $persenLanjutan = $config->persentase_tarif_perjam_lanjutan;
                $biayaAwal += $tarif->tarif_perjam * ($persenLanjutan / 100) * $jamSisa;
            }

            // ================= DISKON =================
            $member = $kendaraan->membershipKendaraan?->membership;

            // Diskon non-member tetap berlaku untuk semua
            $diskonNonMemberPersen = $config->isDiskonBerlaku() ? $config->diskon_persen : 0;
            $diskonNonMemberNominal = ($biayaAwal * $diskonNonMemberPersen) / 100;

            // Diskon member tambahan jika ada
            $diskonMemberPersen = $member?->membershipTier?->diskon ?? 0;
            $diskonMemberNominal = ($biayaAwal * $diskonMemberPersen) / 100;

            // Total diskon
            $totalDiskon = $diskonNonMemberNominal + $diskonMemberNominal;

            // ================= TOTAL AKHIR =================
            $totalAkhir = $biayaAwal - $totalDiskon;

            // ================= SESSION =================
            session()->put('draft_keluar', [
                'transaksi_id' => $transaksi->id,
                'area_parkir_id' => $transaksi->area_parkir_id,
                'tipe_kendaraan_id' => $kendaraan->tipe_kendaraan_id,
                'kendaraan_id' => $kendaraan->id,
                'kode' => $transaksi->kode,
                'plat' => $kendaraan->plat_nomor,
                'jam_masuk' => $waktuMasuk->format('H:i'),
                'jam_keluar' => $waktuKeluar->format('H:i'),
                'durasi_menit' => $durasiMenit,
                'durasi' => floor($durasiMenit / 60) . ' jam ' . ($durasiMenit % 60) . ' menit',
                'tarif_perjam' => $tarif->tarif_perjam,
                'biaya_awal' => $biayaAwal,                   
                'diskon_non_member_persen' => $diskonNonMemberPersen,
                'diskon_non_member' => $diskonNonMemberNominal,
                'diskon_member_persen' => $diskonMemberPersen,
                'diskon_member' => $diskonMemberNominal,
                'diskon_total' => $totalDiskon,
                'total' => $totalAkhir,                    
                'membership_id' => $member?->id,
                'member' => $member?->nama ?? '-',
                'tanggal' => $waktuKeluar->format('d-m-Y'),
                'operator' => auth()->user()->name ?? '-',
            ]);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }

        return back();
    }

    public function transaksiAktif(Request $request)
    {
        $q = $request->query('q');
        $transaksis = Transaksi::whereNull('waktu_keluar')
            ->whereHas('kendaraan', fn($q2) => $q2->where('plat_nomor', 'like', "%$q%"))
            ->orWhere('kode', 'like', "%$q%")
            ->limit(10)
            ->get(['kode', 'kendaraan_id']);

        return response()->json($transaksis->map(fn($t) => [
            'kode' => $t->kode,
            'plat_nomor' => $t->kendaraan->plat_nomor ?? ''
        ]));
    }

    public function bayar(Request $request)
    {
        $request->validate(['metode_pembayaran_id' => 'required']);

        $s = session('draft_keluar');
        if (!$s) return back()->withErrors('Data pembayaran tidak ditemukan');

        DB::transaction(function () use ($request, $s) {
            $transaksi = Transaksi::lockForUpdate()->findOrFail($s['transaksi_id']);
            $metode = MetodePembayaran::findOrFail($request->metode_pembayaran_id);

            $transaksi->update([
                'waktu_keluar' => now(),
                'durasi_menit' => $s['durasi_menit'],
                'biaya' => $s['biaya_awal'],
                'biaya_total' => $s['total'],
                'membership_id' => $s['membership_id'],
                'metode_pembayaran_id' => $metode->id,
                'status' => 'keluar',
            ]);

            $areaDetail = AreaParkirDetail::where('area_parkir_id', $s['area_parkir_id'])
                ->where('tipe_kendaraan_id', $s['tipe_kendaraan_id'])
                ->lockForUpdate()
                ->first();

            if ($areaDetail && $areaDetail->terisi > 0) {
                $areaDetail->decrement('terisi');
            }

            Kendaraan::where('id', $s['kendaraan_id'])->update(['area_parkir_id' => null]);

            session()->put('struk_keluar', array_merge($s, [
                'metode_id' => $metode->id,
                'metode' => $metode->nama_metode,
            ]));

logAktivitas('Transaksi Keluar', [
    'kode_transaksi' => $transaksi->kode,
    'kendaraan_id'   => $transaksi->kendaraan_id,
    'plat_nomor'     => $transaksi->kendaraan->plat_nomor ?? null,
    'area_parkir_id' => $transaksi->area_parkir_id,
    'durasi_menit'   => $transaksi->durasi_menit,
    'biaya_total'    => $transaksi->biaya_total,
    'metode_pembayaran_id' => $transaksi->metode_pembayaran_id,
    'waktu_keluar'   => $transaksi->waktu_keluar->toDateTimeString(),
]);

        });

        session()->forget('draft_keluar');

        return back()->with('success', 'Pembayaran berhasil');
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

        return view('petugas.keluar', compact('kendaraan', 'transaksi', 'metodePembayarans'));
    }

    public function showTransaksi($id)
    {
        $transaksi = Transaksi::with(['kendaraan.tipeKendaraan', 'areaParkir'])->findOrFail($id);

        return view('laporan.showTransaksi', compact('transaksi'));
    }
}
