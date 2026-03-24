@php
    $userId = session('user_id');
    $notifications = DB::table('user_notification as a')
        ->select(
            'a.*',
            DB::raw("
                CASE
                    WHEN TIMESTAMPDIFF(DAY, a.CreatedDate, NOW()) >= 1 
                        THEN CONCAT(TIMESTAMPDIFF(DAY, a.CreatedDate, NOW()), ' Days')
                    WHEN TIMESTAMPDIFF(MINUTE, a.CreatedDate, NOW()) >= 60 
                        THEN CONCAT(ROUND(TIMESTAMPDIFF(MINUTE, a.CreatedDate, NOW()) / 60, 0), ' Hours')
                    ELSE CONCAT(TIMESTAMPDIFF(MINUTE, a.CreatedDate, NOW()), ' MiN')
                END as time_diff
            ")
        )
        ->where('a.ack', 0)
        ->where('a.UserId', $userId)
        ->orderByDesc('a.CreatedDate')
        ->get();

    $activeFeatures = session('active_features', []);
    $softwareType = session('software_type', 'real_state');

    $softwareTypeAccess = [
        'real_state' => ['project_detail_page', 'search', 'attendance', 'settings', 'premium_features', 'notifications', 'integrations'],
        'lead_management' => ['project_detail_page', 'search', 'attendance', 'settings', 'notifications', 'integrations'],
        'task_management' => ['settings', 'notifications'],
        'mis_management' => ['settings', 'notifications']
    ];

    $currentAccess = $softwareTypeAccess[$softwareType] ?? $softwareTypeAccess['real_state'];
@endphp

<header id="page-topbar">
    <div class="navbar-header">
        <div class="d-flex">
            <div class="navbar-brand-box">
                <a href="index.html" class="logo logo-dark">
                    <span class="logo-sm">
                        <img src="{{ asset(Session::get('logo')) }}" alt="Logo" height="22">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ asset(Session::get('logo')) }}" alt="Logo" height="17">
                    </span>
                </a>
                <a href="{{ $softwareType === 'task_management' ? route('task.list') : ($softwareType === 'mis_management' ? route('mis.summary-report') : route('dashboard')) }}" class="logo logo-light">
                    <span class="logo-sm">
                        <img src="{{ asset(Session::get('logo')) }}" alt="" height="22" width="45">
                    </span>
                    <span class="logo-lg">
                        <img src="{{ asset(Session::get('logo')) }}" alt="" height="50" width="150">
                    </span>
                </a>
            </div>

            <button type="button" class="btn btn-sm px-3 font-size-16 header-item waves-effect" id="vertical-menu-btn">
                <i class="fa fa-fw fa-bars"></i>
            </button>

            @if(in_array('search', $currentAccess))
            <form class="app-search d-none d-lg-block" action="{{ route('search') }}" method="GET">
                <div class="position-relative">
                    <input type="text" name="q" class="form-control" placeholder="Search Leads..." value="{{ request('q') }}">
                    <span class="bx bx-search-alt"></span>
                </div>
            </form>
            @endif
        </div>

        <div class="d-flex">
            @if(in_array('project_detail_page', $currentAccess) && in_array('project_detail_page', $activeFeatures))
            <div class="d-inline-block ms-2">
                <a href="{{route('project-details.index')}}" class="btn header-item noti-icon waves-effect d-flex align-items-center">
                    <i class="fa-regular fa-building me-2"></i>
                </a>
            </div>
            @endif

            @if(in_array('attendance', $currentAccess))
            <div class="attendance-toggle-wrapper mt-3">
                <div class="attendance-toggle-container">
                    <input type="checkbox" id="attendanceToggle" class="attendance-toggle-checkbox" 
                    {{ session('attendance_active') ? 'checked' : '' }}>
                    <label for="attendanceToggle" class="attendance-toggle-label">
                        <span class="attendance-toggle-track">
                            <span class="attendance-toggle-handle">
                                <span class="attendance-icon">
                                    <i class="fas fa-business-time"></i>
                                </span>
                            </span>
                            <span class="attendance-status-text">
                                <span class="status-on">Active</span>
                                <span class="status-off">Inactive</span>
                            </span>
                        </span>
                    </label>
                </div>
            </div>
            @endif

            @if(in_array('search', $currentAccess))
            <div class="dropdown d-inline-block d-lg-none ms-2">
                <button type="button" class="btn header-item noti-icon waves-effect" id="page-header-search-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="mdi mdi-magnify"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0" aria-labelledby="page-header-search-dropdown">
                    <form class="p-3">
                        <div class="form-group m-0">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Search ..." aria-label="Recipient's username">
                                <div class="input-group-append">
                                    <button class="btn btn-primary" type="submit"><i class="mdi mdi-magnify"></i></button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            @endif

            @if(in_array('settings', $currentAccess))
            <div class="dropdown d-none d-lg-inline-block ms-1">
                <button type="button" 
                    class="btn header-item noti-icon waves-effect" 
                    data-bs-toggle="dropdown" 
                    aria-haspopup="true" 
                    aria-expanded="false"
                    data-bs-toggle="tooltip"
                    data-bs-placement="bottom"
                    title="Settings & Tools">
                    <i class="bx bx-cog"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                    <div class="px-lg-2">
                        <div class="dropdown-header py-2">
                            <h6 class="mb-0">Settings & Tools</h6>
                        </div>
                        <div class="row g-0">
                            <div class="col text-center">
                                <a class="dropdown-icon-item d-block p-3" href="{{ route('setting.logo') }}">
                                    <div class="icon-wrapper bg-light rounded-circle mx-auto mb-2">
                                        <i class="bx bx-image-alt text-primary fs-4"></i>
                                    </div>
                                    <span class="d-block small">Logo</span>
                                </a>
                            </div>
                            <div class="col text-center">
                                <a class="dropdown-icon-item d-block p-3" href="{{route('setting.login_log')}}">
                                    <div class="icon-wrapper bg-light rounded-circle mx-auto mb-2">
                                        <i class="bx bx-history text-success fs-4"></i>
                                    </div>
                                    <span class="d-block small">Login Log</span>
                                </a>
                            </div>
                            @if(in_array('integrations', $currentAccess))
                            <div class="col text-center">
                                <a class="dropdown-icon-item d-block p-3" href="{{route('integrations.index')}}">
                                    <div class="icon-wrapper bg-light rounded-circle mx-auto mb-2">
                                        <i class="bx bx-plug text-info fs-4"></i>
                                    </div>
                                    <span class="d-block small">Integrations</span>
                                </a>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <div class="dropdown d-none d-lg-inline-block ms-1">
                <button type="button" class="btn header-item noti-icon waves-effect" data-bs-toggle="fullscreen">
                    <i class="bx bx-fullscreen"></i>
                </button>
            </div>

            @php
                $allFeatures = DB::table('software_features')->pluck('feature_name')->toArray();
            @endphp
            <div class="dropdown d-inline-block">
                <a href="{{route('premium.features')}}" class="btn header-item noti-icon waves-effect premium-btn d-flex align-items-center">
                    <i class="fas fa-crown me-2 text-warning"></i> Premium Features
                    <span class="badge bg-warning ms-1 premium-badge">New</span>
                </a>
            </div>

            @if(in_array('notifications', $currentAccess))
            <div class="dropdown d-inline-block">
                <button type="button" class="btn header-item noti-icon waves-effect" id="page-header-notifications-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="bx bx-bell bx-tada"></i>
                    <span class="badge bg-danger rounded-pill">{{ $notifications->count() }}</span>
                </button>

                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0" aria-labelledby="page-header-notifications-dropdown">
                    <div class="p-3">
                        <div class="row align-items-center">
                            <div class="col">
                                <h6 class="m-0">Notifications</h6>
                            </div>
                            <div class="col-auto">
                                <a href="{{ route('notifications.index') }}" class="small">View All</a>
                            </div>
                        </div>
                    </div>

                    <div data-simplebar style="max-height: 230px;">
                        @forelse($notifications as $noti)
                            <a href="{{ route('notifications.index') }}" class="dropdown-item d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-envelope me-2"></i>
                                    <span class="text-wrap" style="max-width: 200px;">{{ $noti->message }}</span>
                                </div>
                                <small class="text-muted">{{ $noti->time_diff }}</small>
                            </a>
                            <div class="dropdown-divider"></div>
                        @empty
                            <div class="text-center p-3 text-muted">
                                No notifications
                            </div>
                        @endforelse
                    </div>

                    <div class="p-2 border-top d-grid">
                        <a class="btn btn-sm btn-link text-center" href="{{ route('notifications.index') }}">
                            <i class="mdi mdi-arrow-right-circle me-1"></i> View All..
                        </a>
                    </div>
                </div>
            </div>
            @endif
            
            <div class="dropdown d-inline-block">
                <button type="button" class="btn header-item waves-effect" id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <img class="rounded-circle header-profile-user" src="{{asset('images/avatar-1.jpg')}}" alt="Header Avatar">
                    <span class="d-none d-xl-inline-block ms-1" key="t-henry">{{ session('user_name', 'Guest') }}</span>
                    <i class="mdi mdi-chevron-down d-none d-xl-inline-block"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end">
                    <a class="dropdown-item" href="{{route('setting.profile')}}">
                        <i class="bx bx-user font-size-16 align-middle me-1"></i> <span key="t-profile">Profile</span>
                    </a>
                    @if(in_array('settings', $currentAccess))
                    <a class="dropdown-item" href="{{ route('support.index') }}">
                        <i class="bx bx-support font-size-16 align-middle me-1"></i> <span>Support Tickets</span>
                    </a>
                    @endif
                    @php
                        $softwareInfo = Session::get('software_info');
                    @endphp

                    @if(!empty($softwareInfo?->apk))
                        <div class="dropdown-item position-relative">
                            <div class="d-flex align-items-center justify-content-between">
                                <a class="d-flex align-items-center text-decoration-none text-dark flex-grow-1"
                                href="{{ asset($softwareInfo->apk) }}"
                                download>
                                    <i class="bx bxl-android font-size-18 text-success me-2"></i>
                                    <div>
                                        <small class="text-muted d-block">Download App</small>
                                        <span class="fw-bold">(.apk)</span>
                                    </div>
                                </a>

                                <button type="button"
                                    class="btn btn-sm btn-link text-muted p-0 ms-2"
                                    data-bs-toggle="modal"
                                    data-bs-target="#installGuideModal"
                                    title="Installation Instructions">
                                    <i class="fas fa-info-circle"></i>
                                </button>
                            </div>
                        </div>
                    @else
                        <div class="dropdown-item text-muted small">
                            <i class="bx bx-block me-2"></i>
                            No APK available
                        </div>
                    @endif

                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item text-danger" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" style="cursor:pointer;">
                        <i class="bx bx-power-off font-size-16 align-middle me-1 text-danger"></i> <span key="t-logout">Logout</span>
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>


<div class="modal fade" id="installGuideModal" tabindex="-1" aria-labelledby="installGuideModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white rounded-top-3">
                <div class="d-flex align-items-center w-100">
                    <i class="fas fa-download fa-lg me-3"></i>
                    <div class="flex-grow-1">
                        <h5 class="modal-title mb-0 fw-bold" id="installGuideModalLabel">Download Mobile App</h5>
                        <p class="small mb-0 opacity-75 mt-1">Install our app on your mobile device</p>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body p-4">
                <div class="row g-4">
                    <div class="col-lg-6">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-header bg-success text-white d-flex align-items-center py-3">
                                <i class="fab fa-android fa-fw me-2 fs-5"></i>
                                <h6 class="mb-0 fw-semibold">For Android Devices</h6>
                            </div>
                            <div class="card-body d-flex flex-column p-4">
                                <div class="alert alert-warning border-warning mb-4">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <strong>Note:</strong> Enable "Install from Unknown Sources"
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-start mb-4">
                                        <span class="badge bg-success rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">1</span>
                                        <div>
                                            <h6 class="fw-semibold mb-1">Download APK File</h6>
                                            <p class="text-muted mb-0">Use the download button below to get the APK file.</p>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-start mb-4">
                                        <span class="badge bg-success rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">2</span>
                                        <div>
                                            <h6 class="fw-semibold mb-1">Enable Installation</h6>
                                            <p class="text-muted mb-0">Allow installation from unknown sources in Settings.</p>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-start">
                                        <span class="badge bg-success rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">3</span>
                                        <div>
                                            <h6 class="fw-semibold mb-1">Install & Use</h6>
                                            <p class="text-muted mb-0">Open the APK, tap "Install", then launch the app.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-header bg-primary text-white d-flex align-items-center py-3">
                                <i class="fab fa-apple fa-fw me-2 fs-5"></i>
                                <h6 class="mb-0 fw-semibold text-light">For iOS Devices</h6>
                            </div>
                            <div class="card-body d-flex flex-column p-4">
                                <div class="alert alert-warning border-warning mb-4">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <strong>Open the CRM link:</strong> Open the provided CRM URL in Safari or Chrome on your iPhone(https://realestatecrm.clikzopdemo.com/mobile/login).
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-start mb-4">
                                        <span class="badge bg-primary rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">1</span>
                                        <div>
                                            <h6 class="fw-semibold mb-1">Open the browser menu</h6>
                                            <p class="text-muted mb-0">In Safari, tap the Share icon (square with upward arrow).</p>
                                            <p class="text-muted mb-0">In Chrome, tap the Share icon (square with upward arrow) in the top-right corner.</p>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-start mb-4">
                                        <span class="badge bg-primary rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">2</span>
                                        <div>
                                            <h6 class="fw-semibold mb-1">Select “Add to Home Screen”</h6>
                                            <p class="text-muted mb-0">Scroll and tap Add to Home Screen.</p>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-start">
                                        <span class="badge bg-primary rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">3</span>
                                        <div>
                                            <h6 class="fw-semibold mb-1">Name your App</h6>
                                            <p class="text-muted mb-0">Enter your preferred name (e.g., Real Estate CRM).</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-center mt-4 pt-4 border-top">
                    <div class="d-inline-block">
                        <a href="{{ asset('realstate.apk') }}" class="btn btn-primary btn-lg px-5 py-3" download>
                            <i class="fas fa-download me-2"></i>Download APK for Android Platforms
                            <div class="fw-normal small mt-2">
                                <!--<i class="fab fa-android me-1"></i> Android & 
                                <i class="fab fa-apple ms-2 me-1"></i> iOS Compatible-->
                            </div>
                        </a>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light rounded-bottom-3">
                <div class="w-100 d-flex justify-content-center">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
