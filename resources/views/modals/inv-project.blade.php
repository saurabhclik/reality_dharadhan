
<div class="modal fade" id="projectModal" tabindex="-1" aria-labelledby="projectModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xxl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-gradient-primary text-white">
                <div class="w-100">
                    <h5 class="modal-title" id="projectModalLabel">Project Inventory Management</h5>
                    <p class="mb-0 small opacity-75" id="formProgressText">Complete all  fields</p>
                    <div class="progress mt-2" style="height: 4px;">
                        <div class="progress-bar bg-white" id="formProgressBar" role="progressbar" style="width: 0%"></div>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="projectForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="id" name="id">
                <div class="modal-body p-0">
                    <div class="row g-0">
                        <div class="col-lg-2 border-end">
                            <div class="nav flex-column nav-pills sticky-top" id="formTabs" role="tablist" style="top: 1rem;">
                                <button class="nav-link active" id="basic-tab" data-bs-toggle="pill" data-bs-target="#basic-info" type="button" role="tab">
                                    <i class="fas fa-info-circle me-2"></i> Basic Info
                                </button>
                                <button class="nav-link" id="location-tab" data-bs-toggle="pill" data-bs-target="#location-info" type="button" role="tab">
                                    <i class="fas fa-map-marker-alt me-2"></i> Location
                                </button>
                                <button class="nav-link" id="media-tab" data-bs-toggle="pill" data-bs-target="#media-assets" type="button" role="tab">
                                    <i class="fas fa-images me-2"></i> Media
                                </button>
                                <button class="nav-link" id="specs-tab" data-bs-toggle="pill" data-bs-target="#specifications" type="button" role="tab">
                                    <i class="fas fa-ruler-combined me-2"></i> Specifications
                                </button>
                                <button class="nav-link" id="amenities-tab" data-bs-toggle="pill" data-bs-target="#amenities" type="button" role="tab">
                                    <i class="fas fa-list-check me-2"></i> Amenities
                                </button>
                                <button class="nav-link" id="contact-tab" data-bs-toggle="pill" data-bs-target="#contact-info" type="button" role="tab">
                                    <i class="fas fa-address-book me-2"></i> Contact
                                </button>
                                <button class="nav-link text-success" id="form-fields-tab" data-bs-toggle="pill" data-bs-target="#lead-fields" type="button" role="tab">
                                    <i class="fas fa-user-check me-2"></i> Form Fields
                                </button>
                                <button class="nav-link" id="styling-tab" data-bs-toggle="pill" data-bs-target="#styling-options" type="button" role="tab">
                                    <i class="fas fa-paint-brush me-2"></i> Styling
                                </button>
                            </div>
                        </div>
                        <div class="col-lg-10">
                            <div class="tab-content p-4" id="formTabsContent">
                                <div class="tab-pane fade show active" id="basic-info" role="tabpanel">
                                    <h5 class="mb-4 text-primary d-flex align-items-center">
                                        <i class="fas fa-info-circle me-2"></i> Basic Information
                                    </h5>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <select class="form-select" name="type" id="category_type" >
                                                    <option value="" disabled selected>Select Project Type</option>
                                                    <option value="Residential">Residential</option>
                                                    <option value="Commercial">Commercial</option>
                                                </select>
                                                <label for="type" class="d-flex align-items-center">
                                                    <span>Project Type</span>
                                                    <span class="text-danger ms-1">*</span>
                                                </label>
                                            </div>
                                        </div>                        
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="text" class="form-control" name="project_name" id="project_name" placeholder="Project Name" >
                                                <label for="name" class="d-flex align-items-center">
                                                    <span>Project Name</span>
                                                    <span class="text-danger ms-1">*</span>
                                                </label>
                                            </div>
                                        </div>                        
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <select class="form-select" name="category_id" id="category_id" >
                                                    <option value="" disabled selected>Select Category</option>
                                                </select>
                                                <label for="category_id" class="d-flex align-items-center">
                                                    <span>Category</span>
                                                    <span class="text-danger ms-1">*</span>
                                                </label>
                                            </div>
                                        </div>                                       
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <select class="form-select" name="sub_category_id" id="sub_category_id" >
                                                    <option value="" disabled selected>Select Sub Category</option>
                                                </select>
                                                <label for="sub_category_id" class="d-flex align-items-center">
                                                    <span>Sub Category</span>
                                                    <span class="text-danger ms-1">*</span>
                                                </label>
                                            </div>
                                        </div>                                        
                                        <div class="col-12">
                                            <div class="form-floating">
                                                <textarea class="form-control" name="description" id="description" placeholder="Detailed Description" style="height: 100px" ></textarea>
                                                <label for="description" class="d-flex align-items-center">
                                                    <span class="mb-5">Detailed Description</span>
                                                    <span class="text-danger ms-1 mb-5">*</span>
                                                </label>
                                            </div>
                                            <div class="form-text">Describe the backend design of the project in detail (minimum 160 characters).</div>
                                        </div>                                      
                                        <div class="col-12">
                                            <div class="form-floating">
                                                <textarea class="form-control" name="short_description" id="short_description" placeholder="Short Description" style="height: 80px"></textarea>
                                                <label for="short_description"><span class="mb-4">Banner Tagline</span></label>
                                            </div>
                                            <div class="form-text">Brief description for cards and listings (optional).</div>
                                        </div>                                       
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="text" class="form-control" name="slug" id="slug" placeholder="URL Slug" readonly>
                                                <label for="slug">URL Slug</label>
                                            </div>
                                            <div class="form-text">Auto-generated from project name</div>
                                        </div>    
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="location-info" role="tabpanel">
                                    <h5 class="mb-4 text-primary d-flex align-items-center">
                                        <i class="fas fa-map-marker-alt me-2"></i> Location Details
                                    </h5>
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <div class="form-floating">
                                                <textarea class="form-control" name="full_address" id="full_address" placeholder="Full Address" style="height: 100px" ></textarea>
                                                <label for="location" class="d-flex align-items-center">
                                                    <span class="mb-5">Full Address</span>
                                                    <span class="text-danger ms-1 mb-5">*</span>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-floating">
                                                <input type="text" class="form-control" name="city" id="city" placeholder="City" >
                                                <label for="city" class="d-flex align-items-center">
                                                    <span>City</span>
                                                    <span class="text-danger ms-1">*</span>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-floating">
                                                <input type="text" class="form-control" name="state" id="state" placeholder="State" >
                                                <label for="state" class="d-flex align-items-center">
                                                    <span>State</span>
                                                    <span class="text-danger ms-1">*</span>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-floating">
                                                <input type="text" class="form-control" name="country" id="country" value="India" readonly>
                                                <label for="country">Country</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-floating">
                                                <input type="text" class="form-control" name="pin_code" id="pin_code" placeholder="PIN Code">
                                                <label for="pin_code">PIN Code</label>
                                            </div>
                                        </div>   
                                        <div class="col-md-4">
                                            <div class="form-floating">
                                                <input type="float" class="form-control" name="longitude" id="longitude" placeholder="Longitude">
                                                <label for="longitude">
                                                    <span>Longitude</span>
                                                    <span class="text-danger ms-1">*</span>
                                                </label>
                                            </div>
                                        </div>                                   
                                        <div class="col-md-4">
                                            <div class="form-floating">
                                                <input type="text" class="form-control" name="latitude" id="latitude" placeholder="Latitude">
                                                <label for="latitude">
                                                    <span>Latitude</span>
                                                    <span class="text-danger ms-1">*</span>
                                                </label>
                                            </div>
                                        </div>   
                                        <div class="col-12">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h6 class="mb-0">Nearby Locations</h6>
                                                <button type="button" class="btn btn-sm btn-outline-primary" id="add_nearby_location">
                                                    <i class="fas fa-plus me-1"></i> Add Location
                                                </button>
                                            </div>                              
                                            <div id="nearby_locations_container">
                                                <div class="row nearby-location mb-2">
                                                    <div class="col-md-5">
                                                        <input type="text" class="form-control" name="nearby_locations[0][building]" placeholder="Building Name">
                                                    </div>
                                                    <div class="col-md-5">
                                                        <input type="text" class="form-control" name="nearby_locations[0][location]" placeholder="Location/Distance">
                                                    </div>
                                                    <div class="col-md-2">
                                                        <button type="button" class="btn btn-sm btn-outline-danger remove-nearby" disabled>
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-text">Add nearby landmarks or important locations</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="media-assets" role="tabpanel">
                                    <h5 class="mb-4 text-primary d-flex align-items-center">
                                        <i class="fas fa-images me-2"></i> Media Assets
                                    </h5>                                    
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label d-flex align-items-center">
                                                <span>Project Logo</span>
                                                <span class="text-danger ms-1">*</span>
                                            </label>
                                            <div class="file-upload-card">
                                                <input type="file" class="form-control d-none" name="logo" id="logo" accept="image/*">
                                                <label for="logo" class="file-upload-label">
                                                    <div class="file-upload-preview" id="logoPreview">
                                                        <div class="upload-placeholder">
                                                            <i class="fas fa-image fs-4"></i>
                                                            <span class="d-block mt-2">Upload Logo</span>
                                                        </div>
                                                    </div>
                                                </label>
                                                <div class="form-text">Recommended: 300×300px, PNG with transparent background</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label d-flex align-items-center">
                                                <span>Cover Image</span>
                                                <span class="text-danger ms-1">*</span>
                                            </label>
                                            <div class="file-upload-card">
                                                <input type="file" class="form-control d-none" name="cover_image" id="cover_image" accept="image/*">
                                                <label for="cover_image" class="file-upload-label">
                                                    <div class="file-upload-preview" id="coverImagePreview">
                                                        <div class="upload-placeholder">
                                                            <i class="fas fa-image fs-4"></i>
                                                            <span class="d-block mt-2">Upload Cover</span>
                                                        </div>
                                                    </div>
                                                </label>
                                                <div class="form-text">Recommended: 1200×600px, JPG/PNG landscape orientation</div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Floor Plan</label>
                                            <div class="file-upload-card">
                                                <input type="file" class="form-control d-none" name="floor_plan" id="floor_plan" accept="image/*,.pdf">
                                                <label for="floor_plan" class="file-upload-label">
                                                    <div class="file-upload-preview" id="floorPlanPreview">
                                                        <div class="upload-placeholder">
                                                            <i class="fas fa-vector-square fs-4"></i>
                                                            <span class="d-block mt-2">Upload Floor Plan</span>
                                                        </div>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Site Map</label>
                                            <div class="file-upload-card">
                                                <input type="file" class="form-control d-none" name="site_map" id="site_map" accept="image/*,.pdf">
                                                <label for="site_map" class="file-upload-label">
                                                    <div class="file-upload-preview" id="siteMapPreview">
                                                        <div class="upload-placeholder">
                                                            <i class="fas fa-map-marked-alt fs-4"></i>
                                                            <span class="d-block mt-2">Upload Site Map</span>
                                                        </div>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Price List</label>
                                            <div class="file-upload-card">
                                                <input type="file" class="form-control d-none" name="price_list" id="price_list" accept="image/*,.pdf">
                                                <label for="price_list" class="file-upload-label">
                                                    <div class="file-upload-preview" id="priceListPreview">
                                                        <div class="upload-placeholder">
                                                            <i class="fas fa-file-invoice-dollar fs-4"></i>
                                                            <span class="d-block mt-2">Upload Price List</span>
                                                        </div>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Brochure/Document</label>
                                            <div class="file-upload-card">
                                                <input type="file" class="form-control d-none" name="brochure" id="brochure" accept=".pdf,.doc,.docx,image/*">
                                                <label for="brochure" class="file-upload-label">
                                                    <div class="file-upload-preview" id="brochurePreview">
                                                        <div class="upload-placeholder">
                                                            <i class="fas fa-file-pdf fs-4"></i>
                                                            <span class="d-block mt-2">Upload Brochure</span>
                                                        </div>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="url" class="form-control" name="video_link" id="video_link" placeholder="YouTube/Vimeo URL">
                                                <label for="video_link">Video Link</label>
                                            </div>
                                            <div class="form-text">Paste YouTube or Vimeo URL for property video</div>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Gallery Images</label>
                                            <div class="file-upload-card">
                                                <input type="file" class="form-control d-none" name="gallery_images[]" id="gallery_images" multiple accept="image/*">
                                                <label for="gallery_images" class="file-upload-label">
                                                    <div class="file-upload-preview" id="galleryPreviews">
                                                        <div class="upload-placeholder">
                                                            <i class="fas fa-images fs-4"></i>
                                                            <span class="d-block mt-2">Upload Gallery Images</span>
                                                            <small class="text-muted">(5-10 images recommended)</small>
                                                        </div>
                                                    </div>
                                                </label>
                                                <div class="form-text">Upload high quality property images (JPEG/PNG, min 800×600px)</div>
                                            </div>
                                        </div>
                                        <!-- <div class="col-12 mt-4">
                                            <h6 class="mb-3"><i class="fas fa-image me-2"></i> Banner Settings</h6>
                                            <div class="row g-3">
                                                <div class="col-md-4">
                                                    <div class="form-floating">
                                                        <select class="form-select" name="banner_size" id="banner_size">
                                                            <option value="cover">Cover (Full)</option>
                                                            <option value="contain">Contain (Fit)</option>
                                                            <option value="custom">Custom Size</option>
                                                        </select>
                                                        <label for="banner_size">Banner Size</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-floating">
                                                        <select class="form-select" name="banner_position" id="banner_position">
                                                            <option value="center">Center</option>
                                                            <option value="top">Top</option>
                                                            <option value="bottom">Bottom</option>
                                                            <option value="left">Left</option>
                                                            <option value="right">Right</option>
                                                        </select>
                                                        <label for="banner_position">Banner Alignment</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-floating">
                                                        <select class="form-select" name="banner_repeat" id="banner_repeat">
                                                            <option value="no-repeat">No Repeat</option>
                                                            <option value="repeat">Repeat</option>
                                                            <option value="repeat-x">Repeat Horizontally</option>
                                                            <option value="repeat-y">Repeat Vertically</option>
                                                        </select>
                                                        <label for="banner_repeat">Banner Repeat</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div> -->
                                        <!-- <div class="col-12 mt-4">
                                            <h6 class="mb-3">
                                                <i class="fas fa-expand me-2"></i> Image Size Settings
                                            </h6>
                                            <div class="row g-3">
                                                <div class="col-md-3">
                                                    <div class="form-floating">
                                                        <input type="number" class="form-control" name="thumb_width" id="thumb_width" value="300" min="50">
                                                        <label for="thumb_width">Thumbnail Width (px)</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-floating">
                                                        <input type="number" class="form-control" name="thumb_height" id="thumb_height" value="200" min="50">
                                                        <label for="thumb_height">Thumbnail Height (px)</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-floating">
                                                        <select class="form-select" name="thumb_crop" id="thumb_crop">
                                                            <option value="fill">Fill</option>
                                                            <option value="fit">Fit</option>
                                                            <option value="crop">Crop</option>
                                                        </select>
                                                        <label for="thumb_crop">Thumbnail Style</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-floating">
                                                        <select class="form-select" name="thumb_quality" id="thumb_quality">
                                                            <option value="80">Good (80%)</option>
                                                            <option value="90">Better (90%)</option>
                                                            <option value="100">Best (100%)</option>
                                                        </select>
                                                        <label for="thumb_quality">Thumbnail Quality</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div> -->
                                    </div>
                                </div>              
                                <div class="tab-pane fade" id="specifications" role="tabpanel">
                                    <h5 class="mb-4 text-primary d-flex align-items-center">
                                        <i class="fas fa-ruler-combined me-2"></i> Property Specifications
                                    </h5>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <div class="input-group">
                                                    <input type="number" class="form-control" name="price" id="price" placeholder="Price" >
                                                    <select class="form-select" name="price_unit" id="price_unit" style="max-width: 120px;">
                                                        <option value="sqft" selected>Sq Ft</option>
                                                        <option value="sqm">Sq M</option>
                                                        <option value="acre">Acre</option>
                                                        <option value="hectare">Hectare</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="text" class="form-control" name="price_display" id="price_display" placeholder="Eg: Starting from 50L">
                                                <label for="price_display">Price Display Text</label>
                                            </div>
                                            <div class="form-text">Example: "Starting from ₹50L" or "Call for pricing"</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="text" class="form-control" name="size" id="size" placeholder="Size">
                                                <label for="size">Size (with unit)</label>
                                            </div>
                                            <div class="form-text">Example: "1200 sqft" or "2.5 acres"</div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-floating">
                                                <input type="number" class="form-control" name="beds" id="beds" placeholder="Beds">
                                                <label for="beds">Bedrooms</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-floating">
                                                <input type="number" class="form-control" name="baths" id="baths" placeholder="Baths">
                                                <label for="baths">Bathrooms</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-floating">
                                                <input type="text" class="form-control" name="balcony" id="balcony" placeholder="Balcony">
                                                <label for="balcony">Balcony</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-floating">
                                                <input type="text" class="form-control" name="terrace" id="terrace" placeholder="Terrace">
                                                <label for="terrace">Terrace</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <select class="form-select" name="super_carpet" id="super_carpet">
                                                    <option value="" selected>Super Carpet</option>
                                                    <option value="Yes">Yes</option>
                                                    <option value="No">No</option>
                                                </select>
                                                <label for="super_carpet">Super Carpet Area</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <select class="form-select" name="carpet_lobby" id="carpet_lobby">
                                                    <option value="" selected>Carpet Lobby</option>
                                                    <option value="Yes">Yes</option>
                                                    <option value="No">No</option>
                                                </select>
                                                <label for="carpet_lobby">Carpet Lobby</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>                      
                                <div class="tab-pane fade" id="amenities" role="tabpanel">
                                    <h5 class="mb-4 text-primary d-flex align-items-center">
                                        <i class="fas fa-list-check me-2"></i> Amenities & Features
                                    </h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="amenity-group">
                                                <h6 class="text-muted mb-3">
                                                    <i class="fas fa-swimming-pool me-2"></i> Recreational
                                                </h6>
                                                <div class="form-check form-switch mb-2">
                                                    <input class="form-check-input" type="checkbox" name="amenities[]" value="Swimming Pool" id="swimming_pool">
                                                    <label class="form-check-label ms-2" for="swimming_pool">Swimming Pool</label>
                                                </div>
                                                <div class="form-check form-switch mb-2">
                                                    <input class="form-check-input" type="checkbox" name="amenities[]" value="Gym" id="gym">
                                                    <label class="form-check-label ms-2" for="gym">Gym/Fitness Center</label>
                                                </div>
                                                <div class="form-check form-switch mb-2">
                                                    <input class="form-check-input" type="checkbox" name="amenities[]" value="Club House" id="club_house">
                                                    <label class="form-check-label ms-2" for="club_house">Club House</label>
                                                </div>
                                                <div class="form-check form-switch mb-2">
                                                    <input class="form-check-input" type="checkbox" name="amenities[]" value="Playground" id="playground">
                                                    <label class="form-check-label ms-2" for="playground">Children's Playground</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="amenity-group">
                                                <h6 class="text-muted mb-3">
                                                    <i class="fas fa-building me-2"></i> Building Features
                                                </h6>
                                                <div class="form-check form-switch mb-2">
                                                    <input class="form-check-input" type="checkbox" name="amenities[]" value="Elevator" id="elevator">
                                                    <label class="form-check-label ms-2" for="elevator">Elevator/Lift</label>
                                                </div>
                                                <div class="form-check form-switch mb-2">
                                                    <input class="form-check-input" type="checkbox" name="amenities[]" value="Parking" id="parking">
                                                    <label class="form-check-label ms-2" for="parking">Parking Space</label>
                                                </div>
                                                <div class="form-check form-switch mb-2">
                                                    <input class="form-check-input" type="checkbox" name="amenities[]" value="Power Backup" id="power_backup">
                                                    <label class="form-check-label ms-2" for="power_backup">Power Backup</label>
                                                </div>
                                                <div class="form-check form-switch mb-2">
                                                    <input class="form-check-input" type="checkbox" name="amenities[]" value="Water Supply" id="water_supply">
                                                    <label class="form-check-label ms-2" for="water_supply">24/7 Water Supply</label>
                                                </div>
                                            </div>                                        
                                        </div>
                                        <div class="col-12 mt-3">
                                            <label class="form-label">Custom Amenities</label>
                                            <div class="input-group mb-3">
                                                <input type="text" class="form-control" id="customAmenityInput" placeholder="Add custom amenity">
                                                <button class="btn btn-outline-primary" type="button" id="addCustomAmenity">
                                                    <i class="fas fa-plus me-1"></i> Add
                                                </button>
                                            </div>
                                            <div id="customAmenitiesContainer" class="d-flex flex-wrap gap-2"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="contact-info" role="tabpanel">
                                    <h5 class="mb-4 text-primary d-flex align-items-center">
                                        <i class="fas fa-address-book me-2"></i> Contact Information
                                    </h5>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="tel" class="form-control" name="contact_number_1" id="contact_number_1" placeholder="Primary Phone" >
                                                <label for="contact_number_1" class="d-flex align-items-center">
                                                    <span>Primary Phone</span>
                                                    <span class="text-danger ms-1">*</span>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="tel" class="form-control" name="contact_number_2" id="contact_number_2" placeholder="Secondary Phone">
                                                <label for="contact_number_2">Secondary Phone</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="tel" class="form-control" name="whatsapp_number" id="whatsapp_number" placeholder="WhatsApp Number">
                                                <label for="whatsapp_number">WhatsApp Number</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="email" class="form-control" name="email" id="email" placeholder="Email Address">
                                                <label for="email">Email Address</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="url" class="form-control" name="instagram_link" id="instagram_link" placeholder="Instagram URL">
                                                <label for="instagram_link">Instagram URL</label>
                                            </div>
                                            <div class="form-text">Paste the Instagram profile URL (optional)</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="url" class="form-control" name="facebook_link" id="facebook_link" placeholder="Facebook URL">
                                                <label for="facebook_link">Facebook URL</label>
                                            </div>
                                            <div class="form-text">Paste the Facebook page URL (optional)</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="url" class="form-control" name="twitter_link" id="twitter_link" placeholder="Twitter URL">
                                                <label for="twitter_link">Twitter URL</label>
                                            </div>
                                            <div class="form-text">Paste the Twitter profile URL (optional)</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="url" class="form-control" name="linkedin_link" id="linkedin_link" placeholder="LinkedIn URL">
                                                <label for="linkedin_link">LinkedIn URL</label>
                                            </div>
                                            <div class="form-text">Paste the LinkedIn profile URL (optional)</div>
                                        </div>
                                        <!-- <div class="col-12">
                                            <div class="form-floating">
                                                <textarea class="form-control" name="contact_notes" id="contact_notes" placeholder="Contact Notes" style="height: 100px"></textarea>
                                                <label for="contact_notes">Contact Notes</label>
                                            </div>
                                            <div class="form-text">Any special instructions for contacting about this property</div>
                                        </div> -->
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="lead-fields" role="tabpanel">
                                    <h5 class="mb-4 text-primary d-flex align-items-center">
                                        <i class="fas fa-user-check me-2"></i> Form Fields Visibility
                                    </h5>
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <p class="text-muted small">Select which fields should be displayed in the form for this project.</p>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-check form-switch mb-3">
                                                <input class="form-check-input" type="checkbox" name="form_fields[]" value="name" id="lead_field_name" {{ isset($project->form_fields) && in_array('name', $project->form_fields) ? 'checked' : '' }}>
                                                <label class="form-check-label ms-2" for="lead_field_name">Name</label>
                                            </div>
                                            <div class="form-check form-switch mb-3">
                                                <input class="form-check-input" type="checkbox" name="form_fields[]" value="budget" id="lead_field_budget" {{ isset($project->form_fields) && in_array('budget', $project->form_fields) ? 'checked' : '' }}>
                                                <label class="form-check-label ms-2" for="lead_field_budget">Budget</label>
                                            </div>
                                            <div class="form-check form-switch mb-3">
                                                <input class="form-check-input" type="checkbox" name="form_fields[]" value="email" id="lead_field_email" {{ isset($project->form_fields) && in_array('email', $project->form_fields) ? 'checked' : '' }}>
                                                <label class="form-check-label ms-2" for="lead_field_email">Email</label>
                                            </div>
                                            <div class="form-check form-switch mb-3">
                                                <input class="form-check-input" type="checkbox" name="form_fields[]" value="phone" id="lead_field_phone" {{ isset($project->form_fields) && in_array('phone', $project->form_fields) ? 'checked' : '' }}>
                                                <label class="form-check-label ms-2" for="lead_field_phone">Phone</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-check form-switch mb-3">
                                                <input class="form-check-input" type="checkbox" name="form_fields[]" value="address" id="lead_field_address" {{ isset($project->form_fields) && in_array('address', $project->form_fields) ? 'checked' : '' }}>
                                                <label class="form-check-label ms-2" for="lead_field_address">Address</label>
                                            </div>
                                            <div class="form-check form-switch mb-3">
                                                <input class="form-check-input" type="checkbox" name="form_fields[]" value="state" id="lead_field_state" {{ isset($project->form_fields) && in_array('state', $project->form_fields) ? 'checked' : '' }}>
                                                <label class="form-check-label ms-2" for="lead_field_state">State</label>
                                            </div>
                                            <div class="form-check form-switch mb-3">
                                                <input class="form-check-input" type="checkbox" name="form_fields[]" value="city" id="lead_field_city" {{ isset($project->form_fields) && in_array('city', $project->form_fields) ? 'checked' : '' }}>
                                                <label class="form-check-label ms-2" for="lead_field_city">City</label>
                                            </div>                                        
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="styling-options" role="tabpanel">
                                    <h5 class="mb-4 text-primary d-flex align-items-center">
                                        <i class="fas fa-paint-brush me-2"></i> Advanced Styling
                                    </h5>   
                                    <div class="col-12 mt-4">
                                        <h6 class="mb-3"><i class="fas fa-palette me-2"></i> Predefined Themes</h6>
                                        <div class="theme-preview-container">
                                            <div class="theme-preview" onclick="applyTheme('classic')">
                                                <div class="theme-colors">
                                                    <div style="background-color: #0a0a0a;"></div>
                                                    <div style="background-color: #b08d57;"></div>
                                                    <div style="background-color: #3c5a66;"></div>
                                                </div>
                                                <div class="theme-info">
                                                    <h6>Classic</h6>
                                                    <small>Elegant gold & dark theme</small>
                                                </div>
                                            </div>

                                            <div class="theme-preview" onclick="applyTheme('modern')">
                                                <div class="theme-colors">
                                                    <div style="background-color: #1a1a1a;"></div>
                                                    <div style="background-color: #2d2d2d;"></div>
                                                    <div style="background-color: #c8a97e;"></div>
                                                </div>
                                                <div class="theme-info">
                                                    <h6>Modern</h6>
                                                    <small>Clean minimalist design</small>
                                                </div>
                                            </div>

                                            <div class="theme-preview" onclick="applyTheme('luxury')">
                                                <div class="theme-colors">
                                                    <div style="background-color: #121212;"></div>
                                                    <div style="background-color: #d4af37;"></div>
                                                    <div style="background-color: #5d432c;"></div>
                                                </div>
                                                <div class="theme-info">
                                                    <h6>Luxury</h6>
                                                    <small>Rich gold & dark tones</small>
                                                </div>
                                            </div>

                                            <div class="theme-preview" onclick="applyTheme('coastal')">
                                                <div class="theme-colors">
                                                    <div style="background-color: #2c3e50;"></div>
                                                    <div style="background-color: #3498db;"></div>
                                                    <div style="background-color: #f39c12;"></div>
                                                </div>
                                                <div class="theme-info">
                                                    <h6>Coastal</h6>
                                                    <small>Beach-inspired colors</small>
                                                </div>
                                            </div>

                                            <div class="theme-preview" onclick="applyTheme('earthy')">
                                                <div class="theme-colors">
                                                    <div style="background-color: #3e2723;"></div>
                                                    <div style="background-color: #8d6e63;"></div>
                                                    <div style="background-color: #a1887f;"></div>
                                                </div>
                                                <div class="theme-info">
                                                    <h6>Earthy</h6>
                                                    <small>Natural tones</small>
                                                </div>
                                            </div>
                                        
                                            <div class="theme-preview" onclick="applyTheme('monochrome')">
                                                <div class="theme-colors">
                                                    <div style="background-color: #222;"></div>
                                                    <div style="background-color: #555;"></div>
                                                    <div style="background-color: #999;"></div>
                                                </div>
                                                <div class="theme-info">
                                                    <h6>Monochrome</h6>
                                                    <small>Black & white elegance</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>                               
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <div class="form-floating">
                                                <input type="color" class="form-control form-control-color" name="style_settings[primary_color]" id="primary_color" value="#1a1a1a">
                                                <label for="primary_color">Primary Color</label>
                                            </div>
                                            <small class="text-muted d-block mt-1">Main buttons, badges, headers, footer bg, hover states.</small>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-floating">
                                                <input type="color" class="form-control form-control-color" name="style_settings[secondary_color]" id="secondary_color" value="#2d2d2d">
                                                <label for="secondary_color">Secondary Color</label>
                                            </div>
                                            <small class="text-muted d-block mt-1">Nav links, h3 highlights, footer links, close button hover.</small>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-floating">
                                                <input type="color" class="form-control form-control-color" name="style_settings[accent_color]" id="accent_color" value="#c8a97e">
                                                <label for="accent_color">Accent Color</label>
                                            </div>
                                            <small class="text-muted d-block mt-1">Icons, accent buttons, highlights, borders.</small>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-floating">
                                                <input type="color" class="form-control form-control-color" name="style_settings[text_color]" id="text_color" value="#f8f8f8">
                                                <label for="text_color">Text Color</label>
                                            </div>
                                            <small class="text-muted d-block mt-1">Main text color for content and labels.</small>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-floating">
                                                <input type="color" class="form-control form-control-color" name="style_settings[text_secondary]" id="text_secondary" value="#cccccc">
                                                <label for="text_secondary">Secondary Text Color</label>
                                            </div>
                                            <small class="text-muted d-block mt-1">Secondary text, meta info, disabled labels.</small>
                                        </div>


                                        <div class="col-md-4">
                                            <div class="form-floating">
                                                <input type="text" class="form-control" name="style_settings[card_radius]" id="card_radius" value="12" min="0" max="50">
                                                <label for="card_radius">Card Radius (px)</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <select class="form-select" name="style_settings[font_heading]" id="font_heading">
                                                    <option value="DM Serif Display, serif">DM Serif Display</option>
                                                    <option value="Cinzel, serif">Cinzel</option>
                                                    <option value="Montserrat, sans-serif">Montserrat</option>
                                                    <option value="Roboto, sans-serif">Roboto</option>
                                                    <option value="Open Sans, sans-serif">Open Sans</option>
                                                    <option value="Lato, sans-serif">Lato</option>
                                                    <option value="Oswald, sans-serif">Oswald</option>
                                                    <option value="Raleway, sans-serif">Raleway</option>
                                                    <option value="PT Serif, serif">PT Serif</option>
                                                    <option value="Merriweather, serif">Merriweather</option>
                                                    <option value="Nunito, sans-serif">Nunito</option>
                                                    <option value="Ubuntu, sans-serif">Ubuntu</option>
                                                    <option value="Playfair Display, serif">Playfair Display</option>
                                                    <option value="Poppins, sans-serif">Poppins</option>
                                                    <option value="Source Sans Pro, sans-serif">Source Sans Pro</option>
                                                    <option value="Work Sans, sans-serif">Work Sans</option>
                                                    <option value="Inconsolata, monospace">Inconsolata</option>
                                                    <option value="Fira Sans, sans-serif">Fira Sans</option>
                                                    <option value="Josefin Sans, sans-serif">Josefin Sans</option>
                                                    <option value="Cabin, sans-serif">Cabin</option>
                                                </select>
                                                <label for="font_heading">Heading Font</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <select class="form-select" name="style_settings[font_body]" id="font_body">
                                                    <option value="'Montserrat', sans-serif">Montserrat</option>
                                                    <option value="EB Garamond, serif">EB Garamond</option>
                                                    <option value="Roboto, sans-serif">Roboto</option>
                                                    <option value="Open Sans, sans-serif">Open Sans</option>
                                                    <option value="Lato, sans-serif">Lato</option>
                                                    <option value="Oswald, sans-serif">Oswald</option>
                                                    <option value="Raleway, sans-serif">Raleway</option>
                                                    <option value="PT Serif, serif">PT Serif</option>
                                                    <option value="Merriweather, serif">Merriweather</option>
                                                    <option value="Nunito, sans-serif">Nunito</option>
                                                    <option value="Ubuntu, sans-serif">Ubuntu</option>
                                                    <option value="Playfair Display, serif">Playfair Display</option>
                                                    <option value="Poppins, sans-serif">Poppins</option>
                                                    <option value="Source Sans Pro, sans-serif">Source Sans Pro</option>
                                                    <option value="Work Sans, sans-serif">Work Sans</option>
                                                    <option value="Inconsolata, monospace">Inconsolata</option>
                                                    <option value="Fira Sans, sans-serif">Fira Sans</option>
                                                    <option value="Josefin Sans, sans-serif">Josefin Sans</option>
                                                    <option value="Droid Serif, serif">Droid Serif</option>
                                                    <option value="Cabin, sans-serif">Cabin</option>
                                                </select>
                                                <label for="font_body">Body Font</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <select class="form-select" name="style_settings[button_style]" id="button_style">
                                                    <option value="rounded">Rounded</option>
                                                    <option value="square">Square</option>
                                                    <option value="pill">Pill</option>
                                                </select>
                                                <label for="button_style">Button Style</label>
                                            </div>
                                        </div>                                        
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <select class="form-select" name="style_settings[nav_style]" id="nav_style">
                                                    <option value="solid">Solid</option>
                                                    <option value="transparent">Transparent</option>
                                                    <option value="glass">Glass Morphism</option>
                                                </select>
                                                <label for="nav_style">Navigation Style</label>
                                            </div>
                                        </div> 
                                        <div class="col-12">
                                            <div class="mb-2 small text-muted">
                                                💡 <strong>Predefined CSS Themes:</strong> Click to apply
                                            </div>                                     
                                            <div class="predefined-css-container mb-3">
                                                <div class="predefined-css-item" onclick="applyPredefinedCss(1)">
                                                <h6>
                                                    <i class="fas fa-moon me-2"></i> Dark Luxury</h6>
                                                    <pre>/* Dark theme with gold accents */
                                                        body {
                                                            background-color: #121212 !important;
                                                            color: #e0e0e0 !important;
                                                        }
                                                        .navbar {
                                                            background-color: rgba(18,18,18,0.95) !important;
                                                            backdrop-filter: blur(10px) !important;
                                                        }
                                                        .card {
                                                            background-color: #1e1e1e !important;
                                                            border: 1px solid #333 !important;
                                                        }
                                                        .btn-primary {
                                                            background: linear-gradient(135deg, #d4af37, #f1c40f) !important;
                                                            color: #121212 !important;
                                                        }</pre>
                                                </div>
                                                <div class="predefined-css-item" onclick="applyPredefinedCss(2)">
                                                    <h6>
                                                        <i class="fas fa-sun me-2"></i> Light & Airy
                                                    </h6>
                                                    <pre>/* Light modern theme */
                                                    body {
                                                        background-color: #f8f9fa !important;
                                                        color: #495057 !important;
                                                    }
                                                    .navbar {
                                                        background-color: rgba(248,249,250,0.95) !important;
                                                        box-shadow: 0 2px 10px rgba(0,0,0,0.1) !important;
                                                    }
                                                    .card {
                                                        background-color: #fff !important;
                                                        box-shadow: 0 5px 15px rgba(0,0,0,0.05) !important;
                                                    }
                                                    .btn-primary {
                                                        background: linear-gradient(135deg, #6c5ce7, #a29bfe) !important;
                                                    }</pre>
                                                </div>
                                                <div class="predefined-css-item" onclick="applyPredefinedCss(3)">
                                                    <h6>
                                                        <i class="fas fa-glass-whiskey me-2"></i> Glass Morphism
                                                    </h6>
                                                    <pre>/* Modern glass effect */
                                                    body {
                                                        background: linear-gradient(135deg, #667eea, #764ba2) !important;
                                                    }
                                                    .navbar, .card {
                                                        background: rgba(255,255,255,0.15) !important;
                                                        backdrop-filter: blur(10px) !important;
                                                        border: 1px solid rgba(255,255,255,0.2) !important;
                                                    }
                                                    .btn-primary {
                                                        background: rgba(255,255,255,0.2) !important;
                                                        backdrop-filter: blur(5px) !important;
                                                        border: 1px solid rgba(255,255,255,0.3) !important;
                                                    }
                                                    </pre>                               
                                                </div>
                                                <div class="predefined-css-item" onclick="applyPredefinedCss(4)">
                                                    <h6>
                                                        <i class="fas fa-chess-board me-2"></i> Minimalist B&W
                                                    </h6>
                                                    <pre>/* Pure black and white */
                                                    body {
                                                        background-color: #fff !important;
                                                        color: #000 !important;
                                                    }
                                                    .navbar {
                                                        background-color: #fff !important;
                                                        border-bottom: 1px solid #000 !important;
                                                    }
                                                    .card {
                                                        background-color: #fff !important;
                                                        border: 1px solid #000 !important;
                                                        border-radius: 0 !important;
                                                    }
                                                    .btn-primary {
                                                        background: #000 !important;
                                                        color: #fff !important;
                                                        border-radius: 0 !important;
                                                    }</pre>
                                                </div>
                                                <div class="predefined-css-item" onclick="applyPredefinedCss(5)">
                                                    <h6>
                                                    <i class="fas fa-palette me-2"></i> Vibrant Gradient
                                                    </h6>
                                                    <pre>/* Colorful gradient theme */
                                                    body {
                                                        background: linear-gradient(135deg, #f5f7fa, #c3cfe2) !important;
                                                    }
                                                    .navbar {
                                                        background: linear-gradient(135deg, #667eea, #764ba2) !important;
                                                    }
                                                    .card {
                                                        background: rgba(255,255,255,0.9) !important;
                                                        box-shadow: 0 10px 30px rgba(0,0,0,0.1) !important;
                                                    }
                                                    .btn-primary {
                                                        background: linear-gradient(135deg, #ff758c, #ff7eb3) !important;
                                                        border: none !important;
                                                    }</pre>
                                                </div>
                                            </div>

                                            <div class="mb-2 small text-muted">
                                                💡 <strong>Tips for writing custom CSS:</strong><br>
                                                • Use valid CSS syntax<br>
                                                • Add <code>!important</code> to override default styles<br>
                                                • Target specific elements or classes<br>
                                                • Test in browser developer tools (F12) before saving<br>
                                            </div>
                                            <div class="form-floating">
                                                <textarea class="form-control" name="style_settings[custom_css]" id="custom_css" placeholder="/* Example:
                                                    body 
                                                    {
                                                        background-color: #121212 !important;
                                                        color: #ffffff !important;
                                                    }
                                                    .card {
                                                        border-radius: 16px !important;
                                                    }
                                                    */" style="height: 200px"></textarea>
                                                <label for="custom_css">Custom CSS</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>               
                <div class="modal-footer bg-light position-sticky bottom-0 z-3">
                    <div class="w-100 d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small me-3 d-none d-md-inline">Required fields marked with <span class="text-danger">*</span></span>
                        </div>
                        <div>
                            <button type="button" class="btn btn-light me-2" data-bs-dismiss="modal">
                                <i class="fas fa-times me-1"></i> Cancel
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <span id="submitBtnText">Save Project</span>
                                <span id="submitBtnSpinner" class="spinner-border spinner-border-sm d-none ms-1" role="status" aria-hidden="true"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>