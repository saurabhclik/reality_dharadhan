@extends('layouts.app')

@section('title', 'Smart Lead Segmentation | Pro-leadexpertz')
@section('content')
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Smart Lead Segmentation</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Reports</a></li>
                            <li class="breadcrumb-item active">Smart Lead</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body bg-light">
                        <form method="GET" id="filterForm">
                            @csrf
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <select class="form-control" name="lead_source" id="lead_source">
                                        <option value="">Select Source</option>
                                        @foreach($sources as $source)
                                            <option value="{{ $source->name }}" {{ request('lead_source') == $source->name ? 'selected' : '' }}>
                                                {{ $source->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <select class="form-control" name="lead_category_type" id="lead_category_type">
                                        <option value="">Select Type</option>
                                        <option value="Residential" {{ request('lead_category_type') == 'Residential' ? 'selected' : '' }}>Residential</option>
                                        <option value="Commercial" {{ request('lead_category_type') == 'Commercial' ? 'selected' : '' }}>Commercial</option>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <select class="form-control" name="lead_category_id" id="lead_category_id">
                                        <option value="">Select Category</option>
                                        @if(request('lead_category_id'))
                                            <option value="{{ request('lead_category_id') }}" selected>
                                                {{ DB::table('inv_catg')->find(request('lead_category_id'))->name ?? '' }}
                                            </option>
                                        @endif
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <select class="form-control" name="lead_sub_category_id" id="lead_sub_category_id">
                                        <option value="">Select Sub Category</option>
                                        @if(request('lead_sub_category_id'))
                                            <option value="{{ request('lead_sub_category_id') }}" selected>
                                                {{ DB::table('inv_subcatg')->find(request('lead_sub_category_id'))->name ?? '' }}
                                            </option>
                                        @endif
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <select class="form-control" name="lead_classification" id="lead_classification">
                                        <option value="">Select Classification</option>
                                        <option value="hot" {{ request('lead_classification') == 'hot' ? 'selected' : '' }}>Hot</option>
                                        <option value="cold" {{ request('lead_classification') == 'cold' ? 'selected' : '' }}>Cold</option>
                                        <option value="warm" {{ request('lead_classification') == 'warm' ? 'selected' : '' }}>Warm</option>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <select class="form-control" name="lead_project" id="lead_project">
                                        <option value="">Select Project</option>
                                        @foreach($projects as $project)
                                            <option value="{{ $project->project_name }}" {{ request('lead_project') == $project->project_name ? 'selected' : '' }}>
                                                {{ $project->project_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <select class="form-control" name="lead_campaign" id="lead_campaign">
                                        <option value="">Select Campaign</option>
                                        @foreach($campaigns as $campaign)
                                            <option value="{{ $campaign->name }}" {{ request('lead_campaign') == $campaign->name ? 'selected' : '' }}>
                                                {{ $campaign->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <select class="form-control" name="lead_state" id="lead_state">
                                        <option value="">Select State</option>
                                        @foreach($states as $state)
                                            <option value="{{ $state->state }}" {{ request('lead_state') == $state->state ? 'selected' : '' }}>
                                                {{ $state->state }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <select class="form-control" name="lead_city" id="lead_city">
                                        <option value="">Select City</option>
                                        @foreach($cities as $city)
                                            <option value="{{ $city->District }}" {{ request('lead_city') == $city->District ? 'selected' : '' }}>
                                                {{ $city->District }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <select class="form-control" name="lead_budget" id="lead_budget">
                                        <option value="">Select Budget</option>
                                        <option value="5-10L" {{ request('lead_budget') == '5-10L' ? 'selected' : '' }}>5-10L</option>
                                        <option value="10-20L" {{ request('lead_budget') == '10-20L' ? 'selected' : '' }}>10-20L</option>
                                        <option value="20-30L" {{ request('lead_budget') == '20-30L' ? 'selected' : '' }}>20-30L</option>
                                        <option value="30-45L" {{ request('lead_budget') == '30-45L' ? 'selected' : '' }}>30-45L</option>
                                        <option value="45-60L" {{ request('lead_budget') == '45-60L' ? 'selected' : '' }}>45-60L</option>
                                        <option value="60-80L" {{ request('lead_budget') == '60-80L' ? 'selected' : '' }}>60-80L</option>
                                        <option value="80-1cr" {{ request('lead_budget') == '80-1cr' ? 'selected' : '' }}>80-1cr</option>
                                        <option value="1-1.25cr" {{ request('lead_budget') == '1-1.25cr' ? 'selected' : '' }}>1-1.25cr</option>
                                        <option value="1.25-1.5cr" {{ request('lead_budget') == '1.25-1.5cr' ? 'selected' : '' }}>1.25-1.5cr</option>
                                        <option value="1.5-1.75cr" {{ request('lead_budget') == '1.5-1.75cr' ? 'selected' : '' }}>1.5-1.75cr</option>
                                        <option value="1.75-2cr" {{ request('lead_budget') == '1.75-2cr' ? 'selected' : '' }}>1.75-2cr</option>
                                        <option value="2-2.50cr" {{ request('lead_budget') == '2-2.50cr' ? 'selected' : '' }}>2-2.50cr</option>
                                        <option value="2.50-3cr" {{ request('lead_budget') == '2.50-3cr' ? 'selected' : '' }}>2.50-3cr</option>
                                        <option value="3-3.50cr" {{ request('lead_budget') == '3-3.50cr' ? 'selected' : '' }}>3-3.50cr</option>
                                        <option value="3.50-4cr" {{ request('lead_budget') == '3.50-4cr' ? 'selected' : '' }}>3.50-4cr</option>
                                        <option value="4-4.50cr" {{ request('lead_budget') == '4-4.50cr' ? 'selected' : '' }}>4-4.50cr</option>
                                        <option value="4.50-5cr" {{ request('lead_budget') == '4.50-5cr' ? 'selected' : '' }}>4.50-5cr</option>
                                        <option value="5-6cr" {{ request('lead_budget') == '5-6cr' ? 'selected' : '' }}>5-6cr</option>
                                        <option value="6-7cr" {{ request('lead_budget') == '6-7cr' ? 'selected' : '' }}>6-7cr</option>
                                        <option value="7-8cr" {{ request('lead_budget') == '7-8cr' ? 'selected' : '' }}>7-8cr</option>
                                        <option value="8-9cr" {{ request('lead_budget') == '8-9cr' ? 'selected' : '' }}>8-9cr</option>
                                        <option value="9-10cr" {{ request('lead_budget') == '9-10cr' ? 'selected' : '' }}>9-10cr</option>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <select class="form-control" name="lead_status" id="lead_status">
                                        <option value="">Select Status</option>
                                        @foreach($status as $status)
                                            <option value="{{ $status->name }}" {{ request('lead_status') == $status->name ? 'selected' : '' }}>
                                                {{ $status->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <select class="form-control" name="lead_agent" id="lead_agent">
                                        <option value="">Select Owner</option>
                                        @foreach($agents as $agent)
                                            <option value="{{ $agent->id }}" {{ request('lead_agent') == $agent->id ? 'selected' : '' }}>
                                                {{ $agent->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3 d-flex align-items-end gap-3">
                                    <button type="submit" name="btnSearch" id="btnSearch" class="btn btn-primary mr-2">Search</button>
                                    <a href="{{ route('report.smart_lead') }}" class="btn btn-secondary">
                                        <i class="fa-solid fa-rotate-right"></i>
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex gap-2 mb-3">
                            <button class="ReportbtnExportExcel shadow btn btn-success btn-sm d-flex align-items-center" data-bs-toggle="tooltip" data-bs-placement="top" title="Export table data to Excel">
                                <i class="fas fa-file-excel me-2"></i> Excel
                            </button>

                            <button class="ReportbtnExportPDF shadow btn btn-danger btn-sm d-flex align-items-center" data-bs-toggle="tooltip" data-bs-placement="top" title="Export table data to PDF">
                                <i class="fas fa-file-pdf me-2"></i> PDF
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table id="table" class="table table-bordered dt-responsive nowrap w-100">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Agent Name</th>
                                        <th>Client Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Source</th>
                                        <th>Campaign</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($results as $key => $row)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <div class="d-flex align-items-center mb-1">
                                                        <div class="flex-grow-1">
                                                            <h6 class="mb-0">{{ $row->agent }}</h6>
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
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $row->client_name }}</td>
                                            <td>{{ $row->email }}</td>
                                            <td>{{ $row->phone }}</td>
                                            <td>{{ $row->source }}</td>
                                            <td>{{ $row->campaign }}</td>
                                            <td>{{ $row->status }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-center mt-3">
                          {!! $results->links('pagination::bootstrap-5') !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() 
    {
        $('#lead_category_type').change(function() 
        {
            let type = $(this).val();
            if (type) {
                $.ajax({
                    method: 'POST',
                    url: '{{ route("get-categories") }}',
                    data: {
                        type: type,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(data) 
                    {
                        $('#lead_category_id').html(data);
                    }
                });
            } 
            else 
            {
                $('#lead_category_id').html('<option value="">Select Category</option>');
                $('#lead_sub_category_id').html('<option value="">Select Sub Category</option>');
            }
        });

        $('#lead_category_id').change(function() 
        {
            let categoryId = $(this).val();
            if (categoryId) 
            {
                $.ajax({
                    method: 'POST',
                    url: '{{ route("get-subcategories") }}',
                    data: {
                        category_id: categoryId,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(data) 
                    {
                        $('#lead_sub_category_id').html(data);
                    }
                });
            } 
            else 
            {
                $('#lead_sub_category_id').html('<option value="">Select Sub Category</option>');
            }
        });

        $('#lead_state').change(function() 
        {
            let state = $(this).val();
            if (state) {
                $.ajax({
                    method: 'POST',
                    url: '{{ route("get-cities") }}',
                    data: {
                        state: state,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(data) 
                    {
                        $('#lead_city').html(data);
                    }
                });
            } 
            else 
            {
                $('#lead_city').html('<option value="">Select City</option>');
            }
        });
    });

    function logCallBeforeRedirect(id, event) 
    {
        event.preventDefault();
        const $btn = $(event.currentTarget);
        const originalHtml = $btn.html();
        $btn.html('<i class="fa fa-spinner fa-spin"></i>');
        
        $.ajax({
            method: 'POST',
            url: '{{ route("log-call") }}',
            data: {
                lead_id: id,
                _token: '{{ csrf_token() }}'
            },
            success: function() 
            {
                window.location.href = $btn.attr('href');
            },
            error: function() 
            {
                $btn.html(originalHtml);
                toastr.error('Error logging call. Please try again.');
            }
        });
    }
</script>
@endsection