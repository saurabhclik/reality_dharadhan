<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http; 
class NotifyEvents extends Command
{
    protected $signature = 'notify:events';
    protected $description = 'Send notifications for tasks and lead events (due, follow-up, allocation, etc.)';

    public function handle()
    {
        try 
        {
            $now = Carbon::now();
            $today = $now->toDateString();
            $tomorrow = Carbon::tomorrow()->toDateString();
            DB::beginTransaction();
            $tasks = DB::table('tasks')
                ->join('task_user', 'tasks.id', '=', 'task_user.task_id')
                ->where('tasks.status', 'pending')     
                ->whereDate('tasks.end_date', '=', $tomorrow) 
                ->select('tasks.id', 'tasks.name', 'tasks.description', 'task_user.user_id', 'tasks.end_date')
                ->get();

            foreach ($tasks as $task) 
            {
                $this->insertNotification($task->user_id, 'Task Ending Soon: ' . $task->name, $task->description ?? 'Task is ending soon.');
            }
            $leads = DB::table('leads')
                ->whereNotNull('remind_date')
                ->whereNotNull('remind_time')
                ->whereRaw("TIMESTAMPDIFF(MINUTE, ?, CONCAT(remind_date, ' ', remind_time)) = 10", [$now])
                ->select('id', 'name', 'user_id', 'remind_date', 'remind_time')
                ->get();

            foreach ($leads as $lead) 
            {
                $this->insertNotification($lead->user_id, 'Follow-up Reminder', 'Follow-up scheduled for lead: ' . $lead->name);
            }
            $newLeads = DB::table('leads')
                ->where('created_at', '>=', $now->copy()->startOfDay())
                ->where('created_at', '<=', $now->copy()->endOfDay())
                ->select('id', 'name', 'user_id')
                ->get();

            foreach ($newLeads as $lead) 
            {
                $this->insertNotification($lead->user_id, 'New Lead Added', 'Lead ' . $lead->name . ' was added today.');
            }
            $allocatedLeads = DB::table('leads')
                ->whereDate('allocated_date', $today)
                ->where('is_allocated', 1)
                ->select('id', 'name', 'user_id')
                ->get();

            foreach ($allocatedLeads as $lead) 
            {
                $this->insertNotification($lead->user_id, 'Lead Allocated', 'Lead ' . $lead->name . ' has been allocated.');
            }
            $assignedTasks = DB::table('tasks')
                ->join('task_user', 'tasks.id', '=', 'task_user.task_id')
                ->where('tasks.created_at', '>=', $now->copy()->startOfDay())
                ->where('tasks.created_at', '<=', $now->copy()->endOfDay())
                ->select('tasks.id', 'tasks.name', 'task_user.user_id')
                ->get();

            foreach ($assignedTasks as $task) 
            {
                $this->insertNotification($task->user_id, 'Task Assigned', 'You have been assigned task: ' . $task->name);
            }
            $completedTasks = DB::table('tasks')
                ->join('task_user', 'tasks.id', '=', 'task_user.task_id')
                ->where('tasks.status', 'completed')
                ->where('tasks.updated_at', '>=', $now->copy()->startOfDay())
                ->where('tasks.updated_at', '<=', $now->copy()->endOfDay())
                ->select('tasks.id', 'tasks.name', 'task_user.user_id')
                ->get();

            foreach ($completedTasks as $task) 
            {
                $this->insertNotification($task->user_id, 'Task Completed', 'Task ' . $task->name . ' has been marked as completed.');
            }

            DB::commit();
            $this->info('Notifications inserted for all events without duplicates.');

        } 
        catch (\Exception $e) 
        {
            DB::rollBack();
            Log::error('Notification scheduler error: ' . $e->getMessage());
            $this->error('Error: ' . $e->getMessage());
        }

        return 0;
    }

    private function insertNotification($userId, $title, $message)
    {
        $exists = DB::table('user_notification')
            ->where('userId', $userId)
            ->where('notification_title', $title)
            ->where('message', $message)
            ->whereDate('CreatedDate', Carbon::now()->toDateString())
            ->exists();

        if (!$exists) 
        {
            DB::table('user_notification')->insert([
                'userId' => $userId,
                'notification_title' => $title,
                'message' => $message,
                'ack' => 0,
                'CreatedDate' => now()
            ]);

            $this->sendFcmNotification($userId, $title, $message);
        }
    }

    private function sendFcmNotification($userId, $title, $message)
    {
        $token = DB::table('users')
            ->where('id', $userId)
            ->whereNotNull('fcm_token')
            ->value('fcm_token');

        if (!$token) 
        {
            return;
        }

        $serverKey = $this->getFirebaseServerKey(); 
        if (!$serverKey) 
        {
            \Log::warning("No Firebase server key found in integration_settings");
            return;
        }

        $payload = [
            'to' => $token,
            'notification' => [
                'title' => $title,
                'body' => $message,
                'sound' => 'default'
            ],
            'data' => [
                'userId' => $userId,
                'action' => 'mis_point'
            ],
            'priority' => 'high'
        ];

        try 
        {
            \Illuminate\Support\Facades\Http::withHeaders([
                'Authorization' => 'key=' . $serverKey,
                'Content-Type' => 'application/json',
            ])->post('https://fcm.googleapis.com/fcm/send', $payload);
        }
        catch (\Exception $e) 
        {
            \Log::error("Error sending FCM to user $userId: " . $e->getMessage());
        }
    }

    private function getFirebaseServerKey()
    {
        $firebaseSettings = DB::table('integration_settings')
            ->where('integration_type', 'firebase')
            ->where('status', 'active')
            ->orderBy('id', 'desc')
            ->first();

        if (!$firebaseSettings)
        {
            return null;
        }

        $settings = json_decode($firebaseSettings->settings, true);
        return $settings['api_key'] ?? null;
    }
}
