<div class="modal fade" id="statusCommentModal" tabindex="-1" aria-labelledby="statusCommentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="statusCommentModalLabel">
                    Update Status to: <span id="selectedStatusText" class="badge bg-primary"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="statusCommentForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="statusComment" class="form-label">Comments (Optional)</label>
                        <textarea class="form-control" id="statusComment" rows="3" placeholder="Add any comments about this status change..."></textarea>
                    </div>
                    <div class="mb-3 completed-file d-none">
                        <label for="file" class="form-label">Upload Files (Optional)</label>
                        <input type="file" class="form-control" id="file" name="file[]" multiple>
                        <small class="text-muted">You can upload multiple files when marking as completed</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>