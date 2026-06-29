<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Middleware responsible for super admin authorization.
 */
class IsSuperAdmin
{
    /**
     * Allow access only to authenticated super admin users.
     *
     * @param  Request  $request  Current request instance
     * @param  Closure  $next  Next middleware action
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->is_super_admin) {
            return $next($request);
        }

        return redirect('/')
            ->with('error', 'Access denied. Super admin privileges required.');
    }
}
