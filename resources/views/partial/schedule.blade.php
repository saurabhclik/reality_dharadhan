<style>
    h6 {
        color: #4e4d4dff;
        margin: 0;
    }

    .nav-tabs .nav-link.active h6 {
        color: #CF5D3B !important;
    }

    .no-data {
        text-align: center;
        font-style: italic;
        color: #999;
    }
    .add-project-btn 
    {
        padding: 2px 6px;
        font-size: 0.7rem;
        border: 1px solid #0d6efd;
    }

    .add-project-btn:hover 
    {
        background-color: #0d6efd;
        color: white;
    }

    #currentProjects .badge 
    {
        font-size: 0.75rem;
        padding: 4px 8px;
    }
    .cust-badge {
        white-space: normal;
        padding: 6px 10px;
        font-size: 0.9rem;
        line-height: 1.4;
    }
    
    .dataTables_scroll {
        overflow: auto;
    }

    .dataTables_scrollHead {
        position: sticky;
        top: 0;
        z-index: 10;
        background: white;
    }

    .dataTables_scrollBody {
        max-height:100% !important;
    }

    #table_filter {
        margin:10px;
    }

    .applicant-modal .modal-dialog {
        max-width: 500px;
    }
    
    .applicant-modal .modal-header {
        background: #4b6cb7;
        color: white;
        border-bottom: none;
        border-radius: 5px 5px 0 0;
    }
    
    .applicant-modal .modal-title {
        font-weight: 600;
        font-size: 1.2rem;
    }
    
    .applicant-modal .modal-body {
        padding: 20px;
    }
    
    .applicant-detail {
        display: flex;
        margin-bottom: 12px;
        align-items: flex-start;
    }
    
    .applicant-label {
        font-weight: 600;
        color: #495057;
        width: 140px;
        flex-shrink: 0;
    }
    
    .applicant-value {
        color: #6c757d;
        flex-grow: 1;
        word-break: break-word;
    }
    
    .applicant-section {
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 1px solid #eee;
    }
    
    .applicant-section:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }
    
    .applicant-section-title {
        font-weight: 600;
        color: #4b6cb7;
        margin-bottom: 15px;
        font-size: 1.1rem;
        padding-bottom: 5px;
        border-bottom: 1px solid #e9ecef;
    }
    
    .eye-btn {
        color: #17a2b8;
        transition: all 0.3s;
    }
    
    .eye-btn:hover {
        color: #138496;
        transform: scale(1.1);
    }
    .duplicate-item {
        color: #fd7e14;
    }

    .share-item 
    {
        color: #20c997;
    }

    .pin-item 
    {
        color: #ffc107;
        cursor: pointer;
        transition: all 0.3s;
    }

    .pin-item.pinned 
    {
        color: #fd7e14;
    }

    .pin-item:hover 
    {
        transform: scale(1.1);
    }

    .pinned-badge 
    {
        background: linear-gradient(45deg, #fd7e14, #ffc107);
        color: white;
        font-size: 0.7rem;
        padding: 2px 6px;
        border-radius: 10px;
        margin-left: 5px;
    }
    .visited-history-table 
    {
        font-size: 0.875rem;
    }

    .visit-status-badge 
    {
        font-size: 0.75rem;
        padding: 4px 8px;
    }

    .no-visits-message 
    {
        text-align: center;
        padding: 40px 20px;
        color: #6c757d;
    }

    .no-visits-message i 
    {
        font-size: 3rem;
        margin-bottom: 15px;
        opacity: 0.5;
    }
</style>

@include('modals.view-comments')

<div class="col-xl-12">
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom-0 py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Activities</h4>
            </div>
        </div>
        <div class="card-body p-4">
            <ul class="nav nav-tabs nav-tabs-custom mb-4" id="followupTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" data-bs-target="#today" style="cursor:pointer;">
                        Today
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" data-bs-target="#tomorrow" style="cursor:pointer;">
                        Tomorrow
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" data-bs-target="#missed" style="cursor:pointer;">
                        Missed
                    </a>
                </li>
            </ul>

            <div class="tab-content" id="followupTabsContent">

                {{-- ================= Today ================= --}}
                <div class="tab-pane fade show active" id="today" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="today-table">
                            <thead class="table-light">
                                <tr>
                                    <th>Type</th>
                                    <th>Lead Details</th>
                                    <th>Agent</th>
                                    <th>Source</th>
                                    <th>Campaign</th>
                                    <th>Date & Time</th>
                                    <th>Status</th>
                                    <th>Classification</th>
                                    <th>Remind Date</th>
                                    <th>Remind Time</th>
                                    <th>Last Comment</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(['todayCalls','todayVisits','interestedToday','todayWhatsapp','todayMeetings'] as $key)
                                    @if(isset($followups[$key]) && $followups[$key]->isNotEmpty())
                                        @foreach($followups[$key] as $item)
                                            @php
                                                switch($key) {
                                                    case 'todayCalls': 
                                                        $type='Call'; $icon='mdi mdi-phone-outline'; $badgeColor='primary'; break;
                                                    case 'todayVisits': 
                                                        $type='Visit'; $icon='mdi mdi-map-marker-outline'; $badgeColor='success'; break;
                                                   case 'todayMeetings':
                                                        $type = $item->type; 
                                                        switch ($item->type) {
                                                            case 'Processing':
                                                                $icon = 'mdi mdi-progress-clock';
                                                                $badgeColor = 'warning';
                                                                break;

                                                            case 'Visit Done':
                                                                $icon = 'mdi mdi-check-circle-outline';
                                                                $badgeColor = 'success';
                                                                break;

                                                            case 'Not Picked':
                                                                $icon = 'mdi mdi-phone-missed';
                                                                $badgeColor = 'danger';
                                                                break;

                                                            case 'Future Lead':
                                                                $icon = 'mdi mdi-calendar-clock';
                                                                $badgeColor = 'info';
                                                                break;

                                                            default:
                                                                $icon = 'mdi mdi-account-group-outline';
                                                                $badgeColor = 'primary';
                                                        }
                                                        break;
                                                    case 'missedFollowups': 
                                                        $type='Missed'; $icon='mdi mdi-alert-circle-outline'; $badgeColor='danger'; break;
                                                    case 'interestedLeads': 
                                                        $type='Interested'; $icon='mdi mdi-thumb-up-outline'; $badgeColor='info'; break;
                                                    case 'todayWhatsapp': 
                                                        $type='WhatsApp'; $icon='fab fa-whatsapp'; $badgeColor='success'; break;
                                                    default: 
                                                        $type=''; $icon=''; $badgeColor='secondary';
                                                }
                                                $comment = strip_tags($item->last_comment ?? '');
                                                $short = \Illuminate\Support\Str::limit($comment, 30);
                                                $createdDate = \Carbon\Carbon::parse($item->created_at);
                                                $currentDate = \Carbon\Carbon::now();
                                                $diff_date_count = $createdDate->diffInDays($currentDate);
                                            @endphp
                                            <tr>
                                                {{-- Type --}}
                                                <td>
                                                    <span class="badge bg-{{ $badgeColor }} bg-soft text-light">
                                                        <i class="{{ $icon }}"></i> {{ $type }}
                                                    </span>
                                                    <div class="d-flex gap-2 align-items-center">
                                                        <a href="tel:{{ $item->phone }}" class="btn btn-xs btn-soft-light" title="Call">
                                                            <i class="fas fa-phone text-primary"></i>
                                                        </a>
                                                        <a href="https://wa.me/91{{ $item->whatsapp_no ?? $item->phone }}" target="_blank" class="btn btn-xs btn-soft-light" title="WhatsApp">
                                                            <i class="fab fa-whatsapp text-success"></i>
                                                        </a>      
                                                        <div class="action-item pin-item {{ $item->is_pinned ? 'pinned' : '' }}" 
                                                            onclick="togglePin({{ $item->id }}, {{ $item->is_pinned ? 0 : 1 }})" 
                                                            data-bs-toggle="tooltip" 
                                                            title="{{ $item->is_pinned ? 'Unpin Lead' : 'Pin Lead' }}" 
                                                            style="cursor:pointer;">
                                                            <i class="fas fa-thumbtack"></i>
                                                        </div>
                                                        <div class="position-relative d-inline-block text-center">
                                                        @if($diff_date_count > 5)
                                                        <span 
                                                            class="position-absolute bottom-100 start-50 translate-middle badge rounded-pill bg-danger text-light shadow-sm cursor-pointer" 
                                                            style="font-size: 0.65rem; padding: 0.25em 0.5em;"
                                                            data-bs-toggle="tooltip" 
                                                            data-bs-placement="top" 
                                                            title="Lead created on {{ $createdDate->format('Y-m-d H:i') }}, {{ $diff_date_count }} days ago">
                                                            👋🏻 {{ $diff_date_count }} days
                                                        </span>
                                                        @endif
                                                        <span>
                                                            {{ $item->id }}
                                                        </span>
                                                        @if($item->is_pinned)
                                                            <span class="pinned-badge">Pinned</span>
                                                        @endif
                                                        
                                                        @if($item->visited_on == 1)
                                                            <span class="rounded-pill px-3 py-1 bg-light border text-success fw-semibold small">
                                                                <i class="fas fa-user-check me-1"></i> Visited
                                                            </span>
                                                        @endif
                                                    </div>
                                                </td>

                                                {{-- Lead Details --}}
                                                <td>
                                                    <div class="d-flex flex-column">
                                                        <h6 class="mb-0">{{ $item->name }}</h6>
                                                        <span class="text-muted small">{{ $item->phone }}</span>
                                                    </div>
                                                    <div class="d-flex">
                                                        <button class="btn btn-xs btn-soft-light" 
                                                            onclick="showStatusUpdateModal({{ $item->id }}, '{{ $item->status }}')"
                                                            data-bs-toggle="tooltip" title="Update Status">
                                                            <i class="fas fa-sync-alt text-info"></i>
                                                        </button>
                                                        <a href="{{ route('lead.edit', $item->id) }}" class="btn btn-xs btn-soft-light" data-bs-toggle="tooltip" title="Edit">
                                                            <i class="fas fa-edit text-warning"></i>
                                                        </a>
                                                    </div>
                                                </td>

                                                <td>{{ $item->agent_name }}</td>
                                                <td>{{ $item->source }}</td>
                                                <td>{{ $item->campaign }}</td>
                                                <td>{{ \Carbon\Carbon::parse($item->datetime)->format('M d, Y h:i A') }}</td>

                                                {{-- Status Badge --}}
                                                <td>
                                                    @if($type == 'Missed')
                                                        <span class="badge bg-danger bg-opacity-10 text-danger">Scheduled</span>
                                                    @elseif($type == 'Call')
                                                        <span class="badge bg-primary bg-opacity-10 text-light">Call</span>
                                                    @elseif($type == 'Visit')
                                                        <span class="badge bg-success bg-opacity-10 text-success">Visit</span>
                                                    @elseif($type == 'Interested')
                                                        <span class="badge bg-info bg-opacity-10 text-info">Interested</span>
                                                    @elseif($type == 'WhatsApp')
                                                        <span class="badge bg-success bg-opacity-10 text-success">WhatsApp</span>
                                                    @else
                                                        <span class="badge bg-secondary bg-opacity-10 text-muted">Scheduled</span>
                                                    @endif
                                                </td>

                                                <td>{{ $item->classification ?? '-' }}</td>
                                                <td>{{ \Carbon\Carbon::parse($item->remind_date)->format('d M Y') }}</td>
                                                <td>{{ \Carbon\Carbon::parse($item->remind_time)->format('h:i A') }}</td>

                                                {{-- Comment --}}
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="flex-shrink-0 me-2">
                                                            <i class="fas fa-comment-alt text-muted"></i>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <span class="d-block" data-bs-toggle="tooltip" title="{{ $comment }}">
                                                                {!! $short !!}
                                                            </span>
                                                            <a href="javascript:void(0);" onclick="showComment({{ $item->id }})" class="text-primary small">
                                                                View more
                                                            </a>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- ================= Tomorrow ================= --}}
                <div class="tab-pane fade" id="tomorrow" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="tomorrow-table">
                            <thead class="table-light">
                                <tr>
                                    <th>Type</th>
                                    <th>Lead Details</th>
                                    <th>Agent</th>
                                    <th>Source</th>
                                    <th>Campaign</th>
                                    <th>Date & Time</th>
                                    <th>Status</th>
                                    <th>Classification</th>
                                    <th>Remind Date</th>
                                    <th>Remind Time</th>
                                    <th>Last Comment</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(['tomorrowCalls','tomorrowVisits','tomorrowWhatsapp','tomorrowMeetings','interestedTomorrow'] as $key)
                                    @if(isset($followups[$key]) && $followups[$key]->isNotEmpty())
                                        @foreach($followups[$key] as $item)
                                            @php
                                                switch($key) {
                                                    case 'tomorrowCalls': 
                                                        $type='Call'; $icon='mdi mdi-phone-outline'; $badgeColor='primary'; break;
                                                    case 'tomorrowVisits': 
                                                        $type='Visit'; $icon='mdi mdi-map-marker-outline'; $badgeColor='success'; break;
                                                    case 'tomorrowMeetings':
                                                        $type = $item->type; 
                                                        switch ($item->type) 
                                                        {
                                                            case 'Processing':
                                                                $icon = 'mdi mdi-progress-clock';
                                                                $badgeColor = 'warning';
                                                                break;

                                                            case 'Visit Done':
                                                                $icon = 'mdi mdi-check-circle-outline';
                                                                $badgeColor = 'success';
                                                                break;

                                                            case 'Not Picked':
                                                                $icon = 'mdi mdi-phone-missed';
                                                                $badgeColor = 'danger';
                                                                break;

                                                            case 'Future Lead':
                                                                $icon = 'mdi mdi-calendar-clock';
                                                                $badgeColor = 'info';
                                                                break;

                                                            default:
                                                                $icon = 'mdi mdi-account-group-outline';
                                                                $badgeColor = 'primary';
                                                        }
                                                        break;

                                                    case 'tomorrowWhatsapp': 
                                                        $type='WhatsApp'; $icon='fab fa-whatsapp'; $badgeColor='success'; break;
                                                    default: 
                                                        $type=''; $icon=''; $badgeColor='secondary';
                                                }
                                                $comment = strip_tags($item->last_comment ?? '');
                                                $short = \Illuminate\Support\Str::limit($comment, 30);
                                                $createdDate = \Carbon\Carbon::parse($item->created_at);
                                                $currentDate = \Carbon\Carbon::now();
                                                $diff_date_count = $createdDate->diffInDays($currentDate);
                                            @endphp
                                            <tr>
                                                {{-- Type --}}
                                                <td>
                                                    <span class="badge bg-{{ $badgeColor }} bg-soft text-light">
                                                        <i class="{{ $icon }}"></i> {{ $type }}
                                                    </span>
                                                    <div class="d-flex gap-2 align-items-center">
                                                        <a href="tel:{{ $item->phone }}" class="btn btn-xs btn-soft-light" title="Call">
                                                            <i class="fas fa-phone text-primary"></i>
                                                        </a>
                                                        <a href="https://wa.me/91{{ $item->whatsapp_no ?? $item->phone }}" target="_blank" class="btn btn-xs btn-soft-light" title="WhatsApp">
                                                            <i class="fab fa-whatsapp text-success"></i>
                                                        </a>

                                                        <div class="action-item pin-item {{ $item->is_pinned ? 'pinned' : '' }}" 
                                                            onclick="togglePin({{ $item->id }}, {{ $item->is_pinned ? 0 : 1 }})" 
                                                            data-bs-toggle="tooltip" 
                                                            title="{{ $item->is_pinned ? 'Unpin Lead' : 'Pin Lead' }}" 
                                                            style="cursor:pointer;">
                                                            <i class="fas fa-thumbtack"></i>
                                                        </div>

                                                        <div class="position-relative d-inline-block text-center">
                                                        @if($diff_date_count > 5)
                                                        <span 
                                                            class="position-absolute bottom-100 start-50 translate-middle badge rounded-pill bg-danger text-light shadow-sm cursor-pointer" 
                                                            style="font-size: 0.65rem; padding: 0.25em 0.5em;"
                                                            data-bs-toggle="tooltip" 
                                                            data-bs-placement="top" 
                                                            title="Lead created on {{ $createdDate->format('Y-m-d H:i') }}, {{ $diff_date_count }} days ago">
                                                            👋🏻 {{ $diff_date_count }} days
                                                        </span>
                                                        @endif
                                                        <span>
                                                            {{ $item->id }}
                                                        </span>
                                                        @if($item->is_pinned)
                                                            <span class="pinned-badge">Pinned</span>
                                                        @endif
                                                        
                                                        @if($item->visited_on == 1)
                                                            <span class="rounded-pill px-3 py-1 bg-light border text-success fw-semibold small">
                                                                <i class="fas fa-user-check me-1"></i> Visited
                                                            </span>
                                                        @endif
                                                    </div>
                                                </td>

                                                {{-- Lead Details --}}
                                                <td>
                                                    <div class="d-flex flex-column">
                                                        <h6 class="mb-0">{{ $item->name }}</h6>
                                                        <span class="text-muted small">{{ $item->phone }}</span>
                                                    </div>
                                                    <div class="d-flex">
                                                        <button class="btn btn-xs btn-soft-light" 
                                                            onclick="showStatusUpdateModal({{ $item->id }}, '{{ $item->status }}')"
                                                            data-bs-toggle="tooltip" title="Update Status">
                                                            <i class="fas fa-sync-alt text-info"></i>
                                                        </button>
                                                        <a href="{{ route('lead.edit', $item->id) }}" class="btn btn-xs btn-soft-light" data-bs-toggle="tooltip" title="Edit">
                                                            <i class="fas fa-edit text-warning"></i>
                                                        </a>
                                                    </div>
                                                </td>

                                                <td>{{ $item->agent_name }}</td>
                                                <td>{{ $item->source }}</td>
                                                <td>{{ $item->campaign }}</td>
                                                <td>{{ \Carbon\Carbon::parse($item->datetime)->format('M d, Y h:i A') }}</td>

                                                {{-- Status Badge --}}
                                                <td>
                                                    @if($type == 'Call')
                                                        <span class="badge bg-primary bg-opacity-10 text-light">Call</span>
                                                    @elseif($type == 'Visit')
                                                        <span class="badge bg-success bg-opacity-10 text-success">Visit</span>
                                                    @elseif($type == 'WhatsApp')
                                                        <span class="badge bg-success bg-opacity-10 text-success">WhatsApp</span>
                                                    @else
                                                        <span class="badge bg-secondary bg-opacity-10 text-muted">Scheduled</span>
                                                    @endif
                                                </td>

                                                <td>{{ $item->classification ?? '-' }}</td>
                                                <td>{{ \Carbon\Carbon::parse($item->remind_date)->format('d M Y') }}</td>
                                                <td>{{ \Carbon\Carbon::parse($item->remind_time)->format('h:i A') }}</td>

                                                {{-- Comment --}}
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="flex-shrink-0 me-2">
                                                            <i class="fas fa-comment-alt text-muted"></i>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <span class="d-block" data-bs-toggle="tooltip" title="{{ $comment }}">
                                                                {!! $short !!}
                                                            </span>
                                                            <a href="javascript:void(0);" onclick="showComment({{ $item->id }})" class="text-primary small">
                                                                View more
                                                            </a>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="tab-pane fade" id="missed" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="missed-table">
                            <thead class="table-light">
                                <tr>
                                    <th>Type</th>
                                    <th>Lead Details</th>
                                    <th>Agent</th>
                                    <th>Source</th>
                                    <th>Campaign</th>
                                    <th>Date & Time</th>
                                    <th>Status</th>
                                    <th>Classification</th>
                                    <th>Remind Date</th>
                                    <th>Remind Time</th>
                                    <th>Last Comment</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($followups['missedFollowups']) && $followups['missedFollowups']->isNotEmpty())
                                    @foreach($followups['missedFollowups'] as $item)
                                        @php
                                            $type='Missed'; 
                                            $icon='mdi mdi-alert-circle-outline'; 
                                            $badgeColor='danger';
                                            $comment = strip_tags($item->last_comment ?? '');
                                            $short = \Illuminate\Support\Str::limit($comment, 30);
                                            $createdDate = \Carbon\Carbon::parse($item->created_at);
                                            $currentDate = \Carbon\Carbon::now();
                                            $diff_date_count = $createdDate->diffInDays($currentDate);
                                        @endphp
                                        <tr>
                                        <td>
                                            <span class="badge bg-{{ $badgeColor }} bg-soft text-light">
                                                <i class="{{ $icon }}"></i> {{ $type }}
                                            </span>
                                            <div class="d-flex gap-2 align-items-center">
                                                <a href="tel:{{ $item->phone }}" class="btn btn-xs btn-soft-light" title="Call">
                                                    <i class="fas fa-phone text-primary"></i>
                                                </a>
                                                <a href="https://wa.me/91{{ $item->whatsapp_no ?? $item->phone }}" target="_blank" class="btn btn-xs btn-soft-light" title="WhatsApp">
                                                    <i class="fab fa-whatsapp text-success"></i>
                                                </a>                                                
                                                <div class="action-item pin-item {{ $item->is_pinned ? 'pinned' : '' }}" 
                                                    onclick="togglePin({{ $item->id }}, {{ $item->is_pinned ? 0 : 1 }})" 
                                                    data-bs-toggle="tooltip" 
                                                    title="{{ $item->is_pinned ? 'Unpin Lead' : 'Pin Lead' }}" 
                                                    style="cursor:pointer;">
                                                    <i class="fas fa-thumbtack"></i>
                                                </div>
                                                <div class="position-relative d-inline-block text-center">
                                                    @if($diff_date_count > 5)
                                                    <span 
                                                        class="position-absolute bottom-100 start-50 translate-middle badge rounded-pill bg-danger text-light shadow-sm cursor-pointer" 
                                                        style="font-size: 0.65rem; padding: 0.25em 0.5em;"
                                                        data-bs-toggle="tooltip" 
                                                        data-bs-placement="top" 
                                                        title="Lead created on {{ $createdDate->format('Y-m-d H:i') }}, {{ $diff_date_count }} days ago">
                                                        👋🏻 {{ $diff_date_count }} days
                                                    </span>
                                                    @endif
                                                    <span>
                                                        {{ $item->id }}
                                                    </span>
                                                </div>
                                                @if($item->is_pinned)
                                                    <span class="pinned-badge">Pinned</span>
                                                @endif
                                                
                                                @if($item->visited_on == 1)
                                                    <span class="rounded-pill px-3 py-1 bg-light border text-success fw-semibold small">
                                                        <i class="fas fa-user-check me-1"></i> Visited
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                        
                                        <td>
                                            <div class="d-flex flex-column">
                                                <h6 class="mb-0">{{ $item->name }}</h6>
                                                <span class="text-muted small">{{ $item->phone }}</span>
                                            </div>
                                            <div class="d-flex">
                                                <button class="btn btn-xs btn-soft-light" 
                                                    onclick="showStatusUpdateModal({{ $item->id }}, '{{ $item->status }}')"
                                                    data-bs-toggle="tooltip" title="Update Status">
                                                    <i class="fas fa-sync-alt text-info"></i>
                                                </button>
                                                <a href="{{ route('lead.edit', $item->id) }}" class="btn btn-xs btn-soft-light" data-bs-toggle="tooltip" title="Edit">
                                                    <i class="fas fa-edit text-warning"></i>
                                                </a>
                                            </div>
                                        </td>

                                        <td>{{ $item->agent_name }}</td>
                                        <td>{{ $item->source }}</td>
                                        <td>{{ $item->campaign }}</td>
                                        <td>{{ \Carbon\Carbon::parse($item->datetime)->format('M d, Y h:i A') }}</td>

                                        {{-- Status Badge --}}
                                        <td>
                                            @if($type == 'Call')
                                                <span class="badge bg-primary bg-opacity-10 text-light">Call</span>
                                            @elseif($type == 'Visit')
                                                <span class="badge bg-success bg-opacity-10 text-success">Visit</span>
                                            @elseif($type == 'WhatsApp')
                                                <span class="badge bg-success bg-opacity-10 text-success">WhatsApp</span>
                                            @else
                                                <span class="badge bg-secondary bg-opacity-10 text-muted">Scheduled</span>
                                            @endif
                                        </td>

                                        <td>{{ $item->classification ?? '-' }}</td>
                                        <td>{{ \Carbon\Carbon::parse($item->remind_date)->format('d M Y') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($item->remind_time)->format('h:i A') }}</td>

                                        {{-- Comment --}}
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0 me-2">
                                                    <i class="fas fa-comment-alt text-muted"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <span class="d-block" data-bs-toggle="tooltip" title="{{ $comment }}">
                                                        {!! $short !!}
                                                    </span>
                                                    <a href="javascript:void(0);" onclick="showComment({{ $item->id }})" class="text-primary small">
                                                        View more
                                                    </a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() 
    {
        var todayTable = $('#today-table').DataTable({
            scrollX: true,
            scrollY: '400px',  
            scrollCollapse: true,
            responsive: false,
            paging: true,
            fixedColumns: {
                leftColumns: 2,
                rightColumns: 1
            }
        });

        var tomorrowTable = $('#tomorrow-table').DataTable({
            scrollX: true,
            scrollY: '400px',  
            scrollCollapse: true,
            responsive: false,
            paging: true,
            fixedColumns: {
                leftColumns: 2,
                rightColumns: 1
            }
        });

        var missedTable = $('#missed-table').DataTable({
            scrollX: true,
            scrollY: '400px',  
            scrollCollapse: true,
            responsive: false,
            paging: true,
            fixedColumns: {
                leftColumns: 2,
                rightColumns: 1
            }
        });

        $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) 
        {
            var target = $(e.target).attr("data-bs-target");
            if(target === '#today') todayTable.columns.adjust();
            if(target === '#tomorrow') tomorrowTable.columns.adjust();
            if(target === '#missed') missedTable.columns.adjust();
        });
    });
</script>
