<?php

namespace App\Http\Controllers;

use App\Models\MembershipKendaraan;
use Illuminate\Http\Request;

class KendaraanMemberController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $query = MembershipKendaraan::with('membership', 'kendaraan')
            ->orderBy('membership_id', 'asc');

        // Filter jika ada search
        if ($search) {
            $query->whereHas('membership', function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%");
            });
        }

        $membershipKendaraans = $query->paginate(10)->withQueryString(); // withQueryString agar search tetap di pagination

        return view('admin.membership_kendaraan.index', compact('membershipKendaraans', 'search'));
    }
}
