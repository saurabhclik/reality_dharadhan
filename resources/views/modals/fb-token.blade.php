<div class="modal fade" id="tokenManagementModal" tabindex="-1" aria-labelledby="tokenManagementModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title d-flex align-items-center">
                    <i class="fas fa-key me-2"></i>Access Token Management
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Important:</strong> Keep your access tokens secure and never share them publicly.
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Short-lived Token</h6>
                            </div>
                            <div class="card-body">
                                <p class="text-muted small">Generate a long-lived token from your short-lived Facebook token</p>
                                <div class="mb-3">
                                    <label class="form-label">Short-lived Token</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="shortToken" placeholder="Enter short-lived token">
                                        <button class="btn btn-outline-secondary" type="button" id="toggleToken">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                <button class="btn btn-primary w-100" id="generateTokenBtn">
                                    <i class="fas fa-exchange-alt me-1"></i> Generate Long-lived Token
                                </button>
                                <p class="text-muted small mt-2">
                                    <i class="fas fa-sync-alt me-1"></i>
                                    If auto-sync is enabled, the token will be automatically regenerated when it expires.
                                </p>
                                <div class="mt-3" id="responseBox"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Long-lived Token</h6>
                            </div>
                            <div class="card-body">
                                <p class="text-muted small">Your current long-lived access token</p>
                                <div class="mb-3">
                                    <label class="form-label">Access Token</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="access-token" readonly>
                                        <button class="btn btn-outline-secondary" type="button">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted"></small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>