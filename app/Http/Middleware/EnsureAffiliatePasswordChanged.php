<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAffiliatePasswordChanged
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user?->role?->isAffiliate() && $user->affiliate?->hasRestrictedPortalAccess()) {
            return $next($request);
        }

        if ($user?->role?->isAffiliate() && $user->must_change_password && ! $request->routeIs('affiliate.password.*') && ! $request->routeIs('logout')) {
            return redirect()->route('affiliate.password.edit')
                ->with('status', 'Debes cambiar tu contraseña inicial para continuar.');
        }

        return $next($request);
    }
}
