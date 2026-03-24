@php
    $activeFeatures = session('active_features', []);
@endphp
@if(in_array('task_management', $activeFeatures))
<style>
    .dataTable
    {
        min-width: 1100px;
    }
</style>
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom-0 py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Tasks</h4>
                </div>
            </div>
            <div class="card-body p-4">
                <ul class="nav nav-tabs nav-tabs-custom mb-4" id="TaskTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" data-bs-target="#TodayTask" style="cursor:pointer;">
                            Today
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" data-bs-target="#TomorrowTask" style="cursor:pointer;">
                            Tomorrow
                        </a>
                    </li>
                </ul>
                <div class="tab-content" id="TaskTabContent">
                    <div class="tab-pane fade show active" id="TodayTask" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table align-middle table-nowrap mb-0" id="today-task-table">
                                <thead class="table-light">
                                    <tr>
                                        <th>Task</th>
                                        <th>Type</th>
                                        <th>Priority</th>
                                        <th>Status</th>
                                        <th>Progress</th>
                                        <th>Due Date</th>
                                        <th>Team Members</th>
                                        <th>Files</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($events->where('timeframe', 'Today') as $task)
                                        <tr class="task-row" data-task-id="{{ $task->id }}">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-grow-1">
                                                        <h6 class="mb-1 task-name fw-medium" style="cursor:pointer;" 
                                                            data-bs-toggle="collapse" 
                                                            data-bs-target="#taskDetails{{ $task->id }}">
                                                            {{ Str::limit($task->title, 30) }}
                                                            <i class="fas fa-chevron-down float-end"></i>
                                                        </h6>
                                                        <div class="collapse" id="taskDetails{{ $task->id }}">
                                                            <div class="card card-body bg-light p-3 mt-2 border-0">
                                                                <p class="mb-0 text-muted text-break" 
                                                                   data-bs-toggle="tooltip" 
                                                                   title="{{ strip_tags($task->description) }}">
                                                                    📃 {{ Str::limit($task->description, 30) }}
                                                                </p>
                                                                @php
                                                                    $allComments = $task_all_comments[$task->id] ?? collect();
                                                                    $lastComment = $allComments->isNotEmpty() ? $allComments->last()->comment : 'No comment found';
                                                                    $commentTooltip = $allComments->isNotEmpty()
                                                                        ? $allComments->pluck('comment')->map(fn($c,$i)=>($i+1).'. '.trim(strip_tags($c)))->implode("\n")
                                                                        : 'No comments found';
                                                                @endphp
                                                                <p class="mb-0 mt-2 text-muted" 
                                                                   data-bs-toggle="tooltip"
                                                                   title="{{ $commentTooltip }}">
                                                                    💬 {{ Str::limit(strip_tags($lastComment), 30) }}
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                           <td>
                                                @if($task->project_name && $task->project_name !== 'No Project')
                                                    <span class="badge bg-info-subtle text-info fw-semibold px-2 py-1" 
                                                        data-bs-toggle="tooltip" 
                                                        data-bs-placement="top"
                                                        title="{{ $task->project_name }}">
                                                        <i class="fas fa-folder-open me-1"></i> Project
                                                        {{ Str::limit($task->project_name, 20) }}
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary-subtle text-secondary fw-semibold px-2 py-1" 
                                                        data-bs-toggle="tooltip" 
                                                        data-bs-placement="top"
                                                        title="Individual Task">
                                                        <i class="fas fa-user me-1"></i> Individual
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $task->priority=='high'?'danger':($task->priority=='medium'?'warning':'success') }} p-2 font-size-12">
                                                    <i class="fas fa-circle me-1 font-size-10"></i> {{ ucfirst($task->priority) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($task->status == 'completed')
                                                    <span class="btn btn-sm btn-success w-100 text-start font-size-12">
                                                        <i class="fas fa-check-circle me-1"></i> Completed
                                                    </span>
                                                @else
                                                    <div class="dropdown">
                                                        <button class="btn btn-sm btn-{{ $task->status=='in_progress'?'info':'secondary' }} dropdown-toggle w-100 text-start font-size-12" 
                                                            type="button" data-bs-toggle="dropdown">
                                                            <i class="fas {{ $task->status=='in_progress'?'fa-spinner':'fa-clock' }} me-1"></i> 
                                                            {{ ucfirst(str_replace('_',' ',$task->status)) }}
                                                        </button>
                                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                                            <li>
                                                                <a class="dropdown-item status-option" data-status="pending">Pending</a>
                                                            </li>
                                                            <li>
                                                                <a class="dropdown-item status-option" data-status="in_progress">In Progress</a>
                                                            </li>
                                                            <li>
                                                                <a class="dropdown-item status-option" data-status="completed">Completed</a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                @php
                                                    $progress = $task->status=='completed'?100:($task->status=='in_progress'?50:0);
                                                @endphp
                                                <div class="progress progress-sm rounded-pill">
                                                    <div class="progress-bar bg-{{ $progress==100?'success':($progress>=50?'info':'secondary') }}" 
                                                        role="progressbar" style="width:{{ $progress }}%">
                                                        {{ $progress }}%
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($task->date)->format('M d, Y') }}</td>
                                            <td>
                                                @php
                                                    $assignedMembers = DB::table('task_user')
                                                        ->join('users','task_user.user_id','=','users.id')
                                                        ->where('task_user.task_id',$task->id)
                                                        ->get(['users.name']);
                                                @endphp
                                                @foreach($assignedMembers as $member)
                                                    <span class="badge bg-light text-dark mb-1 font-size-12">{{ $member->name }}</span>
                                                @endforeach
                                            </td>
                                            <td>
                                                @if(isset($taskFiles[$task->id]))
                                                    @foreach($taskFiles[$task->id] as $file)
                                                        @php
                                                            $fileUrl = Storage::url($file->file_path);
                                                            $fileName = $file->file_name ?? basename($file->file_path);
                                                        @endphp
                                                        <a href="{{ $fileUrl }}" target="_blank" title="{{ $fileName }}">{{ $fileName }}</a><br>
                                                    @endforeach
                                                @else
                                                    <span class="text-muted font-size-12">No files</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="dropdown">
                                                    <a href="#" class="btn btn-light btn-sm dropdown-toggle rounded-circle" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="fas fa-ellipsis-v {{ $task_owner === $task->user_id ? 'text-primary' : 'text-danger no-editDelete-right' }}"></i>
                                                    </a>
                                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                                        @if ($task_owner === $task->user_id)
                                                        <li>
                                                            <a class="dropdown-item" href="{{ route('task.create', ['id' => $task->id]) }}">
                                                                <i class="fas fa-edit me-2 {{ $task_owner === $task->user_id ? 'text-primary' : 'text-danger no-editDelete-right' }}"></i> Edit
                                                            </a>
                                                        </li>
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li>
                                                            <a class="dropdown-item delete-task" href="#" data-id="{{ $task->id }}">
                                                                <i class="fas fa-trash-alt me-2 text-danger"></i> Delete
                                                            </a>
                                                        </li>
                                                        @endif
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="TomorrowTask" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table align-middle table-nowrap mb-0" id="tomorrow-task-table">
                                <thead class="table-light">
                                    <tr>
                                        <th>Task</th>
                                        <th>Priority</th>
                                        <th>Status</th>
                                        <th>Progress</th>
                                        <th>Due Date</th>
                                        <th>Team Members</th>
                                        <th>Files</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($events->where('timeframe', 'Tomorrow') as $task)
                                        <tr class="task-row" data-task-id="{{ $task->id }}">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-grow-1">
                                                        <h6 class="mb-1 task-name fw-medium" style="cursor:pointer;" 
                                                            data-bs-toggle="collapse" 
                                                            data-bs-target="#taskDetails{{ $task->id }}">
                                                            {{ Str::limit($task->title, 30) }}
                                                            <i class="fas fa-chevron-down float-end"></i>
                                                        </h6>
                                                        <div class="collapse" id="taskDetails{{ $task->id }}">
                                                            <div class="card card-body bg-light p-3 mt-2 border-0">
                                                                <p class="mb-0 text-muted text-break" 
                                                                   data-bs-toggle="tooltip" 
                                                                   title="{{ strip_tags($task->description) }}">
                                                                    📃 {{ Str::limit($task->description, 30) }}
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $task->priority=='high'?'danger':($task->priority=='medium'?'warning':'success') }} p-2 font-size-12">
                                                    <i class="fas fa-circle me-1 font-size-10"></i> {{ ucfirst($task->priority) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($task->status == 'completed')
                                                    <span class="btn btn-sm btn-success w-100 text-start font-size-12">
                                                        <i class="fas fa-check-circle me-1"></i> Completed
                                                    </span>
                                                @else
                                                    <div class="dropdown">
                                                        <button class="btn btn-sm btn-{{ $task->status=='in_progress'?'info':'secondary' }} dropdown-toggle w-100 text-start font-size-12" 
                                                            type="button" data-bs-toggle="dropdown">
                                                            <i class="fas {{ $task->status=='in_progress'?'fa-spinner':'fa-clock' }} me-1"></i> 
                                                            {{ ucfirst(str_replace('_',' ',$task->status)) }}
                                                        </button>
                                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                                            <li>
                                                                <a class="dropdown-item status-option" data-status="pending">Pending</a>
                                                            </li>
                                                            <li>
                                                                <a class="dropdown-item status-option" data-status="in_progress">In Progress</a>
                                                            </li>
                                                            <li>
                                                                <a class="dropdown-item status-option" data-status="completed">Completed</a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                @php
                                                    $progress = $task->status=='completed'?100:($task->status=='in_progress'?50:0);
                                                @endphp
                                                <div class="progress progress-sm rounded-pill">
                                                    <div class="progress-bar bg-{{ $progress==100?'success':($progress>=50?'info':'secondary') }}" 
                                                        role="progressbar" style="width:{{ $progress }}%">
                                                        {{ $progress }}%
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($task->date)->format('M d, Y') }}</td>
                                            <td>
                                                @php
                                                    $assignedMembers = DB::table('task_user')
                                                        ->join('users','task_user.user_id','=','users.id')
                                                        ->where('task_user.task_id',$task->id)
                                                        ->get(['users.name']);
                                                @endphp
                                                @foreach($assignedMembers as $member)
                                                    <span class="badge bg-light text-dark mb-1 font-size-12">{{ $member->name }}</span>
                                                @endforeach
                                            </td>
                                            <td>
                                                @if(isset($taskFiles[$task->id]))
                                                    @foreach($taskFiles[$task->id] as $file)
                                                        @php
                                                            $fileUrl = Storage::url($file->file_path);
                                                            $fileName = $file->file_name ?? basename($file->file_path);
                                                        @endphp
                                                        <a href="{{ $fileUrl }}" target="_blank" title="{{ $fileName }}">{{ $fileName }}</a><br>
                                                    @endforeach
                                                @else
                                                    <span class="text-muted font-size-12">No files</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="dropdown">
                                                    <a href="#" class="btn btn-light btn-sm dropdown-toggle rounded-circle" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="fas fa-ellipsis-v {{ $task_owner === $task->user_id ? 'text-primary' : 'text-danger no-editDelete-right' }}"></i>
                                                    </a>
                                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                                        @if ($task_owner === $task->user_id)
                                                        
                                                        <li>
                                                            <a class="dropdown-item" href="{{ route('task.create', ['id' => $task->id]) }}">
                                                                <i class="fas fa-edit me-2 {{ $task_owner === $task->user_id ? 'text-primary' : 'text-danger no-editDelete-right' }}"></i> Edit
                                                            </a>
                                                        </li>
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li>
                                                            <a class="dropdown-item delete-task" href="#" data-id="{{ $task->id }}">
                                                                <i class="fas fa-trash-alt me-2 text-danger"></i> Delete
                                                            </a>
                                                        </li>
                                                        @endif
                                                    </ul>
                                                </div>
                                            </td>
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
        $('#today-task-table').DataTable({
            scrollX: true,
            scrollCollapse: true,
            responsive: false,
            paging: false,
            fixedColumns: {
                leftColumns: 2,
                rightColumns: 1
            }
        });

        $('#tomorrow-task-table').DataTable({
            scrollX: true,
            scrollCollapse: true,
            responsive: false,
            paging: false,
            fixedColumns: {
                leftColumns: 2,
                rightColumns: 1
            }
        });
    });
</script>
@endif