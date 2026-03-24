@extends('layouts.app')

@section('title', 'MIS Daily Report')

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">MIS Daily Report</h3>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('mis.daily-report') }}" class="mb-4">
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
                                <div class="col-md-3">
                                    <label for="date">Date</label>
                                    <input type="date" name="date" id="date" class="form-control" 
                                        value="{{ $dateFilter }}">
                                </div>
                                <!-- <div class="col-md-2">
                                    <label for="week">Week</label>
                                    <select name="week" id="week" class="form-control select2">
                                        <option value="">All Weeks</option>
                                        @for($w = 1; $w <= 53; $w++)
                                            <option value="{{ $w }}" {{ $weekFilter == $w ? 'selected' : '' }}>
                                                Week {{ $w }}
                                            </option>
                                        @endfor
                                    </select>
                                </div> -->
                                <div class="col-md-4 d-flex align-items-end gap-3">
                                    <button type="submit" class="btn btn-primary" id="SubmitBtn">
                                        <span id="SubmitText">Apply Filters</span>
                                        <span id="SubmitSpinner" class="d-none">
                                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Please wait...
                                        </span>
                                    </button>
                                    <a href="{{ route('mis.daily-report') }}" class="btn btn-secondary mr-2">Reset</a>                                   
                                </div>
                            </div>
                        </form>
                        @if(count($dailyData) > 0)
                            <div class="mt-4">
                                <h4>Date-wise Summary</h4>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped" id="dailyReportTable">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th>Date</th>
                                                @foreach($points as $point)
                                                    <th>{{ $point }}</th>
                                                @endforeach
                                                <th>Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($dateWiseData as $date => $tasks)
                                                @php
                                                    $dailyTotal = 0;
                                                @endphp
                                                <tr>
                                                    <td><strong>{{ \Carbon\Carbon::parse($date)->format('M d, Y') }}</strong></td>
                                                    @foreach($points as $point)
                                                        @php
                                                            $value = $tasks[$point] ?? 0;
                                                            $dailyTotal += $value;
                                                        @endphp
                                                        <td>{{ $value }}</td>
                                                    @endforeach
                                                    <td><strong>{{ $dailyTotal }}</strong></td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr class="table-info">
                                                <td><strong>Total</strong></td>
                                                @foreach($points as $point)
                                                    @php
                                                        $pointTotal = 0;
                                                        foreach ($dateWiseData as $tasks) {
                                                            $pointTotal += $tasks[$point] ?? 0;
                                                        }
                                                    @endphp
                                                    <td><strong>{{ $pointTotal }}</strong></td>
                                                @endforeach
                                                @php
                                                    $grandTotal = 0;
                                                    foreach ($points as $point) {
                                                        foreach ($dateWiseData as $tasks) {
                                                            $grandTotal += $tasks[$point] ?? 0;
                                                        }
                                                    }
                                                @endphp
                                                <td><strong>{{ $grandTotal }}</strong></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                <div class="mt-3">
                                    <button type="button" id="btnExportDaily" class="btn btn-success">
                                        <i class="fas fa-file-excel me-1"></i>
                                        <span id="ExportDailyText">Export to Excel</span>
                                        <span id="ExportDailySpinner" class="d-none">
                                            <i class="fas fa-spinner fa-spin me-1"></i> Please wait...
                                        </span>
                                    </button>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-info text-center">
                                <h4>No daily entries found</h4>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('btnExportDaily').addEventListener('click', function() 
    {
        const btn = this;
        const text = document.getElementById('ExportDailyText');
        const spinner = document.getElementById('ExportDailySpinner');
        btn.disabled = true;
        text.classList.add('d-none');
        spinner.classList.remove('d-none');

        setTimeout(() => {
            const table = document.getElementById('dailyReportTable');
            if (!table) 
            {
                toastr.error('No table found to export!');
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
            a.download = `MIS_Daily_Report_{{ now()->format("Y_m_d") }}.csv`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);

            btn.disabled = false;
            text.classList.remove('d-none');
            spinner.classList.add('d-none');
        }, 800);
    });

    document.getElementById('date').addEventListener('change', function() 
    {
        this.form.submit();
    });
    $('#SubmitBtn').closest('form').on('submit', function() 
    {
        $('#SubmitBtn').prop('disabled', true);
        $('#SubmitText').addClass('d-none');
        $('#SubmitSpinner').removeClass('d-none');
    });
</script>

<style>
    .card 
    {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        margin-bottom: 1rem;
    }
    .card-header 
    {
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
    }
    .table th 
    {
        background-color: #343a40;
        color: white;
        font-weight: 600;
    }
    .badge 
    {
        font-size: 0.85em;
    }
</style>
@endsection