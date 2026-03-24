@extends('layouts.app')

@section('title', isset($user) ? 'Edit User' : 'Create User | Pro-leadexpertz')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            @if(session('import_errors'))
                <div class="row justify-content-center">
                    <div class="col-xl-12">
                        <div class="alert alert-danger">
                            <h5 class="alert-heading">Import Errors</h5>
                            <ul class="mb-0">
                                @foreach(session('import_errors') as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif
            <div class="row justify-content-center">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">{{ isset($user) ? 'Edit' : 'Create' }} User</h5>
                            <button type="button" class="btn btn-success btn-sm float-end" data-bs-toggle="modal" data-bs-target="#importModal">
                                <i class="fas fa-file-import"></i> Bulk Import
                            </button>
                        </div>
                        <div class="card-body">
                            @if($userLimit)
                                @php
                                    $totalUsers = $users->total();
                                    $userLimitValue = $userLimit->user_limit;
                                @endphp

                                @if($userLimitValue === 'all')
                                @else
                                    @php
                                        $userLimitInt = (int) $userLimitValue;
                                        $remaining = $userLimitInt - $totalUsers;
                                    @endphp

                                    @if($remaining > 0)
                                        <div class="alert alert-info mb-3" role="alert">
                                            👥 You can create up to <strong>{{ $userLimitInt }}</strong> users in this software.
                                            <p class="text-muted mb-0">
                                                You have <strong>{{ $remaining }}</strong> user slots remaining ({{ $totalUsers }}/{{ $userLimitInt }} used).
                                            </p>
                                        </div>
                                    @else
                                        <div class="alert alert-danger mb-3" role="alert">
                                            ❌ User limit reached! You have already created <strong>{{ $totalUsers }}</strong> users (limit: {{ $userLimitInt }}).
                                        </div>
                                    @endif
                                @endif
                            @endif
                            <form class="needs-validation" novalidate action="{{ isset($user) ? route('users.update', $user->id) : route('users.store') }}" method="POST">
                                @csrf
                                @if(isset($user))
                                    @method('PUT')
                                @endif
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Name</label>
                                            <input type="text" class="form-control" id="name" name="name" 
                                                   value="{{ old('name', $user->name ?? '') }}" required>
                                            <div class="invalid-feedback">Please enter Name</div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="email" class="form-label">Email</label>
                                            <input type="email" class="form-control" id="email" name="email" 
                                                value="{{ old('email', $user->email ?? '') }}" required>
                                            <div class="invalid-feedback">Please enter a valid email</div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="mobile" class="form-label">Mobile</label>
                                            <div class="input-group">
                                                <span class="input-group-text">+91</span>
                                                <input type="tel" class="form-control" id="mobile" name="mobile" 
                                                value="{{ old('mobile', $user->mobile ?? '') }}" required>
                                            </div>
                                            <div class="invalid-feedback">Please enter mobile number</div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="password" class="form-label">
                                                Password @if(isset($user))(Leave blank to keep current)@endif
                                            </label>
                                            <div class="input-group">
                                                <input type="password" class="form-control" id="password" name="password" {{ !isset($user) ? 'required' : '' }} value="{{ ($user->password ?? '') }}">
                                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                                    <i class="fa fa-eye"></i>
                                                </button>
                                            </div>
                                            @if(!isset($user))
                                                <div class="invalid-feedback">Please enter password</div>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="role" class="form-label">Role</label>
                                            <select class="select2" id="role" name="role" required>
                                                <option value="" selected disabled>Select Role</option>
                                                @foreach($roles ?? [] as $role)
                                                    <option value="{{ $role->role_name }}"
                                                        @if(isset($user))
                                                            {{ old('role', $user->role) == $role->role_name ? 'selected' : '' }}
                                                        @else
                                                            {{ old('role') == $role->role_name ? 'selected' : '' }}
                                                        @endif>
                                                        
                                                        {{ ucwords(str_replace('_',' ', $role->role_name)) }}

                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback">Please select role</div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="designation" class="form-label">Designation</label>
                                            <select class="select2" id="designation" name="designation" required>
                                                <option value="" selected disabled>Select designation</option>
                                                @foreach($designation ?? [] as $item)
                                                    <option value="{{ $item->id }}" 
                                                        @if(isset($user))
                                                            {{ old('designation', $user->designation_id) == $item->id ? 'selected' : '' }}
                                                        @else
                                                            {{ old('designation') == $item->id ? 'selected' : '' }}
                                                        @endif>
                                                        {{ $item->designation }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback">Please select designation</div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="manager" class="form-label">Reporting Manager</label>
                                            <select class="select2" id="manager" name="reporting_manager" required>
                                                <option value="" selected disabled>Select manager</option>
                                                @foreach($reporting_manager ?? [] as $manager)
                                                    <option value="{{ $manager->id }}" 
                                                        @if(isset($user))
                                                            {{ old('reporting_manager', $user->tm_id) == $manager->id ? 'selected' : '' }}
                                                        @else
                                                            {{ old('reporting_manager') == $manager->id ? 'selected' : '' }}
                                                        @endif>
                                                        {{ $manager->name }} ({{ $manager->role }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback">Please select manager</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="zone" class="form-label">Zone</label>
                                            <select class="select2" id="zone" name="zone_id" required>
                                                <option value="" selected disabled>Select Zone</option>
                                               @foreach($zones as $zone)
                                                    <option value="{{ $zone->id }}" 
                                                        @if(isset($user))
                                                            {{ old('zone_id', $user->zone_id) == $zone->id ? 'selected' : '' }}
                                                        @else
                                                            {{ old('zone_id') == $zone->id ? 'selected' : '' }}
                                                        @endif>
                                                        {{ $zone->zone_name }}
                                                        @if($zone->sub_area) - {{ $zone->sub_area }} @endif
                                                        @if($zone->district_name) , {{ $zone->district_name }} @endif
                                                        @if($zone->state_name) , {{ $zone->state_name }} @endif
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback">Please select zone</div>
                                        </div>
                                    </div>
                                </div>                   
                                <div class="mt-4">
                                    <button type="submit" id="SubmitUserBtn" class="btn btn-primary px-4 py-2">
                                        <span id="UserSubmitText">
                                            <i class="bi bi-{{ isset($user) ? 'save' : 'person-plus' }} me-2"></i> 
                                            {{ isset($user) ? 'Update' : 'Create' }} User
                                        </span>
                                        <span id="UserSubmitSpinner" class="d-none">
                                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Please wait...
                                        </span>
                                    </button>
                                    <a href="{{ route('users.index') }}" class="btn btn-secondary px-4 py-2 ms-2">
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
    <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('users.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="importModalLabel">Import Users</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="csv_file" class="form-label">CSV File</label>
                            <input class="form-control" type="file" id="csv_file" name="csv_file" accept=".csv" required>
                            <div class="form-text">Please upload a CSV file with columns: name, email, phone, password, role</div>
                        </div>
                        <div class="mb-3">
                            <a href="{{ asset('sample_users.csv') }}" class="btn btn-sm btn-outline-primary">
                                <i class="ri-download-line align-bottom me-1"></i> Download Sample CSV
                            </a>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="SubmitBtn">
                            <span id="SubmitText">Import</span>
                            <span id="SubmitSpinner" class="d-none">
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Please wait...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        $('form.needs-validation').on('submit', function () 
        {
            $('#SubmitUserBtn').prop('disabled', true);
            $('#UserSubmitText').addClass('d-none');
            $('#UserSubmitSpinner').removeClass('d-none');
        });
        
        $('#importModal form').on('submit', function () 
        {
            $('#SubmitBtn').prop('disabled', true);
            $('#SubmitText').addClass('d-none');
            $('#SubmitSpinner').removeClass('d-none');
        });
    </script>
@endsection