<div class="modal fade" id="gmailIntegrationGuideModal" tabindex="-1" aria-labelledby="gmailIntegrationGuideModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-slide-right">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white border-0">
                <h5 class="modal-title" id="gmailIntegrationGuideModalLabel">
                    <i class="fas fa-envelope me-2"></i> Gmail Integration Guide
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="accordion accordion-flush" id="gmailIntegrationGuide">
                    <div class="accordion-item border-0 mb-3 shadow-sm">
                        <h2 class="accordion-header" id="gstep1">
                            <button class="accordion-button collapsed bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#gcollapseStep1" aria-expanded="false" aria-controls="gcollapseStep1">
                                <span class="badge bg-danger rounded-circle me-2">1</span> Enable 2-Factor Authentication
                            </button>
                        </h2>
                        <div id="gcollapseStep1" class="accordion-collapse collapse" aria-labelledby="gstep1" data-bs-parent="#gmailIntegrationGuide">
                            <div class="accordion-body">
                                <p class="text-muted">You must enable 2-Step Verification on your Google account before using SMTP.</p>
                                <ol class="list-group list-group-numbered">
                                    <li class="list-group-item border-0">Go to <a href="https://myaccount.google.com/security" target="_blank" class="text-danger">Google Account Security</a>.</li>
                                    <li class="list-group-item border-0">Under <em>“Signing in to Google”</em>, enable <strong>2-Step Verification</strong>.</li>
                                    <li class="list-group-item border-0">Verify using your phone or Google Authenticator app.</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item border-0 mb-3 shadow-sm">
                        <h2 class="accordion-header" id="gstep2">
                            <button class="accordion-button collapsed bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#gcollapseStep2" aria-expanded="false" aria-controls="gcollapseStep2">
                                <span class="badge bg-danger rounded-circle me-2">2</span> Generate an App Password
                            </button>
                        </h2>
                        <div id="gcollapseStep2" class="accordion-collapse collapse" aria-labelledby="gstep2" data-bs-parent="#gmailIntegrationGuide">
                            <div class="accordion-body">
                                <p class="text-muted">App Passwords are required to connect Gmail securely.</p>
                                <ol class="list-group list-group-numbered">
                                    <li class="list-group-item border-0">Go to <a href="https://myaccount.google.com/apppasswords" target="_blank" class="text-danger">App Passwords</a>.</li>
                                    <li class="list-group-item border-0">Select <strong>Mail</strong> as the app and <strong>Other (Custom name)</strong> (e.g., <em>Leadmanagement</em>).</li>
                                    <li class="list-group-item border-0">Copy the generated 16-digit password. This will be your SMTP password.</li>
                                </ol>
                                <div class="alert alert-warning mt-3">
                                    <i class="fas fa-exclamation-circle me-2"></i> Keep your App Password safe. Google only shows it once.
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item border-0 mb-3 shadow-sm">
                        <h2 class="accordion-header" id="gstep3">
                            <button class="accordion-button collapsed bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#gcollapseStep3" aria-expanded="false" aria-controls="gcollapseStep3">
                                <span class="badge bg-danger rounded-circle me-2">3</span> Update Integration Settings
                            </button>
                        </h2>
                        <div id="gcollapseStep3" class="accordion-collapse collapse" aria-labelledby="gstep3" data-bs-parent="#gmailIntegrationGuide">
                            <div class="accordion-body">
                                <p class="text-muted">Add the Gmail SMTP credentials in the system.</p>
                                <ol class="list-group list-group-numbered">
                                    <li class="list-group-item border-0">Go to <em>Integration → Gmail → Configure</em>.</li>
                                    <li class="list-group-item border-0">Enter:</li>
                                    <li class="list-group-item border-0">SMTP Host: <code>smtp.gmail.com</code></li>
                                    <li class="list-group-item border-0">SMTP Port: <code>587</code></li>
                                    <li class="list-group-item border-0">Encryption: <code>TLS</code></li>
                                    <li class="list-group-item border-0">Username: Your Gmail address</li>
                                    <li class="list-group-item border-0">Password: <strong>The 16-digit App Password</strong> from Step 2</li>
                                    <li class="list-group-item border-0">From Address: Your Gmail address</li>
                                    <li class="list-group-item border-0">From Name: Your company name (e.g., <em>XYZ Pvt. Ltd.</em>)</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item border-0 mb-3 shadow-sm">
                        <h2 class="accordion-header" id="gstep4">
                            <button class="accordion-button collapsed bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#gcollapseStep4" aria-expanded="false" aria-controls="gcollapseStep4">
                                <span class="badge bg-danger rounded-circle me-2">4</span>Connection
                            </button>
                        </h2>
                        <div id="gcollapseStep4" class="accordion-collapse collapse" aria-labelledby="gstep4" data-bs-parent="#gmailIntegrationGuide">
                            <div class="accordion-body">
                                <ol class="list-group list-group-numbered">
                                    <li class="list-group-item border-0">Save the settings.</li>
                                    <li class="list-group-item border-0">Gmail status will show <span class="badge bg-success">Connected</span>.</li>
                                </ol>
                                <div class="alert alert-info mt-3">
                                    <i class="fas fa-info-circle me-2"></i> Gmail does not allow SMTP login without 2FA and an App Password.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
