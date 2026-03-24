<div class="modal fade" id="shareLinksModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Share Rating Link</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning p-2 small" role="alert">
                    ⚠️ This rating link will expire in <strong>7 days</strong>.
                </div>
                <div class="mb-3">
                    <label class="form-label">Share Link</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="shareLink" readonly>
                        <button class="btn btn-outline-secondary" type="button" id="copyLink">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                </div>
                
                <div class="share-buttons">
                    <p class="mb-2">Share via:</p>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="#" class="btn btn-success btn-sm" id="shareWhatsApp" target="_blank">
                            <i class="fab fa-whatsapp"></i> WhatsApp
                        </a>
                        <a href="#" class="btn btn-primary btn-sm" id="shareEmail" target="_blank">
                            <i class="fas fa-envelope"></i> Email
                        </a>
                        <a href="#" class="btn btn-info btn-sm" id="shareTelegram" target="_blank">
                            <i class="fab fa-telegram"></i> Telegram
                        </a>
                        <a href="#" class="btn btn-primary btn-sm" id="shareFacebook" target="_blank">
                            <i class="fab fa-facebook"></i> Facebook
                        </a>
                        <a href="#" class="btn btn-primary btn-sm" id="shareLinkedIn" target="_blank">
                            <i class="fab fa-linkedin"></i> LinkedIn
                        </a>
                        <a href="#" class="btn btn-info btn-sm" id="shareTwitter" target="_blank">
                            <i class="fab fa-twitter"></i> Twitter
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>