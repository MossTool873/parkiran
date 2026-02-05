<?php

use App\Models\LogAktivitas;
use Illuminate\Support\Facades\Auth;

if (!function_exists('logAktivitas')) {
    function logAktivitas(string $aksi, ?int $userId = null): void
    {
        LogAktivitas::create([
            'user_id' => $userId ?? Auth::id(),
            'aksi'    => $aksi,
        ]);
    }
}
