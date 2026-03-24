<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-gradient-primary text-white">
                <h5 class="modal-title" id="exampleModalLabel">
                    <i class="fas fa-user-plus mr-2"></i> Add New Customer
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="addCustomerForm" method="POST" action="">
                @csrf
                <div class="modal-body px-4 py-4">
                    <div class="stepper mb-4">
                        <div class="stepper-progress">
                            <div class="stepper-progress-bar" style="width: 33.33%;"></div>
                        </div>
                        <div class="stepper-step active">
                            <div class="stepper-circle">1</div>
                            <div class="stepper-title">Basic Info</div>
                        </div>
                        <div class="stepper-step">
                            <div class="stepper-circle">2</div>
                            <div class="stepper-title">Project Details</div>
                        </div>
                        <div class="stepper-step">
                            <div class="stepper-circle">3</div>
                            <div class="stepper-title">Additional Info</div>
                        </div>
                    </div>

                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="basicInfo" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="lead_id" class="font-weight-bold">Select Lead</label>
                                        <select class="form-control select2" id="lead_id" name="lead_id" required>
                                            <option value="">Select Lead</option>
                                            <option value="1">John Doe (john@example.com)</option>
                                            <option value="2">Jane Smith (jane@example.com)</option>
                                            <option value="3">Acme Corporation (contact@acme.com)</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="sales_person_id" class="font-weight-bold">Sales Person</label>
                                        <select class="form-control select2" id="sales_person_id" name="sales_person_id" required>
                                            <option value="">Select Sales Person</option>
                                            <option value="1">Michael Johnson</option>
                                            <option value="2">Sarah Williams</option>
                                            <option value="3">David Brown</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="applicant_name" class="font-weight-bold">Applicant Name</label>
                                        <input type="text" class="form-control" id="applicant_name" name="applicant_name" placeholder="Enter full name" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="applicant_number" class="font-weight-bold">Applicant Number</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">+91</span>
                                            </div>
                                            <input type="text" class="form-control" id="applicant_number" name="applicant_number" placeholder="Enter phone number" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="email" class="font-weight-bold">Email ID</label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="Enter email address">
                            </div>
                            
                            <div class="d-flex justify-content-between mt-4">
                                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
                                    <i class="fas fa-times mr-2"></i>Cancel
                                </button>
                                <button type="button" class="btn btn-primary next-step">
                                    Next <i class="fas fa-arrow-right ml-2"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="tab-pane fade" id="projectDetails" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="project_name" class="font-weight-bold">Project Name</label>
                                        <input type="text" class="form-control" id="project_name" name="project_name" placeholder="Enter project name" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="unit_number" class="font-weight-bold">Unit Number</label>
                                        <input type="text" class="form-control" id="unit_number" name="unit_number" placeholder="Enter unit number" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="project_category" class="font-weight-bold">Project Category</label>
                                        <select class="form-control select2" id="project_category" name="project_category" required>
                                            <option value="">Select Category</option>
                                            <option value="1">Residential</option>
                                            <option value="2">Commercial</option>
                                            <option value="3">Industrial</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="project_sub_category" class="font-weight-bold">Project Sub Category</label>
                                        <select class="form-control select2" id="project_sub_category" name="project_sub_category" required>
                                            <option value="">Select Sub Category</option>
                                            <option value="1">Apartment</option>
                                            <option value="2">Villa</option>
                                            <option value="3">Plot</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="dob" class="font-weight-bold">Date of Birth</label>
                                        <div class="input-group date" id="dobDatePicker">
                                            <input type="text" class="form-control" id="dob" name="dob" placeholder="Select date">
                                            <div class="input-group-append">
                                                <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="doa" class="font-weight-bold">Date of Agreement</label>
                                        <div class="input-group date" id="doaDatePicker">
                                            <input type="text" class="form-control" id="doa" name="doa" placeholder="Select date">
                                            <div class="input-group-append">
                                                <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between mt-4">
                                <button type="button" class="btn btn-outline-secondary prev-step">
                                    <i class="fas fa-arrow-left mr-2"></i>Back
                                </button>
                                <button type="button" class="btn btn-primary next-step">
                                    Next <i class="fas fa-arrow-right ml-2"></i>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Tab 3: Additional Info -->
                        <div class="tab-pane fade" id="additionalInfo" role="tabpanel">
                            <div class="form-group">
                                <label for="permanent_address" class="font-weight-bold">Permanent Address</label>
                                <textarea class="form-control" id="permanent_address" name="permanent_address" rows="3" placeholder="Enter permanent address"></textarea>
                            </div>
                            
                            <div class="form-group">
                                <label for="current_address" class="font-weight-bold">Current Address</label>
                                <div class="custom-control custom-checkbox mb-2">
                                    <input type="checkbox" class="custom-control-input" id="sameAsPermanent">
                                    <label class="custom-control-label" for="sameAsPermanent">Same as permanent address</label>
                                </div>
                                <textarea class="form-control" id="current_address" name="current_address" rows="3" placeholder="Enter current address"></textarea>
                            </div>
                            
                            <div class="form-group">
                                <label class="font-weight-bold">Checklist</label>
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="custom-control custom-checkbox mb-3">
                                                    <input type="checkbox" class="custom-control-input" id="checklist1" name="checklist[]" value="1">
                                                    <label class="custom-control-label" for="checklist1">Identity Proof</label>
                                                </div>
                                                <div class="custom-control custom-checkbox mb-3">
                                                    <input type="checkbox" class="custom-control-input" id="checklist2" name="checklist[]" value="2">
                                                    <label class="custom-control-label" for="checklist2">Address Proof</label>
                                                </div>
                                                <div class="custom-control custom-checkbox mb-3">
                                                    <input type="checkbox" class="custom-control-input" id="checklist3" name="checklist[]" value="3">
                                                    <label class="custom-control-label" for="checklist3">PAN Card</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="custom-control custom-checkbox mb-3">
                                                    <input type="checkbox" class="custom-control-input" id="checklist4" name="checklist[]" value="4">
                                                    <label class="custom-control-label" for="checklist4">Agreement Copy</label>
                                                </div>
                                                <div class="custom-control custom-checkbox mb-3">
                                                    <input type="checkbox" class="custom-control-input" id="checklist5" name="checklist[]" value="5">
                                                    <label class="custom-control-label" for="checklist5">Payment Receipts</label>
                                                </div>
                                                <div class="custom-control custom-checkbox mb-3">
                                                    <input type="checkbox" class="custom-control-input" id="checklist6" name="checklist[]" value="6">
                                                    <label class="custom-control-label" for="checklist6">NOC Documents</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between mt-4">
                                <button type="button" class="btn btn-outline-secondary prev-step">
                                    <i class="fas fa-arrow-left mr-2"></i>Back
                                </button>
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-check-circle mr-2"></i>Submit
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>