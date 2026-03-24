<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ReceptionOnly
{
    protected $allowedRoutes = [
        'lead/add',
        'lead/create',
        'attendance-toggle',
        'setting/profile',
        'setting/profile/update',
        'setting/password/update',
        'lead/import/upload',
        'lead/generate-share-link',
    ];

    public function handle($request, Closure $next)
    {
        $userType = session('user_type');

        if ($userType === 'reception') 
        {
            $currentPath = $request->path();
            if (!in_array($currentPath, $this->allowedRoutes)) 
            {
                return redirect()->route('lead.add')
                    ->with('error', 'You do not have rights to access this page');
            }
        }

        return $next($request);
    }
}
