@extends('layouts.app')

@section('title', 'Calling Report Detail | Pro-leadexpertz')
@section('content')
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Calling Report Detail</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript:void(0);">Reports</a></li>
                            <li class="breadcrumb-item active">Call Details</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body bg-light">
                        <form method="GET" action="{{ route('report.call_details') }}" class="form-inline" id="filterForm">
                            <div class="row g-3 align-items-center">
                                <div class="col-auto">
                                    <label class="form-label">Date:</label>
                                </div>
                                <div class="col-auto">
                                    <input type="date" value="{{ $fromDate }}" name="fromDate" class="form-control" id="fromDate">
                                </div>
                                <div class="col-auto">
                                    <label class="form-label">To:</label>
                                </div>
                                <div class="col-auto">
                                    <input type="date" value="{{ $toDate }}" name="toDate" class="form-control" id="toDate">
                                </div>
                                <div class="col-auto">
                                    <button type="submit" class="btn btn-primary" id="SubmitBtn">
                                        <span id="SubmitText">Search</span>
                                        <span id="SubmitSpinner" class="d-none">
                                            <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                                            Please wait...
                                        </span>
                                    </button>
                                    <a href="{{ route('report.call_details') }}" class="btn btn-outline-secondary" id="ClearBtn">
                                        <i class="fa-solid fa-eraser"></i>
                                    </a>
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
                                        <th>Agent Name</th>
                                        <th>Client Name</th>
                                        <th>Client Phone</th>
                                        <th>Call Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($results as $key => $row)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $row->agent }}</td>
                                            <td>{{ $row->client_name }}</td>
                                            <td>{{ $row->mobile }}</td>
                                            <td>{{ $row->call_time }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if(method_exists($results, 'links'))
                            <div class="mt-3">
                                {{ $results->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () 
    {
        const form = document.getElementById("filterForm");
        const btn = document.getElementById("SubmitBtn");
        const btnText = document.getElementById("SubmitText");
        const btnLoader = document.getElementById("SubmitSpinner");

        form.addEventListener("submit", function () 
        {
            btn.disabled = true;
            btnText.classList.add("d-none");
            btnLoader.classList.remove("d-none");
        });
    });
</script>
@endsection
