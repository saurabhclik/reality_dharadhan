<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
class TaskService
{
    public function create(?int $id = null): array
    {
        $task = $id ? DB::table('tasks')->where('id', $id)->first() : null;
        if ($id && !$task) 
        {
            return ['error' => 'Task not found'];
        }

        $teamMembers = DB::table('users')->get(['id', 'name']);

        $assignedMembers = $id
            ? DB::table('task_user')->where('task_id', $id)->get()
            : collect(); 
        $user_type = Session::get('user_type');
        return [
            'task' => $task,
            'teamMembers' => $teamMembers,
            'assignedMembers' => $assignedMembers,
            'priorities' => ['low', 'medium', 'high', 'urgent'],
            'user_type' => $user_type,
            'status' => ['pending', 'in_progress', 'completed']
        ];
    }
}
