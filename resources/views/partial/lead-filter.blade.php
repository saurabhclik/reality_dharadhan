    <div class="col-12 collapse {{ $hasFilters ? 'show' : '' }}" id="filterCollapse">
        <div class="card border-0 shadow-sm mb-4">
            <div class="collapse show" id="leadFilterCollapse">
                <div class="card-body">
                    <form id="leadFilterForm" method="GET" action="{{ request()->url() }}">
                        <input type="hidden" name="length" value="{{ $length }}">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label for="filterSource" class="form-label fw-semibold">Source</label>
                                <select class="form-select select2" id="filterSource" name="source">
                                    <option value="">All Source</option>
                                    @foreach($sources as $source)
                                        <option value="{{ $source->name }}" 
                                            {{ request('source') == $source->name ? 'selected' : '' }}>
                                            {{ $source->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label for="filterUser" class="form-label fw-semibold">User</label>
                                <select class="form-select select2" id="filterUser" name="user">
                                    <option value="">All Users</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" 
                                            {{ request('user') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label for="filterClassification" class="form-label fw-semibold">Classification</label>
                                <select class="form-select select2" id="filterClassification" name="classification">
                                    <option value="">All Classifications</option>
                                    @foreach($classifications as $classification)
                                        @if(!empty($classification))
                                            <option value="{{ $classification }}" {{ request('classification') == $classification ? 'selected' : '' }}>
                                                {{ $classification }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label for="filterProject" class="form-label fw-semibold">Project</label>
                                <select class="form-select select2" id="filterProject" name="project">
                                    <option value="">All Projects</option>
                                    @foreach($projects as $project)
                                        <option value="{{ $project->id }}" {{ request('project') == $project->id ? 'selected' : '' }}>
                                            {{ $project->project_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label for="filterDateFrom" class="form-label fw-semibold">Date From</label>
                                <input type="date" class="form-control" id="filterDateFrom" name="date_from" value="{{ request('date_from') }}">
                            </div>

                            <div class="col-md-3">
                                <label for="filterDateTo" class="form-label fw-semibold">Date To</label>
                                <input type="date" class="form-control" id="filterDateTo" name="date_to" value="{{ request('date_to') }}">
                            </div>

                            <div class="col-md-3">
                                <label for="filterLeadDays" class="form-label fw-semibold">
                                    Lead Older Than (Days)
                                </label>
                                <input 
                                    type="number" 
                                    min="1"
                                    class="form-control"
                                    id="filterLeadDays"
                                    name="lead_days"
                                    placeholder="e.g. 5 or 6"
                                    value="{{ request('lead_days') }}"
                                >
                            </div>
                            <div class="col-md-3">
                                <label for="filterShared" class="form-label fw-semibold">Share Filter</label>
                                <select class="form-select" id="filterShared" name="shared_filter">
                                    <option value="">Choose sharing option
                                    </option>
                                    <option value="shared_by_me" {{ request('shared_filter') == 'shared_by_me' ? 'selected' : '' }}>
                                        Shared By Me
                                    </option>
                                    <option value="shared_with_me" {{ request('shared_filter') == 'shared_with_me' ? 'selected' : '' }}>
                                        Shared With Me
                                    </option>
                                    <option value="not_shared" {{ request('shared_filter') == 'not_shared' ? 'selected' : '' }}>
                                        Not Shared
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-primary px-4 me-2">
                                <i class="fas fa-search me-1"></i> Apply
                            </button>
                            <a href="{{ request()->url() }}" class="btn btn-outline-secondary px-4">
                                <i class="fas fa-times me-1"></i> Clear
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>