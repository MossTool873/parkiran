<?php

namespace App\Http\Controllers;

use App\Models\KendaraanTipe;
use Illuminate\Http\Request;

class KendaraanTipeController extends Controller
{
    public function index()
    {
        $tipeKendaraans = KendaraanTipe::whereNull('deleted_at')->get();
        return view('admin.tipeKendaraan.index', compact('tipeKendaraans'));
    }

    public function create()
    {
        return view('admin.tipeKendaraan.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'tipe_kendaraan' => 'required',
        ]);

        $tipe = KendaraanTipe::create([
            'tipe_kendaraan' => $request->tipe_kendaraan
        ]);

        logAktivitas(
            'Create Kendaraan Tipe: ' . $tipe->tipe_kendaraan,
            [
                'new' => [
                    'tipe_kendaraan' => $tipe->tipe_kendaraan
                ],
                'aksi' => 'create'
            ]
        );

        return redirect('/admin/tipeKendaraan');
    }

    public function edit($id)
    {
        $tipeKendaraan = KendaraanTipe::findOrFail($id);
        return view('admin.tipeKendaraan.edit', compact('tipeKendaraan'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tipe_kendaraan' => 'required',
        ]);

        $tipe = KendaraanTipe::findOrFail($id);

        $oldData = [
            'tipe_kendaraan' => $tipe->tipe_kendaraan
        ];

        $tipe->update([
            'tipe_kendaraan' => $request->tipe_kendaraan
        ]);

        logAktivitas(
            'Update Kendaraan Tipe: ' . $tipe->tipe_kendaraan,
            [
                'old' => $oldData,
                'new' => [
                    'tipe_kendaraan' => $request->tipe_kendaraan
                ],
                'aksi' => 'update'
            ]
        );

        return redirect('/admin/tipeKendaraan');
    }

    public function destroy($id)
    {
        $tipe = KendaraanTipe::findOrFail($id);

        if ($tipe->tarifTipeKendaraans()->exists()) {
            return back()->with('error', 'Tipe kendaraan masih digunakan!!');
        }

        $oldData = [
            'tipe_kendaraan' => $tipe->tipe_kendaraan
        ];

        $tipe->delete();

        logAktivitas(
            'Delete Kendaraan Tipe: ' . $oldData['tipe_kendaraan'],
            [
                'old' => $oldData,
                'aksi' => 'delete'
            ]
        );

        return back()->with('success', 'Tipe kendaraan berhasil dihapus');
    }
}
