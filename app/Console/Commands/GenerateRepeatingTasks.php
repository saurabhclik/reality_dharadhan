<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class GenerateRepeatingTasks extends Command
{
    protected $signature = 'tasks:repeat';
    protected $description = 'Generate the next repeating task based on repeat_interval and repeat_count';

    public function handle()
    {
        try 
        {
            DB::beginTransaction();

            $today = Carbon::today();
            $tasks = DB::table('tasks')
                ->where('repeat_interval', '!=', 'none')
                ->where('repeat_count', '>', 0)
                ->orderBy('id', 'asc')
                ->get();

            foreach ($tasks as $task) 
            {
                $taskEnd = Carbon::parse($task->end_date);
                if ($today->lt($taskEnd)) 
                {
                    continue;
                }

                $assignedUsers = DB::table('task_user')
                    ->where('task_id', $task->id)
                    ->pluck('user_id')
                    ->toArray();
                switch ($task->repeat_interval) 
                {
                    case 'daily':
                        $nextStart = $taskEnd->copy()->addDay();
                        $nextEnd = $nextStart->copy();
                        break;
                    case 'weekly':
                        $nextStart = $taskEnd->copy()->addWeek();
                        $nextEnd = $nextStart->copy();
                        break;
                    case 'monthly':
                        $nextStart = $taskEnd->copy()->addMonth();
                        $nextEnd = $nextStart->copy();
                        break;
                    default:
                        continue 2;
                }

                $nextRepeatCount = $task->repeat_count - 1;
                $exists = DB::table('tasks')
                    ->where('name', $task->name)
                    ->where('start_date', $nextStart->toDateString())
                    ->where('end_date', $nextEnd->toDateString())
                    ->exists();

                if (!$exists && $nextRepeatCount >= 0)
                {
                    $newTaskId = DB::table('tasks')->insertGetId([
                        'user_id' => $task->user_id,
                        'name' => $task->name,
                        'description' => $task->description,
                        'start_date' => $nextStart->toDateString(),
                        'end_date' => $nextEnd->toDateString(),
                        'priority' => $task->priority,
                        'status' => 'pending',
                        'budget' => $task->budget,
                        'tags' => $task->tags,
                        'repeat_interval' => $task->repeat_interval,
                        'repeat_count' => $nextRepeatCount,
                        'created_by' => $task->created_by,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    if (!empty($assignedUsers)) 
                    {
                        $taskMembers = [];
                        foreach ($assignedUsers as $userId) 
                        {
                            $taskMembers[] = [
                                'task_id' => $newTaskId,
                                'user_id' => $userId,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                        }
                        DB::table('task_user')->insert($taskMembers);
                    }
                    DB::table('tasks')->where('id', $task->id)->update([
                        'repeat_count' => 0,
                        'repeat_interval' => 'none',
                        'updated_at' => now(),
                    ]);
                }
            }

            DB::commit();
            $this->info('Repeating tasks generated successfully (daily/weekly/monthly).');

        } 
        catch (\Exception $e) 
        {
            DB::rollBack();
            Log::error('Error generating repeated tasks: ' . $e->getMessage());
            $this->error('Failed to generate repeating tasks: ' . $e->getMessage());
        }

        return 0;
    }
}
