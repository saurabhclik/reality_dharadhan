<div class="modal fade" id="shareLeadModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fas fa-share-alt me-2"></i>Share Lead
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('lead.share') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="lead_id" id="shareLeadId">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Share this lead with multiple team members. You can select multiple users from the dropdown.
                    </div>
                    
                    <div class="mb-3">
                        <label for="share_users" class="form-label">Select Users <span class="text-danger">*</span></label>
                        <select class="form-control select2-multiple" id="share_users" name="users[]" multiple="multiple" required>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} - {{ $user->email }} ({{ ucfirst($user->role) }})</option>
                            @endforeach
                        </select>
                        <div class="form-text">You can select multiple users by clicking and holding Ctrl/Cmd or using the search</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="share_message" class="form-label">Message (Optional)</label>
                        <textarea class="form-control" id="share_message" name="message" rows="3" 
                        placeholder="Add a message for the recipients..."></textarea>
                    </div>
                    <div class="selected-users-preview mt-3" style="display: none;">
                        <h6 class="text-muted mb-2">Selected Users:</h6>
                        <div id="selectedUsersList" class="d-flex flex-wrap gap-2"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="ShareSubmitBtn">
                        <span id="ShareSubmitText">Share with Selected Users</span>
                        <span id="ShareSubmitSpinner" class="d-none">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Please wait...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>