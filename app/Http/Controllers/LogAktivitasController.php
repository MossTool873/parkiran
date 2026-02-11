<?php

namespace App\Http\Controllers;

use App\Models\LogAktivitas;
use Illuminate\Http\Request;

class LogAktivitasController extends Controller
{
    public function index(Request $request)
    {
        $query = LogAktivitas::with('user');

        // ================= SEARCH GLOBAL =================
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;

            $query->where(function ($q) use ($keyword) {
                $q->where('aksi', 'like', "%{$keyword}%")
                  ->orWhere('detail', 'like', "%{$keyword}%")
                  ->orWhere('ip_address', 'like', "%{$keyword}%")
                  ->orWhereHas('user', function ($u) use ($keyword) {
                      $u->where('name', 'like', "%{$keyword}%")
                        ->orWhere('username', 'like', "%{$keyword}%");
                  });
            });
        }

        // ================= SORT & PAGINATE =================
        $logAktivitas = $query
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.log_aktivitas.index', compact('logAktivitas'));
    }
}
