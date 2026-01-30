<?php

namespace App\Http\Controllers;

use App\Models\KendaraanTipe;
use App\Models\TarifTipeKendaraan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TarifTipeKendaraanController extends Controller
{
    public function index()
    {
        $tarifTipeKendaraans = TarifTipeKendaraan::with('tipeKendaraan')->get();

        return view(
            'admin.tarifTipeKendaraan.index',
            compact('tarifTipeKendaraans')
        );
    }

    public function create()
    {
        $tipeKendaraans = KendaraanTipe::all();

        $tipeTerpakai = TarifTipeKendaraan::pluck('tipe_kendaraan_id')->toArray();

        return view(
            'admin.tarifTipeKendaraan.create',
            compact('tipeKendaraans', 'tipeTerpakai')
        );
    }

    public function store(Request $request)
    {
        $request->validate(
            [
                'tipe_kendaraan_id' => [
                    'required',
                    'exists:kendaraan_tipe,id',
                    Rule::unique('tarif_tipe_kendaraan', 'tipe_kendaraan_id')
                        ->whereNull('deleted_at')
                ],
                'tarif_perjam' => 'required|numeric|min:0'
            ],
            [
                'tipe_kendaraan_id.unique' =>
                    'Tipe kendaraan ini sudah memiliki tarif.'
            ]
        );

        TarifTipeKendaraan::create([
            'tipe_kendaraan_id' => $request->tipe_kendaraan_id,
            'tarif_perjam'      => $request->tarif_perjam
        ]);

        return redirect('/admin/tarifTipeKendaraan')
            ->with('success', 'Tarif berhasil ditambahkan');
    }

    public function edit($id)
    {
        $tarif = TarifTipeKendaraan::findOrFail($id);
        $tipeKendaraans = KendaraanTipe::all();

        return view(
            'admin.tarifTipeKendaraan.edit',
            compact('tarif', 'tipeKendaraans') 
            
        );
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tarif_perjam' => 'required|numeric|min:0'
        ]);

        $tarif = TarifTipeKendaraan::findOrFail($id);

        $tarif->update([
            'tarif_perjam' => $request->tarif_perjam
        ]);

        return redirect('/admin/tarifTipeKendaraan')
            ->with('success', 'Tarif berhasil diperbarui');
    }

    public function destroy($id)
    {
        $tarif = TarifTipeKendaraan::findOrFail($id);
        $tarif->delete();

        return redirect('/admin/tarifTipeKendaraan')
            ->with('success', 'Tarif berhasil dihapus');
    }
}