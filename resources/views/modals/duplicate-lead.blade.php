<div class="modal fade" id="duplicateLeadModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title">
                    <i class="fas fa-copy me-2"></i>Duplicate Lead
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('lead.duplicate') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="lead_id" id="duplicateLeadId">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        This will create a copy of the lead with a new status.
                    </div>
                    <div class="mb-3">
                        <label for="share_users" class="form-label">Allocate To<span class="text-danger">*</span></label>
                        <select class="form-control select2" id="allocate-user" name="user" required>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} - {{ $user->email }} ({{ ucfirst($user->role) }})</option>
                            @endforeach
                        </select>
                        <div class="form-text">You can select multiple users by clicking and holding Ctrl/Cmd or using the search</div>
                    </div>
                    <div class="mb-3">
                        <label for="new_status" class="form-label">New Status <span class="text-danger">*</span></label>
                        <select class="form-control select2" id="new_status" name="new_status" required>
                            <option value="">Select Status</option>
                            <option value="NEW LEAD">New</option>
                            <option value="PENDING">Pending</option>
                            <option value="PROCESSING">Processing</option>
                            <option value="INTERESTED">Interested</option>
                            <option value="CALL SCHEDULED">Call Scheduled</option>
                            <option value="WHATSAPP">Whatsapp</option>
                            <option value="MEETING SCHEDULED">Meeting Scheduled</option>
                            <option value="VISIT SCHEDULED">Visit Scheduled</option>
                            <option value="VISIT DONE">Visit Done</option>
                            <option value="WRONG NUMBER">Wrong Number</option>
                            <option value="NOT INTERESTED">Not Interested</option>
                            <option value="FUTURE LEAD">Future Lead</option>
                            <option value="NOT PICKED">Not Picked</option>
                            <option value="NOT REACHABLE">Not Reachable</option>
                            <option value="LOST">Lost</option>
                            <option value="CHANNEL PARTNER">Channel Partner</option>
                        </select>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="remindDate" class="form-label">Reminder Date</label>
                            <input type="date" class="form-control" id="duplicate-remind-date" min="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-md-6">
                            <label for="remindTime" class="form-label">Reminder Time</label>
                            <input type="time" class="form-control" id="duplicate-remind-time">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="duplicate_notes" class="form-label">Notes (Optional)</label>
                        <textarea class="form-control" id="duplicate_notes" name="notes" rows="3" 
                                  placeholder="Add any notes about why you're duplicating this lead..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="DuplicateSubmitBtn">
                        <span id="DuplicateSubmitText">Duplicate Lead</span>
                        <span id="DuplicateSubmitSpinner" class="d-none">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Please wait...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>