<div class="modal fade" id="statusCommentModal" tabindex="-1" aria-labelledby="statusCommentModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="statusCommentForm">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Update Task Status</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p>Are you sure you want to change the status to <strong><span id="selectedStatusText"></span></strong>?</p>
          <div class="mb-3">
            <label for="statusComment" class="form-label">Comment (Optional)</label>
            <textarea class="form-control statusComment" id="statusComment" rows="3" name="comments" placeholder="Add a comment..."></textarea>
          </div>
          <div class="mb-3 d-none completed-file">
            <label for="file" class="file">Upload File</label>
            <input type="file" class="form-control" name="file[]" id="file" multiple accept=".csv, .txt, .pdf, image/*">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Confirm</button>
        </div>
      </div>
    </form>
  </div>
</div>