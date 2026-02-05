<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $userRole = Auth::user()->role?->role;

        // normalisasi role (trim + lowercase)
        $roles = array_map(fn ($r) => strtolower(trim($r)), $roles);

        if (!in_array(strtolower($userRole), $roles, true)) {
            abort(403, 'Akses Ditolak');
        }

        return $next($request);
    }
}

