<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle($request, Closure $next, $permission = null)
    {
        // dd($request->method(), $request->fullUrl(), $request->ip(), $request);
        $role = $request->user()->role_id;
        // dd($permission, $request->user()->cekPermission($permission, $role), $request->user()->roles());
        // if (!$request->user()->hasRole($role)) {
        //     abort(401);
        // }

        if ($permission !== null && !$request->user()->cekPermission($permission, $role)) {
            abort(401);
        }

        return $next($request);
    }
}
