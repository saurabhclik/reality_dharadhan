<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Submit Lead | Pro-leadexpertz</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Pro-leadexpertz" name="description">
    <link rel="shortcut icon" href="{{ asset('images/favicon.ico') }}">
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/icons.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/app.min.css') }}" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <style>
        .ultra-loader 
        {
            position: relative;
            width: 80px;
            height: 80px;
        }

        .glow-ring 
        {
            width: 100%;
            height: 100%;
            border: 6px solid transparent;
            border-top: 6px solid #CF5D3B;
            border-radius: 50%;
            animation: spin 1.2s linear infinite;
            box-shadow: 0 0 25px rgba(0, 212, 255, 0.6);
        }

        @keyframes spin 
        {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        /* ... (rest of your CSS styles) ... */
    </style>
</head>
<body>
    <body data-sidebar="dark">
        <div id="layout-wrapper">
            <header id="page-topbar">
                <div class="navbar-header">
                    <div class="d-flex">
                        <div class="navbar-brand-box">
                            <a href="index.html" class="logo logo-dark">
                                <span class="logo-sm">
                                    <img src="" alt="Logo" height="22">
                                </span>
                                <span class="logo-lg">
                                    <img src="" alt="Logo" height="17">
                                </span>
                            </a>
                            <a href="" class="logo logo-light">
                                <span class="logo-sm">
                                    <img src="{{ asset($logo) }}" alt="" class="rounded-circle" height="34" height="25" width="55">
                                </span>
                                <span class="logo-lg">
                                    <img src="{{ asset($logo) }}" alt="" class="rounded-circle" height="34" height="25" width="55">
                                </span>
                            </a>
                        </div>
                    </div>
                </div>
            </header>
            <div class="page-content container">
                <div class="row justify-content-center">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Submit Lead</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="{{ route('lead.submit-shared-form', ['token' => $token]) }}" class="needs-validation" novalidate>
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label for="type">Type</label>
                                            <select class="form-select select2" name="type" id="type" required>
                                                <option value="">-- Select Type --</option>
                                                @foreach($categoryList as $type)
                                                    <option value="{{ $type->name }}" 
                                                        {{ isset($lead) && $lead->type == $type->name ? 'selected' : '' }}>
                                                        {{ $type->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback">Please select a type</div>
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label for="category" class="form-label">Category</label>
                                            <select class="form-control select2" name="category" id="category" required>
                                                <option value="">-- Select Category --</option>
                                            </select>
                                            <div class="invalid-feedback">Please select a category</div>
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label for="sub_category" class="form-label">Sub Category</label>
                                            <select class="form-control select2" name="sub_category" id="sub_category" required>
                                                <option value="">-- Select Sub Category --</option>
                                            </select>
                                            <div class="invalid-feedback">Please select a sub category</div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label for="project" class="form-label">{{ $isLeadManagement ? 'Product' : 'Project' }}</label>
                                            <select class="form-control select2" name="projects" id="project" required>
                                                <option value="">-- Select {{ $isLeadManagement ? 'Product' : 'Project' }} --</option>
                                                @foreach($projects as $project)
                                                    <option value="{{ $project->id }}">{{ $project->project_name }}</option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback">Please select a {{ $isLeadManagement ? 'product' : 'project' }}</div>
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label for="state" class="form-label">State</label>
                                            <select class="form-control select2" name="field1" id="state" required>
                                                <option value="">-- Select State --</option>
                                                @foreach($states as $state)
                                                    <option value="{{ $state->state }}">{{ $state->state }}</option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback">Please select a state</div>
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label for="city" class="form-label">City</label>
                                            <select class="form-control select2" name="field2" id="city" required>
                                                <option value="">-- Select City --</option>
                                            </select>
                                            <div class="invalid-feedback">Please select a city</div>
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label for="address" class="form-label">Address</label>
                                            <textarea class="form-control" name="field3" id="address" rows="2"></textarea>
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label for="name" class="form-label">Name</label>
                                            <input type="text" class="form-control" name="name" id="name" required>
                                            <div class="invalid-feedback">Please enter a name</div>
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label for="email" class="form-label">Email</label>
                                            <input type="email" class="form-control" name="email" id="email">
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label for="phone" class="form-label">Phone</label>
                                            <input type="text" class="form-control" name="phone" id="phone" required>
                                            <div class="invalid-feedback">Please enter a phone number</div>
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label for="whatsapp" class="form-label">Alternative Number</label>
                                            <input type="text" class="form-control" name="whatsapp" id="whatsapp">
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label for="budget" class="form-label">Budget</label>
                                            <select class="form-control select2" name="budget" id="budget">
                                                <option value="">Select Budget</option>
                                                <option value="10L-20L">₹10 Lakh - ₹20 Lakh</option>
                                                <option value="20L-30L">₹20 Lakh - ₹30 Lakh</option>
                                                <option value="30L-40L">₹30 Lakh - ₹40 Lakh</option>
                                                <option value="40L-50L">₹40 Lakh - ₹50 Lakh</option>
                                                <option value="50L-60L">₹50 Lakh - ₹60 Lakh</option>
                                                <option value="60L-70L">₹60 Lakh - ₹70 Lakh</option>
                                                <option value="70L-80L">₹70 Lakh - ₹80 Lakh</option>
                                                <option value="80L-90L">₹80 Lakh - ₹90 Lakh</option>
                                                <option value="90L-1Cr">₹90 Lakh - ₹1 Crore</option>
                                                <option value="1Cr-1.25Cr">₹1 Crore - ₹1.25 Crore</option>
                                                <option value="1.25Cr-1.5Cr">₹1.25 Crore - ₹1.5 Crore</option>
                                                <option value="1.5Cr-1.75Cr">₹1.5 Crore - ₹1.75 Crore</option>
                                                <option value="1.75Cr-2Cr">₹1.75 Crore - ₹2 Crore</option>
                                                <option value="2Cr-2.25Cr">₹2 Crore - ₹2.25 Crore</option>
                                                <option value="2.25Cr-3Cr">₹2.25 Crore - ₹3 Crore</option>
                                                <option value="3Cr-3.5Cr">₹3 Crore - ₹3.5 Crore</option>
                                                <option value="3.5Cr-5Cr">₹3.5 Crore - ₹5 Crore</option>
                                                <option value="5Cr-10Cr">₹5 Crore - ₹10 Crore</option>
                                            </select>
                                        </div>

                                        <div class="col-12 mt-3">
                                            <button type="submit" class="btn btn-primary px-4">Submit Lead</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script>
            $(document).ready(function() 
            {
                $('.select2').select2({
                    placeholder: 'Select an option',
                    width: '100%'
                });
                $('#state').change(function() 
                {
                    var state = $(this).val();
                    if (state) 
                    {
                        $.ajax({
                            url: '{{ url("/lead/get-cities") }}/' + state,
                            type: 'GET',
                            dataType: 'json',
                            success: function(data) 
                            {
                                $('#city').empty();
                                $('#city').append('<option value="">-- Select City --</option>');
                                $.each(data, function(key, value) 
                                {
                                    $('#city').append('<option value="'+ value.District +'">'+ value.District +'</option>');
                                });
                            }
                        });
                    } 
                    else 
                    {
                        $('#city').empty();
                        $('#city').append('<option value="">-- Select City --</option>');
                    }
                });
                $('#type').change(function() 
                {
                    var type = $(this).val();
                    if (type) 
                    {
                        $.ajax({
                            url: '{{ url("/lead/get-categories") }}/' + type,
                            type: 'GET',
                            dataType: 'json',
                            success: function(data) 
                            {
                                $('#category').empty();
                                $('#category').append('<option value="">-- Select Category --</option>');
                                $.each(data, function(key, value) 
                                {
                                    $('#category').append('<option value="'+ value.id +'">'+ value.name +'</option>');
                                });
                            }
                        });
                    } 
                    else 
                    {
                        $('#category').empty();
                        $('#category').append('<option value="">-- Select Category --</option>');
                        $('#sub_category').empty();
                        $('#sub_category').append('<option value="">-- Select Sub Category --</option>');
                    }
                });
                $('#category').change(function() 
                {
                    var categoryId = $(this).val();
                    if (categoryId) 
                    {
                        $.ajax({
                            url: '{{ url("/lead/get-subcategories") }}/' + categoryId,
                            type: 'GET',
                            dataType: 'json',
                            success: function(data) 
                            {
                                $('#sub_category').empty();
                                $('#sub_category').append('<option value="">-- Select Sub Category --</option>');
                                $.each(data, function(key, value) 
                                {
                                    $('#sub_category').append('<option value="'+ value.id +'">'+ value.name +'</option>');
                                });
                            }
                        });
                    } 
                    else 
                    {
                        $('#sub_category').empty();
                        $('#sub_category').append('<option value="">-- Select Sub Category --</option>');
                    }
                });

                (function() 
                {
                    'use strict';
                    window.addEventListener('load', function() 
                    {
                        var forms = document.getElementsByClassName('needs-validation');
                        var validation = Array.prototype.filter.call(forms, function(form) 
                        {
                            form.addEventListener('submit', function(event) 
                            {
                                if (form.checkValidity() === false) 
                                {
                                    event.preventDefault();
                                    event.stopPropagation();
                                }
                                form.classList.add('was-validated');
                            }, false);
                        });
                    }, false);
                })();
            });
        </script>
    </body>
</html>