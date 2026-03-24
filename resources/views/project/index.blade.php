@extends('layouts.app')
@section('title', 'Projects | Pro-projectexpertz')
@section('content')
@include('modals.inv-project')

<style>
    .predefined-css-container 
    {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .predefined-css-item 
    {
        background: rgba(255,255,255,0.1);
        border: 1px solid rgba(255,255,255,0.2);
        border-radius: var(--border-radius);
        padding: 1rem;
        cursor: pointer;
        transition: var(--transition);
    }

    .predefined-css-item:hover 
    {
        background: rgba(255,255,255,0.15);
        transform: translateY(-3px);
    }

    .predefined-css-item h6 
    {
        color: var(--secondary);
        margin-bottom: 0.75rem;
    }

    .predefined-css-item pre {
        background: rgba(0,0,0,0.2);
        padding: 0.75rem;
        border-radius: 0.5rem;
        font-size: 0.75rem;
        white-space: pre-wrap;
        margin-bottom: 0;
        overflow-x: auto;
    }
    #projects-table th 
    {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        background-color: #f8f9fa;
    }
    
    #projects-table td 
    {
        vertical-align: middle;
        padding: 0.75rem;
    }

    .btn-sm 
    {
        padding: 0.35rem 0.5rem;
        font-size: 0.75rem;
        border-radius: 0.25rem;
    }
    
    .btn-rounded 
    {
        border-radius: 50%;
        width: 32px;
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0;
    }
    
    .badge 
    {
        font-size: 0.7rem;
        font-weight: 500;
        padding: 0.35em 0.65em;
    }
    
    .modal-content
    {
        border: none;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
    
    .modal-header 
    {
        padding: 1rem 1.5rem;
    }
    
    .modal-body 
    {
        padding: 1.5rem;
    }
    
    .modal-footer 
    {
        padding: 1rem 1.5rem;
        background-color: #f8f9fa;
        border-top: 1px solid #eee;
    }
    .form-floating label 
    {
        color: #6c757d;
    }
    
    .form-control:focus, .form-select:focus 
    {
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
    }
    .progress 
    {
        height: 10px;
        border-radius: 5px;
    }
    .progress-bar 
    {
        background-color: #28a745;
    }
    .theme-preview-container 
    {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
        margin-top: 1.5rem;
    }

    .theme-preview
    {
        border: 2px solid #eee;
        border-radius: 10px;
        overflow: hidden;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .theme-preview:hover 
    {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }

    .theme-preview.active 
    {
        border-color: #0d6efd;
        box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.25);
    }

    .theme-colors 
    {
        height: 80px;
        display: flex;
    }

    .theme-colors div 
    {
        flex: 1;
    }

    .theme-info 
    {
        padding: 15px;
        text-align: center;
        background: #f8f9fa;
    }

    .theme-info h6 
    {
        margin-bottom: 0.3rem;
        font-weight: 600;
    }

    .theme-info small 
    {
        color: #6c757d;
        font-size: 0.8rem;
    }

    @media (max-width: 1200px) 
    {
        .theme-preview-container 
        {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 768px) 
    {
        .modal-xxl 
        {
            max-width: 100%;
            margin: 0.5rem;
        }
        
        .theme-preview-container 
        {
            grid-template-columns: 1fr;
        }
        
        .nav-pills 
        {
            flex-direction: row;
            overflow-x: auto;
            padding: 0.5rem;
            white-space: nowrap;
        }
        
        .nav-pills .nav-link 
        {
            display: inline-flex;
            padding: 10px 15px;
            margin: 0 5px;
            border-left: none;
            border-bottom: 3px solid transparent;
        }
        
        .nav-pills .nav-link.active 
        {
            border-left: none;
            border-bottom-color: #0d6efd;
        }
    }
    .progress 
    {
        background-color: rgba(255, 255, 255, 0.2);
    }
</style>

<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0 text-capitalize">Project Details</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('dashboard') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item active">Projects</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="card-title mb-0 text-capitalize"></h4>
                            @if($userType == 'super_admin' || $userType == 'divisional_head')
                            <button type="button" class="btn btn-primary" id="addProjectBtn">
                                <i class="fas fa-plus me-2"></i>Create Project Details
                            </button>
                            @endif
                        </div>
                        <div class="table-responsive">
                            <table id="table" class="table table-hover table-bordered dt-responsive nowrap w-100">
                                <thead class="table-light">
                                    <tr>
                                        <th width="50">S.No</th>
                                        <th width="100">Type</th>
                                        <th>Property</th>
                                        <th>Description</th>
                                        <th>Location</th>
                                        <th width="80">Size</th>
                                        <th width="100">Price</th>
                                        <th width="100">Video</th>
                                        <th width="100">Brochure</th>
                                        <th width="100">Progress</th>
                                        <th width="150">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($projects as $index => $project)
                                        @php
                                            $specifications = json_decode($project->specifications, true) ?? [];
                                            $amenities = json_decode($project->amenities, true) ?? [];
                                            $galleryImages = json_decode($project->gallery_images, true) ?? [];
                                        @endphp
                                        <tr>
                                            <td class="text-center">{{ $projects->firstItem() + $index }}</td>
                                            <td>
                                                <span class="badge bg-{{ $project->type == 'Residential' ? 'success' : 'primary' }}">
                                                    {{ $project->type }}
                                                </span>
                                            </td>
                                            <td class="fw-semibold">{{ $project->name ?? 'N/A' }}</td>
                                            <td>
                                                <div class="text-truncate" style="max-width: 200px;" title="{{ $project->description }}">
                                                    {{ $project->description }}
                                                </div>
                                            </td>
                                            <td>
                                                <div class="text-truncate" style="max-width: 150px;" title="{{ $project->location }}">
                                                    {{ $project->location }}
                                                </div>
                                            </td>
                                            <td class="text-end">{{ $specifications['size'] ?? 'N/A' }}</td>
                                            <td class="text-end">{{ number_format($project->price, 2) }} {{ $project->price_unit }}</td>
                                            <td class="text-center">
                                                @if($project->video_link)
                                                    <a href="{{ $project->video_link }}" target="_blank" class="btn btn-sm btn-outline-primary btn-rounded">
                                                        <i class="fas fa-play"></i>
                                                    </a>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if($project->brochure_path)
                                                    <a href="{{ asset('storage/'.$project->brochure_path) }}" target="_blank" class="btn btn-sm btn-outline-info btn-rounded">
                                                        <i class="fas fa-file-alt"></i>
                                                    </a>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td>
                                                @php
                                                    $progressFields = [
                                                        !empty($project->logo_path),
                                                        !empty($project->cover_image_path),
                                                        !empty($project->floor_plan_path),
                                                        !empty($project->site_map_path),
                                                        !empty($project->price_list_path),
                                                        !empty($project->brochure_path),
                                                        !empty($project->video_link),
                                                        !empty($galleryImages)
                                                    ];
                                                    $completedFields = array_sum($progressFields);
                                                    $totalFields = count($progressFields);
                                                    $progressPercentage = ($completedFields / $totalFields) * 100;
                                                @endphp
                                                <div class="progress mt-1">
                                                    <div class="progress-bar" role="progressbar" style="width: {{ $progressPercentage }}%;" 
                                                        aria-valuenow="{{ $progressPercentage }}" aria-valuemin="0" aria-valuemax="100">
                                                        {{ round($progressPercentage, 1) }}%
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex gap-1 justify-content-center">
                                                    <a href="{{ route('project.public.show', $project->slug) }}" 
                                                        class="btn btn-sm btn-info" 
                                                        title="View" 
                                                        target="_blank">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    @if($userType == 'super_admin' || $userType == 'divisional_head')
                                                    <button class="btn btn-sm btn-primary edit-project" data-id="{{ $project->id }}" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger delete-project" data-id="{{ $project->id }}" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                    @endif
                                                    <a href="https://wa.me/?text=Check out this project: {{ urlencode(route('project.public.show', $project->slug)) }}" 
                                                    class="btn btn-sm btn-success" target="_blank" title="Share on WhatsApp">
                                                        <i class="fab fa-whatsapp"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if($projects->hasPages())
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div class="text-muted">
                                Showing {{ $projects->firstItem() }} to {{ $projects->lastItem() }} of {{ $projects->total() }} entries
                            </div>
                            <div>
                                {{ $projects->links('pagination::bootstrap-5') }}
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>  
</div>
<script>
    $(document).ready(function() 
    {
        const projectModal = new bootstrap.Modal(document.getElementById('projectModal'));
        const projectForm = document.getElementById('projectForm');
        const addProjectBtn = document.getElementById('addProjectBtn');

        const filePreviews = {
            'logo': $('#logoPreview'),
            'cover_image': $('#coverImagePreview'),
            'brochure': $('#brochurePreview'),
            'floor_plan': $('#floorPlanPreview'),
            'site_map': $('#siteMapPreview'),
            'price_list': $('#priceListPreview'),
            'gallery_images': $('#galleryPreviews')
        };

        addProjectBtn.addEventListener('click', function() 
        {
            resetForm();
            projectForm.action = "{{ route('project-details.store') }}";
            projectForm.method = "POST";
            document.getElementById('projectModalLabel').textContent = 'Add Project Inventory';
            $('#category_id, #sub_category_id').empty().append('<option value="" disabled selected>Select...</option>');
            // Ensure name and phone are checked and disabled for new projects
            $('#lead_field_name').prop('checked', true).prop('disabled', true);
            $('#lead_field_phone').prop('checked', true).prop('disabled', true);
            $('input[name="form_fields[]"]').not('#lead_field_name, #lead_field_phone').prop('checked', false);
            projectModal.show();
        });

        $(document).on('click', '.edit-project', function() 
        {
            const projectId = $(this).data('id');
            $('#submitBtnText').text('Loading...');
            $('#submitBtnSpinner').removeClass('d-none');

            $.get(`/project-details/${projectId}/edit`, function(response) 
            {
                if (response.success) 
                {
                    const project = response.project;
                    projectForm.action = `/project-details/${projectId}`;
                    projectForm.method = "POST";
                    $('#projectForm').append('<input type="hidden" name="_method" value="PUT">');
                    $('#id').val(projectId);
                    $('#category_type').val(project.type);
                    $('#project_name').val(project.name);
                    $('#description').val(project.description);
                    $('#short_description').val(project.short_description);
                    $('#full_address').val(project.location);
                    $('#city').val(project.city);
                    $('#state').val(project.state);
                    $('#country').val(project.country);
                    $('#pin_code').val(project.pin_code);
                    $('#longitude').val(project.longitude);
                    $('#latitude').val(project.latitude);
                    $('#price').val(project.price);
                    $('#price_unit').val(project.price_unit);
                    $('#price_display').val(project.price_display);
                    $('#video_link').val(project.video_link || '');
                    $('#contact_number_1').val(project.contact_number_1 || '');
                    $('#contact_number_2').val(project.contact_number_2 || '');
                    $('#whatsapp_number').val(project.whatsapp_number || '');
                    $('#email').val(project.email || '');
                    $('#instagram_link').val(project.instagram_link || '');
                    $('#facebook_link').val(project.facebook_link || '');
                    $('#twitter_link').val(project.twitter_link || '');
                    $('#linkedin_link').val(project.linkedin_link || '');

                    const specifications = JSON.parse(project.specifications || '{}');
                    $('#beds').val(specifications.beds || '');
                    $('#baths').val(specifications.baths || '');
                    $('#balcony').val(specifications.balcony || '');
                    $('#terrace').val(specifications.terrace || '');
                    $('#super_carpet').val(specifications.super_carpet || '');
                    $('#carpet_lobby').val(specifications.carpet_lobby || '');
                    $('#size').val(specifications.size || '');

                    // Handle amenities - separate predefined and custom
                    const amenities = JSON.parse(project.amenities || '[]');
                    const predefinedAmenities = ['Swimming Pool', 'Gym', 'Club House', 'Playground', 'Elevator', 'Parking', 'Power Backup', 'Water Supply'];
                    
                    // Reset all checkboxes
                    $('input[name="amenities[]"]').prop('checked', false);
                    $('#customAmenitiesContainer').empty();
                    
                    // Process each amenity
                    amenities.forEach(amenity => {
                        if (predefinedAmenities.includes(amenity)) {
                            // This is a predefined amenity - check the checkbox
                            $(`input[name="amenities[]"][value="${amenity}"]`).prop('checked', true);
                        } else {
                            // This is a custom amenity - add to custom container
                            const amenityId = 'amenity-' + Math.random().toString(36).substr(2, 9);
                            $('#customAmenitiesContainer').append(`
                                <div class="form-check form-check-inline" id="${amenityId}">
                                    <input class="form-check-input" type="checkbox" name="amenities[]" value="${amenity}" id="${amenityId}-input" checked>
                                    <label class="form-check-label" for="${amenityId}-input">${amenity}</label>
                                    <input type="hidden" name="custom_amenities[]" value="${amenity}">
                                    <button type="button" class="btn btn-sm btn-link text-danger p-0 ms-1 remove-amenity">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            `);
                        }
                    });

                    const nearbyLocations = JSON.parse(project.nearby_locations || '[]');
                    const nearbyContainer = $('#nearby_locations_container');
                    nearbyContainer.empty();
                    nearbyLocations.forEach((location, index) => {
                        nearbyContainer.append(`
                            <div class="row nearby-location mb-2">
                                <div class="col-md-5">
                                    <input type="text" class="form-control" name="nearby_locations[${index}][building]" value="${location.building || ''}" placeholder="Building Name">
                                </div>
                                <div class="col-md-5">
                                    <input type="text" class="form-control" name="nearby_locations[${index}][location]" value="${location.location || ''}" placeholder="Location/Distance">
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-sm btn-outline-danger remove-nearby" ${index === 0 ? 'disabled' : ''}>
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        `);
                    });

                    const styleSettings = JSON.parse(project.style_settings || '{}');
                    $('#primary_color').val(styleSettings.primary_color || '#1a1a1a');
                    $('#secondary_color').val(styleSettings.secondary_color || '#2d2d2d');
                    $('#accent_color').val(styleSettings.accent_color || '#c8a97e');
                    $('#text_color').val(styleSettings.text_color || '#f8f8f8');
                    $('#text_secondary').val(styleSettings.text_secondary || '#cccccc');
                    $('#card_radius').val(styleSettings.card_radius || '12');
                    $('#font_heading').val(styleSettings.font_heading || 'DM Serif Display');
                    $('#font_body').val(styleSettings.font_body || 'Montserrat');
                    $('#button_style').val(styleSettings.button_style || 'rounded');
                    $('#nav_style').val(styleSettings.nav_style || 'solid');
                    $('#banner_size').val(styleSettings.banner_size || 'cover');
                    $('#banner_position').val(styleSettings.banner_position || 'center');
                    $('#banner_repeat').val(styleSettings.banner_repeat || 'no-repeat');
                    $('#thumb_width').val(styleSettings.thumb_width || '300');
                    $('#thumb_height').val(styleSettings.thumb_height || '200');
                    $('#thumb_crop').val(styleSettings.thumb_crop || 'fill');
                    $('#thumb_quality').val(styleSettings.thumb_quality || '80');
                    $('#custom_css').val(styleSettings.custom_css || '');

                    // Handle form_fields
                    const formFields = JSON.parse(project.form_fields || '[]');
                    $('input[name="form_fields[]"]').prop('checked', false);
                    $('#lead_field_name').prop('checked', true).prop('disabled', true);
                    $('#lead_field_phone').prop('checked', true).prop('disabled', true);
                    formFields.forEach(field => {
                        $(`input[name="form_fields[]"][value="${field}"]`).prop('checked', true);
                    });

                    handleFilePreviews(project);
                    loadCategoriesAndSubcategories(project);

                    document.getElementById('projectModalLabel').textContent = 'Edit Project Inventory';
                    projectModal.show();
                } 
                else 
                {
                    flasher.error('Error', response.message);
                }
            }).fail(function(xhr) 
            {
                flasher.error(xhr.responseJSON?.message || 'Failed to load project details');
            }).always(function() 
            {
                $('#submitBtnText').text('Update');
                $('#submitBtnSpinner').addClass('d-none');
            });
        });

        function loadCategoriesAndSubcategories(project) 
        {
            $.get(`/project-details/get-categories/${project.type}`, function(categories) 
            {
                const categorySelect = $('#category_id').empty().append('<option value="" disabled>Select Category</option>');
                categories.forEach(category => {
                    const selected = category.id == project.category ? 'selected' : '';
                    categorySelect.append(`<option value="${category.id}" ${selected}>${category.name}</option>`);
                });

                $.get(`/project-details/get-subcategories/${project.category}`, function(subcategories) {
                    const subcategorySelect = $('#sub_category_id').empty().append('<option value="" disabled>Select Sub Category</option>');
                    subcategories.forEach(subcategory => {
                        const selected = subcategory.id == project.sub_category ? 'selected' : '';
                        subcategorySelect.append(`<option value="${subcategory.id}" ${selected}>${subcategory.name}</option>`);
                    });
                }).fail(function(xhr) {
                    flasher.error('Failed to load subcategories');
                });
            }).fail(function(xhr) {
                flasher.error('Failed to load categories');
            });
        }

        function handleFilePreviews(project) {
            const fileFields = {
                'logo': project.logo_path,
                'cover_image': project.cover_image_path,
                'brochure': project.brochure_path,
                'floor_plan': project.floor_plan_path,
                'site_map': project.site_map_path,
                'price_list': project.price_list_path
            };

            Object.entries(fileFields).forEach(([field, path]) => {
                const previewDiv = filePreviews[field];
                if (path) {
                    const fileName = path.split('/').pop();
                    const fileExt = fileName.split('.').pop().toLowerCase();
                    let previewHtml = '';

                    if (['jpg', 'jpeg', 'png', 'gif'].includes(fileExt)) {
                        previewHtml = `
                            <div class="position-relative" style="width: 100px;">
                                <img src="/storage/${path}" class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover;">
                                <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 p-1 delete-file" data-field="${field}_path" data-path="${path}">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        `;
                    } else if (fileExt === 'pdf') {
                        previewHtml = `
                            <div class="align-items-center">
                                <i class="far fa-file-pdf fa-2x text-danger me-2"></i>
                                <span>${fileName}</span>
                                <button type="button" class="btn btn-sm btn-danger mt-3 float-end delete-file" data-field="${field}_path" data-path="${path}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        `;
                    } else {
                        previewHtml = `
                            <div class="align-items-center">
                                <i class="far fa-file fa-2x text-primary me-2"></i>
                                <span>${fileName}</span>
                                <button type="button" class="btn btn-sm btn-danger mt-3 float-end delete-file" data-field="${field}_path" data-path="${path}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        `;
                    }

                    previewDiv.html(previewHtml);
                } else {
                    previewDiv.empty();
                }
            });

            const galleryImages = JSON.parse(project.gallery_images || '[]');
            const galleryPreviews = $('#galleryPreviews');
            galleryPreviews.empty();

            if (galleryImages.length > 0) {
                let imagesHtml = '<div class="d-flex flex-wrap gap-2">';
                galleryImages.forEach((imagePath, index) => {
                    imagesHtml += `
                        <div class="position-relative" style="width: 100px;">
                            <img src="/storage/${imagePath}" class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover;">
                            <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 p-1 delete-gallery-image" data-index="${index}" data-path="${imagePath}">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    `;
                });
                imagesHtml += '</div>';
                galleryPreviews.html(imagesHtml);
            } else {
                galleryPreviews.html(`
                    <div class="upload-placeholder">
                        <i class="fas fa-images fs-4"></i>
                        <span class="d-block mt-2">Upload Gallery Images</span>
                        <small class="text-muted">(5-10 images recommended)</small>
                    </div>
                `);
            }
        }

        $('#category_type').on('change', function() {
            const type = $(this).val();
            if (type) {
                $.get(`/project-details/get-categories/${type}`, function(data) {
                    $('#category_id').empty().append('<option value="" disabled selected>Select Category</option>');
                    $.each(data, function(key, category) {
                        $('#category_id').append(`<option value="${category.id}">${category.name}</option>`);
                    });
                }).fail(function(xhr) {
                    console.error('Error loading categories:', xhr);
                    flasher.error('Failed to load categories');
                });
            } else {
                $('#category_id').empty().append('<option value="" disabled selected>Select Category</option>');
                $('#sub_category_id').empty().append('<option value="" disabled selected>Select Sub Category</option>');
            }
        });

        $('#category_id').on('change', function() {
            const categoryId = $(this).val();
            if (categoryId) {
                $.get(`/project-details/get-subcategories/${categoryId}`, function(data) {
                    $('#sub_category_id').empty().append('<option value="" disabled selected>Select Sub Category</option>');
                    $.each(data, function(key, subcategory) {
                        $('#sub_category_id').append(`<option value="${subcategory.id}">${subcategory.name}</option>`);
                    });
                }).fail(function(xhr) {
                    console.error('Error loading subcategories:', xhr);
                    flasher.error('Failed to load subcategories');
                });
            } else {
                $('#sub_category_id').empty().append('<option value="" disabled selected>Select Sub Category</option>');
            }
        });

        projectForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const url = $(this).attr('action');
            const method = $(this).attr('method');
            $('#submitBtnText').text('Processing...');
            $('#submitBtnSpinner').removeClass('d-none');

            $.ajax({
                url: url,
                type: method,
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        flasher.success('Success', response.message);
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    }
                },
                error: function(xhr) {
                    const errors = xhr.responseJSON?.errors;
                    let errorMessages = '';
                    if (errors) {
                        for (const field in errors) {
                            errorMessages += errors[field][0] + '<br>';
                        }
                        flasher.error(errorMessages);
                    } else {
                        flasher.error(xhr.responseJSON?.message || 'Something went wrong!');
                    }
                },
                complete: function() {
                    $('#submitBtnText').text($('#id').val() ? 'Update' : 'Save');
                    $('#submitBtnSpinner').addClass('d-none');
                }
            });
        });

        $(document).on('click', '.delete-project', function() {
            const projectId = $(this).data('id');
            const button = $(this);

            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    button.html('<i class="fas fa-spinner fa-spin"></i>');
                    $.ajax({
                        url: `/project-details/${projectId}`,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                flasher.success('Success', response.message);
                                setTimeout(() => {
                                    location.reload();
                                }, 1500);
                            }
                        },
                        error: function(xhr) {
                            flasher.error(xhr.responseJSON?.message || 'Something went wrong!');
                        },
                        complete: function() {
                            button.html('<i class="fas fa-trash"></i>');
                        }
                    });
                }
            });
        });

        $(document).on('click', '.delete-file', function() {
            const field = $(this).data('field');
            const path = $(this).data('path');
            const projectId = $('#id').val();

            Swal.fire({
                title: 'Are you sure?',
                text: "This file will be permanently deleted!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    const button = $(this);
                    button.html('<i class="fas fa-spinner fa-spin"></i>');

                    if (projectId) {
                        $.ajax({
                            url: `/project-details/${projectId}/remove-file`,
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                field: field,
                                path: path,
                                _method: 'DELETE'
                            },
                            success: function(response) {
                                if (response.success) {
                                    button.closest('.position-relative').remove();
                                    flasher.success('Success', response.message);
                                }
                            },
                            error: function(xhr) {
                                flasher.error(xhr.responseJSON?.message || 'Failed to delete file');
                            }
                        });
                    }
                }
            });
        });

        $(document).on('click', '.delete-gallery-image', function() {
            const index = $(this).data('index');
            const path = $(this).data('path');
            const projectId = $('#id').val();
            const button = $(this);

            Swal.fire({
                title: 'Are you sure?',
                text: "This image will be permanently deleted!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    button.html('<i class="fas fa-spinner fa-spin"></i>');

                    if (projectId) {
                        $.ajax({
                            url: `/project-details/${projectId}/remove-gallery-image`,
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                index: index,
                                path: path,
                                _method: 'DELETE'
                            },
                            success: function(response) {
                                if (response.success) {
                                    button.closest('.position-relative').remove();
                                    flasher.success('Success', response.message);
                                }
                            },
                            error: function(xhr) {
                                flasher.error(xhr.responseJSON?.message || 'Failed to delete image');
                            }
                        });
                    }
                }
            });
        });

        $('input[type="file"]').on('change', function() {
            const input = this;
            const field = $(this).attr('id');
            const previewDiv = filePreviews[field];

            if (input.files && input.files[0]) {
                const file = input.files[0];
                const reader = new FileReader();

                reader.onload = function(e) {
                    let previewHtml = '';
                    if (file.type.match('image.*')) {
                        previewHtml = `
                            <div class="position-relative" style="width: 100px;">
                                <img src="${e.target.result}" class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover;">
                            </div>
                        `;
                    } else if (file.type === 'application/pdf') {
                        previewHtml = `
                            <div class="d-flex align-items-center">
                                <i class="far fa-file-pdf fa-2x text-danger me-2"></i>
                                <span>${file.name}</span>
                            </div>
                        `;
                    } else {
                        previewHtml = `
                            <div class="d-flex align-items-center">
                                <i class="far fa-file fa-2x text-primary me-2"></i>
                                <span>${file.name}</span>
                            </div>
                        `;
                    }

                    previewDiv.html(previewHtml);
                };

                reader.readAsDataURL(file);
            }
        });

        $('#gallery_images').on('change', function() {
            const files = this.files;
            const previewDiv = filePreviews['gallery_images'];

            if (files && files.length > 0) {
                previewDiv.empty();
                let imagesHtml = '<div class="d-flex flex-wrap gap-2">';

                for (let i = 0; i < Math.min(files.length, 10); i++) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imagesHtml += `
                            <div class="position-relative" style="width: 100px;">
                                <img src="${e.target.result}" class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover;">
                            </div>
                        `;
                        if (i === Math.min(files.length, 10) - 1) {
                            previewDiv.html(imagesHtml + '</div>');
                        }
                    }
                    reader.readAsDataURL(files[i]);
                }
            }
        });

        function resetForm() 
        {
            projectForm.reset();
            $('#id').val('');
            $('#logoPreview').empty();
            $('#coverImagePreview').empty();
            $('#brochurePreview').empty();
            $('#floorPlanPreview').empty();
            $('#siteMapPreview').empty();
            $('#priceListPreview').empty();
            $('#galleryPreviews').empty();
            $('#instagram_link').val('');
            $('#facebook_link').val('');
            $('#twitter_link').val('');
            $('#linkedin_link').val('');
            $('input[name="_method"]').remove();
            $('#customAmenitiesContainer').empty();
            $('input[name="amenities[]"]').prop('checked', false);
            $('#nearby_locations_container').html(`
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
            `);
            $('#primary_color').val('#1a1a1a');
            $('#secondary_color').val('#2d2d2d');
            $('#accent_color').val('#c8a97e');
            $('#text_color').val('#f8f8f8');
            $('#text_secondary').val('#cccccc');
            $('#card_radius').val('12');
            $('#font_heading').val('DM Serif Display');
            $('#font_body').val('Montserrat');
            $('#button_style').val('rounded');
            $('#nav_style').val('solid');
            $('#banner_size').val('cover');
            $('#banner_position').val('center');
            $('#banner_repeat').val('no-repeat');
            $('#thumb_width').val('300');
            $('#thumb_height').val('200');
            $('#thumb_crop').val('fill');
            $('#thumb_quality').val('80');
            $('#custom_css').val('');
            // Reset form_fields and set name and phone as checked and disabled
            $('input[name="form_fields[]"]').prop('checked', false);
            $('#lead_field_name').prop('checked', true).prop('disabled', true);
            $('#lead_field_phone').prop('checked', true).prop('disabled', true);
        }

        $('#project_name').on('input', function() 
        {
            const name = $(this).val().trim();
            const slug = name.toLowerCase()
                .replace(/[^\w\s-]/g, '')
                .replace(/[\s_-]+/g, '-')
                .replace(/^-+|-+$/g, '');
            $('#slug').val(slug);
        });

        $('#add_nearby_location').on('click', function() 
        {
            const container = $('#nearby_locations_container');
            const index = container.children().length;

            container.append(`
                <div class="row nearby-location mb-2">
                    <div class="col-md-5">
                        <input type="text" class="form-control" name="nearby_locations[${index}][building]" placeholder="Building Name">
                    </div>
                    <div class="col-md-5">
                        <input type="text" class="form-control" name="nearby_locations[${index}][location]" placeholder="Location/Distance">
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-sm btn-outline-danger remove-nearby">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            `);
            if (container.children().length > 1) {
                container.find('.remove-nearby').prop('disabled', false);
            }
        });

        $('#nearby_locations_container').on('click', '.remove-nearby', function() {
            $(this).closest('.nearby-location').remove();
            const container = $('#nearby_locations_container');
            if (container.children().length === 1) {
                container.find('.remove-nearby').prop('disabled', true);
            }
        });

        $('#addCustomAmenity').on('click', function() 
        {
            const input = $('#customAmenityInput');
            const amenity = input.val().trim();
            
            if (amenity) 
            {
                const container = $('#customAmenitiesContainer');
                const amenityId = 'amenity-' + Math.random().toString(36).substr(2, 9);

                container.append(`
                    <div class="form-check form-check-inline" id="${amenityId}">
                        <input class="form-check-input" type="checkbox" name="amenities[]" value="${amenity}" id="${amenityId}-input" checked>
                        <label class="form-check-label" for="${amenityId}-input">${amenity}</label>
                        <input type="hidden" name="custom_amenities[]" value="${amenity}">
                        <button type="button" class="btn btn-sm btn-link text-danger p-0 ms-1 remove-amenity">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                `);

                input.val('');
            }
        });

        $('#customAmenitiesContainer').on('click', '.remove-amenity', function() 
        {
            $(this).closest('.form-check').remove();
        });
    });

    function applyTheme(themeName) 
    {
        const themes = {
            classic: {
                primary_color: '#0a0a0a',
                secondary_color: '#b08d57',
                accent_color: '#3c5a66',
                text_color: '#222222',
                text_secondary: '#666666',
                font_heading: 'Cinzel',
                font_body: 'EB Garamond'
            },
            modern: {
                primary_color: '#1a1a1a',
                secondary_color: '#2d2d2d',
                accent_color: '#c8a97e',
                text_color: '#333333',
                text_secondary: '#777777',
                font_heading: 'Montserrat',
                font_body: 'Roboto'
            },
            luxury: {
                primary_color: '#121212',
                secondary_color: '#d4af37',
                accent_color: '#5d432c',
                text_color: '#f8f8f8',
                text_secondary: '#cccccc',
                font_heading: 'DM Serif Display',
                font_body: 'EB Garamond'
            },
            coastal: {
                primary_color: '#2c3e50',
                secondary_color: '#3498db',
                accent_color: '#f39c12',
                text_color: '#333333',
                text_secondary: '#666666',
                font_heading: 'Montserrat',
                font_body: 'Roboto'
            },
            earthy: {
                primary_color: '#3e2723',
                secondary_color: '#8d6e63',
                accent_color: '#a1887f',
                text_color: '#333333',
                text_secondary: '#666666',
                font_heading: 'EB Garamond',
                font_body: 'Montserrat'
            },
            monochrome: {
                primary_color: '#222222',
                secondary_color: '#555555',
                accent_color: '#999999',
                text_color: '#333333',
                text_secondary: '#666666',
                font_heading: 'Montserrat',
                font_body: 'Roboto'
            }
        };

        const theme = themes[themeName];
        document.getElementById('primary_color').value = theme.primary_color;
        document.getElementById('secondary_color').value = theme.secondary_color;
        document.getElementById('accent_color').value = theme.accent_color;
        document.getElementById('text_color').value = theme.text_color;
        document.getElementById('text_secondary').value = theme.text_secondary;
        document.getElementById('font_heading').value = theme.font_heading;
        document.getElementById('font_body').value = theme.font_body;
        document.querySelectorAll('.theme-preview').forEach(el => {
            el.classList.remove('active');
        });
        event.currentTarget.classList.add('active');

        showThemePreview(theme);
    }

    function showThemePreview(theme) 
    {
        Toastify({
            text: `${theme.font_heading} theme applied`,
            duration: 2000,
            gravity: "top",
            position: "right",
            backgroundColor: theme.secondary_color,
        }).showToast();
    }

    function applyPredefinedCss(themeNumber) 
    {
        const cssTextarea = document.getElementById('custom_css');
        
        const themes = [
            `
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
            }`,
            `
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
            }`,
            `
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
            }`,
            `
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
            }`,
            `
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
            }`
        ];
        
        cssTextarea.value = themes[themeNumber - 1];
        const toast = document.createElement('div');
        toast.className = 'position-fixed bottom-0 end-0 p-3';
        toast.style.zIndex = '9999';
        toast.innerHTML = `
            <div class="toast show" role="alert">
                <div class="toast-header bg-success text-dark">
                    <strong class="me-auto">Theme Applied</strong>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body text-light">
                    Theme ${themeNumber} has been applied. Click "Save Project" to confirm.
                </div>
            </div>
        `;
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.remove();
        }, 3000);
    }
</script>
@endsection