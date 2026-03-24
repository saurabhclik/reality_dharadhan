@extends('layouts.app')

@section('content')
<div class="page-content">
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Reports</h4>
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="#">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item active">Reports</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="row">
            @if($softwareType !== 'task_management')
            <div class="col-xl-3 col-md-4 col-sm-6 mb-4">
                <a href="{{ route('report.dayend_reports') }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm h-100 transition-all">
                        <div class="card-body text-center p-4 position-relative">
                            <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                <i class="fa-solid fa-calendar-days fs-2 text-light"></i>
                            </div>
                            <h5 class="card-title text-dark mb-2 fw-semibold">Dayend Report</h5>
                            <p class="card-text text-muted small mb-0">Daily performance summary and metrics</p>
                        </div>
                        <div class="card-footer bg-transparent border-0 py-3 text-center">
                            <small class="text-primary fw-medium">View Report <i class="bi bi-arrow-right-short"></i></small>
                        </div>
                    </div>
                </a>
            </div>

            @if(session('user_type') == 'super_admin' || session('user_type') == 'divisional_head')
            <div class="col-xl-3 col-md-4 col-sm-6 mb-4">
                <a href="{{ route('report.talecaller_reports') }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm h-100 transition-all">
                        <div class="card-body text-center p-4 position-relative">
                            <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                <i class="fa-solid fa-phone fs-2 text-success"></i>
                            </div>
                            <h5 class="card-title text-dark mb-2 fw-semibold">Telecaller Report</h5>
                            <p class="card-text text-muted small mb-0">Telecaller performance and call metrics</p>
                        </div>
                        <div class="card-footer bg-transparent border-0 py-3 text-center">
                            <small class="text-success fw-medium">View Report <i class="bi bi-arrow-right-short"></i></small>
                        </div>
                    </div>
                </a>
            </div>
            @endif

            @if(session('user_type') == 'super_admin' || session('user_type') == 'divisional_head')
            <div class="col-xl-3 col-md-4 col-sm-6 mb-4">
                <a href="{{ route('report.salesman_reports') }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm h-100 transition-all">
                        <div class="card-body text-center p-4 position-relative">
                            <div class="bg-info bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                <i class="fa-solid fa-person fs-2 text-info"></i>
                            </div>
                            <h5 class="card-title text-dark mb-2 fw-semibold">Salesman Report</h5>
                            <p class="card-text text-muted small mb-0">Sales team performance and conversion metrics</p>
                        </div>
                        <div class="card-footer bg-transparent border-0 py-3 text-center">
                            <small class="text-info fw-medium">View Report <i class="bi bi-arrow-right-short"></i></small>
                        </div>
                    </div>
                </a>
            </div>
            @endif

            <div class="col-xl-3 col-md-4 col-sm-6 mb-4">
                <a href="{{ route('report.campaign_reports') }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm h-100 transition-all">
                        <div class="card-body text-center p-4 position-relative">
                            <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                <i class="fa fa-bullhorn fs-2 text-warning"></i>
                            </div>
                            <h5 class="card-title text-dark mb-2 fw-semibold">Campaign Report</h5>
                            <p class="card-text text-muted small mb-0">Campaign performance and ROI analysis</p>
                        </div>
                        <div class="card-footer bg-transparent border-0 py-3 text-center">
                            <small class="text-warning fw-medium">View Report <i class="bi bi-arrow-right-short"></i></small>
                        </div>
                    </div>
                </a>
            </div>

            @if(session('user_type') == 'super_admin' || session('user_type') == 'divisional_head')
            <div class="col-xl-3 col-md-4 col-sm-6 mb-4">
                <a href="{{ route('report.source_reports') }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm h-100 transition-all">
                        <div class="card-body text-center p-4 position-relative">
                            <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                <i class="fa-solid fa-chart-pie fs-2 text-danger"></i>
                            </div>
                            <h5 class="card-title text-dark mb-2 fw-semibold">Source Report</h5>
                            <p class="card-text text-muted small mb-0">Lead source performance and attribution</p>
                        </div>
                        <div class="card-footer bg-transparent border-0 py-3 text-center">
                            <small class="text-danger fw-medium">View Report <i class="bi bi-arrow-right-short"></i></small>
                        </div>
                    </div>
                </a>
            </div>
            @endif

            <div class="col-xl-3 col-md-4 col-sm-6 mb-4">
                <a href="{{ route('report.classification_reports') }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm h-100 transition-all">
                        <div class="card-body text-center p-4 position-relative">
                            <div class="bg-secondary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                <i class="fa-solid fa-tag fs-2 text-secondary"></i>
                            </div>
                            <h5 class="card-title text-dark mb-2 fw-semibold">Classification Report</h5>
                            <p class="card-text text-muted small mb-0">Lead classification and segmentation</p>
                        </div>
                        <div class="card-footer bg-transparent border-0 py-3 text-center">
                            <small class="text-secondary fw-medium">View Report <i class="bi bi-arrow-right-short"></i></small>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-xl-3 col-md-4 col-sm-6 mb-4">
                <a href="{{ route('report.project_reports') }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm h-100 transition-all">
                        <div class="card-body text-center p-4 position-relative">
                            <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                <i class="fa-solid fa-building fs-2 text-light"></i>
                            </div>
                            <h5 class="card-title text-dark mb-2 fw-semibold">Project Report</h5>
                            <p class="card-text text-muted small mb-0">Project-wise performance and metrics</p>
                        </div>
                        <div class="card-footer bg-transparent border-0 py-3 text-center">
                            <small class="text-primary fw-medium">View Report <i class="bi bi-arrow-right-short"></i></small>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-xl-3 col-md-4 col-sm-6 mb-4">
                <a href="{{ route('report.category_reports') }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm h-100 transition-all">
                        <div class="card-body text-center p-4 position-relative">
                            <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                <i class="fa-solid fa-diagram-predecessor fs-2 text-success"></i>
                            </div>
                            <h5 class="card-title text-dark mb-2 fw-semibold">Category Report</h5>
                            <p class="card-text text-muted small mb-0">Category-wise lead distribution</p>
                        </div>
                        <div class="card-footer bg-transparent border-0 py-3 text-center">
                            <small class="text-success fw-medium">View Report <i class="bi bi-arrow-right-short"></i></small>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-xl-3 col-md-4 col-sm-6 mb-4">
                <a href="{{ route('report.sub_category_reports') }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm h-100 transition-all">
                        <div class="card-body text-center p-4 position-relative">
                            <div class="bg-info bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                <i class="fa-solid fa-diagram-successor fs-2 text-info"></i>
                            </div>
                            <h5 class="card-title text-dark mb-2 fw-semibold">Sub Category Report</h5>
                            <p class="card-text text-muted small mb-0">Sub-category performance analysis</p>
                        </div>
                        <div class="card-footer bg-transparent border-0 py-3 text-center">
                            <small class="text-info fw-medium">View Report <i class="bi bi-arrow-right-short"></i></small>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-xl-3 col-md-4 col-sm-6 mb-4">
                <a href="{{ route('report.city_reports') }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm h-100 transition-all">
                        <div class="card-body text-center p-4 position-relative">
                            <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                <i class="fa-solid fa-book-atlas fs-2 text-warning"></i>
                            </div>
                            <h5 class="card-title text-dark mb-2 fw-semibold">City Report</h5>
                            <p class="card-text text-muted small mb-0">City-wise lead distribution and performance</p>
                        </div>
                        <div class="card-footer bg-transparent border-0 py-3 text-center">
                            <small class="text-warning fw-medium">View Report <i class="bi bi-arrow-right-short"></i></small>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-xl-3 col-md-4 col-sm-6 mb-4">
                <a href="{{ route('report.state_reports') }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm h-100 transition-all">
                        <div class="card-body text-center p-4 position-relative">
                            <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                <i class="fa-solid fa-globe fs-2 text-danger"></i>
                            </div>
                            <h5 class="card-title text-dark mb-2 fw-semibold">State Report</h5>
                            <p class="card-text text-muted small mb-0">State-wise performance metrics</p>
                        </div>
                        <div class="card-footer bg-transparent border-0 py-3 text-center">
                            <small class="text-danger fw-medium">View Report <i class="bi bi-arrow-right-short"></i></small>
                        </div>
                    </div>
                </a>
            </div>

            @if(session('user_type') == 'super_admin' || session('user_type') == 'divisional_head')
            <div class="col-xl-3 col-md-4 col-sm-6 mb-4">
                <a href="{{ route('report.address_reports') }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm h-100 transition-all">
                        <div class="card-body text-center p-4 position-relative">
                            <div class="bg-secondary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                <i class="fa-solid fa-house-user fs-2 text-secondary"></i>
                            </div>
                            <h5 class="card-title text-dark mb-2 fw-semibold">Address Report</h5>
                            <p class="card-text text-muted small mb-0">Address-wise lead distribution</p>
                        </div>
                        <div class="card-footer bg-transparent border-0 py-3 text-center">
                            <small class="text-secondary fw-medium">View Report <i class="bi bi-arrow-right-short"></i></small>
                        </div>
                    </div>
                </a>
            </div>
            @endif

            @if(session('user_type') == 'super_admin' || session('user_type') == 'divisional_head')
            <div class="col-xl-3 col-md-4 col-sm-6 mb-4">
                <a href="{{ route('report.interested_reports') }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm h-100 transition-all">
                        <div class="card-body text-center p-4 position-relative">
                            <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                <i class="fa-solid fa-thumbs-up fs-2 text-light"></i>
                            </div>
                            <h5 class="card-title text-dark mb-2 fw-semibold">Interested Leads Report</h5>
                            <p class="card-text text-muted small mb-0">Analysis of interested leads and follow-ups</p>
                        </div>
                        <div class="card-footer bg-transparent border-0 py-3 text-center">
                            <small class="text-primary fw-medium">View Report <i class="bi bi-arrow-right-short"></i></small>
                        </div>
                    </div>
                </a>
            </div>
            @endif

            @if(session('user_type') == 'super_admin' || session('user_type') == 'divisional_head')
            <div class="col-xl-3 col-md-4 col-sm-6 mb-4">
                <a href="{{ route('report.visit_reports') }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm h-100 transition-all">
                        <div class="card-body text-center p-4 position-relative">
                            <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                <i class="fa-solid fa-user-check fs-2 text-success"></i>
                            </div>
                            <h5 class="card-title text-dark mb-2 fw-semibold">Visit Leads Report</h5>
                            <p class="card-text text-muted small mb-0">Site visit analysis and conversion metrics</p>
                        </div>
                        <div class="card-footer bg-transparent border-0 py-3 text-center">
                            <small class="text-success fw-medium">View Report <i class="bi bi-arrow-right-short"></i></small>
                        </div>
                    </div>
                </a>
            </div>
            @endif

            @if(session('user_type') == 'super_admin' || session('user_type') == 'divisional_head')
            <div class="col-xl-3 col-md-4 col-sm-6 mb-4">
                <a href="{{ route('report.call_reports') }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm h-100 transition-all">
                        <div class="card-body text-center p-4 position-relative">
                            <div class="bg-info bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                <i class="fa-solid fa-phone fs-2 text-info"></i>
                            </div>
                            <h5 class="card-title text-dark mb-2 fw-semibold">Calling Report Summary</h5>
                            <p class="card-text text-muted small mb-0">Call performance summary and metrics</p>
                        </div>
                        <div class="card-footer bg-transparent border-0 py-3 text-center">
                            <small class="text-info fw-medium">View Report <i class="bi bi-arrow-right-short"></i></small>
                        </div>
                    </div>
                </a>
            </div>
            @endif

            @if(session('user_type') == 'super_admin' || session('user_type') == 'divisional_head')
            <div class="col-xl-3 col-md-4 col-sm-6 mb-4">
                <a href="{{ route('report.call_details') }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm h-100 transition-all">
                        <div class="card-body text-center p-4 position-relative">
                            <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                <i class="fa-solid fa-clipboard-list fs-2 text-warning"></i>
                            </div>
                            <h5 class="card-title text-dark mb-2 fw-semibold">Calling Report Detail</h5>
                            <p class="card-text text-muted small mb-0">Detailed call logs and outcomes</p>
                        </div>
                        <div class="card-footer bg-transparent border-0 py-3 text-center">
                            <small class="text-warning fw-medium">View Report <i class="bi bi-arrow-right-short"></i></small>
                        </div>
                    </div>
                </a>
            </div>
            @endif

            <div class="col-xl-3 col-md-4 col-sm-6 mb-4">
                <a href="{{ route('report.smart_lead') }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm h-100 transition-all">
                        <div class="card-body text-center p-4 position-relative">
                            <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                <i class="fa-solid fa-chart-simple fs-2 text-danger"></i>
                            </div>
                            <h5 class="card-title text-dark mb-2 fw-semibold">Smart Lead Segmentation</h5>
                            <p class="card-text text-muted small mb-0">Lead scoring and segmentation</p>
                        </div>
                        <div class="card-footer bg-transparent border-0 py-3 text-center">
                            <small class="text-danger fw-medium">View Report <i class="bi bi-arrow-right-short"></i></small>
                        </div>
                    </div>
                </a>
            </div>
            @endif

            @if(session('user_type') == 'super_admin' || session('user_type') == 'divisional_head')
            <div class="col-xl-3 col-md-4 col-sm-6 mb-4">
                <a href="{{ route('task-report-summary') }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm h-100 transition-all">
                        <div class="card-body text-center p-4 position-relative">
                            <div class="bg-info bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                <i class="fa-solid fa-list-check fs-2 text-info"></i>
                            </div>
                            <h5 class="card-title text-dark mb-2 fw-semibold">Task Summary</h5>
                            <p class="card-text text-muted small mb-0">View all tasks with status, priority, assignees, and attachments</p>
                        </div>
                        <div class="card-footer bg-transparent border-0 py-3 text-center">
                            <small class="text-info fw-medium">View Report <i class="bi bi-arrow-right-short"></i></small>
                        </div>
                    </div>
                </a>
            </div>
            @endif

            @if(session('user_type') == 'super_admin' || session('user_type') == 'divisional_head')
            <div class="col-xl-3 col-md-4 col-sm-6 mb-4">
                <a href="{{ route('task-overdue-summary') }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm h-100 transition-all">
                        <div class="card-body text-center p-4 position-relative">
                            <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                <i class="fa-solid fa-exclamation-triangle fs-2 text-danger"></i>
                            </div>
                            <h5 class="card-title text-dark mb-2 fw-semibold">Overdue Tasks</h5>
                            <p class="card-text text-muted small mb-0">Shows all tasks past their due date by user/project summary option</p>
                        </div>
                        <div class="card-footer bg-transparent border-0 py-3 text-center">
                            <small class="text-danger fw-medium">View Report <i class="bi bi-arrow-right-short"></i></small>
                        </div>
                    </div>
                </a>
            </div>
            @endif

            @if(session('user_type') == 'super_admin' || session('user_type') == 'divisional_head')
                <div class="col-xl-3 col-md-4 col-sm-6 mb-4">
                    <a href="{{ route('upcoming-tasks') }}" class="text-decoration-none">
                        <div class="card border-0 shadow-sm h-100 transition-all">
                            <div class="card-body text-center p-4 position-relative">
                                <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                    <i class="fa-solid fa-calendar-days fs-2 text-warning"></i>
                                </div>
                                <h5 class="card-title text-dark mb-2 fw-semibold">Upcoming Tasks</h5>
                                <p class="card-text text-muted small mb-0">Tasks due in the next 3–7 days by user/project</p>
                            </div>
                            <div class="card-footer bg-transparent border-0 py-3 text-center">
                                <small class="text-warning fw-medium">View Report <i class="bi bi-arrow-right-short"></i></small>
                            </div>
                        </div>
                    </a>
                </div>
            @endif

            @if(session('user_type') == 'super_admin' || session('user_type') == 'divisional_head')
            <div class="col-xl-3 col-md-4 col-sm-6 mb-4">
                <a href="{{ route('task-completion') }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm h-100 transition-all">
                        <div class="card-body text-center p-4 position-relative">
                            <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                <i class="fa-solid fa-check-double fs-2 text-success"></i>
                            </div>
                            <h5 class="card-title text-dark mb-2 fw-semibold">Task Completion</h5>
                            <p class="card-text text-muted small mb-0">View completed tasks by user, team, or project</p>
                        </div>
                        <div class="card-footer bg-transparent border-0 py-3 text-center">
                            <small class="text-success fw-medium">View Report <i class="bi bi-arrow-right-short"></i></small>
                        </div>
                    </div>
                </a>
            </div>
            @endif

            @if(session('user_type') == 'super_admin' || session('user_type') == 'divisional_head')
            <div class="col-xl-3 col-md-4 col-sm-6 mb-4">
                <a href="{{ route('project-wise-task') }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm h-100 transition-all">
                        <div class="card-body text-center p-4 position-relative">
                            <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                <i class="fa-solid fa-diagram-project fs-2 text-light"></i>
                            </div>
                            <h5 class="card-title text-dark mb-2 fw-semibold">Project-Wise Tasks</h5>
                            <p class="card-text text-muted small mb-0">View tasks per project with completed, pending</p>
                        </div>
                        <div class="card-footer bg-transparent border-0 py-3 text-center">
                            <small class="text-primary fw-medium">View Report <i class="bi bi-arrow-right-short"></i></small>
                        </div>
                    </div>
                </a>
            </div>
            @endif

            @if(session('user_type') == 'super_admin' || session('user_type') == 'divisional_head')
                <div class="col-xl-3 col-md-4 col-sm-6 mb-4">
                    <a href="{{ route('report.communication_reports') }}" class="text-decoration-none">
                        <div class="card border-0 shadow-sm h-100 transition-all">
                            <div class="card-body text-center p-4 position-relative">
                                <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                    <i class="fa-solid fa-file fs-2 text-warning"></i>
                                </div>
                                <h5 class="card-title text-dark mb-2 fw-semibold">Communication Report</h5>
                                <p class="card-text text-muted small mb-0">Communication Report For All Users</p>
                            </div>
                            <div class="card-footer bg-transparent border-0 py-3 text-center">
                                <small class="text-warning fw-medium">View Report <i class="bi bi-arrow-right-short"></i></small>
                            </div>
                        </div>
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    .transition-all 
    {
        transition: all 0.3s ease;
    }
    .transition-all:hover 
    {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }
</style>
@endsection