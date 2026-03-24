@extends('layouts.app')

@section('title', 'Search Results | Pro-leadexpertz')

@section('content')
@include('modals.view-comments')
@include('modals.status-update')
@php
    $softwareType = session('software_type', 'real_state');
    $isLeadManagement = $softwareType === 'lead_management';
@endphp
<style>
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
</style>
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <h4 class="mb-0 text-gradient-primary">
                            <i class="fas fa-search me-2"></i>Search Results
                        </h4>
                        <span class="cust-badge text-dark bg-soft-primary ms-2">{{ $leads->total() }} Leads</span>
                    </div>
                </div>
            </div>
            
            <div class="col-12">
                <div class="card p-3">
                    <div>
                        <label>
                            Show 
                            <select id="lengthSelect" class="form-select form-select-sm" style="width: auto; display: inline-block;">
                                <option value="10" {{ $leads->perPage() == 10 ? 'selected' : '' }}>10</option>
                                <option value="25" {{ $leads->perPage() == 25 ? 'selected' : '' }}>25</option>
                                <option value="50" {{ $leads->perPage() == 50 ? 'selected' : '' }}>50</option>
                                <option value="100" {{ $leads->perPage() == 100 ? 'selected' : '' }}>100</option>
                                <option value="500" {{ $leads->perPage() == 500 ? 'selected' : '' }}>500</option>
                            </select> 
                            entries
                        </label>
                    </div>
                    
                    <table id="table" class="table-hover table-bordered dt-responsive nowrap w-100">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Lead ID</th>
                                <th>Lead Details</th>
                                <th>Agent</th>
                                <th>Source</th>
                                <th>Campaign</th>
                                <th>Classification</th>
                                <th>Status</th>
                                <th>Budget</th>
                                <th>{{ $isLeadManagement ? 'Product' : 'Project' }}</th>
                                <th>Email</th>
                                <th>Lead Date</th>
                                <th>Follow Up</th>
                                <th>Last Comment</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($leads as $row)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <div class="d-flex gap-2 align-items-center">
                                        {{ $row->id }}
                                        @if(isset($row->visited_on) && $row->visited_on == 1)
                                            <span class="rounded-pill px-3 py-1 bg-light border text-success fw-semibold small">
                                                <i class="fas fa-user-check me-1"></i> Visited
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <div class="d-flex align-items-center mb-1">
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">{{ $row->name }}</h6>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="text-muted small">{{ $row->phone }}</span>
                                            <div class="d-flex">
                                                <a href="tel:{{ $row->phone }}" class="btn btn-xs btn-soft-light" data-bs-toggle="tooltip" title="Call">
                                                    <i class="fas fa-phone text-primary"></i>
                                                </a>
                                                <a href="https://wa.me/91{{ $row->phone }}" target="_blank" class="btn btn-xs btn-soft-light" data-bs-toggle="tooltip" title="WhatsApp">
                                                    <i class="fab fa-whatsapp text-success"></i>
                                                </a>
                                                <a href="{{ route('lead.edit', $row->id) }}" class="btn btn-xs btn-soft-light" data-bs-toggle="tooltip" title="Edit">
                                                    <i class="fas fa-edit text-warning"></i>
                                                </a>
                                                <button class="btn btn-xs btn-soft-light" 
                                                    onclick="showStatusUpdateModal({{ $row->id }}, '{{ $row->status }}')"
                                                    data-bs-toggle="tooltip" title="Update Status">
                                                    <i class="fas fa-sync-alt text-info"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1">
                                            <span class="d-block">{{ $row->agent ?? '—' }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="cust-badge bg-soft-info text-info">
                                        <i class="fas fa-{{ $row->source == 'Website' ? 'globe' : ($row->source == 'Referral' ? 'user-friends' : 'ad') }} me-1"></i>
                                        {{ $row->source ?? '—' }}
                                    </span>
                                </td>
                                <td>{{ $row->campaign ?? '—' }}</td>
                                <td>
                                    <span class="cust-badge text-dark">
                                        {{ $row->classification ?? '—' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="cust-badge text-dark">
                                        {{ $row->status ?? '—' }}
                                    </span>
                                </td>
                                <td>
                                    {{ $row->budget ?? '—' }}
                                </td>
                                <td>
                                    {{ $row->project_name ?? '—' }}
                                </td>
                                <td>
                                    <a href="mailto:{{ $row->email }}" class="text-truncate d-inline-block" style="max-width: 200px;">
                                        {{ $row->email }}
                                    </a>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($row->lead_date)->format('d M Y') }}</td>
                                <td>
                                    <span class="cust-badge text-dark">
                                        {{ isset($row->updated_date) ? \Carbon\Carbon::parse($row->updated_date)->format('d M Y') : '—' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0 me-2">
                                            <i class="fas fa-comment-alt text-muted"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            @php
                                                $comment = isset($row->last_comment) ? strip_tags($row->last_comment) : '';
                                                $short = \Illuminate\Support\Str::limit($comment, 30);
                                            @endphp
                                            <span class="d-block" data-bs-toggle="tooltip" title="{{ $comment }}">
                                                {!! $short !!}
                                            </span>
                                            <a href="javascript:void(0);" onclick="showComment({{ $row->id }})" class="text-primary small">
                                                View more
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="d-flex justify-content-end mt-3">
                       {{ $leads->appends(['q' => $q])->links('vendor.pagination.bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection