<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\WelcomeMessageService;
use Illuminate\Support\Facades\Log;

class SendWelcomeMessages extends Command
{
    protected $signature = 'send:welcome-messages
        {--hours=24 : Check leads from last X hours (0 = all)}
        {--limit=50 : Maximum leads to process}
        {--all : Send to ALL leads}
        {--dry-run : Test without sending messages}';

    protected $description = 'Send automatic welcome messages to new exhibition leads';

    public function handle(WelcomeMessageService $welcomeService)
    {
        $this->info('Starting welcome message processing...');

        $options = [
            'hours'   => $this->option('all') ? 0 : (int) $this->option('hours'),
            'limit'   => (int) $this->option('limit'),
            'dryRun'  => (bool) $this->option('dry-run'),
        ];

        $this->info(
            $options['hours'] === 0
                ? 'Mode: ALL leads'
                : 'Mode: Last ' . $options['hours'] . ' hours'
        );

        if ($options['dryRun']) 
        {
            $this->info('⚠️ DRY RUN MODE');
        }

        $result = $welcomeService->sendWelcomeToNewLeads($options);

        if (!$result['success']) 
        {
            $this->error('❌ ' . $result['message']);
            return Command::FAILURE;
        }

        $this->info('✅ ' . $result['message']);

        if (!empty($result['results']['details'])) 
        {
            $this->table(
                ['Lead ID', 'Name', 'Phone', 'Status', 'Message'],
                array_map(fn ($d) => [
                    $d['lead_id'],
                    $d['lead_name'],
                    $d['phone'],
                    $d['success'] ? 'Sent' : 'Failed',
                    $d['message']
                ], $result['results']['details'])
            );
        }

        Log::info('Welcome message execution summary', $result);

        return Command::SUCCESS;
    }
}