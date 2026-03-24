<div class="modal fade modal-slide-right" id="acresIntegrationGuideModal" tabindex="-1" aria-labelledby="acresIntegrationGuideLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-slideout modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white d-flex align-items-center">
                <h5 class="modal-title" id="acresIntegrationGuideLabel">
                    <i class="fas fa-building me-2"></i> 99acres Integration Guide
                </h5>
                <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted mb-3">Follow these steps to connect your 99acres account and sync leads</p>

                <div class="accordion" id="acresIntegrationSteps">
                    <div class="accordion-item mb-2">
                        <h2 class="accordion-header" id="step1Header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#step1" aria-expanded="false" aria-controls="step1">
                                1. Generate API Key
                            </button>
                        </h2>
                        <div id="step1" class="accordion-collapse collapse" aria-labelledby="step1Header" data-bs-parent="#acresIntegrationSteps">
                            <div class="accordion-body text-muted">
                                Log in to your 99acres account and navigate to the <strong>API/Developer section</strong>. Generate a unique API key to allow Pro-leadexpertz to fetch leads.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item mb-2">
                        <h2 class="accordion-header" id="step2Header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#step2" aria-expanded="false" aria-controls="step2">
                                2. Enter API Key
                            </button>
                        </h2>
                        <div id="step2" class="accordion-collapse collapse" aria-labelledby="step2Header" data-bs-parent="#acresIntegrationSteps">
                            <div class="accordion-body text-muted">
                                Copy the generated API key and paste it in the <strong>Integration Settings</strong> page for 99acres within Pro-leadexpertz.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item mb-2">
                        <h2 class="accordion-header" id="step3Header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#step3" aria-expanded="false" aria-controls="step3">
                                3. Enable Auto Sync
                            </button>
                        </h2>
                        <div id="step3" class="accordion-collapse collapse" aria-labelledby="step3Header" data-bs-parent="#acresIntegrationSteps">
                            <div class="accordion-body text-muted">
                                Toggle the <em>Auto Sync</em> switch in the 99acres card to automatically fetch new leads periodically.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item mb-2">
                        <h2 class="accordion-header" id="step4Header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#step4" aria-expanded="false" aria-controls="step4">
                                4. Manual Sync
                            </button>
                        </h2>
                        <div id="step4" class="accordion-collapse collapse" aria-labelledby="step4Header" data-bs-parent="#acresIntegrationSteps">
                            <div class="accordion-body text-muted">
                                Click the <em>Sync Now</em> button on the 99acres card to fetch leads immediately.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item mb-2">
                        <h2 class="accordion-header" id="step5Header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#step5" aria-expanded="false" aria-controls="step5">
                                5. Verify Leads
                            </button>
                        </h2>
                        <div id="step5" class="accordion-collapse collapse" aria-labelledby="step5Header" data-bs-parent="#acresIntegrationSteps">
                            <div class="accordion-body text-muted">
                                After syncing, check the 99acres card for <strong>Leads Today</strong> and <strong>Total Leads</strong> to confirm the sync.
                            </div>
                        </div>
                    </div>
                </div>

                <div class="alert alert-warning mt-3">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Make sure your API key has the necessary permissions. Sync may fail without proper access.
                </div>

                <div class="mt-4 text-center">
                    <a href="{{ route('integration.settings') }}" class="btn btn-outline-info">
                        <i class="fas fa-cog me-1"></i> Open Integration Settings
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
