<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Exception;

class TrialStatusUpdater extends Command
{
    protected $signature = 'trial:status-update';
    protected $description = 'Update trial statuses and activate/deactivate features';

    public function handle()
    {
        $now = Carbon::now('Asia/Kolkata');
        $this->info("=== TrialStatusUpdater started at {$now} ===");

        DB::beginTransaction();

        try {
            // 1️⃣ Expire trials whose end_date has passed
            $expiredTrials = DB::table('trials')
                ->where('status', '!=', 'expired')
                ->where('end_date', '<', $now)
                ->get();

            foreach ($expiredTrials as $trial) {
                $featureName = DB::table('software_features')->where('id', $trial->feature_id)->value('feature_name');

                $this->info("Expiring trial '{$trial->software_name}' for {$trial->client_name}, feature '{$featureName}'");

                DB::table('trials')->where('id', $trial->id)
                    ->update(['status' => 'expired', 'updated_at' => now()]);

                DB::table('software_features')->where('id', $trial->feature_id)
                    ->update(['status' => 'inactive', 'updated_at' => now()]);

                $this->createNotification(
                    $trial->client_name,
                    'Trial Expired',
                    "Trial '{$trial->software_name}' expired — feature '{$featureName}' deactivated for {$trial->client_name}"
                );
            }

            // 2️⃣ Activate trials: status = active and end_date >= now
            $activeTrials = DB::table('trials')
                ->where('status', 'active')
                ->where('end_date', '>=', $now)
                ->get();

            foreach ($activeTrials as $trial) {
                $featureName = DB::table('software_features')->where('id', $trial->feature_id)->value('feature_name');
                $startDate = Carbon::parse($trial->start_date, 'Asia/Kolkata');
                $endDate = Carbon::parse($trial->end_date, 'Asia/Kolkata');

                $this->info("Activating feature '{$featureName}' for active trial '{$trial->software_name}' for {$trial->client_name}");

                DB::table('software_features')->where('id', $trial->feature_id)
                    ->update([
                        'status' => 'active',
                        'activate_at' => $startDate,
                        'expires_at' => $endDate,
                        'updated_at' => now(),
                    ]);

                $this->createNotification(
                    $trial->client_name,
                    'Trial Feature Activated',
                    "Trial '{$trial->software_name}' — feature '{$featureName}' activated for {$trial->client_name}"
                );
            }

            // 3️⃣ Skip inactive or cancelled trials
            $skippedTrials = DB::table('trials')
                ->whereIn('status', ['inactive', 'cancelled'])
                ->get();

            foreach ($skippedTrials as $trial) {
                $featureName = DB::table('software_features')->where('id', $trial->feature_id)->value('feature_name');
                $this->info("Skipping trial '{$trial->software_name}' for {$trial->client_name} (status: {$trial->status}) — feature '{$featureName}' remains inactive");
            }

            DB::commit();
            $this->info("=== TrialStatusUpdater finished successfully at " . Carbon::now('Asia/Kolkata') . " ===");
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('TrialStatusUpdater error: ' . $e->getMessage());
            $this->error("TrialStatusUpdater failed: " . $e->getMessage());
        }

        return Command::SUCCESS;
    }

    private function createNotification($clientName, $title, $message)
    {
        $exists = DB::table('user_notification')
            ->where('notification_title', $title)
            ->where('message', $message)
            ->whereDate('CreatedDate', today())
            ->exists();

        if (!$exists) {
            DB::table('user_notification')->insert([
                'UserId'             => 1,
                'notification_title' => $title,
                'message'            => $message,
                'ack'                => 0,
                'CreatedDate'        => now(),
            ]);
        }
    }
}
