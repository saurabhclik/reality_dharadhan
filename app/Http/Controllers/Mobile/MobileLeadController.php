<?php

namespace App\Http\Controllers\mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Services\LeadService;

class MobileLeadController extends Controller
{
    protected $leadService;

    public function __construct(LeadService $leadService)
    {
        $this->leadService = $leadService;
    }
    
    public function addLeads()
    {
        $categorys = DB::table('inv_catg')->get();
        $sources = DB::table('sources')->get();
        $campaigns = DB::table('campaigns')->get();
        $projects = DB::table('projects')->get();
        $cities = DB::table('state_district')->orderBy('District', 'asc')->get();
        $clients = DB::table('users')->get();
        $states = DB::table('leads')->select('field1')->distinct()->pluck('field1');
        return view('mobile.add-leads', [
            'mode' => 'add',
            'categorys' => $categorys,
            'sources' => $sources,
            'campaigns' => $campaigns,
            'projects' => $projects,
            'cities' => $cities,
            'clients' => $clients,
            'states' => $states,
        ]);
    }

    public function create_leads(Request $request)
    {
        $result = $this->leadService->createLead($request, true);
        
        if (!$result['success']) 
        {
            return redirect()->back()->withInput();
        }
        
        return redirect($result['redirect']);
    }

    public function lead_edit($id)
    {
        $result = $this->leadService->editLead($id, true);
        
        if (!$result['success']) 
        {
            return redirect()->back()->withInput();
        }
        
        return view('mobile.add-leads', [
            'mode' => 'edit',
            'lead' => $result['lead'],
            'categorys' => $result['categorys'],
            'sources' => $result['sources'],
            'campaigns' => $result['campaigns'],
            'projects' => $result['projects'],
            'cities' => DB::table('state_district')->orderBy('District', 'asc')->get(),
            'clients' => DB::table('users')->get(),
            'states' => DB::table('leads')->select('field1')->distinct()->pluck('field1'),
            'currentCategory' => $result['currentCategory'],
            'currentSubCategory' => $result['currentSubCategory'],
            'currentSource' => $result['currentSource'],
            'currentCampaign' => $result['currentCampaign'],
            'currentProject' => $result['currentProject'],
        ]);
    }

    public function update_lead(Request $request, $id)
    {
        // echo '<pre>'; print_r($_POST); exit;
        $result = $this->leadService->updateLead($request, $id, true);
        if (!$result['success']) 
        {
            return redirect()->back()->withInput();
        }
        return redirect($result['redirect']);
    }

    public function updateStatus(Request $request)
    {
        $result = $this->leadService->updateLeadStatus($request);
        if (!$result['success']) 
        {
            return response()->json($result, $result['status'] ?? 500);
        }
        return response()->json($result);
    }

    public function quick_Lead(Request $request)
    {
        $result = $this->leadService->quickLead($request);

        if ($result['success']) 
        {
            return redirect()->back()->with('success', $result['message']);
        } 
        else 
        {
            return redirect()->back()
                ->withInput()
                ->with('error', $result['message']);
        }
    }
}
