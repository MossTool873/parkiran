<?php

use App\Models\LogAktivitas;
use Illuminate\Support\Facades\Auth;

if (!function_exists('logAktivitas')) {
    function logAktivitas(string $aksi, $detail = null, ?int $userId = null)
    {
        LogAktivitas::create([
            'user_id'    => $userId ?? \Illuminate\Support\Facades\Auth::id(),
            'aksi'       => $aksi,
            'detail'     => is_array($detail) ? json_encode($detail) : $detail,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}

