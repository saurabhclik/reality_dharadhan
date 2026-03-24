<div class="modal fade" id="fileModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0">
                <h5 class="modal-title font-size-16 fw-bold" id="fileModalTitle">File Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <div class="file-preview-content">
                    <img id="modalFileImage" src="" class="img-fluid" alt="File Preview" style="max-height: 65vh; display: none;">
                    <div id="nonImageContent" class="d-none">
                        <i class="fas fa-file-alt fa-5x text-primary mb-3"></i>
                        <p>This file type cannot be previewed. Please download to view.</p>
                        <a id="fileDownloadLink" href="#" class="btn btn-primary btn-rounded waves-effect waves-light" download>
                            <i class="fas fa-download me-2"></i> Download File
                        </a>
                    </div>
                </div>
                <div class="loading-spinner d-none">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Loading preview...</p>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary btn-rounded" data-bs-dismiss="modal">Close</button>
                <a id="modalDownloadBtn" href="#" class="btn btn-primary btn-rounded" download>
                    <i class="fas fa-download me-2"></i> Download
                </a>
            </div>
        </div>
    </div>
</div>