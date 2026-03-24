<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Jobs\SyncFacebookLeads;

class SyncFacebookLeadsCommand extends Command
{
    protected $signature = 'sync:facebook-leads';
    protected $description = 'Dispatch job to sync Facebook leads';

    public function handle()
    {
        $integration = DB::table('integration_settings')
            ->where('integration_type', 'facebook')
            ->where('status', 'active')
            ->first();

        if (!$integration) 
        {
            $this->error('No active Facebook integration found.');
            return 0;
        }

        if (!$integration->auto_sync) 
        {
            $this->info('Auto sync is disabled for Facebook integration. Job not dispatched.');
            return 0;
        }

        dispatch(new SyncFacebookLeads());
        $this->info('SyncFacebookLeads job dispatched successfully.');
        return 0;
    }
}
