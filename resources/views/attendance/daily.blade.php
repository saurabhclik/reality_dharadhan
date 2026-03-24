@extends('layouts.app')

@section('title', 'Daily Attendance | Pro-leadexpertz')

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Daily Attendance</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                            <li class="breadcrumb-item active">Attendance</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form method="GET" action="{{ route('attendance.daily') }}">
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <label for="fromDt" class="form-label">Select Date</label>
                                    <input type="date" class="form-control" name="fromDt" value="{{ request('fromDt') }}">
                                </div>
                                <div class="col-md-3">
                                    <label for="employee" class="form-label">Select Employee</label>
                                    <select name="employee" id="employee" class="form-control">
                                        <option value="">-- ALL --</option>
                                        @foreach($employees as $emp)
                                            <option value="{{ $emp->id }}" {{ request('employee') == $emp->id ? 'selected' : '' }}>
                                                {{ $emp->name }} ({{ $emp->role }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3 align-self-end">
                                    <button type="submit" class="btn btn-primary">Search</button>
                                    <a href="{{ route('attendance.daily') }}" class="btn btn-secondary">Reset</a>
                                </div>
                            </div>
                        </form>
                        <div class="table-responsive">
                            <table class="table table-bordered dt-responsive nowrap w-100" id="daily-attendance">
                                <thead class="table-light">
                                    <tr>
                                        <th>User</th>
                                        <th>Start Time</th>
                                        <th>End Time</th>
                                        <th>Working Hours</th>
                                        <th>Track</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($list as $row)
                                        <tr>
                                            <td>{{ $row->users }}</td>
                                            <td>{{ \Carbon\Carbon::parse($row->start_time)->format('d-M-Y H:i') }}</td>
                                            <td>{{ \Carbon\Carbon::parse($row->end_time)->format('d-M-Y H:i') }}</td>
                                            <td>
                                                @php
                                                    $start = \Carbon\Carbon::parse($row->start_time);
                                                    $end = \Carbon\Carbon::parse($row->end_time);
                                                    $diff = $start->diff($end);
                                                @endphp
                                                {{ $diff->h }} h : {{ $diff->i }} m : {{ $diff->s }} s
                                            </td>
                                            <td>
                                                @php
                                                    $startLoc = trim($row->start_location ?? '');
                                                    $endLoc = trim($row->end_location ?? '');
                                                    $startCoords = null;
                                                    $endCoords = null;
                                                    if (!empty($startLoc)) 
                                                    {
                                                        if (strpos($startLoc, ',') !== false) 
                                                        {
                                                            $startCoords = $startLoc;
                                                        } 
                                                        elseif (is_numeric($startLoc) && is_numeric($endLoc) && strpos($endLoc, ',') === false) 
                                                        {
                                                            $startCoords = $startLoc . ',' . $endLoc;
                                                        }
                                                    }
                                                    if (!empty($endLoc)) 
                                                    {
                                                        if (strpos($endLoc, ',') !== false) 
                                                        {
                                                            $endCoords = $endLoc;
                                                        } 
                                                        elseif (empty($startCoords) && is_numeric($startLoc) && is_numeric($endLoc)) 
                                                        {
                                                            $endCoords = $startLoc . ',' . $endLoc;
                                                        }
                                                    }
                                                    if ($startCoords && !$endCoords) 
                                                    {
                                                        $endCoords = $startCoords;
                                                    }
                                                    if ($endCoords && !$startCoords) 
                                                    {
                                                        $startCoords = $endCoords;
                                                    }
                                                @endphp
                                                
                                                <div class="d-flex flex-column gap-1">
                                                    @if($startCoords)
                                                        <a class="btn btn-primary btn-sm" target="_blank" 
                                                           href="https://www.google.com/maps?q={{ $startCoords }}"
                                                           title="Start Location: {{ $startCoords }}">
                                                            📍 Start Location
                                                        </a>
                                                    @endif
                                                    
                                                    @if($endCoords && $endCoords !== $startCoords)
                                                        <a class="btn btn-success btn-sm" target="_blank" 
                                                           href="https://www.google.com/maps?q={{ $endCoords }}"
                                                           title="End Location: {{ $endCoords }}">
                                                            📍 End Location
                                                        </a>
                                                    @endif
                                                    
                                                    @if($startCoords && $endCoords && $startCoords !== $endCoords)
                                                        <a class="btn btn-warning btn-sm" target="_blank" 
                                                           href="https://www.google.com/maps/dir/{{ $startCoords }}/{{ $endCoords }}"
                                                           title="Route from Start to End">
                                                            🗺️ View Route
                                                        </a>
                                                    @endif
                                                    
                                                    @if(!$startCoords && !$endCoords)
                                                        <span class="text-muted">No location data</span>
                                                        <small class="text-muted">
                                                            Start: {{ $row->start_location ?? 'N/A' }}<br>
                                                            End: {{ $row->end_location ?? 'N/A' }}
                                                        </small>
                                                    @else
                                                        <small class="text-muted">
                                                            @if($startCoords)Start: {{ $startCoords }}@endif
                                                            @if($endCoords && $endCoords !== $startCoords)<br>End: {{ $endCoords }}@endif
                                                        </small>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="d-flex justify-content-end mt-3">
                                {{ $list->appends(request()->all())->links('pagination::bootstrap-5') }}
                            </div>
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
        var table = $('#daily-attendance').DataTable({
            scrollX: true,
            scrollCollapse: true,
            fixedColumns: {
                leftColumns: 3,
                rightColumns: 1
            }
        });
    });
</script>
@endsection