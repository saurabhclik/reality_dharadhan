<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Flasher\Laravel\Facade\Flasher;
use League\Csv\Reader;
use App\Services\LeadService;
use Carbon\Carbon;
class StaffManagementController extends Controller
{
    public function index()
    {
        $user_role = session()->get('user_type');
        $user_id = session()->get('user_id');
        $roles = DB::table('role_mst')
        ->where('role_name', '!=', 'super_admin')
        ->get();
        if ($user_role !== 'super_admin' && $user_role !== 'divisional_head') 
        {
            abort(404); 
        }
        
        $teamLeads = DB::table('users')
            ->whereIn('role', function($query) 
            {
                $query->select('role_name')
                    ->from('role_mst')
                    ->where('manager_rights', 1);
            })
            ->where('is_active', 1)
            ->get();

        $query = DB::table('users')
            ->leftJoin('users as team_lead', 'users.tm_id', '=', 'team_lead.id')
            ->leftJoin('designation', 'users.designation_id', '=', 'designation.id')
            ->leftJoin('zones', 'users.zone_id', '=', 'zones.id')
            ->leftJoin('state_district', 'zones.city_id', '=', 'state_district.id')
            ->select(
                'users.*',
                'team_lead.name as team_lead_name',
                'designation.designation as designation_name',
                'zones.zone_name as zone',
                'zones.sub_area as zone_sub_area',
                'state_district.state as state_name',
                'state_district.District as district_name'
            );

        if ($user_role == 'super_admin') 
        {
            $query->where('users.role', '!=', 'super_admin');
        } 
        else 
        {
            $query->where('users.tm_id', $user_id);
        }

        if (request()->has('status') && request('status') !== '')  
        {
            $query->where('users.is_active', request('status'));
        }

        if (request()->has('role') && request('role') !== '') 
        {
            $query->where('users.role', request('role'));
        }

        if (request()->has('team_lead') && request('team_lead') !== '') 
        {
            $query->where('users.tm_id', request('team_lead'));
        }

        if (request()->has('name') && request('name') !== '') 
        {
            $query->where('users.name', 'like', '%' . request('name') . '%');
        }

        if (request()->has('date') && request('date') !== '') 
        {
            $date = Carbon::createFromFormat('d-m-Y', request('date'))->format('Y-m-d');
            $query->whereDate('users.created_date', $date);
        }

        $sort = request('sort', 'id');
        $direction = request('direction', 'desc');
        
        $validSortColumns = ['name', 'email', 'role', 'team_lead_name', 'created_date'];
        
        if (in_array($sort, $validSortColumns)) 
        {
            $query->orderBy($sort, $direction);
        } 
        else 
        {
            $query->orderBy('users.id', 'desc');
        }   
        $agentLinks = DB::table('agent_universal_links')
            ->where('is_active', 1)
            ->get()
            ->keyBy(function($link) 
            {
                $meta = json_decode($link->metadata);
                return $meta->agent_user_id ?? null;
            });
        $users = $query->paginate(10)->appends(request()->query());
        return view('staff-management.view', compact('users', 'roles', 'teamLeads', 'agentLinks'));
    }

    public function create()
    {
        $user_role = session()->get('user_type');
        
        if ($user_role !== 'super_admin' && $user_role !== 'divisional_head' ) 
        {
            abort(404); 
        }
        
        if($user_role === 'super_admin') 
        {
            $roles = DB::select("SELECT * FROM `role_mst` WHERE `role_name` != 'super_admin'");
        } 
        else 
        {
            $roles = DB::select("SELECT * FROM `role_mst` WHERE `role_name` != 'super_admin' AND `role_name` != 'postsale' AND `manager_rights` = 0");
        }

        $reporting_manager = DB::select("SELECT a.* FROM `users` a JOIN `role_mst` b ON a.role=b.role_name WHERE b.manager_rights=1 AND is_active=1;");

        $designation = DB::select("SELECT * FROM `designation`");
        $userLimit = DB::table('software_details')->select('user_limit')->first();
        $users = DB::table('users')->paginate(10);
        $zones = DB::table('zones')
            ->leftJoin('state_district', 'zones.city_id', '=', 'state_district.id')
            ->where('zones.status', '=', '1')
            ->select(
                'zones.*',
                'state_district.state as state_name',
                'state_district.District as district_name'
            )
            ->get();
        return view('staff-management.index', [
            'user' => null,
            'roles' => $roles,
            'reporting_manager' => $reporting_manager,
            'designation' => $designation,
            'data' => $roles,
            'userLimit' => $userLimit, 
            'users' => $users,
            'zones' => $zones,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'mobile' => 'required|digits:10',
            'password' => 'required|string|min:5',
            'role' => 'required|string',
            'designation' => 'nullable',
            'reporting_manager' => 'nullable',
            'zone_id' => 'required'
        ]);

        if ($validator->fails()) 
        {
            foreach ($validator->errors()->all() as $error) 
            {
                Flasher::addError($error);
            }
            return redirect()->route('users.create')->withInput();
        }

        try 
        {
            $software = DB::table('software_details')
                ->where('status', 'active')
                ->first();

            if (!$software || empty($software->user_limit)) 
            {
                Flasher::addError('User limit not configured. Please contact admin.');
                return redirect()->route('users.create')->withInput();
            }

            if ($software->user_limit !== 'all') 
            {
                $limit = (int) $software->user_limit;
                $totalUsers = DB::table('users')->count();

                if ($totalUsers >= $limit) 
                {
                    Flasher::addError(
                        "User limit reached ({$limit}). Please contact Clikzop innovation."
                    );
                    return redirect()->route('users.create')->withInput();
                }
            }
            DB::table('users')->insert([
                'name' => $request->name,
                'email' => $request->email,
                'mobile' => $request->mobile,
                'password' => $request->password,
                'role' => $request->role,
                'designation_id' => $request->designation ?? null,
                'tm_id' => $request->reporting_manager ?? null,
                'zone_id' => $request->zone_id,
                'created_date' => now(),
                'updated_date' => now(),
            ]);

            Flasher::addSuccess('User created successfully.');
            return redirect()->route('users.index');

        } 
        catch (Exception $e) 
        {
            Flasher::addError('Something went wrong: ' . $e->getMessage());
            return redirect()->route('users.create')->withInput();
        }
    }

    public function show($id)
    {
        if (!$id) 
        {
            Flasher::addError('Invalid Action!');
            return redirect()->route('users.index');
        }

        $user_role = session()->get('user_type');
        if ($user_role !== 'super_admin' && $user_role !== 'divisional_head' ) 
        {
            abort(404); 
        }
        try 
        {
            $user = DB::table('users')->where('id', $id)->first();

            if (!$user) 
            {
                Flasher::addError('User Not Found!');
                return redirect()->route('users.index');
            }

            if ($user_role === 'super_admin') 
            {
                $roles = DB::table('role_mst')
                    ->where('role_name', '!=', 'super_admin')
                    ->get();
            } 
            else 
            {
                $roles = DB::table('role_mst')
                    ->where('role_name', '!=', 'super_admin')
                    ->where('role_name', '!=', 'postsale')
                    ->where('manager_rights', 0)
                    ->get();
            }

            $reporting_manager = DB::table('users')
                ->whereNotNull('role')
                ->where('id', '!=', $id)
                ->get();

            $designation = DB::table('designation')->get();
            $userLimit = DB::table('software_details')->select('user_limit')->first();
            $users = DB::table('users')->paginate(10);
            $zones = DB::table('zones')
                ->leftJoin('state_district', 'zones.city_id', '=', 'state_district.id')
                ->where('zones.status', '=', '1')
                ->select(
                    'zones.*',
                    'state_district.state as state_name',
                    'state_district.District as district_name'
                )
                ->get();
            return view('staff-management.index', compact('user', 'roles', 'reporting_manager', 'designation', 'userLimit', 'users', 'zones'));
        } 
        catch (Exception $error) 
        {
            Flasher::addError($error->getMessage());
            return redirect()->route('users.index');
        }
    }

    public function edit($id)
    {
        if (!$id) 
        {
            Flasher::addError('Invalid Action!');
            return redirect()->route('users.index');
        }

        $user_role = session()->get('user_type');

        try 
        {
            $user = DB::table('users')->where('id', $id)->first();

            if (!$user) 
            {
                Flasher::addError('User Not Found!');
                return redirect()->route('users.index');
            }

            if ($user_role === 'super_admin') 
            {
                $roles = DB::table('role_mst')
                    ->where('role_name', '!=', 'super_admin')
                    ->get();
            } 
            else 
            {
                $roles = DB::table('role_mst')
                    ->where('role_name', '!=', 'super_admin')
                    ->where('role_name', '!=', 'postsale')
                    ->where('manager_rights', 0)
                    ->get();
            }

            $reporting_manager = DB::table('users')
                ->whereNotNull('role')
                ->where('id', '!=', $id)
                ->get();

            $designation = DB::table('designation')->get();
            $userLimit = DB::table('software_details')->select('user_limit')->first();
            $zones = DB::table('zones')
                ->leftJoin('state_district', 'zones.city_id', '=', 'state_district.id')
                ->where('zones.status', '=', '1')
                ->select(
                    'zones.*',
                    'state_district.state as state_name',
                    'state_district.District as district_name'
                )
                ->get();
            return view('staff-management.edit', compact('user', 'roles', 'reporting_manager', 'designation', 'userLimit', 'zones'));
        } 
        catch (Exception $error) 
        {
            Flasher::addError($error->getMessage());
            return redirect()->route('users.index');
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email,'.$id,
            'mobile' => 'required|digits:10',
            'role' => 'required|string',
            'designation' => 'nullable',
            'reporting_manager' => 'nullable',
            'zone_id' => 'required',
        ]);

        if ($validator->fails()) 
        {
            foreach ($validator->errors()->all() as $error) 
            {
                Flasher::addError($error);
            }
            return redirect()->back()->withInput();
        }

        try 
        {
            $existingUser = DB::table('users')->where('id', $id)->first();

            if (!$existingUser) 
            {
                Flasher::addError('User not found.');
                return redirect()->back();
            }
            $software = DB::table('software_details')
                ->where('status', 'active')
                ->first();

            if (!$software || empty($software->user_limit)) 
            {
                Flasher::addError('User limit not configured. Please contact admin.');
                return redirect()->back()->withInput();
            }

            if ($software->user_limit !== 'all') 
            {
                $limit = (int) $software->user_limit;
                $totalUsers = DB::table('users')->count();
                if ($totalUsers > $limit) 
                {
                    Flasher::addError(
                        "User limit reached ({$limit}). Please contact Clikzop innovation."
                    );
                    return redirect()->back()->withInput();
                }
            }
            $newRole = $request->role;
            $oldRole = $existingUser->role;

            if ($newRole !== $oldRole) 
            {
                DB::table('user_promotion')->insert([
                    'user_id' => $id,
                    'old_role' => $oldRole,
                    'new_role' => $newRole,
                    'is_approved' => 0,
                    'tm_id' => $request->reporting_manager ?? null,
                    'created_date' => now(),
                    'updated_date' => now(),
                ]);
            }

            $updateData = [
                'name' => $request->name,
                'email' => $request->email,
                'mobile' => $request->mobile,
                'role' => $oldRole,
                'designation_id' => $request->designation ?? null,
                'tm_id' => $request->reporting_manager ?? null,
                'zone_id' => $request->zone_id,
                'updated_date' => now(),
            ];

            if ($request->filled('password')) 
            {
                $updateData['password'] = $request->password;
            }

            DB::table('users')->where('id', $id)->update($updateData);

            Flasher::addSuccess('User updated successfully.');
            return redirect()->route('users.index');

        } 
        catch (Exception $e) 
        {
            Flasher::addError('Something went wrong: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function checkDelete($id)
    {
        if (!$id) 
        {
            return response()->json([
                'success' => false,
                'message' => 'Invalid action'
            ]);
        }

        $leadsCount = DB::table('leads')->where('user_id', $id)->count();

        if ($leadsCount > 0) 
        {
            return response()->json([
                'success' => false,
                'hasLeads' => true,
                'leadsCount' => $leadsCount,
                'transferUrl' => route('lead.transfer', ['user' => $id, 'status' => 'ALL LEAD'])
            ]);
        }
        try 
        {
            DB::table('users')->where('id', $id)->delete();
            return response()->json([
                'success' => true,
            ]);
        } 
        catch (\Exception $e) 
        {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function promote_list()
    {
        $user_role = session()->get('user_type');
        if ($user_role !== 'super_admin' && $user_role !== 'divisional_head' ) 
        {
            abort(404); 
        }
        $users = DB::table('user_promotion as a')
            ->join('users as b', 'a.user_id', '=', 'b.id')
            ->select('a.*', 'b.name as name')
            ->paginate(10);
        return view('staff-management.promote-list', compact('user_role', 'users'));
    }

    public function approved(Request $request, $id)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
        ]);

        $action = $request->input('action');
        $promotion = DB::table('user_promotion')->where('id', $id)->first();

        if (!$promotion) 
        {
            return redirect()->back()->with('error', 'Promotion request not found.');
        }

        if ($action === 'approve') 
        {
            DB::table('users')
                ->where('id', $promotion->user_id)
                ->update([
                    'role' => $promotion->new_role,
                    'tm_id' => $promotion->tm_id,
                ]);

            DB::table('user_promotion')
                ->where('id', $id)
                ->update(['is_approved' => 1]);

            return redirect()->back()->with('success', 'User promotion approved successfully.');
        } 
        elseif ($action === 'reject') 
        {
            DB::table('user_promotion')
                ->where('id', $id)
                ->update(['is_approved' => 2]);

            return redirect()->back()->with('success', 'User promotion rejected.');
        }
        return redirect()->back()->with('error', 'Invalid action.');
    }

    public function designation_list()
    {
        $user_role = session()->get('user_type');
        if ($user_role !== 'super_admin') 
        {
            abort(404); 
        }
        $designations = DB::table('designation')->paginate(10);
        return view('staff-management.designation-list', compact('designations'));
    }

    public function update_designation(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'designation' => 'required|string|max:255',
        ]);

        if ($validator->fails()) 
        {
            Flasher::addError($error);  
            return redirect()->back()->withInput();
        }

        DB::table('designation')->where('id', $id)->update([
            'designation' => $request->input('designation'),
        ]);

        return redirect()->route('designation.list')->with('success', 'Designation updated successfully.');
    }

    public function store_designation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'designation' => 'required|string|max:255|unique:designation,designation',
        ]);

        if ($validator->fails()) 
        {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::table('designation')->insert([
            'designation' => $request->input('designation'),
            'created_at'  => now(),
        ]);

        return redirect()->route('designation.list')->with('success', 'Designation created successfully.');
    }

    public function company_hierarchy()
    {
        $user_role = session()->get('user_type');
        if ($user_role !== 'super_admin' && $user_role !== 'divisional_head') {
            abort(404);
        }

        $users = DB::table('users as a')
            ->leftJoin('designation as b', 'a.designation_id', '=', 'b.id')
            ->leftJoin('zones as z', 'a.zone_id', '=', 'z.id')
            ->leftJoin('state_district as sd', 'z.city_id', '=', 'sd.id')
            ->select(
                'a.*',
                'b.designation',
                'z.zone_name',
                'z.sub_area',
                'sd.state as state_name',
                'sd.District as district_name'
            )
            ->get();

        $data = [];

        foreach ($users as $user) 
        {
            $zoneText = $user->district_name . ',' . $user->zone_name ? $user->district_name . ',' . $user->zone_name : 'N/A';
            if ($user->sub_area) {
                $zoneText .= ' (' . $user->sub_area . ')';
            }

            $formattedName = $user->name .
                '<div style="color:green;font-weight:bold">' . $user->role . '</div>' .
                '<div>' . ($user->designation ?? '') . '</div>' .
                '<div style="color:#007bff;font-weight:500">Zone: ' . $zoneText . '</div>';
            $nameNode = [
                'v' => (string)$user->id,
                'f' => $formattedName
            ];

            $managerId = ($user->tm_id == $user->id || $user->tm_id == 0) ? null : (string)$user->tm_id;

            $data[] = [$nameNode, $managerId, $user->designation];
        }

        return view('staff-management.company-hierarchy', ['data' => $data]);
    }

    private function findUserName($users, $id)
    {
        foreach ($users as $user) 
        {
            if ($user->id == $id) 
            {
                return $user->name;
            }
        }
        return null;
    }

    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'csv_file' => 'required|file|mimes:csv,txt'
        ]);

        if ($validator->fails()) 
        {
            foreach ($validator->errors()->all() as $error) 
            {
                Flasher::addError($error);
            }
            return redirect()->back();
        }

        try 
        {
            $file = $request->file('csv_file');
            $filePath = $file->getRealPath();
            
            $csvData = array_map('str_getcsv', file($filePath));
            $header = array_shift($csvData);
            $header = array_map('strtolower', $header);
            
            $requiredHeaders = ['name', 'email', 'phone', 'password', 'role'];
            $missingHeaders = array_diff($requiredHeaders, $header);
            
            if (!empty($missingHeaders)) 
            {
                Flasher::addError("CSV file is missing required columns: " . implode(', ', $missingHeaders));
                return redirect()->back();
            }

            $validRoles = DB::table('role_mst')
                ->where('role_name', '!=', 'super_admin')
                ->pluck('role_name')
                ->toArray();
                
            $results = [
                'imported' => 0,
                'skipped' => 0,
                'errors' => []
            ];
            
            $existingData = DB::table('users')
                ->select('email', 'mobile')
                ->get()
                ->mapWithKeys(function ($item) 
                {
                    return [$item->email => true, $item->mobile => true];
                })
                ->toArray();
            
            foreach ($csvData as $index => $row) 
            {
                $rowNumber = $index + 2;
                try 
                {
                    $row = array_combine($header, $row);
                    $row = array_change_key_case($row, CASE_LOWER);
                    
                    if (strtolower($row['role']) === 'super_admin') 
                    {
                        throw new \Exception("Admin role cannot be imported");
                    }
                    
                    if (!in_array($row['role'], $validRoles)) 
                    {
                        throw new \Exception("Invalid role '{$row['role']}'");
                    }
                    
                    if (isset($existingData[$row['email']])) 
                    {
                        throw new \Exception("Email '{$row['email']}' already exists");
                    }
                    
                    if (isset($existingData[$row['phone']])) 
                    {
                        throw new \Exception("Phone number '{$row['phone']}' already exists");
                    }
                    
                    $validator = Validator::make($row, [
                        'name' => 'required|string|max:255',
                        'email' => 'required|email|max:255|unique:users,email',
                        'phone' => 'required|digits:10|unique:users,mobile',
                        'password' => 'required|string|min:5',
                        'role' => 'required|string|max:255',
                    ]);
                    
                    if ($validator->fails()) 
                    {
                        throw new \Exception(implode(' ', $validator->errors()->all()));
                    }

                    DB::table('users')->insert([
                        'name' => $row['name'],
                        'email' => $row['email'],
                        'mobile' => $row['phone'],
                        'password' => $row['password'],
                        'role' => $row['role'],
                        'created_date' => now(),
                        'updated_date' => now(),
                        'is_active' => 1,
                    ]);
                    
                    $existingData[$row['email']] = true;
                    $existingData[$row['phone']] = true;
                    $results['imported']++;
                    
                } 
                catch (\Exception $e) 
                {
                    $results['skipped']++;
                    $results['errors'][] = "Row $rowNumber: " . $e->getMessage();
                }
            }
            
            if ($results['imported'] > 0) 
            {
                Flasher::addSuccess("Successfully imported {$results['imported']} users.");
            }
            
            if ($results['skipped'] > 0) 
            {
                session()->flash('import_errors', array_slice($results['errors'], 0, 20));
                
                if (count($results['errors']) > 20) 
                {
                    Flasher::addWarning("Skipped {$results['skipped']} rows. Showing first 20 errors.");
                } 
                else 
                {
                    Flasher::addWarning("Skipped {$results['skipped']} rows. See details below.");
                }
                
                return redirect()->back();
            }
            
            return redirect()->route('users.index');
            
        } 
        catch (\Exception $e) 
        {
            Flasher::addError('Error importing users: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function updateStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'is_active' => 'required|boolean'
        ]);

        if ($validator->fails()) 
        {
            return response()->json([
                'success' => false,
                'message' => 'Invalid request'
            ], 400);
        }

        try 
        {
            DB::table('users')
                ->where('id', $request->user_id)
                ->update(['is_active' => $request->is_active]);

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully'
            ]);
        } 
        catch (\Exception $e) 
        {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status'
            ], 500);
        }
    }

    public function category_list()
    {
        $categories = DB::table('category')->paginate(10);
        return view('master.category', compact('categories'));
    }

    public function store_category(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        DB::table('category')->insert([
            'name' => $request->name,
        ]);

        return redirect()->route('category.list')->with('success', 'Category added successfully.');
    }

    public function update_category(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        DB::table('category')->where('id', $id)->update([
            'name' => $request->name,
            'updated_at' => now(),
        ]);

        return redirect()->route('category.list')->with('success', 'Category updated successfully.');
    }

     
    public function property_name()
    {
        try 
        {
           $properties = DB::table('property as pr')
            ->join('users as u', 'pr.user_id', '=', 'u.id')
            ->select('pr.*', 'u.name as user_name')
            ->paginate(10);
            foreach($properties as $property) 
            {
                if($property->property_category) 
                {
                    $category = DB::table('inv_catg')
                        ->where('name', $property->property_category)
                        ->first();
                    $property->category_id = $category->id ?? null;
                } 
                else 
                {
                    $property->category_id = null;
                }
            }
            
            $categoryList = DB::table('category')->select('id', 'name')->get();
            $invCatg = DB::table('inv_catg')->select('id', 'type', 'name')->get();
            
            return view('master.property', compact('properties', 'categoryList', 'invCatg'));
        } 
        catch (\Exception $e) 
        {
            Flasher::addError('Failed to load projects: ' . $e->getMessage());
            return back();
        }
    }

    public function store_property(Request $request)
    {
        DB::beginTransaction();
        try 
        {
            $rules = [
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('projects', 'project_name')
                ]
            ];
            
            if (session('software_type') !== 'lead_management') 
            {
                $rules['property_type'] = 'nullable|string|max:100';
                $rules['property_category'] = 'nullable|exists:inv_catg,id';
                $rules['property_sub_category'] = 'nullable|string|max:255';
                $rules['state'] = 'nullable|string|max:100';
                $rules['city'] = 'nullable|string|max:100';
                $rules['address'] = 'nullable|string';
                $rules['budget_price'] = 'nullable|string|max:100';
                $rules['property_status'] = 'nullable|in:Available,Hold,Procession,Sold';
                $rules['gallery_images.*'] = 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048';
            }

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) 
            {
                foreach ($validator->errors()->all() as $error) 
                {
                    Flasher::addError($error);
                }
                return redirect()->back()->withInput();
            }
            
            $data = [
                'project_name' => $request->name,
                'created_date' => now()
            ];
            
            if (session('software_type') !== 'lead_management') 
            {
                if ($request->hasFile('gallery_images')) 
                {
                    $images = [];
                    foreach ($request->file('gallery_images') as $image) 
                    {
                        $path = $image->store('property-gallery', 'public');
                        $images[] = '/storage/' . $path;
                    }
                    $data['gallery_images'] = json_encode($images);
                }
                
                $categoryName = null;
                if ($request->property_category) 
                {
                    $category = DB::table('inv_catg')->find($request->property_category);
                    $categoryName = $category->name ?? null;
                }

                $data['property_type'] = $request->property_type;
                $data['property_category'] = $categoryName;
                $data['property_sub_category'] = $request->property_sub_category;
                $data['state'] = $request->state;
                $data['city'] = $request->city;
                $data['address'] = $request->address;
                $data['budget_price'] = $request->budget_price;
                $data['property_status'] = $request->property_status ?? 'Available';
            }

            DB::table('projects')->insert($data);

            DB::commit();
            Flasher::addSuccess('Project created successfully.');
            return redirect()->route('project.name');
        } 
        catch (\Exception $e) 
        {
            DB::rollBack();
            Flasher::addError('Failed to create: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function update_property(Request $request, $id)
    {
        DB::beginTransaction();
        try 
        {
            $rules = [
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('projects', 'project_name')->ignore($id)
                ]
            ];
            
            if (session('software_type') !== 'lead_management') 
            {
                $rules['property_type'] = 'nullable|string|max:100';
                $rules['property_category'] = 'nullable|exists:inv_catg,id';
                $rules['property_sub_category'] = 'nullable|string|max:255';
                $rules['state'] = 'nullable|string|max:100';
                $rules['city'] = 'nullable|string|max:100';
                $rules['address'] = 'nullable|string';
                $rules['budget_price'] = 'nullable|string|max:100';
                $rules['property_status'] = 'nullable|in:Available,Hold,Procession,Sold';
                $rules['gallery_images.*'] = 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048';
            }

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails())
            {
                foreach ($validator->errors()->all() as $error) 
                {
                    Flasher::addError($error);
                }
                return redirect()->back()->withInput();
            }
            
            $data = [
                'project_name' => $request->name
            ];
            
            if (session('software_type') !== 'lead_management') 
            {
                if ($request->hasFile('gallery_images')) 
                {
                    $oldProject = DB::table('projects')->where('id', $id)->first();
                    if ($oldProject && $oldProject->gallery_images) 
                    {
                        $oldImages = json_decode($oldProject->gallery_images);
                        foreach ($oldImages as $oldImage) 
                        {
                            $path = str_replace('/storage/', '', $oldImage);
                            Storage::disk('public')->delete($path);
                        }
                    }
                    $images = [];
                    foreach ($request->file('gallery_images') as $image) 
                    {
                        $path = $image->store('property-gallery', 'public');
                        $images[] = '/storage/' . $path;
                    }
                    $data['gallery_images'] = json_encode($images);
                }
                
                $categoryName = null;
                if ($request->property_category) 
                {
                    $category = DB::table('inv_catg')->find($request->property_category);
                    $categoryName = $category->name ?? null;
                }

                $data['property_type'] = $request->property_type;
                $data['property_category'] = $categoryName;
                $data['property_sub_category'] = $request->property_sub_category;
                $data['state'] = $request->state;
                $data['city'] = $request->city;
                $data['address'] = $request->address;
                $data['budget_price'] = $request->budget_price;
                $data['property_status'] = $request->property_status;
                $data['updated_date'] = now();
            }

            DB::table('projects')
                ->where('id', $id)
                ->update($data);

            DB::commit();
            Flasher::addSuccess('Project updated successfully.');
            return redirect()->route('project.name');
        } 
        catch (\Exception $e) 
        {
            DB::rollBack();
            Flasher::addError('Failed to update: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }
}