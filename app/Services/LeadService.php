<?php
namespace App\Services;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Flasher\Laravel\Facade\Flasher;
class LeadService
{
    public function createLead($request, $isMobile = false)
    {
        $current_user_id = Session::get('user_id');
        $user_type = Session::get('user_type');
        $allocated_to = $request->input('allocated_lead');
        
        if ($allocated_to) 
        {
            $user_id = $allocated_to;
        } 
        else 
        {
            if ($user_type === 'reception') 
            {
                $admin = DB::table('users')->where('role', 'super_admin')->first();
                if ($admin) 
                {
                    $user_id = $admin->id;
                }
            } 
            else 
            {
                $user_id = $current_user_id;
            }
        }

        $rules = [
            'type' => 'nullable',
            'category' => 'nullable', 
            'sub_category' => 'nullable', 
            'source' => 'nullable',
            'campaign' => 'nullable',
            'classification' => 'nullable|in:hot,cold,warm',
            'projects' => 'nullable',
            'name' => 'nullable|required',
            'email' => 'nullable|email',
            'phone' => 'required|numeric|digits:10',
            'whatsapp' => 'nullable',
            'budget' => 'nullable',
            'field1' => 'nullable',
            'field2' => 'nullable',
            'field3' => 'nullable',
            'comment' => 'nullable',
            'allocated_lead' => 'nullable|exists:users,id',
        ];

        $rules['remind_date'] = 'required_if:status,CALL SCHEDULED,VISIT SCHEDULED,INTERESTED, MEETING SCHEDULED';
        $rules['remind_time'] = 'required_if:status,CALL SCHEDULED,VISIT SCHEDULED,INTERESTED, MEETING SCHEDULED';

        $validator = Validator::make($request->all(), $rules);

        $validator->after(function ($validator) use ($request) 
        {
            $duplicate = DB::table('leads')
                ->join('users', 'leads.user_id', '=', 'users.id')
                ->where('leads.phone', $request->phone)
                ->select('leads.id', 'users.name')
                ->first();

            if ($duplicate) 
            {
                $validator->errors()->add('phone', 'Duplicate Lead ID: ' . $duplicate->id . ' User: ' . $duplicate->name);
            }
        });

        if ($validator->fails()) 
        {
            foreach ($validator->errors()->all() as $error) 
            {
                Flasher::addError($error);
            }
            return [
                'success' => false,
                'errors' => $validator->errors(),
                'redirect' => $isMobile ? route('mobile.leads.create') : route('lead.add')
            ];
        }
        if ($allocated_to) 
        {
            $allocatedUser = DB::table('users')->where('id', $allocated_to)->first();
            if (in_array($allocatedUser->role, ['super_admin', 'divisional_head'])) 
            {
                $status = 'allocated_lead';
            } 
            else 
            {
                $status = 'NEW LEAD';
            }
        } 
        else 
        {
            if (in_array($user_type, ['super_admin', 'divisional_head'])) 
            {
                $status = 'allocated_lead';
            } 
            else 
            {
                $status = 'NEW LEAD';
            }
        }
        
        $now = now();

        try 
        {
            DB::beginTransaction();
            $projectIds = '';
            if($request->projects)
            {
                $projectIds = implode(',', $request->projects);
            }
            
            $leadData = [
                'remind_date' => $request->remind_date,
                'remind_time' => $request->remind_time,
                'classification' => $request->classification,
                'source' => $request->source,
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'campaign' => $request->campaign,
                'status' => $status,
                'last_comment' => $request->comment ?? 'Lead Added',
                'user_id' => $user_id,
                'project_id' => $projectIds, 
                'type' => $request->type,
                'catg_id' => $request->category,
                'sub_catg_id' => $request->sub_category,
                'whatsapp_no' => $request->whatsapp,
                'budget' => $request->budget,
                'field1' => $request->field1,
                'field2' => $request->field2,
                'field3' => $request->field3,
                'property_city' => $request->property_city,
                'property_state' => $request->property_state,
                'property_location' => $request->property_location,
                'lead_date' => $now,
                'created_at' => $now,
                'updated_at' => $now,
                'updated_date' => $now,
            ];
            
            if ($allocated_to && $allocated_to != $current_user_id) 
            {
                $leadData['is_allocated'] = $current_user_id;
                $leadData['allocated_date'] = $now;
                $leadData['unallocated_lead'] = 0;
            } 
            else 
            {
                $leadData['is_allocated'] = 0;
                $leadData['unallocated_lead'] = 0;
            }
            
            $leadId = DB::table('leads')->insertGetId($leadData);
            $commentText = $request->comment ?? 'Lead Added';
            
            if ($allocated_to && $allocated_to != $current_user_id) 
            {
                $allocatedUserName = DB::table('users')->where('id', $allocated_to)->value('name');
                $allocatedUserRole = DB::table('users')->where('id', $allocated_to)->value('role');
                $statusText = in_array($allocatedUserRole, ['super_admin', 'divisional_head']) ? 'allocated_lead' : 'NEW LEAD';
                $commentText .= " (Allocated to: {$allocatedUserName} - Status: {$statusText})";
            }
            
            DB::table('lead_comments')->insert([
                'remind_date' => $request->remind_date,
                'remind_time' => $request->remind_time,
                'lead_id' => $leadId,
                'user_id' => $current_user_id, 
                'comment' => $commentText,
                'status' => $status,
                'created_date' => $now,
            ]);

            DB::commit();
            
            Flasher::addSuccess('Lead saved successfully!');
            
            return [
                'success' => true,
                'redirect' => $isMobile ? route('mobile.leads.create') : route('lead.add')
            ];

        } 
        catch (\Exception $e) 
        {
            DB::rollBack();
            Flasher::addError('Error saving lead: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'redirect' => $isMobile ? route('mobile.leads.create') : route('lead.add')
            ];
        }
    }

    public function editLead($id, $isMobile = false)
    {
        $lead = DB::table('leads')->where('id', $id)->first();
        
        if (!$lead) 
        {
            Flasher::addError('Lead not found');
            return [
                'success' => false,
                'redirect' => null,
            ];
        }

        $categorys = DB::table('inv_catg')->get();
        $sources = DB::table('sources')->get();
        $campaigns = DB::table('campaigns')->get();
        $projects = DB::table('projects')->get();

        $currentCategory = DB::table('inv_catg')->where('id', $lead->catg_id)->first();
        $currentSubCategory = DB::table('inv_subcatg')->where('id', $lead->sub_catg_id)->first();
        $currentSource = $lead->source;
        $currentCampaign = $lead->campaign;
        $currentProject = $lead->project_id;

        return [
           'success' => true,
            'redirect' => null, 
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
        ];
    }

    public function updateLead($request, $id, $isMobile = false)
    {
        $current_user_id = Session::get('user_id');
        $user_type = Session::get('user_type');
        
        $allocated_to = $request->input('allocated_lead');
        
        $rules = [
            'type' => 'nullable',
            'category' => 'nullable',
            'sub_category' => 'nullable',
            'source' => 'nullable',
            'campaign' => 'nullable',
            'classification' => 'nullable|in:hot,cold,warm',
            'projects' => 'nullable',
            'name' => 'required',
            'email' => 'nullable|email',
            'phone' => 'required|numeric|digits:10',
            'whatsapp' => 'nullable',
            'field1' => 'nullable',
            'field2' => 'nullable',
            'field3' => 'nullable',
            'property_city' => 'nullable',
            'property_state' => 'nullable',
            'property_location' => 'nullable',
            'comment' => 'nullable',
            'budget' => 'nullable',
            'status' => 'nullable|in:NEW LEAD,ALLOCATED,PENDING,PROCESSING,INTERESTED,CALL SCHEDULED,VISIT SCHEDULED,VISIT DONE,SM NEW LEADS,WRONG NUMBER,NOT INTERESTED,FUTURE LEAD,NOT PICKED,NOT REACHABLE,LOST,CHANNEL PARTNER,CONVERTED',
            'conversionType' => 'required_if:status,CONVERTED|in:Completed,Cancelled,Booked',
            'remind_datetime' => 'required_if:status,CALL SCHEDULED,VISIT SCHEDULED,MEETING SCHEDULED,INTERESTED|date|after_or_equal:now',
            'allocated_lead' => 'nullable|exists:users,id',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) 
        {
            foreach ($validator->errors()->all() as $error) 
            {
                Flasher::addError($error);
            }
            return [
                'success' => false,
                'errors' => $validator->errors(),
                'redirect' => $isMobile ? route('mobile.leads.edit', $id) : route('lead.edit', $id)
            ];
        }

        try 
        {
            DB::beginTransaction();
            
            $lead = DB::table('leads')->where('id', $id)->first();
            
            if (!$lead) 
            {
                throw new \Exception('Lead not found');
            }
            
            $projectIds = '';
            if($request->projects)
            {
                $projectIds = implode(',', $request->projects);
            }
            
            $leadData = [
                'type' => $request->type,
                'catg_id' => $request->category,
                'sub_catg_id' => $request->sub_category,
                'source' => $request->source,
                'campaign' => $request->campaign,
                'classification' => $request->classification,
                'project_id' => $projectIds,
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'budget' => $request->budget,
                'field1' => $request->field1,
                'field2' => $request->field2,
                'field3' => $request->field3,
                'property_city' => $request->property_city,
                'property_state' => $request->property_state,
                'property_location' => $request->property_location,
                'whatsapp_no' => $request->whatsapp,
                'last_comment' => $request->comment ?? 'Lead Updated',
                'updated_date' => now(),
                'updated_at' => now(),
            ];

            $allocationChanged = false;
            $oldUserId = $lead->user_id;
            
            if ($allocated_to && $allocated_to != $lead->user_id) 
            {
                $leadData['user_id'] = $allocated_to;
                $leadData['is_allocated'] = $current_user_id;
                $leadData['allocated_date'] = now();
                $leadData['unallocated_lead'] = 0;
                $allocationChanged = true;
                $allocatedUser = DB::table('users')->where('id', $allocated_to)->first();
                if (in_array($allocatedUser->role, ['super_admin', 'divisional_head'])) 
                {
                    $leadData['status'] = 'allocated_lead';
                } 
                else 
                {
                    $leadData['status'] = 'NEW LEAD';
                }
            }

            if ($request->filled('status')) 
            {
                $leadData['status'] = $request->status;
                if ($request->filled('remind_datetime')) 
                {
                    $remindDateTime = \Carbon\Carbon::parse($request->remind_datetime);
                    $leadData['remind_date'] = $remindDateTime->toDateString(); 
                    $leadData['remind_time'] = $remindDateTime->toTimeString(); 
                }

                if ($request->status === 'CONVERTED') 
                {
                    $leadData['conversion_type'] = $request->conversionType;
                }
            }

            DB::table('leads')->where('id', $id)->update($leadData);
            
            $commentText = '';
            $statusForComment = 'UPDATED';
            
            if ($allocationChanged) 
            {
                $oldUser = DB::table('users')->where('id', $oldUserId)->value('name');
                $newUser = DB::table('users')->where('id', $allocated_to)->value('name');
                $newUserRole = DB::table('users')->where('id', $allocated_to)->value('role');
                $statusText = in_array($newUserRole, ['super_admin', 'divisional_head']) ? 'allocated_lead' : 'NEW LEAD';
                $commentText = "Lead reallocated from {$oldUser} to {$newUser} (Status: {$statusText})";
                $statusForComment = 'REALLOCATED';
            }
            
            if ($request->filled('status')) 
            {
                if (!empty($commentText))
                {
                    $commentText .= " | Status changed to " . $request->status;
                } 
                else 
                {
                    $commentText = "Status changed to " . $request->status;
                }
                $statusForComment = $request->status;
            }
            
            if ($request->filled('comment')) 
            {
                if (!empty($commentText)) 
                {
                    $commentText .= " | Note: " . $request->comment;
                } 
                else 
                {
                    $commentText = $request->comment;
                }
            }
            
            if (empty($commentText)) 
            {
                $commentText = 'Lead updated';
            }

            DB::table('lead_comments')->insert([
                'lead_id' => $id,
                'user_id' => $current_user_id,
                'comment' => $commentText,
                'status' => $statusForComment,
                'remind_date' => isset($remindDateTime) ? $remindDateTime->toDateString() : ($request->remind_date ?? null),
                'remind_time' => isset($remindDateTime) ? $remindDateTime->toTimeString() : ($request->remind_time ?? null),
                'created_date' => now(),
            ]);

            DB::commit();
            Flasher::addSuccess('Lead updated successfully!');
            
            return [
                'success' => true,
                'redirect' => $isMobile ? route('mobile.leads.edit', $id) : route('lead.edit', $id)
            ];
        } 
        catch (\Exception $e) 
        {
            DB::rollBack();
            Flasher::addError('Error updating lead: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'redirect' => $isMobile ? route('mobile.leads.edit', $id) : route('lead.edit', $id)
            ];
        }
    }

    public function updateLeadStatus($request, $isMobile = false)
    {      
        $user_id = Session::get('user_id');
        $rules = [
            'leadId' => 'required|exists:leads,id',
            'newStatus' => 'required|in:NEW LEAD,ALLOCATED,PENDING,PROCESSING,INTERESTED,CALL SCHEDULED,VISIT SCHEDULED,VISIT DONE,SM NEW LEADS,WRONG NUMBER,NOT INTERESTED,FUTURE LEAD,NOT PICKED,NOT REACHABLE,LOST,CHANNEL PARTNER,CONVERTED,MEETING SCHEDULED,WHATSAPP,booked',
            'conversionType' => 'required_if:newStatus,CONVERTED|in:Completed,Cancelled,booked',
            'comment' => 'nullable|string|max:500',
            'remindDate' => 'nullable|required_if:newStatus,CALL SCHEDULED,VISIT SCHEDULED,MEETING SCHEDULED,INTERESTED|date|after_or_equal:today',
            'remindTime' => 'nullable|required_if:newStatus,CALL SCHEDULED,VISIT SCHEDULED,MEETING SCHEDULED,INTERESTED',
        ];

        if ($request->newStatus === 'CONVERTED') 
        {
            $rules = array_merge($rules, [
                'app_name' => 'nullable|string|max:255',
                'app_contact' => 'nullable|numeric|digits:10',
                'app_city' => 'nullable|string|max:255',
                'app_dob' => 'nullable|date',
                'app_doa' => 'nullable|date',
                'final_price' => 'nullable|numeric',
                'prj_id' => 'nullable|integer',
                'prop_size' => 'nullable|string|max:255',
            ]);
        }

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) 
        {
            return [
                'success' => false,
                'message' => $validator->errors()->first(),
                'status' => 422
            ];
        }

        try 
        {
            DB::beginTransaction();
            $lead = DB::table('leads')->where('id', $request->leadId)->first();
            $projectIds = $request->prj_id ?? [];
            $projectIdsString = implode(',', $projectIds);
            // dd($projectIdsString);
            $baseComment = $request->comment ?: 'Status changed to ' . $request->newStatus;
            $status = trim(strtoupper($request->newStatus));
            $projectName = null;
            if (!empty($request->prj_id)) 
            {
                $projectName = DB::table('projects')->where('id', $request->prj_id)->value('project_name');
            } 
            elseif (!empty($request->visitProjects) && is_array($request->visitProjects)) 
            {
                $projectNames = DB::table('projects')
                    ->whereIn('id', $request->visitProjects)
                    ->pluck('project_name')
                    ->toArray();
                $projectName = implode(', ', $projectNames);
            } 
            elseif (!empty($lead->project_id)) 
            {
                $projectName = DB::table('projects')->where('id', $lead->project_id)->value('project_name');
            }
            if (in_array($status, ['VISIT SCHEDULED', 'VISIT DONE']) && $projectName) 
            {
                $commentText = $baseComment . ' | Project: ' . $projectName;
            } 
            else 
            {
                $commentText = $baseComment;
            }
            // dd($commentText);
            $updateData = [
                'status' => $request->newStatus,
                'updated_date' => now(),
                'remind_date' => $request->remindDate,
                'remind_time' => $request->remindTime,
                'last_comment' => $commentText,
            ];

            if ($request->newStatus === 'VISIT DONE') 
            {
                $updateData['visited_on'] = 1;
            }

            if ($request->newStatus === 'CONVERTED') 
            {
                $updateData = array_merge($updateData, [
                    'conversion_type' => $request->conversionType,
                    'app_name' => $request->app_name,
                    'app_contact' => $request->app_contact,
                    'app_city' => $request->app_city,
                    'app_dob' => $request->app_dob,
                    'app_doa' => $request->app_doa,
                    'final_price' => $request->final_price,
                    'project_id' => $request->prj_id,
                    'size' => $request->prop_size,
                ]);
            }
            $updateData = array_merge($updateData, [
                'project_id' => $projectIdsString,
            ]);
            DB::table('leads')->where('id', $request->leadId)->update($updateData);
            DB::table('lead_comments')->insert([
                'lead_id' => $request->leadId,
                'user_id' => $user_id,
                'comment' => $commentText,
                'status' => $request->newStatus,
                'remind_date' => $request->remindDate,
                'remind_time' => $request->remindTime,
                'created_date' => now()->format('Y-m-d H:i:s'),
            ]);

            DB::commit();

            return [
                'success' => true,
                'message' => 'Status updated successfully',
                'status' => 200
            ];

        } 
        catch (\Exception $e) 
        {
            DB::rollBack();
            return [
                'success' => false,
                'message' => 'Error updating status: ' . $e->getMessage(),
                'status' => 500
            ];
        }
    }

    public function quickLead($request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required|digits:10',
        ]);

        if ($validator->fails()) 
        {
            return [
                'success' => false,
                'message' => $validator->errors()->first()
            ];
        }

        try 
        {
            $userId = Session::get('user_id');
            $userType = Session::get('user_type');
            $phone = $request->phone;

            $existingLead = DB::table('leads')
                ->where('phone', $phone)
                ->first();

            if ($existingLead) 
            {
                return [
                    'success' => false,
                    'message' => "This phone number already exists (Lead ID: {$existingLead->id})"
                ];
            }

            $status = in_array($userType, ['super_admin', 'divisional_head']) ? 'allocated_lead' : 'NEW LEAD';
            $isAllocated = in_array($userType, ['super_admin', 'divisional_head']) ? 0 : 1;

            $now = now();

            $leadId = DB::table('leads')->insertGetId([
                'name' => $request->name,
                'phone' => $phone,
                'status' => $status,
                'user_id' => $userId,
                'is_allocated' => $isAllocated,
                'visited_on' => 0,
                'is_pinned' => 0,
                'lead_date' => $now,
                'created_at' => $now,
                'updated_date' => $now,
            ]);

            DB::table('lead_comments')->insert([
                'lead_id' => $leadId,
                'user_id' => $userId,
                'comment' => 'Quick lead created',
                'status' => $status,
                'created_date' => $now,
            ]);

            return [
                'success' => true,
                'message' => 'Lead added successfully!'
            ];

        } 
        catch (\Exception $error) 
        {
            return [
                'success' => false,
                'message' => 'Something went wrong. Please try again.' . $error->getMessage()
            ];
        }
    }

    public function getLeadStatistics($userId, $transferredToUserIds = [], $dateRange = [], $childIds = [])
    {
        $user_type = session('user_type', 'user');

        $query = DB::table('leads');
        
        if ($user_type != 'super_admin')
        {
            $query->where(function ($q) use ($userId, $childIds) 
            {
                $q->where('user_id', $userId);

                if (!empty($childIds)) 
                {
                    $q->orWhereIn('user_id', $childIds);
                }
                $q->orWhereRaw("FIND_IN_SET(?, lead_shared_with)", [$userId]);
            });
        } 
        else 
        {
            if (!empty($childIds)) 
            {
                $query->where(function ($q) use ($childIds, $userId) 
                {
                    $q->whereIn('user_id', $childIds)
                    ->orWhereRaw("FIND_IN_SET(?, lead_shared_with)", [$userId]);
                });
            } 
            else 
            {
                $query->where(function ($q) use ($userId) 
                {
                    $q->whereNotNull('user_id')
                    ->orWhereRaw("lead_shared_with IS NOT NULL AND lead_shared_with != ''");
                });
            }
        }

        if (!empty($dateRange['start']) && !empty($dateRange['end'])) 
        {
            $query->whereBetween('lead_date', [$dateRange['start'], $dateRange['end']]);
        }
        
        $query->select(
            DB::raw("COUNT(*) as total_lead"),
            DB::raw("SUM(CASE WHEN status = 'NEW LEAD' THEN 1 ELSE 0 END) as new_lead"),
            DB::raw("SUM(CASE WHEN status = 'ALLOCATED' THEN 1 ELSE 0 END) as allocated"),
            DB::raw("SUM(CASE WHEN status = 'SM NEW LEADS' THEN 1 ELSE 0 END) as sm_new_leads"),
            DB::raw("SUM(CASE WHEN status = 'PENDING' THEN 1 ELSE 0 END) as pending_lead"),
            DB::raw("SUM(CASE WHEN status = 'PROCESSING' THEN 1 ELSE 0 END) as processing"),
            DB::raw("SUM(CASE WHEN status = 'INTERESTED' THEN 1 ELSE 0 END) as interested"),
            DB::raw("SUM(CASE WHEN status = 'CALL SCHEDULED' THEN 1 ELSE 0 END) as call_schedule"),
            DB::raw("SUM(CASE WHEN status = 'MEETING SCHEDULED' THEN 1 ELSE 0 END) as meeting_scheduled"),
            DB::raw("SUM(CASE WHEN status = 'VISIT SCHEDULED' THEN 1 ELSE 0 END) as visit_schedule"),
            DB::raw("SUM(CASE WHEN status = 'VISIT DONE' THEN 1 ELSE 0 END) as visit_done"),
            DB::raw("SUM(CASE WHEN status = 'WHATSAPP' THEN 1 ELSE 0 END) as whatsapp"),
            DB::raw("SUM(CASE WHEN (status = 'CONVERTED' AND conversion_type = 'Completed') OR status = 'COMPLETED' THEN 1 ELSE 0 END) as completed"),
            DB::raw("SUM(CASE WHEN (status = 'CONVERTED' AND conversion_type = 'Cancelled') OR status = 'CANCELLED' THEN 1 ELSE 0 END) as cancelled"),
            DB::raw("SUM(CASE WHEN (status = 'CONVERTED' AND conversion_type = 'Booked') OR status = 'BOOKED' THEN 1 ELSE 0 END) as booked"),
            DB::raw("SUM(CASE WHEN status = 'NOT REACHABLE' THEN 1 ELSE 0 END) as not_reachable"),
            DB::raw("SUM(CASE WHEN status = 'WRONG NUMBER' THEN 1 ELSE 0 END) as wrong_number"),
            DB::raw("SUM(CASE WHEN status = 'CHANNEL PARTNER' THEN 1 ELSE 0 END) as channel_partner"),
            DB::raw("SUM(CASE WHEN status = 'NOT INTERESTED' THEN 1 ELSE 0 END) as not_interested"),
            DB::raw("SUM(CASE WHEN status = 'NOT PICKED' THEN 1 ELSE 0 END) as not_picked"),
            DB::raw("SUM(CASE WHEN status = 'FUTURE LEAD' THEN 1 ELSE 0 END) as future_lead"),
            DB::raw("SUM(CASE WHEN status = 'LOST' THEN 1 ELSE 0 END) as lost"),
            DB::raw("SUM(CASE WHEN status = 'TRANSFER LEAD' THEN 1 ELSE 0 END) as transfer_lead"),
        );

        return $query->first();
    }

    public function getTransferredLeadIds($userId)
    {
        $child_ids = Session::get('child_ids', '');
        $accessibleUserIds = array_merge([$userId], explode(',', $child_ids));
        $accessibleUserIds = array_filter($accessibleUserIds);
        
        return DB::table('transfer_leads')
            ->whereIn('to', $accessibleUserIds)
            ->pluck('lead_id')
            ->toArray();
    }
    
}