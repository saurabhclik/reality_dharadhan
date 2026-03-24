<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Login | Pro-leadexpertz</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="Pro-leadexpertz login" name="description">
        <meta content="saurabh" name="author">
        <link rel="shortcut icon" href="{{ asset($logo) }}">
        <link href="css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css">
        <link href="{{asset('css/icons.min.css')}}" rel="stylesheet" type="text/css">
        <link href="{{asset('css/app.min.css')}}" id="app-style" rel="stylesheet" type="text/css">
    </head>
    <body>
        <div class="account-pages my-5 pt-sm-5">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-8 col-lg-6 col-xl-5">
                        <div class="card overflow-hidden">
                            <div class="bg-primary-subtle">
                                <div class="row">
                                    <div class="col-7">
                                        <div class="text-primary p-4">
                                            <h5 class="text-primary">Welcome Back !</h5>
                                            <p>Sign in to continue.</p>
                                        </div>
                                    </div>
                                    <div class="col-5 align-self-end">
                                        <img src="images/profile-img.png" alt="" class="img-fluid">
                                    </div>
                                </div>
                            </div>
                            <div class="card-body pt-0"> 
                                <div class="auth-logo">
                                    <a href="" class="auth-logo-light">
                                        <div class="avatar-md profile-user-wid mb-4">
                                            <span class="avatar-title rounded-circle bg-light">
                                                <img src="{{ asset($logo) }}" alt="" class="rounded-circle" height="34" height="25" width="55">
                                            </span>
                                        </div>
                                    </a>

                                    <a href="" class="auth-logo-dark">
                                        <div class="avatar-md profile-user-wid mb-4">
                                            <span class="avatar-title rounded-circle bg-light">
                                                <img src="{{ asset($logo) }}" alt="" class="rounded-circle" height="25" width="55">
                                            </span>
                                        </div>
                                    </a>
                                </div>
                                <div class="p-2">
                                    <form class="form-horizontal" action="{{route('login')}}" method="POST">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="email" class="form-label">Email</label>
                                            <input type="email" class="form-control" id="email" placeholder="Enter email address" name="email" value="{{old('email')}}" required>
                                        </div>
                
                                        <div class="mb-3">
                                            <label class="form-label">Password</label>
                                            <div class="input-group auth-pass-inputgroup">
                                                <input type="password" class="form-control" placeholder="Enter password" aria-label="Password" aria-describedby="password-addon" name="password" value="" required>
                                                <button class="btn btn-light " type="button" id="password-addon"><i class="mdi mdi-eye-outline"></i></button>
                                            </div>
                                        </div>

                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="remember-check">
                                            <label class="form-check-label" for="remember-check">
                                                Remember me
                                            </label>
                                        </div>                        
                                        <div class="mt-3 d-grid">
                                            <button type="submit" class="btn btn-primary" id="SubmitBtn">
                                                <span id="SubmitText">login</span>
                                                <span id="SubmitSpinner" class="d-none">
                                                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Please wait...
                                                </span>
                                            </button>
                                        </div>
                                        <div class="mt-4 text-center">
                                            <a data-bs-toggle="modal" data-bs-target="#staticBackdrop" class="text-muted" style="cursor:pointer;">
                                                <i class="mdi mdi-lock me-1"></i> Forgot your password?
                                            </a>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('modals.forgot-password')
        <script src="{{asset('js/jquery.min.js')}}"></script>
        <script src="{{asset('js/bootstrap.bundle.min.js')}}"></script>
        <script src="{{asset('js/metisMenu.min.js')}}"></script>
        <script src="{{asset('js/simplebar.min.js')}}"></script>
        <script src="{{asset('js/waves.min.js')}}"></script>
        <script src="{{asset('js/app.js')}}"></script>
        <script>
            document.addEventListener("DOMContentLoaded", function () 
            {
                const submitBtn = document.getElementById('SubmitBtn');
                const form = document.querySelector('form');
                const warning = document.createElement('div');
                warning.id = 'location-warning';
                warning.className = 'text-danger mt-2 alert alert-warning';
                warning.style.display = 'none';
                submitBtn.closest('.mt-3').appendChild(warning);
                if (navigator.geolocation) 
                {
                    navigator.geolocation.getCurrentPosition(
                        function (position) 
                        {
                            const latInput = document.createElement('input');
                            const lngInput = document.createElement('input');

                            latInput.type = 'hidden';
                            lngInput.type = 'hidden';
                            latInput.name = 'latitude';
                            lngInput.name = 'longitude';
                            latInput.value = position.coords.latitude;
                            lngInput.value = position.coords.longitude;

                            form.appendChild(latInput);
                            form.appendChild(lngInput);
                        },
                        function (error) 
                        {
                            warning.textContent = 'Location permission denied. You can still log in.';
                            warning.style.display = 'block';
                        }
                    );
                } 
                else 
                {
                    warning.textContent = 'Geolocation is not supported by your browser.';
                    warning.style.display = 'block';
                }

                $('#SubmitBtn').closest('form').on('submit', function () 
                {
                    $('#SubmitBtn').prop('disabled', true);
                    $('#SubmitText').addClass('d-none');
                    $('#SubmitSpinner').removeClass('d-none');
                });
            });
            const form = document.getElementById('resetPasswordForm');
            const btn = document.getElementById('resetBtn');
            const btnText = document.getElementById('btnText');
            const btnLoader = document.getElementById('btnLoader');

            form.addEventListener('submit', function() 
            {
                btn.disabled = true;
                btnLoader.classList.remove('d-none');
                btnText.textContent = 'Sending...';
            });
        </script>
    </body>
</html>