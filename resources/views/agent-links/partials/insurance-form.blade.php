<div id="insurance-fields" class="service-fields">
    <div class="form-section">
        <h5>
            <i class="fa-solid fa-shield-halved"></i> Insurance Details
        </h5>
        <div class="mb-4">
            <label class="form-label">Insurance Type</label>
            <div class="radio-group" id="insuranceTypeGroup">
                <div class="radio-option">
                    <input type="radio" name="insurance_type" id="ins_life" value="life" checked>
                    <label for="ins_life">
                        <i class="fa-solid fa-heart me-2"></i>Life Insurance
                        <small>Life coverage</small>
                    </label>
                </div>
                <div class="radio-option">
                    <input type="radio" name="insurance_type" id="ins_health" value="health">
                    <label for="ins_health">
                        <i class="fa-solid fa-hospital me-2"></i>Health Insurance
                        <small>Medical coverage</small>
                    </label>
                </div>
                <div class="radio-option">
                    <input type="radio" name="insurance_type" id="ins_motor" value="motor">
                    <label for="ins_motor">
                        <i class="fa-solid fa-car me-2"></i>Motor Insurance
                        <small>Vehicle insurance</small>
                    </label>
                </div>
                <div class="radio-option">
                    <input type="radio" name="insurance_type" id="ins_travel" value="travel">
                    <label for="ins_travel">
                        <i class="fa-solid fa-plane me-2"></i>Travel Insurance
                        <small>Travel coverage</small>
                    </label>
                </div>
            </div>
        </div>
        <div class="mb-4">
            <label class="form-label">Coverage Type</label>
            <div class="radio-group" id="insuranceCategoryGroup">
                <div class="radio-option">
                    <input type="radio" name="category" id="ins_cat_individual" value="individual" checked>
                    <label for="ins_cat_individual">Individual</label>
                </div>
                <div class="radio-option">
                    <input type="radio" name="category" id="ins_cat_family" value="family">
                    <label for="ins_cat_family">Family Floater</label>
                </div>
                <div class="radio-option">
                    <input type="radio" name="category" id="ins_cat_group" value="group">
                    <label for="ins_cat_group">Group</label>
                </div>
            </div>
        </div>
        <div class="dependent-field" id="insuranceSubCategoryContainer">
            <label class="form-label">Plan Type</label>
            <div class="radio-group" id="insuranceSubCategoryGroup">
                <div class="radio-option">
                    <input type="radio" name="sub_category" id="ins_sub_term" value="term" checked>
                    <label for="ins_sub_term">Term Plan</label>
                </div>
                <div class="radio-option">
                    <input type="radio" name="sub_category" id="ins_sub_ulip" value="ulip">
                    <label for="ins_sub_ulip">ULIP</label>
                </div>
                <div class="radio-option">
                    <input type="radio" name="sub_category" id="ins_sub_endowment" value="endowment">
                    <label for="ins_sub_endowment">Endowment</label>
                </div>
            </div>
        </div>
        <div class="row g-3 mt-3">
            <div class="col-md-6">
                <label class="form-label">Sum Assured</label>
                <div class="input-group">
                    <span class="input-group-text">₹</span>
                    <input class="form-control" name="sum_assured" placeholder="10,00,000">
                </div>
            </div>
            <div class="col-md-6">
                <label class="form-label">Policy Term</label>
                <input class="form-control" name="policy_term" placeholder="e.g. 20 years">
            </div>
            <div class="col-md-6">
                <label class="form-label">City</label>
                <input class="form-control" name="city" placeholder="Enter city">
            </div>
            <div class="col-md-6">
                <label class="form-label">State</label>
                <input class="form-control" name="state" placeholder="Enter state">
            </div>
            <div class="col-12">
                <label class="form-label">Address</label>
                <textarea class="form-control" name="address" rows="2" placeholder="Full address"></textarea>
            </div>
        </div>
    </div>
</div>