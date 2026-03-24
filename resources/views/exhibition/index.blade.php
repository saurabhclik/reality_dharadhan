@extends('layouts.app')

@section('title', 'Exhibitions | LeadManagement')

@section('content')
<style>
    .toggle-auto-welcome .fa-toggle-on 
    {
        color: #28a745;
    }
    .toggle-auto-welcome .fa-toggle-off 
    {
        color: #6c757d;
    }
    .btn-xs 
    {
        padding: 0.15rem 0.35rem;
        font-size: 0.75rem;
    }
    .btn-group .btn 
    {
        border-radius: 0.25rem !important;
        margin-right: 2px;
    }
</style>
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Exhibitions Management</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Exhibitions</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <h4 class="card-title">All Exhibitions</h4>
                                <p class="card-title-desc">Manage your exhibitions below. Only one exhibition can be active at a time.</p>
                            </div>
                            <div class="col-md-6 text-end">
                                <button type="button" class="btn btn-primary" id="createExhibitionBtn">
                                    <i class="fas fa-plus-circle"></i> Add New Exhibition
                                </button>
                            </div>
                        </div>
                        <div class="table-responsive">                        
                            <table id="table" class="table table-hover table-bordered dt-responsive nowrap w-100">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Exhibition Name</th>
                                        <th>Date Range</th>
                                        <th>Location</th>
                                        <th>Status</th>
                                        <th>Duration</th>
                                        <th>Leads</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($exhibitions as $exhibition)
                                    @php
                                        $leadCount = DB::table('exhibition_leads')->where('exhibition_id', $exhibition->id)->count();
                                    @endphp
                                    <tr id="exhibition-row-{{ $exhibition->id }}">
                                        <td>
                                            {{ $loop->iteration }}
                                            <div class="btn-group" role="group">                                            
                                                <button type="button" 
                                                    class="btn btn-xs btn-soft-light edit-exhibition"
                                                    data-id="{{ $exhibition->id }}"
                                                    data-name="{{ $exhibition->name }}"
                                                    data-description="{{ $exhibition->description }}"
                                                    data-start-date="{{ \Carbon\Carbon::parse($exhibition->start_date)->format('Y-m-d\TH:i') }}"
                                                    data-end-date="{{ \Carbon\Carbon::parse($exhibition->end_date)->format('Y-m-d\TH:i') }}"
                                                    data-location="{{ $exhibition->location }}"
                                                    data-is-active="{{ $exhibition->is_active }}"
                                                    data-auto-welcome="{{ $exhibition->auto_welcome_message ?? 0 }}"
                                                    title="Edit">
                                                    <i class="fas fa-edit text-primary"></i>
                                                </button>
                                                
                                                <a href="{{ route('exhibition.view', $exhibition->id) }}" 
                                                class="btn btn-xs btn-soft-light" 
                                                title="View Details">
                                                    <i class="fas fa-eye text-info"></i>
                                                </a>
                                                
                                                <button type="button" 
                                                    class="btn btn-xs {{ $exhibition->auto_welcome_message ? 'btn-success' : 'btn-outline-secondary' }} toggle-auto-welcome"
                                                    data-id="{{ $exhibition->id }}"
                                                    data-name="{{ $exhibition->name }}"
                                                    data-status="{{ $exhibition->auto_welcome_message ?? 0 }}"
                                                    title="Auto Welcome {{ $exhibition->auto_welcome_message ? 'ON' : 'OFF' }}">
                                                    <i class="fas fa-bolt"></i>
                                                </button>
                                                
                                                @if(!$exhibition->is_active)
                                                <button type="button" 
                                                    class="btn btn-xs btn-soft-light activate-exhibition"
                                                    data-id="{{ $exhibition->id }}"
                                                    data-name="{{ $exhibition->name }}"
                                                    title="Activate">
                                                    <i class="fas fa-toggle-on text-success"></i>
                                                </button>
                                                @endif
                                                
                                                <button type="button" 
                                                    class="btn btn-xs btn-soft-light delete-exhibition"
                                                    data-id="{{ $exhibition->id }}"
                                                    data-name="{{ $exhibition->name }}"
                                                    title="Delete">
                                                    <i class="fas fa-trash-alt text-danger"></i>
                                                </button>

                                                @if(isset($shareLinks[$exhibition->id]))
                                                    @php
                                                        $shareUrl = route('exhibition.share.access', $shareLinks[$exhibition->id]->share_code);
                                                    @endphp
                                                    <a href="{{ $shareUrl }}" 
                                                    class="btn btn-xs btn-soft-light" 
                                                    target="_blank" 
                                                    title="View Share Link">
                                                        <i class="fas fa-link text-success"></i>
                                                    </a>
                                                    <button type="button"
                                                        class="btn btn-xs btn-soft-light copy-share-link"
                                                        data-link="{{ $shareUrl }}"
                                                        title="Copy Share Link">
                                                        <i class="fas fa-copy text-primary"></i>
                                                    </button>
                                                @else
                                                    <button type="button" 
                                                        class="btn btn-xs btn-soft-light share-exhibition"
                                                        data-id="{{ $exhibition->id }}"
                                                        data-name="{{ $exhibition->name }}"
                                                        title="Create Share Link">
                                                        <i class="fas fa-share-alt text-success"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0 me-3">
                                                    <div class="avatar-xs">
                                                        <div class="avatar-title bg-soft-primary text-primary rounded-circle text-light">
                                                            EX
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h5 class="font-size-14 mb-1">{{ $exhibition->name }}</h5>
                                                    @if($exhibition->description)
                                                    <p class="text-muted mb-0 font-size-12">
                                                        {{ Str::limit($exhibition->description, 50) }}
                                                    </p>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-muted">
                                                <i class="fas fa-calendar-day me-1"></i>
                                                {{ \Carbon\Carbon::parse($exhibition->start_date)->format('d M, Y') }}
                                            </div>
                                            <div class="text-muted">
                                                <i class="fas fa-calendar-times me-1"></i>
                                                {{ \Carbon\Carbon::parse($exhibition->end_date)->format('d M, Y') }}
                                            </div>
                                        </td>
                                        <td>
                                            <i class="fas fa-map-marker-alt text-danger me-1"></i>
                                            {{ $exhibition->location }}
                                        </td>
                                        <td>
                                            @if($exhibition->is_active)
                                            <span class="badge bg-success rounded-pill">
                                                <i class="fas fa-check-circle me-1"></i> Active
                                            </span>
                                            @else
                                            <span class="badge bg-secondary rounded-pill">
                                                <i class="fas fa-times-circle me-1"></i> Inactive
                                            </span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ \Carbon\Carbon::parse($exhibition->start_date)->diffInDays($exhibition->end_date) + 1 }} days
                                        </td>
                                        <td>
                                            <div class="text-center">
                                                <span class="badge bg-info rounded-pill mb-1">
                                                    <i class="fas fa-users me-1"></i> {{ $leadCount }}
                                                </span>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if($exhibitions->hasPages())
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        Showing {{ $exhibitions->firstItem() }} to {{ $exhibitions->lastItem() }} 
                                        of {{ $exhibitions->total() }} entries
                                    </div>
                                    <nav aria-label="Page navigation">
                                        {{ $exhibitions->links('pagination::bootstrap-5') }}
                                    </nav>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="exhibitionModal" tabindex="-1" aria-labelledby="exhibitionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-light">
                <h5 class="modal-title" id="exhibitionModalLabel">
                    <i class="fas fa-plus-circle me-2"></i>Create New Exhibition
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="exhibitionForm">
                @csrf
                <input type="hidden" id="_method" name="_method" value="POST">
                <input type="hidden" id="exhibition_id" name="id">
                
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="name" class="form-label">Exhibition Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" required>
                            <div class="invalid-feedback" id="name-error"></div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
                            <input type="datetime-local" class="form-control" id="start_date" name="start_date" required>
                            <div class="invalid-feedback" id="start_date-error"></div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="end_date" class="form-label">End Date <span class="text-danger">*</span></label>
                            <input type="datetime-local" class="form-control" id="end_date" name="end_date" required>
                            <div class="invalid-feedback" id="end_date-error"></div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="location" class="form-label">Location <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="location" name="location" required>
                            <div class="invalid-feedback" id="location-error"></div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                            <div class="invalid-feedback" id="description-error"></div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1">
                                <label class="form-check-label" for="is_active">
                                    <strong>Set as Active Exhibition</strong>
                                    <small class="text-muted d-block">
                                        (Only one exhibition can be active at a time. Activating this will deactivate any currently active exhibition.)
                                    </small>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="saveExhibitionBtn">
                        <i class="fas fa-save me-1"></i> Save Exhibition
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="shareExhibitionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fas fa-share-alt me-2"></i>Share Exhibition
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Copy and share the link below.
                </div>
                <input type="hidden" id="shareExhibitionId">
                <div class="mb-3">
                    <label for="share_link_input" class="form-label">Shareable Link</label>
                    <input type="text" class="form-control" id="share_link_input" readonly>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="generateShareLinkBtn">
                    <i class="fas fa-link me-1"></i> Generate Share Link
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() 
    {
        $(document).on('click', '.toggle-auto-welcome', function() 
        {
            const exhibitionId = $(this).data('id');
            const exhibitionName = $(this).data('name');
            const currentStatus = $(this).data('status');
            const newStatus = currentStatus ? 0 : 1;
            
            const action = newStatus ? 'enable' : 'disable';
            const confirmMessage = `Are you sure you want to ${action} auto welcome messages for "${exhibitionName}"?`;
            
            if (confirm(confirmMessage)) 
            {
                $.ajax({
                    url: '/exhibitions/' + exhibitionId + '/toggle-auto-welcome',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        auto_welcome_message: newStatus
                    },
                    success: function(response) 
                    {
                        if (response.success) 
                        {
                            const $button = $(`.toggle-auto-welcome[data-id="${exhibitionId}"]`);
                            const $icon = $button.find('i');
                            
                            if (newStatus) 
                            {
                                $icon.removeClass('fa-toggle-off text-secondary')
                                    .addClass('fa-toggle-on text-success');
                                $button.attr('title', 'Disable Auto Welcome');
                            } 
                            else 
                            {
                                $icon.removeClass('fa-toggle-on text-success')
                                    .addClass('fa-toggle-off text-secondary');
                                $button.attr('title', 'Enable Auto Welcome');
                            }
                            
                            $button.data('status', newStatus);
                            const $row = $(`#exhibition-row-${exhibitionId}`);
                            let $statusCell = $row.find('td').eq(4);
                            if (newStatus) 
                            {
                                if (!$statusCell.find('.badge.bg-info').length) 
                                {
                                    $statusCell.append('<br><span class="badge bg-info rounded-pill mt-1"><i class="fas fa-robot me-1"></i> Auto-Welcome</span>');
                                }
                            } 
                            else 
                            {
                                $statusCell.find('.badge.bg-info').remove();
                            }
                            
                            toastr.success(response.message);
                        } 
                        else 
                        {
                            toastr.error(response.message);
                        }
                    },
                    error: function(xhr) 
                    {
                        toastr.error('Failed to update auto welcome setting');
                    }
                });
            }
        });

        $(document).on('click', '.edit-exhibition', function() 
        {
            const exhibitionId = $(this).data('id');
            $.ajax({
                url: '/exhibitions/' + exhibitionId + '/get-details',
                method: 'GET',
                success: function(response) 
                {
                    if (response.success) 
                    {
                        $('#exhibitionModalLabel').html('<i class="fas fa-edit me-2"></i>Edit Exhibition');
                        $('#_method').val('PUT');
                        $('#exhibition_id').val(response.exhibition.id);
                        $('#name').val(response.exhibition.name);
                        $('#description').val(response.exhibition.description);
                        $('#start_date').val(response.exhibition.start_date);
                        $('#end_date').val(response.exhibition.end_date);
                        $('#location').val(response.exhibition.location);
                        $('#is_active').prop('checked', response.exhibition.is_active == 1);
                        $('#auto_welcome_message').prop('checked', response.exhibition.auto_welcome_message == 1);
                        $('#exhibitionModal').modal('show');
                    }
                }
            });
        });

        $(document).on('click', '.copy-share-link', function() 
        {
            const link = $(this).data('link');
            navigator.clipboard.writeText(link).then(function() 
            {
                toastr.success('Share link copied to clipboard!');
            });
        });
    });
</script>
@endsection