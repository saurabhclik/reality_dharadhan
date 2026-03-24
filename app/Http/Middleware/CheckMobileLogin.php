<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CheckMobileLogin
{
    public function handle(Request $request, Closure $next)
    {
        if (!Session::has('user_id') || Session::get('platform') !== 'mobile') 
        {
            return redirect()->route('mobile.login.form')->with('error', 'Please login to access the dashboard.');
        }
        return $next($request);
    }
}
