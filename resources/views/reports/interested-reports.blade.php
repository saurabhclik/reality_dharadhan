@extends('layouts.app')

@section('title', 'Interested Leads Report | Pro-leadexpertz')
@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-flex align-items-center justify-content-between">
                        <h4 class="mb-0">Interested Leads Report</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Reports</a></li>
                                <li class="breadcrumb-item active">Interested Leads</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body bg-light">
                            <form class="form-inline" method="get">
                                <div class="row g-3 align-items-center">
                                    <div class="col-auto">
                                        <label class="form-label">Date:</label>
                                    </div>
                                    <div class="col-auto">
                                        <input type="date" value="{{ $fromDate }}" name="fromDate" class="form-control">
                                    </div>
                                    <div class="col-auto">
                                        <label class="form-label">To:</label>
                                    </div>
                                    <div class="col-auto">
                                        <input type="date" value="{{ $toDate }}" name="toDate" class="form-control">
                                    </div>
                                    <div class="col-auto">
                                        <button type="submit" class="btn btn-secondary">Search</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <div class="card">
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
                                <table id="table" class="table table-bordered dt-responsive nowrap w-100">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Phone</th>
                                            <th>Interested</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($results as $key => $row)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>{{ $row->name }}</td>
                                                <td>{{ $row->mobile }}</td>
                                                <td>{{ $row->interested }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection