<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class MobileTaskController extends Controller
{
    private function userTasksQuery($userId)
    {
        return DB::table('tasks')
            ->where('tasks.created_by', $userId)
            ->orWhereIn('tasks.id', function ($subQuery) use ($userId) 
            {
                $subQuery->select('task_id')
                    ->from('task_user')
                    ->where('user_id', $userId);
            });
    }

    public function index(Request $request)
    {
        $userId   = Session::get('user_id');
        $userType = Session::get('user_type', 'user');
        $query = DB::table('tasks')
            ->leftJoin('users as creator', 'tasks.created_by', '=', 'creator.id')
            ->select(
                'tasks.id',
                'tasks.name as task',
                'tasks.description',
                'tasks.status',
                'tasks.start_date',
                'tasks.end_date',
                'tasks.created_at',
                'tasks.updated_at',
                'creator.name as creator_name'
            );
        $query->where(function ($q) use ($userId) 
        {
            $q->where('tasks.created_by', $userId)
            ->orWhereIn('tasks.id', function ($subQuery) use ($userId) 
            {
                $subQuery->select('task_id')
                        ->from('task_user')
                        ->where('user_id', $userId);
            });
        });

        if ($request->status && $request->status !== 'all') 
        {
            if ($request->status === 'today') 
            {
                $query->whereDate('tasks.end_date', Carbon::today());
            } 
            else 
            {
                $query->where('tasks.status', $request->status);
            }
        }

        if ($request->search) 
        {
            $query->where(function ($q) use ($request) 
            {
                $q->where('tasks.name', 'like', '%' . $request->search . '%')
                ->orWhere('tasks.description', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->startDate) 
        {
            $query->whereDate('tasks.start_date', '>=', $request->startDate);
        }

        if ($request->endDate) 
        {
            $query->whereDate('tasks.end_date', '<=', $request->endDate);
        }
        $tasks = $query->orderBy('tasks.created_at', 'desc')
                    ->paginate($request->limit ?? 10);
        $taskIds = $tasks->pluck('id')->toArray();
        $assignedMembers = DB::table('task_user')
            ->join('users', 'task_user.user_id', '=', 'users.id')
            ->whereIn('task_user.task_id', $taskIds)
            ->select('task_user.task_id', 'users.name as user_name')
            ->get()
            ->groupBy('task_id');

        $tasks->setCollection(
            $tasks->getCollection()->map(function ($task) use ($assignedMembers) 
            {
                $task->deadLineDate = $task->end_date ? Carbon::parse($task->end_date)->format('Y-m-d') : null;
                $task->deadLineTime = $task->end_date ? Carbon::parse($task->end_date)->format('H:i') : null;

                if (isset($assignedMembers[$task->id])) 
                {
                    $task->assigned_users   = $assignedMembers[$task->id]->pluck('user_name')->toArray();
                    $task->assign_user_name = implode(', ', $task->assigned_users);
                } 
                else 
                {
                    $task->assigned_users   = [];
                    $task->assign_user_name = null;
                }

                return $task;
            })
        );
        $statsQuery = DB::table('tasks')
            ->where(function ($q) use ($userId) 
            {
                $q->where('tasks.created_by', $userId)
                ->orWhereIn('tasks.id', function ($subQuery) use ($userId) 
                {
                    $subQuery->select('task_id')
                            ->from('task_user')
                            ->where('user_id', $userId);
                });
            });

        $stats = [
            'totalTasks'      => (clone $statsQuery)->count(),
            'completedTasks'  => (clone $statsQuery)->where('status', 'completed')->count(),
            'inProgressTasks' => (clone $statsQuery)->where('status', 'in_progress')->count(),
            'pendingTasks'    => (clone $statsQuery)->where('status', 'pending')->count(),
            'overdueTasks'    => (clone $statsQuery)
                                ->where('status', '!=', 'completed')
                                ->where('end_date', '<', now())
                                ->count(),
            'todayTasks'      => (clone $statsQuery)
                                ->whereDate('end_date', Carbon::today())
                                ->count(),
        ];

        $priorities  = ['low' => 'Low', 'medium' => 'Medium', 'high' => 'High', 'urgent' => 'Urgent'];
        $statuses    = ['pending', 'in_progress', 'completed'];
        $teamMembers = DB::table('users')->select('id', 'name')->get();
        $users       = DB::table('users')->get(['id', 'name', 'role']);

        if ($request->ajax()) 
        {
            return response()->json([
                'tasks'   => $tasks,
                'hasMore' => $tasks->hasMorePages()
            ]);
        }

        return view('mobile.tasks', [
            'tasks'          => $tasks,
            'assignedMembers'=> $assignedMembers,
            'users'          => $users,
            'totalTasks'     => $stats['totalTasks'],
            'completedTasks' => $stats['completedTasks'],
            'inProgressTasks'=> $stats['inProgressTasks'],
            'pendingTasks'   => $stats['pendingTasks'],
            'todayTasks'     => $stats['todayTasks'],
            'priorities'     => $priorities,
            'statuses'       => $statuses,
            'teamMembers'    => $teamMembers,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'task' => 'required|string|max:255',
            'user_id' => 'required|exists:users,id',
            'deadLineDate' => 'required|date',
            'deadLineTime' => 'nullable|date_format:H:i',
        ]);

        if ($validator->fails()) 
        {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        try 
        {
            $endDateTime = Carbon::parse($request->deadLineDate . ' ' . ($request->deadLineTime ?? '00:00'));
            $taskId = DB::table('tasks')->insertGetId([
                'name' => $request->task,
                'description' => $request->description ?? '',
                'created_by' => Session::get('user_id'),
                'start_date' => now(),
                'end_date' => $endDateTime,
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            DB::table('task_user')->insert([
                'task_id' => $taskId,
                'user_id' => $request->user_id,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Task created successfully!'
            ]);
        } 
        catch (\Exception $e) 
        {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create task: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'task' => 'required|string|max:255',
            'user_id' => 'required|exists:users,id',
            'deadLineDate' => 'required|date',
            'deadLineTime' => 'nullable|date_format:H:i',
        ]);

        if ($validator->fails()) 
        {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        try 
        {
            $endDateTime = Carbon::parse($request->deadLineDate . ' ' . ($request->deadLineTime ?? '00:00'));

            DB::table('tasks')->where('id', $id)->update([
                'name' => $request->task,
                'end_date' => $endDateTime,
                'updated_at' => now(),
            ]);
            DB::table('task_user')->where('task_id', $id)->delete();
            DB::table('task_user')->insert([
                'task_id' => $id,
                'user_id' => $request->user_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Task updated successfully!'
            ]);
        } 
        catch (\Exception $e) 
        {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update task: ' . $e->getMessage()
            ], 500);
        }
    }

    public function complete(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'remarks' => 'required|string|max:500',
        ]);

        if ($validator->fails()) 
        {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        try 
        {
            DB::table('tasks')->where('id', $id)->update([
                'status' => 'completed',
                'updated_at' => now(),
            ]);

            DB::table('task_comment')->insert([
                'task_id' => $id,
                'comment' => $request->remarks,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Task marked as completed!'
            ]);
        } 
        catch (\Exception $e) 
        {
            return response()->json([
                'success' => false,
                'message' => 'Failed to complete task: ' . $e->getMessage()
            ], 500);
        }
    }

    public function allocate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'task_ids' => 'required|string',
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) 
        {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        try 
        {
            $taskIds = explode(',', $request->task_ids);
            foreach ($taskIds as $taskId) 
            {
                DB::table('task_user')->updateOrInsert(
                    ['task_id' => $taskId],
                    [
                        'user_id' => $request->user_id,
                        'updated_at' => now(),
                    ]
                );
            }

            return response()->json([
                'success' => true,
                'message' => 'Tasks allocated successfully!'
            ]);
        } 
        catch (\Exception $e) 
        {
            return response()->json([
                'success' => false,
                'message' => 'Failed to allocate tasks: ' . $e->getMessage()
            ], 500);
        }
    }
}
