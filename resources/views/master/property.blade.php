@extends('layouts.app')

@section('title', session('software_type') === 'lead_management' ? 'Product Name | Pro-leadexpertz' : 'Property Master | Pro-leadexpertz')

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">
                        {{ session('software_type') === 'lead_management' ? 'Manage Product' : 'Property Master' }}
                    </h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">
                                <a href="javascript:void(0);">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item active">
                                {{ session('software_type') === 'lead_management' ? 'Products' : 'Properties' }}
                            </li>
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
                            <h4 class="card-title mb-0">
                                {{ session('software_type') === 'lead_management' ? 'Product List' : 'Property List' }}
                            </h4>
                            <button class="btn btn-primary btn-small px-4 py-1 rounded-pill fw-bold text-white shadow-lg add-property"
                                data-bs-toggle="modal"
                                data-bs-target="#Modalbox"
                                data-action="/properties/store"
                                data-type="Create"
                                data-modal="{{ session('software_type') === 'lead_management' ? 'Product' : 'Property' }}">
                                <i class="fa fa-plus"></i>
                                Add {{ session('software_type') === 'lead_management' ? 'Product' : 'Property' }}
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table id="table" class="table-hover table-bordered dt-responsive nowrap w-100">
                                <thead class="table-light">
                                    <tr>
                                        <th>S.No</th>
                                        <th>{{ session('software_type') === 'lead_management' ? 'Product Name' : 'Property Name' }}</th>           
                                        <th>Agent</th>                            
                                        @if(session('software_type') !== 'lead_management')
                                        <th>Type</th>
                                        <th>Category</th>
                                        <th>Sub Category</th>
                                        <th>Location</th>
                                        <th>Budget</th>
                                        <th>Status</th>
                                        <th>Images</th>
                                        @endif                                   
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($properties as $property)
                                        @php
                                            $isOwner = $property->user_id == $userId;
                                            $isPublic = $property->property_status == 'Active';
                                            $canView = $isOwner || $isPublic;
                                        @endphp
                                    <tr>
                                        <td>
                                            {{ $loop->iteration }}
                                            @if($isOwner)
                                            <button 
                                                class="btn btn-xs btn-soft-light edit-btn"
                                                data-id="{{ $property->id }}"
                                                data-name="{{ $property->property_name }}"
                                                @if(session('software_type') !== 'lead_management')
                                                data-property_type="{{ $property->property_type }}"
                                                data-property_category_id="{{ $property->category_id ?? '' }}"
                                                data-property_category="{{ $property->property_category ?? '' }}"
                                                data-property_sub_category="{{ $property->property_sub_category ?? '' }}"
                                                data-state="{{ $property->state ?? '' }}"
                                                data-city="{{ $property->city ?? '' }}"
                                                data-address="{{ $property->address ?? '' }}"
                                                data-budget_price="{{ $property->budget_price ?? '' }}"
                                                data-property_status="{{ $property->property_status ?? '' }}"
                                                @endif                         
                                                data-bs-toggle="modal"
                                                data-bs-target="#Modalbox"
                                                data-action="/properties"
                                                data-type="Update"
                                                data-modal="{{ session('software_type') === 'lead_management' ? 'Product' : 'Property' }}">
                                                <i class="fas fa-edit text-warning"></i>
                                            </button>
                                            @endif
                                        </td>
                                        <td>{{ $property->property_name }}</td>               
                                        <td>{{ $property->user_name }}</td>          
                                        @if(session('software_type') !== 'lead_management')
                                        <td>{{ $property->property_type ?? '-' }}</td>
                                        <td>{{ $property->property_category ?? '-' }}</td>
                                        <td>{{ $property->property_sub_category ?? '-' }}</td>
                                        <td>
                                            @if($isOwner)
                                            @if($property->city || $property->state)
                                                {{ $property->city ?? '' }} {{ $property->state ? ', '.$property->state : '' }}
                                            @else
                                                -
                                            @endif
                                            @else
                                            <span class="restricted-access"> <span class="blur-content">----</span></span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($isOwner)
                                            @if($property->budget_price)
                                                {{$property->budget_price }}
                                            @else
                                                -
                                            @endif
                                            @else
                                            <span class="restricted-access"> <span class="blur-content">----</span></span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($isOwner)
                                            @if($property->property_status)
                                                @php
                                                    $statusColors = [
                                                        'Available' => 'success',
                                                        'Hold' => 'warning',
                                                        'Sold' => 'danger'
                                                    ];
                                                    $color = $statusColors[$property->property_status] ?? 'secondary';
                                                @endphp
                                                <span class="badge bg-{{ $color }}">{{ $property->property_status }}</span>
                                            @else
                                                -
                                            @endif
                                            @else
                                            <span class="restricted-access"> <span class="blur-content">----</span></span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($isOwner)
                                            @if($property->gallery_images)
                                                @php
                                                    $images = json_decode($property->gallery_images, true); 
                                                @endphp
                                                <div class="d-flex flex-wrap gap-1">
                                                    @foreach($images as $image)
                                                        <img src="{{ url($image) }}" alt="Gallery Image" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">
                                                    @endforeach
                                                </div>
                                            @else
                                                -
                                            @endif
                                            @else
                                            <span class="restricted-access"> <span class="blur-content">----</span></span>
                                            @endif
                                        </td>
                                        @endif
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-end mt-3">
                            {!! $properties->links('pagination::bootstrap-5') !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="Modalbox" tabindex="-1" aria-labelledby="ModalboxLabel" aria-hidden="true">
            <div class="modal-dialog {{ session('software_type') !== 'lead_management' ? 'modal-xl' : 'modal-md' }}">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white py-3">
                        <h5 class="modal-title fw-bold" id="ModalboxLabel">
                            <i class="fas fa-plus-circle me-2"></i>
                            <span id="modalTitleText"></span>
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    
                    <form method="POST" id="action" action="" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="id" id="id">
                        <input type="hidden" name="_method" id="method_field" value="POST">
                        
                        <div class="modal-body p-4">
                            <div class="mb-4">
                                <label for="name" class="form-label fw-semibold" id="modal-name">
                                    {{ session('software_type') === 'lead_management' ? 'Product Name' : 'Property Name' }} <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control form-control-lg" name="name" id="name" 
                                    placeholder="{{ session('software_type') === 'lead_management' ? 'Enter product name' : 'Enter property name' }}" required>
                            </div>
                            @if(session('software_type') !== 'lead_management')
                            <div class="row g-4">
                                <div class="col-md-4">
                                    <label for="property_type" class="form-label fw-semibold">Type <span class="text-muted">(From Category)</span></label>
                                    <select class="form-select" name="property_type" id="property_type">
                                        <option value="">Select Type</option>
                                        @foreach($categoryList as $cat)
                                        <option value="{{ $cat->name }}">{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="property_category" class="form-label fw-semibold">Category</label>
                                    <select class="form-select" name="property_category" id="property_category">
                                        <option value="">Select Category</option>
                                        @foreach($invCatg as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="property_sub_category" class="form-label fw-semibold">Sub Category</label>
                                    <select class="form-select" name="property_sub_category" id="property_sub_category">
                                        <option value="">Select Sub Category</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="state" class="form-label fw-semibold">State</label>
                                    <select class="form-select" name="state" id="state">
                                        <option value="">Select State</option>
                                        @php
                                            $states = DB::table('state_district')->select('state')->distinct()->orderBy('state', 'asc')->get();
                                        @endphp
                                        @foreach($states as $state)
                                            <option value="{{ $state->state }}">{{ $state->state }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label for="city" class="form-label fw-semibold">City</label>
                                    <select class="form-select" name="city" id="city">
                                        <option value="">Select City</option>
                                    </select>
                                </div>

                                <div class="col-12">
                                    <label for="address" class="form-label fw-semibold">Address</label>
                                    <textarea class="form-control" name="address" id="address" rows="2" placeholder="Enter complete address"></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label for="budget_price" class="form-label fw-semibold">Budget/Price (₹)</label>
                                    <input type="text" class="form-control" name="budget_price" id="budget_price">
                                </div>

                                <div class="col-md-6">
                                    <label for="property_status" class="form-label fw-semibold">Status</label>
                                    <select class="form-select" name="property_status" id="property_status">
                                        <option value="Available">Available</option>
                                        <option value="Hold">Hold</option>
                                        <option value="Sold">Sold</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label for="gallery_images" class="form-label fw-semibold">Gallery Images</label>
                                    <input type="file" class="form-control" name="gallery_images[]" id="gallery_images" multiple accept="image/*">
                                    <div class="form-text text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        You can select multiple images (JPEG, PNG, JPG, GIF)
                                    </div>
                                </div>
                                <div class="col-12" id="imagePreviewContainer" style="display: none;">
                                    <label class="form-label fw-semibold">Preview</label>
                                    <div class="row g-2" id="imagePreview"></div>
                                </div>
                            </div>
                            @endif
                        </div>
                        
                        <div class="modal-footer bg-light py-3">
                            <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
                                <i class="fas fa-times me-2"></i>Cancel
                            </button>
                            <button type="submit" class="btn btn-primary px-4" id="modal-type">
                                <i class="fas fa-save me-2"></i><span></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function()
    {
        @foreach($properties as $property)
        @endforeach
        $('.add-property').click(function() 
        {
            resetForm();
            var actionUrl = '/properties/store';
            $('#method_field').val('POST');
            $('#action').attr('action', actionUrl);
            $('#modalTitleText').text('Add ' + $(this).data('modal'));
            $('#modal-type span').text('Save ' + $(this).data('modal'));
            
            @if(session('software_type') !== 'lead_management')
            $('#property_status').val('Available');
            @endif
        });

        @if(session('software_type') !== 'lead_management')
        $('.edit-btn').click(function() 
        {
            resetForm();
            var button = $(this);
            var propertyId = button.data('id');
            var actionUrl = '/properties/' + propertyId;
            $('#action').attr('action', actionUrl);
            $('#method_field').val('PUT');
            $('#id').val(propertyId);
            $('#name').val(button.data('name'));
            $('#modalTitleText').text('Update ' + button.data('modal'));
            $('#modal-type span').text('Update ' + button.data('modal'));
            $('#property_type').val(button.data('property_type'));
            var categoryId = button.data('property_category_id');
            var subCategoryName = button.data('property_sub_category');
            
            if (categoryId) 
            {
                $('#property_category').val(categoryId);
                loadSubCategories(categoryId, subCategoryName);
            }
            var selectedState = button.data('state');
            var selectedCity = button.data('city');
            
            if (selectedState) 
            {
                $('#state').val(selectedState);
                loadCities(selectedState, selectedCity);
            }
            $('#address').val(button.data('address'));
            $('#budget_price').val(button.data('budget_price'));
            $('#property_status').val(button.data('property_status'));
        });

        $('#property_category').change(function() 
        {
            var categoryId = $(this).val();
            loadSubCategories(categoryId, null);
        });
        $('#state').change(function() 
        {
            var state = $(this).val();
            if (state) 
            {
                loadCities(state, null);
            } 
            else 
            {
                $('#city').html('<option value="">Select City</option>');
            }
        });
        $('#gallery_images').change(function() 
        {
            previewImages(this);
        });
        @endif
    });

    function resetForm() 
    {
        $('#action')[0].reset();
        $('#id').val('');
        $('#method_field').val('POST');
        $('#imagePreviewContainer').hide();
        $('#imagePreview').empty();
        $('#property_sub_category').html('<option value="">Select Sub Category</option>');
        $('#city').html('<option value="">Select City</option>');
    }

    @if(session('software_type') !== 'lead_management')
        function loadCities(state, selectedCity = null)
        {
            $.ajax({
                url: '/lead/get-cities/' + encodeURIComponent(state),
                type: 'GET',
                dataType: 'json',
                success: function(data) 
                {
                    var options = '<option value="">Select City</option>';
                    $.each(data, function(key, value) {
                        var selected = (value.District == selectedCity) ? 'selected' : '';
                        options += '<option value="' + value.District + '" ' + selected + '>' + value.District + '</option>';
                    });
                    $('#city').html(options);
                },
                error: function(xhr, status, error) 
                {
                    $('#city').html('<option value="">Error loading cities</option>');
                }
            });
        }

        function loadSubCategories(categoryId, selectedValue) 
        {
            if (!categoryId) 
            {
                $('#property_sub_category').html('<option value="">Select Sub Category</option>');
                return;
            }

            $.ajax({
                url: '/lead/get-subcategories/' + categoryId,
                type: 'GET',
                dataType: 'json',
                success: function(data) 
                {
                    var options = '<option value="">Select Sub Category</option>';
                    $.each(data, function(key, value) {
                        var selected = (value.name == selectedValue) ? 'selected' : '';
                        options += '<option value="' + value.name + '" ' + selected + '>' + value.name + '</option>';
                    });
                    $('#property_sub_category').html(options);
                },
                error: function(xhr, status, error)
                {
                    $('#property_sub_category').html('<option value="">Error loading sub categories</option>');
                }
            });
        }

        function previewImages(input) 
        {
            var preview = $('#imagePreview');
            preview.empty();
            if (input.files && input.files.length > 0) 
            {
                $('#imagePreviewContainer').show();
                for (var i = 0; i < input.files.length; i++) 
                {
                    var reader = new FileReader();
                    reader.onload = function(e) 
                    {
                        preview.append(
                            '<div class="col-md-3 mb-2">' +
                                '<div class="position-relative">' +
                                    '<img src="' + e.target.result + '" class="img-fluid rounded" style="height: 100px; width: 100%; object-fit: cover; border: 1px solid #dee2e6;">' +
                                    '<span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="cursor: pointer;" onclick="removeImage(this)">×</span>' +
                                '</div>' +
                            '</div>'
                        );
                    }
                    
                    reader.readAsDataURL(input.files[i]);
                }
            } else {
                $('#imagePreviewContainer').hide();
            }
        }

        function removeImage(element) 
        {
            $(element).closest('.col-md-3').remove();
            if ($('#imagePreview').children().length === 0) 
            {
                $('#imagePreviewContainer').hide();
            }
        }
    @endif
</script>
@endsection