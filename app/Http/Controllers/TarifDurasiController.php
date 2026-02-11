<?php

namespace App\Http\Controllers;

use App\Models\TarifDurasi;
use App\Models\TarifTipeKendaraan;
use Illuminate\Http\Request;

class TarifDurasiController extends Controller
{
    public function index()
    {
        $tarifDurasi = TarifDurasi::orderBy('batas_jam')->paginate(10);
        $tarifDasar = TarifTipeKendaraan::with('tipeKendaraan')->get();

        return view('admin.tarif_durasi.index', compact('tarifDurasi', 'tarifDasar'));
    }

    public function create()
    {
        $tarifDasar = TarifTipeKendaraan::with('tipeKendaraan')->get();
        return view('admin.tarif_durasi.create', compact('tarifDasar'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'batas_jam' => 'required|integer|min:1|unique:tarif_durasi,batas_jam,NULL,id,deleted_at,NULL',
            'persentase' => 'required|integer|min:1',
        ]);

        $tarif = TarifDurasi::create($request->all());

        logAktivitas(
            'Create Tarif Durasi: ' . $tarif->batas_jam . ' jam',
            [
                'new' => [
                    'batas_jam' => $tarif->batas_jam,
                    'persentase' => $tarif->persentase,
                ],
                'aksi' => 'create'
            ]
        );

        return redirect(url('/admin/tarif-durasi'))
            ->with('success', 'Tarif durasi berhasil ditambahkan');
    }

    public function edit(TarifDurasi $tarif_durasi)
    {
        $tarifDasar = TarifTipeKendaraan::with('tipeKendaraan')->get();
        return view('admin.tarif_durasi.edit', compact('tarif_durasi', 'tarifDasar'));
    }

    public function update(Request $request, TarifDurasi $tarif_durasi)
    {
        $request->validate([
            'batas_jam'  => 'required|integer|min:1|unique:tarif_durasi,batas_jam,' . $tarif_durasi->id,
            'persentase' => 'required|integer|min:1',
        ]);

        $oldData = [
            'batas_jam' => $tarif_durasi->batas_jam,
            'persentase' => $tarif_durasi->persentase,
        ];

        $tarif_durasi->update($request->all());

        logAktivitas(
            'Update Tarif Durasi: ' . $tarif_durasi->batas_jam . ' jam',
            [
                'old' => $oldData,
                'new' => [
                    'batas_jam' => $tarif_durasi->batas_jam,
                    'persentase' => $tarif_durasi->persentase,
                ],
                'aksi' => 'update'
            ]
        );

        return redirect(url('/admin/tarif-durasi'))
            ->with('success', 'Tarif durasi berhasil diperbarui');
    }

    public function destroy(TarifDurasi $tarif_durasi)
    {
        $oldData = [
            'batas_jam' => $tarif_durasi->batas_jam,
            'persentase' => $tarif_durasi->persentase,
        ];

        $tarif_durasi->delete();

        logAktivitas(
            'Delete Tarif Durasi: ' . $oldData['batas_jam'] . ' jam',
            [
                'old' => $oldData,
                'aksi' => 'delete'
            ]
        );

        return redirect(url('/admin/tarif-durasi'))
            ->with('success', 'Tarif durasi berhasil dihapus');
    }
}
