<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DeleteOldSharedLinks extends Command
{
    protected $signature = 'delete:old-shared-links';
    protected $description = 'Delete shared_leads and shared_post_sale entries older than 8 days';

    public function handle()
    {
        try 
        {
            $thresholdDate = Carbon::now()->subDays(8);

            $deletedLeads = DB::table('shared_leads')
                ->where('created_at', '<', $thresholdDate)
                ->delete();

            $this->info("$deletedLeads shared lead(s) deleted that were older than 8 days.");

            $deletedPostSales = DB::table('shared_post_sale')
                ->where('created_at', '<', $thresholdDate)
                ->delete();

            $this->info("$deletedPostSales shared post-sale(s) deleted that were older than 8 days.");
        } 
        catch (\Exception $e) 
        {
            $this->error("Error: " . $e->getMessage());
        }

        return 0;
    }
}
