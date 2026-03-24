
<div class="modal fade" id="shareAppModal" tabindex="-1" aria-labelledby="shareAppModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-light" id="shareAppModalLabel">Share</h5>
                <button type="button" class="btn-close text-light" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Select Properties</label>
                    <div class="properties-list" style="max-height: 200px; overflow-y: auto;">
                    </div>
                </div>
                <div class="mb-3">
                    <label for="messageText" class="form-label">Message</label>
                    <textarea class="form-control" id="messageText" rows="3">Check out these premium properties:</textarea>
                </div>
                <div class="mb-3">
                    <label for="whatsappNumber" class="form-label">WhatsApp Number</label>
                    <input type="text" class="form-control" id="whatsappNumber" placeholder="Enter WhatsApp number">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" id="sendWhatsAppBtn">
                    <i class="fa-brands fa-whatsapp"></i> Send via WhatsApp
                </button>
            </div>
        </div>
    </div>
</div>