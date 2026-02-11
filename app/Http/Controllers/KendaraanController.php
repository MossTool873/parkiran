<?php

namespace App\Http\Controllers;

use App\Models\AreaParkir;
use App\Models\Kendaraan;
use App\Models\KendaraanTipe;
use App\Models\Transaksi;
use Illuminate\Http\Request;

class KendaraanController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search'); 

        $kendaraans = Kendaraan::with('tipeKendaraan')
            ->when($search, function ($query, $search) {
                return $query->where('plat_nomor', 'like', "%{$search}%");
            })
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->withQueryString(); 

        return view('admin.kendaraan.index', compact('kendaraans', 'search'));
    }

    public function create()
    {
        $tipeKendaraans = KendaraanTipe::all();
        return view('admin.kendaraan.create', compact('tipeKendaraans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'plat_nomor' => 'required|unique:kendaraan,plat_nomor,',
            'warna' => 'required',
            'tipe_kendaraan_id' => 'required',
        ]);

        $kendaraan = Kendaraan::create([
            'plat_nomor' => $request->plat_nomor,
            'warna' => $request->warna,
            'tipe_kendaraan_id' => $request->tipe_kendaraan_id,
        ]);

        logAktivitas(
            'Create Kendaraan: ' . $kendaraan->plat_nomor,
            [
                'new' => [
                    'plat_nomor' => $kendaraan->plat_nomor,
                    'warna' => $kendaraan->warna,
                    'tipe_kendaraan_id' => $kendaraan->tipe_kendaraan_id,
                ],
                'aksi' => 'create'
            ]
        );

        return redirect('/admin/kendaraan')->with('success', 'Kendaraan berhasil ditambahkan');
    }

    public function edit($id)
    {
        $kendaraan = Kendaraan::findOrFail($id);
        $tipeKendaraans = KendaraanTipe::all();
        return view('admin.kendaraan.edit', compact('kendaraan', 'tipeKendaraans'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'plat_nomor' => 'required',
            'warna' => 'required',
            'tipe_kendaraan_id' => 'required',
        ]);

        $kendaraan = Kendaraan::findOrFail($id);

        $oldData = [
            'plat_nomor' => $kendaraan->plat_nomor,
            'warna' => $kendaraan->warna,
            'tipe_kendaraan_id' => $kendaraan->tipe_kendaraan_id,
        ];

        $kendaraan->update([
            'plat_nomor' => $request->plat_nomor,
            'warna' => $request->warna,
            'tipe_kendaraan_id' => $request->tipe_kendaraan_id,
        ]);

        logAktivitas(
            'Update Kendaraan: ' . $kendaraan->plat_nomor,
            [
                'old' => $oldData,
                'new' => [
                    'plat_nomor' => $request->plat_nomor,
                    'warna' => $request->warna,
                    'tipe_kendaraan_id' => $request->tipe_kendaraan_id,
                ],
                'aksi' => 'update'
            ]
        );

        return redirect('/admin/kendaraan')->with('success', 'Kendaraan berhasil diperbarui');
    }

    public function destroy($id)
    {
        $kendaraan = Kendaraan::findOrFail($id);

        $oldData = [
            'plat_nomor' => $kendaraan->plat_nomor,
            'warna' => $kendaraan->warna,
            'tipe_kendaraan_id' => $kendaraan->tipe_kendaraan_id,
        ];

        $kendaraan->delete();

        logAktivitas(
            'Delete Kendaraan: ' . $oldData['plat_nomor'],
            [
                'old' => $oldData,
                'aksi' => 'delete'
            ]
        );

        return redirect('/admin/kendaraan')->with('success', 'Kendaraan berhasil dihapus');
    }

    public function search(Request $request)
    {
        $q = $request->query('q');

        $data = Kendaraan::with('tipeKendaraan')
            ->where('plat_nomor', 'like', "%$q%")
            ->limit(10)
            ->get()
            ->map(function ($k) {
                return [
                    'id' => $k->id,
                    'plat_nomor' => $k->plat_nomor,
                    'warna' => $k->warna,
                    'tipe_kendaraan_id' => $k->tipe_kendaraan_id,
                    'tipe_kendaraan_nama' => $k->tipeKendaraan->nama ?? '',
                ];
            });
        return response()->json($data);
    }

    public function tracking(Request $request)
    {
        $search = $request->search;

        $transaksis = Transaksi::with([
            'kendaraan.tipeKendaraan',
            'kendaraan.membershipAktif.membership',
            'areaParkir'
        ])
        ->whereNull('waktu_keluar')
        ->when($search, function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('kendaraan', function ($q2) use ($search) {
                    $q2->where('plat_nomor', 'like', '%' . $search . '%');
                })
                ->orWhereHas('kendaraan.membershipAktif.membership', function ($q3) use ($search) {
                    $q3->where('nama', 'like', '%' . $search . '%');
                });
            });
        })
        ->orderBy('waktu_masuk', 'desc')
        ->paginate(10)
        ->withQueryString();

        return view('laporan.trackingKendaraan', compact('transaksis', 'search'));
    }
}
