<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Exception;

class SyncCrmUsers extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'crm:fetch-users';

    /**
     * The console command description.
     */
    protected $description = 'Fetch users from Reality CRM API and log response';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $response = [];
        try 
        {
            $client = new Client([
                'base_uri' => 'https://external-crm.com/api/',
                'timeout'  => 10.0,
            ]);

            $raw = $client->request('GET', 'users', [
                'headers' => [
                    'Authorization' => 'Bearer YOUR_API_KEY',
                    'Accept'        => 'application/json',
                ],
                'query' => [
                ],
            ]);

            $data = json_decode($raw->getBody(), true);
            if ($raw->status === 200) 
            {
                $response = [
                    'status'  => 200,
                    'message' => 'User details fetched successfully',
                    'data'    => $data,
                ];
            } 
            else 
            {
                $response = [
                    'status'  => $raw->status,
                    'message' => $data['message'] ?? 'Something went wrong',
                    'data'    => $data['data'] ?? [],
                ];
            }

        } 
        catch (Exception $error) 
        {
            $response = [
                'status'  => 500,
                'message' => 'Something went wrong: ' . $error->getMessage(),
                'data'    => [],
            ];
        }
        Log::info('FetchExternalUsers Command Response', $response);
        $this->info(json_encode($response));
    }
}