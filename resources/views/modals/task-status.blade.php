@if($task->status == 'completed')
    <span class="task-status-pill bg-success text-white">
        <i class="fas fa-check-circle me-1"></i> Completed
    </span>
@else
    <div class="dropdown">
        <button class="task-status-pill bg-{{ $task->status == 'in_progress' ? 'info' : 'secondary' }} text-white dropdown-toggle" 
                type="button" data-bs-toggle="dropdown">
            <i class="fas {{ $task->status == 'in_progress' ? 'fa-spinner' : 'fa-clock' }} me-1"></i>
            {{ ucfirst(str_replace('_', ' ', $task->status)) }}
        </button>
        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
            <li>
                <a class="dropdown-item status-option" data-status="pending" style="cursor:pointer;">
                    <i class="fas fa-clock me-2"></i> Pending
                </a>
            </li>
            <li>
                <a class="dropdown-item status-option" data-status="in_progress" style="cursor:pointer;">
                    <i class="fas fa-spinner me-2"></i> In Progress
                </a>
            </li>
            <li>
                <a class="dropdown-item status-option" data-status="completed" style="cursor:pointer;">
                    <i class="fas fa-check-circle me-2"></i> Completed
                </a>
            </li>
        </ul>
    </div>
@endif