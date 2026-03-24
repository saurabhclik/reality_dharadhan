@extends('layouts.app')

@section('title', 'Visit Leads Report | Pro-leadexpertz')
@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-flex align-items-center justify-content-between">
                        <h4 class="mb-0">Visit Leads Report</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Reports</a></li>
                                <li class="breadcrumb-item active">Visit Leads</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body bg-light">
                            <form id="filterForm" class="form-inline">
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
                                            <th>Scheduled Visit</th>
                                            <th>Visit Done</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($results as $key => $row)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>{{ $row->name }}</td>
                                                <td>{{ $row->visit_scheduled }}</td>
                                                <td>{{ $row->visit_done }}</td>
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

    <script>
        $(document).ready(function() 
        {
            $('#filterForm').on('submit', function(e) 
            {
                e.preventDefault();
                var formData = $(this).serialize();
                
                $.ajax({
                    url: window.location.href,
                    type: 'GET',
                    data: formData,
                    dataType: 'json',
                    success: function(response) 
                    {
                        $('input[name="fromDate"]').val(response.fromDate);
                        $('input[name="toDate"]').val(response.toDate);
                        table.clear();
                        
                        $.each(response.data, function(index, row) 
                        {
                            table.row.add([
                                index + 1,
                                row.name,
                                row.visit_scheduled,
                                row.visit_done
                            ]);
                        });
                        
                        table.draw();
                    },
                    error: function(xhr) 
                    {
                        console.error(xhr.responseText);
                    }
                });
            });
            $('#resetFilter').on('click', function() 
            {
                var today = new Date().toISOString().split('T')[0];
                $('input[name="fromDate"]').val(today);
                $('input[name="toDate"]').val(today);
                $('#filterForm').submit();
            });
        });
    </script>

@endsection