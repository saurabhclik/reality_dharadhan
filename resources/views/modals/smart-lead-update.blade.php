<div class="modal fade" id="updateLeadModal" tabindex="-1" role="dialog" aria-labelledby="updateLeadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-gray">
                <h5 class="modal-title" id="updateLeadModalLabel">Update Lead</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body bg-light-gray">
                <form id="updateLeadForm" method="post">
                    @csrf
                    <div class="row">
                    </div>
                    <input type="hidden" name="id" id="leadId">
                </form>
            </div>
            <div class="modal-footer bg-gray">
                <button type="submit" form="updateLeadForm" class="btn btn-primary">Save Changes</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>