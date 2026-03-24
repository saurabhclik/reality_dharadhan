@extends('layouts.app')

@section('title', 'Salesman Users Reports')

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Salesman Users Reports</h4>
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
                    <table class="table table-bordered dt-responsive nowrap w-100" id="table">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>New Leads</th>
                                <th>Whatsapp Scheduled</th>
                                <th>Meeting Scheduled</th>
                                <th>Call Scheduled</th>
                                <th>Visit Scheduled</th>
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
                                @if($userRole !== 'telecaller')
                                    <th>SM New Lead</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @php $count = ($results->currentPage() - 1) * $results->perPage() + 1; @endphp
                            @foreach($results as $row)
                                <tr>
                                    <td>{{ $count++ }}</td>
                                    <td>{{ $row->name }}</td>
                                    <td>{{ $row->new_leads }}</td>
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
                                    @if($userRole !== 'telecaller')
                                        <td>{{ $row->sm_new_leads }}</td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>Total</th>
                                <td></td>
                                <td>{{ $totals['new_leads'] }}</td>
                                <td>{{ $totals['whatsapp'] }}</td>
                                <td>{{ $totals['meeting_scheduled'] }}</td>
                                <td>{{ $totals['call_scheduled'] }}</td>
                                <td>{{ $totals['visit_scheduled'] }}</td>
                                <td>{{ $totals['visit_done'] }}</td>
                                <td>{{ $totals['booked'] }}</td>
                                <td>{{ $totals['completed'] }}</td>
                                <td>{{ $totals['cancelled'] }}</td>
                                <td>{{ $totals['not_reachable'] }}</td>
                                <td>{{ $totals['wrong_number'] }}</td>
                                <td>{{ $totals['channel_partner'] }}</td>
                                <td>{{ $totals['not_interested'] }}</td>
                                <td>{{ $totals['not_picked'] }}</td>
                                <td>{{ $totals['lost'] }}</td>
                                @if($userRole !== 'telecaller')
                                    <td>{{ $totals['sm_new_leads'] }}</td>
                                @endif
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div>
                {!! $results->links('pagination::bootstrap-5') !!}
            </div>
        </div>
    </div>
</div>
@endsection
