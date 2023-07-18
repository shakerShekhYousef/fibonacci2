<?php

namespace App\Http\Middleware;

use App\Models\Role;
use Closure;
use Illuminate\Http\Request;

class AdminRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $code)
    {
        $user = auth()->user();
        if ($user->role_id == null) {
            return forbidden_response('forbidden');
        }
        $role = Role::with(['permissions'])->find($user->role_id);
        foreach ($role->permissions as $permission) {
            if ($permission->permission->code == $code) {
                return $next($request);
            }
        }

        return forbidden_response('forbidden');
    }
}
