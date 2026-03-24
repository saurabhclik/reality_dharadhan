<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Exception;

class LeadIntegrationController extends Controller
{
    public function insertLead(Request $request)
    {
        try
        {
            $token = $request->header('Authorization');
            if ($token && str_starts_with($token, 'Bearer ')) 
            {
                $token = substr($token, 7);
            }
            if (!$token || $token !== 'OXpROcBEl0JYqCO6XwW4') 
            {
                return response()->json([
                    'status' => 401,
                    'message' => 'Unauthorized: Invalid or missing token',
                    'data' => []
                ], 401);
            }

            $validator = Validator::make($request->all(), [
                'phone' => 'required|string|max:15',
                'unique_id' => 'required|string',
                'name' => 'nullable|string|max:255',
                'email' => 'nullable|email|max:255',
                'last_comment' => 'nullable|string',
                'step2_message' => 'nullable|string',
                'step3_message' => 'nullable|string',
                'property_type' => 'nullable|string|max:100',
                'catg_id' => 'nullable|integer',
                'sub_catg_id' => 'nullable|integer',
                'zone_id' => 'nullable|string',
                'projects' => 'nullable|string',
            ]);

            if ($validator->fails()) 
            {
                return response()->json([
                    'status' => 400,
                    'message' => $validator->errors()->first(),
                    'data' => []
                ], 400);
            }

            $user = DB::table('users')->where('unique_id', $request->unique_id)->first();

            if (!$user) 
            {
                return response()->json([
                    'status' => 404,
                    'message' => 'User not found with this unique_id',
                    'data' => []
                ], 404);
            }

            $exists = DB::table('leads')->where('phone', $request->phone)->first();

            if ($exists) 
            {
                return response()->json([
                    'status' => 409,
                    'message' => 'Lead already exists with this phone number',
                    'data' => [
                        'existing_lead_id' => $exists->id,
                        'existing_lead_name' => $exists->name
                    ]
                ], 409);
            }
            $step2Message = $request->step2_message;
            $step3Message = $request->step3_message;
            $lastComment = $request->last_comment;
            if (empty($lastComment) && ($step2Message || $step3Message)) 
            {
                $dateTime = now()->format('Y-m-d H:i:s');
                if ($step2Message && $step3Message) 
                {
                    $lastComment = "[{$dateTime}] Requirements: {$step2Message} | Additional Info: {$step3Message}";
                } 
                elseif ($step2Message) 
                {
                    $lastComment = "[{$dateTime}] Requirements: {$step2Message}";
                } 
                elseif ($step3Message) 
                {
                    $lastComment = "[{$dateTime}] Additional Info: {$step3Message}";
                }
            }
            $propertyLocations = [];
            $zoneNames = [];
            $cities = [];
            $states = [];

            if ($request->zone_id) 
            {
                $zoneIds = explode(',', $request->zone_id);
                $zones = DB::table('zones')->whereIn('id', $zoneIds)->get();

                foreach ($zones as $zone) 
                {
                    $fullInfo = trim($zone->zone_name);
                    if ($zone->sub_area) $fullInfo .= ', ' . trim($zone->sub_area);
                    if ($zone->pincode) $fullInfo .= ', ' . trim($zone->pincode);
                    $propertyLocations[] = $fullInfo;
                    $zoneNames[] = $zone->zone_name;
                    $cityRecord = DB::table('state_district')->where('id', $zone->city_id)->first();
                    if ($cityRecord) 
                    {
                        if ($cityRecord->District) $cities[] = $cityRecord->District;
                        if ($cityRecord->state) $states[] = $cityRecord->state;
                    }
                }
                $propertyCity = implode(', ', array_unique($cities));
                $propertyState = implode(', ', array_unique($states));
            } 
            else 
            {
                $propertyCity = null;
                $propertyState = null;
            }

            $leadData = [
                'uuid' => $request->unique_id,
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'notes' => $step3Message,
                'last_comment' => $lastComment,
                'source' => 'Agent Link',
                'status' => 'NEW LEAD',
                'unallocated_lead' => 0,
                'is_allocated' => 1,
                'user_id' => $user->id ?? 1,
                'lead_date' => now(),
                'catg_id' => $request->catg_id,
                'sub_catg_id' => $request->sub_catg_id,
                'type' => $request->property_type,
                'project_id' => $request->projects,
                'property_location' => implode(' | ', $propertyLocations),
                'name_of_location' => implode(', ', $zoneNames),
                'property_city' => $propertyCity,
                'property_state' => $propertyState,
                'created_at' => now(),
                'updated_at' => now()
            ];

            $leadId = DB::table('leads')->insertGetId($leadData);

            DB::table('lead_comments')->insert([
                'lead_id' => $leadId,
                'comment' => $lastComment ?: 'Lead created via agent link',
                'status' => 'NEW LEAD',
                'user_id' => $user->id ?? 1,
                'created_date' => now()
            ]);

            return response()->json([
                'status' => 200,
                'message' => 'Lead created successfully',
                'data' => ['lead_id' => $leadId]
            ], 200);

        } 
        catch (Exception $e) 
        {
            return response()->json([
                'status' => 500,
                'message' => 'Error: '.$e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    public function getCategories(Request $request, $type)
    {
        try 
        {
            $type = $type ?? 'Residential';
            
            $categories = DB::table('inv_catg')
                ->where('type', $type)
                ->select('id', 'name', 'type')
                ->get();

            return response()->json([
                'status' => 200,
                'message' => 'Categories fetched successfully',
                'data' => $categories
            ], 200);
        } 
        catch (Exception $e) 
        {
            return response()->json([
                'status' => 500,
                'message' => 'Error: '.$e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    public function getSubCategories($catgId)
    {
        try 
        {
            $subCategories = DB::table('inv_subcatg')
                ->where('catg_id', $catgId)
                ->select('id', 'name', 'catg_id')
                ->get();

            return response()->json([
                'status' => 200,
                'message' => 'Sub categories fetched successfully',
                'data' => $subCategories
            ], 200);
        } 
        catch (Exception $e) 
        {
            return response()->json([
                'status' => 500,
                'message' => 'Error: '.$e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    public function getProjectName()
    {
        try
        {
            $projects = DB::table('projects')
                ->select('id', 'project_name')
                ->get();
                
            return response()->json([
                'status' => 200,
                'message' => 'Projects fetched successfully',
                'data' => $projects
            ], 200);
        }
        catch(Exception $e)
        {
            return response()->json([
                'status' => 500,
                'message' => 'Error: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    public function getLocation(Request $request)
    {
        try 
        {
            $query = DB::table('zones as z')
                ->join('state_district as sd', 'z.city_id', '=', 'sd.id')
                ->select(
                    'z.id',
                    'z.zone_name',
                    'z.sub_area',
                    'z.pincode',
                    'sd.state',
                    'sd.district',
                    'sd.id as district_id'
                );
            
            // Filter by city_id if provided
            if ($request->has('city_id') && !empty($request->city_id)) 
            {
                $query->where('z.city_id', $request->city_id);
            }
            
            $locations = $query->get();

            return response()->json([
                'status' => 200,
                'data' => $locations
            ], 200);
        } 
        catch (\Exception $error) 
        {
            return response()->json([
                'status' => 500,
                'message' => 'Error: ' . $error->getMessage(),
                'data' => []
            ], 500);
        }
    }

    public function getStates()
    {
        try 
        {
            $states = DB::table('state_district')
                ->select('state')
                ->distinct()
                ->orderBy('state')
                ->get();

            return response()->json([
                'status' => 200,
                'message' => 'States fetched successfully',
                'data' => $states
            ], 200);
        } 
        catch (Exception $e) 
        {
            return response()->json([
                'status' => 500,
                'message' => 'Error: '.$e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    public function getDistricts($state)
    {
        try 
        {
            $districts = DB::table('state_district')
                ->where('state', $state)
                ->select('id', 'District')
                ->orderBy('District')
                ->get();

            return response()->json([
                'status' => 200,
                'message' => 'Districts fetched successfully',
                'data' => $districts
            ], 200);
        } 
        catch (Exception $e) 
        {
            return response()->json([
                'status' => 500,
                'message' => 'Error: '.$e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    public function getLead()
    {
        try
        {
            $token = request()->header('Authorization');
            if ($token && str_starts_with($token, 'Bearer '))
            {
                $token = substr($token, 7);
            }
            if (!$token || $token !== 'OXpROcBEl0JYqCO6XwW4')
            {
                return response()->json([
                    'status' => 401,
                    'message' => 'Unauthorized: Invalid or missing token',
                    'data' => []
                ], 401);
            }
            
            $lead = DB::table('leads')
                ->leftJoin('users', 'leads.user_id', '=', 'users.id')
                ->leftJoin('inv_catg', 'leads.catg_id', '=', 'inv_catg.id')
                ->leftJoin('inv_subcatg', 'leads.sub_catg_id', '=', 'inv_subcatg.id')
                ->whereNotNull('leads.uuid')
                ->where('leads.uuid', '!=', '')
                ->orderBy('leads.created_at', 'desc')
                ->select(
                    'leads.*',
                    'users.name as assigned_to',
                    'inv_catg.name as category_name',
                    'inv_subcatg.name as sub_category_name'
                )
                ->first();

            if (!$lead)
            {
                return response()->json([
                    'status' => 404,
                    'message' => 'Lead not found',
                    'data' => []
                ], 404);
            }
            
            $leadData = [
                'id' => $lead->id,
                'uuid' => $lead->uuid,
                'name' => $lead->name,
                'email' => $lead->email,
                'phone' => $lead->phone,
                'whatsapp_no' => $lead->whatsapp_no,
                'status' => $lead->status,
                'source' => $lead->source,
                'campaign' => $lead->campaign,
                'notes' => $lead->notes,
                'last_comment' => $lead->last_comment,
                'budget' => $lead->budget,
                'size' => $lead->size,
                'property_type' => $lead->type,
                'property_category' => $lead->field2,
                'property_subcategory' => $lead->field3,
                'category_id' => $lead->catg_id,
                'category_name' => $lead->category_name,
                'sub_category_id' => $lead->sub_catg_id,
                'sub_category_name' => $lead->sub_category_name,
                'property_city' => $lead->property_city,
                'property_state' => $lead->property_state,
                'property_location' => $lead->property_location,
                'name_of_location' => $lead->name_of_location,
                'zone_id' => $lead->zone_id,
                'projects' => $lead->projects,
                'address' => $lead->field4,
                'assigned_to' => $lead->assigned_to,
                'assigned_to_id' => $lead->user_id,
                'is_allocated' => $lead->is_allocated,
                'lead_date' => $lead->lead_date,
                'created_at' => $lead->created_at,
                'updated_at' => $lead->updated_at
            ];

            return response()->json([
                'status' => 200,
                'message' => 'Lead fetched successfully',
                'data' => $leadData
            ], 200);
        }
        catch (Exception $e)
        {
            return response()->json([
                'status' => 500,
                'message' => 'Error: '.$e->getMessage(),
                'data' => []
            ], 500);
        }
    }
}