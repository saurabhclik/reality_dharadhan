<div id="solar-fields" class="service-fields">
    <div class="form-section">
        <h5><i class="fa-solid fa-sun"></i> Solar Installation Details</h5>
        <div class="mb-4">
            <label class="form-label">Installation Type</label>
            <div class="radio-group" id="solarTypeGroup">
                <div class="radio-option">
                    <input type="radio" name="installation_type" id="solar_res" value="residential" checked>
                    <label for="solar_res">
                        <i class="fa-solid fa-home me-2"></i>Residential
                        <small>Home installation</small>
                    </label>
                </div>
                <div class="radio-option">
                    <input type="radio" name="installation_type" id="solar_com" value="commercial">
                    <label for="solar_com">
                        <i class="fa-solid fa-building me-2"></i>Commercial
                        <small>Business setup</small>
                    </label>
                </div>
                <div class="radio-option">
                    <input type="radio" name="installation_type" id="solar_ind" value="industrial">
                    <label for="solar_ind">
                        <i class="fa-solid fa-industry me-2"></i>Industrial
                        <small>Factory/Plant</small>
                    </label>
                </div>
            </div>
        </div>
        <div class="mb-4">
            <label class="form-label">System Type</label>
            <div class="radio-group" id="solarCategoryGroup">
                <div class="radio-option">
                    <input type="radio" name="category" id="solar_on" value="on_grid" checked>
                    <label for="solar_on">On Grid</label>
                </div>
                <div class="radio-option">
                    <input type="radio" name="category" id="solar_off" value="off_grid">
                    <label for="solar_off">Off Grid</label>
                </div>
                <div class="radio-option">
                    <input type="radio" name="category" id="solar_hybrid" value="hybrid">
                    <label for="solar_hybrid">Hybrid</label>
                </div>
            </div>
        </div>

        <!-- Sub Category -->
        <div class="dependent-field" id="solarSubCategoryContainer">
            <label class="form-label">Installation Type</label>
            <div class="radio-group" id="solarSubCategoryGroup">
                <div class="radio-option">
                    <input type="radio" name="sub_category" id="solar_sub_roof" value="rooftop" checked>
                    <label for="solar_sub_roof">Rooftop</label>
                </div>
                <div class="radio-option">
                    <input type="radio" name="sub_category" id="solar_sub_ground" value="ground">
                    <label for="solar_sub_ground">Ground Mounted</label>
                </div>
            </div>
        </div>

        <!-- Solar Fields -->
        <div class="row g-3 mt-3">
            <div class="col-md-6">
                <label class="form-label">Capacity (kW)</label>
                <input class="form-control" name="capacity" placeholder="e.g. 5 kW">
            </div>
            <div class="col-md-6">
                <label class="form-label">Budget</label>
                <div class="input-group">
                    <span class="input-group-text">₹</span>
                    <input class="form-control" name="budget" placeholder="3,00,000">
                </div>
            </div>
            <div class="col-md-6">
                <label class="form-label">Location</label>
                <input class="form-control" name="location" placeholder="e.g. Andheri, Mumbai">
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