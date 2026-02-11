<?php

namespace App\Http\Controllers;

use App\Models\AreaParkir;
use App\Models\AreaParkirDetail;
use App\Models\KendaraanTipe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AreaParkirController extends Controller
{
    public function index(Request $request)
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

        return view('admin.areaParkir.index', compact('areaParkirs', 'search'));
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
            'lokasi' => 'required|string|max:255',
            'kapasitas' => 'required|array',
            'kapasitas.*' => 'nullable|integer|min:0'
        ]);

        $total_kapasitas = array_sum($request->kapasitas);

        if ($total_kapasitas <= 0) {
            return back()
                ->withErrors(['error' => 'Total kapasitas harus lebih dari 0'])
                ->withInput();
        }

        DB::transaction(function () use ($request, $total_kapasitas) {

            $area = AreaParkir::create([
                'nama_area'       => $request->nama_area,
                'lokasi'          => $request->lokasi,
                'total_kapasitas' => $total_kapasitas
            ]);

            foreach ($request->kapasitas as $tipeId => $jumlah) {
                if ($jumlah > 0) {
                    AreaParkirDetail::create([
                        'area_parkir_id'     => $area->id,
                        'tipe_kendaraan_id'  => $tipeId,
                        'kapasitas'          => $jumlah
                    ]);
                }
            }

            logAktivitas(
                'Create Area Parkir: ' . $area->nama_area,
                [
                    'new' => [
                        'nama_area' => $area->nama_area,
                        'lokasi' => $area->lokasi,
                        'total_kapasitas' => $area->total_kapasitas,
                        'kapasitas_detail' => $request->kapasitas
                    ],
                    'aksi' => 'create'
                ]
            );
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
            'nama_area'   => 'required|string|max:255',
            'lokasi'      => 'required|string|max:255',
            'kapasitas'   => 'required|array',
            'kapasitas.*' => 'nullable|integer|min:0'
        ]);

        // ================= VALIDASI KAPASITAS VS TERISI =================
        foreach ($request->kapasitas as $tipeId => $jumlah) {
            $detail = AreaParkirDetail::with('tipeKendaraan')
                ->where('area_parkir_id', $id)
                ->where('tipe_kendaraan_id', $tipeId)
                ->first();

            if ($detail && $jumlah < $detail->terisi) {
                $namaTipe = $detail->tipeKendaraan->tipe_kendaraan ?? 'Tipe kendaraan';

                return back()
                    ->withErrors([
                        "kapasitas.$tipeId" =>
                            "Kapasitas {$namaTipe} tidak boleh kurang dari jumlah terpakai ({$detail->terisi})"
                    ])
                    ->withInput();
            }
        }

        // ================= TRANSAKSI UPDATE =================
        DB::transaction(function () use ($request, $id) {

            $areaParkir = AreaParkir::with('detailKapasitas')->findOrFail($id);

            // simpan data lama
            $oldData = [
                'nama_area' => $areaParkir->nama_area,
                'lokasi' => $areaParkir->lokasi,
                'total_kapasitas' => $areaParkir->total_kapasitas,
                'kapasitas_detail' => $areaParkir->detailKapasitas->pluck('kapasitas', 'tipe_kendaraan_id')->toArray()
            ];

            $total_kapasitas = array_sum($request->kapasitas);

            $areaParkir->update([
                'nama_area'       => $request->nama_area,
                'lokasi'          => $request->lokasi,
                'total_kapasitas' => $total_kapasitas
            ]);

            foreach ($request->kapasitas as $tipeId => $jumlah) {
                AreaParkirDetail::updateOrCreate(
                    [
                        'area_parkir_id'    => $id,
                        'tipe_kendaraan_id' => $tipeId
                    ],
                    [
                        'kapasitas' => $jumlah
                    ]
                );
            }

            logAktivitas(
                'Update Area Parkir: ' . $areaParkir->nama_area,
                [
                    'old' => $oldData,
                    'new' => [
                        'nama_area' => $request->nama_area,
                        'lokasi' => $request->lokasi,
                        'total_kapasitas' => $total_kapasitas,
                        'kapasitas_detail' => $request->kapasitas
                    ],
                    'aksi' => 'update'
                ]
            );
        });

        return redirect('/admin/areaParkir')
            ->with('success', 'Area parkir berhasil diperbarui');
    }

    public function destroy($id)
    {
        $areaParkir = AreaParkir::with('detailKapasitas')->findOrFail($id);

        // simpan data lama
        $oldData = [
            'nama_area' => $areaParkir->nama_area,
            'lokasi' => $areaParkir->lokasi,
            'total_kapasitas' => $areaParkir->total_kapasitas,
            'kapasitas_detail' => $areaParkir->detailKapasitas->pluck('kapasitas', 'tipe_kendaraan_id')->toArray()
        ];

        $areaParkir->delete();

        logAktivitas(
            'Delete Area Parkir: ' . $areaParkir->nama_area,
            [
                'old' => $oldData,
                'aksi' => 'delete'
            ]
        );

        return redirect('/admin/areaParkir')
            ->with('success', 'Area parkir berhasil dihapus');
    }
}
