@extends('layouts.app')

@section('title', 'Source-wise Reports | Pro-leadexpertz')

@section('content')
<div class="page-content">
    <div class="container-fluid mt-4">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Source-wise Reports</h4>
            </div>
            <div class="card-body table-responsive">
                <div class="d-flex gap-2 mb-3">
                    <button class="ReportbtnExportExcel shadow btn btn-success btn-sm d-flex align-items-center" data-bs-toggle="tooltip" data-bs-placement="top" title="Export table data to Excel">
                        <i class="fas fa-file-excel me-2"></i> Excel
                    </button>

                    <button class="ReportbtnExportPDF shadow btn btn-danger btn-sm d-flex align-items-center" data-bs-toggle="tooltip" data-bs-placement="top" title="Export table data to PDF">
                        <i class="fas fa-file-pdf me-2"></i> PDF
                    </button>
                </div>
                <table class="table table-bordered" id="table">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Source</th>
                            <th>New Leads</th>
                            <th>Pending</th>
                            <th>Processing</th>
                            <th>Interested</th>
                            <th>Whatsapp</th>
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
                        @foreach ($reports as $i => $row)
                        <tr>
                            <td>{{ $reports->firstItem() + $i }}</td>
                            <td>{{ $row->source }}</td>
                            <td>{{ $row->new_leads }}</td>
                            <td>{{ $row->pending }}</td>
                            <td>{{ $row->processing }}</td>
                            <td>{{ $row->interested }}</td>
                            <td>{{ $row->whatsapp }}</td>
                            <td>{{ $row->meeting_scheduled }}</td>
                            <td>{{ $row->call_scheduled }}</td>
                            <td>{{ $row->visit_scheduled }}</td>
                            <td>{{ $row->visit_done }}</td>
                            <td>{{ $row->booked }}</td>
                            <td>{{ $row->completed }}</td>
                            <td>{{ $row->cancelled }}</td>
                            <td>{{ $row->not_reachable }}</td>
                            <td>{{ $row->wrong_number }}</td>
                            <td>{{ $row->channel_partner }}</td>
                            <td>{{ $row->not_interested }}</td>
                            <td>{{ $row->not_picked }}</td>
                            <td>{{ $row->lost }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Total</th>
                            <th></th>
                            <th>{{ $totals['new_leads'] }}</th>
                            <th>{{ $totals['pending'] }}</th>
                            <th>{{ $totals['processing'] }}</th>
                            <th>{{ $totals['interested'] }}</th>
                            <th>{{ $totals['whatsapp'] }}</th>
                            <th>{{ $totals['meeting_scheduled'] }}</th>
                            <th>{{ $totals['call_scheduled'] }}</th>
                            <th>{{ $totals['visit_scheduled'] }}</th>
                            <th>{{ $totals['visit_done'] }}</th>
                            <th>{{ $totals['booked'] }}</th>
                            <th>{{ $totals['completed'] }}</th>
                            <th>{{ $totals['cancelled'] }}</th>
                            <th>{{ $totals['not_reachable'] }}</th>
                            <th>{{ $totals['wrong_number'] }}</th>
                            <th>{{ $totals['channel_partner'] }}</th>
                            <th>{{ $totals['not_interested'] }}</th>
                            <th>{{ $totals['not_picked'] }}</th>
                            <th>{{ $totals['lost'] }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div>
                {!! $reports->links('pagination::bootstrap-5') !!}
            </div>
        </div>
    </div>
</div>
@endsection
