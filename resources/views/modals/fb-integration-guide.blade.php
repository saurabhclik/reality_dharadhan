
<div class="modal fade" id="integrationGuideModal" tabindex="-1" aria-labelledby="integrationGuideModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-slide-right">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white border-0">
                <h5 class="modal-title" id="integrationGuideModalLabel">
                    <i class="fab fa-facebook-f me-2"></i> Facebook Integration Guide
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="accordion accordion-flush" id="facebookIntegrationGuide">
                    <div class="accordion-item border-0 mb-3 shadow-sm">
                        <h2 class="accordion-header" id="step1">
                            <button class="accordion-button collapsed bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#collapseStep1" aria-expanded="false" aria-controls="collapseStep1">
                                <span class="badge bg-primary rounded-circle me-2">1</span> Create a Facebook App
                            </button>
                        </h2>
                        <div id="collapseStep1" class="accordion-collapse collapse" aria-labelledby="step1" data-bs-parent="#facebookIntegrationGuide">
                            <div class="accordion-body">
                                <p class="text-muted">To sync leads from Facebook, you need to create a developer app.</p>
                                <ol class="list-group list-group-numbered">
                                    <li class="list-group-item border-0">Visit <a href="https://developers.facebook.com" target="_blank" class="text-primary">developers.facebook.com</a> and log in.</li>
                                    <li class="list-group-item border-0">Click <strong>My Apps</strong> > <strong>Create App</strong>.</li>
                                    <li class="list-group-item border-0">Select <strong>Business</strong> as the app type and name it (e.g., "leadexpertz Lead Sync").</li>
                                    <li class="list-group-item border-0">Copy the <strong>App ID</strong> and <strong>App Secret</strong> from <strong>Settings > Basic</strong>.</li>
                                </ol>
                                <div class="alert alert-info mt-3">
                                    <i class="fas fa-info-circle me-2"></i> Keep your App Secret secure and do not share it publicly.
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item border-0 mb-3 shadow-sm">
                        <h2 class="accordion-header" id="step2">
                            <button class="accordion-button collapsed bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#collapseStep2" aria-expanded="false" aria-controls="collapseStep2">
                                <span class="badge bg-primary rounded-circle me-2">2</span> Generate a Long-Lived Access Token
                            </button>
                        </h2>
                        <div id="collapseStep2" class="accordion-collapse collapse" aria-labelledby="step2" data-bs-parent="#facebookIntegrationGuide">
                            <div class="accordion-body">
                                <p class="text-muted">A long-lived access token is required to fetch leads.</p>
                                <ol class="list-group list-group-numbered">
                                    <li class="list-group-item border-0">Go to <a href="https://developers.facebook.com/tools/explorer" target="_blank" class="text-primary">Graph API Explorer</a>.</li>
                                    <li class="list-group-item border-0">Select your app and click <strong>Generate Access Token</strong>.</li>
                                    <li class="list-group-item border-0">Grant permissions: <code>pages_show_list</code>, <code>leads_retrieval</code>, <code>pages_read_engagement</code>, <code>pages_manage_ads</code>.</li>
                                    <li class="list-group-item border-0">Copy the short-lived token.</li>
                                    <li class="list-group-item border-0">In the Integration section, open the Token Management modal, paste the short-lived token, and click 'Generate' to receive a long-lived token.</li>
                                </ol>
                                <button class="btn btn-outline-primary btn-sm mt-3" data-bs-toggle="modal" data-bs-target="#tokenManagementModal" data-bs-dismiss="modal">
                                    <i class="fas fa-key me-1"></i> Open Token Management
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item border-0 mb-3 shadow-sm">
                        <h2 class="accordion-header" id="step3">
                            <button class="accordion-button collapsed bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#collapseStep3" aria-expanded="false" aria-controls="collapseStep3">
                                <span class="badge bg-primary rounded-circle me-2">3</span> Configure Integration
                            </button>
                        </h2>
                        <div id="collapseStep3" class="accordion-collapse collapse" aria-labelledby="step3" data-bs-parent="#facebookIntegrationGuide">
                            <div class="accordion-body">
                                <p class="text-muted">Add your credentials to leadexpertz.</p>
                                <ol class="list-group list-group-numbered">
                                    <li class="list-group-item border-0">Go to <a href="{{ route('integration.settings') }}" class="text-primary">Integration Settings</a>.</li>
                                    <li class="list-group-item border-0">Enter your <strong>App ID</strong>, <strong>App Secret</strong>, and <strong>Long-Lived Access Token</strong>.</li>
                                    <li class="list-group-item border-0">Save the settings.</li>
                                </ol>
                                <a href="{{ route('integration.settings') }}" class="btn btn-outline-primary btn-sm mt-3">
                                    <i class="fas fa-cog me-1"></i> Go to the API Integration Settings located in the master
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item border-0 mb-3 shadow-sm">
                        <h2 class="accordion-header" id="step4">
                            <button class="accordion-button collapsed bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#collapseStep4" aria-expanded="false" aria-controls="collapseStep4">
                                <span class="badge bg-primary rounded-circle me-2">4</span> Sync Leads
                            </button>
                        </h2>
                        <div id="collapseStep4" class="accordion-collapse collapse" aria-labelledby="step4" data-bs-parent="#facebookIntegrationGuide">
                            <div class="accordion-body">
                                <p class="text-muted">Start syncing leads from your Facebook pages.</p>
                                <ol class="list-group list-group-numbered">
                                    <li class="list-group-item border-0">Ensure your Facebook pages have lead generation forms set up.</li>
                                    <li class="list-group-item border-0">Click <strong>Sync Now</strong> on the Facebook integration card.</li>
                                    <li class="list-group-item border-0">Wait up to 2 minutes for the sync to complete.</li>
                                    <li class="list-group-item border-0">Check the sync status for leads imported today and total leads.</li>
                                </ol>
                                <div class="alert alert-warning mt-3">
                                    <i class="fas fa-exclamation-circle me-2"></i> Ensure your pages and forms are correctly configured in Facebook to avoid sync issues.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>