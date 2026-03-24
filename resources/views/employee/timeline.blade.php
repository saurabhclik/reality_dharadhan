@extends('layouts.app')

@section('title', 'Timeline | Pro-leadexpertz')

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Timeline Report</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Timeline</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Filter Options</h5>
                        <form method="GET" action="{{ route('employee.timeline') }}" class="row g-3">
                            <div class="col-md-3">
                                <label for="year" class="form-label">Year</label>
                                <select name="year" id="year" class="form-select" required>
                                    <option value="">-- Select Year --</option>
                                    @for ($y = now()->year; $y >= 2021; $y--)
                                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label for="month" class="form-label">Month</label>
                                <select name="month" id="month" class="form-select" required>
                                    <option value="">-- Select Month --</option>
                                    @for ($m = 1; $m <= 12; $m++)
                                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                            {{ \Carbon\Carbon::createFromDate(null, $m, 1)->format('F') }}
                                        </option>
                                    @endfor
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label for="employee" class="form-label">Employee</label>
                                <select name="employee" id="employee" class="select2">
                                    <option value="">-- All Employees --</option>
                                    @foreach($employees as $emp)
                                        <option value="{{ $emp->id }}" {{ $employeeId == $emp->id ? 'selected' : '' }}>
                                            {{ $emp->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3 d-flex align-items-end">
                                <div>
                                    <button type="submit" class="btn btn-primary" id="SubmitBtn">
                                        <span id="SubmitText">Generate Report</span>
                                        <span id="SubmitSpinner" class="d-none">
                                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Please wait...
                                        </span>
                                    </button>
                                    <a href="{{ route('employee.timeline') }}" class="btn btn-outline-secondary ms-2">
                                        <i class="ri-refresh-line align-middle me-1"></i> Reset
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @if(count($dates) > 0)
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="card-title mb-0">Tracking Details for {{ \Carbon\Carbon::createFromDate($year, $month, 1)->format('F Y') }}</h5>
                            <div>
                                <button class="btn btn-sm btn-outline-primary" onclick="window.print()">
                                    <i class="ri-printer-line align-middle me-1"></i> Print
                                </button>
                                <button class="btn btn-sm btn-outline-success ms-2" onclick="exportToExcel()">
                                    <i class="ri-file-excel-line align-middle me-1"></i> Export
                                </button>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="tbl_exporttable_to_xls">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Employee Name</th>
                                        @foreach($dates as $date)
                                            <th class="text-center {{ $date->isSunday() ? 'bg-light-secondary text-muted' : '' }}">
                                                <div>
                                                    <a href="{{ route('employee.timeline', array_merge(request()->all(), ['day' => $date->format('Y-m-d')])) }}">
                                                        {{ $date->format('d') }}
                                                    </a>
                                                </div>
                                                <div class="small">{{ $date->format('D') }}</div>
                                            </th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($employees as $index => $emp)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $emp->name }}</td>
                                            @foreach($dates as $date)
                                                @php
                                                    $dateStr = $date->format('Y-m-d');
                                                    $records = $trackingData[$emp->id][$dateStr] ?? [];
                                                @endphp
                                                <td class="text-center">
                                                    @if(count($records) > 0)
                                                        @foreach($records as $rec)
                                                            <div class="small mb-1">
                                                                <strong>{{ \Carbon\Carbon::parse($rec->start_time)->format('H:i') }}</strong> → 
                                                                <strong>{{ \Carbon\Carbon::parse($rec->end_time)->format('H:i') }}</strong><br>
                                                                <span class="text-muted small">
                                                                    Start: {{ $rec->start_location }}<br>
                                                                    End: {{ $rec->end_location }}
                                                                </span>
                                                                <br>
                                                                <a href="https://www.google.com/maps/search/?api=1&query={{ $rec->start_location }}" 
                                                                target="_blank" class="btn btn-sm btn-outline-primary mt-1">Start Track</a>
                                                                <a href="https://www.google.com/maps/search/?api=1&query={{ $rec->end_location }}" 
                                                                target="_blank" class="btn btn-sm btn-outline-success mt-1">End Track</a>
                                                            </div>
                                                        @endforeach
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">
                            <h6 class="text-muted">Timeline:</h6>
                            <div class="d-flex flex-wrap gap-2">
                                <span class="badge bg-success">✔</span> <small>Tracked</small>
                                <span class="badge bg-danger ms-2">-</span> <small>No Record</small>
                                <span class="badge bg-light-secondary text-muted ms-2">Sun</span> <small>Sunday</small>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        @else
            <div class="row">
                <div class="col-12">
                    <div class="card text-center py-5">
                        <div class="avatar-lg mx-auto mb-4">
                            <div class="avatar-title bg-light text-primary display-4 rounded-circle">
                                <i class="ri-calendar-line"></i>
                            </div>
                        </div>
                        <h5>No data available</h5>
                        <p class="text-muted">Please select year and month to view tracking report.</p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
<script>
     $('#SubmitBtn').closest('form').on('submit', function ()
    {
        $('#SubmitBtn').prop('disabled', true);
        $('#SubmitText').addClass('d-none');
        $('#SubmitSpinner').removeClass('d-none');
    });
</script>
@endsection
