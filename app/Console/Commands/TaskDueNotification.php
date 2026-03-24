<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class TaskDueNotification extends Command
{
    protected $signature = 'notify:task-due'; 
    protected $description = 'Send notifications for tasks ending today or tomorrow';

    public function handle()
    {
        try 
        {
            $now = Carbon::now();
            $today = $now->toDateString();

            $tasks = DB::table('tasks')
                ->join('task_user', 'tasks.id', '=', 'task_user.task_id')
                ->where('tasks.status', 'pending')
                ->whereRaw('DATEDIFF(tasks.end_date, ?) BETWEEN 0 AND 1', [$today])
                ->select('tasks.id', 'tasks.name', 'tasks.description', 'task_user.user_id', 'tasks.end_date')
                ->get();

            if ($tasks->isEmpty()) 
            {
                $this->info('No tasks ending today or tomorrow.');
                return 0;
            }

            DB::beginTransaction();

            foreach ($tasks as $task)
            {
                $datePart = Carbon::parse($task->end_date)->format('Y-m-d');
                $timePart = now()->format('H:i:s');
                $createdDate = $datePart . ' ' . $timePart;

                DB::table('user_notification')->insert([
                    'userId' => $task->user_id,
                    'notification_title' => 'Task Ending Soon: ' . $task->name,
                    'message' => $task->description ?? 'Task is ending soon.',
                    'ack' => 0,
                    'CreatedDate' => $createdDate,
                ]);
            }

            DB::commit();
            $this->info('Notifications inserted for tasks ending today or tomorrow.');

        } 
        catch (\Exception $e) 
        {
            DB::rollBack();
            Log::error('Task scheduler error: ' . $e->getMessage());
            $this->error('Error: ' . $e->getMessage());
        }

        return 0;
    }
}

