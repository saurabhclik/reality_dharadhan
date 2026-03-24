<div class="modal fade" id="documentsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-light">Manage Documents</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="uploadForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="post_sale_id" name="post_sale_id">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label>Document Name</label>
                            <input type="text" class="form-control" name="document_name" required>
                        </div>
                        <div class="col-md-6">
                            <label>File (Max 10MB)</label>
                            <input type="file" class="form-control" name="document" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary mt-3">
                        <i class="fas fa-upload"></i> Upload
                    </button>
                </form>
                <hr>
                <div class="table-responsive mt-3">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Size</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="documentsList">
                            <tr>
                                <td colspan="4" class="text-center">Click "Manage Documents" to load</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>