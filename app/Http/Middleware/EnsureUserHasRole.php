<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = $request->user();

        if (! $user || $user->role !== UserRole::from($role)) {
            abort(403, 'No tenés permiso para acceder a esta sección.');
        }

        if (! $user->is_active) {
            abort(403, 'Tu cuenta está desactivada. Contactá al administrador del portal.');
        }

        return $next($request);
    }
}
