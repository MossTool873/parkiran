<?php

namespace App\Http\Controllers;

use App\Models\MembershipKendaraan;
use Illuminate\Http\Request;

class KendaraanMembershipController extends Controller
{
public function index()
{
    $kendaraanMemberships = MembershipKendaraan::with([
        'kendaraan',
        'membership',
        'areaParkir'
    ])->get();

    return view('admin.membership.kendaraan', compact('kendaraanMemberships'));
}

}
