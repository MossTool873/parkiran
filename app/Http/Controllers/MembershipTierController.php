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

        MembershipTier::create([
            'membership_tier' => $request->membership_tier,
            'diskon' => $request->diskon,
        ]);

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
        $tier->update([
            'membership_tier' => $request->membership_tier,
            'diskon' => $request->diskon,
        ]);

        return redirect()
            ->route('membership-tier.index')
            ->with('success', 'Membership tier berhasil diperbarui');
    }

    public function destroy($id)
    {
        $tier = MembershipTier::findOrFail($id);
        $tier->delete();

        return redirect()->route('membership-tier.index')->with('success', 'Membership tier berhasil dihapus');
    }

    public function petugasIndex()
{
    $tiers = MembershipTier::latest()->get();

    return view('petugas.membership_tier.index', compact('tiers'));
}

}
