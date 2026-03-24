<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {

    }

    public function boot()
    {
        $integration = DB::table('integration_settings')
            ->where('integration_type', 'gmail')
            ->first();

        if ($integration && $integration->status === 'active') 
        {
            $settings = json_decode($integration->settings, true);
            Config::set('mail.mailers.smtp.transport', 'smtp');
            Config::set('mail.mailers.smtp.host', $settings['mail_host'] ?? 'smtp.gmail.com');
            Config::set('mail.mailers.smtp.port', (int) ($settings['mail_port'] ?? 587));
            Config::set('mail.mailers.smtp.encryption', $settings['mail_encryption'] ?? 'tls');
            Config::set('mail.mailers.smtp.username', $settings['mail_username'] ?? null);
            Config::set('mail.mailers.smtp.password', $settings['mail_password'] ?? null);

            Config::set('mail.from.address', $settings['mail_from_address'] 
                ?? $settings['mail_username'] 
                ?? '');

            Config::set('mail.from.name', $settings['mail_from_name'] ?? 'LeadManagement');
        } 
        else 
        {
            Config::set('mail.default', 'log');
            Config::set('mail.from.address', env('MAIL_FROM_ADDRESS', ''));
            Config::set('mail.from.name', env('MAIL_FROM_NAME', 'LeadManagement'));
        }
    }
}
