<div class="col-xl-8">
    <div class="row" id="lead-stats-container">
        @foreach($leadCards as $index => $card)
            @if(!isset($card['others_lead']) && $card['title'] !== 'Other Leads')
                <div class="col-md-4 lead-card">
                    <div class="card mini-stats-wid">
                        @if($card['route'])
                            <a href="{{ route($card['route']) }}">
                        @endif
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="flex-grow-1">
                                    <p class="text-muted fw-medium">{{ $card['title'] }}</p>
                                    <h4 class="mb-0">{{ $card['value'] }}</h4>
                                </div>
                                <div class="flex-shrink-0 align-self-center">
                                    <div class="avatar-sm rounded-circle bg-primary mini-stat-icon">
                                        <span class="avatar-title rounded-circle bg-primary">
                                            <i class="bx {{ $card['icon'] }} font-size-24"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @if($card['route'])
                            </a>
                        @endif
                    </div>
                </div>
            @endif
        @endforeach
    </div>
</div>
<div class="col-xl-12">
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom-0 py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Lead Analytics</h4>
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-light text-dark dropdown-toggle" type="button" 
                        data-bs-toggle="dropdown">
                        <i class="bx bx-dots-vertical-rounded"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="#" id="export-analytics-png">
                            <i class="bx bx-image me-2"></i>Export as PNG</a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#" id="export-analytics-csv">
                            <i class="bx bx-file me-2"></i>Export as CSV</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="card-body">
            <ul class="nav nav-tabs nav-tabs-custom" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#monthly-report" role="tab">
                        Monthly Recap Report
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#source-analysis" role="tab">
                        Source Analysis
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#project-analysis" role="tab">
                        Project Analysis
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#campaign-analysis" role="tab">
                        Campaign Analysis
                    </a>
                </li>
            </ul>
            <div class="tab-content p-3 border border-top-0">
                <div class="tab-pane active" id="monthly-report" role="tabpanel">
                    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
                        <div class="ms-md-auto d-flex align-items-center">
                            <div class="me-3">
                                <select id="year-filter" class="form-select form-select-sm">
                                    @foreach($availableYears as $year)
                                        <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>
                                            {{ $year }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div id="monthly-analytics-chart" style="height: 350px;"></div>
                </div>
                <div class="tab-pane" id="source-analysis" role="tabpanel">
                    <div id="source-analytics-chart" style="height: 350px;"></div>
                </div>
                <div class="tab-pane" id="project-analysis" role="tabpanel">
                    <div id="project-analytics-chart" style="height: 350px;"></div>
                </div>
                <div class="tab-pane" id="campaign-analysis" role="tabpanel">
                    <div id="campaign-analytics-chart" style="height: 350px;"></div>
                </div>
            </div>
        </div>
    </div>
</div>