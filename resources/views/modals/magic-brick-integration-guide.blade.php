<div class="modal fade" id="magicbricksIntegrationGuideModal" tabindex="-1" aria-labelledby="magicbricksIntegrationGuideModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-slide-right">
        <div class="modal-content">
            <div class="modal-header bg-success text-white border-0">
                <h5 class="modal-title" id="magicbricksIntegrationGuideModalLabel">
                    <i class="fas fa-building me-2"></i> MagicBricks Integration Guide
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="accordion accordion-flush" id="magicbricksIntegrationGuide">
                    <div class="accordion-item border-0 mb-3 shadow-sm">
                        <h2 class="accordion-header" id="mbstep1">
                            <button class="accordion-button collapsed bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#mbcollapseStep1" aria-expanded="false" aria-controls="mbcollapseStep1">
                                <span class="badge bg-success rounded-circle me-2">1</span> Get API Access from MagicBricks
                            </button>
                        </h2>
                        <div id="mbcollapseStep1" class="accordion-collapse collapse" aria-labelledby="mbstep1" data-bs-parent="#magicbricksIntegrationGuide">
                            <div class="accordion-body">
                                <p class="text-muted">You need API credentials from MagicBricks to fetch leads.</p>
                                <ol class="list-group list-group-numbered">
                                    <li class="list-group-item border-0">Login to your <a href="https://accounts.magicbricks.com/" target="_blank" class="text-success">MagicBricks Partner Account</a>.</li>
                                    <li class="list-group-item border-0">Contact your MagicBricks account manager or support team.</li>
                                    <li class="list-group-item border-0">Request API key, secret, and endpoint access for lead integration.</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item border-0 mb-3 shadow-sm">
                        <h2 class="accordion-header" id="mbstep2">
                            <button class="accordion-button collapsed bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#mbcollapseStep2" aria-expanded="false" aria-controls="mbcollapseStep2">
                                <span class="badge bg-success rounded-circle me-2">2</span> Configure API Credentials
                            </button>
                        </h2>
                        <div id="mbcollapseStep2" class="accordion-collapse collapse" aria-labelledby="mbstep2" data-bs-parent="#magicbricksIntegrationGuide">
                            <div class="accordion-body">
                                <p class="text-muted">Once you get credentials, configure them in the system.</p>
                                <ol class="list-group list-group-numbered">
                                    <li class="list-group-item border-0">Go to <em>Integration → MagicBricks → Configure</em>.</li>
                                    <li class="list-group-item border-0">Enter:</li>
                                    <li class="list-group-item border-0">API Key: <strong>Your MagicBricks API Key</strong></li>
                                    <li class="list-group-item border-0">API Secret: <strong>Your Secret Key</strong></li>
                                    <li class="list-group-item border-0">Endpoint URL: <code>https://partnerapi.magicbricks.com/leads</code></li>
                                </ol>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item border-0 mb-3 shadow-sm">
                        <h2 class="accordion-header" id="mbstep3">
                            <button class="accordion-button collapsed bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#mbcollapseStep3" aria-expanded="false" aria-controls="mbcollapseStep3">
                                <span class="badge bg-success rounded-circle me-2">3</span> Enable Auto-Sync (Optional)
                            </button>
                        </h2>
                        <div id="mbcollapseStep3" class="accordion-collapse collapse" aria-labelledby="mbstep3" data-bs-parent="#magicbricksIntegrationGuide">
                            <div class="accordion-body">
                                <p class="text-muted">Auto-sync will fetch leads automatically.</p>
                                <ol class="list-group list-group-numbered">
                                    <li class="list-group-item border-0">Toggle <strong>Auto Sync</strong> in MagicBricks integration card.</li>
                                    <li class="list-group-item border-0">System will fetch new leads every minutes.</li>
                                </ol>
                                <div class="alert alert-info mt-3">
                                    <i class="fas fa-info-circle me-2"></i> Manual sync is always available using the <strong>Sync Now</strong> button.
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item border-0 mb-3 shadow-sm">
                        <h2 class="accordion-header" id="mbstep4">
                            <button class="accordion-button collapsed bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#mbcollapseStep4" aria-expanded="false" aria-controls="mbcollapseStep4">
                                <span class="badge bg-success rounded-circle me-2">4</span> Test Connection
                            </button>
                        </h2>
                        <div id="mbcollapseStep4" class="accordion-collapse collapse" aria-labelledby="mbstep4" data-bs-parent="#magicbricksIntegrationGuide">
                            <div class="accordion-body">
                                <ol class="list-group list-group-numbered">
                                    <li class="list-group-item border-0">Click <strong>Sync Now</strong> in the MagicBricks card.</li>
                                    <li class="list-group-item border-0">If credentials are valid, leads will be imported into your system.</li>
                                </ol>
                                <div class="alert alert-warning mt-3">
                                    <i class="fas fa-exclamation-triangle me-2"></i> If sync fails, verify API credentials with your MagicBricks account manager.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
