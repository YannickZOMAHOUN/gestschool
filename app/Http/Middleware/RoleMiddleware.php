<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;


class RoleMiddleware
{
   public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = $request->user();

        if (!$user || !$user->roles()->where('name', $role)->exists()) {
            abort(403, 'Accès refusé. Rôle requis : ' . $role);
        }

        return $next($request);
    }
}
