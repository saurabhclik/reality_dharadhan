@extends('layouts.app')

@section('title', 'Monthly Attendance | Pro-leadexpertz')

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Monthly Attendance Report</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('dashboard') }}">Dashboard</a>
                            </li>
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
                        <h5 class="card-title mb-4">Filter Options</h5>
                        <form method="GET" action="{{ route('attendance.monthly') }}" class="row g-3">
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
                            <div class="col-md-3 d-flex align-items-end">
                                <div>
                                    <button type="submit" class="btn btn-primary" id="SubmitBtn">
                                        <span id="SubmitText">Generate Report</span>
                                        <span id="SubmitSpinner" class="d-none">
                                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Please wait...
                                        </span>
                                    </button>
                                    <a href="{{ route('attendance.monthly') }}" class="btn btn-outline-secondary ms-2">
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
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="card-title mb-0">Attendance Details</h5>
                            <div>
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
                                            <th class="text-center {{ $date->isSunday() ? 'bg-light-danger text-danger' : '' }}">
                                                <div>{{ $date->format('d') }}</div>
                                                <div class="small">{{ $date->format('D') }}</div>
                                            </th>
                                        @endforeach
                                        <th class="text-center bg-light-primary">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($employees as $index => $emp)
                                        @php
                                            $totalHours = collect($data[$emp->id])->sum('hours');
                                            $workingDays = $dates->filter(fn($date) => !$date->isSunday())->count();
                                            $attendancePercentage = $workingDays > 0 ? round(($totalHours / $workingDays) * 100, 1) : 0;
                                        @endphp
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $emp->name }}</td>
                                            @foreach($data[$emp->id] as $dayIndex => $info)
                                                @php
                                                    $hours = $info['hours'];
                                                    $status = $info['status'];
                                                    $date = $dates[$dayIndex];
                                                @endphp

                                                @if($date->isSunday())
                                                    <td class="text-center bg-light-secondary">
                                                        <span class="badge bg-light text-muted">Sun</span>
                                                    </td>
                                                @elseif($status === 'Absent')
                                                    <td class="text-center bg-light-danger">
                                                        <span class="badge bg-danger">A</span>
                                                    </td>
                                                @elseif($status === 'Half Day')
                                                    <td class="text-center bg-light-warning">
                                                        <span class="badge bg-warning">H</span>
                                                    </td>
                                                @elseif($status === 'Full Day')
                                                    <td class="text-center bg-light-success">
                                                        <span class="badge bg-success">P</span>
                                                    </td>
                                                @else
                                                    <td class="text-center bg-light">
                                                        <span class="badge bg-secondary">?</span>
                                                    </td>
                                                @endif
                                            @endforeach
                                            <td class="text-center bg-light-primary fw-bold">
                                                {{ $totalHours }}h
                                                <div class="progress mt-1" style="height: 5px;">
                                                    <div class="progress-bar bg-primary" role="progressbar" 
                                                    style="width: {{ $attendancePercentage }}%" 
                                                    aria-valuenow="{{ $attendancePercentage }}" 
                                                    aria-valuemin="0" aria-valuemax="100">
                                                    </div>
                                                </div>
                                                <small class="text-muted">{{ $attendancePercentage }}%</small>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">
                            <div class="mt-4">
                            <h6 class="text-muted">Attendance Types:</h6>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach ($attendanceTypes as $type)
                                    @php
                                        $label = strtoupper(substr($type->type, 0, 1)); 
                                        $badgeClass = 'bg-secondary';
                                        if (stripos($type->type, 'full') !== false) 
                                        {
                                            $badgeClass = 'bg-success';
                                        } 
                                        elseif (stripos($type->type, 'half') !== false) 
                                        {
                                            $badgeClass = 'bg-warning';
                                        } 
                                        elseif (stripos($type->type, 'absent') !== false) 
                                        {
                                            $badgeClass = 'bg-danger';
                                        }
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">{{ $label }}</span>
                                    <small>{{ ucwords($type->type) }} ({{ $type->hours }} hrs)</small>
                                @endforeach
                                <span class="badge bg-light-secondary text-muted ms-2">Sun</span>
                                <small>Sunday</small>
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
                    <p class="text-muted">Please select year and month to view attendance report.</p>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<script>
    function exportToExcel() 
    {
        const table = document.getElementById("tbl_exporttable_to_xls");
        const wb = XLSX.utils.table_to_book(table, { sheet: "Attendance" });
        XLSX.writeFile(wb, 'Monthly_Attendance_Report.xlsx');
    }
    
    $('#SubmitBtn').closest('form').on('submit', function ()
    {
        $('#SubmitBtn').prop('disabled', true);
        $('#SubmitText').addClass('d-none');
        $('#SubmitSpinner').removeClass('d-none');
    });
</script>
@endsection
