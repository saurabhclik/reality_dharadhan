<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CheckSoftwareType
{
    public function handle(Request $request, Closure $next, $type)
    {
        $currentType = session('software_type');

        if (!$currentType) 
        {
            return redirect()->route('dashboard')->with('error', 'Software type not defined.');
        }

        $accessRules = [
            'real_state' => ['all_modules','universal_modules', 'real_state_only', 'task_management', 'mis_management'],
            'lead_management' => ['all_modules','universal_modules', 'task_management', 'mis_management'],
            'task_management' => ['task_management', 'universal_modules'],
            'mis_management' => ['mis_management','universal_modules'],
        ];

        $commonRoutes = [
            'setting.profile',
            'setting.update_profile',
            'setting.update_password',
            'setting.logo',
            'setting.login_log',
            'setting.update_logo',
            'logout',
            'reports',
            'task-report-summary',
            'task-overdue-summary',
            'upcoming-tasks',
            'task-completion',
            'project-wise-task',
        ];

        
        if (in_array($request->route()->getName(), $commonRoutes)) 
        {
            return $next($request);
        }

        if ($request->route()->getName() === 'dashboard') 
        {
            if ($currentType === 'task_management') 
            {
                return redirect()->route('task.list');
            }
            if ($currentType === 'mis_management') 
            {
                return redirect()->route('mis.summary-report');
            }
            return $next($request);
        }

        if (!isset($accessRules[$currentType]) || !in_array($type, $accessRules[$currentType])) 
        {
            if ($currentType === 'task_management') 
            {
                return redirect()->route('task.list')->with('error', 'Access denied for this module.');
            }
            if ($currentType === 'mis_management') 
            {
                return redirect()->route('mis.summary-report')->with('error', 'Access denied for this module.');
            }
            return redirect()->route('dashboard')->with('error', 'Access denied for this module.');
        }

        return $next($request);
    }
}