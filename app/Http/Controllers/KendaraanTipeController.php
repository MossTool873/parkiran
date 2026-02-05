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
        $request->validate(['tipe_kendaraan' => 'required',]);
        KendaraanTipe::create(['tipe_kendaraan' => $request->tipe_kendaraan]);
        return redirect('/admin/tipeKendaraan');
    }

    public function edit($id)
    {
        $tipeKendaraan = KendaraanTipe::findOrFail($id);
        return view('admin.tipeKendaraan.edit', compact('tipeKendaraan'));
    }

    // UPDATE
    public function update(Request $request, $id)
    {
        $request->validate(['tipe_kendaraan' => 'required',]);
        KendaraanTipe::where('id', $id)->update(['tipe_kendaraan' => $request->tipe_kendaraan]);
        return redirect('/admin/tipeKendaraan');
    }

public function destroy($id)
{
    $tipe = KendaraanTipe::findOrFail($id);

    if ($tipe->tarifTipeKendaraans()->exists()) {
        return back()->with('error', 'Tipe kendaraan masih digunakan!!');
    }

    $tipe->delete();

    return back()->with('success', 'Tipe kendaraan berhasil dihapus');
}
}
