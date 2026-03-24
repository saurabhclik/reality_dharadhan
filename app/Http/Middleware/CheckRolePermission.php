<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class CheckRolePermission
{
    public function handle(Request $request, Closure $next)
    {
        $roleId = session('role_id');
        $currentRoute = $request->route()->getName();
        $allowed = DB::table('role_permissions')
                    ->join('permissions', 'role_permissions.permission_id', '=', 'permissions.id')
                    ->where('role_permissions.role_id', $roleId)
                    ->where('permissions.name', $currentRoute)
                    ->exists();

        if (!$allowed) 
        {
            return redirect()->route('lead.add')
                ->with('error', 'You do not have rights to access this page');
        }

        return $next($request);
    }
}
