<div class="modal fade" id="createProjectModal" tabindex="-1" aria-labelledby="createProjectModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form method="POST" action="{{ route('task.project.store') }}">
            @csrf
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header bg-primary text-white rounded-top-4">
                    <h5 class="modal-title fw-semibold" id="createProjectModalLabel">
                        <i class="fas fa-folder-plus me-2"></i> Create New Project
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="project_id" id="edit_project_id">
                    <div class="mb-3">
                        <label for="edit_project_name" class="form-label">Project Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit-project-name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_project_description" class="form-label">Description</label>
                        <textarea class="form-control" id="edit-project_-escription" name="description" rows="3"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_project_status" class="form-label">Status</label>
                                <select class="form-select" id="edit-project-status" name="status">
                                    <option value="planning">Planning</option>
                                    <option value="active">Active</option>
                                    <option value="on_hold">On Hold</option>
                                    <option value="completed">Completed</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_project_priority" class="form-label">Priority</label>
                                <select class="form-select" id="edit-project-priority" name="priority">
                                    <option value="low">Low</option>
                                    <option value="medium">Medium</option>
                                    <option value="high">High</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="SubmitTaskProjectBtn">
                        <span id="SubmitTaskProjectText">Submit</span>
                        <span id="SubmitTaskProjectSpinner" class="d-none">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Please wait...
                        </span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>