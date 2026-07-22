<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role)
    {
        if (!auth()->check() || auth()->user()->role !== $role) {
            return redirect()->route('dashboard')
                ->with('error', '⚠️ No tenés permisos para acceder a esta sección.');
        }

        return $next($request);
    }
}
