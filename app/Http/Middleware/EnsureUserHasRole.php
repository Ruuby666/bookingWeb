<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Middleware responsible for role-based authorization (admin, super_admin).
 */
class EnsureUserHasRole
{
    /**
     * Allow access only to authenticated users holding the given role.
     *
     * @param  Request  $request  Current request instance
     * @param  Closure  $next  Next middleware action
     * @param  string  $role  Either "admin" or "super_admin"
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $role)
    {
        $column = $role === 'super_admin' ? 'is_super_admin' : 'is_admin';

        if (Auth::check() && Auth::user()->{$column}) {
            return $next($request);
        }

        $message = $role === 'super_admin'
            ? 'Access denied. Super admin privileges required.'
            : 'Access denied — admin privileges required.';

        return redirect('/login')->with('error', $message);
    }
}
