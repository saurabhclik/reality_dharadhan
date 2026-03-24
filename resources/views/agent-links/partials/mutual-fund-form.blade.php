<div id="mutual_fund-fields" class="service-fields">
    <div class="form-section">
        <h5><i class="fa-solid fa-chart-line"></i> Investment Details</h5>
        <div class="mb-4">
            <label class="form-label">Fund Type</label>
            <div class="radio-group" id="fundTypeGroup">
                <div class="radio-option">
                    <input type="radio" name="fund_type" id="fund_equity" value="equity" checked>
                    <label for="fund_equity">
                        <i class="fa-solid fa-chart-line me-2"></i>Equity
                        <small>High risk, high return</small>
                    </label>
                </div>
                <div class="radio-option">
                    <input type="radio" name="fund_type" id="fund_debt" value="debt">
                    <label for="fund_debt">
                        <i class="fa-solid fa-chart-bar me-2"></i>Debt
                        <small>Low risk, stable</small>
                    </label>
                </div>
                <div class="radio-option">
                    <input type="radio" name="fund_type" id="fund_hybrid" value="hybrid">
                    <label for="fund_hybrid">
                        <i class="fa-solid fa-chart-pie me-2"></i>Hybrid
                        <small>Balanced</small>
                    </label>
                </div>
                <div class="radio-option">
                    <input type="radio" name="fund_type" id="fund_elss" value="elss">
                    <label for="fund_elss">
                        <i class="fa-solid fa-taxi me-2"></i>ELSS
                        <small>Tax saving</small>
                    </label>
                </div>
            </div>
        </div>

        <!-- Category Radio (Dynamic) -->
        <div class="mb-4">
            <label class="form-label">Category</label>
            <div class="radio-group" id="mfCategoryGroup">
                <div class="radio-option">
                    <input type="radio" name="category" id="mf_cat_large" value="large_cap" checked>
                    <label for="mf_cat_large">Large Cap</label>
                </div>
                <div class="radio-option">
                    <input type="radio" name="category" id="mf_cat_mid" value="mid_cap">
                    <label for="mf_cat_mid">Mid Cap</label>
                </div>
                <div class="radio-option">
                    <input type="radio" name="category" id="mf_cat_small" value="small_cap">
                    <label for="mf_cat_small">Small Cap</label>
                </div>
            </div>
        </div>

        <!-- Sub Category -->
        <div class="dependent-field" id="mfSubCategoryContainer">
            <label class="form-label">Option</label>
            <div class="radio-group" id="mfSubCategoryGroup">
                <div class="radio-option">
                    <input type="radio" name="sub_category" id="mf_sub_growth" value="growth" checked>
                    <label for="mf_sub_growth">Growth</label>
                </div>
                <div class="radio-option">
                    <input type="radio" name="sub_category" id="mf_sub_dividend" value="dividend">
                    <label for="mf_sub_dividend">Dividend</label>
                </div>
            </div>
        </div>

        <!-- Investment Fields -->
        <div class="row g-3 mt-3">
            <div class="col-md-6">
                <label class="form-label">Investment Amount</label>
                <div class="input-group">
                    <span class="input-group-text">₹</span>
                    <input class="form-control" name="investment_amount" placeholder="5,000">
                </div>
            </div>
            <div class="col-md-6">
                <label class="form-label">Investment Tenure</label>
                <input class="form-control" name="investment_tenure" placeholder="e.g. 5 years">
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