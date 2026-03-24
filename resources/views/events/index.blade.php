@extends('layouts.app')

@section('title', 'Events Management | Pro-leadexpertz')
@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-flex align-items-center justify-content-between">
                        <h4 class="mb-0">Events Management</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                                <li class="breadcrumb-item active">Events</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            @php $type = request('type', 'birthday'); @endphp
                            <ul class="nav nav-tabs nav-tabs-custom mb-4">
                                <li class="nav-item">
                                    <a class="nav-link {{ $type === 'birthday' ? 'active' : '' }}" href="{{ route('event.index', array_merge(request()->except('page'), ['type' => 'birthday'])) }}">
                                        <i class="fas fa-birthday-cake me-1"></i> Birthdays
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ $type === 'anniversary' ? 'active' : '' }}" href="{{ route('event.index', array_merge(request()->except('page'), ['type' => 'anniversary'])) }}">
                                        <i class="fas fa-calendar-alt me-1"></i> Anniversaries
                                    </a>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane fade {{ $type === 'birthday' ? 'show active' : '' }}" id="birthday">
                                    <form method="GET" action="{{ route('event.index') }}">
                                        <input type="hidden" name="type" value="birthday">
                                        <div class="row mb-3">
                                            <div class="col-md-3">
                                                <input type="date" class="form-control" name="fromDt" value="{{ request('fromDt') }}">
                                            </div>
                                            <div class="col-md-3">
                                                <input type="date" class="form-control" name="toDt" value="{{ request('toDt') }}">
                                            </div>
                                            <div class="col-md-3">
                                                <button type="submit" class="btn btn-primary">Search</button>
                                                <a href="{{ route('event.index') }}" class="btn btn-secondary">Reset</a>
                                            </div>
                                        </div>
                                    </form>

                                    <div class="table-responsive">
                                        <table id="birthday-table" class="table table-bordered dt-responsive nowrap w-100">
                                            <thead class="table-light">
                                                <tr>
                                                    <th class="text-center">ID</th>
                                                    <th>Agent Name</th>
                                                    <th>Status</th>
                                                    <th>Name</th>
                                                    <th>Applicant Name</th>
                                                    <th>App. Contact</th>
                                                    <th>App. City</th>
                                                    <th>Birthday</th>
                                                    <th>Anniversary</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($birthdayLeads as $lead)
                                                    <tr>
                                                        <td>{{ $lead->id }}</td>
                                                        <td>{{ $lead->agent_name }} ({{ $lead->agent_role }})</td>
                                                        <td>{{ $lead->status }}</td>
                                                        <td>{{ $lead->name }}</td>
                                                        <td>{{ $lead->app_name }}</td>
                                                        <td>{{ $lead->app_contact }}</td>
                                                        <td>{{ $lead->app_city }}</td>
                                                        <td>{{ $lead->app_dob ? \Carbon\Carbon::parse($lead->app_dob)->format('Y-m-d') : '' }}</td>
                                                        <td>{{ $lead->app_doa ? \Carbon\Carbon::parse($lead->app_doa)->format('Y-m-d') : '' }}</td>
                                                        <td>
                                                            <button class="btn btn-sm btn-warning" type="button" data-bs-toggle="modal" data-bs-target="#showCommentModal" onclick="loadComments({{ $lead->id }})">
                                                                <i class="fas fa-eye"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        <div class="d-flex justify-content-end mt-3">
                                            {{ $birthdayLeads->appends(request()->all())->links('pagination::bootstrap-5') }}
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane fade {{ $type === 'anniversary' ? 'show active' : '' }}" id="anniversary">
                                    <form method="GET" action="{{ route('event.index') }}">
                                        <input type="hidden" name="type" value="anniversary">
                                        <div class="row mb-3">
                                            <div class="col-md-3">
                                                <input type="date" class="form-control" name="fromDt" value="{{ request('fromDt') }}">
                                            </div>
                                            <div class="col-md-3">
                                                <input type="date" class="form-control" name="toDt" value="{{ request('toDt') }}">
                                            </div>
                                            <div class="col-md-3">
                                                <button type="submit" class="btn btn-primary">Search</button>
                                                <a href="{{ route('event.index') }}" class="btn btn-secondary">Reset</a>
                                            </div>
                                        </div>
                                    </form>

                                    <div class="table-responsive">
                                        <table id="anniversary-table" class="table table-bordered dt-responsive nowrap w-100">
                                            <thead class="table-light">
                                                <tr>
                                                    <th class="text-center">ID</th>
                                                    <th>Agent Name</th>
                                                    <th>Status</th>
                                                    <th>Name</th>
                                                    <th>Applicant Name</th>
                                                    <th>App. Contact</th>
                                                    <th>App. City</th>
                                                    <th>Birthday</th>
                                                    <th>Anniversary</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($anniversaryLeads as $lead)
                                                    <tr>
                                                        <td>{{ $lead->id }}</td>
                                                        <td>{{ $lead->agent_name }} ({{ $lead->agent_role }})</td>
                                                        <td>{{ $lead->status }}</td>
                                                        <td>{{ $lead->name }}</td>
                                                        <td>{{ $lead->app_name }}</td>
                                                        <td>{{ $lead->app_contact }}</td>
                                                        <td>{{ $lead->app_city }}</td>
                                                        <td>{{ $lead->app_dob ? \Carbon\Carbon::parse($lead->app_dob)->format('Y-m-d') : '' }}</td>
                                                        <td>{{ $lead->app_doa ? \Carbon\Carbon::parse($lead->app_doa)->format('Y-m-d') : '' }}</td>
                                                        <td>
                                                            <button class="btn btn-sm btn-warning" type="button" data-bs-toggle="modal" data-bs-target="#showCommentModal" onclick="loadComments({{ $lead->id }})">
                                                                <i class="fas fa-eye"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        <div class="d-flex justify-content-end mt-3">
                                            {{ $anniversaryLeads->appends(request()->all())->links('pagination::bootstrap-5') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="showCommentModal" tabindex="-1" aria-labelledby="showCommentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="showCommentModalLabel">Comments</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body bg-light-gray">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>S. No.</th>
                                    <th>Comment</th>
                                    <th>Status</th>
                                    <th>Remind Date</th>
                                    <th>User(Role)</th>
                                    <th>Create Date</th>
                                </tr>
                            </thead>
                            <tbody id="commentList">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() 
        {
            $('#birthday-table').DataTable({
                scrollX: true,
                scrollCollapse: true,
                fixedColumns: {
                    leftColumns: 3,
                    rightColumns: 1
                },
                paging: false, 
                searching: false,
                info: false
            });
            
            $('#anniversary-table').DataTable({
                scrollX: true,
                scrollCollapse: true,
                fixedColumns: {
                    leftColumns: 3,
                    rightColumns: 1
                },
                paging: false,
                searching: false,
                info: false
            });
            
            const urlParams = new URLSearchParams(window.location.search);
            const eventType = urlParams.get('type');
            
            if(eventType === 'anniversary') 
            {
                $('.nav-tabs a[href="#anniversary"]').tab('show');
            }

            $('#showCommentModal').on('hidden.bs.modal', function () 
            {
                $('#commentList').html('');
            });
        });
    
        function loadComments(id) 
        {
            $.ajax({
                method: 'GET',
                url: '{{ route("event.comments", "") }}/' + id,
                success: function(data) 
                {
                    let html = '';
                    let count = 1;
                    
                    $.each(data, function(index, value) 
                    {
                        html += `
                        <tr>
                            <td>${count++}</td>
                            <td>${value.comment || ''}</td>
                            <td>${value.status || ''}</td>
                            <td>${value.remind_date || ''} ${value.remind_time || ''}</td>
                            <td>${value.role || ''}</td>
                            <td>${value.created_date || ''}</td>
                        </tr>
                        `;
                    });
                    
                    $('#commentList').html(html);
                },
                error: function(data) 
                {
                    toastr.error('Error loading comments');
                }
            });
        }
    </script>
@endsection