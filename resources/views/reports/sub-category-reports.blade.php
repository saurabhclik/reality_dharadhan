@extends('layouts.app')
@section('title', 'Sub Category Reports | Pro-leadexpertz')
@section('content')
<div class="page-content">
    <div class="container-fluid mt-4">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Sub Category Reports</h4>
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
                <table class="table table-bordered dt-responsive nowrap w-100" id="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Sub Category</th>
                            <th>New Leads</th>
                            <th>Pending</th>
                            <th>Processing</th>
                            <th>Interested</th>
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
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $totals = [
                                'new_leads' => 0, 'pending' => 0, 'processing' => 0,
                                'interested' => 0, 'whatsapp' => 0, 'meeting_scheduled' => 0, 'call_scheduled' => 0, 'visit_scheduled' => 0,
                                'visit_done' => 0, 'booked' => 0, 'completed' => 0, 'cancelled' => 0,
                                'not_reachable' => 0, 'wrong_number' => 0, 'channel_partner' => 0,
                                'not_interested' => 0, 'not_picked' => 0, 'lost' => 0,
                            ];
                        @endphp

                        @foreach ($reports as $key => $item)
                        <tr>
                            <td>{{ $reports->firstItem() + $key }}</td>
                            <td>{{ $item->sub_category }}</td>
                            <td>{{ $item->new_leads }}</td>
                            <td>{{ $item->pending }}</td>
                            <td>{{ $item->processing }}</td>
                            <td>{{ $item->interested }}</td>
                            <td>{{ $item->whatsapp }}</td>
                            <td>{{ $item->meeting_scheduled }}</td>
                            <td>{{ $item->call_scheduled }}</td>
                            <td>{{ $item->visit_scheduled }}</td>
                            <td>{{ $item->visit_done }}</td>
                            <td>{{ $item->booked }}</td>
                            <td>{{ $item->completed }}</td>
                            <td>{{ $item->cancelled }}</td>
                            <td>{{ $item->not_reachable }}</td>
                            <td>{{ $item->wrong_number }}</td>
                            <td>{{ $item->channel_partner }}</td>
                            <td>{{ $item->not_interested }}</td>
                            <td>{{ $item->not_picked }}</td>
                            <td>{{ $item->lost }}</td>
                        </tr>
                        @php
                            foreach ($totals as $key => $value) {
                                $totals[$key] += $item->$key ?? 0;
                            }
                        @endphp
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Total</th>
                            <th></th>
                            @foreach ($totals as $value)
                                <th>{{ $value }}</th>
                            @endforeach
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="mt-3">
                {!! $reports->links('pagination::bootstrap-5') !!}
            </div>
        </div>
    </div>
</div>
@endsection
