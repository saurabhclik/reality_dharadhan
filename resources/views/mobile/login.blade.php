<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="{{ asset($logo ?? 'mobile/images/default-logo.png') }}">
    <title>Login | Enterprise Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="{{ asset('mobile/css/login.css') }}" rel="stylesheet">
</head>
<body style="background: linear-gradient(135deg, rgba(79, 70, 229, 0.2) 0%, rgba(67, 56, 202, 0.1) 100%);">

    <div class="flash">
        <div class="logo">
            <img src="{{ asset($logo ?? 'mobile/images/default-logo.png') }}" alt="Enterprise Portal" width="82" height="82">
        </div>
        <h4 class="text-light">Lead Management Portal</h4>
        <p class="subtitle">The ultimate business management solution for modern enterprises</p>
        <div class="loader"></div>
    </div>

    <div class="particles" id="particles"></div>

    <div class="app-login-wrapper">
        <div class="app-login-container">
            <div class="app-brand text-center">
                <div class="app-logo shadow">
                    <span class="bg-light rounded">
                        <img src="{{ asset($logo ?? 'mobile/images/default-logo.png') }}" alt="Enterprise Portal">
                    </span>
                </div>
                <h1 class="app-title">Welcome Back</h1>
                <p class="app-subtitle">Sign in to access your enterprise dashboard and management tools</p>
            </div>
            
            <form id="loginForm" action="{{ route('mobile.login') }}" method="POST">
                @csrf
                <input type="hidden" name="action" value="login">
                <input type="hidden" name="fcm_token" id="fcmToken">
                
                <div class="mb-4">
                    <label for="email" class="form-label">Corporate Email</label>
                    <input type="email" class="form-control form-control-app" name="email" id="email" 
                           placeholder="your.name@company.com" value="{{ old('email') }}" 
                           required autocomplete="email">
                </div>
                
                <div class="mb-4">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group mb-2">
                        <input type="password" class="form-control form-control-app" id="password" 
                               name="password" placeholder="••••••••" required autocomplete="current-password">
                        <span class="input-group-text" id="togglePassword">
                            <i class="fas fa-eye"></i>
                        </span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="rememberMe" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label" for="rememberMe" style="font-size: 0.8125rem;">Remember me</label>
                        </div>
                        <a href="#" data-bs-toggle="modal" data-bs-target="#resetPasswordModal" class="forgot-password-link">Forgot password?</a>
                    </div>
                </div>
                
                <div class="d-grid gap-2 mt-4">
                    <button type="submit" class="btn btn-app btn-primary" id="submitBtn">
                        <i class="fas fa-sign-in-alt me-2"></i> Sign In
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() 
        {
            const loginForm = document.getElementById('loginForm');
            const fcmInput = document.getElementById('fcmToken');
            const submitBtn = document.getElementById('submitBtn');
            if (!loginForm) 
            {
                return;
            }
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            
            if (togglePassword && passwordInput)
            {
                togglePassword.addEventListener('click', function() {
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);
                    this.querySelector('i').classList.toggle('fa-eye');
                    this.querySelector('i').classList.toggle('fa-eye-slash');
                });
            }

            loginForm.addEventListener('submit', async function(e) 
            {
                e.preventDefault();
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Please wait...';
                submitBtn.disabled = true;

                try 
                {
                    if (!fcmInput.value) 
                    {
                        const token = await getFcmToken();
                        if (token) fcmInput.value = token;
                    }
                } 
                catch (err) 
                {
                    console.warn('FCM token error, proceeding anyway:', err);
                }
                loginForm.submit();
            });
            initializeFirebase();
        });
        
        async function initializeFirebase() 
        {
            const firebaseConfig = {
                apiKey: "{{ firebase_settings('api_key') }}",
                authDomain: "{{ firebase_settings('auth_domain') }}",
                projectId: "{{ firebase_settings('project_id') }}",
                storageBucket: "{{ firebase_settings('storage_bucket') }}",
                messagingSenderId: "{{ firebase_settings('messagingSenderId') }}",
                appId: "{{ firebase_settings('app_id') }}",
                measurementId: "{{ firebase_settings('measurementId') }}"
            };
            window.firebaseConfig = firebaseConfig;
            window.firebaseVapidKey = "{{ firebase_settings('vapidKey') }}";
            await registerServiceWorker();
            if (firebaseConfig.apiKey) 
            {
                try 
                {
                    const { initializeApp } = await import("https://www.gstatic.com/firebasejs/12.9.0/firebase-app.js");
                    const { getMessaging, getToken } = await import("https://www.gstatic.com/firebasejs/12.9.0/firebase-messaging.js");
                    
                    const app = initializeApp(firebaseConfig);
                    window.firebaseApp = app;
                    window.firebaseMessaging = getMessaging(app);
                    if (window.firebaseVapidKey)
                    {
                        getToken(window.firebaseMessaging, { 
                            vapidKey: window.firebaseVapidKey 
                        }).then((token) => {
                            if (token) 
                            {
                                const fcmInput = document.getElementById('fcmToken');
                                if (fcmInput) 
                                {
                                    fcmInput.value = token;
                                }
                            } 
                            else 
                            {
                                console.log('No existing token found');
                            }
                        }).catch(err => {
                            console.log('Error getting existing token:', err.message);
                        });
                    }
                    
                } 
                catch (error) 
                {
                    console.error('Firebase initialization error:', error);
                }
            }
        }

        async function registerServiceWorker() 
        {
            if ('serviceWorker' in navigator) 
            {
                try 
                {
                    const registration = await navigator.serviceWorker.register('/firebase-messaging-sw.js', {
                        scope: '/'
                    });
                    return registration;
                    
                } 
                catch (error) 
                {
                    try 
                    {
                        const altRegistration = await navigator.serviceWorker.register('/firebase-messaging-sw.js');
                        return altRegistration;
                    } 
                    catch (altError) 
                    {
                        return null;
                    }
                }
            } 
            else 
            {
                return null;
            }
        }

        async function getFcmToken() 
        {
            if (!window.firebaseConfig || !window.firebaseConfig.apiKey) 
            {
                return null;
            }
            
            if (!window.firebaseApp) 
            {
                return null;
            }
            try 
            {
                if (!window.firebaseMessaging) 
                {
                    const { getMessaging, getToken } = await import("https://www.gstatic.com/firebasejs/12.9.0/firebase-messaging.js");
                    window.firebaseMessaging = getMessaging(window.firebaseApp);
                }
                
                const { getToken } = await import("https://www.gstatic.com/firebasejs/12.9.0/firebase-messaging.js");
                const permission = await Notification.requestPermission();
                if (permission !== 'granted') 
                {
                    return null;
                }
                
                const vapidKey = window.firebaseVapidKey || "{{ firebase_settings('vapid_key') }}";
                
                if (!vapidKey) 
                {
                    console.warn('VAPID key not found');
                    return null;
                }
                
                const token = await getToken(window.firebaseMessaging, { 
                    vapidKey: vapidKey 
                });
                
                if (token) 
                {
                    return token;
                } 
                else 
                {
                    return null;
                }
                
            } 
            catch (error) 
            {
                return null;
            }
        }
    </script>
</body>
</html>