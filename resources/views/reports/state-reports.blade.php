@extends('layouts.app')

@section('content')
<div class="page-content">
    <div class="container-fluid mt-4">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">State-wise Reports</h4>
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
                <div class="table table-bordered dt-responsive nowrap w-100">
                    <table class="table table-bordered table-striped" id="table">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>{{ $fieldLabel }}</th>
                                <th>New Leads</th>
                                <th>Pending</th>
                                <th>Processing</th>
                                <th>Interested</th>
                                <th>Whatsapp Schedule</th>
                                <th>Meeting Schedule</th>
                                <th>Call Schedule</th>
                                <th>Visit Schedule</th>
                                <th>Visit Done</th>
                                <th>Booked</th>
                                <th>Completed</th>
                                <th>Cancelled</th>
                                <th>Not Reachable</th>
                                <th>Wrong Number</th>
                                <th>Channel Partner</th>
                                <th>Not Interested</th>
                                <th>Not Picked</th>
                                <th>Lost</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($reports as $index => $report)
                            <tr>
                                <td>{{ $reports->firstItem() + $index }}</td>
                                <td>{{ $report->field2 }}</td>
                                <td>{{ $report->new_leads }}</td>
                                <td>{{ $report->pending }}</td>
                                <td>{{ $report->processing }}</td>
                                <td>{{ $report->interested }}</td>
                                <td>{{ $report->whatsapp }}</td>
                                <td>{{ $report->meeting_scheduled }}</td>
                                <td>{{ $report->call_scheduled }}</td>
                                <td>{{ $report->visit_scheduled }}</td>
                                <td>{{ $report->visit_done }}</td>
                                <td>{{ $report->booked }}</td>
                                <td>{{ $report->completed }}</td>
                                <td>{{ $report->cancelled }}</td>
                                <td>{{ $report->not_reachable }}</td>
                                <td>{{ $report->wrong_number }}</td>
                                <td>{{ $report->channel_partner }}</td>
                                <td>{{ $report->not_interested }}</td>
                                <td>{{ $report->not_picked }}</td>
                                <td>{{ $report->lost }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="18" class="text-center">No records found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div>
            {!! $reports->links('pagination::bootstrap-5') !!}
        </div>
    </div>
</div>
@endsection
