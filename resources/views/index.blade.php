@extends('layouts.app')
@section('title', 'Dashboard | Pro-leadexpertz')
@section('content')
@include('modals.task-file')
@include('modals.task-comment')
@include('modals.view-comments')
@include('modals.status-update')
@include('modals.quick-lead')
    @php
        $leadCards = [
            
            ['route' => 'lead.new', 'title' => 'New Leads', 'value' => $leadStats->new_lead ?? 0.00, 'icon' => 'bx bx-copy-alt'],
            
            ['route' => 'transfer_list.lead', 'title' => 'Transfer Leads', 'value' => $leadStats->transfer_lead = $transferLeadCount ?? 0.00, 'icon' => 'bx bx-transfer-alt'],
            
            ['route' => 'lead.pending', 'title' => 'Pending Leads', 'value' => $leadStats->pending_lead ?? 0.00, 'icon' => 'bx bx-time-five'],
            
            ['route' => 'lead.processing', 'title' => 'Processing Leads', 'value' => $leadStats->processing ?? 0.00, 'icon' => 'bx bx-loader-circle'],
            
            ['route' => 'lead.interested', 'title' => 'Interested Leads', 'value' => $leadStats->interested ?? 0.00, 'icon' => 'bx bx-like'],
            
            ['route' => 'lead.not_picked', 'title' => 'Not Picked Leads', 'value' => $leadStats->not_picked ?? 0.00, 'icon' => 'bx bx-dislike'],
            
            ['route' => 'lead.meeting_scheduled', 'title' => 'Meeting Scheduled Leads', 'value' => $leadStats->meeting_scheduled ?? 0.00, 'icon' => 'bx bx-calendar'],

            ['route' => 'lead.whatsapp', 'title' => 'Whatsapp Scheduled Leads', 'value' => $leadStats->whatsapp ?? 0.00, 'icon' => 'bx bx-check-double'],

            ['route' => 'lead.call_scheduled', 'title' => 'Call Scheduled Leads', 'value' => $leadStats->call_schedule ?? 0.00, 'icon' => 'bx bx-calendar'],
            
            ['route' => 'lead.visit_scheduled', 'title' => 'Visit Scheduled Leads', 'value' => $leadStats->visit_schedule ?? 0.00, 'icon' => 'bx bx-map'],
            
            ['route' => 'lead.visit_done', 'title' => 'Visit Done Leads', 'value' => $leadStats->visit_done ?? 0.00, 'icon' => 'bx bx-check-double'],
            
            ['route' => 'lead.booked', 'title' => 'Booked Leads', 'value' => $leadStats->booked ?? 0.00, 'icon' => 'bx bx-bookmark'],
            
            ['route' => 'lead.completed', 'title' => 'Completed', 'value' => $leadStats->completed ?? 0.00, 'icon' => 'bx bx-badge-check'],
            
            ['route' => 'lead.cancelled', 'title' => 'Cancelled', 'value' => $leadStats->cancelled ?? 0.00, 'icon' => 'bx bx-x-circle'],

            ['route' => null, 'title' => 'Other Leads', 'value' => ($leadStats->total_lead ?? 0.00)
                - ($leadStats->new_lead ?? 0)
                - ($leadStats->pending_lead ?? 0)
                - ($leadStats->processing ?? 0)
                - ($leadStats->interested ?? 0)
                - ($leadStats->transfer_lead ?? 0)
                - ($leadStats->call_schedule ?? 0)
                - ($leadStats->visit_schedule ?? 0)
                - ($leadStats->visit_done ?? 0)
                - ($leadStats->booked ?? 0)
                - ($leadStats->completed ?? 0)
                - ($leadStats->cancelled ?? 0),
                'icon' => 'bx bx-dots-horizontal-rounded'],
        ];

        $othersLeadData = [
            ['route' => 'lead.allocate', 'title' => 'Allocate Leads', 'value' => $allocatedLeadCount->total(), 'icon' => 'bx bx-copy-alt'],
            
            ['route' => 'lead.unallocated', 'title' => 'Unallocate Leads', 'value' => $unallocatedLeadCount->total(), 'icon' => 'bx bx-copy-alt'],
            
            ['route' => 'lead.not_reachable', 'title' => 'Not Reachable', 'value' => $leadStats->not_reachable ?? 0.00, 'icon' => 'bx bx-wifi-off'],
            
            ['route' => 'lead.wrong_number', 'title' => 'Wrong Number', 'value' => $leadStats->wrong_number ?? 0.00, 'icon' => 'bx bx-error'],
            
            ['route' => 'lead.channel_partner', 'title' => 'Channel Partner', 'value' => $leadStats->channel_partner ?? 0.00, 'icon' => 'bx bx-group'],
            
            ['route' => 'lead.not_interested', 'title' => 'Not Interested', 'value' => $leadStats->not_interested ?? 0.00, 'icon' => 'bx bx-dislike'],
            
            ['route' => 'lead.future', 'title' => 'Future Lead', 'value' => $leadStats->future_lead ?? 0.00, 'icon' => 'bx bx-time'],
            
            ['route' => 'lead.lost', 'title' => 'Lost', 'value' => $leadStats->lost ?? 0.00, 'icon' => 'bx bx-block'],
        ];

        $totalOthersLead = ($leadStats->total_lead ?? 0.00)
            - ($leadStats->new_lead ?? 0)
            - ($leadStats->pending_lead ?? 0)
            - ($leadStats->processing ?? 0)
            - ($leadStats->interested ?? 0)
            - ($leadStats->transfer_lead ?? 0)
            - ($leadStats->call_schedule ?? 0)
            - ($leadStats->visit_schedule ?? 0)
            - ($leadStats->visit_done ?? 0)
            - ($leadStats->booked ?? 0)
            - ($leadStats->completed ?? 0)
            - ($leadStats->cancelled ?? 0);
            $userType = Session::get('user_type');
            $isBaUser = $userType === 'ba';
            $currentUserId = Session::get('user_id');
    @endphp
    <style>
        .refresh-btn 
        {
            border-radius: 50%;
            width: 30px;
            height: 30px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.3s ease;
        }

        .refresh-btn:hover i 
        {
            transform: rotate(360deg);
        }
        .circle-btn 
        {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
        }

        .circle-btn i 
        {
            pointer-events: none;
        }

        .floating-calendar 
        {
            position: fixed;
            bottom: 105px;
            right: 75px;
            z-index: 1050;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            border: 1px solid #e0e0e0;
            display: none;
            height: 515px;
        }
        
        .floating-calendar-header 
        {
            background: #3762b8;
            color: white;
            padding: 15px;
            border-radius: 10px 10px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .floating-calendar-title 
        {
            font-weight: 600;
            font-size: 16px;
        }
        
        .floating-calendar-close 
        {
            background: none;
            border: none;
            color: white;
            font-size: 18px;
            cursor: pointer;
            padding: 0;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .floating-calendar-body 
        {
            padding: 15px;
        }
        
        .floating-calendar-events 
        {
            max-height: 300px;
            overflow-y: auto;
            margin-top: 15px;
        }
        
        .calendar-event-item 
        {
            padding: 10px;
            margin-bottom: 8px;
            background: #f8f9fa;
            border-left: 4px solid #4e54c8;
            border-radius: 4px;
            font-size: 14px;
        }
        
        .calendar-event-time 
        {
            font-weight: 600;
            color: #4e54c8;
            font-size: 12px;
        }
        
        .calendar-event-title
        {
            font-weight: 500;
            margin: 5px 0;
        }
        
        .calendar-event-type 
        {
            display: inline-block;
            padding: 2px 8px;
            background: #e9ecef;
            border-radius: 12px;
            font-size: 11px;
            color: #6c757d;
        }
        
        .floating-calendar-toggle 
        {
            position: fixed;
            bottom: 100px;
            right: 20px;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background:#3762b8;
            color: white;
            border: none;
            box-shadow: 0 4px 12px rgba(78, 84, 200, 0.4);
            cursor: pointer;
            z-index: 1049;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            transition: all 0.3s ease;
        }
        
        .floating-calendar-toggle:hover 
        {
            transform: scale(1.1);
            box-shadow: 0 6px 16px rgba(78, 84, 200, 0.5);
        }
        
        .ui-datepicker 
        {
            background: white !important;
            border: 1px solid #dee2e6 !important;
            border-radius: 8px !important;
            padding: 10px !important;
        }
        
        .ui-datepicker-header 
        {
            background: #f8f9fa !important;
            border: none !important;
            border-radius: 6px !important;
        }
        
        .ui-datepicker .ui-state-default 
        {
            border: none !important;
            background: white !important;
            color: #495057 !important;
            text-align: center !important;
            border-radius: 4px !important;
        }
        
        .ui-datepicker .ui-state-active 
        {
            background: #3762b8 !important;
            color: white !important;
        }
        
        .ui-datepicker .ui-state-hover 
        {
            background: #e9ecef !important;
        }
        
        .ui-datepicker-today .ui-state-default 
        {
            background: #e9ecef !important;
            font-weight: bold !important;
        }
        
        .no-events
        {
            text-align: center;
            color: #6c757d;
            padding: 20px;
            font-size: 14px;
        }
        .floating-calendar 
        {
            width: 450px;
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            border: 1px solid #e0e0e0;
        }
        .calendar-header 
        {
            color: #3762b8;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .calendar-header button 
        {
            background: rgba(255,255,255,0.2);
            border: none;
            color: #3762b8;
            width: 34px;
            height: 0px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            font-size: 14px;
        }

        .calendar-header button:hover 
        {
            background: rgba(255,255,255,0.3);
            transform: scale(1.1);
        }

        .calendar-header h5 
        {
            margin: 0;
            font-size: 16px;
            font-weight: 600;
        }

        .calendar-weekdays 
        {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            padding: 12px 10px;
            background: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
        }

        .calendar-weekdays div 
        {
            text-align: center;
            font-size: 13px;
            font-weight: 600;
            color: #495057;
        }

        .calendar-days 
        {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 4px;
            padding: 12px 10px;
            background: white;
        }

        .calendar-day 
        {
            aspect-ratio: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: white;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            color: #212529;
            transition: all 0.2s;
            position: relative;
            padding: 4px;
        }

        .calendar-day:not(.empty):hover 
        {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(55, 98, 184, 0.2);
            border-color: #3762b8;
            z-index: 10;
        }

        .calendar-day.empty 
        {
            background: #f8f9fa;
            border-color: #f1f3f5;
            cursor: default;
        }

        .calendar-day.today 
        {
            background: #3762b8;
            color: #ffffff;
            border-color: #3762b8;
            font-weight: 600;
            box-shadow: 0 2px 8px rgba(55, 98, 184, 0.3);
        }

        .calendar-day.has-events:not(.empty) 
        {
            background: #e8f0fe;
            border-color: #3762b8;
        }

        .calendar-day.has-missed:not(.empty) 
        {
            background: #fee8e8;
            border-color: #dc3545;
        }

        .event-count 
        {
            font-size: 10px;
            background: rgba(55, 98, 184, 0.15);
            padding: 2px 6px;
            border-radius: 12px;
            margin-top: 2px;
            font-weight: 600;
            color: #3762b8;
        }

        .calendar-day.has-missed .event-count 
        {
            background: rgba(220, 53, 69, 0.15);
            color: #dc3545;
        }

        .event-tooltip 
        {
            position: absolute;
            bottom: 110%;
            left: 50%;
            transform: translateX(-50%);
            background: white;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
            padding: 12px;
            min-width: 260px;
            max-width: 300px;
            z-index: 10000;
            margin-bottom: 8px;
            border: 1px solid #e0e0e0;
            pointer-events: none;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.2s, visibility 0.2s;
        }

        .event-tooltip::after 
        {
            content: '';
            position: absolute;
            top: 100%;
            left: 50%;
            transform: translateX(-50%);
            border-width: 8px;
            border-style: solid;
            border-color: white transparent transparent transparent;
        }

        .calendar-day:not(.empty):hover .event-tooltip 
        {
            opacity: 1;
            visibility: visible;
            transition-delay: 0.2s;
        }

        .tooltip-event-item 
        {
            padding: 10px;
            border-left: 4px solid;
            margin-bottom: 8px;
            background: #f8f9fa;
            border-radius: 8px;
            font-size: 12px;
            transition: all 0.2s;
        }

        .tooltip-event-item:last-child 
        {
            margin-bottom: 0;
        }

        .tooltip-event-item .event-title 
        {
            font-weight: 600;
            margin-bottom: 4px;
            font-size: 13px;
        }

        .tooltip-event-item .event-time 
        {
            font-size: 11px;
            color: #6c757d;
            margin-bottom: 4px;
        }

        .tooltip-event-item .event-time i 
        {
            margin-right: 4px;
        }

        .tooltip-event-item .event-status 
        {
            font-size: 10px;
            padding: 3px 8px;
            border-radius: 20px;
            display: inline-block;
            background: white;
            border: 1px solid;
        }
        #dayEventsList 
        {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 6px;
        }

        .day-event-item 
        {
            padding: 4px 6px; 
            border-left: 1.5px solid #3762b8; 
            background: #f8f9fa;
            font-size: 9px;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.15s;
            min-height: 50px;
        }

        .day-event-item:hover 
        {
            transform: translateX(2px);
            box-shadow: 0 1px 4px rgba(0,0,0,0.1);
        }

        .day-event-item .event-header 
        {
            display: flex;
            align-items: center;
            margin-bottom: 2px;
        }

        .day-event-item .event-header i 
        {
            font-size: 12px;
            margin-right: 4px;
        }

        .day-event-item .event-title 
        {
            font-weight: 600;
            font-size: 11px;
        }

        .day-event-item .event-details 
        {
            display: flex;
            flex-wrap: wrap;
            gap: 4px;
            font-size: 8px; 
            color: #6c757d;
            margin-bottom: 2px;
        }

        .day-event-item .event-details span 
        {
            display: flex;
            align-items: center;
        }

        .day-event-item .event-details i 
        {
            margin-right: 2px;
            font-size: 8px;
        }

        .day-event-item .event-badge 
        {
            display: inline-block;
            padding: 1px 4px; 
            border-radius: 10px;
            font-size: 8px;
            font-weight: 600;
        }

        .calendar-day.empty 
        {
            background: #f8f9fa !important;
            border-color: #f1f3f5 !important;
            color: #adb5bd !important;
        }

        .calendar-day.empty .event-count,
        .calendar-day.empty .event-tooltip 
        {
            display: none !important;
        }
        
    </style>
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">Dashboard</h4>
                        <div class="d-flex align-items-center flex-wrap gap-2">
                            <button type="button" class="btn btn-info btn-sm circle-btn" data-bs-toggle="modal" data-bs-target="#importModal" title="Bulk Import">
                                <i class="fas fa-file-import"></i>
                            </button>
                            <a class="btn btn-warning btn-sm circle-btn" href="{{ route('lead.add') }}" title="Add Manual Lead">
                                <i class="fas fa-plus"></i>
                            </a>
                            <button class="btn btn-success btn-sm circle-btn" data-bs-toggle="modal" data-bs-target="#quickLeadModal" title="Quick Add Lead">
                                <i class="fas fa-bolt"></i>
                            </button>
                            <a href="{{ route('settings.ratings') }}" class="btn btn-outline-warning btn-sm circle-btn" title="View Ratings">
                                <i class="fas fa-star"></i>
                            </a>
                            <input type="text" class="form-control form-control-sm date-range-filter" style="width: 180px;"
                                placeholder="Select date range"
                                value="{{ isset($dateRange['start'], $dateRange['end']) 
                                    ? $dateRange['start']->format('Y-m-d') . ' - ' . $dateRange['end']->format('Y-m-d') 
                                    : '' }}" title="Select Date Range">

                            <select class="form-control form-control-sm" id="agent-filter" style="width: 150px;" title="Select Agent">
                                <option value="">All Agents</option>
                                @foreach($agents as $agent)
                                    <option value="{{ $agent->id }}" {{ $selectedAgent == $agent->id ? 'selected' : '' }}>
                                        {{ $agent->name }}
                                    </option>
                                @endforeach
                            </select>
                            <button class="btn btn-primary btn-sm circle-btn" id="apply-filters" title="Apply Filters">
                                <i class="fas fa-filter"></i>
                            </button>
                            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm circle-btn" 
                            data-bs-toggle="tooltip" data-bs-placement="top" title="Clear Filter">
                                <i class="fas fa-sync-alt"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xl-4">
                    <div class="card overflow-hidden border-0 shadow-sm mb-3 pb-0">
                        <div class="bg-primary bg-soft">
                            <div class="row">
                                <div class="col-7">
                                    <div class="text-white p-3 pb-0">
                                        <h5 class="text-white mb-0">Welcome Back!</h5>
                                        <p>{{ session('user_name', 'Guest') }} Dashboard</p>
                                    </div>
                                </div>
                                <div class="col-5 align-self-end">
                                    <img src="{{ asset('images/profile-img.png') }}" alt="" class="img-fluid">
                                </div>
                            </div>
                        </div>
                        <div class="card-body pt-0 pb-3">
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="avatar-md profile-user-wid mb-4">
                                        <img src="{{ asset('images/avatar-1.jpg') }}" alt="" class="img-thumbnail rounded-circle">
                                    </div>
                                    <h5 class="font-size-15 text-truncate">{{ session('user_name', 'Guest') }}</h5>
                                    <p class="text-muted mb-0 text-truncate">
                                        {{ ucwords(str_replace('_', ' ', session('user_type', ''))) }}
                                    </p>
                                </div>
                                <div class="col-sm-8">
                                    <div class="pt-2">
                                        <div class="row">
                                            <div class="row">
                                                <div class="col-6">
                                                    <a href="{{ route('lead.all_lead') }}" class="text-decoration-none" style="cursor:pointer;">
                                                        <h5 class="font-size-15">{{ $leadStats->total_lead ?? 0 }}</h5>
                                                        <p class="text-muted mb-0">Total Leads</p>
                                                    </a>
                                                </div>
                                                <div class="col-6">
                                                    <a href="{{ route('lead.transfer_lead') }}" class="text-decoration-none" style="cursor:pointer;">
                                                        <h5 class="font-size-15">{{ $transferLeadCount ?? 0 }}</h5>
                                                        <p class="text-muted mb-0">Transferred Leads</p>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mt-2" style="line-height:34px;">
                                            <a href="{{route('setting.profile')}}" class="btn btn-primary waves-effect waves-light btn-sm">View Profile 
                                                <i class="mdi mdi-arrow-right ms-1"></i>
                                            </a>
                                            <br>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="py-0">   
                            <!-- <div class="card m-0 p-0">
                                <div class="card-header p-0 m-0">
                                    <h5 class="mb-2 mt-0 pt-0 pb-1">Others Lead {{ $totalOthersLead }}</h5>
                                </div>
                            </div>                           -->
                            <div class="row g-2">
                                @foreach($othersLeadData as $other)
                                    <div class="col-md-6 col-sm-6 col-12">
                                        <div class="card mini-stats-wid border-light shadow-sm mb-1">
                                            @if(!empty($other['route']))
                                                <a href="{{ route($other['route']) }}" class="text-decoration-none text-dark">
                                            @endif
                                            <div class="card-body py-2 px-3">
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <div class="flex-grow-1">
                                                        <p class="text-muted mb-1 small fw-medium">{{ $other['title'] ?? '' }}</p>
                                                        <h6 class="mb-0 fw-semibold">{{ $other['value'] ?? 0 }}</h6>
                                                    </div>
                                                    <div class="flex-shrink-0 ms-3">
                                                        <div class="avatar-xs rounded-circle bg-primary text-white d-flex align-items-center justify-content-center">
                                                            <i class="bx {{ $other['icon'] ?? 'bx-help-circle' }} font-size-16"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @if(!empty($other['route']))
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                @include('partial.lead-widget')
            </div>
            @include('partial.schedule')
            @include('partial.task-details')
        </div>
        @if($advertisement)
            @include('modals.advertisement')
        @endif
    </div>
    <div class="modal fade day-events-modal" id="dayEventsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-calendar-day me-2"></i>
                        <span id="dayEventsDate"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="dayEventsList">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        @if($advertisement)
            document.addEventListener("DOMContentLoaded", function() 
            {
                var adModal = new bootstrap.Modal(document.getElementById('advertisementModal'));
                adModal.show();
            });
        @endif
        $(document).ready(function() 
        {
            initMonthlyReportChart();
            initSourceAnalysisChart();
            initProjectAnalysisChart();
            initCampaignAnalysisChart();
            loadAllChartData();
            
            $('.date-range-filter').daterangepicker({
                opens: 'left',
                autoUpdateInput: false,
                locale: {
                    cancelLabel: 'Clear',
                    format: 'YYYY-MM-DD'
                }
            });
            
            $('.date-range-filter').on('apply.daterangepicker', function(ev, picker) 
            {
                $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
            });

            $('.date-range-filter').on('cancel.daterangepicker', function(ev, picker) 
            {
                $(this).val('');
            });
            
            $('#apply-filters').click(function() 
            {
                const dateRange = $('.date-range-filter').val();
                const year = $('#year-filter').val();
                const agentId = $('#agent-filter').val(); 
                let url = '{{ route("dashboard") }}';
                const params = new URLSearchParams();
                
                if (dateRange) 
                {
                    params.append('dateRange', dateRange);
                }
                
                if (year && year !== '{{ date("Y") }}') 
                {
                    params.append('year', year);
                }

                if (agentId) 
                {
                    params.append('agent_id', agentId);
                }

                if (params.toString()) 
                {
                    url += '?' + params.toString();
                }
                window.location.href = url;
            });

            $('#year-filter').change(function() 
            {
                loadAllChartData();
            });
            
            $('#export-analytics-png').click(function(e) 
            {
                e.preventDefault();
                exportChartAsPNG();
            });
            
            $('#export-analytics-csv').click(function(e) 
            {
                e.preventDefault();
                exportChartAsCSV();
            });

            
            function initMonthlyReportChart() 
            {
                const options = {
                    chart: {
                        height: 350,
                        type: 'bar',
                        toolbar: {
                            show: false
                        }
                    },
                    plotOptions: {
                        bar: {
                            borderRadius: 10,
                            dataLabels: {
                                position: 'top',
                            },
                        }
                    },
                    dataLabels: {
                        enabled: true,
                        formatter: function(val) {
                            return val;
                        },
                        offsetY: -20,
                        style: {
                            fontSize: '12px',
                            colors: ["#304758"]
                        }
                    },
                    colors: ['#3b76e1'],
                    series: [{
                        name: 'Leads',
                        data: []
                    }],
                    xaxis: {
                        categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                        position: 'bottom',
                        axisBorder: {
                            show: false
                        },
                        axisTicks: {
                            show: false
                        },
                        crosshairs: {
                            fill: {
                                type: 'gradient',
                                gradient: {
                                    colorFrom: '#D8E3F0',
                                    colorTo: '#BED1E6',
                                    stops: [0, 100],
                                    opacityFrom: 0.4,
                                    opacityTo: 0.5,
                                }
                            }
                        },
                        tooltip: {
                            enabled: true,
                        }
                    },
                    yaxis: {
                        axisBorder: {
                            show: false
                        },
                        axisTicks: {
                            show: false,
                        },
                        labels: {
                            show: true,
                            formatter: function(val) {
                                return val;
                            }
                        }
                    },
                    tooltip: {
                        enabled: true,
                        y: {
                            formatter: function(val) {
                                return val + " leads";
                            }
                        }
                    }
                };

                const chart = new ApexCharts(
                    document.querySelector("#monthly-analytics-chart"),
                    options
                );
                chart.render();
                window.monthlyReportChart = chart;
            }

            function initSourceAnalysisChart() 
            {
                const options = {
                    chart: {
                        type: 'pie',
                        height: 350
                    },
                    series: [],
                    labels: [],
                    colors: ['#3b76e1', '#63ad6f', '#f7cc53', '#f34e4e', '#564ab1', '#5fd0f3', '#ff7c00', '#b300ff', '#00ffaa', '#ff66b3'],
                    legend: {
                        position: 'bottom'
                    },
                    tooltip: {
                        enabled: true,
                        y: {
                            formatter: function(value) {
                                return value + " leads";
                            }
                        }
                    },
                    responsive: [{
                        breakpoint: 480,
                        options: {
                            chart: {
                                width: 200
                            },
                            legend: {
                                position: 'bottom'
                            }
                        }
                        }]
                };

                const chart = new ApexCharts(
                    document.querySelector("#source-analytics-chart"),
                    options
                );
                chart.render();
                window.sourceAnalysisChart = chart;
            }

            function initProjectAnalysisChart() 
            {
                const options = {
                    chart: {
                        type: 'donut',
                        height: 350
                    },
                    series: [],
                    labels: [],
                    colors: ['#3b76e1', '#63ad6f', '#f7cc53', '#f34e4e', '#564ab1', '#5fd0f3', '#ff7c00', '#b300ff', '#00ffaa', '#ff66b3'],
                    legend: {
                        position: 'bottom'
                        },
                    plotOptions: {
                        pie: {
                            donut: {
                                labels: {
                                    show: true,
                                    total: {
                                        show: true,
                                        label: 'Total Leads',
                                        formatter: function(w) 
                                        {
                                            return w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                        }
                                    }
                                }
                            }
                        }
                    },
                    tooltip: {
                        enabled: true,
                        y: {
                            formatter: function(value) 
                            {
                                return value + " leads";
                            }
                        }
                    }
                };

                const chart = new ApexCharts(
                    document.querySelector("#project-analytics-chart"),
                    options
                );
                chart.render();
                window.projectAnalysisChart = chart;
            }

            function initCampaignAnalysisChart() 
            {
                const options = {
                    chart: {
                        type: 'radialBar',
                        height: 350
                    },
                    series: [],
                    labels: [],
                    colors: ['#3b76e1', '#63ad6f', '#f7cc53', '#f34e4e', '#564ab1', '#5fd0f3', '#ff7c00', '#b300ff', '#00ffaa', '#ff66b3'],
                    legend: {
                        position: 'bottom'
                        },
                    plotOptions: {
                        pie: {
                            donut: {
                                labels: {
                                    show: true,
                                    total: {
                                        show: true,
                                        label: 'Total Leads',
                                        formatter: function(w) 
                                        {
                                            return w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                        }
                                    }
                                }
                            }
                        }
                    },
                    tooltip: {
                        enabled: true,
                        y: {
                            formatter: function(value) 
                            {
                                return value + " leads";
                            }
                        }
                    }
                };

                const chart = new ApexCharts(
                    document.querySelector("#campaign-analytics-chart"),
                    options
                );
                chart.render();
                window.campaignAnalysisChart = chart;
            }
            
            function loadAllChartData() 
            {
                const dateRange = $('.date-range-filter').val();
                const year = $('#year-filter').val();
                const filters = {
                    dateRange: dateRange,
                    year: year
                };

                $.ajax({
                    url: '{{ route("dashboard.analytics") }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        filters: filters
                    },
                    success: function(response) 
                    {
                        // console.log(response)
                        if (response.monthlyTrendData) 
                        {
                            window.monthlyReportChart.updateOptions({
                                series: [{
                                    name: 'Leads',
                                    data: response.monthlyTrendData.values
                                }]
                            });
                        }
                        if (response.sourceData) 
                        {
                            window.sourceAnalysisChart.updateOptions({
                                series: response.sourceData.values,
                                labels: response.sourceData.labels
                            });
                        }
                        if (response.projectData) 
                        {
                            window.projectAnalysisChart.updateOptions({
                                series: response.projectData.values,
                                labels: response.projectData.labels
                            });
                        }
                        if (response.campaignData) 
                        {
                            window.campaignAnalysisChart.updateOptions({
                                series: response.campaignData.values,
                                labels: response.campaignData.labels
                            });
                        }
                    },
                    error: function(xhr, status, error) 
                    {
                        console.error('Error fetching chart data:', error);
                    }
                });
            }

            function exportChartAsPNG() 
            {
                const activeTab = $('.nav-tabs .nav-link.active').attr('href');
                let chart;

                switch(activeTab) 
                {
                    case '#monthly-report':
                        chart = window.monthlyReportChart;
                        break;
                    case '#source-analysis':
                        chart = window.sourceAnalysisChart;
                        break;
                    case '#project-analysis':
                        chart = window.projectAnalysisChart;
                        break;
                    case '#campaign-analysis':
                        chart = window.campaignAnalysisChart;
                        break;
                }

                if (chart) 
                {
                    chart.dataURI().then(({ imgURI }) => {
                        const link = document.createElement('a');
                        link.download = 'lead-analytics-' + activeTab.replace('#', '') + '.png';
                        link.href = imgURI;
                        link.click();
                    });
                }
            }

            function exportChartAsCSV() 
            {
                const activeTab = $('.nav-tabs .nav-link.active').attr('href');
                $.ajax({
                    url: '{{ route("dashboard.export.analytics") }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        type: activeTab.replace('#', ''),
                        filters: getCurrentFilters()
                    },
                    success: function(response) 
                    {
                        const link = document.createElement('a');
                        link.download = 'lead-analytics-' + activeTab.replace('#', '') + '.csv';
                        link.href = 'data:text/csv;charset=utf-8,' + encodeURIComponent(response);
                        link.click();
                    },
                    error: function(xhr, status, error) 
                    {
                        console.error('Error exporting CSV:', error);
                    }
                });
            }

            function getCurrentFilters() 
            {
                return {
                    dateRange: $('.date-range-filter').val(),
                    year: $('#year-filter').val()
                };
            }
        });
    </script>
@endsection