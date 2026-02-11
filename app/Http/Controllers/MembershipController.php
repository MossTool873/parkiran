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

        DB::transaction(function () use ($request, &$membership) {

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

        logAktivitas(
            'Create Membership: ' . $request->nama,
            [
                'new' => [
                    'nama' => $request->nama,
                    'membership_tier_id' => $request->membership_tier_id,
                    'kadaluarsa' => $request->kadaluarsa,
                    'kendaraan' => $request->kendaraan,
                ],
                'aksi' => 'create'
            ]
        );

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

        $oldData = [
            'nama' => $membership->nama,
            'membership_tier_id' => $membership->membership_tier_id,
            'kadaluarsa' => $membership->kadaluarsa,
            'kendaraan' => $membership->kendaraans()->get()->map(function ($k) {
                return [
                    'plat_nomor' => $k->plat_nomor,
                    'warna' => $k->warna,
                    'tipe_kendaraan_id' => $k->tipe_kendaraan_id,
                ];
            })->toArray()
        ];

        DB::transaction(function () use ($request, $membership) {
            $membership->update([
                'nama' => $request->nama,
                'membership_tier_id' => $request->membership_tier_id,
                'pembaruan_terakhir' => now(),
                'kadaluarsa' => $request->kadaluarsa,
            ]);

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

            $membership->kendaraans()->sync($kendaraanIds);
        });

        logAktivitas(
            'Update Membership: ' . $request->nama,
            [
                'old' => $oldData,
                'new' => [
                    'nama' => $request->nama,
                    'membership_tier_id' => $request->membership_tier_id,
                    'kadaluarsa' => $request->kadaluarsa,
                    'kendaraan' => $request->kendaraan,
                ],
                'aksi' => 'update'
            ]
        );

        return redirect()
            ->route('membership.index')
            ->with('success', 'Membership berhasil diperbarui');
    }

    public function destroy(Membership $membership)
    {
        $oldData = [
            'nama' => $membership->nama,
            'membership_tier_id' => $membership->membership_tier_id,
            'kadaluarsa' => $membership->kadaluarsa,
            'kendaraan' => $membership->kendaraans()->get()->map(function ($k) {
                return [
                    'plat_nomor' => $k->plat_nomor,
                    'warna' => $k->warna,
                    'tipe_kendaraan_id' => $k->tipe_kendaraan_id,
                ];
            })->toArray()
        ];

        $membership->delete();

        logAktivitas(
            'Delete Membership: ' . $oldData['nama'],
            [
                'old' => $oldData,
                'aksi' => 'delete'
            ]
        );

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

        DB::transaction(function () use ($request, &$membership) {
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

        logAktivitas(
            'Create Membership (Petugas): ' . $request->nama,
            [
                'new' => [
                    'nama' => $request->nama,
                    'membership_tier_id' => $request->membership_tier_id,
                    'kadaluarsa' => $request->kadaluarsa,
                    'kendaraan' => $request->kendaraan,
                ],
                'aksi' => 'create'
            ]
        );

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

        $oldData = [
            'nama' => $membership->nama,
            'membership_tier_id' => $membership->membership_tier_id,
            'kadaluarsa' => $membership->kadaluarsa,
            'kendaraan' => $membership->kendaraans()->get()->map(function ($k) {
                return [
                    'plat_nomor' => $k->plat_nomor,
                    'warna' => $k->warna,
                    'tipe_kendaraan_id' => $k->tipe_kendaraan_id,
                ];
            })->toArray()
        ];

        DB::transaction(function () use ($request, $membership) {
            $membership->update([
                'nama' => $request->nama,
                'membership_tier_id' => $request->membership_tier_id,
                'pembaruan_terakhir' => now(),
                'kadaluarsa' => $request->kadaluarsa,
            ]);

            $membership->kendaraans()->detach();

            foreach ($request->kendaraan as $item) {
                $kendaraan = Kendaraan::updateOrCreate(
                    ['plat_nomor' => $item['plat_nomor']],
                    [
                        'warna' => $item['warna'],
                        'tipe_kendaraan_id' => $item['tipe_kendaraan_id'],
                    ]
                );

                $membership->kendaraans()->attach($kendaraan->id);
            }
        });

        logAktivitas(
            'Update Membership (Petugas): ' . $request->nama,
            [
                'old' => $oldData,
                'new' => [
                    'nama' => $request->nama,
                    'membership_tier_id' => $request->membership_tier_id,
                    'kadaluarsa' => $request->kadaluarsa,
                    'kendaraan' => $request->kendaraan,
                ],
                'aksi' => 'update'
            ]
        );

        return redirect()
            ->route('petugas.membership.index')
            ->with('success', 'Membership berhasil diperbarui');
    }
}
