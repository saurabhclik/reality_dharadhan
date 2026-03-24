@extends('layouts.app')

@section('title', 'Campaign Reports | Pro-leadexpertz')

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Campaign Reports</h4>
            </div>
            <div class="card-body">
                <div class="d-flex gap-2 mb-3">
                    <button class="ReportbtnExportExcel shadow btn btn-success btn-sm d-flex align-items-center" data-bs-toggle="tooltip" data-bs-placement="top" title="Export table data to Excel">
                        <i class="fas fa-file-excel me-2"></i> Excel
                    </button>

                    <button class="ReportbtnExportPDF shadow btn btn-danger btn-sm d-flex align-items-center" data-bs-toggle="tooltip" data-bs-placement="top" title="Export table data to PDF">
                        <i class="fas fa-file-pdf me-2"></i> PDF
                    </button>
                </div>
                <table class="table table-bordered table-hover" id="table">
                    <thead>
                        <tr>
                            <th>Campaign</th>
                            <th>New Leads</th>
                            <th>Pending</th>
                            <th>Processing</th>
                            <th>Whatsapp Schedule</th>
                            <th>Meeting Schedule</th>
                            <th>Call Scheduled</th>
                            <th>Interested</th>
                            <th>Not Reachable</th>
                            <th>Wrong Number</th>
                            <th>Not Interested</th>
                            <th>Not Picked</th>
                            <th>Lost</th>
                            <th>Booked</th>
                            <th>Completed</th>
                            <th>Cancelled</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($reports as $report)
                        <tr>
                            <td>{{ $report->campaign }}</td>
                            <td>{{ $report->new_leads }}</td>
                            <td>{{ $report->pending }}</td>
                            <td>{{ $report->processing }}</td>
                            <td>{{ $report->whatsapp }}</td>
                            <td>{{ $report->meeting_scheduled }}</td>
                            <td>{{ $report->call_scheduled }}</td>
                            <td>{{ $report->interested }}</td>
                            <td>{{ $report->not_reachable }}</td>
                            <td>{{ $report->wrong_number }}</td>
                            <td>{{ $report->not_interested }}</td>
                            <td>{{ $report->not_picked }}</td>
                            <td>{{ $report->lost }}</td>
                            <td>{{ $report->booked }}</td>
                            <td>{{ $report->completed }}</td>
                            <td>{{ $report->cancelled }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Total</th>
                            <th>{{ $totals['new_leads'] }}</th>
                            <th>{{ $totals['pending'] }}</th>
                            <th>{{ $totals['processing'] }}</th>
                            <th>{{ $totals['whatsapp'] }}</th>
                            <th>{{ $totals['meeting_scheduled'] }}</th>
                            <th>{{ $totals['call_scheduled'] }}</th>
                            <th>{{ $totals['interested'] }}</th>
                            <th>{{ $totals['not_reachable'] }}</th>
                            <th>{{ $totals['wrong_number'] }}</th>
                            <th>{{ $totals['not_interested'] }}</th>
                            <th>{{ $totals['not_picked'] }}</th>
                            <th>{{ $totals['lost'] }}</th>
                            <th>{{ $totals['booked'] }}</th>
                            <th>{{ $totals['completed'] }}</th>
                            <th>{{ $totals['cancelled'] }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    <div class="d-flex justify-content-end mt-3">
        {!! $reports->links('pagination::bootstrap-5') !!}
    </div>
</div>
@endsection
