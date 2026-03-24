@extends('mobile.layouts.app')
@section('content')
<style>
    .task-actions {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px;
        background: #f8f9fa;
        border-radius: 8px;
        margin-bottom: 15px;
    }
    .task-actions .form-control {
        flex: 1;
        min-width: 150px;
    }
    .select-checkbox {
        margin-right: 10px;
    }
    .lead-card.selected {
        background-color: #e3f2fd;
        border-color: #CF5D3B;
    }
    .filter-btn.active {
        background-color: #CF5D3B;
        color: white;
    }
</style>

<button class="back-button text-decoration-none" id="backButton" onclick="window.history.back()">
    <i class="fas fa-arrow-left"></i>
</button>
<div class="dashboard mt-md-0 pt-md-0 mt-5 pt-2">
    <div class="filter-container">
        <div class="filter-buttons d-flex flex-wrap gap-1 p-2">
            <a href="{{ request()->url() }}" class="filter-btn {{ !request()->has('status') ? 'active' : '' }}">ALL</a>
            <button type="button" class="filter-btn {{ request()->get('status') == 'pending' ? 'active' : '' }}" data-status="pending">Pending</button>
            <button type="button" class="filter-btn {{ request()->get('status') == 'in_progress' ? 'active' : '' }}" data-status="in_progress">In Progress</button>
            <button type="button" class="filter-btn {{ request()->get('status') == 'completed' ? 'active' : '' }}" data-status="completed">Completed</button>
            <button type="button" class="filter-btn {{ request()->get('status') == 'today' ? 'active' : '' }}" data-status="today">Today</button>
        </div>
    </div>
    <div class="filter-section">
        <div class="filter-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Task Filters</h4>
            <div class="d-flex align-items-center">
                <button class="select-all-btn" id="selectAllBtn" style="display: none; background: none; border: none; font-size: 1.2rem; color: #CF5D3B;">
                    <i class="fas fa-check-double"></i>
                </button>
                <button class="share-btn me-2 ms-4" id="shareBtn" style="display: none; background: none; border: none; font-size: 1.2rem; color: #CF5D3B;">
                    <i class="fas fa-share-alt"></i>
                </button>
                <button class="filter-toggle" id="filterToggle" style="background: none; border: none; font-size: 1.2rem; color: #CF5D3B;">
                    <i class="fas fa-sliders-h"></i>
                </button>
            </div>
        </div>
        <div class="filter-controls" id="filterControls">
            <div class="filter-group">
                <label for="searchQuery"><i class="fas fa-search"></i> Search</label>
                <input type="text" id="searchQuery" class="form-control" placeholder="Search tasks..." value="{{ request()->get('search', '') }}">
            </div>
            <div class="filter-group">
                <label for="date-range"><i class="far fa-calendar-alt"></i> Date Range</label>
                <div class="date-range-picker d-flex align-items-center">
                    <input type="date" id="startDate" class="form-control" value="{{ request()->get('startDate', '') }}">
                    <span class="mx-2">to</span>
                    <input type="date" id="endDate" class="form-control" value="{{ request()->get('endDate', '') }}">
                </div>
            </div>
            <div class="d-flex gap-2 mt-2">
                <button class="apply-filters-btn flex-grow-1" id="applyFilters">
                    <i class="fas fa-check"></i> Apply
                </button>
                <button class="reset-filters-btn flex-grow-1" id="resetFilters">
                    <i class="fas fa-undo"></i> Reset
                </button>
            </div>
        </div>
    </div>
    <div class="lead-container" id="taskContainer"></div>

    <div class="loader-container text-center py-3" id="loader">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-2">Loading more tasks...</p>
    </div>

    <div class="no-more-tasks text-center py-3 text-muted" id="noMoreTasks" style="display: none;">
        <p>No tasks to show</p>
    </div>

    <div class="fab" role="button" onclick="openTaskSheet('add')">
        <i class="fas fa-plus"></i>
    </div>
</div>

<div class="overlay" id="overlay"></div>
<div class="bottom-sheet-form" id="taskSheet">
    <div class="sheet-header">
        <div class="handle"></div>
        <h5 class="modal-title m-0" id="sheetTitle">Add Task</h5>
        <button type="button" class="btn-close" onclick="closeTaskSheet()">&times;</button>
    </div>
    <form method="POST" id="taskForm" class="p-3">
        @csrf
        <input type="hidden" name="task_id" id="modalTaskId">
        <input type="hidden" name="action_type" id="actionType">
        
        <div class="form-group mb-3" id="taskField">
            <label class="form-label text-dark">Task Description</label>
            <textarea name="task" id="modalTask" class="form-control" rows="3"></textarea>
        </div>
        <div class="form-group mb-3" id="userField">
            <label class="form-label text-dark">Assigned To</label>
            <select name="user_id" id="modalUserId" class="form-control">
                <option value="">Select User</option>
                @foreach($teamMembers as $member)
                    <option value="{{ $member->id }}">{{ $member->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group mb-3" id="dateFields">
            <label class="form-label text-dark">Due Date & Time</label>
            <div class="d-flex gap-2">
                <input type="date" name="deadLineDate" id="modalDueDate" class="form-control">
                <input type="time" name="deadLineTime" id="modalDueTime" class="form-control">
            </div>
        </div>
        <div class="form-group mb-3" id="remarksField" style="display: none;">
            <label class="form-label text-dark">Remarks (Required for completion)</label>
            <textarea name="remarks" id="modalRemarks" class="form-control" rows="3"></textarea>
        </div>
        <div class="modal-actions">
            <button type="button" class="btn btn-secondary" onclick="closeTaskSheet()">Close</button>
            <button type="submit" class="btn btn-primary" id="modalSubmitBtn">Save Task</button>
        </div>
    </form>
</div>

<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
    let selectedTasks = [];
    let currentTasks = [];
    let isLoading = false;
    let currentPage = 1;
    let hasMoreTasks = true;
    let activeFilters = {
        status: '{{ request()->get("status", "") }}',
        search: '{{ request()->get("search", "") }}',
        startDate: '{{ request()->get("startDate", "") }}',
        endDate: '{{ request()->get("endDate", "") }}'
    };

    document.addEventListener('DOMContentLoaded', function() 
    {
        initializeFilters();
        initializeEventListeners();
        loadMoreTasks();
    });

    function initializeFilters() 
    {
        document.getElementById('searchQuery').value = activeFilters.search;
        document.getElementById('startDate').value = activeFilters.startDate;
        document.getElementById('endDate').value = activeFilters.endDate;

        if (activeFilters.status) 
        {
            document.querySelector(`.filter-btn[data-status="${activeFilters.status}"]`).classList.add('active');
        } 
        else 
        {
            document.querySelector('.filter-btn[href]').classList.add('active');
        }
    }

    function initializeEventListeners() 
    {
        document.getElementById('filterToggle').addEventListener('click', toggleFilters);
        
        document.getElementById('searchQuery').addEventListener('input', debounce(function()
        {
            activeFilters.search = this.value.trim();
            resetTaskState();
            loadMoreTasks();
        }, 500));
 
        document.getElementById('applyFilters').addEventListener('click', function() 
        {
            activeFilters.startDate = document.getElementById('startDate').value;
            activeFilters.endDate = document.getElementById('endDate').value;
            resetTaskState();
            loadMoreTasks();
        });
 
        document.getElementById('resetFilters').addEventListener('click', function() 
        {
            document.getElementById('searchQuery').value = '';
            document.getElementById('startDate').value = '';
            document.getElementById('endDate').value = '';
            activeFilters.search = '';
            activeFilters.startDate = '';
            activeFilters.endDate = '';
            activeFilters.status = '';
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            document.querySelector('.filter-btn[href]').classList.add('active');
            resetTaskState();
            loadMoreTasks();
        });
 
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.addEventListener('click', function(e) 
            {
                e.preventDefault();
                const status = this.getAttribute('data-status') || '';
                activeFilters.status = status;
                
                document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                
                resetTaskState();
                loadMoreTasks();
            });
        });
        
        document.getElementById('shareBtn').addEventListener('click', showShareModal);
        document.getElementById('selectAllBtn').addEventListener('click', toggleSelectAll);
        document.getElementById('overlay').addEventListener('click', closeTaskSheet);
        document.getElementById('taskForm').addEventListener('submit', handleFormSubmit);
        document.querySelector('.dashboard').addEventListener('scroll', debounce(function() 
        {
            if (isLoading || !hasMoreTasks) 
            {
                if (!hasMoreTasks) 
                {
                    document.getElementById('noMoreTasks').style.display = 'block';
                    document.getElementById('loader').style.display = 'none';
                }
                return;
            }
            const { scrollTop, scrollHeight, clientHeight } = this;
            if (scrollTop + clientHeight >= scrollHeight - 50) {
                loadMoreTasks();
            }
        }, 100));
    }

    function openTaskSheet(action, taskId = null) 
    {
        resetTaskForm();
        if (action === 'add') 
        {
            setupAddTask();
        } 
        else if (action === 'edit' && taskId) 
        {
            setupEditTask(taskId);
        } 
        else if (action === 'complete' && taskId) 
        {
            setupCompleteTask(taskId);
        } 
        else if (action === 'allocate') 
        {
            setupAllocateTasks();
        }
        
        document.getElementById('taskSheet').classList.add('show');
        document.getElementById('overlay').classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    function closeTaskSheet() 
    {
        document.getElementById('taskSheet').classList.remove('show');
        document.getElementById('overlay').classList.remove('show');
        document.body.style.overflow = 'auto';
    }

    function setupAddTask() 
    {
        document.getElementById('sheetTitle').textContent = 'Add Task';
        document.getElementById('actionType').value = 'add';
        document.getElementById('modalSubmitBtn').textContent = 'Save Task';
        showFormFields(['task', 'user', 'date']);
        document.getElementById('modalTask').setAttribute('required', 'required');
        document.getElementById('modalUserId').setAttribute('required', 'required');
        document.getElementById('modalDueDate').setAttribute('required', 'required');
        document.getElementById('modalRemarks').removeAttribute('required');
    }
    function setupEditTask(taskId) 
    {
        const task = currentTasks.find(t => t.id.toString() === taskId);
        if (!task) return;
        document.getElementById('sheetTitle').textContent = 'Edit Task';
        document.getElementById('actionType').value = 'edit';
        document.getElementById('modalTaskId').value = taskId;
        document.getElementById('modalTask').value = task.task;
        document.getElementById('modalUserId').value = task.user_id;
        document.getElementById('modalDueDate').value = task.deadLineDate;
        document.getElementById('modalDueTime').value = task.deadLineTime;
        document.getElementById('modalSubmitBtn').textContent = 'Update Task';
        showFormFields(['task', 'user', 'date']);
        document.getElementById('modalTask').setAttribute('required', 'required');
        document.getElementById('modalUserId').setAttribute('required', 'required');
        document.getElementById('modalDueDate').setAttribute('required', 'required');
        document.getElementById('modalRemarks').removeAttribute('required');
    }
    function setupCompleteTask(taskId) 
    {
        const task = currentTasks.find(t => t.id.toString() === taskId);
        if (!task) return;
        document.getElementById('sheetTitle').textContent = 'Complete Task';
        document.getElementById('actionType').value = 'complete';
        document.getElementById('modalTaskId').value = taskId;
        document.getElementById('modalDueDate').value = task.deadLineDate;
        document.getElementById('modalDueTime').value = task.deadLineTime;
        document.getElementById('modalSubmitBtn').textContent = 'Complete Task';
        showFormFields(['remarks']);
        document.getElementById('modalTask').removeAttribute('required');
        document.getElementById('modalUserId').removeAttribute('required');
        document.getElementById('modalDueDate').removeAttribute('required');
        document.getElementById('modalRemarks').setAttribute('required', 'required');
    }

    function setupAllocateTasks()
    {
        if (selectedTasks.length === 0) 
        {
            toastr.error('Please select at least one task to allocate.');
            return;
        }
        document.getElementById('sheetTitle').textContent = 'Allocate Tasks';
        document.getElementById('actionType').value = 'allocate';
        document.getElementById('modalTaskId').value = selectedTasks.join(',');
        document.getElementById('modalSubmitBtn').textContent = 'Allocate Tasks';
        showFormFields(['user']);
        document.getElementById('modalTask').value = '';
        document.getElementById('modalTask').removeAttribute('required');
        document.getElementById('modalUserId').setAttribute('required', 'required');
        document.getElementById('modalDueDate').removeAttribute('required');
        document.getElementById('modalRemarks').removeAttribute('required');
    }


    function showFormFields(fieldsToShow) 
    {
        document.getElementById('taskField').style.display = 'none';
        document.getElementById('userField').style.display = 'none';
        document.getElementById('dateFields').style.display = 'none';
        document.getElementById('remarksField').style.display = 'none';
        fieldsToShow.forEach(field => {
            switch(field) 
            {
                case 'task':
                    document.getElementById('taskField').style.display = 'block';
                    break;
                case 'user':
                    document.getElementById('userField').style.display = 'block';
                    break;
                case 'date':
                    document.getElementById('dateFields').style.display = 'block';
                    break;
                case 'remarks':
                    document.getElementById('remarksField').style.display = 'block';
                    break;
            }
        });
    }

    function resetTaskForm() 
    {
        document.getElementById('taskForm').reset();
        document.getElementById('modalTaskId').value = '';
        document.getElementById('actionType').value = '';
        document.getElementById('modalTask').removeAttribute('required');
        document.getElementById('modalUserId').removeAttribute('required');
        document.getElementById('modalDueDate').removeAttribute('required');
        document.getElementById('modalRemarks').removeAttribute('required');
    }

    function toggleSelectAll() 
    {
        const allCheckboxes = document.querySelectorAll('.task-checkbox');
        const allSelected = Array.from(allCheckboxes).every(checkbox => checkbox.checked);
        const newCheckedState = !allSelected;
        
        allCheckboxes.forEach(checkbox => {
            checkbox.checked = newCheckedState;
            const taskId = checkbox.getAttribute('data-task-id');
            const taskCard = checkbox.closest('.lead-card');
            
            if (checkbox.checked) 
            {
                taskCard.classList.add('selected');
                if (!selectedTasks.includes(taskId)) 
                {
                    selectedTasks.push(taskId);
                }
            }
            else 
            {
                taskCard.classList.remove('selected');
                selectedTasks = selectedTasks.filter(id => id !== taskId);
            }
        });
        toastr.success(newCheckedState ? 'All tasks selected' : 'All tasks deselected');
        updateSelectionUI();
    }
    function updateSelectionUI() 
    {
        const show = selectedTasks.length > 0;
        document.getElementById('shareBtn').style.display = show ? 'block' : 'none';
        document.getElementById('selectAllBtn').style.display = show ? 'block' : 'none';
    }

    function resetTaskState() 
    {
        currentPage = 1;
        hasMoreTasks = true;
        document.getElementById('taskContainer').innerHTML = '';
        document.getElementById('noMoreTasks').style.display = 'none';
        selectedTasks = [];
        updateSelectionUI();
    }

    function getFetchUrl() 
    {
        let url = `{{ route('mobile.tasks') }}?page=${currentPage}&per_page=10`;
        if (activeFilters.status) url += `&status=${encodeURIComponent(activeFilters.status)}`;
        if (activeFilters.search) url += `&search=${encodeURIComponent(activeFilters.search)}`;
        if (activeFilters.startDate) url += `&startDate=${encodeURIComponent(activeFilters.startDate)}`;
        if (activeFilters.endDate) url += `&endDate=${encodeURIComponent(activeFilters.endDate)}`;
        return url;
    }

    function loadMoreTasks() 
    {
        if (isLoading || !hasMoreTasks) 
        {
            if (!hasMoreTasks) 
            {
                document.getElementById('noMoreTasks').style.display = 'block';
                document.getElementById('loader').style.display = 'none';
            }
            return;
        }
        
        isLoading = true;
        document.getElementById('loader').style.display = 'block';
        
        fetch(getFetchUrl(), {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) 
            {
                throw new Error('Network response was not ok: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            setTimeout(() => {
                if (data.tasks && data.tasks.data && data.tasks.data.length > 0) 
                {
                    renderTasks(data.tasks.data);
                    currentTasks = data.tasks.data;
                    currentPage = data.tasks.current_page + 1;
                    hasMoreTasks = data.tasks.current_page < data.tasks.last_page;
                } 
                else 
                {
                    hasMoreTasks = false;
                    document.getElementById('noMoreTasks').style.display = 'block';
                }
                isLoading = false;
                document.getElementById('loader').style.display = hasMoreTasks ? 'none' : 'none';
            }, 500);
        })
        .catch(error => {
            setTimeout(() => {
                hasMoreTasks = false;
                document.getElementById('noMoreTasks').style.display = 'block';
                document.getElementById('loader').style.display = 'none';
                isLoading = false;
                toastr.error('Error loading tasks: ' + error.message);
            }, 500);
        });
    }

    function renderTasks(tasks) 
    {
        tasks.forEach(task => {
            console.log(tasks);
            const isSelected = selectedTasks.includes(task.id.toString());
            const taskHtml = `
                <div class="lead-card mobile-card ${isSelected ? 'selected' : ''}">
                    <div class="lead-header">
                        <div class="lead-badge-container">
                            <span class="lead-badge status-${task.status.toLowerCase().replace('_', '-')}">
                                ${task.status.replace('_', ' ').toUpperCase()}
                            </span>
                            <span class="lead-time">${task.end_date ? formatDate(task.end_date) : '---'}</span>
                        </div>
                        <div class="lead-actions">
                            ${task.status !== 'completed' ? `
                            <button class="action-btn-complete text-success btn complete-task" data-task-id="${task.id}">
                                <i class="fas fa-check-circle pt-2"></i>
                            </button>
                            ` : ''}
                            <button class="action-btn-edit btn open-task-modal" data-task-id="${task.id}">
                                <i class="fas fa-edit pt-2"></i>
                            </button>
                        </div>
                    </div>
                    <div class="lead-content">
                        <div class="lead-main-info">
                            <h3 class="lead-title">${task.task}</h3>
                        </div>
                        <div class="lead-meta-grid">
                            <div class="meta-item">
                                <span class="meta-label">Assigned To</span>
                                <span class="meta-value">${task.assign_user_name || '---'}</span>
                            </div>
                            <div class="meta-item">
                                <span class="meta-label">Due Date</span>
                                <span class="meta-value">${task.end_date ? formatDate(task.end_date) : '---'}</span>
                            </div>
                            ${task.status === 'completed' ? `
                            <div class="meta-item">
                                <span class="meta-label">Completed On</span>
                                <span class="meta-value">${formatDateTime(task.updated_at)}</span>
                            </div>
                            ` : ''}
                        </div>
                        <div class="lead-meta-grid">
                            <div class="meta-item">
                                <span class="meta-label">Creator</span>
                                <span class="meta-value">${task.creator_name}</span>
                            </div>
                            <div class="meta-item">
                                <span class="meta-label">Created At</span>
                                <span class="meta-value">${formatDateTime(task.created_at)}</span>
                            </div>
                        </div>
                    </div>
                </div>`;
                
            document.getElementById('taskContainer').insertAdjacentHTML('beforeend', taskHtml);
        });

        // Add event listeners to the newly created elements
        document.querySelectorAll('.complete-task').forEach(btn => {
            btn.addEventListener('click', () => {
                const taskId = btn.getAttribute('data-task-id');
                openTaskSheet('complete', taskId);
            });
        });

        document.querySelectorAll('.open-task-modal').forEach(btn => {
            btn.addEventListener('click', () => {
                const taskId = btn.getAttribute('data-task-id');
                openTaskSheet('edit', taskId);
            });
        });

        document.querySelectorAll('.task-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const taskId = this.getAttribute('data-task-id');
                const taskCard = this.closest('.lead-card');
                
                if (this.checked) {
                    taskCard.classList.add('selected');
                    if (!selectedTasks.includes(taskId)) {
                        selectedTasks.push(taskId);
                    }
                } else {
                    taskCard.classList.remove('selected');
                    selectedTasks = selectedTasks.filter(id => id !== taskId);
                }
                
                updateSelectionUI();
            });
        });
    }

    function showShareModal() 
    {
        openTaskSheet('allocate');
    }

    function toggleFilters(e) 
    {
        e.stopPropagation();
        e.preventDefault();
        const filterControls = document.getElementById('filterControls');
        const icon = document.getElementById('filterToggle').querySelector('i');
        const filtersOpen = filterControls.classList.contains('open');
        filterControls.classList.toggle('open', !filtersOpen);
        icon.classList.toggle('fa-sliders-h', filtersOpen);
        icon.classList.toggle('fa-xmark', !filtersOpen);
    }

    function handleFormSubmit(e) 
    {
        e.preventDefault();
        const formData = new FormData(this);
        const actionType = document.getElementById('actionType').value;
        const submitBtn = document.getElementById('modalSubmitBtn');
        let actionUrl;
        switch(actionType)
        {
            case 'add':
                actionUrl = "{{ route('mobile.task.store') }}";
                break;
            case 'edit':
                const taskId = formData.get('task_id');
                actionUrl = "{{ route('mobile.task.update', ':id') }}".replace(':id', taskId);
                break;
            case 'complete':
                const completeTaskId = formData.get('task_id');
                actionUrl = "{{ route('mobile.task.complete', ':id') }}".replace(':id', completeTaskId);
                break;
            case 'allocate':
                actionUrl = "{{ route('mobile.task.allocate') }}";
                const taskIds = formData.get('task_id');
                formData.delete('task_id');
                formData.append('task_ids', taskIds);
                break;
            default:
                toastr.error('Invalid action type');
                return;
        }
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
        
        fetch(actionUrl, {
            method: 'POST',
            body: formData,
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(errorData => {
                    throw new Error('Network response was not ok: ' + response.status + ' - ' + (errorData.message || 'Unknown error'));
                });
            }
            return response.json();
        })
        .then(data => {
            submitBtn.disabled = false;
            switch(actionType) {
                case 'add':
                    submitBtn.innerHTML = 'Save Task';
                    break;
                case 'edit':
                    submitBtn.innerHTML = 'Update Task';
                    break;
                case 'complete':
                    submitBtn.innerHTML = 'Complete Task';
                    break;
                case 'allocate':
                    submitBtn.innerHTML = 'Allocate Tasks';
                    break;
            }
            
            if (data.success) {
                toastr.success(data.message || 'Operation completed successfully!');
                closeTaskSheet();
                resetTaskState();
                loadMoreTasks();
            } else {
                toastr.error(data.message || 'Error performing operation');
            }
        })
        .catch(error => {
            submitBtn.disabled = false;
            switch(actionType) {
                case 'add':
                    submitBtn.innerHTML = 'Save Task';
                    break;
                case 'edit':
                    submitBtn.innerHTML = 'Update Task';
                    break;
                case 'complete':
                    submitBtn.innerHTML = 'Complete Task';
                    break;
                case 'allocate':
                    submitBtn.innerHTML = 'Allocate Tasks';
                    break;
            }
            
            toastr.error('Error: ' + error.message);
        });
    }
    function debounce(func, wait) {
        let timeout;
        return function(...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), wait);
        };
    }

    function formatDate(dateString) {
        try {
            const date = new Date(dateString);
            if (isNaN(date.getTime())) return '---';
            return date.toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric' });
        } catch (e) {
            return '---';
        }
    }

    function formatDateTime(dateString) 
    {
        try 
        {
            const date = new Date(dateString);
            if (isNaN(date.getTime())) return '---';
            return date.toLocaleDateString('en-US', { day: 'numeric', month: 'short', year: 'numeric' }) +
                   ' ' + date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
        } catch (e) {
            return '---';
        }
    }
</script>
@endsection