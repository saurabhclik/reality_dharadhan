@extends('layouts.app')
@section('title', 'Dayend Reports')
@section('content')
<div class="page-content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Telecaller Users Reports</h4>
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
                <div class="table-responsive">
                    <table class="table table-bordered" id="table">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Name</th>
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
                            </tr>
                        </thead>
                        <tbody>
                            @php $count = 1; @endphp
                            @foreach($results as $row)
                            <tr>
                                <td>{{ $count++ }}</td>
                                <td>{{ $row->name }}</td>
                                <td>{{ $row->new_leads }}</td>
                                <td>{{ $row->pending }}</td>
                                <td>{{ $row->processing }}</td>
                                <td>{{ $row->whatsapp }}</td>
                                <td>{{ $row->meeting_scheduled }}</td>
                                <td>{{ $row->call_scheduled }}</td>
                                <td>{{ $row->interested }}</td>
                                <td>{{ $row->not_reachable }}</td>
                                <td>{{ $row->wrong_number }}</td>
                                <td>{{ $row->not_interested }}</td>
                                <td>{{ $row->not_picked }}</td>
                                <td>{{ $row->lost }}</td>
                            </tr>
                            @endforeach
                            <tr>
                                <th>Total</th>
                                <td></td>
                                <td>{{ $totals['new_leads'] }}</td>
                                <td>{{ $totals['pending'] }}</td>
                                <td>{{ $totals['processing'] }}</td>
                                <td>{{ $totals['whatsapp'] }}</td>
                                <td>{{ $totals['meeting_scheduled'] }}</td>
                                <td>{{ $totals['call_scheduled'] }}</td>
                                <td>{{ $totals['interested'] }}</td>
                                <td>{{ $totals['not_reachable'] }}</td>
                                <td>{{ $totals['wrong_number'] }}</td>
                                <td>{{ $totals['not_interested'] }}</td>
                                <td>{{ $totals['not_picked'] }}</td>
                                <td>{{ $totals['lost'] }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-end mt-3">
            {!! $results->links('pagination::bootstrap-5') !!}
        </div>
    </div>
</div>
@endsection
