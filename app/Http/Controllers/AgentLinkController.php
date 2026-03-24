<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Session;

class AgentLinkController extends Controller
{
    public function create()
    {
        $agents = DB::table('users')
            ->where('role', '!=', 'super_admin')
            ->where('unique_id','!=',  '')
            ->where('is_active', 1)
            ->select('id', 'name', 'email', 'mobile', 'role')
            ->orderBy('name')
            ->get();

        $agentLinks = DB::table('agent_universal_links')
            ->where('is_active', 1)
            ->get()
            ->keyBy(function($link) 
            {
                $meta = json_decode($link->metadata);
                return $meta->agent_user_id ?? null;
            });
            
        return view('agent-links.create', compact('agents', 'agentLinks'));
    }

    public function store(Request $request)
    {
        try 
        {
            $request->validate([
                'agent_id' => 'required|exists:users,id',
            ]);
            $existingLink = DB::table('agent_universal_links')
                ->where('metadata', 'like', '%"agent_user_id":' . $request->agent_id . '%')
                ->first();
                
            if ($existingLink) 
            {
                return redirect()->route('users.index')
                    ->with('error', 'A universal link already exists for this agent. Please delete the existing link first if you want to generate a new one.');
            }
            
            $agent = DB::table('users')->where('id', $request->agent_id)->first();
            $uniqueIdentifier = $agent->unique_id;
            
            $linkUrl = "agent/{$uniqueIdentifier}";
            
            $agentLinkId = DB::table('agent_universal_links')->insertGetId([
                'agent_name' => $agent->name,
                'agent_email' => $agent->email,
                'agent_phone' => $agent->mobile,
                'unique_identifier' => $uniqueIdentifier,
                'link_url' => $linkUrl,
                'business_type' => 'realestate',
                'company_id' => 1,
                'is_active' => 1,
                'clicks' => 0,
                'metadata' => json_encode([
                    'created_by' => Session::get('user_id') ?? 1,
                    'ip' => $request->ip(),
                    'agent_user_id' => $agent->id,
                    'agent_role' => $agent->role,
                    'created_at' => now()->toDateTimeString()
                ]),
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            $agentLink = DB::table('agent_universal_links')->where('id', $agentLinkId)->first();
            $qrCodePath = $this->generateQRCode($agentLink);
            $barcodePath = $this->generateBarcode($agentLink);
            
            DB::table('agent_universal_links')
                ->where('id', $agentLinkId)
                ->update([
                    'qr_code_path' => $qrCodePath,
                    'barcode_path' => $barcodePath
                ]);

            return redirect()->route('users.index')
                ->with('success', 'Universal link generated successfully for ' . $agent->name . '!');
        } 
        catch(\Exception $error) 
        {
            return redirect()->route('users.index')
                ->with('error', 'Something went wrong: ' . $error->getMessage());
        }
    }

    public function show($id)
    {
        $agentLink = DB::table('agent_universal_links')->where('id', $id)->first();
        
        if (!$agentLink)
        {
            abort(404);
        }
        
        $agentLink->metadata = json_decode($agentLink->metadata);
        return view('agent-links.show', compact('agentLink'));
    }

    public function destroy($id)
    {
        try 
        {
            $agentLink = DB::table('agent_universal_links')->where('id', $id)->first();
            
            if (!$agentLink) 
            {
                return redirect()->route('users.index')
                    ->with('error', 'Link not found.');
            }
            
            $agentName = $agentLink->agent_name;
            if ($agentLink->qr_code_path && file_exists(public_path($agentLink->qr_code_path))) 
            {
                unlink(public_path($agentLink->qr_code_path));
            }
            
            if ($agentLink->barcode_path && file_exists(public_path($agentLink->barcode_path))) 
            {
                unlink(public_path($agentLink->barcode_path));
            }

            DB::table('agent_universal_links')->where('id', $id)->delete();
            
            return redirect()->route('users.index')
                ->with('success', 'Universal link for ' . $agentName . ' has been deleted successfully.');
        } 
        catch(\Exception $e) 
        {
            return redirect()->route('users.index')
                ->with('error', 'Error deleting link: ' . $e->getMessage());
        }
    }

    public function publicForm($identifier)
    {
        $agentLink = DB::table('agent_universal_links')
            ->where('unique_identifier', $identifier)
            ->where('is_active', 1)
            ->first();

        if (!$agentLink) 
        {
            abort(404, 'Invalid or expired link');
        }

        DB::table('agent_universal_links')
            ->where('id', $agentLink->id)
            ->increment('clicks');

        return view('agent-links.public-form', compact('agentLink'));
    }

    public function submitLead(Request $request, $identifier)
    {
        try 
        {
            $agentLink = DB::table('agent_universal_links')
                ->where('unique_identifier', $identifier)
                ->where('is_active', 1)
                ->first();

            if (!$agentLink) 
            {
                if ($request->ajax()) 
                {
                    return response()->json(['success' => false, 'message' => 'Invalid link'], 404);
                }
                abort(404);
            }

            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email',
                'phone' => 'required|string|max:20',
                'service_type' => 'required|string',
                'budget' => 'nullable|string',
                'location' => 'nullable|string',
                'property_type' => 'nullable|string',
                'message' => 'nullable|string'
            ]);

            DB::table('lead_submissions')->insert([
                'agent_universal_link_id' => $agentLink->id,
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'budget' => $request->budget,
                'location' => $request->location,
                'service_type' => $request->service_type,
                'form_data' => json_encode($request->except('_token')),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            if ($request->ajax()) 
            {
                return response()->json([
                    'success' => true,
                    'redirect' => route('agent.thank-you', ['identifier' => $identifier])
                ]);
            }

            return redirect()->route('agent.thank-you', ['identifier' => $identifier])
                ->with('success', 'Thank you for your submission!');
                
        } 
        catch(\Exception $e) 
        {
            if ($request->ajax()) 
            {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    public function thankYou($identifier)
    {
        $agentLink = DB::table('agent_universal_links')
            ->where('unique_identifier', $identifier)
            ->first();
        
        return view('agent-links.thank-you', compact('agentLink'));
    }

    public function downloadBarcode($id)
    {
        $agentLink = DB::table('agent_universal_links')->where('id', $id)->first();
        
        if (!$agentLink || !$agentLink->barcode_path || !file_exists(public_path($agentLink->barcode_path))) {
            abort(404);
        }

        return response()->download(public_path($agentLink->barcode_path));
    }

    private function generateQRCode($agentLink)
    {
        $qrCodePath = "uploads/qrcodes/{$agentLink->unique_identifier}.svg";
        
        if (!file_exists(public_path('uploads/qrcodes'))) {
            mkdir(public_path('uploads/qrcodes'), 0777, true);
        }

        $fullLink = url($agentLink->link_url);
        
        QrCode::format('svg')
            ->size(300)
            ->generate($fullLink, public_path($qrCodePath));

        return $qrCodePath;
    }

    private function generateBarcode($agentLink)
    {
        $barcodePath = "uploads/barcodes/{$agentLink->unique_identifier}.svg";
        
        if (!file_exists(public_path('uploads/barcodes'))) {
            mkdir(public_path('uploads/barcodes'), 0777, true);
        }

        $generator = new \Picqer\Barcode\BarcodeGeneratorSVG();
        file_put_contents(
            public_path($barcodePath),
            $generator->getBarcode($agentLink->unique_identifier, $generator::TYPE_CODE_128)
        );

        return $barcodePath;
    }
}