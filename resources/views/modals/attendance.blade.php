
<div class="modal fade" id="attendanceModal" tabindex="-1" aria-labelledby="attendanceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen-md-down">
        <form method="POST" action="">
            @csrf
            <input type="hidden" name="_method">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="attendanceModalLabel">Add / Edit Attendance Type</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label for="type" class="form-label">Attendance Type</label>
                        <input type="text" class="form-control" name="type" required readonly>
                    </div>
                    <div class="mb-3">
                        <label for="hours" class="form-label">Hours</label>
                        <input type="number" class="form-control" name="hours" step="0.1" required>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">
                        Save
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </form>
    </div>
</div>
