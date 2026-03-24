<div class="modal fade modal-slide-right" id="managePagesModal" tabindex="-1" aria-labelledby="managePagesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title d-flex align-items-center">
                    <i class="fas fa-flag me-2"></i>Facebook Pages
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="p-4 border-bottom">
                    <div class="table-responsive" style="max-height: 400px;">
                        <table id="fbPagesTable" class="table table-hover mb-0">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th class="ps-4">#</th>
                                    <th>Page ID</th>
                                    <th>Page Name</th>
                                    <th>Category</th>
                                    <th class="text-center">Tasks</th>
                                </tr>
                            </thead>
                            <tbody id="fbPagesTableBody">
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <div class="spinner-border text-primary mb-3" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <p class="text-muted">Loading pages...</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>