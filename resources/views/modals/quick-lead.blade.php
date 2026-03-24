<div class="modal fade" id="quickLeadModal" tabindex="-1" aria-labelledby="quickLeadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-sm">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="quickLeadModalLabel">Add New Lead</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="quickLeadForm" action="{{ route('lead.quick_add') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info mb-3">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-info-circle me-2"></i>
                            <div>
                                @if(in_array(Session::get('user_type'), ['super_admin', 'divisional_head']))
                                <span class="text-muted">This lead needs to go to the allocated leads section</span>
                                @else
                                <span class="text-muted">This lead will go to NEW LEADS</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="leadName" class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="leadName" name="name" placeholder="Enter lead name" required>
                    </div>
                    <div class="mb-3">
                        <label for="leadPhone" class="form-label">Phone <span class="text-danger">*</span></label>
                        <input type="tel" class="form-control" id="leadPhone" name="phone" placeholder="Enter phone number" required pattern="[0-9]{10}">
                        <div class="invalid-feedback">Please enter a valid 10-digit phone number</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" id="submitBtn" class="btn btn-primary">
                        <span id="btnText">Add Lead</span>
                        <span id="btnSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () 
    {
        $('#quickLeadForm').on('submit', function (e) 
        {
            const $submitBtn = $('#submitBtn');
            const $btnSpinner = $('#btnSpinner');
            const $btnText = $('#btnText');
            
            $submitBtn.prop('disabled', true);
            $btnSpinner.removeClass('d-none');
            $btnText.text('Saving...');
        });

        $('#quickLeadModal').on('hidden.bs.modal', function () 
        {
            $('#quickLeadForm')[0].reset();
            $('#submitBtn').prop('disabled', false);
            $('#btnSpinner').addClass('d-none');
            $('#btnText').text('Add Lead');
            $('#leadPhone').removeClass('is-invalid');
        });
        $('#leadPhone').on('input', function() 
        {
            if(this.validity.patternMismatch) 
            {
                $(this).addClass('is-invalid');
            } 
            else 
            {
                $(this).removeClass('is-invalid');
            }
        });
    });
</script>