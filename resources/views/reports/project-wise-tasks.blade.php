@extends('layouts.app')

@section('title', 'Project-Wise Task Report')

@section('content')
<div class="page-content">
    <div class="container-fluid mt-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Project-Wise Task Report</h4>
                <div class="d-flex gap-2">
                    <button class="ReportbtnExportExcel shadow btn btn-success btn-sm">
                        <i class="fas fa-file-excel me-2"></i> Excel
                    </button>
                    <button class="ReportbtnExportPDF shadow btn btn-danger btn-sm">
                        <i class="fas fa-file-pdf me-2"></i> PDF
                    </button>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered data-table">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Project Name</th>
                                <th>Total Tasks</th>
                                <th>Completed</th>
                                <th>Pending</th>
                                <th>Delayed</th>
                                <th>Assignee Members</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reports as $index => $project)
                            <tr>
                                <td>{{ $reports->firstItem() + $index }}</td>
                                <td>{{ $project->project_name ?? '-' }}</td>
                                <td>{{ $project->total_tasks }}</td>
                                <td>{{ $project->completed_tasks }}</td>
                                <td>{{ $project->pending_tasks }}</td>
                                <td>{{ $project->delayed_tasks }}</td>
                                <td>{{ $project->assigned_members ?? 'Unassigned' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {!! $reports->links('pagination::bootstrap-5') !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
