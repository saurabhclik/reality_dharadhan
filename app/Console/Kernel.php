<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;
class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('fetch:housing-leads')->everyMinute();
        $schedule->command('delete:old-shared-links')->daily();
        $schedule->command('notify:events')->everyMinute();
        $schedule->command('notify:task-due')->dailyAt('09:00');
        $schedule->command('sync:facebook-leads')->everyFifteenMinutes();
        $schedule->command('facebook:fetch-campaigns')->hourly();
        $schedule->call(function () 
        {
            try 
            {
                $job = new \App\Jobs\SyncFacebookLeads();
                $job->handle();
            } 
            catch (\Exception $e) 
            {
                Log::error('Facebook leads sync failed: ' . $e->getMessage(), [
                    'trace' => $e->getTraceAsString()
                ]);
            }
        })
        ->name('sync-facebook-leads')
        ->everyFifteenMinutes()
        ->withoutOverlapping()
        ->sendOutputTo(storage_path('logs/facebook-sync.log'));
        $schedule->command('facebook:refresh-token')->dailyAt('02:00');
        $schedule->command('report:dayend')->dailyAt('00:00');
        $schedule->command('mis:auto-assign-targets')->everyMinute();
        $schedule->command('notify:events')->everyMinute();
        $schedule->command('tasks:repeat')->everyMinute();
        $schedule->command('trial:status-update')->everyMinute();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected $commands = [
        \App\Console\Commands\FetchHousingLeads::class,
        \App\Console\Commands\DeleteOldSharedLinks::class,
        \App\Console\Commands\TaskDueNotification::class,
        \App\Console\Commands\NotifyEvents::class,
        \App\Console\Commands\SyncFacebookLeadsCommand::class,
        \App\Console\Commands\FetchFacebookCampaigns::class,
        \App\Console\Commands\RefreshFacebookToken::class,
        \App\Console\Commands\GenerateDayEndReport::class,
        \App\Console\Commands\GenerateRepeatingTasks::class,
        \App\Console\Commands\AutoAssignMISTargets::class,
        \App\Console\Commands\TrialStatusUpdater::class,
    ];
}
