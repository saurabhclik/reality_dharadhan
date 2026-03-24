<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
class SoftwareFeatureController extends Controller
{
    public function index()
    {
        $features = DB::table('software_features')
            ->where('software_name', Session::get('software_name'))
            ->where('is_realstate', 0)
            ->whereRaw("LOWER(TRIM(feature_name)) != 'integration'")
            ->get();

        $active_user_email = Session::get('user_email');
        $faqs = DB::table('faqs')->get();
        $activeFeatures = session('active_features', []);
        $userType = Session::get('user_type');

        return view('software-feature.features', compact(
            'features',
            'faqs',
            'activeFeatures',
            'active_user_email',
            'userType'
        ));
    }

    public function requestAccess(Request $request)
    {
        $request->validate([
            'feature' => 'required|string',
            'feature_id' => 'required|integer'
        ]);

        $feature = $request->input('feature');
        $featureId = $request->input('feature_id'); 

        $activeFeatures = session('active_features', []);
        if (!in_array($feature, $activeFeatures)) 
        {
            $activeFeatures[] = $feature;
            session(['active_features' => $activeFeatures]);
        }

        DB::table('software_requests')->insert([
            'software_name' => Session::get('software_name'),
            'software_id' => $featureId,
            'client_name' => Session::get('user_name'),
            'email' => Session::get('user_email'),
            'phone' => Session::get('user_mobile'),
            'requested_date' => now(),
            'message' => "Requested access to feature: {$feature}",
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Feature access requested successfully. Please contact Clikzop to complete your request'
        ]);
    }

    
    public function activateFeature(Request $request)
    {
        $request->validate([
            'feature_id' => 'required|integer'
        ]);
        
        $featureId = $request->input('feature_id');

        DB::table('software_features')
            ->where('id', $featureId)
            ->update([
                'status' => 'active',
                'activate_at' => now(),
                'updated_at' => now()
            ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Feature activated successfully'
        ]);
    }

    public function startFreeTrial(Request $request)
    {
        if ($request->isMethod('get')) 
        {
            return view('software-feature.trial-signup');
        }

        $request->validate([
            'email' => 'required|email',
            'feature_id' => 'required|exists:software_features,id'
        ]);

        $email = $request->input('email');
        $userName = Session::get('user_name', 'Guest User');
        $userPhone = Session::get('user_mobile', 'N/A');
        $featureId = $request->input('feature_id');
        $existingTrial = DB::table('trials')
            ->where('feature_id', $featureId)
            ->where('software_name', Session::get('software_name'))
            ->first();

        if ($existingTrial) 
        {
            return redirect()->back()->with('error', 'You have already requested a free trial for this feature. Please contact Clikzop Innovation.');
        }

        DB::table('trials')->insert([
            'software_name' => Session::get('software_name'),
            'feature_id' => $featureId,
            'client_name' => $userName,
            'email' => $email,
            'phone' => $userPhone,
            'start_date' => now(), 
            'end_date' => now(),
            'status' => 'inactive', 
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        return redirect()->back()
            ->with('success', 'Your free trial request has been sent. Please contact Clikzop to start your trial. Our team will reach out to you shortly');
    }
}