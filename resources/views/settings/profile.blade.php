@extends('layouts.app')

@section('title', 'User Profile | Pro-leadexpertz')
@section('content')
<div class="page-content">
    <div class="container">
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="row justify-content-center">
            <div class="col-lg-4">
                <h4 class="pb-2">Profile Information</h4>
                <span class="text-justify mb-3" style="padding-top:-3px;">Update your account's profile information and email address.</span>
            </div>
            <div class="col-lg-8 text-center pt-2">
                <div class="card py-4 mb-5 mt-md-3 bg-white rounded " style="box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.175)">
                    <form method="POST" action="{{ route('setting.update_profile') }}">
                        @csrf
                        <div class="form-group px-3">
                            <div class="row">
                                <div class="col-6">
                                    <label for="name" class="text-left pl-0">Name</label>
                                    <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
                                </div>
                                <div class="col-6">
                                    <label for="email" class="text-left pl-0">Email</label>
                                    <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
                                </div>
                                <div class="col-12 mt-4">
                                    <label for="phone" class="text-left pl-0">Phone Number</label>
                                    <input type="text" name="phone" class="form-control" value="{{ $user->mobile ?? '' }}">
                                </div>
                            </div>
                        </div>
                        <div class="form-group row mb-0 mr-4 mt-4">
                            <div class="col-md-8 offset-md-4 text-right">
                                <button type="submit" class="btn btn-primary">Save</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="border-bottom border-grey"></div>

        <div class="row justify-content-center pt-5">
            <div class="col-lg-4">
                <h4 class="pb-2">Update Password</h4>
                <span class="text-justify" style="padding-top:-3px;">Ensure your account is using a long, random password to stay secure.</span>
            </div>
            <div class="col-lg-8 text-center pt-2">
                <div class="card py-4 mb-5 mt-md-3 bg-white rounded" style="box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.175)">
                    <form method="POST" action="{{ route('setting.update_password') }}">
                        @csrf
                        <div class="form-group px-3">
                            <label for="current_password" class="col-12 text-left pl-0">Current Password</label>
                            <input type="text" name="current_password" class="col-md-8 form-control" value="{{ $user->password }}" readonly>

                            <label for="password" class="pt-3 col-12 text-left pl-0">New Password</label>
                            <input type="text" name="password" class="col-md-8 form-control" required>

                            <label for="password_confirmation" class="pt-3 col-12 text-left pl-0">Confirm Password</label>
                            <input type="text" name="password_confirmation" class="col-md-8 form-control" required>
                        </div>
                        <div class="form-group row mb-0 mr-4 mt-4">
                            <div class="col-md-8 offset-md-4 text-right">
                                <button type="submit" class="btn btn-primary">Save</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="border-bottom border-grey"></div>
    </div>
</div>
@endsection