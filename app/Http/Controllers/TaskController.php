<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use App\Services\TaskService;
use Flasher\Laravel\Facade\Flasher;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    protected $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    public function create($id = null)
    {
        $activeFeatures = Session::get('active_features', []);
        if(in_array('task_management', $activeFeatures))
        {
            $data = $this->taskService->create($id);

            if (isset($data['error'])) 
            {
                Flasher::addError($data['error']);
                return redirect()->route('task.list');
            }
            $task_projects = DB::table('task_projects')->where('status', '!=', 'completed')->get();
            return view('task.create', [...$data, 'task_projects' => $task_projects]);
        }
        else
        {
            abort(404);
        }
    }

    public function store(Request $request, $id = null)
    {
        try 
        {
            if ($request->filled('start_date')) 
            {
                $start_date = Carbon::createFromFormat('d/m/Y', $request->start_date)->format('Y-m-d');
                $request->merge(['start_date' => $start_date]);
            }

            if ($request->filled('end_date')) 
            {
                $end_date = Carbon::createFromFormat('d/m/Y', $request->end_date)->format('Y-m-d');
                $request->merge(['end_date' => $end_date]);
            }
        } 
        catch (\Exception $e) 
        {
            Flasher::addError('Invalid date format. Please use dd/mm/yyyy.');
            return redirect()->back()->withInput();
        }

        $userRole = Session::get('user_type'); 
        $userId = Session::get('user_id');

        if (in_array($userRole, ['super_admin', 'divisional_head'])) 
        {
            $teamMemberRule = ['team_members' => 'required|array'];
        } 
        else 
        {
            $request->merge(['team_members' => [$userId]]);
            $teamMemberRule = ['team_members' => 'nullable|array'];
        }

        $validator = Validator::make($request->all(), array_merge([
            'taskname' => 'required|string|max:255',
            'description' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'priority' => 'required|in:low,medium,high,urgent',
            'task_project_id' => 'nullable|exists:task_projects,id', 
            'budget' => 'nullable|numeric|min:0',
            'tags' => 'nullable|string',
            'repeat_interval' => 'nullable|string',
            'repeat_count' => 'nullable|numeric|min:0',
            'team_members.*' => 'required|exists:users,id',
            'member_files.*' => 'nullable|file|mimes:jpg,jpeg,png,gif,pdf,doc,docx|max:2048'
        ], $teamMemberRule));

        if ($validator->fails()) 
        {
            foreach ($validator->errors()->all() as $error) 
            {
                Flasher::addError($error);
            }
            return redirect()->back()->withInput();
        }

        DB::beginTransaction();
        $description = strip_tags($request->description);

        try 
        {
            $taskData = [
                'name' => $request->taskname,
                'user_id' => $userId,
                'description' => $description,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'priority' => $request->priority,
                'task_project_id' => $request->task_project_id ?: null, 
                'status' => 'pending',
                'budget' => $request->budget ?? null,
                'repeat_interval' => $request->repeat_interval,
                'repeat_count' => $request->repeat_count,
                'tags' => $request->tags,
                'updated_at' => now(),
            ];
            $taskData['task_type'] = $request->filled('task_project_id') ? 'project' : 'individual';

            if (!$id) 
            {
                $taskData['created_by'] = $userId;
                $taskData['created_at'] = now();
                $taskId = DB::table('tasks')->insertGetId($taskData);
                $message = 'Task created successfully!';
            } 
            else 
            {
                DB::table('tasks')->where('id', $id)->update($taskData);
                $taskId = $id;
                $message = 'Task updated successfully!';
            }

            if (!empty($request->team_members)) 
            {
                if ($id) 
                {
                    DB::table('task_user')->where('task_id', $id)->delete();
                }

                $taskMembers = [];

                foreach ($request->team_members as $index => $memberId) 
                {
                    $filePath = null;
                    $fileName = null;
                    $fileType = null;
                    $isRemoved = isset($request->removed_files[$index]) && $request->removed_files[$index];

                    if (isset($request->member_files[$index])) 
                    {
                        if ($request->member_files[$index]->isValid()) 
                        {
                            $file = $request->member_files[$index];
                            $filePath = $file->store("tasks/$taskId", 'public');
                            $fileName = $file->getClientOriginalName();
                            $fileType = $file->getClientMimeType();

                            if (isset($request->existing_files[$index])) 
                            {
                                Storage::disk('public')->delete($request->existing_files[$index]);
                            }
                        } 
                        elseif (isset($request->existing_files[$index]) && !$isRemoved) 
                        {
                            $filePath = $request->existing_files[$index];
                            $fileName = $request->existing_file_names[$index] ?? null;
                            $fileType = $request->existing_file_types[$index] ?? null;
                        }
                    } 
                    elseif (isset($request->existing_files[$index]) && !$isRemoved) 
                    {
                        $filePath = $request->existing_files[$index];
                        $fileName = $request->existing_file_names[$index] ?? null;
                        $fileType = $request->existing_file_types[$index] ?? null;
                    }

                    $taskMembers[] = [
                        'task_id' => $taskId,
                        'user_id' => $memberId,
                        'file_path' => $filePath,
                        'file_name' => $fileName,
                        'file_type' => $fileType,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }

                if ($id) 
                {
                    $currentFiles = array_filter(array_column($taskMembers, 'file_path'));
                    $allExistingFiles = Storage::disk('public')->files("tasks/$taskId");
                    foreach ($allExistingFiles as $file) 
                    {
                        if (!in_array($file, $currentFiles)) 
                        {
                            Storage::disk('public')->delete($file);
                        }
                    }
                }

                if (!empty($taskMembers)) 
                {
                    DB::table('task_user')->insert($taskMembers);
                }
            }

            DB::commit();
            Flasher::addSuccess($message);
            return redirect()->route('task.list');
        } 
        catch (\Exception $e) 
        {
            DB::rollBack();
            Flasher::addError('Failed to save task: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function list(Request $request)
    {
        $activeFeatures = Session::get('active_features', []);
        if(in_array('task_management', $activeFeatures))
        {
            $taskQuery = DB::table('tasks')
                ->leftJoin('users as creator', 'tasks.created_by', '=', 'creator.id')
                ->select('tasks.*', 'creator.name as creator_name')
                ->orderBy('tasks.created_at', 'desc');

            if ($request->task_type && $request->task_type !== 'all') 
            {
                if ($request->task_type === 'project') 
                {
                    $taskQuery->whereNotNull('tasks.task_project_id');
                } 
                elseif ($request->task_type === 'individual') 
                {
                    $taskQuery->whereNull('tasks.task_project_id');
                }
            }
            if ($request->status_filter) 
            {
                if ($request->status_filter === 'today') 
                {
                    $taskQuery->whereDate('tasks.end_date', Carbon::today());
                } 
                elseif ($request->status_filter === 'overdue') 
                {
                    $taskQuery->where('tasks.status', '!=', 'completed')
                        ->where('tasks.end_date', '<', Carbon::today());
                }
                else 
                {
                    $taskQuery->where('tasks.status', $request->status_filter);
                }
            }

            if ($request->project_filter && $request->project_filter !== 'all') 
            {
                $taskQuery->where('tasks.task_project_id', $request->project_filter);
            }
            if ($request->task_search) 
            {
                $taskQuery->where('tasks.name', 'like', '%' . $request->task_search . '%');
            }

            $tasks = $taskQuery->paginate(20);

            $projectQuery = DB::table('task_projects')
                ->orderBy('created_at', 'desc');

            if ($request->project_search) 
            {
                $projectQuery->where('name', 'like', '%' . $request->project_search . '%');
            }

            $task_projects = $projectQuery->paginate(20, ['*'], 'project_page');

            $taskIds = $tasks->pluck('id')->toArray();
            $assignedMembers = DB::table('task_user')
                ->join('users', 'task_user.user_id', '=', 'users.id')
                ->whereIn('task_user.task_id', $taskIds)
                ->select('task_user.*', 'users.name as user_name')
                ->get()
                ->groupBy('task_id');
            $taskFiles = DB::table('task_user')
                ->whereIn('task_id', $taskIds)
                ->whereNotNull('file_path')
                ->get()
                ->groupBy('task_id');

            $task_all_comments = DB::table('task_comment')
                ->whereIn('task_id', $taskIds)
                ->whereNotNull('comment')
                ->get()
                ->groupBy('task_id');

            $task_owner = Session::get('user_id');
            
            $projectTasksStats = [];
            foreach ($task_projects as $project) 
            {
                $totalTasks = DB::table('tasks')->where('task_project_id', $project->id)->count();
                $completedTasks = DB::table('tasks')->where('task_project_id', $project->id)->where('status', 'completed')->count();
                $projectTasksStats[$project->id] = [
                    'total' => $totalTasks,
                    'completed' => $completedTasks
                ];
            }
            $projectTasksCount = DB::table('tasks')->whereNotNull('task_project_id')->count();
            $individualTasksCount = DB::table('tasks')->whereNull('task_project_id')->count();

            $task_all_comments = DB::table('task_comment')
                ->whereIn('task_id', $taskIds)
                ->whereNotNull('comment')
                ->get()
                ->groupBy('task_id');
                
            $stats = [
                'totalTasks' => DB::table('tasks')->count(),
                'completedTasks' => DB::table('tasks')->where('status', 'completed')->count(),
                'inProgressTasks' => DB::table('tasks')->where('status', 'in_progress')->count(),
                'pendingTasks' => DB::table('tasks')->where('status', 'pending')->count(),
                'overdueTasks' => DB::table('tasks')
                    ->where('status', '!=', 'completed')
                    ->where('end_date', '<', now())
                    ->count(),
                'task_all_comments' => $task_all_comments,
                'todayTasks' => DB::table('tasks')
                    ->whereDate('end_date', Carbon::today())
                    ->count()
            ];

            
            return view('task.list', [
                'tasks' => $tasks,
                'assignedMembers' => $assignedMembers,
                'task_all_comments' => $task_all_comments,
                'taskFiles' => $taskFiles,
                'user_type' => Session::get('user_type'),
                'totalTasks' => $stats['totalTasks'],
                'completedTasks' => $stats['completedTasks'],
                'inProgressTasks' => $stats['inProgressTasks'],
                'pendingTasks' => $stats['pendingTasks'], 
                'todayTasks' => $stats['todayTasks'],
                'overdueTasks' => $stats['overdueTasks'],
                'task_owner' => $task_owner,
                'task_all_comments' => $task_all_comments,
                'task_projects' => $task_projects,
                'projectTasksCount' => $projectTasksCount,
                'individualTasksCount' => $individualTasksCount,
                'projectTasksStats' => $projectTasksStats
            ]);
        }
        else
        {
            abort(404);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try 
        {
            $files = DB::table('task_user')
                ->where('task_id', $id)
                ->whereNotNull('file_path')
                ->pluck('file_path');

            foreach ($files as $file) 
            {
                Storage::disk('public')->delete($file);
            }
            DB::table('task_user')->where('task_id', $id)->delete();
            DB::table('task_comment')->where('task_id', $id)->delete();
            DB::table('tasks')->where('id', $id)->delete();

            DB::commit();
            return response()->json([
                'status' => 200,
                'message' => 'Task deleted successfully',
                'data' => ''
            ]);
        } 
        catch (\Exception $e) 
        {
            DB::rollBack();
            return response()->json([
                'status' => 500,
                'message' => 'Failed to delete task: '.$e->getMessage(),
                'data' => ''
            ]);
        }
    }

    public function updateStatus(Request $request, $id)
    {
        // echo '<pre>'; print_r($_POST); exit;
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,in_progress,completed',
            'comments' => 'nullable|string',
            'file.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,bmp,svg,webp,pdf,txt,csv|max:1024'
        ]);

        if ($validator->fails()) 
        {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first(),
                'data' => ''
            ]);
        }

        try 
        {
            $taskMembers = [];
            if ($request->hasFile('file')) 
            {
                foreach ($request->file('file') as $file) 
                {
                    $filePath = $file->store("tasks/$id", 'public');
                    $fileName = $file->getClientOriginalName();
                    $fileType = $file->getClientMimeType();
                    
                    $taskMembers[] = [
                        'task_id' => $id,
                        'user_id' => session()->get('user_id'),
                        'file_path' => $filePath,
                        'file_name' => $fileName,
                        'file_type' => $fileType,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }

                if (!empty($taskMembers)) 
                {
                    DB::table('task_user')->insert($taskMembers);
                }
            }
            
            if (!empty($request->comments)) 
            {
                DB::table('task_comment')->insert([
                    'task_id' => $id,
                    'comment' => $request->comments,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
            
            DB::table('tasks')->where('id', $id)->update([
                'status' => $request->status,
                'updated_at' => now()
            ]);

            return response()->json([
                'status' => 200,
                'message' => 'Status updated successfully',
                'data' => '',
            ]);
        } 
        catch (\Exception $e) 
        {
            return response()->json([
                'success' => 500,
                'message' => 'Failed to update status: ' . $e->getMessage(),
                'data' => ''
            ]);
        }
    }

    public function task_project_store(Request $request, $id = null)
    {   
        $validator = Validator::make($request->all(), [
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|in:planning,active,on_hold,completed',
            'priority'    => 'nullable|in:low,medium,high',
        ]);

        if ($validator->fails()) 
        {
            foreach ($validator->errors()->all() as $error) 
            {
                Flasher::addError($error);
            }
            return redirect()->back()->withInput();
        }

        try 
        {
            DB::beginTransaction();
            $data = [
                'name'        => $request->name,
                'description' => $request->description,
                'status'      => $request->status ?? 'planning',
                'priority'    => $request->priority ?? 'medium',
                'updated_at'  => now(),
            ];

            if ($id) 
            {
                DB::table('task_projects')->where('id', $id)->update($data);
                $message = 'Project updated successfully!';
            } 
            else 
            {
                $data['created_by'] = session('user_id');
                $data['created_at'] = now();
                DB::table('task_projects')->insert($data);
                $message = 'Project created successfully!';
            }

            DB::commit();
            Flasher::addSuccess($message);
            return redirect()->back();

        } 
        catch (\Exception $e) 
        {
            DB::rollBack();
            Flasher::addError('Failed to save project: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function task_project_update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|in:planning,active,on_hold,completed',
            'priority'    => 'nullable|in:low,medium,high',
        ]);

        if ($validator->fails()) 
        {
            return response()->json([
                'success' => 422,
                'message' => $validator->errors()->first(),
                'data' => ''
            ]);
        }

        try 
        {
            DB::table('task_projects')->where('id', $id)->update([
                'name'        => $request->name,
                'description' => $request->description,
                'status'      => $request->status,
                'priority'    => $request->priority,
                'updated_at'  => now(),
            ]);

            return response()->json([
                'status' => 200,
                'message' => 'Project updated successfully!',
                'data' => ''
            ]);

        } 
        catch (\Exception $e) 
        {
            return response()->json([
                'status' => 500,
                'message' => 'Failed to update project: ' . $e->getMessage(),
                'data' => ''
            ]);
        }
    }

    public function updateProjectStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:planning,active,on_hold,completed',
        ]);

        if ($validator->fails()) 
        {
            return response()->json([
                'status' => 422,
                'message' => $validator->errors()->first(),
                'data' => ''
            ]);
        }
        try 
        {
            DB::table('task_projects')->where('id', $id)->update([
                'status' => $request->status,
                'updated_at' => now(),
            ]);

            return response()->json([
                'status' => 200,
                'message' => 'Project status updated successfully!',
                'data' => ''
            ]);

        } 
        catch (\Exception $e) 
        {
            return response()->json([
                'success' => 500,
                'message' => 'Failed to update project status: ' . $e->getMessage(),
                'data' => ''
            ]);
        }
    }

    public function task_project_destroy($id)
    {
        DB::beginTransaction();
        try 
        {
            $tasksCount = DB::table('tasks')->where('task_project_id', $id)->count();
            
            if ($tasksCount > 0) 
            {
                return response()->json([
                    'status' => 422,
                    'message' => 'Cannot delete project. There are tasks associated with this project. Please reassign or delete those tasks first.',
                    'data' => ''
                ]);
            }

            DB::table('task_projects')->where('id', $id)->delete();

            DB::commit();
            return response()->json([
                'status' => 200,
                'message' => 'Project deleted successfully',
                'data' => ''
            ]);
        } 
        catch (\Exception $e) 
        {
            DB::rollBack();
            return response()->json([
                'status' => 500,
                'message' => 'Failed to delete project: '.$e->getMessage(),
                'data' => ''
            ]);
        }
    }
}