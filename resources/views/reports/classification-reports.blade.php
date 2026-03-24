@extends('layouts.app')

@section('title', 'Classification Reports | Pro-leadexpertz')
@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">CLassification-wise Reports</h4>
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
                                    <th>Classification</th>
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
                                @php
                                    $total = [
                                        'new_leads' => 0, 'pending' => 0, 'processing' => 0, 'interested' => 0,
                                        'whatsapp' => 0, 'meeting_scheduled' => 0, 'call_scheduled' => 0, 'visit_scheduled' => 0, 'visit_done' => 0,
                                        'Booked' => 0, 'Completed' => 0, 'Cancelled' => 0,
                                        'not_reachable' => 0, 'wrong_number' => 0, 'channel_partner' => 0,
                                        'not_interested' => 0, 'not_picked' => 0, 'lost' => 0
                                    ];
                                @endphp
                                @foreach($reports as $index => $row)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $row->classification }}</td>
                                        @foreach($total as $key => $value)
                                            <td>{{ $row->$key }}</td>
                                            @php $total[$key] += $row->$key; @endphp
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Total</th>
                                    <td></td>
                                    @foreach($total as $sum)
                                        <td>{{ $sum }}</td>
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
    </div>  
@endsection