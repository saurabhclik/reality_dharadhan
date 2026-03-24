@extends('layouts.app')
@section('title', 'MIS Targets')
@section('content')
@include('modals.mis-module-info')
<style>
    .week-cell 
    {
        min-width: 80px;
        text-align: center;
    }

    .target-input,
    .achieved-input 
    {
        text-align: center;
        padding: 0.25rem;
        height: 35px;
        font-weight: 500;
    }

    .percentage-cell 
    {
        min-width: 60px;
        text-align: center;
        font-weight: 600;
        background-color: rgba(52, 152, 219, 0.05);
    }

    .points-header 
    {
        min-width: 250px;
        position: sticky;
        left: 0;
        background-color: white;
        z-index: 2;
        box-shadow: 2px 0 5px rgba(0, 0, 0, 0.05);
    }

    .table-container 
    {
        max-height: 70vh;
        overflow: auto;
    }

    .sticky-header 
    {
        position: sticky;
        top: 0;
        z-index: 3;
        background-color: white;
    }

    .sticky-column 
    {
        position: sticky;
        left: 0;
        background-color: white;
        z-index: 1;
        box-shadow: 2px 0 5px rgba(0, 0, 0, 0.05);
    }

    .week-input-container 
    {
        position: relative;
        margin-bottom: 1.5rem;
    }

    .week-input-container .week-label 
    {
        position: absolute;
        top: -10px;
        left: 12px;
        background: white;
        padding: 0 8px;
        font-size: 0.85rem;
        color: #6c757d;
        z-index: 5;
    }

    .week-input-container input 
    {
        padding-top: 1.4rem;
        padding-bottom: 0.8rem;
    }

    .percentage-badge 
    {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
    }

    .percentage-100 
    {
        background-color: rgba(40, 167, 69, 0.15);
        color: #28a745;
    }

    .percentage-50 
    {
        background-color: rgba(255, 193, 7, 0.15);
        color: #ffc107;
    }

    .percentage-0
    {
        background-color: rgba(220, 53, 69, 0.15);
        color: #dc3545;
    }

    .filter-form .form-select,
    .filter-form .form-control 
    {
        height: 42px;
    }

    .table th 
    {
        vertical-align: middle;
    }
    .info-icon 
    {
        cursor: pointer;
        font-size: 1.2rem;
        color: #17a2b8;
        margin-left: 10px;
    }

    .info-icon:hover 
    {
        color: #138496;
    }
</style>
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                   <div class="d-flex align-items-center">
                        <h4 class="mb-0">MIS Weekly Targets</h4>
                        <i class="fas fa-info-circle info-icon" 
                           data-bs-toggle="tooltip" 
                           title="Click for info about the MIS module" 
                           data-bs-target="#misInfoModal"></i>
                    </div>
                    <div class="page-title-right">
                        <button class="btn btn-primary btn-small px-4 py-1 rounded-pill fw-bold text-white shadow-lg" data-bs-toggle="modal" data-bs-target="#setTargetModal">
                            <i class="fa fa-plus"></i> Add Weekly Target
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <form action="{{ route('mis.targets') }}" method="GET" class="filter-form">
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="teamFilter" class="form-label">Team</label>
                            <select class="form-control select2" id="teamFilter" name="team_id">
                                <option value="">-- Select Team --</option>
                                @foreach($teams as $team)
                                    <option value="{{ $team->id }}" {{ $teamFilter == $team->id ? 'selected' : '' }}>{{ $team->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="yearFilter" class="form-label">Year</label>
                            <select class="form-control select2" id="yearFilter" name="year">
                                @for($y = date('Y') - 2; $y <= date('Y') + 2; $y++)
                                    <option value="{{ $y }}" {{ $yearFilter == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-4 d-flex align-items-end gap-2">
                            <button type="submit" class="btn btn-primary" id="SubmitBtn">
                                <span id="SubmitText">Apply Filters</span>
                                <span id="SubmitSpinner" class="d-none">
                                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Please wait...
                                </span>
                            </button>
                            <a href="{{ route('mis.targets') }}" class="btn btn-outline-secondary">Reset</a>
                        </div>
                    </div>
                </div>
            </div>
            @if(!empty($teamFilter))
                @php
                    $teamAutoAssign = DB::table('mis_weekly_targets')
                        ->where('team_id', $teamFilter)
                        ->value('auto_assign') ?? false;
                @endphp
                @if($teamAutoAssign)
                    <div class="alert alert-info alert-dismissible fade show mb-3" role="alert">
                        <i class="fa fa-sync-alt me-2"></i>
                        <strong>Auto-Assign Enabled:</strong> Targets will be automatically assigned for next week when current week ends.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
            @endif
        </form>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <!-- <button id="exportBtn" class="btn btn-sm btn-outline-secondary me-2">
                        <i class="bi bi-download me-1"></i> Export
                    </button> -->
                </div>
                @if(!empty($teamFilter))
                <div>
                    <button class="btn btn-success btn-sm" id="quickEntryBtn" data-bs-toggle="modal" data-bs-target="#quickEntryModal">
                        <i class="fa fa-plus-circle me-1"></i> Quick Entry for All Points
                    </button>
                </div>
                @endif
            </div>
            <div id="printableArea">
                <div class="table-container">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover mb-0">
                            <thead class="sticky-header">
                                <tr>
                                    <th class="points-header sticky-column">POINTS</th>
                                    @foreach($displayWeeks as $week)
                                        <th colspan="3" class="text-center">
                                            @if(isset($weekDates[$week]))
                                                {{ $weekDates[$week] }}
                                            @endif
                                        </th>
                                    @endforeach
                                </tr>
                                <tr>
                                    <th class="points-header sticky-column"></th>
                                    @foreach($displayWeeks as $week)
                                        <th class="text-center week-cell">Target</th>
                                        <th class="text-center week-cell">Achieved</th>
                                        <th class="text-center week-cell">%</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $mapKey = $teamFilter ?: 'all';
                                @endphp
                                @foreach($points as $point)
                                    <tr>
                                        <td class="points-header sticky-column">{{ $point }}</td>
                                        @foreach($displayWeeks as $week)
                                            @php
                                                $weekTarget = $targetsMap[$mapKey][$yearFilter][$point][$week] ?? 0;
                                                $weekAchieved = $achievedMap[$point][$week] ?? 0;
                                                $weekPercent = $percentageMap[$point][$week] ?? 0;
                                                $percentClass = $weekPercent >= 100 ? 'percentage-100' : ($weekPercent > 0 ? 'percentage-50' : 'percentage-0');
                                            @endphp
                                            @php
                                                $canEdit = isset($teamFilter) && $teamFilter;
                                            @endphp

                                            <td class="text-center">
                                                <input type="number"
                                                    class="form-control form-control-sm target-input {{ $canEdit ? 'admin-edit' : 'all-input cursor-pointer' }}"
                                                    value="{{ $weekTarget }}"
                                                    {{ $canEdit ? '' : 'readonly' }}
                                                    data-point="{{ $point }}"
                                                    data-week="{{ $week }}"
                                                    data-team="{{ $teamFilter ?? '' }}"
                                                    data-year="{{ $yearFilter }}">
                                            </td>

                                            <td class="text-center">
                                                <input type="number"
                                                    class="form-control form-control-sm achieved-input {{ $canEdit ? 'admin-edit-achieved' : 'all-input cursor-pointer' }}"
                                                    value="{{ $weekAchieved }}"
                                                    {{ $canEdit ? '' : 'readonly' }}
                                                    data-point="{{ $point }}"
                                                    data-week="{{ $week }}"
                                                    data-team="{{ $teamFilter ?? '' }}"
                                                    data-year="{{ $yearFilter }}">
                                            </td>


                                           <td class="text-center percentage-cell">
                                            @php
                                                $taskPercent = $normalizedPercentageMap[$point][$week] ?? 0;
                                                $percentClass = $taskPercent >= 100 ? 'percentage-100' : ($taskPercent > 0 ? 'percentage-50' : 'percentage-0');
                                            @endphp
                                            <span class="percentage-badge {{ $percentClass }}">
                                                {{ $taskPercent }}%
                                            </span>
                                        </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="quickEntryModal" tabindex="-1" aria-labelledby="quickEntryModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title d-flex align-items-center" id="quickEntryModalLabel">
                            <i class="fa fa-calendar-plus me-2"></i> Daily Entry for All Points
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form id="quickEntryForm">
                            @csrf
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Team</label>
                                    <input type="text" class="form-control" value="{{ $teams->where('id', $teamFilter)->first()->name ?? '' }}" readonly>
                                    <input type="hidden" id="quickEntryTeamId" value="{{ $teamFilter }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Entry Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="quickEntryDate" value="{{ date('Y-m-d') }}" required>
                                    <small class="text-muted">Select the date for which you want to enter data</small>
                                </div>
                            </div>           
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="table-success">
                                        <tr>
                                            <th width="60%">Point</th>
                                            <th width="40%">Achieved Value</th>
                                        </tr>
                                    </thead>
                                    <tbody id="quickEntryPointsBody">
                                        @foreach($points as $point)
                                        <tr>
                                            <td class="fw-semibold">{{ $point }}</td>
                                            <td>
                                                <input type="number" 
                                                class="form-control form-control-sm point-value-input" 
                                                data-point="{{ $point }}"
                                                placeholder="Enter value" 
                                                min="0"
                                                value="0">
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td class="fw-bold text-end">Total:</td>
                                            <td class="fw-bold" id="totalValue">0</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            
                            <div class="d-flex justify-content-between mt-4">
                                <div>
                                    <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-success" id="saveQuickEntryBtn">
                                        <span id="saveQuickEntryText">Save Daily Entries</span>
                                        <span id="saveQuickEntrySpinner" class="d-none">
                                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="setTargetModal" tabindex="-1" aria-labelledby="setTargetModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title d-flex align-items-center" id="setTargetModalLabel">
                            <i class="bi bi-bar-chart-line me-2"></i> Set Weekly MIS Targets
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('mis.targets.save') }}" method="POST" id="misTargetForm" class="needs-validation" novalidate>
                            @csrf
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Team <span class="text-danger">*</span></label>
                                    <select name="team_id" class="form-control select2" id="modalTeamSelect" required>
                                        <option value="">-- Select Team --</option>
                                        @foreach($teams as $team)
                                            <option value="{{ $team->id }}" {{ $teamFilter == $team->id ? 'selected' : '' }}>{{ $team->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback">Please select a team.</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Year <span class="text-danger">*</span></label>
                                    <input type="number" name="year" class="form-control" value="{{ $yearFilter }}" required readonly>
                                    <div class="invalid-feedback">Please enter a valid year.</div>
                                </div>
                            </div>
                            <div class="row g-3 mb-4">
                                <div class="col-12">
                                    <div class="card border-0 bg-light">
                                        <div class="card-body">
                                            <div class="form-check form-switch mb-2">
                                                <input class="form-check-input" type="checkbox" name="auto_assign" id="autoAssignSwitch" value="1">
                                                <label class="form-check-label fw-semibold" for="autoAssignSwitch">
                                                    <i class="fa fa-sync-alt me-2 ms-2"></i>Enable Auto-Assign Targets
                                                </label>
                                            </div>
                                            <small class="form-text text-muted">
                                                <i class="fa fa-info-circle me-1"></i>
                                                When enabled, targets will be automatically assigned for the next week after the current week ends.
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div id="pointsContainer">                             
                            </div>
                            <div class="d-flex justify-content-end mt-3">
                                <button type="submit" class="btn btn-primary" id="SubmitTargetBtn">
                                    <span id="SubmitTargetText">Save Target</span>
                                    <span id="SubmitTargetSpinner" class="d-none">
                                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Please wait...
                                    </span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="adminEditModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Confirm Update</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>Previous Value: <span id="prevTarget"></span></p>
                        <p>New Value: <span id="newTarget" contenteditable="true"></span></p>
                        <input type="hidden" id="modalPoint">
                        <input type="hidden" id="modalWeek">
                        <input type="hidden" id="modalTeam">
                        <input type="hidden" id="modalYear">
                        <input type="hidden" id="modalType">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" id="confirmUpdateBtn">
                            <span id="SubmitConfirmText">Confirm Update</span>
                            <span id="SubmitConfirmSpinner" class="d-none">
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Please wait...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() 
    {
        const container = document.querySelector('.target-rows');
        
        function updateTaskOptions() 
        {
            const selectedTasks = Array.from(container.querySelectorAll('select[name="tasks[]"]'))
                .map(s => s.value)
                .filter(v => v !== "");

            container.querySelectorAll('select[name="tasks[]"]').forEach(select => {
                const currentValue = select.value;
                select.querySelectorAll('option').forEach(option => {
                    if (option.value === "") return;
                    if (option.value === currentValue) 
                    {
                        option.disabled = false;
                    } 
                    else if (selectedTasks.includes(option.value)) 
                    {
                        option.disabled = true;
                    } 
                    else 
                    {
                        option.disabled = false;
                    }
                });
            });
        }

        container.addEventListener('click', function(e) 
        {
            const row = e.target.closest('.target-row');
            if (e.target.closest('.add-row')) 
            {
                const clone = row.cloneNode(true);
                clone.querySelectorAll('select[name="tasks[]"]').forEach(s => s.selectedIndex = 0);
                clone.querySelectorAll('input[name="targets[]"]').forEach(i => i.value = '');
                clone.querySelectorAll('input[name="week[]"]').forEach(i => i.value = row.querySelector('input[name="week[]"]').value);
                container.appendChild(clone);
                updateTaskOptions();
            }

            if (e.target.closest('.remove-row')) 
            {
                if (container.querySelectorAll('.target-row').length > 1) 
                {
                    row.remove();
                    updateTaskOptions();
                } 
                else 
                {
                    toastr.error("At least one task is required.");
                }
            }
        });

        container.addEventListener('change', function(e) 
        {
            if (e.target.matches('select[name="tasks[]"]')) 
            {
                updateTaskOptions();
            }
        });

        const form = document.getElementById('misTargetForm');
        const submitBtn = document.getElementById('SubmitBtn');
        const submitText = document.getElementById('SubmitText');
        const submitSpinner = document.getElementById('SubmitSpinner');

        form.addEventListener('submit', function() 
        {
            submitBtn.disabled = true;
            submitText.classList.add('d-none');
            submitSpinner.classList.remove('d-none');
        });

        updateTaskOptions();
    });

    $(document).ready(function() 
    {
        let currentWeekData = null;
        
        $(document).on('change', '.admin-edit', function() 
        {
            var $input = $(this);
            var prev = $input.prop('defaultValue');
            var newVal = $input.val();
            var point = $input.data('point');
            var week = $input.data('week');
            var team = $input.data('team');
            var year = $input.data('year');
            
            $('#prevTarget').text(prev);
            $('#newTarget').text(newVal);
            $('#modalPoint').val(point);
            $('#modalWeek').val(week);
            $('#modalTeam').val(team);
            $('#modalYear').val(year);
            $('#modalType').val('target');
            $('#adminEditModal').modal('show');
            $input.val(prev);
        });
        
        $(document).on('change', '.admin-edit-achieved', function() 
        {
            var $input = $(this);
            var prev = $input.prop('defaultValue');
            var point = $input.data('point');
            var week = $input.data('week');
            var team = $input.data('team');
            var year = $input.data('year');
            $input.val(prev);
            toastr.info('Loading daily data...');
            
            $.ajax({
                url: "{{ route('mis.get.week.daily.data') }}",
                method: "GET",
                data: {
                    team_id: team,
                    week: week,
                    year: year,
                    point: point
                },
                success: function(data) 
                {
                    if (data.success) 
                    {
                        currentWeekData = data;
                        showDailyDataModal(data);
                    } 
                    else 
                    {
                        toastr.error('Error loading daily data: ' + (data.message || 'Unknown error'));
                    }
                },
                error: function(xhr, status, error) 
                {
                    console.error('AJAX Error:', error);
                    toastr.error('Error loading daily data');
                }
            });
        });

        function showDailyDataModal(weekData) 
        {
            $('#dailyDataModal').remove();
            var modalHtml = `
                <div class="modal fade" id="dailyDataModal" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title">
                                    <i class="fa fa-calendar me-2"></i>
                                    Daily Data - Week ${weekData.week} - ${weekData.point}
                                </h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                ${weekData.daily_data && weekData.daily_data.length > 0 ? `
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Current Value</th>
                                                    <th>New Value</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                ${weekData.daily_data.map(day => `
                                                    <tr>
                                                        <td class="fw-bold">${day.date}</td>
                                                        <td>${day.value}</td>
                                                        <td>
                                                            <input type="number" class="form-control form-control-sm daily-value-input" 
                                                                    value="${day.value}" min="0" data-date="${day.date}">
                                                        </td>
                                                        <td>
                                                            <button type="button" class="btn btn-sm btn-primary update-day-btn" 
                                                                    data-date="${day.date}">
                                                                <i class="fa fa-save me-1"></i>Update
                                                            </button>
                                                        </td>
                                                    </tr>
                                                `).join('')}
                                            </tbody>
                                        </table>
                                    </div>
                                ` : `
                                    <div class="text-center text-muted py-4">
                                        <i class="fa fa-inbox fa-3x mb-3"></i>
                                        <p>No daily data found for this week</p>
                                    </div>
                                `}
                                
                                <div class="mt-4 p-3 bg-light rounded">
                                    <h6 class="mb-3">
                                        <i class="fa fa-plus-circle me-2"></i>Add New Daily Entry
                                    </h6>
                                    <div class="row g-2 align-items-end">
                                        <div class="col-md-5">
                                            <label class="form-label small">Date</label>
                                            <input type="date" class="form-control form-control-sm" id="newEntryDate">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label small">Value</label>
                                            <input type="number" class="form-control form-control-sm" id="newEntryValue" placeholder="Enter value" min="0">
                                        </div>
                                        <div class="col-md-3">
                                            <button type="button" class="btn btn-sm btn-success w-100" id="addNewEntryBtn">
                                                <i class="fa fa-plus me-1"></i>Add Entry
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                    <i class="fa fa-times me-1"></i>Close
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            $('body').append(modalHtml);
            $('#dailyDataModal').data('weekData', weekData);
            $('#dailyDataModal').modal('show');
        }

        $(document).on('click', '#dailyDataModal .update-day-btn', function() 
        {
            var weekData = $('#dailyDataModal').data('weekData');
            if (!weekData) return;
            var date = $(this).data('date');
            var newValue = $(this).closest('tr').find('.daily-value-input').val();
            if (newValue === '' || parseInt(newValue) < 0) 
            {
                toastr.error('Please enter a valid value');
                return;
            }
            
            updateDailyValue(weekData.point, weekData.week, weekData.team_id, weekData.year, date, newValue);
        });
        
        $(document).on('click', '#dailyDataModal #addNewEntryBtn', function() 
        {
            var weekData = $('#dailyDataModal').data('weekData');
            if (!weekData) return;
            
            var date = $('#newEntryDate').val();
            var value = $('#newEntryValue').val();
            
            if (!date) 
            {
                toastr.error('Please select a date');
                return;
            }
            
            if (value === '' || parseInt(value) < 0) 
            {
                toastr.error('Please enter a valid value');
                return;
            }
            
            updateDailyValue(weekData.point, weekData.week, weekData.team_id, weekData.year, date, value);
        });
        
        function updateDailyValue(point, week, team, year, date, value) 
        {
            $.ajax({
                url: "{{ route('mis.admin.update.daily.achieved') }}",
                method: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    point: point,
                    week: week,
                    value: value,
                    team_id: team,
                    year: year,
                    date: date
                },
                beforeSend: function() 
                {
                    $('#dailyDataModal .btn').prop('disabled', true);
                    toastr.info('Updating...');
                },
                success: function(data) 
                {
                    if (data.success) 
                        {
                        toastr.success('Daily value updated successfully');
                        $('#dailyDataModal').modal('hide');
                        setTimeout(function() 
                        {
                            location.reload();
                        }, 1000);
                    } else {
                        toastr.error(data.message || 'Failed to update daily value');
                        $('#dailyDataModal .btn').prop('disabled', false);
                    }
                },
                error: function(xhr, status, error) 
                {
                    console.error('Update error:', error);
                    toastr.error('Error updating daily value');
                    $('#dailyDataModal .btn').prop('disabled', false);
                }
            });
        }
        
        $('#adminEditModal').on('shown.bs.modal', function() 
        {
            $('#newTarget').focus();
        });

        $('#confirmUpdateBtn').click(function() 
        {
            $(this).prop("disabled", true);
            $("#SubmitConfirmText").addClass("d-none");
            $("#SubmitConfirmSpinner").removeClass("d-none");
            
            var point = $('#modalPoint').val();
            var week = $('#modalWeek').val();
            var newVal = $('#newTarget').text().trim();
            var team = $('#modalTeam').val();
            var year = $('#modalYear').val();
            var type = $('#modalType').val();
            
            if (newVal === '' || isNaN(newVal) || parseInt(newVal) < 0) 
            {
                toastr.error('Please enter a valid positive number');
                resetConfirmButton();
                return;
            }

            var url = type === 'target' ? "{{ route('mis.admin.update') }}" : "{{ route('mis.admin.update.daily.achieved') }}";
            
            $.ajax({
                url: url,
                method: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    point: point,
                    week: week,
                    value: newVal,
                    team_id: team,
                    year: year
                },
                success: function(data) 
                {
                    if (data.success) 
                    {
                        toastr.success(type === 'target' ? 'Target updated successfully' : 'Achieved value updated successfully');
                        var selector = type === 'target' ? 
                            '.admin-edit[data-point="' + point + '"][data-week="' + week + '"]' :
                            '.admin-edit-achieved[data-point="' + point + '"][data-week="' + week + '"]';
                        
                        $(selector).val(newVal);
                        $(selector).prop('defaultValue', newVal);
                        setTimeout(function() 
                        {
                            location.reload();
                        }, 1000);
                    } 
                    else 
                    {
                        toastr.error(data.message || (type === 'target' ? 'Failed to update target' : 'Failed to update achieved value'));
                    }
                    $('#adminEditModal').modal('hide');
                    resetConfirmButton();
                },
                error: function(xhr, status, error) 
                {
                    toastr.error(type === 'target' ? 'Error updating target' : 'Error updating achieved value');
                    $('#adminEditModal').modal('hide');
                    resetConfirmButton();
                }
            });
        });

        function resetConfirmButton() 
        {
            $('#confirmUpdateBtn').prop("disabled", false);
            $("#SubmitConfirmText").removeClass("d-none");
            $("#SubmitConfirmSpinner").addClass("d-none");
        }

        $('#adminEditModal').on('hidden.bs.modal', function() 
        {
            resetConfirmButton();
        });

        $('#SubmitBtn').closest('form').on('submit', function() 
        {
            $('#SubmitBtn').prop('disabled', true);
            $('#SubmitText').addClass('d-none');
            $('#SubmitSpinner').removeClass('d-none');
        });
        
        $("#misTargetForm").on("submit", function() 
        {
            $("#SubmitTargetBtn").prop("disabled", true);
            $("#SubmitTargetText").addClass("d-none");
            $("#SubmitTargetSpinner").removeClass("d-none");
        });

        $('.all-input').on('click', function()
        {
            toastr.error('Please select a team to access the input for editing');
        });
    });

    $(document).ready(function() 
    {
        $('select[name="team_id"]').on('change', function()
        {
            var teamId = $(this).val();
            if (teamId) 
            {
                checkAutoAssignStatus(teamId);
            }
        });

        function checkAutoAssignStatus(teamId) 
        {
            $.ajax({
                url: "{{ route('mis.get.autoassign.status') }}",
                method: "GET",
                data: {
                    team_id: teamId
                },
                success: function(response) 
                {
                    if (response.success) 
                    {
                        $('#autoAssignSwitch').prop('checked', response.auto_assign);
                    }
                },
                error: function() 
                {
                    $('#autoAssignSwitch').prop('checked', false);
                }
            });
        }
        $('#setTargetModal').on('shown.bs.modal', function() 
        {
            var teamId = $('select[name="team_id"]').val();
            if (teamId) 
            {
                checkAutoAssignStatus(teamId);
            }
        });
    });

    document.querySelector('.info-icon').addEventListener('click', function() 
    {
        $('#misInfoModal').modal('show');
    });

    $('#quickEntryBtn').click(function() 
    {
        $('#quickEntryModal').modal('show');
        calculateTotal();
    });

    function calculateTotal() 
    {
        var total = 0;
        $('.point-value-input').each(function() 
        {
            var value = $(this).val();
            if (value && !isNaN(value)) 
            {
                total += parseInt(value);
            }
        });
        $('#totalValue').text(total);
    }

    $(document).on('input', '.point-value-input', function() 
    {
        calculateTotal();
    });

    $('#quickEntryForm').submit(function(e) 
    {
        e.preventDefault();
        
        var teamId = $('#quickEntryTeamId').val();
        var entryDate = $('#quickEntryDate').val();
        var year = '{{ $yearFilter }}';
        
        if (!entryDate) 
        {
            toastr.error('Please select a date');
            return;
        }
        var date = new Date(entryDate);
        var year = date.getFullYear();
        var target = new Date(date.valueOf());
        var dayNr = (date.getDay() + 6) % 7;
        target.setDate(target.getDate() - dayNr + 3);
        var firstThursday = target.valueOf();
        target.setMonth(0, 1);
        if (target.getDay() !== 4) 
        {
            target.setMonth(0, 1 + ((4 - target.getDay()) + 7) % 7);
        }
        var weekNumber = 1 + Math.ceil((firstThursday - target) / 604800000);
        weekNumber = Math.max(1, Math.min(53, weekNumber));
        
        var entries = [];
        var hasData = false;
        
        $('.point-value-input').each(function() 
        {
            var point = $(this).data('point');
            var value = $(this).val();
            
            if (value === '' || isNaN(value)) 
            {
                value = 0;
            } 
            else 
            {
                value = parseInt(value);
                if (value > 0) 
                {
                    hasData = true;
                }
            }
            
            entries.push({
                point: point,
                value: value
            });
        });
        
        if (!hasData) 
        {
            toastr.error('Please enter at least one value');
            return;
        }
        $('#saveQuickEntryBtn').prop('disabled', true);
        $('#saveQuickEntryText').addClass('d-none');
        $('#saveQuickEntrySpinner').removeClass('d-none');
        saveDailyEntries(teamId, year, weekNumber, entries, entryDate);
    });

    function saveDailyEntries(teamId, year, week, entries, entryDate) 
    {
        $.ajax({
            url: "{{ route('mis.save.daily.entries') }}",
            method: "POST",
            data: {
                _token: '{{ csrf_token() }}',
                team_id: teamId,
                year: year,
                week: week,
                entries: entries,
                entry_date: entryDate
            },
            success: function(response) 
            {
                if (response.success) 
                {
                    toastr.success('Daily entries saved successfully!');
                    $('#quickEntryModal').modal('hide');
                    setTimeout(function() 
                    {
                        location.reload();
                    }, 1500);
                } 
                else 
                {
                    toastr.error(response.message || 'Failed to save entries');
                    resetQuickEntryButton();
                }
            },
            error: function(xhr)
            {
                var errorMessage = 'Error saving entries';
                if (xhr.responseJSON && xhr.responseJSON.message) 
                {
                    errorMessage = xhr.responseJSON.message;
                }
                toastr.error(errorMessage);
                resetQuickEntryButton();
            }
        });
    }

    function resetQuickEntryButton() 
    {
        $('#saveQuickEntryBtn').prop('disabled', false);
        $('#saveQuickEntryText').removeClass('d-none');
        $('#saveQuickEntrySpinner').addClass('d-none');
    }

    $('#quickEntryModal').on('shown.bs.modal', function() 
    {
        var entryDate = $('#quickEntryDate').val();
        if (entryDate) 
        {
            loadExistingData(entryDate);
        }
    });

    $('#quickEntryDate').change(function() 
    {
        var entryDate = $(this).val();
        if (entryDate) 
        {
            loadExistingData(entryDate);
        }
    });

    function loadExistingData(entryDate) 
    {
        var teamId = $('#quickEntryTeamId').val();
        
        if (!teamId || !entryDate) return;
        
        $.ajax({
            url: "{{ route('mis.get.daily.data') }}",
            method: "GET",
            data: {
                team_id: teamId,
                entry_date: entryDate
            },
            beforeSend: function() 
            {
                $('.point-value-input').prop('disabled', true);
            },
            success: function(response) 
            {
                if (response.success) 
                {
                    $('.point-value-input').val('0');
                    if (response.data && response.data.length > 0) 
                    {
                        response.data.forEach(function(item) 
                        {
                            $('.point-value-input[data-point="' + item.point + '"]').val(item.value);
                        });
                        toastr.info('Loaded existing data for ' + entryDate);
                    }
                }
                calculateTotal();
            },
            error: function() 
            {
                toastr.warning('Could not load existing data for this date');
            },
            complete: function() 
            {
                $('.point-value-input').prop('disabled', false);
            }
        });
    }

    $('#quickEntryModal').on('hidden.bs.modal', function() 
    {
        $('#quickEntryForm')[0].reset();
        $('#quickEntryDate').val('{{ date("Y-m-d") }}');
        $('.point-value-input').val('0');
        calculateTotal();
        resetQuickEntryButton();
    });

    // Replace the week calculation with this:
function getWeekNumber(date) {
    // Send to server for accurate week calculation
    return new Promise((resolve, reject) => {
        $.ajax({
            url: "{{ route('mis.get.week.number') }}",
            method: "GET",
            data: {
                date: date
            },
            success: function(response) {
                if (response.success) {
                    resolve(response.week);
                } else {
                    reject(response.message);
                }
            },
            error: function() {
                reject('Error calculating week number');
            }
        });
    });
}

// Then update your form submit handler:
$('#quickEntryForm').submit(function(e) {
    e.preventDefault();
    
    var teamId = $('#quickEntryTeamId').val();
    var entryDate = $('#quickEntryDate').val();
    var year = '{{ $yearFilter }}';
    
    if (!entryDate) {
        toastr.error('Please select a date');
        return;
    }
    
    var entries = [];
    var hasData = false;
    
    $('.point-value-input').each(function() {
        var point = $(this).data('point');
        var value = $(this).val();
        
        if (value === '' || isNaN(value)) {
            value = 0;
        } else {
            value = parseInt(value);
            if (value > 0) {
                hasData = true;
            }
        }
        
        entries.push({
            point: point,
            value: value
        });
    });
    
    if (!hasData) {
        toastr.error('Please enter at least one value');
        return;
    }

    $('#saveQuickEntryBtn').prop('disabled', true);
    $('#saveQuickEntryText').addClass('d-none');
    $('#saveQuickEntrySpinner').removeClass('d-none');

    getWeekNumber(entryDate)
        .then(function(weekNumber) {
            saveDailyEntries(teamId, year, weekNumber, entries, entryDate);
        })
        .catch(function(error) {
            toastr.error('Error: ' + error);
            resetQuickEntryButton();
        });
});
</script>
@endsection