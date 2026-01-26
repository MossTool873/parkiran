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
        $areaParkirs = AreaParkir::with('detailKapasitas.tipeKendaraan')
        ->whereNull('deleted_at')
        ->get();
        
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
            'nama_area' => 'required',
            'kapasitas' => 'required|array'
        ]);

        DB::transaction(function () use ($request) {
            $total_kapasitas = array_sum($request->kapasitas);

            $area = AreaParkir::create([
                'nama_area' => $request->nama_area,
                'total_kapasitas' => $total_kapasitas
            ]);

            foreach ($request->kapasitas as $tipeId => $jumlah) {
                if ($jumlah > 0) {
                    AreaParkirDetail::create([
                        'area_parkir_id' => $area->id,
                        'tipe_kendaraan_id' => $tipeId,
                        'kapasitas' => $jumlah
                    ]);
                }
            }
        });

        return redirect('/admin/areaParkir');
    }

public function edit($id)
{
    $areaParkir = AreaParkir::with('detailKapasitas')->findOrFail($id);
    $tipeKendaraans = KendaraanTipe::all();

    return view('admin.areaParkir.edit', compact('areaParkir', 'tipeKendaraans'));
}

    // UPDATE
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_area' => 'required',
            'kapasitas' => 'required|array'
        ]);

        DB::transaction(function () use ($request, $id) {
            AreaParkir::where('id', $id)->update([
                'nama_area' => $request->nama_area
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
