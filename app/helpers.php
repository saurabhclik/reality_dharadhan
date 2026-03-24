<?php

use Illuminate\Support\Facades\DB;

if (!function_exists('firebase_settings')) 
{
    function firebase_settings($key = null)
    {
        $record = DB::table('integration_settings')
            ->where('integration_type', 'firebase')
            ->where('status', 'active')
            ->first();

        if (!$record) return null;

        $settings = json_decode($record->settings, true);
        if ($key) 
        {
            return $settings[$key] ?? null;
        }

        return $settings;
    }
}