@extends('layouts.app')

@section('title', 'Task Management | Pro-leadexpertz')

@section('content')
@include('modals.task-file')
@include('modals.task-comment')
@include('modals.create-task-project')
@include('modals.edit-task-project')
@include('modals.status-comment')
@include('modals.team-member-detail')
<style>
    .timeline {
    position: relative;
    padding-left: 30px;
}

.timeline-item {
    position: relative;
}

.timeline-marker {
    position: absolute;
    left: -30px;
    top: 15px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: #007bff;
}

.timeline-content {
    margin-left: 0;
}
</style>
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0 fw-bold">Task Dashboard</h4>
                    <div class="d-flex gap-2">
                        @if($user_type == 'super_admin' || $user_type == 'divisional_head')
                        <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createProjectModal">
                            <i class="fas fa-folder-plus me-1"></i> New Project
                        </button>
                        @endif
                        <a href="{{ route('task.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus-circle me-1"></i> Create Task
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-xl-2 col-md-4 mb-3">
                <div class="card bg-primary text-white border-0 shadow-sm">
                    <div class="card-body py-3 px-3">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="text-white-75 mb-1">Total Tasks</h6>
                                <h4 class="mb-0 fw-bold">{{ $totalTasks }}</h4>
                            </div>
                            <div class="flex-shrink-0">
                                <i class="fas fa-tasks fa-lg text-white-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-md-4 mb-3">
                <div class="card bg-info text-white border-0 shadow-sm">
                    <div class="card-body py-3 px-3">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="text-white-75 mb-1">Project Tasks</h6>
                                <h4 class="mb-0 fw-bold">{{ $projectTasksCount }}</h4>
                            </div>
                            <div class="flex-shrink-0">
                                <i class="fas fa-folder fa-lg text-white-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-md-4 mb-3">
                <div class="card bg-success text-white border-0 shadow-sm">
                    <div class="card-body py-3 px-3">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="text-white-75 mb-1">Individual Tasks</h6>
                                <h4 class="mb-0 fw-bold">{{ $individualTasksCount }}</h4>
                            </div>
                            <div class="flex-shrink-0">
                                <i class="fas fa-user fa-lg text-white-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-md-4 mb-3">
                <div class="card bg-warning text-white border-0 shadow-sm">
                    <div class="card-body py-3 px-3">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="text-white-75 mb-1">Pending</h6>
                                <h4 class="mb-0 fw-bold">{{ $pendingTasks }}</h4>
                            </div>
                            <div class="flex-shrink-0">
                                <i class="fas fa-clock fa-lg text-white-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-md-4 mb-3">
                <div class="card bg-info text-white border-0 shadow-sm">
                    <div class="card-body py-3 px-3">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="text-white-75 mb-1">In Progress</h6>
                                <h4 class="mb-0 fw-bold">{{ $inProgressTasks }}</h4>
                            </div>
                            <div class="flex-shrink-0">
                                <i class="fas fa-spinner fa-lg text-white-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-md-4 mb-3">
                <div class="card bg-success text-white border-0 shadow-sm">
                    <div class="card-body py-3 px-3">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="text-white-75 mb-1">Completed</h6>
                                <h4 class="mb-0 fw-bold">{{ $completedTasks }}</h4>
                            </div>
                            <div class="flex-shrink-0">
                                <i class="fas fa-check-circle fa-lg text-white-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <ul class="nav nav-tabs nav-tabs-custom mb-3" id="dashboardTabs">
            <li class="nav-item">
                <a class="nav-link {{ request('active_tab', 'projects') == 'projects' ? 'active' : '' }}" 
                   data-bs-toggle="tab" 
                   href="#projects">
                    <i class="fas fa-folder me-1"></i> Projects
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request('active_tab') == 'tasks' ? 'active' : '' }}" 
                   data-bs-toggle="tab" 
                   href="#tasks">
                    <i class="fas fa-tasks me-1"></i> Tasks
                </a>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade {{ request('active_tab', 'projects') == 'projects' ? 'show active' : '' }}" id="projects">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light border-0 py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0 fw-bold">Project Management</h5>
                            <div class="d-flex align-items-center gap-2">
                                @if(request('project_search'))
                                <a href="{{ route('task.list', ['active_tab' => 'projects']) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-times me-1"></i> Clear Search
                                </a>
                                @endif
                                <form method="GET" action="{{ route('task.list') }}" class="d-flex align-items-center">
                                    <input type="hidden" name="active_tab" value="projects">
                                    <div class="position-relative">
                                        <i class="fas fa-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                                        <input type="text" 
                                        name="project_search" 
                                        value="{{ request('project_search') }}"
                                        class="form-control ps-5 rounded-pill" 
                                        placeholder="Search projects..." 
                                        style="width: 250px;">
                                    </div>
                                    <button type="submit" class="btn btn-primary ms-2 d-none">Search</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($task_projects->count() > 0)
                        <div class="row g-4">
                            @foreach($task_projects as $project)
                            @php
                                $projectStats = $projectTasksStats[$project->id] ?? ['total' => 0, 'completed' => 0];
                                $progress = $projectStats['total'] > 0 ? round(($projectStats['completed'] / $projectStats['total']) * 100) : 0;
                            @endphp
                            <div class="col-xl-4 col-lg-6 col-md-6">
                                <div class="card border-0 h-100 shadow-lg rounded-3 task-card bg-light bg-opacity-75 position-relative">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div class="flex-grow-1">
                                                <h6 class="card-title mb-1 fw-bold text-truncate cursor-pointer"  
                                                    data-bs-toggle="tooltip" 
                                                    title="{{ $project->name }}">
                                                    {{ $project->name }}
                                                </h6>
                                                <p class="text-muted small mb-2 lh-sm" data-bs-toggle="tooltip" 
                                                
                                                title="{{ $project->description }}">{{ Str::limit($project->description, 80) }}</p>
                                            </div>
                                            <div class="dropdown">
                                                @if ($task_owner === $project->created_by)
                                                <button class="btn btn-sm btn-light border-0" type="button" data-bs-toggle="dropdown">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end shadow">
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('task.list', ['active_tab' => 'tasks', 'project_filter' => $project->id]) }}">
                                                            <i class="fas fa-eye me-2 text-primary"></i> View Tasks
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('task.create') }}?task_project_id={{ $project->id }}">
                                                            <i class="fas fa-plus me-2 text-success"></i> Add Task
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item edit-project" href="#" 
                                                           data-project-id="{{ $project->id }}" 
                                                           data-project-name="{{ $project->name }}" 
                                                           data-project-description="{{ $project->description }}"
                                                           data-project-status="{{ $project->status }}"
                                                           data-project-priority="{{ $project->priority }}">
                                                            <i class="fas fa-edit me-2 text-info"></i> Edit
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <a class="dropdown-item text-danger delete-project" href="#" 
                                                           data-project-id="{{ $project->id }}" 
                                                           data-project-name="{{ $project->name }}">
                                                            <i class="fas fa-trash-alt me-2"></i> Delete
                                                        </a>
                                                    </li>
                                                </ul>
                                                @else
                                                <i class="fas fa-ellipsis-v text-muted"></i>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="badge bg-{{ $project->priority == 'high' ? 'danger' : ($project->priority == 'medium' ? 'warning' : ($project->priority == 'low' ? 'success' : 'secondary')) }}">
                                                {{ ucfirst($project->priority) }}
                                            </span>
                                            <small class="text-muted">{{ $projectStats['total'] }} tasks</small>
                                        </div>

                                        <div class="progress mb-3" style="height: 6px;">
                                            <div class="progress-bar bg-primary" style="width: {{ $progress }}%"></div>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-{{ $project->status == 'active' ? 'success' : ($project->status == 'completed' ? 'primary' : ($project->status == 'on_hold' ? 'warning' : 'secondary')) }} dropdown-toggle py-1" 
                                                    data-bs-toggle="dropdown">
                                                    <i class="fas fa-caret-down me-1"></i>
                                                    {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                                                </button>
                                                @if ($task_owner === $project->created_by)
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <a class="dropdown-item update-project-status" href="#" data-project-id="{{ $project->id }}" data-status="planning">
                                                            <i class="fas fa-clock me-2 text-secondary"></i> Planning
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item update-project-status" href="#" data-project-id="{{ $project->id }}" data-status="active">
                                                            <i class="fas fa-play me-2 text-success"></i> Active
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item update-project-status" href="#" data-project-id="{{ $project->id }}" data-status="on_hold">
                                                            <i class="fas fa-pause me-2 text-warning"></i> On Hold
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item update-project-status" href="#" data-project-id="{{ $project->id }}" data-status="completed">
                                                            <i class="fas fa-check me-2 text-primary"></i> Completed
                                                        </a>
                                                    </li>
                                                </ul>
                                                @endif
                                            </div>
                                            <small class="text-muted">{{ $progress }}% complete</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        
                        @if($task_projects->hasPages())
                        <div class="row mt-4 align-items-center">
                            <div class="col-sm-12 col-md-5">
                                <div class="text-muted small">
                                    Showing {{ $task_projects->firstItem() }} to {{ $task_projects->lastItem() }} of {{ $task_projects->total() }} entries
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-7">
                                <div class="d-flex justify-content-end">
                                    <nav aria-label="Page navigation">
                                        <ul class="pagination pagination-sm mb-0">
                                            {{ $task_projects->appends([
                                                'active_tab' => 'projects',
                                                'project_search' => request('project_search')
                                            ])->onEachSide(1)->links('pagination::bootstrap-4') }}
                                        </ul>
                                    </nav>
                                </div>
                            </div>
                        </div>
                        @endif
                        
                        @else
                        <div class="text-center py-5">
                            <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No Projects Yet</h5>
                            <p class="text-muted mb-3">Create your first project to organize tasks efficiently</p>
                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createProjectModal">
                                <i class="fas fa-plus me-1"></i> Create Project
                            </button>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="tab-pane fade {{ request('active_tab') == 'tasks' ? 'show active' : '' }}" id="tasks">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light border-0 py-3">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title mb-0 fw-bold">Task Management</h5>
                            <div class="d-flex align-items-center gap-2">
                                @php
                                    $activeFiltersCount = 0;
                                    if(request('task_type') && request('task_type') != 'all') $activeFiltersCount++;
                                    if(request('status_filter')) $activeFiltersCount++;
                                    if(request('project_filter') && request('project_filter') != 'all') $activeFiltersCount++;
                                    if(request('task_search')) $activeFiltersCount++;
                                @endphp
                                @if($activeFiltersCount > 0)
                                <a href="{{ route('task.list', ['active_tab' => 'tasks']) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-times me-1"></i> Clear Filters
                                </a>
                                @endif
                                <form method="GET" action="{{ route('task.list') }}" class="d-flex align-items-center">
                                    <input type="hidden" name="active_tab" value="tasks">
                                    <div class="position-relative">
                                        <i class="fas fa-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                                        <input type="text" 
                                        name="task_search" 
                                        value="{{ request('task_search') }}"
                                        class="form-control ps-5 rounded-pill" 
                                        placeholder="Search tasks..." 
                                        style="width: 250px;">
                                    </div>
                                    <button type="submit" class="btn btn-primary ms-2 d-none">Search</button>
                                </form>
                            </div>
                        </div>
                        <form method="GET" action="{{ route('task.list') }}">
                            <input type="hidden" name="active_tab" value="tasks">
                            <input type="hidden" name="task_search" value="{{ request('task_search') }}">
                            
                            <div class="d-flex flex-wrap align-items-center gap-2">
                                <div class="d-flex flex-wrap gap-1">
                                    @foreach([
                                        ['value' => 'all', 'label' => 'All Tasks', 'count' => $totalTasks],
                                        ['value' => 'project', 'label' => 'Project Tasks', 'count' => $projectTasksCount],
                                        ['value' => 'individual', 'label' => 'Individual Tasks', 'count' => $individualTasksCount]
                                    ] as $filter)
                                    <div class="position-relative">
                                        <button type="submit" name="task_type" value="{{ $filter['value'] }}" 
                                            class="btn btn-outline-primary btn-sm position-relative {{ request('task_type', 'all') == $filter['value'] ? 'active' : '' }}">
                                            {{ $filter['label'] }}
                                            @if(request('task_type') == $filter['value'] || ($filter['value'] == 'all' && !request('task_type')))
                                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                                {{ $filter['count'] }}
                                            </span>
                                            @endif
                                        </button>
                                    </div>
                                    @endforeach
                                </div>
                                
                                <div class="vr d-none d-md-block"></div>
                                <div class="d-flex flex-wrap gap-1">
                                    @foreach([
                                        ['value' => 'today', 'label' => 'Today', 'count' => $todayTasks ?? 0],
                                        ['value' => 'pending', 'label' => 'Pending', 'count' => $pendingTasks ?? 0],
                                        ['value' => 'in_progress', 'label' => 'In Progress', 'count' => $inProgressTasks ?? 0],
                                        ['value' => 'completed', 'label' => 'Completed', 'count' => $completedTasks ?? 0],
                                        ['value' => 'overdue', 'label' => 'Overdue', 'count' => $overdueTasks ?? 0]
                                    ] as $filter)

                                    <div class="position-relative">
                                        <button type="submit" name="status_filter" value="{{ $filter['value'] }}" 
                                            class="btn btn-outline-primary btn-sm position-relative {{ request('status_filter') == $filter['value'] ? 'active' : '' }}">
                                            {{ $filter['label'] }}
                                            @if(request('status_filter') == $filter['value'])
                                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                                {{ $filter['count'] }}
                                            </span>
                                            @endif
                                        </button>
                                    </div>
                                    @endforeach
                                </div>
                                
                                <div class="vr d-none d-md-block"></div>
                                <select class="form-select form-select-sm" name="project_filter" style="width: 180px;" onchange="this.form.submit()">
                                    <option value="all">All Projects</option>
                                    @foreach($task_projects as $project)
                                    <option value="{{ $project->id }}" {{ request('project_filter') == $project->id ? 'selected' : '' }}>
                                        {{ $project->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </form>
                    </div>
                    
                    <div class="card-body">
                        @if($tasks->count() > 0)
                        <div class="row g-4">
                            @foreach($tasks as $task)
                                @php
                                    $progress = $task->status == 'completed' ? 100 : ($task->status == 'in_progress' ? 50 : 0);
                                    $projectName = $task->task_project_id ? ($task_projects->firstWhere('id', $task->task_project_id)->name ?? 'Unknown Project') : null;
                                    $assignedMembers = DB::table('task_user')
                                        ->join('users', 'task_user.user_id', '=', 'users.id')
                                        ->where('task_user.task_id', $task->id)
                                        ->get(['users.id', 'users.name', 'users.email']);
                                    $isOverdue = \Carbon\Carbon::now()->gt($task->end_date) && $task->status != 'completed';
                                    $descriptionText = strip_tags($task->description);
                                    $descriptionShort = Str::limit($descriptionText, 80);
                                    $taskFiles = $taskFiles ?? [];
                                    $files = $taskFiles[$task->id] ?? [];
                                    $taskComments = $task_all_comments[$task->id] ?? collect();
                                @endphp
                                <div class="col-xl-3 col-lg-4 col-md-6">
                                    <div class="card border-0 h-100 shadow-lg rounded-3 task-card bg-light bg-opacity-75 position-relative" id="task-card-{{ $task->id }}"data-status="{{ $task->status }}">
                                        <div class="card-body position-relative mb-0 pb-0 px-3">
                                            <div class="position-absolute top-0 end-0 m-2">
                                                <div class="d-flex gap-2">
                                                    @if($task->status == 'completed')
                                                    <span class="badge bg-success text-white p-2" data-bs-toggle="tooltip" data-bs-placement="top" title="Task Completed">
                                                       <i class="fa-solid fa-check"></i>
                                                    </span>
                                                @endif
                                                @if ($task_owner === $task->user_id)
                                                    <div class="dropdown">
                                                        <button class="btn btn-sm btn-outline-secondary border-0" type="button" data-bs-toggle="dropdown">
                                                            <i class="fas fa-ellipsis-h"></i>
                                                        </button>
                                                        <ul class="dropdown-menu dropdown-menu-end">
                                                            <li>
                                                                <a class="dropdown-item" href="{{ route('task.create', ['id' => $task->id]) }}">
                                                                    <i class="fas fa-edit me-2 text-primary"></i> Edit
                                                                </a>
                                                            </li>
                                                            <li><hr class="dropdown-divider"></li>
                                                            <li>
                                                                <a class="dropdown-item text-danger delete-task" href="#" data-id="{{ $task->id }}">
                                                                    <i class="fas fa-trash-alt me-2"></i> Delete
                                                                </a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                @endif
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-start mb-3">
                                                <div class="flex-grow-1">
                                                    <div class="d-flex align-items-center gap-1 mb-2">
                                                        @if($task->task_project_id)
                                                        <span class="badge bg-primary bg-opacity-10 text-light border-0">
                                                            <i class="fas fa-folder me-1"></i> Project
                                                        </span>
                                                        @else
                                                        <span class="badge bg-success bg-opacity-10 text-success border-0">
                                                            <i class="fas fa-user me-1"></i> Individual
                                                        </span>
                                                        @endif
                                                        <span class="badge bg-{{ $task->priority == 'high' ? 'danger' : ($task->priority == 'medium' ? 'warning' : 'success') }}">
                                                            {{ ucfirst($task->priority) }}
                                                        </span>
                                                    </div>
                                                    <h6 class="card-title mb-1 fw-bold text-truncate">{{ $task->name }}</h6>
                                                    @if($task->task_project_id && $projectName)
                                                    <small class="text-muted">
                                                        <i class="fas fa-folder me-1"></i> {{ Str::limit($projectName, 25) }}
                                                    </small>
                                                    @endif
                                                </div>
                                            </div>
                                            <p class="text-muted small mb-3 lh-sm" 
                                            data-bs-toggle="tooltip" 
                                            data-bs-placement="top" 
                                            title="{{ $descriptionText }}">
                                                {{ $descriptionShort }}
                                            </p>
                                            <div class="progress mb-3" style="height: 6px;">
                                                <div class="progress-bar bg-{{ $task->task_project_id ? 'primary' : 'success' }}" style="width: {{ $progress }}%"></div>
                                            </div>
                                            <div class="">
                                                <div class="d-flex gap-1">
                                                    <button class="btn btn-outline-secondary btn-sm attachment-modal-btn" 
                                                            data-task-id="{{ $task->id }}"
                                                            data-files='@json($files)'>
                                                        <i class="fas fa-paperclip me-1"></i> {{ count($files) }}
                                                    </button>

                                                    <button class="btn btn-outline-secondary btn-sm team-modal-btn" 
                                                            data-task-id="{{ $task->id }}"
                                                            data-members='@json($assignedMembers)'>
                                                        <i class="fas fa-users me-1"></i> {{ $assignedMembers->count() }}
                                                    </button>

                                                    <button class="btn btn-outline-secondary btn-sm comment-modal-btn" 
                                                            data-task-id="{{ $task->id }}"
                                                            data-comments='@json($taskComments)'
                                                            data-task-name="{{ $task->name }}">
                                                        <i class="fas fa-comment me-1"></i> {{ $taskComments->count() }}
                                                    </button>
                                                </div>
                                                @if ($task_owner === $task->user_id)
                                                <div class="d-flex gap-3 mt-2">
                                                    <div class="d-flex align-items-center gap-2">
                                                        <i class="fas fa-calendar text-{{ $isOverdue ? 'danger' : 'muted' }}"></i>
                                                        <small class="{{ $isOverdue ? 'text-danger fw-bold' : 'text-muted' }}">
                                                            {{ \Carbon\Carbon::parse($task->end_date)->format('M d, Y') }}
                                                        </small>
                                                    </div>
                                                    <div class="dropdown">
                                                        <button class="btn btn-{{ $task->status == 'in_progress' ? 'info' : 'warning' }} btn-sm text-white dropdown-toggle" 
                                                            data-bs-toggle="dropdown" 
                                                            data-task-id="{{ $task->id }}"
                                                            data-current-status="{{ $task->status }}">
                                                            <i class="fas fa-caret-down me-1"></i>
                                                            {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                                        </button>
                                                        <ul class="dropdown-menu dropdown-menu-end">
                                                            <li>
                                                                <a class="dropdown-item status-option" href="#" data-status="pending" data-task-id="{{ $task->id }}">
                                                                    <i class="fas fa-clock me-2 text-warning"></i> Pending
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a class="dropdown-item status-option" href="#" data-status="in_progress" data-task-id="{{ $task->id }}">
                                                                    <i class="fas fa-spinner me-2 text-info"></i> In Progress
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a class="dropdown-item status-option" href="#" data-status="completed" data-task-id="{{ $task->id }}">
                                                                    <i class="fas fa-check-circle me-2 text-success"></i> Completed
                                                                </a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @else
                        <div class="text-center py-5">
                            <i class="fas fa-tasks fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No Tasks Found</h5>
                            <p class="text-muted mb-3">Create your first task to get started</p>
                            <a href="{{ route('task.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus me-1"></i> Create Task
                            </a>
                        </div>
                        @endif
                        <div class="row mt-4 align-items-center">
                            <div class="col-sm-12 col-md-5">
                                <div class="text-muted small">
                                    Showing {{ $tasks->firstItem() }} to {{ $tasks->lastItem() }} of {{ $tasks->total() }} entries
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-7">
                                <div class="d-flex justify-content-end">
                                    <nav aria-label="Page navigation">
                                        <ul class="pagination pagination-sm mb-0">
                                            {{ $tasks->appends([
                                                'active_tab' => 'tasks',
                                                'task_type' => request('task_type'),
                                                'status_filter' => request('status_filter'),
                                                'project_filter' => request('project_filter'),
                                                'task_search' => request('task_search')
                                            ])->onEachSide(1)->links('pagination::bootstrap-4') }}
                                        </ul>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="attachmentsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-paperclip me-2"></i>Task Attachments
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="attachmentsModalBody">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="teamMembersModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-users me-2"></i>Team Members
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="teamMembersModalBody">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imagePreviewTitle">
                    <i class="fas fa-image me-2"></i>Image Preview
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="previewImage" src="" class="img-fluid rounded shadow-sm" alt="Preview" style="max-height: 70vh;">
            </div>
            <div class="modal-footer">
                <a id="downloadImage" href="#" class="btn btn-primary" download>
                    <i class="fas fa-download me-1"></i> Download
                </a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="commentsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-comments me-2"></i>Task Comments - <span id="commentsTaskName"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="commentsModalBody">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() 
    {
        $('[data-bs-toggle="tooltip"]').tooltip();
        $('#SubmitTaskProjectBtn').closest('form').on('submit', function() 
        {
            $('#SubmitTaskProjectBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Creating...');
        });

        $('#EditProjectBtn').closest('form').on('submit', function() 
        {
            $('#EditProjectBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Updating...');
        });

        $(document).on('click', '.attachment-modal-btn', function() 
        {
            const taskId = $(this).data('task-id');
            const files = $(this).data('files');
            
            let attachmentsHtml = '<div class="row g-3">';
            
            if (files.length === 0) 
            {
                attachmentsHtml = `
                    <div class="text-center py-4">
                        <i class="fas fa-paperclip fa-2x text-muted mb-3"></i>
                        <p class="text-muted">No attachments found for this task</p>
                    </div>
                `;
            } 
            else 
            {
                files.forEach(file => {
                    const fileUrl = "{{ Storage::url('') }}" + file.file_path;
                    const fileName = file.file_name || file.file_path.split('/').pop();
                    const extension = file.file_path.split('.').pop().toLowerCase();
                    const isImage = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'].includes(extension);
                    
                    attachmentsHtml += `
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body text-center">
                                    ${isImage ? 
                                        `<img src="${fileUrl}" class="img-fluid rounded mb-2 shadow-sm" style="max-height: 150px; object-fit: cover; cursor: pointer;" 
                                              onclick="openImagePreview('${fileUrl}', '${fileName}')" alt="${fileName}">
                                         <h6 class="card-title text-truncate mt-2">${fileName}</h6>
                                         <div class="mt-2">
                                             <button class="btn btn-sm btn-outline-primary" onclick="openImagePreview('${fileUrl}', '${fileName}')">
                                                 <i class="fas fa-eye me-1"></i> Preview
                                             </button>
                                         </div>` :
                                        `<div class="display-4 text-muted mb-2">
                                            <i class="fas fa-file"></i>
                                         </div>
                                         <h6 class="card-title text-truncate">${fileName}</h6>
                                         <div class="mt-2">
                                             <a href="${fileUrl}" target="_blank" class="btn btn-sm btn-outline-success">
                                                 <i class="fas fa-download me-1"></i> Download
                                             </a>
                                         </div>`
                                    }
                                </div>
                            </div>
                        </div>
                    `;
                });
                attachmentsHtml += '</div>';
            }
            
            $('#attachmentsModalBody').html(attachmentsHtml);
            $('#attachmentsModal').modal('show');
        });

        $(document).on('click', '.team-modal-btn', function() 
        {
            const taskId = $(this).data('task-id');
            const members = $(this).data('members');
            
            let membersHtml = '<div class="list-group">';
            
            if (members.length === 0)
            {
                membersHtml = `
                    <div class="text-center py-4">
                        <i class="fas fa-users fa-2x text-muted mb-3"></i>
                        <p class="text-muted">No team members assigned to this task</p>
                    </div>
                `;
            } 
            else 
            {
                members.forEach(member => {
                    membersHtml += `
                        <div class="list-group-item">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 40px; height: 40px;">
                                        <span class="text-white fw-bold">${member.name.charAt(0).toUpperCase()}</span>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-0">${member.name}</h6>
                                    <small class="text-muted">${member.email}</small>
                                </div>
                            </div>
                        </div>
                    `;
                });
            }
            
            membersHtml += '</div>';
            $('#teamMembersModalBody').html(membersHtml);
            $('#teamMembersModal').modal('show');
        });
        let selectedTaskId = null;
        let selectedStatus = null;

        $(document).on('click', '.status-option', function(e) 
        {
            e.preventDefault();
            selectedStatus = $(this).data('status');
            selectedTaskId = $(this).data('task-id');
            
            $('#selectedStatusText').text(selectedStatus.replace('_', ' ').toUpperCase());
            $('#comment').val('');
            $('#file').val('');
            $('#statusCommentModal').modal('show');
        });

        $('#statusCommentForm').on('submit', function(e) 
        {
            e.preventDefault();
            if (!selectedTaskId || !selectedStatus) 
            {
                flasher.error('Error', 'Invalid task or status');
                return;
            }

            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('status', selectedStatus);
            formData.append('comments', $('#statusComment').val());

            const files = $('#file')[0].files;
            for (let i = 0; i < files.length; i++) 
            {
                formData.append('file[]', files[i]);
            }

            $.ajax({
                url: `/task/update-status/${selectedTaskId}`,
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function() 
                {
                    $('#statusCommentModal').modal('hide');
                },
                success: function(response) 
                {
                    if (response.status === 200) 
                    {
                        flasher.success(response.message);
                        setTimeout(() => location.reload(), 1000);
                    } 
                    else 
                    {
                        flasher.error('Error', response.message || 'Failed to update task status');
                    }
                },
                error: function() 
                {
                    flasher.error('Error', 'Failed to update task status');
                }
            });
        });

        $(document).on('click', '.delete-task', function(e) 
        {
            e.preventDefault();
            const taskId = $(this).data('id');
            
            Swal.fire({
                title: 'Are you sure?',
                text: "This action cannot be undone!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) 
                {
                    $.ajax({
                        url: `/task/delete/${taskId}`,
                        type: 'DELETE',
                        data: { _token: '{{ csrf_token() }}' },
                        success: function(response) 
                        {
                            if (response.status === 200) 
                            {
                                flasher.success(response.message);
                                setTimeout(() => location.reload(), 2000);
                            } 
                            else 
                            {
                                flasher.error('Error', response.message || 'Failed to delete task');
                            }
                        },
                        error: function() 
                        {
                            flasher.error('Error', 'Failed to delete task');
                        }
                    });
                }
            });
        });

        $(document).on('click', '.edit-project', function(e) 
        {
            e.preventDefault();
            const projectId = $(this).data('project-id');
            const projectName = $(this).data('project-name');
            const projectDescription = $(this).data('project-description');
            const projectStatus = $(this).data('project-status');
            const projectPriority = $(this).data('project-priority');
            
            $('#editProjectId').val(projectId);
            $('#editProjectName').val(projectName);
            $('#editProjectDescription').val(projectDescription);
            $('#editProjectStatus').val(projectStatus);
            $('#editProjectPriority').val(projectPriority);
            
            $('#editProjectModal').modal('show');
        });
    });

    function openImagePreview(fileUrl, fileName) 
    {
        $('#previewImage').attr('src', fileUrl);
        $('#downloadImage').attr('href', fileUrl).attr('download', fileName);
        $('#imagePreviewTitle').html(`<i class="fas fa-image me-2"></i>${fileName}`);
        $('#imagePreviewModal').modal('show');
    }

    $(document).on('click', '.comment-modal-btn', function() 
    {
        const taskId = $(this).data('task-id');
        const comments = $(this).data('comments');
        const taskName = $(this).data('task-name');
        
        $('#commentsTaskName').text(taskName);
        
        let commentsHtml = '';
        
        if (comments.length === 0) 
        {
            commentsHtml = `
                <div class="text-center py-4">
                    <i class="fas fa-comments fa-2x text-muted mb-3"></i>
                    <p class="text-muted">No comments found for this task</p>
                </div>
            `;
        } 
        else 
        {
            commentsHtml = '<div class="timeline">';
            comments.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
            
            comments.forEach(comment => {
                const commentDate = new Date(comment.created_at);
                const formattedDate = commentDate.toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
                
                commentsHtml += `
                    <div class="timeline-item mb-4">
                        <div class="timeline-marker bg-primary"></div>
                        <div class="timeline-content">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div class="flex-grow-1">
                                            <p class="mb-2 text-dark">${comment.comment}</p>
                                            <small class="text-muted">
                                                <i class="fas fa-clock me-1"></i>${formattedDate}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            commentsHtml += '</div>';
        }
        
        $('#commentsModalBody').html(commentsHtml);
        $('#commentsModal').modal('show');
    });
</script>
@endsection