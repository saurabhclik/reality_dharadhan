
@php
    $softwareType = session('software_type', 'real_state');
    $isLeadManagement = $softwareType === 'lead_management';
@endphp
<div class="modal fade" id="postSaleModal" tabindex="-1" aria-labelledby="postSaleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="postSaleModalLabel">Add New Customer</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="postSaleForm" action="{{ route('post-sale.store') }}" method="POST">
                @csrf
                <input type="hidden" id="post-sale-id" name="post-sale-id">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Basic Information</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label">Select Lead <span class="text-danger">*</span></label>
                                        <select class="form-select selectPostSale" name="lead_id" id="lead_id" required>
                                            <option value="">Select Lead</option>
                                            @foreach($leads as $lead)
                                                <option value="{{ $lead->id }}">{{ $lead->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Select Sales Person <span class="text-danger">*</span></label>
                                        <select class="form-select selectSalesPerson" name="sales_person_id" id="sales_person_id" required>
                                            <option value="">Select Sales Person</option>
                                            @foreach($salesPersons as $person)
                                                <option value="{{ $person->id }}">{{ $person->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Applicant Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="applicant_name" id="applicant_name" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Applicant Number <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="applicant_number" id="applicant_number" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">{{ $softwareType === 'lead_management' ? 'Product Detail' : 'Project Detail' }}</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label">{{ $softwareType === 'lead_management' ? 'Product Name' : 'Project Name' }} <span class="text-danger">*</span></label>
                                        <select name="project_name" id="project_name" class="form-select selectProject" required>
                                            @foreach($projects as $project)
                                                <option value="{{$project->id}}">{{$project->project_name}}</option>
                                            @endforeach
                                        </select>
                                        <!-- <input type="text" class="form-control" name="project_name" id="project_name" required> -->
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Unit Number <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="unit_number" id="unit_number" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">{{ $softwareType === 'lead_management' ? 'Product Category' : 'Project Category' }} <span class="text-danger">*</span></label>
                                        <select class="form-select selectProjectCat" name="project_category" id="project_category_id" required>
                                            <option value="">Select Category</option>
                                            @foreach($projectCategories as $category)
                                                <option value="{{ $category->name }}">{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">{{ $softwareType === 'lead_management' ? 'Product Sub Category' : 'Project Sub Category' }} <span class="text-danger">*</span></label>
                                        <select class="form-select selectPostSubCat" name="project_sub_category" id="project_subcategory_id">
                                            <option value="">Select Subcategory</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Dates & Contact</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row g-2">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Date of Birth <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control" name="dob" id="dob" required>
                                        </div>
                                        
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Date of Agreement</label>
                                            <input type="date" class="form-control" name="doa" id="doa">
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" class="form-control" name="email" id="email">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Address Information</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label">Permanent Address <span class="text-danger">*</span></label>
                                        <textarea class="form-control" name="permanent_address" id="permanent_address" rows="3" required></textarea>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Current Address <span class="text-danger">*</span></label>
                                        <textarea class="form-control" name="current_address" id="current_address" rows="3" required></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Checklist Items</h6>
                                </div>
                                <div class="card-body">
                                    @if($checklistItems->count() > 0)
                                    <div class="row">
                                        @foreach($checklistItems as $item)
                                        <div class="col-md-3 mb-3">
                                            <div class="form-check card checklist-card">
                                                <input class="form-check-input checklist-item" type="checkbox" 
                                                    name="checklist[]" value="{{ $item->id }}" 
                                                    id="checklist_{{ $item->id }}">
                                                <label class="form-check-label card-body py-2" for="checklist_{{ $item->id }}">
                                                    <div class="d-flex align-items-center">
                                                        <i class="fas fa-check-circle text-success me-2"></i>
                                                        <span>{{ $item->name }}</span>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                    @else
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i> No checklist items available. Please add some in the settings.
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
