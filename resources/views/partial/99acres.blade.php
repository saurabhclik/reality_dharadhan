<div class="col-md-6 col-lg-4">
    <div class="card h-100 shadow-sm">
        <div class="card-header d-flex align-items-center bg-white border-bottom">
            <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                <i class="fas fa-building"></i>
            </div>
            <div>
                <h5 class="mb-0">99acres</h5>
                @if(isset($acresIntegration->status) && $acresIntegration->status === 'active')
                    <span class="badge bg-success mt-1">
                        <i class="fas fa-check-circle me-1" style="font-size:0.5rem;"></i> Active
                    </span>
                @else
                    <span class="badge bg-danger mt-1">
                        <i class="fas fa-times-circle me-1" style="font-size:0.5rem;"></i> Inactive
                    </span>
                @endif
            </div>
            <div class="form-check form-switch ms-auto">
                <input class="form-check-input auto-sync-toggle" 
                    type="checkbox" 
                    id="autoSyncAcres"
                    data-integration-type="acres"
                    {{ isset($acresIntegration->auto_sync) && $acresIntegration->auto_sync ? 'checked' : '' }}>
                <label class="form-check-label small text-muted fs-6" for="autoSyncAcres">
                    Auto Sync
                </label>
            </div>
            <!-- <button class="btn btn-link text-info ms-2 p-0" data-bs-toggle="modal" data-bs-target="#acresIntegrationGuideModal">
                <i class="fas fa-question-circle"></i>
            </button> -->
        </div>
        <div class="card-body">
            <p class="text-muted">Connect your 99acres account to sync leads.</p>
            <div class="d-flex justify-content-around mb-3 text-center">
                <div>
                    <h6 class="mb-0">{{ $acresLeadsToday ?? 0 }}</h6>
                    <small class="text-muted">Leads Today</small>
                </div>
                <div>
                    <h6 class="mb-0">{{ $acresTotalLeads ?? 0 }}</h6>
                    <small class="text-muted">Total Leads</small>
                </div>
            </div>
            <div class="d-flex justify-content-between">
                <button class="btn btn-outline-primary btn-sm" id="syncNowAcresBtn">
                    <i class="fas fa-sync me-1"></i> Sync Now
                </button>
                <a href="{{ route('integration.settings') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-cog me-1"></i> Configure
                </a>
            </div>
            <div id="acresSyncResult" class="mt-3"></div>
        </div>
    </div>
</div>