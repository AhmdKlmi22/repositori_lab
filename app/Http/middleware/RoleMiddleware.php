<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect('/');
        }

        $userRole = Auth::user()->role->nama_role;

        // Cek apakah role user saat ini ada di dalam daftar role yang diizinkan route
        if (in_array($userRole, $roles)) {
            return $next($request);
        }

        // Jika tidak punya akses, kembalikan ke dashboard masing-masing
        return redirect('/dashboard-' . strtolower($userRole))
            ->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
    }
}