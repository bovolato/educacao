<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Garante que roles do Spatie estejam carregadas cedo no request,
 * evitando múltiplos hits ao resolver hasRole / exibição no layout.
 */
class LoadAuthenticatedUserRoles
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($user = $request->user()) {
            $user->loadMissing('roles');
        }

        return $next($request);
    }
}
