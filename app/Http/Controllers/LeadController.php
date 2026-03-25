<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Flasher\Laravel\Facade\Flasher;
use League\Csv\Reader;
use App\Services\LeadService;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
class LeadController extends Controller
{
    protected $leadService;
    const STATUS_NEW_LEAD = 'NEW LEAD';
    const STATUS_ALLOCATED = 'allocated_lead';
    const STATUS_PENDING = 'PENDING';
    const STATUS_PROCESSING = 'PROCESSING';
    const STATUS_INTERESTED = 'INTERESTED';
    const STATUS_WHATSAPP = 'WHATSAPP';
    const STATUS_PARTIALLY_COMPLETE = 'PARTIALLY COMPLETE';
    const STATUS_CALL_SCHEDULED = 'CALL SCHEDULED';
    const STATUS_MEETING_SCHEDULED = 'MEETING SCHEDULED';
    const STATUS_VISIT_SCHEDULED = 'VISIT SCHEDULED';
    const STATUS_VISIT_DONE = 'VISIT DONE';
    const STATUS_BOOKED = 'BOOKED';
    const STATUS_COMPLETED = 'Completed';
    const STATUS_CANCELLED = 'Cancelled';
    const STATUS_FUTURE = 'FUTURE LEAD';
    const STATUS_TRANSFER = 'TRANSFER LEAD';
    const STATUS_NOT_REACHABLE = 'NOT REACHABLE';
    const STATUS_WRONG_NUMBER = 'WRONG NUMBER';
    const STATUS_CHANNEL_PARTNER = 'CHANNEL PARTNER';
    const STATUS_NOT_INTERESTED = 'NOT INTERESTED';
    const STATUS_NOT_PICKED = 'NOT PICKED';
    const STATUS_LOST = 'LOST';
    const STATUS_SALE_MANAGER = 'SM NEW LEADS';

    public function __construct(LeadService $leadService)
    {
        $this->leadService = $leadService;
    }

    public function add_lead()
    {
        $categorys = DB::table('inv_catg')->get();
        $categoryList = DB::table('category')->get();
        $sources = DB::table('sources')->get();
        $campaigns = DB::table('campaigns')->get();
        $projects = DB::table('projects')->get();
        $cities = DB::table('state_district')->orderBy('District', 'asc')->get();
        $users = DB::table('users')->where('is_active', 1)->get();
        return view('lead.index', compact('categorys', 'sources', 'campaigns', 'projects', 'cities', 'categoryList', 'users'));
    }

    public function create_lead(Request $request)
    {
        $result = $this->leadService->createLead($request);
        
        if (!$result['success']) 
        {
            return redirect()->back()->withInput();
        }
        
        return redirect($result['redirect']);
    }

    public function importUpload(Request $request)
    {
        try 
        {
            $request->validate([
                'file' => 'required|mimes:csv,txt|max:2048',
            ]);

            if (!$request->hasFile('file')) 
            {
                session()->flash('error', '❌ No file uploaded.');
                return redirect()->back();
            }

            $file = $request->file('file');
            if (!$file->isValid()) 
            {
                session()->flash('error', '❌ Uploaded file is not valid.');
                return redirect()->back();
            }

            $path = $file->storeAs('temp', uniqid() . '_' . $file->getClientOriginalName());
            $fullPath = storage_path('app/' . $path);

            if (!file_exists($fullPath)) 
            {
                session()->flash('error', '❌ File not found after upload.');
                return redirect()->back();
            }

            $csv = \League\Csv\Reader::createFromPath($fullPath, 'r');
            $csv->setHeaderOffset(0);
            $records = $csv->getRecords();

            $success = 0;
            $duplicate = 0;
            $errors = [];
            $validationErrors = [];

            $user_type = session()->get('user_type');
            $user_id   = session()->get('user_id');
            $defaultStatus = in_array($user_type, ['super_admin', 'divisional_head']) ? 'allocated_lead' : 'NEW LEAD';
            $headers = $csv->getHeader();
            
            foreach ($records as $index => $row) 
            {
                $rowNumber = $index + 2;
                try 
                {
                    if (empty(array_filter($row))) 
                    {
                        $validationErrors[] = "Row $rowNumber: Empty row skipped";
                        continue;
                    }
                    
                    $phone = '';
                    foreach ($row as $key => $value) 
                    {
                        $keyLower = strtolower($key);
                        if (in_array($keyLower, ['phone no.', 'phone', 'phone no', 'phone number'])) 
                        {
                            $phone = trim($value);
                            break;
                        }
                    }
                    
                    if (empty($phone)) 
                    {
                        $validationErrors[] = "Row $rowNumber: Phone number is missing or empty";
                        continue;
                    }
                    
                    $originalPhone = $phone;
                    $phone = preg_replace('/\D/', '', $phone);
                    
                    if (strlen($phone) == 12 && substr($phone, 0, 2) == '91') 
                    {
                        $phone = substr($phone, 2);
                    }
                    
                    if (!$phone || !preg_match('/^[6-9]\d{9}$/', $phone)) 
                    {
                        $validationErrors[] = "Row $rowNumber: Invalid phone number '$originalPhone'. Must be 10 digits starting with 6-9";
                        continue;
                    }

                    $email = '';
                    foreach ($row as $key => $value) 
                    {
                        $keyLower = strtolower($key);
                        if (in_array($keyLower, ['e-mail', 'email', 'mail'])) 
                        {
                            $email = trim($value);
                            break;
                        }
                    }
                    
                    if (!empty($email)) 
                    {
                        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
                        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) 
                        {
                            $validationErrors[] = "Row $rowNumber: Invalid email format '$email'";
                            continue;
                        }
                    }

                    $name = '';
                    foreach ($row as $key => $value) 
                    {
                        $keyLower = strtolower($key);
                        if (in_array($keyLower, ['name', 'full name', 'customer name']))
                        {
                            $name = trim($value);
                            break;
                        }
                    }
                    
                    if (empty($name)) 
                    {
                        $validationErrors[] = "Row $rowNumber: Name is required";
                        continue;
                    }
                    
                    $source = '';
                    $campaign = '';
                    $whatsapp = '';
                    
                    foreach ($row as $key => $value) 
                    {
                        $keyLower = strtolower($key);
                        if (in_array($keyLower, ['source', 'lead source'])) 
                        {
                            $source = trim($value);
                        }
                        if (in_array($keyLower, ['campaign', 'campaign name'])) 
                        {
                            $campaign = trim($value);
                        }
                        if (in_array($keyLower, ['alternative no.', 'whatsapp', 'whatsapp no', 'alt phone'])) 
                        {
                            $whatsapp = trim($value);
                        }
                    }
                    if (!empty($campaign)) 
                    {
                        $campaign = preg_replace('/[\x80-\x9F]/', '', $campaign); 
                        $campaign = preg_replace('/[^\x20-\x7E]/', '', $campaign);
                        $campaign = preg_replace('/�/', '-', $campaign);
                        $campaign = preg_replace('/[\x96]/', '-', $campaign);
                        $campaign = preg_replace('/[\x93\x94]/', '"', $campaign);
                        $campaign = preg_replace('/[\x91\x92]/', "'", $campaign);
                        $campaign = mb_convert_encoding($campaign, 'UTF-8', 'auto');
                    }
                    $name = mb_convert_encoding($name, 'UTF-8', 'auto');
                    $source = mb_convert_encoding($source, 'UTF-8', 'auto');
                    $whatsapp = mb_convert_encoding($whatsapp, 'UTF-8', 'auto');
                    
                    $existingLead = DB::table('leads')
                        ->where('phone', $phone)
                        ->orWhere('phone', '91' . $phone)
                        ->orWhere('phone', '+' . $phone)
                        ->orWhere('phone', '+91' . $phone)
                        ->first();

                    if ($existingLead) 
                    {
                        $duplicate++;
                        continue;
                    }

                    DB::table('leads')->insert([
                        'name'         => $name,
                        'phone'        => $phone,
                        'email'        => $email ?: null,
                        'source'       => $source,
                        'campaign'     => $campaign,
                        'whatsapp_no'  => $whatsapp,
                        'status'       => $defaultStatus,
                        'user_id'      => $user_id,
                        'is_allocated' => 0,
                        'lead_date'    => now(),
                        'created_at'   => now(),
                        'updated_at'   => now(),
                    ]);

                    $success++;

                } 
                catch (\Exception $e) 
                {
                    $errors[] = "Row $rowNumber: Database error - " . $e->getMessage();
                }
            }

            if (file_exists($fullPath)) 
            {
                unlink($fullPath);
            }
            $allMessages = [];
            $summaryMessage = "";
            
            if ($success > 0) 
            {
                $summaryMessage .= "✅ $success lead(s) imported successfully. ";
            }
            
            if ($duplicate > 0) 
            {
                $summaryMessage .= "⚠️ $duplicate duplicate(s) skipped. ";
            }

            if ($success > 0) 
            {
                $allMessages[] = "success✅ $success new lead(s) imported successfully.";
            }
            
            if ($duplicate > 0) 
            {
                $allMessages[] = "warning⚠️ $duplicate lead(s) already exist and were skipped.";
            }
            
            if (!empty($validationErrors)) 
            {
                $displayErrors = array_slice($validationErrors, 0, 10);
                foreach ($displayErrors as $err) 
                {
                    $allMessages[] = "error❌ " . $err;
                }
                
                if (count($validationErrors) > 10) 
                {
                    $allMessages[] = "warning⚠️ And " . (count($validationErrors) - 10) . " more validation errors. Check logs for details.";
                }
                
                if (empty($summaryMessage)) 
                {
                    $summaryMessage = "❌ Found " . count($validationErrors) . " validation error(s). ";
                } 
                else 
                {
                    $summaryMessage .= "❌ Found " . count($validationErrors) . " validation error(s). ";
                }
            }
            
            if (!empty($errors)) 
            {
                foreach ($errors as $err) 
                {
                    $allMessages[] = "error❌ " . $err;
                }
                
                if (empty($summaryMessage)) 
                {
                    $summaryMessage = "❌ Found " . count($errors) . " database error(s). ";
                } 
                else 
                {
                    $summaryMessage .= "❌ Found " . count($errors) . " database error(s). ";
                }
            }
            
            if ($success == 0 && $duplicate == 0 && empty($validationErrors) && empty($errors))
            {
                $allMessages[] = "warning⚠️ No valid data found in CSV file. Headers found: " . implode(', ', $headers);
                $summaryMessage = "⚠️ No valid data found in CSV file.";
            }
            session()->flash('import_messages', $allMessages);
            if (!empty($summaryMessage)) 
            {
                if ($success > 0 && empty($validationErrors) && empty($errors)) 
                {
                    Flasher::addSuccess(trim($summaryMessage));
                } 
                elseif ($success > 0 && (!empty($validationErrors) || !empty($errors))) 
                {
                    Flasher::addWarning(trim($summaryMessage) . " Check details below.");
                } 
                elseif ($duplicate > 0 && $success == 0 && empty($validationErrors) && empty($errors)) 
                {
                    Flasher::addWarning(trim($summaryMessage));
                } 
                else 
                {
                    Flasher::addError(trim($summaryMessage) . " Check details below.");
                }
            } 
            else 
            {
                Flasher::addSuccess("✅ File processed successfully. No leads were imported.");
            }
            
            return redirect()->back();
        } 
        catch (\Exception $e) 
        {
            session()->flash('error', '❌ Unexpected error: ' . $e->getMessage());
            Flasher::addError('❌ Unexpected error: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function allocate_lead(Request $request)
    {
        $lead_name = 'allocate';
        $length = $request->query('length', 10);
        $current_user_id = Session::get('user_id');
        $child_ids = Session::get('child_ids', []);

        if (is_string($child_ids)) 
        {
            $child_ids = array_map('trim', explode(',', $child_ids));
        }

        $user_ids = $child_ids; 

        $query = DB::table('leads as a')
            ->where(function ($q) 
            {
                $q->where('a.status', 'allocated_lead')
                ->orWhereNull('a.status')
                ->orWhere('a.status', '');
            })
            ->whereIn('a.user_id', $user_ids)
            ->where('a.is_allocated', '!=', $current_user_id)
            ->leftJoin('users as b', 'b.id', '=', 'a.user_id')
            ->leftJoin('projects as c', 'c.id', '=', 'a.project_id')
            ->select(
                'a.*',
                'b.name as agent',
                'b.role',
                'c.project_name as project_name'
            )
            ->orderBy('a.is_pinned', 'desc')
            ->orderBy('a.id', 'desc');

        $query = $this->applyLeadFilters($query, $request);
        // dd($query);
        $leads = $query->paginate($length);
        $leads->getCollection()->transform(function($lead) 
        {
            $duplicates = \DB::table('leads')
                ->where('phone', $lead->phone)
                ->where('id', '!=', $lead->id)  
                ->get(['id','status','created_at']);
            $lead->duplicate_count = $duplicates->count();
            $lead->duplicate_details = $duplicates;
            return $lead;
        });
        $user_role = Session::get('user_type');
        $users = ($user_role === 'super_admin')
            ? DB::table('users')->where('is_active', 1)->get()
            : DB::table('users')->whereIn('id', $child_ids)->get();
        $projects = DB::table('projects')->select('id', 'project_name')->distinct()->get();
        $cities = DB::table('state_district')->orderBy('District', 'asc')->get();
        $sources = DB::table('sources')->get();
        $statuses = DB::table('status')->pluck('name', 'id');
        $classifications = DB::table('leads')->distinct()->pluck('classification');
        $agents = DB::table('users')->where('role', 'agent')->pluck('name', 'id');

        $hasFilters = $request->anyFilled([
            'source', 'status','user', 'classification', 'agent', 'lead_days','shared_filter',
            'project', 'date_from', 'date_to'
        ]);

        return view('lead.lead-data', compact(
            'leads', 'lead_name', 'users', 'projects', 'cities', 'length',
            'sources', 'statuses', 'classifications', 'agents', 'hasFilters'
        ));
    }

    // public function allocateLeads(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'user' => 'required|exists:users,id',
    //         'checked' => 'required|array',
    //         'checked.*' => 'exists:leads,id'
    //     ]);

    //     if ($validator->fails()) 
    //     {
    //         foreach ($validator->errors()->all() as $error) 
    //         {
    //             Flasher::addError($error);
    //         }
    //         return redirect()->back();
    //     }

    //     $userType = Session::get('user_type');
    //     $userId = Session::get('user_id');
    //     $allocatedTo = DB::table('users')->where('id', $request->user)->first();

    //     if (!$allocatedTo) 
    //     {
    //         Flasher::addError('User not found');
    //         return redirect()->back();
    //     }
    //     $status = null;
    //     $unAllocated = 0;

    //     if ($userType == 'super_admin') 
    //     {
    //         if ($request->user == $userId) 
    //         {
    //             $status = 'NEW LEAD';
    //             $unAllocated = 0; 
    //         } 
    //         elseif ($allocatedTo->role == 'divisional_head') 
    //         {
    //             $status = 'allocated_lead';
    //             $unAllocated = 1;
    //         } 
    //         elseif ($allocatedTo->role == 'salesman') 
    //         {
    //             $status = 'NEW LEAD';
    //             $unAllocated = 0;
    //         }
    //     } 
    //     elseif ($userType == 'divisional_head') 
    //     {
    //         if ($request->user == $userId) 
    //         {
    //             $status = 'NEW LEAD';
    //             $unAllocated = 0;
    //         } 
    //         elseif ($allocatedTo->role == 'salesman') 
    //         {
    //             $status = 'NEW LEAD';
    //             $unAllocated = 0;
    //         }
    //     }
    //     elseif ($userType == 'salesman') 
    //     {
    //         if ($allocatedTo->role == 'salesman') 
    //         {
    //             $status = 'NEW LEAD';
    //             $unAllocated = 0;
    //         } 
    //         else 
    //         {
    //             Flasher::addError('Salesman can only allocate leads to other salesmen.');
    //             return redirect()->back();
    //         }
    //     } 
    //     else 
    //     {
    //         Flasher::addError('Invalid user role.');
    //         return redirect()->back();
    //     }

    //     try 
    //     {
    //         DB::beginTransaction();
    //         DB::table('leads')
    //             ->whereIn('id', $request->checked)
    //             ->update([
    //                 'allocated_date' => now(),
    //                 'unallocated_lead' => $unAllocated,
    //                 'is_allocated' => $userId,
    //                 'user_id' => $request->user,
    //                 'status' => $status,
    //                 'updated_date' => now()
    //             ]);

    //         foreach ($request->checked as $leadId) 
    //         {
    //             DB::table('lead_comments')->insert([
    //                 'lead_id' => $leadId,
    //                 'user_id' => $userId,
    //                 'comment' => 'Lead allocated to ' . $allocatedTo->name,
    //                 'status' => 'ALLOCATED',
    //                 'created_date' => now()
    //             ]);
    //         }

    //         DB::commit();
    //         Flasher::addSuccess('Leads allocated successfully!');
    //         return redirect()->route('lead.allocate');
    //     } 
    //     catch (\Exception $e) 
    //     {
    //         DB::rollBack();
    //         Flasher::addError('Error allocating leads: ' . $e->getMessage());
    //         return redirect()->back();
    //     }
    // }

    public function allocateLeads(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user' => 'required|exists:users,id',
            'checked' => 'required|array',
            'checked.*' => 'exists:leads,id',
            'send_to_new_lead' => 'nullable|in:0,1,on'
        ]);

        if ($validator->fails()) 
        {
            foreach ($validator->errors()->all() as $error) 
            {
                Flasher::addError($error);
            }
            return redirect()->back();
        }

        $userType = Session::get('user_type');
        $userId = Session::get('user_id');
        $allocatedTo = DB::table('users')->where('id', $request->user)->first();
        $sendToNewLead = $request->has('send_to_new_lead') && ($request->send_to_new_lead === 'on' || $request->send_to_new_lead == '1');

        if (!$allocatedTo) 
        {
            Flasher::addError('User not found');
            return redirect()->back();
        }
        
        $status = null;
        $unAllocated = 0;
        if ($sendToNewLead) 
        {
            $status = 'NEW LEAD';
            $unAllocated = 0;
        } 
        else 
        {
            if ($userType == 'super_admin') 
            {
                if ($request->user == $userId) 
                {
                    $status = 'NEW LEAD';
                    $unAllocated = 0; 
                } 
                elseif ($allocatedTo->role == 'divisional_head') 
                {
                    $status = 'allocated_lead';
                    $unAllocated = 1;
                } 
                elseif ($allocatedTo->role == 'salesman') 
                {
                    $status = 'NEW LEAD';
                    $unAllocated = 0;
                }
            } 
            elseif ($userType == 'divisional_head') 
            {
                if ($request->user == $userId) 
                {
                    $status = 'NEW LEAD';
                    $unAllocated = 0;
                } 
                elseif ($allocatedTo->role == 'salesman') 
                {
                    $status = 'NEW LEAD';
                    $unAllocated = 0;
                }
            }
            elseif ($userType == 'salesman') 
            {
                if ($allocatedTo->role == 'salesman') 
                {
                    $status = 'NEW LEAD';
                    $unAllocated = 0;
                } 
                else 
                {
                    Flasher::addError('Salesman can only allocate leads to other salesmen.');
                    return redirect()->back();
                }
            } 
            else 
            {
                Flasher::addError('Invalid user role.');
                return redirect()->back();
            }
        }

        try 
        {
            DB::beginTransaction();
            DB::table('leads')
                ->whereIn('id', $request->checked)
                ->update([
                    'allocated_date' => now(),
                    'unallocated_lead' => $unAllocated,
                    'is_allocated' => $userId,
                    'user_id' => $request->user,
                    'status' => $status,
                    'updated_date' => now()
                ]);

            foreach ($request->checked as $leadId) 
            {
                $commentText = $sendToNewLead 
                    ? 'Lead allocated to ' . $allocatedTo->name . ' (Sent to NEW LEAD)'
                    : 'Lead allocated to ' . $allocatedTo->name;
                    
                DB::table('lead_comments')->insert([
                    'lead_id' => $leadId,
                    'user_id' => $userId,
                    'comment' => $commentText,
                    'status' => $status,
                    'created_date' => now()
                ]);
            }

            DB::commit();
            
            $successMessage = $sendToNewLead 
                ? 'Leads allocated successfully as NEW LEAD!' 
                : 'Leads allocated successfully!';
                
            Flasher::addSuccess($successMessage);
            return redirect()->route('lead.allocate');
        } 
        catch (\Exception $e) 
        {
            DB::rollBack();
            Flasher::addError('Error allocating leads: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function unallocated_lead(Request $request)
    {
        $user_type = Session::get('user_type');
        $user_id = Session::get('user_id');
        $length = $request->query('length', 10);
        $lead_name = 'unallocated';
        $user_role = Session::get('user_type');
        $child_ids = Session::get('child_ids', []);
        if (is_string($child_ids)) 
        {
            $child_ids = array_map('trim', explode(',', $child_ids));
        }

        $users = ($user_role === 'super_admin')
            ? DB::table('users')->where('is_active', 1)->get()
            : DB::table('users')->whereIn('id', $child_ids)->get();

        if (!in_array($user_type, ['super_admin', 'divisional_head'])) 
        {
            abort(403, 'Unauthorized');
        }

        $query = DB::table('leads as a')
            ->join('users as b', 'b.id', '=', 'a.user_id')
            ->leftJoin('projects as c', 'c.id', '=', 'a.project_id')
            ->select(
                'a.*',
                'b.name as agent',
                'b.role',
                'c.project_name as project_name'
            )
            ->where('a.unallocated_lead', 1)
            ->where('a.is_allocated', $user_id)
            ->orderBy('a.is_pinned', 'desc')
            ->orderByDesc('a.id'); 

        $query = $this->applyLeadFilters($query, $request);
        $leads = $query->paginate($length);
        $leads->getCollection()->transform(function($lead) 
        {
            $duplicates = \DB::table('leads')
                ->where('phone', $lead->phone)
                ->where('id', '!=', $lead->id) 
                ->get(['id','status','created_at']);
            $lead->duplicate_count = $duplicates->count();
            $lead->duplicate_details = $duplicates; 
            return $lead;
        });
        $projects = DB::table('projects')->select('id', 'project_name')->distinct()->get();
        $cities = DB::table('state_district')->orderBy('District', 'asc')->get();
        $sources = DB::table('sources')->get();
        $statuses = DB::table('status')->pluck('name', 'id');
        $classifications = DB::table('leads')->distinct()->pluck('classification');
        $agents = DB::table('users')->where('role', 'agent')->pluck('name', 'id');

        $hasFilters = $request->anyFilled([
            'source', 'status','user', 'classification', 'agent', 'lead_days','shared_filter',
            'project', 'date_from', 'date_to', 'users'
        ]);

        return view('lead.lead-data', compact(
            'leads', 'lead_name', 'projects', 'cities', 'length',
            'sources', 'statuses', 'classifications', 'agents', 'hasFilters', 'users'
        ));
    }

    public function getLeadsByStatusType(Request $request, $status, $lead_name)
    {
        $length = $request->query('length', 10);

        if (in_array(strtolower($lead_name), ['booked', 'completed', 'cancelled'])) 
        {
            $conversionType = ucfirst(strtolower($lead_name));

            if ($conversionType === 'Booked') 
            {
                $query = $this->getLeadsByStatus('BOOKED', $lead_name);
            }
            else 
            {
                $query = $this->getLeadsByConversionType($conversionType);
            }
        } 
        else 
        {
            $query = $this->getLeadsByStatus($status, $lead_name);
        }
        $query = $this->applyLeadFilters($query, $request);
        $leads = $query->paginate($length);
        $leads->getCollection()->transform(function($lead) 
        {
            $duplicates = DB::table('leads')
                ->where('phone', $lead->phone)
                ->where('id', '!=', $lead->id)  
                ->get(['id','status','created_at']);
            $lead->duplicate_count = $duplicates->count();
            $lead->duplicate_details = $duplicates;
            return $lead;
        });

        return $this->renderLeadDataView($leads, $lead_name, $length, $request);
    }

    public function new_lead(Request $request)
    {
        return $this->getLeadsByStatusType($request, self::STATUS_NEW_LEAD, 'new');
    }

    public function transfer_leads(Request $request)
    {
        return $this->getLeadsByStatusType($request, self::STATUS_TRANSFER, 'transfer');
    }

    public function pending(Request $request)
    {
        return $this->getLeadsByStatusType($request, self::STATUS_PENDING, 'pending');
    }

    public function processing(Request $request)
    {
        return $this->getLeadsByStatusType($request, self::STATUS_PROCESSING, 'processing');
    }

    public function interested(Request $request)
    {
        return $this->getLeadsByStatusType($request, self::STATUS_INTERESTED, 'interested');
    }

    public function whatsapp(Request $request)
    {
        return $this->getLeadsByStatusType($request, self::STATUS_WHATSAPP, 'whatsapp');
    }

    public function partially_complete(Request $request)
    {
        return $this->getLeadsByStatusType($request, self::STATUS_PARTIALLY_COMPLETE, 'partially_complete');
    }

    public function call_scheduled(Request $request)
    {
        return $this->getLeadsByStatusType($request, self::STATUS_CALL_SCHEDULED, 'call_scheduled');
    }

    public function meeting_scheduled(Request $request)
    {
        return $this->getLeadsByStatusType($request, self::STATUS_MEETING_SCHEDULED, 'meeting_scheduled');
    }

    public function visit_scheduled(Request $request)
    {
        return $this->getLeadsByStatusType($request, self::STATUS_VISIT_SCHEDULED, 'visit_scheduled');
    }

    public function visit_done(Request $request)
    {
        return $this->getLeadsByStatusType($request, self::STATUS_VISIT_DONE, 'visit_done');
    }

    public function booked(Request $request)
    {
        return $this->getLeadsByStatusType($request, self::STATUS_BOOKED, 'booked');
    }

    public function completed(Request $request)
    {
        return $this->getLeadsByStatusType($request, self::STATUS_COMPLETED, 'completed');
    }

    public function cancelled(Request $request)
    {
        return $this->getLeadsByStatusType($request, self::STATUS_CANCELLED, 'cancelled');
    }

    public function future(Request $request)
    {
        return $this->getLeadsByStatusType($request, self::STATUS_FUTURE, 'future');
    }

    public function not_reachable(Request $request)
    {
        return $this->getLeadsByStatusType($request, self::STATUS_NOT_REACHABLE, 'not_reachable');
    }

    public function wrong_number(Request $request)
    {
        return $this->getLeadsByStatusType($request, self::STATUS_WRONG_NUMBER, 'wrong_number');
    }

    public function channel_partner(Request $request)
    {
        return $this->getLeadsByStatusType($request, self::STATUS_CHANNEL_PARTNER, 'channel_partner');
    }

    public function not_interested(Request $request)
    {
        return $this->getLeadsByStatusType($request, self::STATUS_NOT_INTERESTED, 'not_interested');
    }

    public function not_picked(Request $request)
    {
        return $this->getLeadsByStatusType($request, self::STATUS_NOT_PICKED, 'not_picked');
    }

    public function lost(Request $request)
    {
        return $this->getLeadsByStatusType($request, self::STATUS_LOST, 'lost');
    }

    public function sale_manager(Request $request)
    {
        return $this->getLeadsByStatusType($request, self::STATUS_SALE_MANAGER, 'sale_manager');
    }

    public function all_lead(Request $request)
    {
        $lead_name = 'all_lead';
        $requestedLength = $request->query('length', 10); 
        $length = $request->query('length', 10);
        $current_user_id = Session::get('user_id');
        $child_ids = Session::get('child_ids', []);

        if (is_string($child_ids)) 
        {
            $child_ids = array_map('trim', explode(',', $child_ids));
        }

        $user_ids = array_merge([$current_user_id], $child_ids);
        $query = DB::table('leads as a')
            ->join('users as b', 'b.id', '=', 'a.user_id')
            ->leftJoin('projects as c', 'c.id', '=', 'a.project_id')
            ->select('a.*', 'b.name as agent', 'b.role', 'c.project_name as project_name')
            ->where('a.status', '!=', 'allocated_lead')
            ->orderBy('a.is_pinned', 'desc')
            ->orderBy('a.id', 'desc');

        if (!$request->filled('user')) 
        {
            $query->whereIn('a.user_id', $user_ids);
        }

        $this->applyLeadFilters($query, $request);
        if ($request->filled('shared_filter')) 
        {
            $sharedFilter = $request->shared_filter;
            if ($sharedFilter == 'shared_by_me') 
            {
                $query->where('a.user_id', $current_user_id)
                    ->whereNotNull('a.lead_shared_with')
                    ->where('a.lead_shared_with', '!=', '');
            } 
            elseif ($sharedFilter == 'shared_with_me') 
            {
                $query->where(function($q) use ($current_user_id) 
                {
                    $q->whereNotNull('a.lead_shared_with')
                    ->where(function($q2) use ($current_user_id) 
                    {
                        $q2->where('a.lead_shared_with', 'LIKE', '%' . $current_user_id . '%')
                            ->orWhereRaw("FIND_IN_SET(?, a.lead_shared_with)", [$current_user_id]);
                    });
                });
            } 
            elseif ($sharedFilter == 'not_shared') 
            {
                $query->where(function($q) 
                {
                    $q->whereNull('a.lead_shared_with')
                    ->orWhere('a.lead_shared_with', '');
                });
            }
        }

        if ($request->filled('user')) 
        {
            $query->where('a.user_id', $request->user);
        }

        if ($request->filled('source'))
        {
            $query->where('a.source', $request->source);
        }

        if ($request->filled('classification')) 
        {
            $query->where('a.classification', $request->classification);
        }

        if ($request->filled('project')) 
        {
            $query->where('a.project_id', $request->project);
        }

        if ($request->filled('date_from')) 
        {
            $query->whereDate('a.created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) 
        {
            $query->whereDate('a.created_at', '<=', $request->date_to);
        }

        if ($request->filled('lead_days')) 
        {
            $days = (int)$request->lead_days;
            $query->whereDate('a.created_at', '>=', now()->subDays($days));
        }

        if ($length === 'all') 
        {
            $length = DB::table('leads')->count();
        } 
        else 
        {
            $length = (int)$length;
        }

        $leads = $query->paginate($length);
        $leads->getCollection()->transform(function($lead) 
        {
            $duplicates = DB::table('leads')
                ->where('phone', $lead->phone)
                ->where('id', '!=', $lead->id)
                ->get(['id','status','created_at']);
            $lead->duplicate_count = $duplicates->count();
            $lead->duplicate_details = $duplicates;
            return $lead;
        });

        return $this->renderLeadDataView($leads, $lead_name, $length, $request, $requestedLength);
    }

    public function edit_lead($id)
    {
        $lead = DB::table('leads')->where('id', $id)->first();

        if (!$lead) 
        {
            Flasher::addError('Lead not found');
            return redirect()->route('lead.index'); 
        }

        $categorys = DB::table('inv_catg')->get();
        $categoryList = DB::table('category')->get();
        $sources = DB::table('sources')->get();
        $campaigns = DB::table('campaigns')->get();
        $projects = DB::table('projects')->get();

        $currentCategory = DB::table('inv_catg')->where('id', $lead->catg_id)->first();
        $currentSubCategory = DB::table('inv_subcatg')->where('id', $lead->sub_catg_id)->first();
        $users = DB::table('users')->where('is_active', 1)->get();
        $currentSource = $lead->source;
        $currentCampaign = $lead->campaign;
        $currentProject = $lead->project_id;
        $lead->req_state = $lead->property_state;
        $lead->req_location = $lead->property_location;
        $lead->req_city = $lead->property_city;
        return view('lead.index', [
            'lead' => $lead,
            'categorys' => $categorys,
            'sources' => $sources,
            'campaigns' => $campaigns,
            'projects' => $projects,
            'currentCategory' => $currentCategory,
            'currentSubCategory' => $currentSubCategory,
            'currentSource' => $currentSource,
            'currentCampaign' => $currentCampaign,
            'currentProject' => $currentProject,
            'categoryList' => $categoryList,
            'users' => $users
        ]);
    }

    public function update_lead(Request $request, $id)
    {
        $result = $this->leadService->updateLead($request, $id);
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

    public function transfer(Request $request)
    {
        $lead_name = 'transfer';
        $userId = Session::get('user_id');
        $user_role = Session::get('user_type');
        $child_ids = Session::get('child_ids') ?? ''; 
        $users = [];

        if ($user_role == 'super_admin') 
        {
            $users = DB::table('users')
                ->where('is_active', 1)
                ->get();
        } 
        else 
        {
            $childIdArray = array_filter(explode(',', $child_ids));
            $users = DB::table('users')
            ->where(function ($query) use ($childIdArray, $user_role, $userId) 
            {
                $query->whereIn('id', $childIdArray)
                    ->orWhere(function ($q) use ($user_role, $userId) 
                    {
                        $q->where('role', $user_role)
                        ->where('id', '!=', $userId);
                    });
            })
            ->where('is_active', 1)
            ->where('id', '!=', $userId)
            ->get();
        }

        $status_counts = [
            'total_lead' => 0,
            'NEW LEAD' => 0,
            'allocated_lead' => 0,
            'pending_lead' => 0,
            'processing' => 0,
            'interested' => 0,
            'call_schedule' => 0,
            'saleManager_lead' => 0,
            'visit_schedule' => 0,
            'visit_done' => 0,
            'converted' => 0,
            'completed' => 0,
            'cancelled' => 0,
            'booked' => 0,
            'open' => 0,
            'close' => 0
        ];

        $leads = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20);
        $selected_user = null;
        $selected_status = null;
        $selected_user_name = null;
        $from_date = null;
        $to_date = null;

        $statuses = [
            'ALL LEAD', 'NEW LEAD', 'ALLOCATED','TRANSFER LEAD', 'PENDING', 'PROCESSING', 'INTERESTED',
            'CALL SCHEDULED', 'VISIT SCHEDULED', 'VISIT DONE','NOT PICKED','LOST',
            'CONVERTED','MEETING SCHEDULED','WHATSAPP','BOOKED','COMPLETED','CANCELLED','CHANNEL PARTNER','WRONG NUMBER','NOT INTERESTED','FUTURE LEAD','NOT REACHABLE','BOOKED'
        ];

        if ($request->has('user') && $request->has('status')) 
        {
            $selected_user = $request->user;
            $selected_status = $request->status;
            $from_date = $request->from_date;
            $to_date = $request->to_date;
            
            $selected_user_name = DB::table('users')
                ->where('id', $selected_user)
                ->value('name');

            $counts = DB::table('leads')
                ->where('user_id', $selected_user)
                ->select([
                    DB::raw("COUNT(*) as total_lead"),
                    DB::raw("SUM(CASE WHEN status = 'NEW LEAD' THEN 1 ELSE 0 END) as new_lead"),
                    DB::raw("SUM(CASE WHEN status = 'ALLOCATED' THEN 1 ELSE 0 END) as allocated_lead"),
                    DB::raw("SUM(CASE WHEN status = 'PENDING' THEN 1 ELSE 0 END) as pending_lead"),
                    DB::raw("SUM(CASE WHEN status = 'PROCESSING' THEN 1 ELSE 0 END) as processing"),
                    DB::raw("SUM(CASE WHEN status = 'INTERESTED' THEN 1 ELSE 0 END) as interested"),
                    DB::raw("SUM(CASE WHEN status = 'CALL SCHEDULED' THEN 1 ELSE 0 END) as call_schedule"),
                    DB::raw("SUM(CASE WHEN status = 'SM NEW LEADS' THEN 1 ELSE 0 END) as saleManager_lead"),
                    DB::raw("SUM(CASE WHEN status = 'VISIT SCHEDULED' THEN 1 ELSE 0 END) as visit_schedule"),
                    DB::raw("SUM(CASE WHEN status = 'VISIT DONE' THEN 1 ELSE 0 END) as visit_done"),
                    DB::raw("SUM(CASE WHEN status = 'CONVERTED' THEN 1 ELSE 0 END) as converted"),
                    DB::raw("SUM(CASE WHEN conversion_type = 'Completed' THEN 1 ELSE 0 END) as completed"),
                    DB::raw("SUM(CASE WHEN conversion_type = 'Cancelled' THEN 1 ELSE 0 END) as cancelled"),
                    DB::raw("SUM(CASE WHEN status = 'BOOKED' THEN 1 ELSE 0 END) as booked"),
                    DB::raw("SUM(CASE WHEN conversion_type = 'open' THEN 1 ELSE 0 END) as open"),
                    DB::raw("SUM(CASE WHEN conversion_type = 'close' THEN 1 ELSE 0 END) as close")
                ])
                ->first();

            if ($counts) 
            {
                $status_counts = (array)$counts;
            }

            $query = DB::table('leads')
                ->where('user_id', $selected_user);

            if ($selected_status !== 'ALL LEAD') 
            {
                $query->where('status', $selected_status);
            }

            if ($from_date) 
            {
                $query->whereDate('lead_date', '>=', $from_date);
            }

            if ($to_date) 
            {
                $query->whereDate('lead_date', '<=', $to_date);
            }

            $leads = $query->orderBy('is_pinned', 'desc') 
                ->orderBy('lead_date', 'desc')
                ->paginate(20);
        }

        $projects = DB::table('projects')->get();
        $cities = DB::table('state_district')->orderBy('District', 'asc')->get();
        return view('lead.transfer-lead', compact(
            'lead_name', 'leads', 'users', 'selected_user',
            'selected_status', 'statuses', 'selected_user_name',
            'from_date', 'to_date', 'status_counts','projects', 'cities'
        ));
    }

    public function transfer_user(Request $request)
    {
        $request->validate([
            'from_user' => 'required|exists:users,id',
            'from_status' => 'required|string|max:50',
            'to_user' => 'required|exists:users,id',
            'checked' => 'required_without:all_lead_ids|array',
            'checked.*' => 'exists:leads,id',
            'all_lead_ids' => 'required_without:checked|array',
            'all_lead_ids.*' => 'exists:leads,id',
        ]);

        $leadIds = $request->filled('all_lead_ids') 
            ? $request->all_lead_ids 
            : $request->checked;

        $fromUserId = $request->from_user;
        $fromStatus = $request->from_status;
        $toUserId = $request->to_user;

        DB::beginTransaction();
        try 
        {
            foreach ($leadIds as $leadId) 
            {
                $lead = DB::table('leads')->where('id', $leadId)->first();
                $previousStatus = $lead->status;
                DB::table('leads')->where('id', $leadId)->update([
                    'user_id' => $toUserId,
                    'status' => 'TRANSFER LEAD',
                    'is_allocated' => 1,
                    'unallocated_lead' => 0,
                    'last_comment' => "Lead transferred from user ID $fromUserId ($fromStatus) to user ID $toUserId",
                    'allocated_date' => now(),
                    'updated_date' => now(),
                ]);

                DB::table('lead_comments')->insert([
                    'lead_id' => $leadId,
                    'user_id' => Session::get('user_id'),
                    'comment' => "Lead transferred from user ID $fromUserId ($fromStatus) to user ID $toUserId",
                    'status' => 'TRANSFER LEAD',
                    'created_date' => now(),
                ]);

                DB::table('transfer_leads')->insert([
                    'lead_id' => $leadId,
                    'from' => $fromUserId,
                    'to' => $toUserId,
                    'previous_status' => $previousStatus,
                    'new_status' => 'TRANSFER LEAD',
                ]);
            }

            DB::commit();
            return back()->with('success', count($leadIds) . ' lead(s) transferred successfully.');
        } 
        catch (\Exception $e) 
        {
            DB::rollBack();
            return back()->with('error', 'Transfer failed: ' . $e->getMessage());
        }
    }

    public function transfer_history()
    {
        $from_user = request('from_user');
        $to_user = request('to_user');
        $from_date = request('from_date');
        $to_date = request('to_date');

        $userId = Session::get('user_id');
        $child_ids = Session::get('child_ids', '');
    
        $accessibleUserIds = array_merge([$userId], explode(',', $child_ids));
        $accessibleUserIds = array_filter($accessibleUserIds);
        
        $users = DB::table('users')
            ->whereIn('id', $accessibleUserIds)
            ->select('id', 'name')
            ->get();
        
        $leads = DB::table('transfer_leads')
            ->join('leads', 'transfer_leads.lead_id', '=', 'leads.id')
            ->leftJoin('users as from_user', 'transfer_leads.from', '=', 'from_user.id')
            ->leftJoin('users as to_user', 'transfer_leads.to', '=', 'to_user.id')
            ->select(
                'transfer_leads.*',
                'leads.name as lead_name',
                'leads.phone as lead_phone',
                'from_user.name as from_user_name',
                'to_user.name as to_user_name'
            )
            ->where(function($query) use ($accessibleUserIds) 
            {
                $query->whereIn('transfer_leads.from', $accessibleUserIds)
                    ->orWhereIn('transfer_leads.to', $accessibleUserIds);
            })
            ->when($from_user, function($query) use ($from_user) 
            {
                return $query->where('transfer_leads.from', $from_user);
            })
            ->when($to_user, function($query) use ($to_user) 
            {
                return $query->where('transfer_leads.to', $to_user);
            })
            ->when($from_date, function($query) use ($from_date)
            {
                return $query->whereDate('transfer_leads.created_at', '>=', $from_date);
            })
            ->when($to_date, function($query) use ($to_date) 
            {
                return $query->whereDate('transfer_leads.created_at', '<=', $to_date);
            })
            ->orderBy('transfer_leads.created_at', 'desc')
            ->paginate(15)
            ->withQueryString();
        
        return view('lead.transfer-lead-history', compact('leads', 'users'));
    }

    public function transfer_lead()
    {
        $lead_name = 'transfer_lead';
        $users = DB::table('users')->select('id', 'name')->get();
        
        $leads = DB::table('transfer_leads')
            ->join('leads', 'transfer_leads.lead_id', '=', 'leads.id')
            ->leftJoin('users as from_user', 'transfer_leads.from', '=', 'from_user.id')
            ->leftJoin('users as to_user', 'transfer_leads.to', '=', 'to_user.id')
            ->select(
                'transfer_leads.*',
                'leads.name as lead_name',
                'leads.phone as lead_phone',
                'leads.status as lead_status',
                'from_user.name as from_user_name',
                'to_user.name as to_user_name'
            )
            ->orderBy('transfer_leads.created_at', 'desc')
            ->paginate(10);
        
        return view('lead.transfer-lead-history', compact('lead_name', 'leads', 'users'));
    }

    public function getCategories($type)
    {
        $availableTypes = DB::table('inv_catg')->distinct()->pluck('type');
        $sampleCategories = DB::table('inv_catg')->limit(5)->get();
        $categories = DB::table('inv_catg')
            ->where('type', $type)
            ->get();
        return response()->json($categories);
    }

    public function getSubCategories($categoryId)
    {
        $subCategories = DB::table('inv_subcatg')
            ->where('catg_id', $categoryId)
            ->get();
            
        return response()->json($subCategories);
    }

    public function getCities($state)
    {
        $cities = DB::table('state_district')
            ->where('state', $state)
            ->get();
            
        return response()->json($cities);
    }

    public function getComments($lead)
    {
        try 
        {
            $comments = DB::table('lead_comments')
                ->where('lead_id', $lead)
                ->leftJoin('users', 'lead_comments.user_id', '=', 'users.id')
                ->select(
                    'lead_comments.*',
                    'users.name as user_name'
                )
                ->orderBy('created_date', 'desc')
                ->get();
                
            return response()->json([
                'success' => true,
                'comments' => $comments
            ]);
        } 
        catch (\Exception $e) 
        {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch comments',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function getLeadsByStatus($status, $lead_name)
    {
        $user_type = Session::get('user_type');
        $user_id = Session::get('user_id');
        $child_ids = Session::get('child_ids');
        
        if (!is_array($child_ids)) 
        {
            $child_ids = $child_ids ? explode(',', $child_ids) : [];
        }

        $query = DB::table('leads as a')
            ->join('users as b', 'b.id', '=', 'a.user_id')
            ->leftJoin('projects as c', 'c.id', '=', 'a.project_id')
            ->select(
                'a.*',
                'b.name as agent',
                'b.role',
                'c.project_name as project_name',
            );
        $statusUpper = strtoupper($status);
        
        if ($statusUpper === 'BOOKED') 
        {
            $query->where(function ($q) 
            {
                $q->where('a.status', 'BOOKED')
                ->orWhere(function ($q2) 
                {
                    $q2->where('a.status', 'CONVERTED')
                        ->where('a.conversion_type', 'Booked');
                });
            });
        }
        elseif (in_array(strtolower($status), ['completed', 'cancelled'])) 
        {
            $query->where(function ($q) use ($status) 
            {
                $q->where('a.conversion_type', ucfirst(strtolower($status)))
                ->where('a.status', 'CONVERTED'); 
            });
        }
        elseif ($statusUpper === 'CONVERTED') 
        {
            $query->where('a.status', 'CONVERTED');
        }
        else 
        {
            $query->where('a.status', $statusUpper);
        }
        if ($user_type != 'super_admin') 
        {
            $query->where(function ($q) use ($user_id, $child_ids) 
            {
                $q->where('a.user_id', $user_id);
                
                if (!empty($child_ids)) 
                {
                    $q->orWhereIn('a.user_id', $child_ids);
                }
                
                $q->orWhere(function ($sharedQ) use ($user_id) 
                {
                    $sharedQ->whereNotNull('a.lead_shared_with')
                        ->where('a.lead_shared_with', '!=', '')
                        ->whereRaw("FIND_IN_SET(?, a.lead_shared_with)", [$user_id]);
                });
            });
        }
        $query->orderBy('a.is_pinned', 'desc') 
            ->orderBy('a.id', 'desc'); 
            
        return $query;
    }

    private function getLeadsByConversionType($conversionType)
    {
        $user_type = Session::get('user_type');
        $user_id = Session::get('user_id');
        $child_ids = Session::get('child_ids');
        if (!is_array($child_ids)) 
        {
            $child_ids = $child_ids ? explode(',', $child_ids) : [];
        }

        $query = DB::table('leads as a')
            ->join('users as b', 'b.id', '=', 'a.user_id')
            ->leftJoin('projects as c', 'c.id', '=', 'a.project_id')
            ->select(
                'a.*',
                'b.name as agent',
                'b.role',
                'c.project_name as project_name',
            )
            ->where('a.conversion_type', $conversionType)
            ->where('a.status', 'CONVERTED');

        if ($user_type != 'super_admin') 
        {
            $query->where(function ($q) use ($user_id, $child_ids) 
            {
                $q->where('a.user_id', $user_id);
                if (!empty($child_ids)) 
                {
                    $q->orWhereIn('a.user_id', $child_ids);
                }
            });
        }
        
        $query->orderBy('a.is_pinned', 'desc') 
              ->orderBy('a.id', 'desc');
              
        return $query;
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

    public function getProjectName($id)
    {
        try 
        {
            $project = DB::table('projects')->where('id', $id)->first();
            
            if ($project) 
            {
                return response()->json([
                    'success' => true,
                    'projectName' => $project->name
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Project not found'
            ]);
        } 
        catch (\Exception $e) 
        {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching project'
            ]);
        }
    }

    public function generateShareLink(Request $request)
    {
        try 
        {
            $userId = Session::get('user_id');
            if (!$userId) 
            {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated.'
                ], 401);
            }
            $token = Str::random(40);
            DB::table('shared_leads')->insert([
                'user_id' => $userId,
                'token' => $token,
                'expires_at' => now()->addDays(7),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $link = route('lead.shared-form', ['token' => $token]);

            return response()->json([
                'success' => true,
                'link' => $link
            ]);
        } 
        catch (\Exception $e) 
        {
            return response()->json([
                'success' => false,
                'message' => 'Error generating share link: ' . $e->getMessage()
            ], 500);
        }
    }

    public function showSharedLeadForm($token)
    {
        $logo = DB::table('settings')->where('id', 1)->value('logo');
        $sharedLead = DB::table('shared_leads')
            ->where('token', $token)
            ->where(function ($query) 
            {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>=', now());
            })
            ->first();

        if (!$sharedLead) 
        {
            abort(404, 'Invalid or expired share link.');
        }

        $categorys = DB::table('inv_catg')->get();
        $sources = DB::table('sources')->get();
        $campaigns = DB::table('campaigns')->get();
        $projects = DB::table('projects')->get();
        $categoryList = DB::table('category')->get();
        $states = DB::table('state_district')->select('state')->distinct()->orderBy('state', 'asc')->get();

        $software_type = session::get('software_type');
        if($software_type == 'lead_management')
        {
            $isLeadManagement = true;
        }
        else
        {
            $isLeadManagement = false;
        }

        return view('lead.shared-form', compact('categorys', 'sources', 'campaigns', 'projects', 'states', 'token', 'logo', 'isLeadManagement', 'categoryList'));
    }

    public function submitSharedLeadForm(Request $request, $token)
    {
        $sharedLead = DB::table('shared_leads')
            ->where('token', $token)
            ->where(function ($query) 
            {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>=', now());
            })
            ->first();

        if (!$sharedLead) 
        {
            return redirect()->back()->with('error', 'Invalid or expired share link.');
        }

        $validator = Validator::make($request->all(), [
            'type' => 'required|in:Residential,Commercial',
            'category' => 'nullable',
            'sub_category' => 'nullable',
            'projects' => 'required',
            'field1' => 'nullable',
            'field2' => 'nullable',
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|regex:/^[6-9]\d{9}$/',
            'whatsapp' => 'nullable|regex:/^[6-9]\d{9}$/',
            'budget' => 'nullable|string',
        ]);

        if ($validator->fails()) 
        {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try 
        {
            $exists = DB::table('leads')->where('phone', $request->phone)->exists();
            if ($exists) 
            {
                return redirect()->back()->with('error', 'A lead with this phone number already exists.')->withInput();
            }
            $user = DB::table('users')->where('id', $sharedLead->user_id)->first();
            $status = 'NEW LEAD';
            if ($user && in_array($user->role, ['super_admin', 'divisional_head'])) 
            {
                $status = 'allocated_lead';
            }
            DB::table('leads')->insert([
                'user_id' => $sharedLead->user_id,
                'type' => $request->type,
                'catg_id' => $request->category,
                'sub_catg_id' => $request->sub_category,
                'source' => 'shared form',
                'project_id' => $request->projects,
                'field1' => $request->field1,
                'field2' => $request->field2,
                'field3' => $request->field3,
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'whatsapp_no' => $request->whatsapp,
                'budget' => $request->budget,
                'status' => $status,
                'lead_date' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return redirect()->back()->with('success', 'Lead submitted successfully!');
        } 
        catch (\Exception $e) 
        {
            return redirect()->back()->with('error', 'Error submitting lead: ' . $e->getMessage())->withInput();
        }
    }

    public function submitInquiry(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|numeric|digits:10',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:255',
            'field1' => 'nullable|string|max:100',
            'field2' => 'nullable|string|max:100',
            'budget' => 'nullable|string|max:50',
            'inquiry_question' => 'required|integer|exists:inquiry_questions,id',
            'description' => 'required|string|max:1000',
        ]);
        $duplicate = DB::table('leads')->where('phone', $request->phone)->first();

        if ($duplicate) 
        {
            Flasher::addError('A lead with this phone number already exists.');
            return redirect()->back()->withInput();
        }

        try 
        {
            $now = now();
            $leadId = DB::table('leads')->insertGetId([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'field1' => $request->field1,             
                'field2' => $request->field2,             
                'field3' => $request->address,        
                'budget' => $request->budget,
                'last_comment' => $request->description,
                'inquiry_question_id' => $request->inquiry_question,
                'source' => 'website',
                'user_id' => 1,
                'status' => 'NEW LEAD',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            Flasher::addSuccess('Inquiry submitted successfully!');
            return redirect()->back();
        } 
        catch (\Exception $e) 
        {
            Flasher::addError('Failed to submit inquiry: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    protected function applyLeadFilters($query, Request $request)
    {
        if ($request->filled('search')) 
        {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) 
            {
                $q->where('a.name', 'LIKE', '%' . $searchTerm . '%')
                ->orWhere('a.email', 'LIKE', '%' . $searchTerm . '%')
                ->orWhere('a.phone', 'LIKE', '%' . $searchTerm . '%');
                if (is_numeric($searchTerm)) 
                {
                    $q->orWhere('a.id', $searchTerm);
                }
            });
        }
        if ($request->filled('source')) 
        {
            $query->where('a.source', $request->source);
        }

        if($request->filled('user'))
        {
            $query->where('a.user_id', $request->user);
        }

        if ($request->filled('status')) 
        {
            $query->where('a.status', $request->status);
        }

        if ($request->filled('classification')) 
        {
            $query->where('a.classification', $request->classification);
        }

        if ($request->filled('agent')) 
        {
            $query->where('a.user_id', $request->agent);
        }

        if ($request->filled('project')) 
        {
            $query->where('a.project_id', $request->project);
        }
        
        if ($request->filled('date_from')) 
        {
            $query->whereDate('a.lead_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) 
        {
            $query->whereDate('a.lead_date', '<=', $request->date_to);
        }

        if ($request->filled('lead_days')) 
        {
            $days = (int) $request->lead_days;
            $query->whereDate(
                'a.lead_date',
                '<=',
                Carbon::now()->subDays($days)->startOfDay()
            );
        }

        if ($request->filled('shared_filter')) 
        {
            $current_user_id = Session::get('user_id');
            $sharedFilter = $request->shared_filter;
            
            if ($sharedFilter == 'shared_by_me') 
            {
                $query->where('a.user_id', $current_user_id)
                    ->whereNotNull('a.lead_shared_with')
                    ->where('a.lead_shared_with', '!=', '');
            } 
            elseif ($sharedFilter == 'shared_with_me') 
            {
                $query->where(function($q) use ($current_user_id) 
                {
                    $q->whereNotNull('a.lead_shared_with')
                    ->where(function($q2) use ($current_user_id) 
                    {
                        $q2->where('a.lead_shared_with', 'LIKE', '%' . $current_user_id . '%')
                            ->orWhereRaw("FIND_IN_SET(?, a.lead_shared_with)", [$current_user_id]);
                    });
                });
            }
            elseif ($sharedFilter == 'not_shared') 
            {
                $query->where(function($q) 
                {
                    $q->whereNull('a.lead_shared_with')
                    ->orWhere('a.lead_shared_with', '');
                });
            }
        }

        return $query;
    }

    public function filterLeads(Request $request)
    {
        $length = $request->query('length', 10);
        $classification = $request->query('classification');
        $source = $request->query('source');
        $campaign = $request->query('campaign');

        $lead_name = 'filtered'; 
        $users = DB::table('users')->select('id', 'name')->get();

        $query = DB::table('leads as a')
            ->join('users as b', 'b.id', '=', 'a.user_id')
            ->leftJoin('projects as c', 'c.id', '=', 'a.project_id')
            ->select(
                'a.*',
                'b.name as agent',
                'b.role',
                'c.project_name as project_name'
            )
            ->orderBy('a.is_pinned', 'desc')
            ->orderBy('a.id', 'desc');

        if ($classification) 
        {
            $query->where('a.classification', $classification);
        }
        if ($source) 
        {
            $query->where('a.source', $source);
        }
        if ($campaign) 
        { 
            $query->where('a.campaign', $campaign);
        }

        $user_type = Session::get('user_type');
        $user_id = Session::get('user_id');
        $child_ids = Session::get('child_ids', []);
        if (!is_array($child_ids)) 
        {
            $child_ids = explode(',', $child_ids);
        }
        if ($user_type != 'super_admin') 
        {
            $query->where(function ($q) use ($user_id, $child_ids) 
            {
                $q->where('a.user_id', $user_id);
                if (!empty($child_ids)) 
                {
                    $q->orWhereIn('a.user_id', $child_ids);
                }
            });
        }

        $leads = $query->paginate($length);

        $projects = DB::table('projects')->get();
        $cities = DB::table('state_district')->orderBy('District', 'asc')->get();

        $selected_classification = $classification;
        $selected_source = $source;
        $selected_campaign = $campaign;

        return view('partial.filter-leads', compact( 
            'leads', 'lead_name', 'projects', 'cities', 'length',
            'selected_classification', 'selected_source', 'selected_campaign', 'users'
        ));
    }

    public function duplicateLead(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lead_id' => 'required|exists:leads,id',
            'new_status' => 'required|string|max:255',
            'notes' => 'nullable|string|max:1000',
            'user' => 'required',
            'remind_date' => 'nullable|date',
            'remind_time' => 'nullable'
        ]);

        $statusRequireReminder = ['CALL SCHEDULED', 'VISIT SCHEDULED', 'INTERESTED', 'WHATSAPP'];

        if (in_array(strtoupper($request->new_status), $statusRequireReminder)) 
        {
            $validator->addRules([
                'remind_date' => 'required|date',
                'remind_time' => 'required'
            ]);
        }

        if ($validator->fails()) 
        {
            foreach ($validator->errors()->all() as $error) 
            {
                Flasher::addError($error);
            }
            return redirect()->back();
        }

        $validator->validate();

        DB::beginTransaction();

        try 
        {
            $originalLead = DB::table('leads')->where('id', $request->lead_id)->first();
            if (!$originalLead) 
            {
                throw new \Exception('Lead not found');
            }

            $duplicateData = [
                'name' => $originalLead->name,
                'email' => $originalLead->email,
                'phone' => $originalLead->phone,
                'whatsapp_no' => $originalLead->whatsapp_no,
                'source' => $originalLead->source,
                'campaign' => $originalLead->campaign,
                'classification' => $originalLead->classification,
                'field1' => $originalLead->field1,
                'field2' => $originalLead->field2,
                'field3' => $originalLead->field3,
                'field4' => $originalLead->field4,
                'status' => $request->new_status,
                'unallocated_lead' => 0,
                'is_allocated' => $originalLead->is_allocated,
                'lead_date' => now(),
                'last_comment' => $request->notes ? "Duplicated from lead #{$originalLead->id}. Notes: {$request->notes}" : "Duplicated from lead #{$originalLead->id}",
                'user_id' => $request->user,
                'updated_date' => now(),
                'allocated_date' => $originalLead->allocated_date,
                'is_interested_allocated' => $originalLead->is_interested_allocated,
                'projects' => $originalLead->projects,
                'app_city' => $originalLead->app_city,
                'app_contact' => $originalLead->app_contact,
                'app_doa' => $originalLead->app_doa,
                'app_dob' => $originalLead->app_dob,
                'app_name' => $originalLead->app_name,
                'budget' => $originalLead->budget,
                'catg_id' => $originalLead->catg_id,
                'conversion_type' => $originalLead->conversion_type,
                'final_price' => $originalLead->final_price,
                'project_id' => $originalLead->project_id,
                'size' => $originalLead->size,
                'sub_catg_id' => $originalLead->sub_catg_id,
                'type' => $originalLead->type,
                'lead_shared_with' => $originalLead->lead_shared_with,
                'checklist_status' => $originalLead->checklist_status,
                'inquiry_question_id' => $originalLead->inquiry_question_id,
                'visited_on' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $newLeadId = DB::table('leads')->insertGetId($duplicateData);
            $originalComments = DB::table('lead_comments')
                ->where('lead_id', $originalLead->id)
                ->get();

            foreach ($originalComments as $comment) 
            {
                DB::table('lead_comments')->insert([
                    'remind_date' => $comment->remind_date,
                    'remind_time' => $comment->remind_time,
                    'lead_id' => $newLeadId,
                    'user_id' => $comment->user_id,
                    'comment' => $comment->comment,
                    'status' => $comment->status,
                    'created_date' => $comment->created_date,
                ]);
            }

            DB::table('lead_comments')->insert([
                'lead_id' => $newLeadId,
                'user_id' => Session::get('user_id'),
                'comment' => "Lead duplicated from #{$originalLead->id}" . ($request->notes ? ". Notes: {$request->notes}" : ""),
                'status' => $request->new_status,
                'remind_date' => $request->remind_date,
                'remind_time' => $request->remind_time,
                'created_date' => now(),
            ]);

            DB::commit();
            Flasher::addSuccess("Lead duplicated successfully! New Lead ID: #{$newLeadId}");
            return redirect()->back();

        } 
        catch (\Exception $e) 
        {
            DB::rollBack();
            Flasher::addError('Failed to duplicate lead: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function shareLead(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lead_id' => 'required|exists:leads,id',
            'users' => 'required|array',
            'users.*' => 'exists:users,id',
            'message' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) 
        {
            foreach ($validator->errors()->all() as $error) 
            {
                Flasher::addError($error);
            }
            return redirect()->back();
        }

        DB::beginTransaction();
        try 
        {
            $lead = DB::table('leads')->where('id', $request->lead_id)->first();
            
            if (!$lead) 
            {
                throw new \Exception('Lead not found');
            }

            $currentUserId = Session::get('user_id');
            $currentUser = DB::table('users')->where('id', $currentUserId)->first();
            $sharedUserIds = $request->users;

            $currentSharedUsers = [];
            if (!empty($lead->lead_shared_with)) 
            {
                $currentSharedUsers = explode(',', $lead->lead_shared_with);
                $currentSharedUsers = array_filter(array_map('trim', $currentSharedUsers));
            }
            
            $newShares = [];
            $alreadySharedCount = 0;
            
            foreach ($sharedUserIds as $userId) 
            {
                $userIdStr = (string)$userId;
                
                if (in_array($userIdStr, $currentSharedUsers)) 
                {
                    $alreadySharedCount++;
                    continue;
                }
                
                $sharedWithUser = DB::table('users')->where('id', $userId)->first();
                
                if ($sharedWithUser) 
                {
                    $newShares[] = $userIdStr;
                    
                    $commentMessage = "Lead shared with {$sharedWithUser->name}";
                    if ($request->message) 
                    {
                        $commentMessage .= ". Message: {$request->message}";
                    }
                    
                    DB::table('lead_comments')->insert([
                        'lead_id' => $request->lead_id,
                        'user_id' => $currentUserId,
                        'comment' => $commentMessage,
                        'status' => $lead->status,
                        'created_date' => now(),
                    ]);


                    $notificationTitle = "📋 Lead Shared: {$lead->name}";
                    $notificationMessage = "👤 **Shared by:** {$currentUser->name}\n";
                    $notificationMessage .= "Lead Phone:** {$lead->phone}\n";
                    $notificationMessage .= "📧 **Lead Email:** " . ($lead->email ?? 'N/A') . "\n";
                    $notificationMessage .= "🏢 **Project:** " . ($lead->project_name ?? 'N/A') . "\n";
                    $notificationMessage .= "💰 **Budget:** " . ($lead->budget ?? 'N/A');
                    
                    if ($request->message) 
                    {
                        $notificationMessage .= "\n💬 **Note:** {$request->message}";
                    }

                    DB::table('user_notification')->insert([
                        'userId' => $userId,
                        'notification_title' => $notificationTitle,
                        'message' => $notificationMessage,
                        'ack' => 0,
                        'CreatedDate' => now()
                    ]);

                    DB::table('user_notification')->insert([
                        'userId' => $currentUserId,
                        'notification_title' => "Lead Shared Successfully",
                        'message' => "You shared lead '{$lead->name}' with {$sharedWithUser->name}",
                        'ack' => 0,
                        'CreatedDate' => now()
                    ]);
                }
            }
            
            $allSharedUsers = array_merge($currentSharedUsers, $newShares);
            $allSharedUsers = array_unique(array_filter($allSharedUsers));
            
            DB::table('leads')
                ->where('id', $request->lead_id)
                ->update([
                    'lead_shared_with' => implode(',', $allSharedUsers),
                    'updated_at' => now()
                ]);

            DB::commit();

            $newSharesCount = count($newShares);
            
            if ($newSharesCount > 0) 
            {
                Flasher::addSuccess("Lead shared with {$newSharesCount} user(s)! Notifications sent.");
            } 
            else 
            {
                Flasher::addInfo("Lead was already shared with all selected users.");
            }
            
            if ($alreadySharedCount > 0) 
            {
                Flasher::addInfo("{$alreadySharedCount} user(s) were already shared with this lead.");
            }
            return redirect()->back();

        } 
        catch (\Exception $e) 
        {
            DB::rollBack();
            Flasher::addError('Failed to share lead: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function togglePin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lead_id' => 'required|exists:leads,id',
            'is_pinned' => 'required|boolean'
        ]);

        if ($validator->fails()) 
        {
            return response()->json([
                'status' => 400,
                'message' => 'Invalid request',
                'data' => ''
            ]);
        }

        try 
        {
            DB::table('leads')
                ->where('id', $request->lead_id)
                ->update([
                    'is_pinned' => $request->is_pinned,
                    'updated_at' => now()
                ]);

            return response()->json([
                'status' => 200,
                'message' => $request->is_pinned ? 'Lead pinned successfully' : 'Lead unpinned successfully',
                'data' => $request->is_pinned
            ]);

        } 
        catch (\Exception $e)
        {
            return response()->json([
                'status' => 500,
                'message' => 'Failed to update pin status: ' . $e->getMessage(),
                'data' => '',
            ]);
        }
    }

    private function renderLeadDataView($leads, $lead_name, $length, Request $request, $requestedLength = null)
    {
        $projects = DB::table('projects')->select('id', 'project_name')->distinct()->get();
        $cities = DB::table('state_district')->orderBy('District', 'asc')->get();
        $sources = DB::table('sources')->get();
        $statuses = DB::table('status')->pluck('name', 'id');
        $classifications = DB::table('leads')->distinct()->pluck('classification');
        $agents = DB::table('users')->where('role', 'agent')->pluck('name', 'id');
        $users = DB::table('users')->get();
        
        $hasFilters = $request->anyFilled([
            'source', 'status','user', 'classification', 'agent','lead_days','shared_filter', 
            'project', 'date_from', 'date_to', 'users'
        ]);

        return view('lead.lead-data', compact(
            'leads', 'lead_name', 'projects', 'cities', 'length',
            'sources', 'statuses', 'classifications', 'agents', 
            'hasFilters', 'users', 'requestedLength'
        ));
    }

    public function addProjects(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lead_id' => 'required|exists:leads,id',
            'projects' => 'required|array|min:1',
            'projects.*' => 'exists:projects,id',
            'notes' => 'nullable|string|max:1000'
        ], [
            'lead_id.required' => 'Lead ID is required',
            'lead_id.exists' => 'The selected lead does not exist',
            'projects.required' => 'Please select at least one project',
            'projects.min' => 'Please select at least one project',
            'projects.*.exists' => 'One or more selected projects are invalid'
        ]);

        if ($validator->fails()) 
        {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try 
        {
            $lead = DB::table('leads')->where('id', $request->lead_id)->first();
            if (!$lead) 
            {
                return response()->json([
                    'success' => false,
                    'message' => 'Lead not found'
                ], 404);
            }
            $currentProjects = $lead->project_id ? explode(',', $lead->project_id) : [];
            $newProjects = $request->projects;
            $allProjects = array_unique(array_merge($currentProjects, $newProjects));
            DB::table('leads')
                ->where('id', $request->lead_id)
                ->update([
                    'project_id' => implode(',', $allProjects),
                    'updated_at' => now()
                ]);
            $newProjectNames = [];
            foreach ($newProjects as $projectId) 
            {
                $project = DB::table('projects')->where('id', $projectId)->first();
                if ($project) 
                {
                    $newProjectNames[] = $project->project_name;
                }
            }

            $comment = "Added projects: " . implode(', ', $newProjectNames);
            if ($request->notes) 
            {
                $comment .= ". Notes: " . $request->notes;
            }
            // dd($comment);
            DB::table('lead_comments')->insert([
                'lead_id' => $request->lead_id,
                'user_id' => Session::get('user_id'),
                'comment' => $comment,
                'status' => $lead->status,
                'created_date' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => count($newProjects) . ' project(s) added successfully!',
                'added_count' => count($newProjects)
            ]);

        } 
        catch (\Exception $e) 
        {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to add projects: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getProjectNames(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'project_ids' => 'required|array'
        ]);

        if ($validator->fails()) 
        {
            return response()->json([
                'success' => false,
                'message' => 'Invalid project IDs'
            ]);
        }

        try 
        {
            $projectNames = [];
            foreach ($request->project_ids as $projectId) 
            {
                $project = DB::table('projects')->where('id', trim($projectId))->first();
                if ($project) 
                {
                    $projectNames[] = $project->project_name;
                }
            }

            return response()->json([
                'success' => true,
                'projectNames' => $projectNames
            ]);

        }
        catch (\Exception $e) 
        {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching project names'
            ]);
        }
    }

    public function getLeadProjects(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lead_id' => 'required|exists:leads,id'
        ]);

        if ($validator->fails()) 
        {
            return response()->json([
                'success' => false,
                'message' => 'Invalid lead ID'
            ]);
        }

        try 
        {
            $lead = DB::table('leads')->where('id', $request->lead_id)->first();
            
            if (!$lead->project_id) 
            {
                return response()->json([
                    'success' => true,
                    'projects' => []
                ]);
            }

            $projectIds = explode(',', $lead->project_id);
            $projectIds = array_filter(array_map('trim', $projectIds));
            
            $projects = DB::table('projects')
                ->whereIn('id', $projectIds)
                ->select('id', 'project_name')
                ->get();

            return response()->json([
                'success' => true,
                'projects' => $projects
            ]);

        } 
        catch (\Exception $e) 
        {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching lead projects: ' . $e->getMessage()
            ]);
        }
    }

    public function getLeadProjectVisits(Request $request)
    {
        try 
        {
            $leadId = $request->lead_id;
            if (!$leadId) 
            {
                return response()->json([
                    'success' => false,
                    'message' => 'Lead ID is required'
                ], 400);
            }

            $leadExists = DB::table('leads')->where('id', $leadId)->exists();
            
            if (!$leadExists) 
            {
                return response()->json([
                    'success' => false,
                    'message' => 'Lead not found'
                ], 404);
            }
            
            $projectVisits = DB::table('lead_projects')
                ->leftJoin('projects', 'lead_projects.project_id', '=', 'projects.id')
                ->where('lead_projects.lead_id', $leadId)
                ->select(
                    'lead_projects.*',
                    'projects.project_name'
                )
                ->orderBy('lead_projects.created_at', 'desc')
                ->get();


            return response()->json([
                'success' => true,
                'projectVisits' => $projectVisits,
                'count' => $projectVisits->count()
            ]);

        } 
        catch (\Exception $e) 
        {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function delete(Request $request)
    {
        try 
        {
            $request->validate([
                'lead_id' => 'required|exists:leads,id',
                'password' => 'required|string',
            ]);
            $leadId = $request->lead_id;
            $cookieName = "lead_attempt_{$leadId}";
            $attempts = (int) $request->cookie($cookieName, 0);

            if ($attempts >= 3) 
            {
                return response()->json([
                    'status' => 403,
                    'message' => 'You have reached the maximum number of attempts. You cannot delete this lead for 1 day.',
                    'data' => '',
                ]);
            }

            $admin = DB::table('users')->where('role', 'super_admin')->first();
            if (!$admin || $request->password !== $admin->password) 
            {
                $attempts++;
                $cookie = cookie($cookieName, $attempts, 1440);

                return response()->json([
                    'status' => 404,
                    'message' => "Incorrect admin password. Attempt $attempts of 3.",
                    'data' => '',
                ])->withCookie($cookie);
            }

            $deleted = DB::table('leads')->where('id', $leadId)->delete();
            $cookie = cookie($cookieName, 0, 0); 
            if ($deleted) 
            {
                return response()->json([
                    'status' => 200,
                    'message' => 'Lead deleted successfully!',
                    'lead_id' => $leadId
                ])->withCookie($cookie);
            } 
            else 
            {
                return response()->json([
                    'status' => 404,
                    'message' => 'Failed to delete lead.',
                    'data' => '',
                ]);
            }

        } 
        catch (\Exception $error) 
        {
            return response()->json([
                'status' => 500,
                'message' => $error->getMessage(),
                'data' => '',
            ]);
        }
    }

    public function getMatchingProperties($id, Request $request)
    {
        // echo '<pre>'; print_r($id); exit;
        try 
        {
            $lead = DB::table('leads')->where('id', $id)->first();
            if (!$lead) 
            {
                return response()->json([
                    'status' => 404,
                    'message' => 'Lead not found',
                    'data' => []
                ]);
            }

            $userId = $request->user_id ?? Session::get('user_id');
            $category = $lead->catg_id ? DB::table('inv_catg')->where('id', $lead->catg_id)->first() : null;
            $subCategory = $lead->sub_catg_id ? DB::table('inv_subcatg')->where('id', $lead->sub_catg_id)->first() : null;
            $budgetRange = $this->parseBudgetRange($lead->budget);
            $query = DB::table('properties')
                ->where('property_status', '!=', 'Sold')
                ->where('property_status', '!=', 'Hold');
            if (Schema::hasColumn('properties', 'user_id')) 
            {
                // echo '<pre>'; print_r($id); exit;
                $query->where(function ($q) use ($userId, $id) 
                {
                    $q->where('user_id', $userId)
                    ->orWhereIn('id', function ($subQuery) use ($id)
                    {
                        $subQuery->select('property_id')
                                ->from('lead_assignments')
                                ->where('lead_id', $id)
                                ->where('status', 'active');
                    });
                });
            }
            $properties = $query->get();

            $matchedProperties = [];
            foreach ($properties as $property) 
            {
                $score = $this->calculateMatchScore($lead, $property, $category, $subCategory);
                if ($score > 0) 
                {
                    $property->match_score = $score;
                    $property->match_reasons = $this->getMatchReasons($lead, $property, $category, $subCategory);
                    $matchedProperties[] = $property;
                }
            }
            usort($matchedProperties, function ($a, $b) 
            {
                return $b->match_score <=> $a->match_score;
            });

            if (empty($matchedProperties)) 
            {
                return response()->json([
                    'status' => 404,
                    'data' => [],
                    'message' => 'No matching properties found for your account'
                ]);
            }
            // echo '<pre>'; print_r($matchedProperties); exit;
            return response()->json([
                'status' => 200,
                'data' => $matchedProperties,
                'message' => 'Properties retrieved successfully'
            ]);

        } 
        catch (\Exception $e) 
        {
            return response()->json([
                'status' => 500,
                'message' => 'Error fetching matching properties: ' . $e->getMessage(),
                'data' => []
            ]);
        }
    }

    private function parseBudgetRange($budgetString)
    {
        if (empty($budgetString) || $budgetString === 'NULL') 
        {
            return null;
        }
        $budgetString = str_replace(['₹', ' ', ','], '', $budgetString);
        if (strpos($budgetString, '-') !== false) 
        {
            list($min, $max) = explode('-', $budgetString);
            $minValue = $this->convertToNumber($min);
            $maxValue = $this->convertToNumber($max);
            $rangeWidth = ($maxValue - $minValue) * 0.2;
            return [
                'min' => max(0, $minValue - $rangeWidth),
                'max' => $maxValue + $rangeWidth,
                'original_min' => $minValue,
                'original_max' => $maxValue
            ];
        }
        $value = $this->convertToNumber($budgetString);
        $buffer = $value * 0.2; 
        return [
            'min' => max(0, $value - $buffer),
            'max' => $value + $buffer,
            'original_min' => $value,
            'original_max' => $value
        ];
    }

    private function convertToNumber($value)
    {
        $value = trim($value);
        $number = (float) filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        
        if (stripos($value, 'Cr') !== false || stripos($value, 'cr') !== false) 
        {
            return $number * 100;
        } 
        elseif (stripos($value, 'L') !== false || stripos($value, 'l') !== false) 
        {
            return $number;
        }
        
        return $number;
    }

    private function calculateMatchScore($lead, $property, $category = null, $subCategory = null)
    {
        $score = 0;
        $totalWeight = 0;
        $weights = [
            'type' => 25,
            'category' => 20,
            'sub_category' => 15,
            'location' => 25,
            'budget' => 15
        ];

        if (!empty($lead->type) && $lead->type !== 'NULL' && !empty($property->property_type)) 
        {
            $totalWeight += $weights['type'];
            if (strcasecmp(trim($lead->type), trim($property->property_type)) == 0) 
            {
                $score += $weights['type'];
            }
            elseif (stripos($property->property_type, $lead->type) !== false || stripos($lead->type, $property->property_type) !== false) 
            {
                $score += $weights['type'] * 0.5;
            }
        }

        if ($category && !empty($category->name) && !empty($property->property_category)) 
        {
            $totalWeight += $weights['category'];
            if (strcasecmp(trim($category->name), trim($property->property_category)) == 0) 
            {
                $score += $weights['category'];
            }
            elseif (stripos($property->property_category, $category->name) !== false || stripos($category->name, $property->property_category) !== false) 
            {
                $score += $weights['category'] * 0.5;
            }
        }

        if ($subCategory && !empty($subCategory->name) && !empty($property->property_sub_category)) 
        {
            $totalWeight += $weights['sub_category'];
            if (strcasecmp(trim($subCategory->name), trim($property->property_sub_category)) == 0) 
            {
                $score += $weights['sub_category'];
            }
            elseif (stripos($property->property_sub_category, $subCategory->name) !== false || 
                    stripos($subCategory->name, $property->property_sub_category) !== false) 
            {
                $score += $weights['sub_category'] * 0.5;
            }
        }

        if (!empty($lead->property_state) || !empty($lead->property_city) || !empty($lead->property_location)) 
        {
            $totalWeight += $weights['location'];
            $locationMatch = false;
            if (!empty($lead->property_state) && $lead->property_state !== 'NULL' && !empty($property->state)) 
            {
                if (stripos($property->state, $lead->property_state) !== false || stripos($lead->property_state, $property->state) !== false) 
                {
                    $locationMatch = true;
                }
            }

            if (!$locationMatch && !empty($lead->property_city) && $lead->property_city !== 'NULL' && !empty($property->city)) 
            {
                if (stripos($property->city, $lead->property_city) !== false || stripos($lead->property_city, $property->city) !== false) 
                {
                    $locationMatch = true;
                }
            }

            if (!$locationMatch && !empty($lead->property_location) && $lead->property_location !== 'NULL') 
            {
                $locationTerms = explode(' ', strtolower($lead->property_location));
                foreach ($locationTerms as $term) 
                {
                    if (strlen($term) < 3) continue; 
                    
                    if (!empty($property->state) && stripos($property->state, $term) !== false) 
                    {
                        $locationMatch = true;
                        break;
                    }
                    if (!empty($property->city) && stripos($property->city, $term) !== false) 
                    {
                        $locationMatch = true;
                        break;
                    }
                    if (!empty($property->address) && stripos($property->address, $term) !== false) 
                    {
                        $locationMatch = true;
                        break;
                    }
                }
            }
            
            if ($locationMatch) 
            {
                $score += $weights['location'];
            }
        }

        if (!empty($lead->budget) && $lead->budget !== 'NULL' && !empty($property->budget_price)) 
        {
            $totalWeight += $weights['budget'];
            if ($this->isBudgetMatch($lead->budget, $property->budget_price)) 
            {
                $score += $weights['budget'];
            }
        }
        
        return $totalWeight > 0 ? round(($score / $totalWeight) * 100) : 0;
    }

    private function getMatchReasons($lead, $property, $category = null, $subCategory = null)
    {
        $reasons = [];
        if (!empty($lead->type) && $lead->type !== 'NULL' && !empty($property->property_type)) 
        {
            if (strcasecmp(trim($lead->type), trim($property->property_type)) == 0) 
            {
                $reasons[] = "Property type matches: {$property->property_type}";
            }
            elseif (stripos($property->property_type, $lead->type) !== false) 
            {
                $reasons[] = "Property type matches: {$property->property_type}";
            }
        }
        if ($category && !empty($category->name) && !empty($property->property_category)) 
        {
            if (strcasecmp(trim($category->name), trim($property->property_category)) == 0) 
            {
                $reasons[] = "Category matches: {$property->property_category}";
            }
            elseif (stripos($property->property_category, $category->name) !== false) 
            {
                $reasons[] = "Category matches: {$property->property_category}";
            }
        }
        if ($subCategory && !empty($subCategory->name) && !empty($property->property_sub_category)) 
        {
            if (strcasecmp(trim($subCategory->name), trim($property->property_sub_category)) == 0) 
            {
                $reasons[] = "Sub-category matches: {$property->property_sub_category}";
            }
            elseif (stripos($property->property_sub_category, $subCategory->name) !== false) 
            {
                $reasons[] = "Sub-category matches: {$property->property_sub_category}";
            }
        }
        if (!empty($lead->property_state) && $lead->property_state !== 'NULL' && !empty($property->state)) 
        {
            if (stripos($property->state, $lead->property_state) !== false) 
            {
                $reasons[] = "State matches: {$property->state}";
            }
        }
        
        if (!empty($lead->property_city) && $lead->property_city !== 'NULL' && !empty($property->property_city)) 
        {
            if (stripos($property->city, $lead->property_city) !== false) 
            {
                $reasons[] = "City matches: {$property->property_city}";
            }
        }
        
        if (!empty($lead->property_location) && $lead->property_location !== 'NULL') 
        {
            $locationTerms = explode(' ', strtolower($lead->property_location));
            foreach ($locationTerms as $term) 
            {
                if (strlen($term) < 3) continue;
                
                if (!empty($property->state) && stripos($property->state, $term) !== false) 
                {
                    $reasons[] = "Location matches: {$property->state}";
                    break;
                }
                if (!empty($property->city) && stripos($property->city, $term) !== false) 
                {
                    $reasons[] = "Location matches: {$property->city}";
                    break;
                }
                if (!empty($property->address) && stripos($property->address, $term) !== false) 
                {
                    $reasons[] = "Location matches: {$property->address}";
                    break;
                }
            }
        }
        if (!empty($lead->budget) && $lead->budget !== 'NULL' && !empty($property->budget_price) && 
            $this->isBudgetMatch($lead->budget, $property->budget_price)) 
        {
            $reasons[] = "Budget range matches: {$property->budget_price}";
        }
        
        return $reasons;
    }

    private function isBudgetMatch($leadBudget, $propertyBudget)
    {
        $leadRange = $this->parseBudgetRange($leadBudget);
        $propertyRange = $this->parseBudgetRange($propertyBudget);
        
        if (!$leadRange || !$propertyRange) 
        {
            return false;
        }
        return !($propertyRange['max'] < $leadRange['min'] || $propertyRange['min'] > $leadRange['max']);
    }

    public function sharePropertyOnWhatsApp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'property_id' => 'required|exists:properties,id',
            'lead_id' => 'required|exists:leads,id',
            'phone' => 'required'
        ]);

        if ($validator->fails()) 
        {
            return response()->json([
                'status' => 400,
                'message' => 'Invalid request: ' . implode(', ', $validator->errors()->all()),
                'data' => null
            ]);
        }

        try 
        {
            $property = DB::table('properties')->where('id', $request->property_id)->first();
            $phone = $request->phone;
            if (!$property) 
            {
                return response()->json([
                    'status' => 404,
                    'message' => 'Property not found',
                    'data' => null
                ]);
            }
            
            $lead = DB::table('leads')->where('id', $request->lead_id)->first();
            if (!$lead) 
            {
                return response()->json([
                    'status' => 404,
                    'message' => 'Lead not found',
                    'data' => null
                ]);
            }
            $message = "🏠 *EXCLUSIVE PROPERTY DETAILS* 🏠\n\n";
            $message .= "📍 *PROJECT HIGHLIGHTS*\n";
            $message .= "┌─────────────────────\n";
            $message .= "│ 🏢 *Project:* {$property->property_name}\n";
            $message .= "│ 📋 *Type:* {$property->property_type}\n";
            $message .= "│ 📊 *Category:* {$property->property_category}\n";
            $message .= "│ 📌 *Sub Category:* {$property->property_sub_category}\n";
            $message .= "└─────────────────────\n\n";
            $message .= "📍 *LOCATION*\n";
            $message .= "┌─────────────────────\n";
            $message .= "│ 🏙️ *City:* {$property->city}\n";
            $message .= "│ 🗺️ *State:* {$property->state}\n";
            if (!empty($property->address)) 
            {
                $message .= "│ 📍 *Address:* {$property->address}\n";
            }
            $message .= "└─────────────────────\n\n";
            $message .= "💰 *PRICING & STATUS*\n";
            $message .= "┌─────────────────────\n";
            $message .= "│ 💵 *Budget:* {$property->budget_price}\n";
            $message .= "│ 📈 *Status:* {$property->property_status}\n";
            if (!empty($property->size_sqft)) 
            {
                $message .= "│ 📐 *Size:* {$property->size_sqft} sq.ft\n";
            }
            $message .= "└─────────────────────\n\n";
            if (!empty($property->amenities)) 
            {
                $amenities = is_string($property->amenities) ? json_decode($property->amenities, true) : $property->amenities;
                if (!empty($amenities) && is_array($amenities)) 
                {
                    $message .= "✨ *AMENITIES*\n";
                    $message .= "┌─────────────────────\n";
                    foreach(array_slice($amenities, 0, 5) as $amenity) 
                    {
                        $message .= "│ • {$amenity}\n";
                    }
                    if (count($amenities) > 5) 
                    {
                        $message .= "│ • + " . (count($amenities) - 5) . " more amenities\n";
                    }
                    $message .= "└─────────────────────\n\n";
                }
            }
            if (!empty($lead->phone)) 
            {
                $message .= "📞 *Phone:* {$lead->phone}\n";
            }
            $message .= "━━━━━━━━━━━━━━━━━━━\n\n";
            
            $message .= "🕐 *Schedule a visit today!*\n";
            $message .= "Reply 'YES' to book a site visit.";
            if (strlen($phone) == 10) 
            {
                $phone = '91' . $phone;
            }
            $propertyLink = url("/property/{$property->id}");
            $whatsappUrl = "https://wa.me/{$phone}?text=" . urlencode($message);
            try 
            {
                if (Schema::hasTable('property_shares')) 
                {
                    DB::table('property_shares')->insert([
                        'property_id' => $property->id,
                        'lead_id' => $lead->id,
                        'shared_by' => Session::get('user_id'),
                        'shared_to' => $phone,
                        'shared_at' => now()
                    ]);
                }
            } 
            catch (\Exception $logError) 
            {
            }

            return response()->json([
                'status' => 200,
                'data' => $whatsappUrl,
                'message' => 'Professional WhatsApp link generated successfully'
            ]);

        } 
        catch (\Exception $e) 
        {
            return response()->json([
                'status' => 500,
                'message' => 'Error generating WhatsApp link: ' . $e->getMessage(),
                'data' => null
            ]);
        }
    }

    public function getPropertyDetails($id)
    {
        try 
        {
            $property = DB::table('projects')->where('id', $id)->first();
            if (!$property) 
            {
                return response()->json([
                    'status' => 404,
                    'message' => 'Property not found',
                    'data' => ''
                ]);
            }
            $property->gallery_images = !empty($property->gallery_images) 
                ? json_decode($property->gallery_images) 
                : [];

            return response()->json([
                'status' => 200,
                'data' => $property,
                'message' => 'Image Retreive Successfully'
            ]);

        } 
        catch (\Exception $e) 
        {
            return response()->json([
                'status' => 500,
                'message' => 'Error fetching property details: ' . $e->getMessage(),
                'data' => ''
            ]);
        }
    }

    public function assignProjects(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lead_id' => 'required|exists:leads,id',
            'project_ids' => 'required|array|min:1',
            'project_ids.*' => 'required|exists:properties,id'
        ]);

        if ($validator->fails()) 
        {
            return response()->json([
                'success' => false,
                'message' => 'Invalid request: ' . implode(', ', $validator->errors()->all())
            ], 422);
        }

        DB::beginTransaction();
        try 
        {
            $lead = DB::table('leads')->where('id', $request->lead_id)->first();
            $userId = Session::get('user_id');
            $assignedCount = 0;
            $alreadyAssigned = [];

            foreach ($request->project_ids as $propertyId) 
            {
                $exists = DB::table('lead_assignments')
                    ->where('lead_id', $request->lead_id)
                    ->where('property_id', $propertyId)
                    ->exists();

                if (!$exists) 
                {
                    DB::table('lead_assignments')->insert([
                        'lead_id' => $request->lead_id,
                        'agent_id' => $userId,
                        'property_id' => $propertyId,
                        'assigned_at' => now(),
                        'status' => 'active'
                    ]);
                    $assignedCount++;
                } 
                else 
                {
                    $alreadyAssigned[] = $propertyId;
                }
            }
            if ($assignedCount > 0) 
            {
                $propertyNames = DB::table('properties')
                    ->whereIn('id', $request->project_ids)
                    ->pluck('property_name')
                    ->implode(', ');

                DB::table('lead_comments')->insert([
                    'lead_id' => $request->lead_id,
                    'user_id' => $userId,
                    'comment' => "BA assigned matching properties: {$propertyNames}",
                    'status' => $lead->status,
                    'created_date' => now()
                ]);
            }

            DB::commit();

            $message = $assignedCount > 0 
                ? "✅ {$assignedCount} project(s) assigned successfully." 
                : "Selected projects were already assigned.";

            if (!empty($alreadyAssigned)) 
            {
                $message .= " ⚠️ " . count($alreadyAssigned) . " project(s) were already assigned.";
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'assigned_count' => $assignedCount,
                'already_assigned' => count($alreadyAssigned)
            ]);

        } 
        catch (\Exception $e) 
        {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error assigning projects: ' . $e->getMessage()
            ], 500);
        }
    }
}