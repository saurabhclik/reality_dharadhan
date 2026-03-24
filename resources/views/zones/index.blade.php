@extends('layouts.app')

@section('title', 'Zones Management | Pro-leadexpertz')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">Zones Management</h5>
                            <div>
                                <button type="button" class="btn btn-success btn-sm me-2" data-bs-toggle="modal" data-bs-target="#importModal">
                                    <i class="fas fa-file-import"></i> Bulk Import
                                </button>
                                <a href="{{ route('zone.create') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus"></i> Add New Zone
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('zone.index') }}" method="GET" class="mb-4">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <input type="text" class="form-control" name="search"
                                        placeholder="Search by zone, sub area, pincode, district..."
                                        value="{{ request('search') }}">
                                    </div>
                                    <div class="col-md-3">
                                        <select class="form-select" name="city_id">
                                            <option value="">All Districts</option>
                                            @foreach($cities as $city)
                                                <option value="{{ $city->id }}" {{ request('city_id') == $city->id ? 'selected' : '' }}>
                                                    {{ $city->city_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <select class="form-select" name="status">
                                            <option value="">All Status</option>
                                            <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                                            <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-search"></i> Search
                                        </button>
                                        <a href="{{ route('zone.index') }}" class="btn btn-secondary">
                                            <i class="fas fa-redo"></i> Reset
                                        </a>
                                    </div>
                                </div>
                            </form>
                            <div class="table-responsive">
                                <table id="table" class="table-hover table-bordered dt-responsive nowrap w-100">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="50">#</th>
                                            <th>
                                                <a href="{{ route('zone.index', array_merge(request()->query(), ['sort_field' => 'zone_name', 'sort_direction' => request('sort_direction') == 'asc' ? 'desc' : 'asc'])) }}" class="text-dark text-decoration-none">
                                                    Zone Name
                                                    @if(request('sort_field') == 'zone_name')
                                                        <i class="fas fa-sort-{{ request('sort_direction') == 'asc' ? 'up' : 'down' }}"></i>
                                                    @endif
                                                </a>
                                            </th>
                                            <th>City</th>
                                            <th>Sub Area</th>
                                            <th>Pincode</th>
                                            <th width="100">
                                                <a href="{{ route('zone.index', array_merge(request()->query(), ['sort_field' => 'zone_order', 'sort_direction' => request('sort_direction') == 'asc' ? 'desc' : 'asc'])) }}" class="text-dark text-decoration-none">
                                                    Order
                                                    @if(request('sort_field') == 'zone_order')
                                                        <i class="fas fa-sort-{{ request('sort_direction') == 'asc' ? 'up' : 'down' }}"></i>
                                                    @endif
                                                </a>
                                            </th>
                                            <th width="100">Status</th>
                                            <th width="150">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($zones as $index => $zone)
                                            <tr>
                                                <td>{{ $zones->firstItem() + $index }}</td>
                                                <td>{{ $zone->zone_name }}</td>
                                                <td>{{ $zone->district_name ?? 'N/A' }}</td>
                                                <td>{{ $zone->sub_area ?? 'N/A' }}</td>
                                                <td>{{ $zone->pincode ?? 'N/A' }}</td>
                                                <td class="text-center">{{ $zone->zone_order }}</td>
                                                <td class="text-center">
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input status-toggle" type="checkbox" 
                                                        data-id="{{ $zone->id }}"
                                                        {{ $zone->status ? 'checked' : '' }}>
                                                    </div>
                                                </td>
                                                <td>
                                                    <a href="{{ route('zone.edit', $zone->id) }}" class="btn btn-sm btn-primary" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-danger delete-btn" 
                                                        data-id="{{ $zone->id }}"
                                                        data-name="{{ $zone->zone_name }}"
                                                        title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div> 
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div>
                                Showing {{ $zones->firstItem() ?? 0 }} to {{ $zones->lastItem() ?? 0 }} of {{ $zones->total() }} entries
                            </div>
                            <div>
                                {{ $zones->appends(request()->query())->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('modals.import-zone')

    @include('modals.delete-zone')

    <script>
        $(document).ready(function() 
        {
            $('#importModal form').on('submit', function() 
            {
                $('#SubmitBtn').prop('disabled', true);
                $('#SubmitText').addClass('d-none');
                $('#SubmitSpinner').removeClass('d-none');
            });
            $('.status-toggle').on('change', function() 
            {
                const zoneId = $(this).data('id');
                const checkbox = $(this);
                $.ajax({
                    url: `/zone/${zoneId}/toggle-status`,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) 
                    {
                        if (response.success) 
                        {
                            toastr.success(response.message);
                        }
                    },
                    error: function() 
                    {
                        checkbox.prop('checked', !checkbox.prop('checked'));
                        toastr.error('Failed to update status');
                    }
                });
            });
            $('.delete-btn').on('click', function() 
            {
                const zoneId = $(this).data('id');
                const zoneName = $(this).data('name');
                $('#deleteZoneName').text(zoneName);
                $('#deleteForm').attr('action', `/zone/${zoneId}`);
                $('#deleteModal').modal('show');
            });
        });
    </script>
@endsection