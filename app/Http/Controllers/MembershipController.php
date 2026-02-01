<?php

namespace App\Http\Controllers;

use App\Models\Kendaraan;
use App\Models\KendaraanTipe;
use App\Models\Membership;
use App\Models\MembershipKendaraan;
use App\Models\MembershipTier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MembershipController extends Controller
{
    public function index()
    {
        $memberships = Membership::with('membershipTier')->latest()->get();
        return view('admin.membership.index', compact('memberships'));
    }

    public function create()
    {
        $tiers = MembershipTier::all();
        $tipeKendaraans = KendaraanTipe::all();
        $kendaraanList = Kendaraan::all();
        return view('admin.membership.create', compact('tiers','tipeKendaraans','kendaraanList'));
    }

  public function store(Request $request)
{
    $request->validate([
        'nama' => 'required',
        'membership_tier_id' => 'required|exists:membership_tier,id',
        'kadaluarsa' => 'required|date',
        'kendaraan.*.plat_nomor' => 'required',
        'kendaraan.*.warna' => 'required',
        'kendaraan.*.tipe_kendaraan_id' => 'required|exists:kendaraan_tipe,id',
    ]);

    DB::transaction(function () use ($request) {

        $membership = Membership::create([
            'nama' => $request->nama,
            'membership_tier_id' => $request->membership_tier_id,
            'pembaruan_terakhir' => now(),
            'kadaluarsa' => $request->kadaluarsa,
        ]);

        foreach ($request->kendaraan as $item) {

            $kendaraan = Kendaraan::firstOrCreate(
                ['plat_nomor' => $item['plat_nomor']],
                [
                    'warna' => $item['warna'],
                    'tipe_kendaraan_id' => $item['tipe_kendaraan_id'],
                ]
            );

            MembershipKendaraan::create([
                'membership_id' => $membership->id,
                'kendaraan_id' => $kendaraan->id,
            ]);
        }
    });

    return redirect()->route('membership.index')
        ->with('success', 'Membership berhasil ditambahkan');
}

    public function edit(Membership $membership)
    {
        $tiers = MembershipTier::all();
        return view('admin.membership.edit', compact('membership', 'tiers'));
    }

    public function update(Request $request, Membership $membership)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'membership_tier_id' => 'required|exists:membership_tier,id',
            'pembaruan_terakhir' => 'required|date',
            'kadaluarsa' => 'required|date|after_or_equal:pembaruan_terakhir',
        ]);

        $membership->update([
            'nama' => $request->nama,
            'membership_tier_id' => $request->membership_tier_id,
            'pembaruan_terakhir' => $request->pembaruan_terakhir,
            'kadaluarsa' => $request->kadaluarsa,
        ]);

        return redirect()
            ->route('membership.index')
            ->with('success', 'Membership berhasil diperbarui');
    }


    public function destroy(Membership $membership)
    {
        $membership->delete();

        return redirect()->route('membership.index')
            ->with('success', 'Membership berhasil dihapus');
    }
}
