<div class="modal fade" id="bulkImportModal" tabindex="-1" aria-labelledby="bulkImportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-light">
                <h5 class="modal-title" id="bulkImportModalLabel">
                    <i class="fas fa-file-import me-2"></i>Bulk Import Inventory
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('inventory.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="import_inventory_id" class="form-label">Project</label>
                        <select class="form-control select2" id="import_inventory_id" name="inventory_id" required>
                            <option value="">Select Project</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}" 
                                    {{ ($selectedProject->id ?? '') == $project->id ? 'selected' : '' }}>
                                    {{ $project->project_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="inventory_file" class="form-label">CSV File</label>
                        <input type="file" class="form-control" id="inventory_file" name="inventory_file" 
                               accept=".csv,.txt" required>
                        <div class="form-text">
                            <strong>File format:</strong> CSV file with columns: unit_no, size<br>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="SubmitBtnBulk">
                        <span id="SubmitTextBulk">Import</span>
                        <span id="SubmitSpinnerBulk" class="d-none">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Please wait...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>