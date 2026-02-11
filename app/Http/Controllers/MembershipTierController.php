<?php

namespace App\Http\Controllers;

use App\Models\MembershipTier;
use Illuminate\Http\Request;

class MembershipTierController extends Controller
{
    public function index()
    {
        $tiers = MembershipTier::latest()->get();
        return view('admin.membership_tier.index', compact('tiers'));
    }

    public function create()
    {
        return view('admin.membership_tier.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'membership_tier' => 'required|string|max:100',
            'diskon' => 'required|numeric|min:0|max:100',
        ]);

        $tier = MembershipTier::create([
            'membership_tier' => $request->membership_tier,
            'diskon' => $request->diskon,
        ]);

        logAktivitas(
            'Create Membership Tier: ' . $request->membership_tier,
            [
                'new' => [
                    'membership_tier' => $request->membership_tier,
                    'diskon' => $request->diskon,
                ],
                'aksi' => 'create'
            ]
        );

        return redirect()
            ->route('membership-tier.index')
            ->with('success', 'Membership tier berhasil ditambahkan');
    }

    public function edit($id)
    {
        $tier = MembershipTier::findOrFail($id);
        return view('admin.membership_tier.edit', compact('tier'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'membership_tier' => 'required|string|max:100',
            'diskon' => 'required|numeric|min:0|max:100',
        ]);

        $tier = MembershipTier::findOrFail($id);

        $oldData = [
            'membership_tier' => $tier->membership_tier,
            'diskon' => $tier->diskon,
        ];

        $tier->update([
            'membership_tier' => $request->membership_tier,
            'diskon' => $request->diskon,
        ]);

        logAktivitas(
            'Update Membership Tier: ' . $request->membership_tier,
            [
                'old' => $oldData,
                'new' => [
                    'membership_tier' => $request->membership_tier,
                    'diskon' => $request->diskon,
                ],
                'aksi' => 'update'
            ]
        );

        return redirect()
            ->route('membership-tier.index')
            ->with('success', 'Membership tier berhasil diperbarui');
    }

    public function destroy($id)
    {
        $tier = MembershipTier::findOrFail($id);

        if ($tier->memberships()->exists()) {
            return back()->with('error', 'membership tier masih digunakan!!');
        }

        $oldData = [
            'membership_tier' => $tier->membership_tier,
            'diskon' => $tier->diskon,
        ];

        $tier->delete();

        logAktivitas(
            'Delete Membership Tier: ' . $oldData['membership_tier'],
            [
                'old' => $oldData,
                'aksi' => 'delete'
            ]
        );

        return redirect()->route('membership-tier.index')->with('success', 'Membership tier berhasil dihapus');
    }

    public function petugasIndex()
    {
        $tiers = MembershipTier::latest()->get();
        return view('petugas.membership_tier.index', compact('tiers'));
    }
}
