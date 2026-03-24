@extends('layouts.app')

@section('title', 'MIS Summary Report')
@section('content')
<style>
    #table
    {
        width:100% important;
    }
    .info-box 
    {
        box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
        border-radius: 0.25rem;
        background: #fff;
        display: flex;
        margin-bottom: 1rem;
        min-height: 80px;
        padding: 0.5rem;
        position: relative;
    }
    .info-box .info-box-icon 
    {
        border-radius: 0.25rem;
        align-items: center;
        display: flex;
        font-size: 1.875rem;
        justify-content: center;
        text-align: center;
        width: 70px;
    }
    .info-box .info-box-content 
    {
        display: flex;
        flex-direction: column;
        justify-content: center;
        line-height: 1.8;
        flex: 1;
        padding: 0 10px;
    }
    .info-box-text 
    {
        text-transform: uppercase;
        font-size: 0.875rem;
    }
    .info-box-number 
    {
        font-weight: 700;
        font-size: 1.5rem;
    }
    .bg-info 
    { 
        background-color: #17a2b8 !important; color: white; 
    }
    .bg-success 
    { 
        background-color: #28a745 !important; color: white; 
    }
    .bg-warning 
    { 
        background-color: #ffc107 !important; color: black; 
    }
    .bg-danger 
    { 
        background-color: #dc3545 !important; color: white; 
    }
</style>
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">MIS Summary Report</h3>
                    </div>
                    <div class="card-body">
                        @if($userType == 'super_admin' || $userType == 'divisional_head')
                        <form method="GET" action="{{ route('mis.summary-report') }}" class="mb-4">
                            <div class="row">
                                <div class="col-md-3">
                                    <label for="team_id">Team</label>
                                    <select name="team_id" id="team_id" class="form-control select2">
                                        <option value="">All Teams</option>
                                        @foreach($teams as $team)
                                            <option value="{{ $team->id }}" {{ $teamFilter == $team->id ? 'selected' : '' }}>
                                                {{ $team->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label for="year">Year</label>
                                    <select name="year" id="year" class="form-control select2">
                                        @for($y = date('Y'); $y >= 2020; $y--)
                                            <option value="{{ $y }}" {{ $yearFilter == $y ? 'selected' : '' }}>{{ $y }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label for="month">Month</label>
                                    <select name="month" id="month" class="form-control select2">
                                        <option value="">All Months</option>
                                        @for($m = 1; $m <= 12; $m++)
                                            <option value="{{ $m }}" {{ $monthFilter == $m ? 'selected' : '' }}>
                                                {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label for="week">Date Range</label>
                                    <select name="week" id="week" class="form-control select2">
                                        <option value="">Select Date Range</option>
                                        @foreach ($weeks as $week)
                                            <option value="{{ $week['number'] }}" 
                                                {{ $weekFilter == $week['number'] ? 'selected' : '' }}>
                                                {{ $week['label'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3 d-flex align-items-end gap-2">
                                    <button type="submit" class="btn btn-primary" id="SubmitBtn">
                                        <span id="SubmitText">Apply Filters</span>
                                        <span id="SubmitSpinner" class="d-none">
                                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Please wait...
                                        </span>
                                    </button>
                                    <a href="{{ route('mis.summary-report') }}" class="btn btn-secondary">Reset</a>
                                </div>
                            </div>
                        </form>
                        @endif
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="info-box bg-info">
                                    <span class="info-box-icon"><i class="fas fa-bullseye"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Total Target</span>
                                        <span class="info-box-number">{{ number_format($totalTarget) }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-box bg-success">
                                    <span class="info-box-icon"><i class="fas fa-chart-line"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Total Achieved</span>
                                        <span class="info-box-number">{{ number_format($totalAchieved) }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-box {{ $overallPercentage >= 100 ? 'bg-success' : ($overallPercentage >= 80 ? 'bg-warning' : 'bg-danger') }}">
                                    <span class="info-box-icon"><i class="fas fa-percentage"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Overall Achievement</span>
                                        <span class="info-box-number">{{ $overallPercentage }}%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table id="table" class="table table-bordered table-striped">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>MIS Point</th>
                                        <th>Target</th>
                                        <th>Achieved</th>
                                        <th>Achievement %</th>
                                        <th>Variance</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($points as $point)
                                        @php
                                            $data = $summaryData[$point] ?? ['target' => 0, 'achieved' => 0, 'percentage' => 0, 'variance' => 0];
                                            $statusClass = $data['percentage'] >= 100 ? 'success' : 
                                            ($data['percentage'] >= 80 ? 'warning' : 'danger');
                                        @endphp
                                        <tr>
                                            <td><strong>{{ $point }}</strong></td>
                                            <td>{{ number_format($data['target']) }}</td>
                                            <td>{{ number_format($data['achieved']) }}</td>
                                            <td>
                                                <div class="progress progress-sm">
                                                    <div class="progress-bar bg-{{ $statusClass }}" 
                                                        style="width: {{ min($data['percentage'], 100) }}%">
                                                    </div>
                                                </div>
                                                <span class="badge bg-{{ $statusClass }}">
                                                    {{ $data['percentage'] }}%
                                                </span>
                                            </td>
                                            <td class="{{ $data['variance'] >= 0 ? 'text-success' : 'text-danger' }}">
                                                {{ $data['variance'] >= 0 ? '+' : '' }}{{ number_format($data['variance']) }}
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $statusClass }}">
                                                    @if($data['percentage'] >= 100)
                                                        Exceeded
                                                    @elseif($data['percentage'] >= 80)
                                                        On Track
                                                    @else
                                                        Behind
                                                    @endif
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if($userType == 'super_admin' || $userType == 'divisional_head')
                        <div class="mt-3">
                            <button id="btnExport" class="btn btn-success">
                                <i class="fas fa-file-excel me-1"></i>
                                <span id="ExportText">Export to Excel</span>
                                <span id="ExportSpinner" class="d-none">
                                    <i class="fas fa-spinner fa-spin me-1"></i> Please wait...
                                </span>
                            </button>
                        </div>
                        @endif
                    </div>
                    @if($userType != 'super_admin' && $userType != 'divisional_head')
                        @include('partial.schedule')
                        @include('partial.task-details')
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('btnExport').addEventListener('click', function() 
    {
        const btn = this;
        const text = document.getElementById('ExportText');
        const spinner = document.getElementById('ExportSpinner');
        btn.disabled = true;
        text.classList.add('d-none');
        spinner.classList.remove('d-none');

        setTimeout(() => {
            const table = document.querySelector('#table');
            if (!table) 
            {
                toastr.error('Table not found!');
                btn.disabled = false;
                text.classList.remove('d-none');
                spinner.classList.add('d-none');
                return;
            }

            let csv = [];
            const rows = table.querySelectorAll('tr');

            rows.forEach(row => {
                const cols = row.querySelectorAll('th, td');
                const rowData = [];
                cols.forEach(col => {
                    let data = col.innerText.replace(/"/g, '""');
                    rowData.push(`"${data}"`);
                });
                csv.push(rowData.join(','));
            });

            const csvString = csv.join('\n');
            const blob = new Blob([csvString], { type: 'text/csv;charset=utf-8;' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `MIS_Summary_Report_{{ now()->format('Y_m_d') }}.csv`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);

            btn.disabled = false;
            text.classList.remove('d-none');
            spinner.classList.add('d-none');
        }, 200);
    });

    $('#SubmitBtn').closest('form').on('submit', function() 
    {
        $('#SubmitBtn').prop('disabled', true);
        $('#SubmitText').addClass('d-none');
        $('#SubmitSpinner').removeClass('d-none');
    });
</script>
@endsection