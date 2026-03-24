<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class MobileNotificationController extends Controller
{
    public function notification(Request $request)
    {
        $childIds = Session::get('child_ids', '');
        $childIdsArray = $childIds ? array_map('trim', explode(',', $childIds)) : [];

        if (empty($childIdsArray)) 
        {
            if ($request->ajax()) 
            {
                return response()->json([
                    'notifications' => [],
                    'hasMore' => false,
                    'nextPage' => 1,
                    'totalCount' => 0,
                ]);
            }

            return view('mobile.notification', [
                'initialNotifications' => [],
                'hasMoreInitial' => false,
                'totalCount' => 0,
                'selectedStatus' => $request->get('status', 'all')
            ]);
        }

        $allowedStatuses = ['CALL SCHEDULED', 'VISIT SCHEDULED', 'INTERESTED', 'MEETING SCHEDULED'];
        $selectedStatus = $request->get('status', 'all');

        $query = DB::table('leads')
            ->select('id', 'name', 'phone', 'status', 'last_comment', 'lead_date', 'created_at')
            ->whereIn('user_id', $childIdsArray)
            ->orderBy('created_at', 'desc');
        $today = Carbon::today()->toDateString();
        $tomorrow = Carbon::tomorrow()->toDateString();

        if ($selectedStatus === 'today') 
        {
            $query->whereDate('lead_date', $today);
        } 
        elseif ($selectedStatus === 'tomorrow') {
            $query->whereDate('lead_date', $tomorrow);
        } 
        elseif ($selectedStatus === 'missed') 
        {
            $query->whereDate('lead_date', '<', $today)
                ->whereIn('status', $allowedStatuses);
        } 
        elseif ($selectedStatus !== 'all') 
        {
            $query->where('status', $selectedStatus);
        } 
        else 
        {
            $query->whereIn('status', $allowedStatuses);
        }

        $perPage = $request->get('per_page', 10);
        $notifications = $query->paginate($perPage);

        if ($request->ajax()) 
        {
            return response()->json([
                'notifications' => $notifications->items(),
                'hasMore' => $notifications->hasMorePages(),
                'nextPage' => $notifications->currentPage() + 1,
                'totalCount' => $notifications->total(),
            ]);
        }
        
        return view('mobile.notification', [
            'initialNotifications' => $notifications->items(),
            'hasMoreInitial' => $notifications->hasMorePages(),
            'totalCount' => $notifications->total(),
            'selectedStatus' => $selectedStatus
        ]);
    }
}