@extends('layouts.app')

@section('title', 'Task Summary Reports')

@section('content')
<div class="page-content">
    <div class="container-fluid mt-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Task Summary Reports</h4>
                <div class="d-flex gap-2">
                    <button class="ReportbtnExportExcel shadow btn btn-success btn-sm d-flex align-items-center" data-bs-toggle="tooltip" data-bs-placement="top" title="Export table data to Excel">
                        <i class="fas fa-file-excel me-2"></i> Excel
                    </button>

                    <button class="ReportbtnExportPDF shadow btn btn-danger btn-sm d-flex align-items-center" data-bs-toggle="tooltip" data-bs-placement="top" title="Export table data to PDF">
                        <i class="fas fa-file-pdf me-2"></i> PDF
                    </button>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="table">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Task Type</th>
                                <th>Task Name</th>
                                <th>Description</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th>Assignee Members</th>
                                <th>Attachments</th>
                                <th>Comments</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reports as $index => $task)
                            <tr>
                                <td>{{ $reports->firstItem() + $index }}</td>
                                <td>{{ ucfirst($task->task_type ?? '-') }}</td>
                                <td>{{ $task->name ?? '-' }}</td>
                                <td>{{ $task->description ?? '-' }}</td>
                                <td>{{ \Carbon\Carbon::parse($task->start_date)->format('d M, Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($task->end_date)->format('d M, Y') }}</td>
                                <td>
                                    <span class="badge 
                                        @if($task->priority == 'high') bg-danger 
                                        @elseif($task->priority == 'medium') bg-warning 
                                        @else bg-info @endif">
                                        {{ ucfirst($task->priority) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge 
                                        @if($task->status == 'completed') bg-success 
                                        @elseif($task->status == 'in_progress') bg-primary 
                                        @else bg-secondary @endif">
                                        {{ str_replace('_', ' ', ucfirst($task->status)) }}
                                    </span>
                                </td>
                                <td>{{ $task->assigned_members ?? 'Unassigned' }}</td>
                                <td>{{ $task->attachment_count }}</td>
                                <td>{{ $task->comment_count }}</td>
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
