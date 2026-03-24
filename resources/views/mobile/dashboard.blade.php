@extends('mobile.layouts.app')

@section('title', 'Mobile Dashboard | Pro-leadexpertz')

@section('content')
    <div class="dashboard">
        <header class="header">
            <div class="notification shadow">
                <span class="footer-nav-item notification-item position-absolute" style="top:25px !important; right:127px !important; z-index:999999 !important; cursor: default;">
                    <i class="fa-solid fa-bell position-relative">
                        <span class="notification-badge badge">0</span>
                    </i>
                </span>
            </div>
            <div class="app-logo shadow">
                <img src="{{ asset(Session::get('logo')) }}" alt="Enterprise Portal Logo" width="56" height="100"
                    onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                <i class="fas fa-briefcase" style="display:none;"></i>
            </div>

            <div class="d-flex align-items-center justify-content-center gap-2" id="locationLoader">
                <div class="spinner-border" role="status"></div>
                <span>Fetching location...</span>
            </div>

            <div class="toggle-container d-none" id="attendanceToggleContainer">
                @php
                    $hasActiveAttendance = DB::table('attendance')
                        ->where('user_id', session('user_id'))
                        ->whereNull('end_time')
                        ->exists();
                @endphp
                <input type="checkbox" id="attendanceToggle" class="attendance-toggle-checkbox" {{ $hasActiveAttendance ? 'checked' : '' }}>
                <label for="attendanceToggle" class="toggle-label">
                    <span class="toggle-text">Start</span>
                    <span class="toggle-text">End</span>
                    <span class="toggle-handle"></span>
                </label>
                <input type="hidden" id="latitude" name="latitude">
                <input type="hidden" id="longitude" name="longitude">
            </div>

            <h4>Sales Dashboard</h4>
            <p>Welcome, {{ session('user_name') ?? 'User' }}!</p>
        </header>

        <div class="stats-container">
            <div class="stat" role="region" aria-label="Total leads">
                <a href="{{route('mobile.all-leads')}}">
                    <p>Total Leads</p>
                    <h3>{{ number_format($totalLeads ?? 0, 2) }}</h3>
                </a>
            </div>
            <div class="stat" role="region" aria-label="Total tasks">
                <a href="{{route('mobile.tasks')}}">
                    <p>Total Tasks</p>
                    <h3>{{ number_format($taskStats->total_task ?? 0, 0) }}</h3>
                </a>
            </div>
        </div>

        <div class="section-title d-flex gap-2">
            <h5>Lead Management</h5>
            <a class="cust-lead-item" href="{{route('mobile.leads.create')}}" style="padding-left: 5rem !important;">
                <p class="mt-3">
                    <i class="fa-solid fa-circle-plus"></i> Add Lead
                </p>
            </a>
        </div>
       @include('mobile/partials/lead-management')
    </div>

    <div class="fab" role="button" onclick="openBottomSheet()">
        <a href="#" class="text-decoration-none text-light">
            <i class="fa fa-plus"></i>
        </a>
    </div>

    <div class="bottom-sheet-form" id="leadSheet">
        <div class="sheet-header">
            <div class="handle"></div>
            <h5>Add Quick Lead</h5>
            <button type="button" class="btn-close float-end" onclick="closeBottomSheet()"></button>
        </div>
        <form method="POST" action="{{route('mobile.quick-leads')}}" class="p-3">
            @csrf
            <input type="hidden" name="action" value="quick_lead">
            <div class="form floating-form mb-3">
                <input type="name" name="name" autocomplete="off" required placeholder=" " />
                <label class="label-name">
                    <span class="content">Name</span>
                </label>
            </div>

            <div class="form floating-form mb-3">
                <input type="phone" name="phone" autocomplete="off" required placeholder=" " />
                <label class="label-name">
                    <span class="content">Phone Number</span>
                </label>
            </div>

            <div class="d-flex justify-content-end">
                <button type="button" class="btn btn-outline-light me-2" onclick="closeBottomSheet()">Close</button>
                <button type="submit" class="btn btn-primary" id="SubmitBtn">
                    <span id="SubmitText">Add Lead</span>
                    <span id="SubmitSpinner" class="d-none">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Please wait...
                    </span>
                </button>
            </div>
        </form>
    </div>
@endsection
