<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;


class RoleMiddleware
{
   public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            abort(403, 'Non authentifié.');
        }

        foreach ($roles as $role) {
            if ($user->hasRole(trim($role))) {
                return $next($request);
            }
        }

        abort(403, 'Accès refusé. Vous n\'avez pas les droits nécessaires pour cette page.');
    }
}
