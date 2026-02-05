<?php

namespace App\Http\Controllers;

use App\Models\AreaParkir;
use App\Models\AreaParkirDetail;
use App\Models\KendaraanTipe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AreaParkirController extends Controller
{
public function index()
{
    $areaParkirs = AreaParkir::with(['detailKapasitas.tipeKendaraan'])
        ->orderBy('id', 'desc')
        ->paginate(10); 

    return view('admin.areaParkir.index', compact('areaParkirs'));
}


    public function create()
    {
        $tipeKendaraans = KendaraanTipe::all();
        return view('admin.areaParkir.create', compact('tipeKendaraans'));
    }

public function store(Request $request)
{
    $request->validate([
        'nama_area' => 'required|string|max:255',
        'kapasitas' => 'required|array',
        'kapasitas.*' => 'nullable|integer|min:0'
    ]);

    $total_kapasitas = array_sum($request->kapasitas);

    if ($total_kapasitas <= 0) {
        return back()
            ->withErrors(['eror' => 'Total kapasitas harus lebih dari 0'])
            ->withInput();
    }

    DB::transaction(function () use ($request, $total_kapasitas) {

        $area = AreaParkir::create([
            'nama_area'       => $request->nama_area,
            'total_kapasitas' => $total_kapasitas
        ]);

        foreach ($request->kapasitas as $tipeId => $jumlah) {
            if ($jumlah > 0) {
                AreaParkirDetail::create([
                    'area_parkir_id'     => $area->id,
                    'tipe_kendaraan_id' => $tipeId,
                    'kapasitas'         => $jumlah
                ]);
            }
        }
    });

    return redirect('/admin/areaParkir')
        ->with('success', 'Area parkir berhasil ditambahkan');
}


public function edit($id)
{
    $areaParkir = AreaParkir::with('detailKapasitas')->findOrFail($id);
    $tipeKendaraans = KendaraanTipe::all();

    return view('admin.areaParkir.edit', compact('areaParkir', 'tipeKendaraans'));
}

public function update(Request $request, $id)
{
    $request->validate([
        'nama_area' => 'required',
        'kapasitas' => 'required|array'
    ]);

    DB::transaction(function () use ($request, $id) {
        $total_kapasitas = array_sum($request->kapasitas);

        AreaParkir::where('id', $id)->update([
            'nama_area' => $request->nama_area,
            'total_kapasitas' => $total_kapasitas
        ]);

        foreach ($request->kapasitas as $tipeId => $jumlah) {
            AreaParkirDetail::updateOrCreate(
                [
                    'area_parkir_id' => $id,
                    'tipe_kendaraan_id' => $tipeId
                ],
                [
                    'kapasitas' => $jumlah
                ]
            );
        }
    });

    return redirect('/admin/areaParkir');
}

    public function destroy($id)
    {
        $areaParkir = AreaParkir::findOrFail($id);

        $areaParkir->delete();

        return redirect('/admin/areaParkir');
    }
}
