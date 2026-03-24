@extends('layouts.app')

@section('title', 'User Management | Pro-leadexpertz')
@section('content')
    <style>
        .dataTables_info, .dataTables_filter 
        {
            display:none !important;
        }
        #table_length 
        {
            display:none !important;
        }
        .datepicker 
        {
            border:1px solid #d1d1d1 !important;
        }
        .form-check-input:checked 
        {
            background-color: #28a745;
            border-color: #28a745;
        }
        .status-inactive 
        {
            color: #dc3545;
        }
        .status-active 
        {
            color: #28a745;
        }
        .input-group-text 
        {
            min-width: 100px;
            justify-content: center;
        }
    </style>
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-flex align-items-center justify-content-between">
                        <h4 class="mb-0">User Management</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                                <li class="breadcrumb-item active">Users</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h4 class="card-title mb-0">User List</h4>
                                <div>
                                    <button class="btn btn-outline-secondary me-2" id="resetFilters">
                                        <i class="fas fa-sync-alt me-1"></i> Reset
                                    </button>
                                    <a href="{{ route('users.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus me-1"></i> Add User
                                    </a>
                                </div>
                            </div>

                            <div class="row mb-3 g-2">
                                <div class="col-md-3">
                                    <div class="input-group">
                                        <span class="input-group-text bg-transparent"><i class="fas fa-search"></i></span>
                                        <input type="text" class="form-control" id="nameFilter" placeholder="Search by name">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="input-group">
                                        <span class="input-group-text bg-transparent">Status</span>
                                        <select class="form-select" id="statusFilter">
                                            <option value="">All</option>
                                            <option value="1">Active</option>
                                            <option value="0">Inactive</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="input-group">
                                        <span class="input-group-text bg-transparent">Role</span>
                                        <select class="form-select" id="roleFilter">
                                            <option value="">All</option>
                                            @foreach($roles as $role)
                                                <option value="{{ $role->role_name }}">{{ ucfirst($role->role_name) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="input-group">
                                        <span class="input-group-text bg-transparent">Team Lead</span>
                                        <select class="form-select" id="teamLeadFilter">
                                            <option value="">All</option>
                                            @foreach($teamLeads as $lead)
                                                <option value="{{ $lead->id }}">{{ $lead->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group">
                                        <span class="input-group-text bg-transparent"><i class="fas fa-calendar"></i></span>
                                        <input type="text" class="form-control datepicker" id="dateFilter" placeholder="Joined date">
                                        <button class="btn btn-outline-secondary" id="clearDate">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-sm table-hover table-bordered mb-0" id="table">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="40">#</th>
                                            <th>User</th>
                                            <th>Contact</th>
                                            <th width="70">Status</th>
                                            <th width="70">Role</th>
                                            <th>Team Lead</th>
                                            <th width="80">Joined</th>
                                            <th width="100">Agent Link</th>
                                            <th width="45">QR</th>
                                            <th width="85">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="table-compact">
                                        @foreach($users as $user)
                                        @php
                                            $hasLink = isset($agentLinks[$user->id]);
                                            $link = $hasLink ? $agentLinks[$user->id] : null;
                                        @endphp
                                        <tr>
                                            <td class="align-middle">{{ $loop->iteration }}</td>
                                            <td class="align-middle">
                                                <div class="d-flex align-items-center">
                                                    <div>
                                                        <span class="fw-bold">{{ $user->name }}</span>
                                                        @if($user->unique_id)
                                                            <span class="badge bg-danger unique-id-badge">{{ $user->unique_id }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="align-middle">
                                                <div><small>{{ $user->email }}</small></div>
                                                <small class="text-muted">{{ $user->mobile ?? 'N/A' }}</small>
                                                @if($user->zone)
                                                    <small class="d-block text-muted">{{ $user->district_name }}, {{ $user->zone }}, {{ $user->zone_sub_area }}</small>
                                                @endif
                                            </td>
                                            <td class="align-middle">
                                                <div class="form-check form-switch form-switch-sm d-inline-block me-1">
                                                    <input type="checkbox" class="form-check-input status-toggle"
                                                        data-user-id="{{ $user->id }}"
                                                        {{ $user->is_active ? 'checked' : '' }}>
                                                </div>
                                            </td>
                                            <td class="align-middle">
                                                <span class="badge badge-compact {{ $user->role == 'super_admin' ? 'bg-danger' : 'bg-primary' }}">
                                                    {{ ucfirst(substr($user->role, 0, 3)) }}
                                                </span>
                                            </td>
                                            <td class="align-middle">
                                                <small>{{ $user->team_lead_name ?? 'N/A' }}</small>
                                            </td>
                                            <td class="align-middle">
                                                <small>{{ \Carbon\Carbon::parse($user->created_date)->format('d M y') }}</small>
                                            </td>
                                            <td class="align-middle">
                                                @if($hasLink && $link)
                                                    <div class="d-flex align-items-center">
                                                        <code class="text-truncate-custom" title="{{ $link->unique_identifier }}">
                                                            {{ $link->unique_identifier }}
                                                        </code>
                                                        <button class="btn btn-link text-primary p-0 ms-1" 
                                                                onclick="copyLink('{{ url($link->link_url) }}')"
                                                                title="Copy Link">
                                                            <i class="fa-solid fa-copy"></i>
                                                        </button>
                                                    </div>
                                                    <span class="badge bg-success badge-compact">Active</span>
                                                @else
                                                    <span class="text-muted small">Not generated</span>
                                                @endif
                                            </td>
                                            <td class="align-middle text-center">
                                                @if($hasLink && $link && $link->qr_code_path)
                                                    <a href="{{ asset($link->qr_code_path) }}" target="_blank">
                                                        <img src="{{ asset($link->qr_code_path) }}" class="qr-img" alt="QR" width="40" height="40">
                                                    </a>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="align-middle">
                                                <div class="d-flex gap-1">
                                                    <a href="{{ route('users.show', $user->id) }}"
                                                       class="btn btn-sm btn-soft-primary action-btn"
                                                       title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    
                                                    @if($user->unique_id)
                                                        @if($hasLink && $link)
                                                            <a href="{{ route('agent-links.show', $link->id) }}"
                                                               class="btn btn-sm btn-soft-info action-btn"
                                                               title="View">
                                                                <i class="fa fa-eye"></i>
                                                            </a>
                                                            <a href="{{ url($link->link_url) }}"
                                                               target="_blank"
                                                               class="btn btn-sm btn-soft-success action-btn"
                                                               title="Open">
                                                                <i class="fa fa-external-link-alt"></i>
                                                            </a>
                                                            <button class="btn btn-sm btn-soft-danger action-btn"
                                                                    onclick="deleteLink({{ $link->id }}, '{{ $user->name }}')"
                                                                    title="Delete Link">
                                                                <i class="fa fa-trash"></i>
                                                            </button>
                                                        @else
                                                            <button class="btn btn-sm btn-soft-primary action-btn" 
                                                                    onclick="generateLinkForAgent({{ $user->id }}, '{{ $user->name }}')"
                                                                    title="Generate Link">
                                                                <i class="fa fa-magic"></i>
                                                            </button>
                                                        @endif
                                                    @endif
                                                    
                                                    <button class="btn btn-sm btn-soft-danger action-btn delete-user-btn"
                                                            data-user-id="{{ $user->id }}"
                                                            title="Delete">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <div class="text-muted">
                                    Showing <span class="fw-bold">{{ $users->firstItem() }}</span> to 
                                    <span class="fw-bold">{{ $users->lastItem() }}</span> of 
                                    <span class="fw-bold">{{ $users->total() }}</span> entries
                                </div>
                                <div>
                                    {{ $users->appends(request()->query())->links('vendor.pagination.bootstrap-5') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="generateLinkModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('agent-links.store') }}">
                    @csrf
                    <input type="hidden" name="agent_id" id="modal_agent_id">

                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">Generate Link</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        Generate link for <strong id="modal_agent_name"></strong> ?
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-primary">
                            <i class="fa fa-magic"></i> Generate
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="deleteLinkModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <input type="hidden" id="delete_link_id">

                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Delete Link</h5>
                </div>

                <div class="modal-body text-center">
                    Delete link for <strong id="delete_agent_name"></strong> ?
                </div>

                <div class="modal-footer">
                    <button class="btn btn-danger" id="confirmDeleteBtn">
                        <i class="fa fa-trash"></i> Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
    <form id="delete-form" method="POST" style="display:none;">
        @csrf
        @method('DELETE')
    </form>
    <script>
        $(document).ready(function() 
        {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl)
            {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
            $('#dateFilter').datepicker({
                format: 'dd-mm-yyyy',
                autoclose: true,
                todayHighlight: true
            });
            $('#clearDate').click(function() 
            {
                $('#dateFilter').val('');
                applyFilters();
            });
            $('#resetFilters').click(function() 
            {
                $('#nameFilter').val('');
                $('#statusFilter').val('');
                $('#roleFilter').val('');
                $('#teamLeadFilter').val('');
                $('#dateFilter').val('');
                applyFilters();
            });
            $('.status-toggle').change(function() 
            {
                const userId = $(this).data('user-id');
                const isActive = $(this).is(':checked') ? 1 : 0;
                const $toggle = $(this);
                const $label = $(`#statusLabel${userId}`);
                
                $toggle.prop('disabled', true);
                
                $.ajax({
                    url: "{{ route('users.update-status') }}",
                    method: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        user_id: userId,
                        is_active: isActive
                    },
                    success: function(response) 
                    {
                        if(response.success) 
                        {
                            $label.text(isActive ? 'Active' : 'Inactive');
                            $label.toggleClass('status-active status-inactive');
                            toastr.success('User status updated successfully');
                        } 
                        else 
                        {
                            toastr.error(response.message || 'Failed to update status');
                            $toggle.prop('checked', !isActive);
                        }
                    },
                    error: function(xhr) 
                    {
                        toastr.error('Error updating status');
                        $toggle.prop('checked', !isActive);
                    },
                    complete: function() 
                    {
                        $toggle.prop('disabled', false);
                    }
                });
            });
            $('#nameFilter, #statusFilter, #roleFilter, #teamLeadFilter, #dateFilter').change(function() 
            {
                applyFilters();
            });
            let timer;
            $('#nameFilter').keyup(function() 
            {
                clearTimeout(timer);
                timer = setTimeout(() => {
                    applyFilters();
                }, 500);
            });

            function applyFilters() 
            {
                const name = $('#nameFilter').val();
                const status = $('#statusFilter').val();
                const role = $('#roleFilter').val();
                const teamLead = $('#teamLeadFilter').val();
                const date = $('#dateFilter').val();
                
                let queryParams = {};
                if (name) queryParams.name = name;
                if (status) queryParams.status = status;
                if (role) queryParams.role = role;
                if (teamLead) queryParams.team_lead = teamLead;
                if (date) queryParams.date = date;
                const queryString = $.param(queryParams);
                window.location.href = "{{ route('users.index') }}?" + queryString;
            }
            function setFilterValuesFromUrl()
            {
                const urlParams = new URLSearchParams(window.location.search);
                $('#nameFilter').val(urlParams.get('name') || '');
                $('#statusFilter').val(urlParams.get('status') || '');
                $('#roleFilter').val(urlParams.get('role') || '');
                $('#teamLeadFilter').val(urlParams.get('team_lead') || '');
                $('#dateFilter').val(urlParams.get('date') || '');
            }
            setFilterValuesFromUrl();
        });
        function copyLink(link)
        {
            navigator.clipboard.writeText(link);
            toastr.success('Link copied!');
        }

        function generateLinkForAgent(id, name)
        {
            $('#modal_agent_id').val(id);
            $('#modal_agent_name').text(name);

            new bootstrap.Modal(document.getElementById('generateLinkModal')).show();
        }

        function deleteLink(id, name)
        {
            $('#delete_link_id').val(id);
            $('#delete_agent_name').text(name);

            new bootstrap.Modal(document.getElementById('deleteLinkModal')).show();
        }

        $('#confirmDeleteBtn').click(function()
        {
            let id = $('#delete_link_id').val();
            $('#delete-form').attr('action', '/agent-links/' + id).submit();
        });
    </script>
@endsection