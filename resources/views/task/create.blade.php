@extends('layouts.app')
@section('title', 'Task Management Create| Pro-leadexpertz')
@section('content')
@include('modals.repeat-info');
@include('modals.create-task-project')
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">{{ isset($task) ? 'Edit' : 'Create' }} Task</h4>
                    <div class="page-title-right">
                        <!-- <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('task.list') }}">Tasks</a>
                            </li>
                            <li class="breadcrumb-item active">{{ isset($task) ? 'Edit' : 'Create' }} Task</li>
                        </ol> -->
                        @if($user_type == 'super_admin' || $user_type == 'divisional_head')
                        <button class="btn btn-outline-primary btn-rounded" data-bs-toggle="modal" data-bs-target="#createProjectModal">
                            <i class="fas fa-folder-plus me-2"></i> New Project
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">{{ isset($task) ? 'Edit' : 'Create New' }} Task</h4>
                        <form id="taskForm" method="post" action="{{ route('task.store', isset($task) ? $task->id : null) }}" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group row mb-4">
                                <label for="taskname" class="col-form-label col-lg-2">Task Name <span class="text-danger">*</span></label>
                                <div class="col-lg-10">
                                    <input id="taskname" name="taskname" type="text" class="form-control" 
                                        placeholder="Enter Task Name..." 
                                        value="{{ $task->name ?? old('taskname') }}" required>
                                </div>
                            </div>
                            
                            <div class="form-group row mb-4">
                                <label class="col-form-label col-lg-2">Task Description <span class="text-danger">*</span></label>
                                <div class="col-lg-10">
                                    <textarea id="taskdesc-editor" name="description" class="form-control" required>
                                        {{ $task->description ?? old('description') }}
                                    </textarea>
                                </div>
                            </div>
                            
                            <div class="form-group row mb-4">
                                <label class="col-form-label col-lg-2">Task Date <span class="text-danger">*</span></label>
                                <div class="col-lg-10">
                                    <div class="input-daterange input-group" data-provide="datepicker" data-date-format="dd/mm/yyyy">
                                        <input type="text" class="form-control" placeholder="Start Date" name="start_date" 
                                            value="{{ isset($task) ? \Carbon\Carbon::parse($task->start_date)->format('d/m/Y') : (old('start_date') ?: '') }}" 
                                            autocomplete="off" required>
                                        <span class="input-group-text">to</span>
                                        <input type="text" class="form-control" placeholder="End Date" name="end_date" 
                                            value="{{ isset($task) ? \Carbon\Carbon::parse($task->end_date)->format('d/m/Y') : (old('end_date') ?: '') }}" 
                                            autocomplete="off" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group row mb-4">
                                <label class="col-form-label col-lg-2">Priority</label>
                                <div class="col-lg-10">
                                    <select class="select2" name="priority">
                                        @foreach($priorities as $priority)
                                            <option value="{{ $priority }}" 
                                                {{ (isset($task) && $task->priority == $priority) || old('priority', 'medium') == $priority ? 'selected' : '' }}>
                                                {{ ucfirst($priority) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @if($user_type == 'super_admin' || $user_type == 'divisional_head')
                            <div class="form-group row mb-4">
                                <label class="col-form-label col-lg-2">Project</label>
                                <div class="col-lg-10">
                                    <select class="select2" name="task_project_id" id="projectSelect">
                                        <option value="">Individual Task (No Project)</option>
                                        @foreach($task_projects as $task_project)
                                            <option value="{{ $task_project->id }}" 
                                                {{ (isset($task) && $task->task_project_id == $task_project->id) || old('task_project_id') == $task_project->id ? 'selected' : '' }}>
                                                {{ $task_project->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">Select a project to make this a project task, or leave empty for individual task</small>
                                </div>
                            </div>
                            @endif

                            @if($user_type == 'super_admin' || $user_type == 'divisional_head')
                            <div class="form-group row mb-4">
                                <label class="col-form-label col-lg-2">Team Members</label>
                                <div class="col-lg-10" id="teamMembersContainer">
                                    @php
                                        $oldMembers = old('team_members', []);
                                        $oldFiles = old('member_files', []);
                                    @endphp

                                    @if(count($oldMembers) > 0)
                                        @foreach($oldMembers as $index => $memberId)
                                            <div class="member-row mb-3 row align-items-center">
                                                <div class="col-md-6">
                                                    <select class="select2" name="team_members[]">
                                                        <option value="">Select Team Member</option>
                                                        @foreach($teamMembers as $memberOption)
                                                            <option value="{{ $memberOption->id }}" {{ $memberId == $memberOption->id ? 'selected' : '' }}>
                                                                {{ $memberOption->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-4">
                                                    <input class="form-control" type="file" name="member_files[]">
                                                </div>
                                                <div class="col-md-2">
                                                    <button type="button" class="btn btn-danger remove-member" {{ $loop->first ? 'disabled' : '' }}>
                                                        <i class="fas fa-trash-alt"></i> Remove
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    @elseif(isset($task) && count($assignedMembers) > 0)
                                        @foreach($assignedMembers as $index => $member)
                                            <div class="member-row mb-3 row align-items-center">
                                                <div class="col-md-6">
                                                    <select class="select2" name="team_members[]">
                                                        <option value="">Select Team Member</option>
                                                        @foreach($teamMembers as $memberOption)
                                                            <option value="{{ $memberOption->id }}" {{ $member->user_id == $memberOption->id ? 'selected' : '' }}>
                                                                {{ $memberOption->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-4">
                                                    <input class="form-control" type="file" name="member_files[]">
                                                    @if($member->file_path)
                                                        <input type="hidden" name="existing_files[{{ $index }}]" value="{{ $member->file_path }}">
                                                        <input type="hidden" name="existing_file_names[{{ $index }}]" value="{{ $member->file_name }}">
                                                        <input type="hidden" name="existing_file_types[{{ $index }}]" value="{{ $member->file_type }}">
                                                        <div class="mt-2">
                                                            <small class="text-muted">Current file: 
                                                                <a href="{{ Storage::url($member->file_path) }}" target="_blank">
                                                                    {{ $member->file_name ?? basename($member->file_path) }}
                                                                </a>
                                                                <button type="button" class="btn btn-sm btn-outline-danger remove-file" data-index="{{ $index }}">
                                                                    <i class="fas fa-trash-alt"></i> Remove
                                                                </button>
                                                            </small>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="col-md-2">
                                                    <button type="button" class="btn btn-danger remove-member" {{ $loop->first ? 'disabled' : '' }}>
                                                        <i class="fas fa-trash-alt"></i> Remove
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="member-row mb-3 row align-items-center">
                                            <div class="col-md-6">
                                                <select class="select2" name="team_members[]">
                                                    <option value="">Select Team Member</option>
                                                    @foreach($teamMembers as $member)
                                                        <option value="{{ $member->id }}">{{ $member->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <input class="form-control" type="file" name="member_files[]">
                                            </div>
                                            <div class="col-md-2">
                                                <button type="button" class="btn btn-danger remove-member" disabled>
                                                    <i class="fas fa-trash-alt"></i> Remove
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <div class="row justify-content-end">
                                    <div class="col-lg-10">
                                        <button type="button" id="addMemberBtn" class="btn btn-success">
                                            <i class="fas fa-plus"></i> Add Member
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label for="repeatInterval" class="col-form-label col-lg-2">
                                    Repeat Settings
                                    <i class="fas fa-info-circle text-primary" style="cursor:pointer;" data-bs-toggle="modal" data-bs-target="#repeatInfoModal"></i>
                                </label>
                                <div class="col-lg-10">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Repeat Type</label>
                                                <select class="select2" name="repeat_interval" id="repeat-type">
                                                    <option value="none">No Repeat</option>
                                                    <option value="daily">Daily</option>
                                                    <option value="weekly">Weekly</option>
                                                    <option value="monthly">Monthly</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Repeat Every</label>
                                                <div class="input-group">
                                                    <input type="number" class="form-control" name="repeat_count" min="0" max="12" value="{{ old('repeat_count', $task->repeat_count ?? 0) }}">
                                                    <span class="input-group-text">occurrences</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                            <div class="row justify-content-end">
                                <div class="col-lg-10">
                                    <button type="submit" class="btn btn-primary" id="SubmitBtn">
                                        <span id="SubmitText"> <i class="fas fa-save"></i> {{ isset($task) ? 'Update' : 'Create' }} Task</span>
                                        <span id="SubmitSpinner" class="d-none">
                                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Please wait...
                                        </span>
                                    </button>
                                    <a href="{{ route('task.list') }}" class="btn btn-secondary ms-2">
                                        <i class="fas fa-times"></i> Cancel
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() 
    {
        const $intervalInput = $('#repeat-interval');
        const $countInput = $('#repeat-count');
        const $visualIndicator = $('#repeat-visual');
        const $summaryText = $('#repeat-summary');

        function updateRepeatVisualization() 
        {
            const interval = parseInt($intervalInput.val()) || 1;
            const count = parseInt($countInput.val()) || 1;

            const percentage = Math.min(100, (count / 10) * 100);
            $visualIndicator.css('width', `${percentage}%`);
            $visualIndicator.attr('aria-valuenow', count);
            if (count === 1) 
            {
                $summaryText.text('Task will occur 1 time (no repetition)');
            } 
            else 
            {
                const totalDays = interval * (count - 1);
                $summaryText.text(`Task will repeat every ${interval} days for ${count} occurrences (${totalDays} total days)`);
            }
        }
        updateRepeatVisualization();
        $intervalInput.add($countInput).on('input change', function () 
        {
            let val = parseInt($(this).val()) || 1;
            if (val < 1) val = 1;
            if (this.id === 'repeat-interval' && val > 365) val = 365;
            if (this.id === 'repeat-count' && val > 100) val = 100;

            $(this).val(val);
            updateRepeatVisualization();
        });

        $('#taskdesc-editor').summernote({
            height: 200,
            toolbar: [
                ['style', ['bold', 'italic', 'underline']],
                ['font', ['strikethrough', 'superscript', 'subscript']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['view', ['fullscreen', 'codeview']]
            ],
            callbacks: {
                onInit: function() 
                {
                    var content = $('#taskdesc-editor').val();
                    $('#taskdesc-editor').summernote('code', content);
                }
            }
        });

        $('.input-daterange').datepicker({
            todayHighlight: true,
            autoclose: true,
            format: 'dd/mm/yyyy'
        }).on('changeDate', function (e) 
        {
            const startDateStr = $('input[name="start_date"]').val();
            const endDateStr = $('input[name="end_date"]').val();
            if (startDateStr) 
            {
                const startDate = moment(startDateStr, 'DD/MM/YYYY');
                const endDate = moment(endDateStr, 'DD/MM/YYYY');
                if (!endDateStr || endDate.isBefore(startDate)) 
                {
                    $('input[name="end_date"]').val(startDate.format('DD/MM/YYYY'));
                }
            }
        });

        $(document).on('click', '.remove-file', function() 
        {
            const index = $(this).data('index');
            const $container = $(this).closest('.mt-2');
            $container.after(`
                <input type="hidden" name="removed_files[${index}]" value="1">
            `);
            $container.remove();
        });

        let memberCounter = {{ $memberIndex ?? 1 }};
        $('#addMemberBtn').click(function() 
        {
            const newRow = `
                <div class="member-row mb-3 row align-items-center">
                    <div class="col-md-6">
                        <select class="form-select" name="team_members[]">
                            <option value="">Select Team Member</option>
                            @foreach($teamMembers as $member)
                                <option value="{{ $member->id }}">{{ $member->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <div class="mt-4 mt-md-0">
                            <input class="form-control" type="file" name="member_files[]">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="mt-2 mt-md-0 d-grid">
                            <button type="button" class="btn btn-danger remove-member">
                                <i class="fas fa-trash-alt"></i> Remove
                            </button>
                        </div>
                    </div>
                </div>
            `;
            $('#teamMembersContainer').append(newRow);
            memberCounter++;
            if ($('#teamMembersContainer .member-row').length > 1) 
            {
                $('.remove-member').prop('disabled', false);
            }
        });

        $(document).on('click', '.remove-member', function() 
        {
            if ($('#teamMembersContainer .member-row').length > 1) 
            {
                $(this).closest('.member-row').remove();
                if ($('#teamMembersContainer .member-row').length === 1) 
                {
                    $('.remove-member').prop('disabled', true);
                }
            }
        });

        $('#taskForm').validate({
            rules: {
                taskname: {
                    required: true,
                    maxlength: 255
                },
                description: {
                    required: true
                },
                start_date: {
                    required: true,
                    date: true
                },
                end_date: {
                    required: true,
                    date: true,
                    greaterThanStart: true
                }
            },
            messages: {
                taskname: {
                    required: "Please enter task name",
                    maxlength: "Task name cannot be longer than 255 characters"
                },
                description: {
                    required: "Please enter task description"
                },
                start_date: {
                    required: "Please select start date",
                    date: "Please enter a valid date"
                },
                end_date: {
                    required: "Please select end date",
                    date: "Please enter a valid date",
                    greaterThanStart: "End date must be after start date"
                }
            },
            errorElement: 'span',
            errorPlacement: function(error, element) 
            {
                error.addClass('invalid-feedback');
                element.closest('.form-group').append(error);
            },
            highlight: function(element, errorClass, validClass) 
            {
                $(element).addClass('is-invalid');
            },
            unhighlight: function(element, errorClass, validClass) 
            {
                $(element).removeClass('is-invalid');
            }
        });

        $.validator.addMethod("greaterThanStart", function(value, element) 
        {
            var startDate = $('input[name="start_date"]').val();
            if (!startDate || !value) return true;
            
            try 
            {
                var start = moment(startDate, "DD/MM/YYYY");
                var end = moment(value, "DD/MM/YYYY");
                return end.isSameOrAfter(start);
            } 
            catch (e) 
            {
                return false;
            }
        }, "End date must be after start date");

    });
    $('#SubmitBtn').closest('form').on('submit', function ()
    {
        $('#SubmitBtn').prop('disabled', true);
        $('#SubmitText').addClass('d-none');
        $('#SubmitSpinner').removeClass('d-none');
    });

</script>
@endsection