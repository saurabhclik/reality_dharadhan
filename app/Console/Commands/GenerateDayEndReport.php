<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GenerateDayEndReport extends Command
{
    protected $signature = 'report:dayend';
    protected $description = 'Generate day-end report for all agents';

    public function handle()
    {
        $yesterday = Carbon::yesterday()->toDateString();
        $users = DB::table('users')->get();

        foreach ($users as $agent) 
        {
            $data = DB::table('leads')
                ->selectRaw("
                    SUM(CASE WHEN remind_date <= ? AND status != 'completed' THEN 1 ELSE 0 END) as pending_followups,
                    SUM(CASE WHEN is_allocated = 1 THEN 1 ELSE 0 END) as total_allocated_leads,
                    SUM(CASE WHEN DATE(created_at) = ? THEN 1 ELSE 0 END) as total_added_leads,
                    SUM(CASE WHEN visited_on = 1 THEN 1 ELSE 0 END) as visit_done,
                    SUM(CASE WHEN status = 'CONVERTED' AND conversion_type IS NOT NULL THEN 1 ELSE 0 END) as converted_leads,
                    SUM(CASE WHEN status = 'CONVERTED' AND conversion_type = 'Completed' THEN 1 ELSE 0 END) as completed_leads
                ", [$yesterday, $yesterday])
                ->where('user_id', $agent->id)
                ->first();
            $pendingFollowups = DB::table('leads')
                ->where('user_id', $agent->id)
                ->where('remind_date', '<=', $yesterday)
                ->where('status', '!=', 'completed')
                ->get();

            $addedLeads = DB::table('leads')
                ->where('user_id', $agent->id)
                ->whereDate('created_at', $yesterday)
                ->get();

            $visitDoneLeads = DB::table('leads')
                ->where('user_id', $agent->id)
                ->where('visited_on', 1)
                ->get();

            $convertedLeads = DB::table('leads')
                ->where('user_id', $agent->id)
                ->where('status', 'CONVERTED')
                ->get();

            $completedLeads = DB::table('leads')
                ->where('user_id', $agent->id)
                ->where('status', 'CONVERTED')
                ->where('conversion_type', 'Completed')
                ->get();

            DB::table('dayend_reports')->updateOrInsert(
                [
                    'report_date' => $yesterday,
                    'agent_id'    => $agent->id,
                ],
                [
                    'pending_followups'         => $data->pending_followups ?? 0,
                    'pending_followup_leads'    => $pendingFollowups->toJson(),
                    'total_allocated_leads'     => $data->total_allocated_leads ?? 0,
                    'total_added_leads'         => $data->total_added_leads ?? 0,
                    'added_leads'               => $addedLeads->toJson(),
                    'visit_done'                => $data->visit_done ?? 0,
                    'visit_done_leads'          => $visitDoneLeads->toJson(),
                    'converted_leads'           => $data->converted_leads ?? 0,
                    'converted_leads_info'      => $convertedLeads->toJson(),
                    'completed_leads'           => $data->completed_leads ?? 0,
                    'completed_leads_info'      => $completedLeads->toJson(),
                    'updated_at'                => now(),
                    'created_at'                => DB::raw('COALESCE(created_at, NOW())'),
                ]
            );
        }

        $this->info("Day-end report generated for {$yesterday}");
    }
}
