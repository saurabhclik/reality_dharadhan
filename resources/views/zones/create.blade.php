@extends('layouts.app')

@section('title', 'Create Zone | Pro-leadexpertz')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Create New Zone</h5>
                        </div>
                        <div class="card-body">
                            @if(session('error'))
                                <div class="alert alert-danger">
                                    {{ session('error') }}
                                </div>
                            @endif

                            @if($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form class="needs-validation" novalidate action="{{ route('zone.store') }}" method="POST">
                                @csrf
                                
                                <div class="row g-3">
                                    <!-- City/District Selection -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="city_id" class="form-label">District <span class="text-danger">*</span></label>
                                            <select class="form-control select2" id="city_id" name="city_id" required>
                                                <option value="" selected disabled>Select District</option>
                                                @foreach($cities as $city)
                                                    <option value="{{ $city->id }}" {{ old('city_id') == $city->id ? 'selected' : '' }}>
                                                        {{ $city->city_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback">Please select a district</div>
                                        </div>
                                    </div>

                                    <!-- Zone Name -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="zone_name" class="form-label">Zone Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="zone_name" name="zone_name" 
                                                   value="{{ old('zone_name') }}" required maxlength="255">
                                            <div class="invalid-feedback">Please enter zone name</div>
                                        </div>
                                    </div>

                                    <!-- Sub Area -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="sub_area" class="form-label">Sub Area</label>
                                            <input type="text" class="form-control" id="sub_area" name="sub_area" 
                                                   value="{{ old('sub_area') }}" maxlength="255">
                                            <div class="form-text">e.g., Sector, Block, Locality</div>
                                        </div>
                                    </div>

                                    <!-- Pincode -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="pincode" class="form-label">Pincode</label>
                                            <input type="text" class="form-control" id="pincode" name="pincode" 
                                                   value="{{ old('pincode') }}" maxlength="10" pattern="[0-9]{6,10}">
                                            <div class="invalid-feedback">Please enter a valid pincode</div>
                                        </div>
                                    </div>

                                    <!-- Zone Order -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="zone_order" class="form-label">Display Order</label>
                                            <input type="number" class="form-control" id="zone_order" name="zone_order" 
                                                   value="{{ old('zone_order', $maxOrder) }}" min="0">
                                            <div class="form-text">Lower number displays first</div>
                                        </div>
                                    </div>

                                    <!-- Status -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="status" class="form-label">Status</label>
                                            <div class="form-check form-switch mt-2">
                                                <input class="form-check-input" type="checkbox" id="status" name="status" value="1" {{ old('status', 1) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="status">Active</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Submit Buttons -->
                                <div class="mt-4">
                                    <button type="submit" id="SubmitZoneBtn" class="btn btn-primary px-4 py-2">
                                        <span id="ZoneSubmitText">
                                            <i class="bi bi-plus-circle me-2"></i> Create Zone
                                        </span>
                                        <span id="ZoneSubmitSpinner" class="d-none">
                                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Please wait...
                                        </span>
                                    </button>
                                    <a href="{{ route('zone.index') }}" class="btn btn-secondary px-4 py-2 ms-2">
                                        <i class="bi bi-arrow-left me-2"></i> Cancel
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            // Initialize Select2 if available
            if ($.fn.select2) {
                $('.select2').select2({
                    theme: 'bootstrap-5',
                    width: '100%'
                });
            }

            // Form submission spinner
            $('form.needs-validation').on('submit', function() {
                $('#SubmitZoneBtn').prop('disabled', true);
                $('#ZoneSubmitText').addClass('d-none');
                $('#ZoneSubmitSpinner').removeClass('d-none');
            });

            // Pincode validation
            $('#pincode').on('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
            });
        });
    </script>
@endsection