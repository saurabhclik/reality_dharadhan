<div class="col-md-6 col-lg-4">
    <div class="card h-100 shadow-sm">
        <div class="card-header d-flex align-items-center bg-white border-bottom">
            <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                <i class="fas fa-file-alt"></i>
            </div>
            <div>
                <h5 class="mb-0">Google Form</h5>
                @if(isset($googleFormIntegration->status) && $googleFormIntegration->status === 'active')
                    <span class="badge bg-success mt-1">
                        <i class="fas fa-check-circle me-1" style="font-size:0.5rem;"></i> Connected
                    </span>
                @else
                    <span class="badge bg-danger mt-1">
                        <i class="fas fa-times-circle me-1" style="font-size:0.5rem;"></i> Not Connected
                    </span>
                @endif
            </div>
            <div class="form-check form-switch ms-auto">
                <input class="form-check-input auto-sync-toggle" 
                    type="checkbox" 
                    id="autoSyncGoogleForm"
                    data-integration-type="googleform"
                    {{ isset($googleFormIntegration->auto_sync) && $googleFormIntegration->auto_sync ? 'checked' : '' }}>
                <label class="form-check-label small text-muted fs-6" for="autoSyncGoogleForm">
                    Auto Sync
                </label>
            </div>
        </div>
        <div class="card-body">
            <p class="text-muted">Sync leads automatically from your Google Form responses.</p>
            <div class="d-flex justify-content-around mb-3 text-center">
                <div>
                    <h6 class="mb-0">{{ $googleFormLeadsToday ?? 0 }}</h6>
                    <small class="text-muted">Leads Today</small>
                </div>
                <div>
                    <h6 class="mb-0">{{ $googleFormTotalLeads ?? 0 }}</h6>
                    <small class="text-muted">Total Leads</small>
                </div>
            </div>
            <div class="d-flex justify-content-between">
                <button class="btn btn-outline-primary btn-sm" id="syncNowGoogleFormBtn">
                    <i class="fas fa-sync me-1"></i> Sync Now
                </button>
                <a href="{{ route('integration.settings') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-cog me-1"></i> Configure
                </a>
            </div>
            <div id="googleFormSyncResult" class="mt-3"></div>
        </div>
    </div>
</div>