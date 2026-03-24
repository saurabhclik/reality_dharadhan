<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use App\Models\ExhibitionShareLink;
use Illuminate\Support\Str;
class WebExhibitionController extends Controller
{

    public function toggleAutoWelcome(Request $request, $id)
    {
        try 
        {
            $validator = Validator::make($request->all(), [
                'auto_welcome_message' => 'required|boolean'
            ]);
            
            if ($validator->fails()) 
            {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid request'
                ], 422);
            }
            
            $exhibition = DB::table('exhibitions')->where('id', $id)->first();
            
            if (!$exhibition) 
            {
                return response()->json([
                    'success' => false,
                    'message' => 'Exhibition not found!'
                ], 404);
            }
            
            DB::table('exhibitions')->where('id', $id)->update([
                'auto_welcome_message' => $request->auto_welcome_message,
                'updated_at' => now()
            ]);
            
            $status = $request->auto_welcome_message ? 'enabled' : 'disabled';
            
            return response()->json([
                'success' => true,
                'message' => 'Auto welcome messages ' . $status . ' successfully!'
            ]);
            
        } 
        catch (\Exception $e) 
        {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function getExhibitionDetails($id)
    {
        try 
        {
            $exhibition = DB::table('exhibitions')->where('id', $id)->first();
            
            if (!$exhibition) 
            {
                return response()->json([
                    'success' => false,
                    'message' => 'Exhibition not found'
                ], 404);
            }
            
            $exhibition->start_date = Carbon::parse($exhibition->start_date)->format('Y-m-d\TH:i');
            $exhibition->end_date = Carbon::parse($exhibition->end_date)->format('Y-m-d\TH:i');
            
            return response()->json([
                'success' => true,
                'exhibition' => $exhibition
            ]);
            
        } 
        catch (\Exception $e) 
        {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching exhibition details'
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'location' => 'required|string|max:255',
            'is_active' => 'sometimes|boolean',
            'auto_welcome_message' => 'sometimes|boolean'
        ]);
        
        if ($validator->fails()) 
        {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        try 
        {
            DB::beginTransaction();
            $isActive = $request->has('is_active') ? 1 : 0;
            $autoWelcome = $request->has('auto_welcome_message') ? 1 : 0;
            if ($isActive) 
            {
                DB::table('exhibitions')->update(['is_active' => 0]);
            }
            
            DB::table('exhibitions')->insert([
                'name' => $request->name,
                'description' => $request->description,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'location' => $request->location,
                'is_active' => $isActive,
                'auto_welcome_message' => $autoWelcome,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Exhibition created successfully!'
            ]);
            
        } 
        catch (\Exception $e) 
        {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create exhibition: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $exhibition = DB::table('exhibitions')->where('id', $id)->first();
        
        if (!$exhibition) 
        {
            return response()->json([
                'success' => false,
                'message' => 'Exhibition not found!'
            ], 404);
        }
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'location' => 'required|string|max:255',
            'is_active' => 'sometimes|boolean',
            'auto_welcome_message' => 'sometimes|boolean'
        ]);
        
        if ($validator->fails()) 
        {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        try 
        {
            DB::beginTransaction();
            
            $isActive = $request->has('is_active') ? 1 : 0;
            $autoWelcome = $request->has('auto_welcome_message') ? 1 : 0;
            
            if ($isActive) 
            {
                DB::table('exhibitions')
                    ->where('id', '!=', $id)
                    ->update(['is_active' => 0]);
            }
            
            DB::table('exhibitions')->where('id', $id)->update([
                'name' => $request->name,
                'description' => $request->description,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'location' => $request->location,
                'is_active' => $isActive,
                'auto_welcome_message' => $autoWelcome,
                'updated_at' => now(),
            ]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Exhibition updated successfully!'
            ]);
            
        } 
        catch (\Exception $e) 
        {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update exhibition: ' . $e->getMessage()
            ], 500);
        }
    }

    public function index()
    {
        $activeExhibition = DB::table('exhibitions')
            ->where('is_active', 1)
            ->first();

        $exhibitions = DB::table('exhibitions')
            ->select('*')
            ->orderBy('start_date', 'desc')
            ->paginate(10);

    
        $exhibitionsWithLeads = [];
        foreach ($exhibitions as $exhibition) 
        {
            $leadCount = DB::table('exhibition_leads')
                ->where('exhibition_id', $exhibition->id)
                ->count();
            
            $exhibition->lead_count = $leadCount;
            $exhibitionsWithLeads[] = $exhibition;
        }

        $stats = [
            'total' => DB::table('exhibitions')->count(),
            'active' => DB::table('exhibitions')->where('is_active', 1)->count(),
            'upcoming' => DB::table('exhibitions')
                ->where('start_date', '>', Carbon::now())
                ->count(),
            'past' => DB::table('exhibitions')
                ->where('end_date', '<', Carbon::now())
                ->count(),
        ];
        $shareLinks = ExhibitionShareLink::whereIn('exhibition_id', $exhibitions->pluck('id'))->get()->keyBy('exhibition_id');
        return view('exhibition.index', compact('exhibitions', 'activeExhibition', 'stats', 'shareLinks'));
    }

    public function view($id)
    {
        try 
        {
            $exhibition = DB::table('exhibitions')->where('id', $id)->first();
            
            if (!$exhibition) 
            {
                return redirect()->route('exhibition.index')
                    ->with('error', 'Exhibition not found!');
            }
            
            $leadCount = DB::table('exhibition_leads')->where('exhibition_id', $id)->count();
            $todayLeads = DB::table('exhibition_leads')
                ->where('exhibition_id', $id)
                ->whereDate('created_at', Carbon::today())
                ->count();
            $totalVisitors = $leadCount;

            $query = DB::table('exhibition_leads')
                ->where('exhibition_id', $id);

            if (request()->has('name') && !empty(request('name'))) 
            {
                $query->where('name', 'like', '%' . request('name') . '%');
            }

            if (request()->has('country') && !empty(request('country'))) 
            {
                $query->where('country', 'like', '%' . request('country') . '%');
            }

            if (request()->has('type') && !empty(request('type'))) 
            {
                $typeFilter = request('type');
                $query->where(function ($q) use ($typeFilter) 
                {
                    $q->where('type', 'like', '%"' . $typeFilter . '"%')
                    ->orWhere('type', 'like', "%'$typeFilter'%")
                    ->orWhere('type', 'like', '%' . $typeFilter . '%')
                    ->orWhere('type', 'like', '%"' . strtolower($typeFilter) . '"%')
                    ->orWhere('type', 'like', '%"' . ucfirst($typeFilter) . '"%');
                });
            }

            if (request()->has('operating_country') && !empty(request('operating_country'))) 
            {
                $operatingCountry = request('operating_country');
                $query->where(function ($q) use ($operatingCountry) 
                {
                    $q->where('operating_country', 'like', '%"' . $operatingCountry . '"%')
                    ->orWhere('operating_country', 'like', "%'$operatingCountry'%")
                    ->orWhere('operating_country', 'like', '%' . $operatingCountry . '%');
                });
            }

            $perPage = request('per_page', 10);

            $leads = $query
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);

            $leads->appends(request()->query());
            $leads->appends(request()->query());
            
            $countries = DB::table('countries')
                ->select('id', 'name')
                ->orderBy('name')
                ->get();

            $allTypes = DB::table('exhibition_leads')
                ->where('exhibition_id', $id)
                ->whereNotNull('type')
                ->where('type', '!=', '')
                ->where('type', '!=', 'null')
                ->where('type', '!=', 'NULL')
                ->select('type')
                ->get();

            $tempTypes = [];

            foreach ($allTypes as $lead) 
            {
                if (!empty($lead->type)) 
                {
                    $typeData = trim($lead->type);
                    $decoded = json_decode($typeData, true);
                    
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) 
                    {
                        foreach ($decoded as $type) 
                        {
                            if (is_string($type) && trim($type) !== '') 
                            {
                                $tempTypes[] = trim($type);
                            }
                        }
                    } 
                    else 
                    {
                        if (strpos($typeData, ',') !== false) 
                        {
                            $splitTypes = array_map('trim', explode(',', $typeData));
                            foreach ($splitTypes as $type) 
                            {
                                if ($type !== '') 
                                {
                                    $tempTypes[] = $type;
                                }
                            }
                        } 
                        else
                        {
                            $tempTypes[] = $typeData;
                        }
                    }
                }
            }
            
            $uniqueTypes = collect($tempTypes)
                ->filter(function ($type) 
                {
                    return !empty(trim($type)) && 
                        $type !== 'null' && 
                        $type !== 'NULL';
                })
                ->unique()
                ->sort()
                ->values()
                ->toArray();
            $operatingCountries = collect();
            
            $allOperatingCountries = DB::table('exhibition_leads')
                ->where('exhibition_id', $id)
                ->whereNotNull('operating_country')
                ->where('operating_country', '!=', '')
                ->where('operating_country', '!=', 'null')
                ->where('operating_country', '!=', 'NULL')
                ->select('operating_country')
                ->get();

            $tempCountries = [];

            foreach ($allOperatingCountries as $lead) 
            {
                if (!empty($lead->operating_country)) 
                {
                    $opCountry = trim($lead->operating_country);
                    $decoded = json_decode($opCountry, true);
                    
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) 
                    {
                        foreach ($decoded as $country) 
                        {
                            if (is_string($country) && trim($country) !== '') 
                            {
                                $tempCountries[] = trim($country);
                            }
                        }
                    } 
                    else 
                    {
                        if (strpos($opCountry, ',') !== false) 
                        {
                            $splitCountries = array_map('trim', explode(',', $opCountry));
                            foreach ($splitCountries as $country) 
                            {
                                if ($country !== '') 
                                {
                                    $tempCountries[] = $country;
                                }
                            }
                        } 
                        else
                        {
                            $tempCountries[] = $opCountry;
                        }
                    }
                }
            }
            
            $operatingCountries = collect($tempCountries)
                ->filter(function ($country) 
                {
                    return !empty(trim($country)) && 
                        $country !== 'null' && 
                        $country !== 'NULL';
                })
                ->unique()
                ->sort()
                ->values()
                ->toArray();

            $types = DB::table('category')->orderBy('id', 'desc')->get();

            // echo '<pre>'; print_r( $id); exit;
            return view('exhibition.view', [
                'exhibition' => $exhibition,
                'leadCount' => $leadCount,
                'todayLeads' => $todayLeads,
                'totalVisitors' => $totalVisitors,
                'leads' => $leads,
                'types' => $types,
                'uniqueTypes' => $uniqueTypes,
                'countries' => $countries,
                'operatingCountries' => $operatingCountries
            ]);

        } 
        catch (\Exception $e) 
        {
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function leadsPage($id)
    {
        $exhibition = DB::table('exhibitions')->where('id', $id)->first();
        
        if (!$exhibition) 
        {
            return redirect()->route('exhibition.index')
                ->with('error', 'Exhibition not found!');
        }

        $query = DB::table('exhibition_leads')
            ->where('exhibition_id', $id);

        if (request()->has('name') && request('name') != '')
        {
            $query->where('name', 'like', '%' . request('name') . '%');
        }

        if (request()->has('phone') && request('phone') != '') 
        {
            $query->where('phone', 'like', '%' . request('phone') . '%');
        }

        if (request()->has('type') && request('type') != '') 
        {
            $query->where('type', request('type'));
        }

        if (request()->has('country') && request('country') != '') 
        {
            $query->where('country', 'like', '%' . request('country') . '%');
        }

        if (request()->has('operating_country') && request('operating_country') != '') 
        {
            $operatingCountry = request('operating_country');
            $query->where(function ($q) use ($operatingCountry) 
            {
                $q->where('operating_country', 'like', '%' . $operatingCountry . '%')
                ->orWhere('operating_country', 'like', '%"' . $operatingCountry . '"%')
                ->orWhere('operating_country', 'like', "%'$operatingCountry'%");
            });
        }
        $leads = $query->orderBy('created_at', 'desc')->paginate(20);
        $leads->appends(request()->query());
        return view('exhibition.leads', compact('exhibition', 'leads'));
    }

    public function storeLead(Request $request, $exhibitionId)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'whatsapp' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'company' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
            'fax' => 'nullable|string|max:50',
            'country' => 'nullable|string|max:100',
            'address' => 'nullable|string',
            'type' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'remarks' => 'nullable|string',
            'reminder_date' => 'nullable|date',
            'visit_card' => 'nullable|string|max:50',
        ]);

        if ($validator->fails())
        {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try 
        {
            DB::table('exhibition_leads')->insert([
                'exhibition_id' => $exhibitionId,
                'name' => $request->name,
                'phone' => $request->phone,
                'whatsapp' => $request->whatsapp,
                'email' => $request->email,
                'company' => $request->company,
                'website' => $request->website,
                'fax' => $request->fax,
                'country' => $request->country,
                'address' => $request->address,
                'type' => $request->type,
                'description' => $request->description,
                'remarks' => $request->remarks,
                'date' => now(),
                'reminder_date' => $request->reminder_date,
                'visit_card' => $request->visit_card,
                'is_converted' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return redirect()->back()
                ->with('success', 'Lead added successfully!');

        } 
        catch (\Exception $e) 
        {
            return redirect()->back()
                ->with('error', 'Failed to add lead: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function updateLead(Request $request, $leadId)
    {
        $lead = DB::table('exhibition_leads')->where('id', $leadId)->first();
        
        if (!$lead) 
        {
            return response()->json([
                'success' => false,
                'message' => 'Lead not found!'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:255',
            'type' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'whatsapp' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'company' => 'nullable|string|max:255',
            'website' => 'nullable|string|max:255',
            'fax' => 'nullable|string|max:50',
            'country' => 'nullable|string|max:100',
            'operating_country' => 'nullable',
            'address' => 'nullable|string',
            'description' => 'nullable|string',
            'remarks' => 'nullable|string',
            'reminder_date' => 'nullable|date',
            'visit_card' => 'nullable|file|image|max:2048',
        ]);

        if ($validator->fails()) 
        {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try 
        {
            $data = $validator->validated();
            
            if (!empty($data['operating_country'])) 
            {
                $data['operating_country'] = json_encode(array_map('trim', explode(',', $data['operating_country'])));
            }

            if ($request->hasFile('visit_card')) 
            {
                if ($lead->visit_card && Storage::disk('public')->exists($lead->visit_card)) 
                {
                    Storage::disk('public')->delete($lead->visit_card);
                }
                $file = $request->file('visit_card');
                $filename = 'visit_card_' . $leadId . '_' . time() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('visit_cards', $filename, 'public');
                $data['visit_card'] = $path;
            }

            $data['updated_at'] = now();

            DB::table('exhibition_leads')->where('id', $leadId)->update($data);

            $updatedLead = DB::table('exhibition_leads')->where('id', $leadId)->first();

            return response()->json([
                'success' => true,
                'message' => 'Lead updated successfully!',
                'data' => $updatedLead
            ]);

        } 
        catch (\Exception $e) 
        {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update lead: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroyLead($id, $exhibition_id)
    {
        try 
        {
            $lead = DB::table('exhibition_leads')
                ->where('id', $id)
                ->where('exhibition_id', $exhibition_id)
                ->first();

            if (!$lead) 
            {
                return response()->json([
                    'status' => 404,
                    'message' => 'Exhibition lead not found!',
                    'data' => ''
                ], 404);
            }

            if (isset($lead->visit_card) && $lead->visit_card && Storage::disk('public')->exists($lead->visit_card)) 
            {
                Storage::disk('public')->delete($lead->visit_card);
            }

            DB::table('exhibition_leads')
                ->where('id', $id)
                ->where('exhibition_id', $exhibition_id)
                ->delete();

            return response()->json([
                'status' => 200,
                'message' => 'Exhibition lead deleted successfully!',
                'data' => ''
            ]);

        } 
        catch (\Exception $e) 
        {
            return response()->json([
                'status' => 500,
                'message' => 'Server error: ' . $e->getMessage(),
                'data' => ''
            ], 500);
        }
    }

    public function destroy($id)
    {
        try 
        {
            DB::beginTransaction();

            $exhibition = DB::table('exhibitions')->where('id', $id)->first();
            
            if (!$exhibition) 
            {
                return response()->json([
                    'success' => false,
                    'message' => 'Exhibition not found!'
                ], 404);
            }

            if ($exhibition->is_active) 
            {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete active exhibition. Deactivate it first.'
                ], 400);
            }

            DB::table('exhibitions')->where('id', $id)->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Exhibition deleted successfully!'
            ]);

        } 
        catch (\Exception $e) 
        {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete exhibition: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getLeads($id)
    {
        try 
        {
            $exhibition = DB::table('exhibitions')->where('id', $id)->first();
            
            if (!$exhibition) 
            {
                return response()->json([
                    'success' => false,
                    'message' => 'Exhibition not found!'
                ], 404);
            }

            $leads = DB::table('exhibition_leads')
                ->where('exhibition_id', $id)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'leads' => $leads,
                'count' => $leads->count(),
                'exhibition' => $exhibition
            ]);

        } 
        catch (\Exception $e)
        {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load leads: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getLeadDetails($id)
    {
        try 
        {
            $lead = DB::table('exhibition_leads')->where('id', $id)->first();
            
            if (!$lead) 
            {
                return response()->json([
                    'success' => false,
                    'message' => 'Lead not found!'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'lead' => $lead
            ]);

        } 
        catch (\Exception $e) 
        {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load lead details: ' . $e->getMessage()
            ], 500);
        }
    }

    public function convertLeadToCRM(Request $request, $leadId)
    {
        try 
        {
            DB::beginTransaction();
            $exhibitionLead = DB::table('exhibition_leads')->where('id', $leadId)->first();
            if (!$exhibitionLead) 
            {
                return response()->json([
                    'success' => false,
                    'message' => 'Exhibition lead not found!'
                ], 404);
            }

            if ($exhibitionLead->is_converted == 1) 
            {
                return response()->json([
                    'success' => false,
                    'message' => 'This lead is already converted to CRM!'
                ], 400);
            }

            $existingLeadQuery = DB::table('leads');
            if (!empty($exhibitionLead->email) && !empty($exhibitionLead->phone)) 
            {
                $existingLeadQuery->where(function ($q) use ($exhibitionLead) 
                {
                    $q->where('email', $exhibitionLead->email)
                    ->orWhere('phone', $exhibitionLead->phone);
                });
            } 
            elseif (!empty($exhibitionLead->email)) 
            {
                $existingLeadQuery->where('email', $exhibitionLead->email);
            } 
            elseif (!empty($exhibitionLead->phone)) 
            {
                $existingLeadQuery->where('phone', $exhibitionLead->phone);
            } 
            else 
            {
                $existingLeadQuery = null;
            }

            $existingLead = $existingLeadQuery ? $existingLeadQuery->first() : null;

            if ($existingLead)
            {
                return response()->json([
                    'success' => false,
                    'message' => 'This lead already exists in CRM!',
                    'crm_lead_id' => $existingLead->id
                ], 400);
            }

            $exhibitionName = $exhibitionLead->exhibition_id
                ? DB::table('exhibitions')->where('id', $exhibitionLead->exhibition_id)->value('name')
                : 'Exhibition';

            $typeIds = [];
            if (!empty($exhibitionLead->type)) 
            {
                $typeNames = json_decode($exhibitionLead->type, true);
                if (!is_array($typeNames)) 
                {
                    $typeNames = explode(',', $exhibitionLead->type);
                }

                foreach ($typeNames as $typeName) 
                {
                    $id = DB::table('category')
                        ->whereRaw('LOWER(name) = ?', [strtolower(trim($typeName))])
                        ->value('id');

                    if ($id) 
                    {
                        $typeIds[] = $id;
                    }
                }
            }
            // ddr($exhibitionLead->visit_card);
            $crmLeadData = [
                'name' => $exhibitionLead->name ?? '',
                'user_id' => 1,
                'email' => $exhibitionLead->email ?? '',
                'phone' => $exhibitionLead->phone ?? '',
                'whatsapp_no' => $exhibitionLead->whatsapp ?? '',
                'notes' => $exhibitionLead->description ?? '',
                'source' => 'Exhibition',
                'campaign' => $exhibitionName,
                'stage_id' => 1,
                'type' => !empty($typeIds) ? implode(',', $typeIds) : null,
                'classification' => 'Exhibition Lead',
                'status' => 'NEW LEAD',
                'unallocated_lead' => 1,
                'is_allocated' => 0,
                'lead_date' => $exhibitionLead->date ?? now(),
                'last_comment' => 'Converted from exhibition lead',
                'remind_date' => $exhibitionLead->reminder_date ?? null,
                'conversion_type' => 'Exhibition',
                'checklist_status' => 'open',
                'created_at' => now(),
                'updated_at' => now(),
                'country_name' => $exhibitionLead->country ?? '',
                'address' => $exhibitionLead->address ?? '',
                'company' => $exhibitionLead->company ?? null,
                'website' => $exhibitionLead->website ?? null,
                'visit_card' => $exhibitionLead->visit_card ?? null,
                'fax' => $exhibitionLead->fax ?? null,
                'operating_country' => $exhibitionLead->operating_country ?? null,
            ];

            $crmLeadId = DB::table('leads')->insertGetId($crmLeadData);

            DB::table('exhibition_leads')
                ->where('id', $leadId)
                ->update([
                    'is_converted' => 1,
                    'updated_at' => now(),
                ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Lead converted to CRM successfully!',
                'crm_lead_id' => $crmLeadId,
                'exhibition_name' => $exhibitionName
            ]);

        } 
        catch (\Exception $e) 
        {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to convert lead: ' . $e->getMessage()
            ], 500);
        }
    }

    public function convertMultipleLeads(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lead_ids' => 'required|array|min:1',
            'lead_ids.*' => 'integer|exists:exhibition_leads,id',
        ]);

        if ($validator->fails()) 
        {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $leadIds = $request->lead_ids;

        try 
        {
            DB::beginTransaction();

            $convertedCount = 0;
            $failedCount = 0;
            $results = [];

            foreach ($leadIds as $leadId) 
            {
                try 
                {
                    $exhibitionLead = DB::table('exhibition_leads')->where('id', $leadId)->first();

                    if (!$exhibitionLead) 
                    {
                        $failedCount++;
                        $results[] = ['lead_id' => $leadId, 'status' => 'not_found'];
                        continue;
                    }

                    if ($exhibitionLead->is_converted == 1) 
                    {
                        $failedCount++;
                        $results[] = ['lead_id' => $leadId, 'status' => 'already_converted'];
                        continue;
                    }

                    $existingLeadQuery = DB::table('leads');
                    if (!empty($exhibitionLead->email) && !empty($exhibitionLead->phone)) 
                    {
                        $existingLeadQuery->where(function ($q) use ($exhibitionLead) 
                        {
                            $q->where('email', $exhibitionLead->email)
                            ->orWhere('phone', $exhibitionLead->phone);
                        });
                    } 
                    elseif (!empty($exhibitionLead->email)) 
                    {
                        $existingLeadQuery->where('email', $exhibitionLead->email);
                    } 
                    elseif (!empty($exhibitionLead->phone)) 
                    {
                        $existingLeadQuery->where('phone', $exhibitionLead->phone);
                    } 
                    else 
                    {
                        $existingLeadQuery = null;
                    }

                    $existingLead = $existingLeadQuery ? $existingLeadQuery->first() : null;

                    if ($existingLead) 
                    {
                        DB::table('exhibition_leads')->where('id', $leadId)->update([
                            'is_converted' => 1,
                            'updated_at' => now(),
                        ]);

                        $convertedCount++;
                        $results[] = [
                            'lead_id' => $leadId,
                            'crm_lead_id' => $existingLead->id,
                            'status' => 'already_exists'
                        ];
                        continue;
                    }

                    $exhibitionName = $exhibitionLead->exhibition_id
                        ? DB::table('exhibitions')->where('id', $exhibitionLead->exhibition_id)->value('name')
                        : 'Exhibition';

                    $typeIds = [];
                    if (!empty($exhibitionLead->type)) 
                    {
                        $typeNames = json_decode($exhibitionLead->type, true);
                        if (!is_array($typeNames)) 
                        {
                            $typeNames = explode(',', $exhibitionLead->type);
                        }
                        foreach ($typeNames as $typeName) 
                        {
                            $id = DB::table('category')
                                ->whereRaw('LOWER(name) = ?', [strtolower(trim($typeName))])
                                ->value('id');
                            if ($id) 
                            {
                                $typeIds[] = $id;
                            }
                        }
                    }

                    $crmLeadData = [
                        'name' => $exhibitionLead->name ?? '',
                        'user_id' => 1,
                        'email' => $exhibitionLead->email ?? '',
                        'phone' => $exhibitionLead->phone ?? '',
                        'whatsapp_no' => $exhibitionLead->whatsapp ?? '',
                        'notes' => $exhibitionLead->description ?? '',
                        'source' => 'Exhibition',
                        'campaign' => $exhibitionName,
                        'stage_id' => 1,
                        'type' => !empty($typeIds) ? implode(',', $typeIds) : null,
                        'classification' => 'Exhibition Lead',
                        'status' => 'NEW LEAD',
                        'unallocated_lead' => 1,
                        'is_allocated' => 0,
                        'lead_date' => $exhibitionLead->date ?? now(),
                        'last_comment' => 'Converted from exhibition lead',
                        'conversion_type' => 'Exhibition',
                        'checklist_status' => 'open',
                        'created_at' => now(),
                        'updated_at' => now(),
                        'country_name' => $exhibitionLead->country ?? '',
                        'address' => $exhibitionLead->address ?? '',
                        'company' => $exhibitionLead->company ?? null,
                        'website' => $exhibitionLead->website ?? null,
                        'fax' => $exhibitionLead->fax ?? null,
                        'visit_card' => $exhibitionLead->visit_card ?? null,
                        'operating_country' => $exhibitionLead->operating_country ?? null,
                    ];

                    $crmLeadId = DB::table('leads')->insertGetId($crmLeadData);

                    DB::table('exhibition_leads')->where('id', $leadId)->update([
                        'is_converted' => 1,
                        'updated_at' => now(),
                    ]);

                    $convertedCount++;
                    $results[] = [
                        'lead_id' => $leadId,
                        'crm_lead_id' => $crmLeadId,
                        'status' => 'converted',
                        'exhibition_name' => $exhibitionName
                    ];

                } 
                catch (\Exception $e) 
                {
                    $failedCount++;
                    $results[] = [
                        'lead_id' => $leadId,
                        'status' => 'failed',
                        'error' => $e->getMessage()
                    ];
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Converted: $convertedCount | Failed: $failedCount",
                'converted_count' => $convertedCount,
                'failed_count' => $failedCount,
                'results' => $results
            ]);

        } 
        catch (\Exception $e) 
        {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Bulk conversion failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function createShareLink(Request $request, $exhibitionId)
    {
        try 
        {
            $exhibition = DB::table('exhibitions')->where('id', $exhibitionId)->first();
            if (!$exhibition)
            {
                return response()->json([
                    'success' => false,
                    'message' => 'Exhibition not found'
                ], 404);
            }
            $shareCode = ExhibitionShareLink::generateShareCode($exhibitionId);
            $shareLink = ExhibitionShareLink::create([
                'exhibition_id' => $exhibitionId,
                'share_code' => $shareCode,
                'user_id' => auth()->id(),
                'expires_at' => null,
                'max_uses' => null,
                'used_count' => 0,
                'is_active' => true,
            ]);
            $shareUrl = route('exhibition.share.access', $shareCode);
            return response()->json([
                'success' => true,
                'message' => 'Share link created successfully',
                'data' => [
                    'share_link' => $shareUrl,
                    'share_code' => $shareCode,
                    'expires_at' => '',
                    'max_uses' =>'',
                    'created_at' => $shareLink->created_at->format('Y-m-d H:i:s'),
                ]
            ]);

        }
        catch (\Exception $e) 
        {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create share link: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getShareLinks($exhibitionId)
    {
        try 
        {
            $exhibition = DB::table('exhibitions')->where('id', $exhibitionId)->first();
            if (!$exhibition) 
            {
                return response()->json([
                    'success' => false,
                    'message' => 'Exhibition not found'
                ], 404);
            }

            $shareLinks = ExhibitionShareLink::where('exhibition_id', $exhibitionId)
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($link) 
                {
                    return [
                        'id' => $link->id,
                        'share_code' => $link->share_code,
                        'share_url' => route('exhibition.share.access', $link->share_code),
                        'user_name' => $link->user ? $link->user->name : 'System',
                        'expires_at' => $link->expires_at ? $link->expires_at->format('Y-m-d H:i:s') : 'Never',
                        'max_uses' => $link->max_uses,
                        'used_count' => $link->used_count,
                        'is_active' => $link->is_active,
                        'can_be_used' => $link->canBeUsed(),
                        'created_at' => $link->created_at->format('Y-m-d H:i:s'),
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $shareLinks
            ]);

        } 
        catch (\Exception $e) 
        {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch share links: ' . $e->getMessage()
            ], 500);
        }
    }

    public function accessShareLink($shareCode)
    {
        $data = DB::table('exhibition_share_links as esl')
            ->join('exhibitions as e', 'e.id', '=', 'esl.exhibition_id')
            ->where('esl.share_code', $shareCode)
            ->where('esl.is_active', 1)
            ->select(
                'esl.id as share_link_id',
                'esl.share_code',
                'e.id as id',            
                'e.name',
                'e.description',
                'e.start_date',
                'e.end_date'
            )
            ->first();

        if (!$data) 
        {
            abort(404, 'Share link not found or expired.');
        }
        return view('exhibition.share-form', [
            'shareCode'  => $data->share_code,
            'exhibition' => $data
        ]);
    }

    public function submitShareForm(Request $request, $shareCode)
    {
        try 
        {
            $shareLink = ExhibitionShareLink::where('share_code', $shareCode)
                ->firstOrFail();
                
            $validator = Validator::make($request->all(), [
                'name'               => 'required|string|max:255',
                'phone'              => 'required|string|max:20',
                'phone_code'         => 'required|string|max:10',
                'whatsapp'           => 'nullable|string|max:20',
                'whatsapp_code'      => 'nullable|string|max:10',
                'email'              => 'nullable|email|max:255',
                'company'            => 'nullable|string|max:255',
                'website'            => 'nullable|string|max:255',
                'fax'                => 'nullable|string|max:50',
                'country_id'         => 'nullable|exists:countries,id',
                'operating_country'  => 'nullable|array',
                'operating_country.*'=> 'string|max:100',
                'address'            => 'nullable|string',
                'description'        => 'nullable|string',
                'date'               => 'nullable|date',
                'reminder_date'      => 'nullable|date',
                'visit_card' => 'nullable|array',
                'visit_card.*' => 'file|mimes:jpg,jpeg,png,pdf|max:50000',
            ]);

            if ($validator->fails()) 
            {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $validated = $validator->validated();
            $fullPhone = $validated['phone_code'] . preg_replace('/\D/', '', $validated['phone']);
            $fullWhatsapp = null;
            
            if (!empty($validated['whatsapp'])) 
            {
                $fullWhatsapp = $validated['whatsapp_code'] . preg_replace('/\D/', '', $validated['whatsapp']);
            }
            $countryName = null;
            if (!empty($validated['country_id'])) 
            {
                $country = DB::table('countries')->where('id', $validated['country_id'])->first();
                $countryName = $country ? $country->name : null;
            }

            $leadData = [
                'exhibition_id' => $shareLink->exhibition_id,
                'name'          => $validated['name'],
                'phone'         => $fullPhone,
                'whatsapp'      => $fullWhatsapp,
                'email'         => $validated['email'] ?? null,
                'company'       => $validated['company'] ?? null,
                'website'       => $validated['website'] ?? null,
                'fax'           => $validated['fax'] ?? null,
                'country'       => $countryName,
                'address'       => $validated['address'] ?? null,
                'description'   => $validated['description'] ?? null,
                'remarks'       => $validated['remarks'] ?? null,
                'created_at'    => now(),
                'updated_at'    => now(),
            ];

            if (isset($validated['operating_country'])) 
            {
                $leadData['operating_country'] = json_encode($validated['operating_country']);
            }

            if (!empty($validated['date'])) 
            {
                $leadData['date'] = date('Y-m-d H:i:s', strtotime($validated['date']));
            }

            if (!empty($validated['reminder_date'])) 
            {
                $leadData['reminder_date'] = date('Y-m-d H:i:s', strtotime($validated['reminder_date']));
            }

            $visitCards = [];
            $deviceId = $validated['device_id'] ?? Str::uuid()->toString();
            if ($request->hasFile('visit_card')) 
            {
                foreach ($request->file('visit_card') as $file) 
                {
                    $filename = 'visit_card_' . $deviceId . '_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('visit_cards', $filename, 'public');
                    $visitCards[] = $path;
                }

                $leadData['visit_card'] = json_encode($visitCards);
            }
            
            $leadId = DB::table('exhibition_leads')->insertGetId($leadData);
            $shareLink->increment('used_count');

            return redirect()->back()->with('success', 'Form submitted successfully');

        } 
        catch (\Exception $e) 
        {
            return redirect()->back()
                ->with('error', 'Error submitting form: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function activate($id)
    {
        try 
        {
            DB::beginTransaction();

            $exhibition = DB::table('exhibitions')->where('id', $id)->first();
            
            if (!$exhibition) 
            {
                return response()->json([
                    'success' => false,
                    'message' => 'Exhibition not found!'
                ], 404);
            }
            DB::table('exhibitions')->update(['is_active' => 0]);
            DB::table('exhibitions')->where('id', $id)->update([
                'is_active' => 1,
                'updated_at' => now()
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Exhibition activated successfully!'
            ]);

        } 
        catch (\Exception $e) 
        {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to activate exhibition: ' . $e->getMessage()
            ], 500);
        }
    }

    public function import(Request $request, $exhibitionId)
    {
        try 
        {
            $request->validate([
                'file' => 'required|mimes:csv,txt|max:2048'
            ]);

            $file = $request->file('file');
            
            if (!$file->isValid()) 
            {
                return redirect()
                    ->back()
                    ->with('error', 'Invalid file upload.');
            }

            $filePath = $file->getRealPath();
            
            if (!file_exists($filePath)) 
            {
                return redirect()
                    ->back()
                    ->with('error', 'File not found.');
            }

            $file = fopen($filePath, 'r');
            
            if (!$file) 
            {
                return redirect()
                    ->back()
                    ->with('error', 'Unable to open file.');
            }
            $header = fgetcsv($file);
            if (!$header) 
            {
                fclose($file);
                return redirect()
                    ->back()
                    ->with('error', 'Empty or invalid CSV file.');
            }

            $leads = [];
            $rowCount = 0;
            $importedCount = 0;

            while (($row = fgetcsv($file)) !== false) 
            {
                $rowCount++;
                if (empty(array_filter($row)))
                {
                    continue;
                }
                $data = [];
                foreach ($header as $index => $columnName) 
                {
                    $cleanColumnName = trim($columnName);
                    if (isset($row[$index])) 
                    {
                        $data[$cleanColumnName] = trim($row[$index]);
                    } 
                    else 
                    {
                        $data[$cleanColumnName] = null;
                    }
                }
                if ($rowCount <= 3) 
                {
                    \Log::info('Row ' . $rowCount . ' data:', $data);
                }
                $phonesRaw = $data['Phone'] ?? '';
                $phonesRaw = preg_replace('/^\.\+/', '+', $phonesRaw);
                $phonesRaw = str_replace(['.', ','], ' ', $phonesRaw);
                $phoneNumbers = preg_split('/\s+/', trim($phonesRaw));
                $phoneNumbers = array_filter($phoneNumbers, function($phone) 
                {
                    return !empty(trim($phone));
                });
                $phoneNumbers = array_values($phoneNumbers);
                $phone = !empty($phoneNumbers[0]) ? $phoneNumbers[0] : null;
                $whatsapp = !empty($phoneNumbers[1]) ? $phoneNumbers[1] : $phone;
                $leadData = [
                    'exhibition_id'     => $exhibitionId,
                    'company'           => $data['Company'] ?? null,
                    'country'           => $data['Country'] ?? null,
                    'phone'             => $phone,
                    'whatsapp'          => $whatsapp,
                    'email'             => $data['Email'] ?? null,
                    'website'           => $data['Website'] ?? null,
                    'address'           => $data['Address'] ?? null,
                    'remarks'           => $data['Remarks'] ?? null,
                    'name'              => $data['SPOC'] ?? null,
                    'type'              => null,
                    'description'       => $data['Enquiry'] ?? null,
                    'device_id'         => 'bulk_import',
                    'is_converted'      => 0,
                    'created_at'        => now(),
                    'updated_at'        => now(),
                ];

                $leads[] = $leadData;
                $importedCount++;
                if (count($leads) >= 50) 
                {
                    try 
                    {
                        DB::table('exhibition_leads')->insert($leads);
                        $leads = [];
                    } 
                    catch (\Exception $e) 
                    {
                        fclose($file);
                        return redirect()
                            ->back()
                            ->with('error', 'Error inserting batch: ' . $e->getMessage());
                    }
                }
            }
            if (!empty($leads)) 
            {
                try 
                {
                    DB::table('exhibition_leads')->insert($leads);
                } 
                catch (\Exception $e) 
                {
                    fclose($file);
                    return redirect()
                        ->back()
                        ->with('error', 'Error inserting data: ' . $e->getMessage());
                }
            }

            fclose($file);
            return redirect()
                ->back()
                ->with('success', $importedCount . ' leads imported successfully!');

        } 
        catch (\Exception $e) 
        {
            return redirect()
                ->back()
                ->with('error', 'Import failed: ' . $e->getMessage());
        }
    }
}