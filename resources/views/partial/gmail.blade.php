<div class="col-md-6 col-lg-4">
    <div class="card h-100 shadow-sm">
        <div class="card-header d-flex align-items-center bg-white border-bottom">
            <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                <i class="fas fa-envelope"></i>
            </div>
            <div>
                <h5 class="mb-0">Gmail</h5>
                @if(isset($gmailIntegration->status) && $gmailIntegration->status === 'active')
                    <span class="badge bg-success mt-1">
                        <i class="fas fa-check-circle me-1" style="font-size:0.5rem;"></i> Connected
                    </span>
                @else
                    <span class="badge bg-danger mt-1">
                        <i class="fas fa-times-circle me-1" style="font-size:0.5rem;"></i> Not Connected
                    </span>
                @endif
            </div>
            <div class="col text-end">
                <button class="btn btn-link text-primary p-0 text-end" data-bs-toggle="modal" data-bs-target="#gmailIntegrationGuideModal">
                    <i class="fas fa-question-circle"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <p class="text-muted">Connect your Gmail account to send emails.</p>
            <div class="d-flex justify-content-between">
                <a href="{{ route('integration.settings') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-cog me-1"></i> Configure
                </a>
            </div>
            <div id="gmailIntegrationResult" class="mt-3"></div>
        </div>
    </div>
</div>