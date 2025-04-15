<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RolMiddleware
{
    public function handle($request, Closure $next, $rol)
    {
        if (Auth::check() && Auth::user()->role === $rol) {
            return $next($request);
        }

        abort(403, 'No tienes permisos para acceder a esta sección.');
    }
}
