<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Helpers\logAktivitas; // pastikan helper sudah include

class LogMenuAccess
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, $menuName = null)
    {
        $response = $next($request);

        // Hanya log jika menuName diberikan
        if ($menuName) {
            logAktivitas('Menu '.$menuName);
        }

        return $response;
    }
}
