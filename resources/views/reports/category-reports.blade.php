@extends('layouts.app')
@section('title', 'Category Reports | Pro‑leadexpertz')

@section('content')
<div class="page-content">
    <div class="container-fluid mt-4">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Category Reports</h4>
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
                                <th>Category</th>
                                <th>New</th>
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
                                <th>Wrong Num</th>
                                <th>Channel Partner</th>
                                <th>Not Interested</th>
                                <th>Not Picked</th>
                                <th>Lost</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $totals = array_fill_keys([
                                    'new_leads','pending','processing','interested','whatsapp','meeting_scheduled','call_scheduled','visit_scheduled','visit_done',
                                    'booked','completed','cancelled','not_reachable','wrong_number','channel_partner','not_interested','not_picked','lost'
                                ], 0);
                            @endphp
                            @foreach($reports as $i => $row)
                                <tr>
                                    <td>{{ $reports->firstItem() + $i }}</td>
                                    <td>{{ $row->category }}</td>
                                    @foreach($totals as $key => $_)
                                        <td>{{ $val = $row->$key }}</td>
                                        @php $totals[$key] += $val; @endphp
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>Total</th><th></th>
                                @foreach($totals as $sum)
                                    <th>{{ $sum }}</th>
                                @endforeach
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
