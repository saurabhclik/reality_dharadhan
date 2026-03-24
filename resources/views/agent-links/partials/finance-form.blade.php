
<div class="form-section">
    <h5><i class="fa-solid fa-coins"></i> Loan Details</h5>
    <div class="mb-4">
        <label class="form-label">Loan Type</label>
        <div class="radio-group" id="loanTypeGroup">
            <div class="radio-option">
                <input type="radio" name="loan_type" id="loan_home" value="home" checked>
                <label for="loan_home">
                    <i class="fa-solid fa-home me-2"></i>Home Loan
                    <small>Purchase/Construction</small>
                </label>
            </div>
            <div class="radio-option">
                <input type="radio" name="loan_type" id="loan_personal" value="personal">
                <label for="loan_personal">
                    <i class="fa-solid fa-user me-2"></i>Personal Loan
                    <small>Immediate needs</small>
                </label>
            </div>
            <div class="radio-option">
                <input type="radio" name="loan_type" id="loan_business" value="business">
                <label for="loan_business">
                    <i class="fa-solid fa-briefcase me-2"></i>Business Loan
                    <small>Business expansion</small>
                </label>
            </div>
            <div class="radio-option">
                <input type="radio" name="loan_type" id="loan_car" value="car">
                <label for="loan_car">
                    <i class="fa-solid fa-car me-2"></i>Car Loan
                    <small>Vehicle purchase</small>
                </label>
            </div>
            <div class="radio-option">
                <input type="radio" name="loan_type" id="loan_education" value="education">
                <label for="loan_education">
                    <i class="fa-solid fa-graduation-cap me-2"></i>Education Loan
                    <small>Studies funding</small>
                </label>
            </div>
        </div>
    </div>

    <!-- Category Radio -->
    <div class="mb-4">
        <label class="form-label">Category</label>
        <div class="radio-group" id="loanCategoryGroup">
            <div class="radio-option">
                <input type="radio" name="category" id="cat_secured" value="secured" checked>
                <label for="cat_secured">
                    <i class="fa-solid fa-lock me-2"></i>Secured
                    <small>Against collateral</small>
                </label>
            </div>
            <div class="radio-option">
                <input type="radio" name="category" id="cat_unsecured" value="unsecured">
                <label for="cat_unsecured">
                    <i class="fa-solid fa-unlock me-2"></i>Unsecured
                    <small>No collateral</small>
                </label>
            </div>
        </div>
    </div>

    <!-- Sub Category (Dynamic) -->
    <div class="dependent-field" id="loanSubCategoryContainer">
        <label class="form-label">Employment Type</label>
        <div class="radio-group" id="loanSubCategoryGroup">
            <div class="radio-option">
                <input type="radio" name="sub_category" id="emp_salaried" value="salaried" checked>
                <label for="emp_salaried">Salaried</label>
            </div>
            <div class="radio-option">
                <input type="radio" name="sub_category" id="emp_self" value="self-employed">
                <label for="emp_self">Self-Employed</label>
            </div>
        </div>
    </div>

    <!-- Loan Amount Fields -->
    <div class="row g-3 mt-3">
        <div class="col-md-6">
            <label class="form-label">Loan Amount</label>
            <div class="input-group">
                <span class="input-group-text">₹</span>
                <input class="form-control" name="loan_amount" placeholder="10,00,000">
            </div>
        </div>
        <div class="col-md-6">
            <label class="form-label">Loan Tenure (Years)</label>
            <input class="form-control" name="loan_tenure" placeholder="e.g. 5">
        </div>
        <div class="col-md-6">
            <label class="form-label">Date of Birth</label>
            <input type="date" class="form-control" name="dob">
        </div>
        <div class="col-md-6">
            <label class="form-label">Date of Anniversary</label>
            <input type="date" class="form-control" name="doa">
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
