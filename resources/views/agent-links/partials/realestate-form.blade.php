<div class="form-section">
    <h5><i class="fa-solid fa-building"></i> Property Details</h5>
    <div class="mb-4">
        <label class="form-label">Property Type</label>
        <div class="radio-group" id="propertyTypeGroup">
            <div class="radio-option">
                <input type="radio" name="property_type" id="prop_apartment" value="Residential" checked>
                <label for="prop_apartment">
                    <i class="fa-solid fa-building me-2"></i>Residential
                    <small>Apartments, Villas</small>
                </label>
            </div>
            <div class="radio-option">
                <input type="radio" name="property_type" id="prop_commercial" value="Commercial">
                <label for="prop_commercial">
                    <i class="fa-solid fa-store me-2"></i>Commercial
                    <small>Shops, Offices</small>
                </label>
            </div>
        </div>
    </div>
    <div class="mb-3">
        <label class="form-label">Category</label>
        <select class="form-select" name="catg_id" id="categorySelect">
            <option value="">Select Category</option>
        </select>
    </div>
    <div class="mb-3" id="subCategoryDiv" style="display: none;">
        <label class="form-label">Sub Category</label>
        <select class="form-select" name="sub_catg_id" id="subCategorySelect">
            <option value="">Select Sub Category</option>
        </select>
    </div>
    <div class="mb-3">
        <label class="form-label">Projects <small class="text-muted">(You can select multiple)</small></label>
        <select class="form-select" name="name_of_location[]" id="locationName" multiple>
            <option value="">Loading projects...</option>
        </select>
    </div>
    <div class="row g-3 mb-3">
        <div class="col-md-6">
            <label class="form-label">State</label>
            <select class="form-select" name="property_state" id="stateSelect">
                <option value="">Select State</option>
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label">City/District</label>
            <select class="form-select" name="property_city" id="citySelect" disabled>
                <option value="">Select City</option>
            </select>
        </div>
        <div class="col-md-10">
            <label class="form-label">Location/Area</label>
            <select class="form-select" name="property_location[]" id="locationSelect" multiple>
                <option value="">Loading...</option>
            </select>
        </div>
    </div>
    <div class="mb-3">
        <label class="form-label">Additional Requirements</label>
        <textarea class="form-control" name="notes" rows="2" placeholder="Any specific requirements..."></textarea>
    </div>
</div>
