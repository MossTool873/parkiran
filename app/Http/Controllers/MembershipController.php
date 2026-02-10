<?php

namespace App\Http\Controllers;

use App\Models\Kendaraan;
use App\Models\KendaraanTipe;
use App\Models\Membership;
use App\Models\MembershipKendaraan;
use App\Models\MembershipTier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MembershipController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');

        $memberships = Membership::with('membershipTier')
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.membership.index', compact('memberships', 'search'));
    }


    public function create()
    {
        $tiers = MembershipTier::all();
        $tipeKendaraans = KendaraanTipe::all();
        $kendaraanList = Kendaraan::all();
        return view('admin.membership.create', compact('tiers', 'tipeKendaraans', 'kendaraanList'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|unique:membership,nama',
            'membership_tier_id' => 'required|exists:membership_tier,id',
            'kadaluarsa' => 'required|date',
            'kendaraan.*.plat_nomor' => 'required',
            'kendaraan.*.warna' => 'required',
            'kendaraan.*.tipe_kendaraan_id' => 'required|exists:kendaraan_tipe,id',
        ], [
            'nama.unique' => 'Nama membership sudah digunakan.',
        ]);

        DB::transaction(function () use ($request) {

            /* ===================== VALIDASI KENDARAAN SUDAH PUNYA MEMBERSHIP ===================== */
            foreach ($request->kendaraan as $i => $item) {

                $sudahAda = DB::table('membership_kendaraan')
                    ->join('membership', 'membership.id', '=', 'membership_kendaraan.membership_id')
                    ->join('kendaraan', 'kendaraan.id', '=', 'membership_kendaraan.kendaraan_id')
                    ->whereNull('membership.deleted_at')
                    ->whereNull('membership_kendaraan.deleted_at')
                    ->where('kendaraan.plat_nomor', $item['plat_nomor'])
                    ->whereDate('membership.kadaluarsa', '>=', now()) // membership masih aktif
                    ->exists();

                if ($sudahAda) {
                    throw ValidationException::withMessages([
                        "kendaraan.$i.plat_nomor" =>
                        "Kendaraan {$item['plat_nomor']} sudah terdaftar di membership lain"
                    ]);
                }
            }

            /* ===================== CREATE MEMBERSHIP ===================== */
            $membership = Membership::create([
                'nama' => $request->nama,
                'membership_tier_id' => $request->membership_tier_id,
                'pembaruan_terakhir' => now(),
                'kadaluarsa' => $request->kadaluarsa,
            ]);

            /* ===================== SIMPAN KENDARAAN ===================== */
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

        return redirect()
            ->route('membership.index')
            ->with('success', 'Membership berhasil ditambahkan');
    }

    public function edit($id)
    {
        $membership = Membership::findOrFail($id);
        $tiers = MembershipTier::all();
        $tipeKendaraans = KendaraanTipe::all();
        $kendaraanList = Kendaraan::all();

        return view('admin.membership.edit', compact('membership', 'tiers', 'kendaraanList', 'tipeKendaraans'));
    }

   public function update(Request $request, Membership $membership)
{
    $request->validate([
        'nama' => 'required|unique:membership,nama,' . $membership->id,
        'membership_tier_id' => 'required|exists:membership_tier,id',
        'kadaluarsa' => 'required|date|after_or_equal:pembaruan_terakhir',
        'kendaraan.*.plat_nomor' => 'required',
        'kendaraan.*.warna' => 'required',
        'kendaraan.*.tipe_kendaraan_id' => 'required|exists:kendaraan_tipe,id',
    ], [
        'nama.unique' => 'Nama membership sudah digunakan.',
    ]);

    DB::transaction(function () use ($request, $membership) {

        // ===================== VALIDASI KENDARAAN SUDAH PUNYA MEMBERSHIP LAIN =====================
        foreach ($request->kendaraan as $i => $item) {
            $sudahAda = DB::table('membership_kendaraan')
                ->join('membership', 'membership.id', '=', 'membership_kendaraan.membership_id')
                ->join('kendaraan', 'kendaraan.id', '=', 'membership_kendaraan.kendaraan_id')
                ->whereNull('membership.deleted_at')
                ->whereNull('membership_kendaraan.deleted_at')
                ->where('kendaraan.plat_nomor', $item['plat_nomor'])
                ->whereDate('membership.kadaluarsa', '>=', now())
                ->where('membership.id', '<>', $membership->id) // kecuali membership yang sedang diedit
                ->exists();

            if ($sudahAda) {
                throw ValidationException::withMessages([
                    "kendaraan.$i.plat_nomor" =>
                    "Kendaraan {$item['plat_nomor']} sudah terdaftar di membership lain"
                ]);
            }
        }

        // ===================== UPDATE MEMBERSHIP =====================
        $membership->update([
            'nama' => $request->nama,
            'membership_tier_id' => $request->membership_tier_id,
            'pembaruan_terakhir' => now(),
            'kadaluarsa' => $request->kadaluarsa,
        ]);

        // ===================== SIMPAN / UPDATE KENDARAAN =====================
        $kendaraanIds = [];
        foreach ($request->kendaraan as $item) {
            $kendaraan = Kendaraan::firstOrCreate(
                ['plat_nomor' => $item['plat_nomor']],
                [
                    'warna' => $item['warna'],
                    'tipe_kendaraan_id' => $item['tipe_kendaraan_id'],
                ]
            );
            $kendaraanIds[] = $kendaraan->id;
        }

        // sync supaya kendaraan yang tidak ada di request dihapus
        $membership->kendaraans()->sync($kendaraanIds);
    });

    return redirect()
        ->route('membership.index')
        ->with('success', 'Membership berhasil diperbarui');
}


    public function destroy(Membership $membership)
    {
        $membership->delete();

        return redirect()->route('membership.index')->with('success', 'Membership berhasil dihapus');
    }

    // ===================== UNTUK PETUGAS =====================
    public function indexPetugas(Request $request)
    {
        $search = $request->query('search');

        $memberships = Membership::with('membershipTier')
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('petugas.membership.index', compact('memberships', 'search'));
    }

    public function createPetugas()
    {
        $tiers = MembershipTier::all();
        $tipeKendaraans = KendaraanTipe::all();
        $kendaraanList = Kendaraan::all();
        return view('petugas.membership.create', compact('tiers', 'tipeKendaraans', 'kendaraanList'));
    }

    public function storePetugas(Request $request)
    {
        $request->validate([
            'nama' => 'required|unique:membership,nama',
            'membership_tier_id' => 'required|exists:membership_tier,id',
            'kadaluarsa' => 'required|date',
            'kendaraan.*.plat_nomor' => 'required',
            'kendaraan.*.warna' => 'required',
            'kendaraan.*.tipe_kendaraan_id' => 'required|exists:kendaraan_tipe,id',
        ], [
            'nama.unique' => 'Nama membership sudah digunakan.',
        ]);

        DB::transaction(function () use ($request) {
            foreach ($request->kendaraan as $i => $item) {
                $sudahAda = DB::table('membership_kendaraan')
                    ->join('membership', 'membership.id', '=', 'membership_kendaraan.membership_id')
                    ->join('kendaraan', 'kendaraan.id', '=', 'membership_kendaraan.kendaraan_id')
                    ->whereNull('membership.deleted_at')
                    ->whereNull('membership_kendaraan.deleted_at')
                    ->where('kendaraan.plat_nomor', $item['plat_nomor'])
                    ->whereDate('membership.kadaluarsa', '>=', now())
                    ->exists();

                if ($sudahAda) {
                    throw ValidationException::withMessages([
                        "kendaraan.$i.plat_nomor" =>
                        "Kendaraan {$item['plat_nomor']} sudah terdaftar di membership lain"
                    ]);
                }
            }

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

        return redirect()
            ->route('petugas.membership.index')
            ->with('success', 'Membership berhasil ditambahkan');
    }

 public function editPetugas(Membership $membership)
{
    $tiers = MembershipTier::all();
    $tipeKendaraans = KendaraanTipe::all();
    $kendaraanList = Kendaraan::all();

    return view('petugas.membership.edit', compact(
        'membership',
        'tiers',
        'kendaraanList',
        'tipeKendaraans'
    ));
}

public function updatePetugas(Request $request, Membership $membership)
{
    $request->validate([
        'nama' => 'required|unique:membership,nama,' . $membership->id,
        'membership_tier_id' => 'required|exists:membership_tier,id',
        'kadaluarsa' => 'required|date|after_or_equal:pembaruan_terakhir',
        'kendaraan.*.plat_nomor' => 'required',
        'kendaraan.*.warna' => 'required',
        'kendaraan.*.tipe_kendaraan_id' => 'required|exists:kendaraan_tipe,id',
    ], [
        'nama.unique' => 'Nama membership sudah digunakan.',
    ]);

    DB::transaction(function () use ($request, $membership) {

        // ================= UPDATE MEMBERSHIP =================
        $membership->update([
            'nama' => $request->nama,
            'membership_tier_id' => $request->membership_tier_id,
                'pembaruan_terakhir' => now(),
            'kadaluarsa' => $request->kadaluarsa,
        ]);

        // ================= HAPUS RELASI KENDARAAN LAMA =================
        $membership->kendaraans()->detach();

        // ================= SIMPAN / UPDATE KENDARAAN =================
        foreach ($request->kendaraan as $item) {
            // Cek apakah kendaraan sudah ada
            $kendaraan = Kendaraan::updateOrCreate(
                ['plat_nomor' => $item['plat_nomor']], // kondisi unik
                [
                    'warna' => $item['warna'],
                    'tipe_kendaraan_id' => $item['tipe_kendaraan_id'],
                ]
            );

            // Buat relasi dengan membership
            $membership->kendaraans()->attach($kendaraan->id);
        }
    });

    return redirect()
        ->route('petugas.membership.index')
        ->with('success', 'Membership berhasil diperbarui');
}

}
