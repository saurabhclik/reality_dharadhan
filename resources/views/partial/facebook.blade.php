<div class="col-md-6 col-lg-4">
    <div class="card h-100 shadow-sm">
        <div class="card-header d-flex align-items-center bg-white border-bottom">
            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                <i class="fab fa-facebook-f"></i>
            </div>
            <div>
                <h5 class="mb-0">Facebook</h5>
                @if(isset($facebookIntegration->status) && $facebookIntegration->status === 'active')
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
                    id="autoSyncFacebook"
                    data-integration-type="facebook"
                    {{ isset($facebookIntegration->auto_sync) && $facebookIntegration->auto_sync ? 'checked' : '' }}>
                <label class="form-check-label small text-muted fs-6" for="autoSyncFacebook">
                    Auto Sync
                </label>
            </div>
            <button class="btn btn-link text-primary ms-2 p-0" data-bs-toggle="modal" data-bs-target="#integrationGuideModal">
                <i class="fas fa-question-circle"></i>
            </button>
        </div>
        <div class="card-body">
            <p class="text-muted">Connect your Facebook account to sync leads.</p>
            <div class="d-flex justify-content-around mb-3 text-center">
                <div>
                    <h6 class="mb-0">{{ $facebookLeadsToday ?? 0 }}</h6>
                    <small class="text-muted">Leads Today</small>
                </div>
                <div>
                    <h6 class="mb-0">{{ $facebookTotalLeads ?? 0 }}</h6>
                    <small class="text-muted">Total Leads</small>
                </div>
            </div>
            <div class="d-flex gap-2 mb-3 flex-wrap">
                <button class="btn btn-light border btn-sm" data-bs-toggle="modal" data-bs-target="#managePagesModal">
                    <i class="fas fa-flag me-1 text-primary"></i> Pages
                </button>
                <button class="btn btn-light border btn-sm" data-bs-toggle="modal" data-bs-target="#manageAccountsModal">
                    <i class="fas fa-user-circle me-1 text-primary"></i> Accounts
                </button>
                <button class="btn btn-light border btn-sm" data-bs-toggle="modal" data-bs-target="#tokenManagementModal">
                    <i class="fas fa-key me-1 text-primary"></i> Token
                </button>
            </div>
            <div class="d-flex justify-content-between">
                <button class="btn btn-outline-primary btn-sm" id="syncNowFbBtn">
                    <i class="fas fa-sync me-1"></i> Sync Now
                </button>
                <a href="{{ route('integration.settings') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-cog me-1"></i> Configure
                </a>
            </div>
            <div id="facebookSyncResult" class="mt-3"></div>
        </div>
    </div>
</div>