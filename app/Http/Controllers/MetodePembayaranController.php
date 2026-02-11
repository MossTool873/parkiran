<?php

namespace App\Http\Controllers;

use App\Models\MetodePembayaran;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MetodePembayaranController extends Controller
{
    public function index()
    {
        $metodePembayarans = MetodePembayaran::all();
        return view('admin.metodePembayaran.index', compact('metodePembayarans'));
    }

    public function create()
    {
        return view('admin.metodePembayaran.create');
    }

    public function store(Request $request)
    {
        $request->validate(
            [
                'nama_metode' => [
                    'required',
                    'string',
                    'max:100',
                ]
            ],
            [
                'nama_metode.unique' => 'Metode pembayaran ini sudah ada.'
            ]
        );

        $metode = MetodePembayaran::create([
            'nama_metode' => $request->nama_metode
        ]);

        logAktivitas(
            'Create Metode Pembayaran: ' . $request->nama_metode,
            [
                'new' => [
                    'nama_metode' => $request->nama_metode,
                ],
                'aksi' => 'create'
            ]
        );

        return redirect('/admin/metodePembayaran')
            ->with('success', 'Metode pembayaran berhasil ditambahkan');
    }

    public function edit($id)
    {
        $metode = MetodePembayaran::findOrFail($id);
        return view('admin.metodePembayaran.edit', compact('metode'));
    }

    public function update(Request $request, $id)
    {
        $request->validate(
            [
                'nama_metode' => [
                    'required',
                    'string',
                    'max:100',
                    Rule::unique('metode_pembayaran', 'nama_metode')->ignore($id)
                ]
            ]
        );

        $metode = MetodePembayaran::findOrFail($id);

        $oldData = [
            'nama_metode' => $metode->nama_metode
        ];

        $metode->update([
            'nama_metode' => $request->nama_metode
        ]);

        logAktivitas(
            'Update Metode Pembayaran: ' . $request->nama_metode,
            [
                'old' => $oldData,
                'new' => [
                    'nama_metode' => $request->nama_metode
                ],
                'aksi' => 'update'
            ]
        );

        return redirect('/admin/metodePembayaran')
            ->with('success', 'Metode pembayaran berhasil diperbarui');
    }

    public function destroy($id)
    {
        $metode = MetodePembayaran::findOrFail($id);

        $oldData = [
            'nama_metode' => $metode->nama_metode
        ];

        $metode->delete();

        logAktivitas(
            'Delete Metode Pembayaran: ' . $oldData['nama_metode'],
            [
                'old' => $oldData,
                'aksi' => 'delete'
            ]
        );

        return redirect('/admin/metodePembayaran')
            ->with('success', 'Metode pembayaran berhasil dihapus');
    }
}
