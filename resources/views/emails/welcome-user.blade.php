<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Welcome to Our Platform</title>
    <style>
        body 
        { 
            font-family: Arial, sans-serif; background-color: #f7f7f7; margin: 0; padding: 0; 
        }
        .container 
        { 
            max-width: 600px; margin: 20px auto; background: #fff; padding: 20px; border-radius: 8px; 
        }
        .header 
        { 
            background-color: #CF5D3B; color: white; padding: 15px; text-align: center; border-radius: 8px 8px 0 0; 
        }
        .content 
        { 
            padding: 20px;
        }
        .footer 
        { 
            font-size: 12px; color: #777; text-align: center; margin-top: 20px;
        }
        .btn 
        { 
            display: inline-block; background-color: #CF5D3B; color: #fff !important; padding: 10px 15px; text-decoration: none; border-radius: 5px; margin-top: 15px; 
        }
        .alert 
        { 
            color: #d32f2f; font-weight: bold; background-color: #ffebee; padding: 10px; border-radius: 5px; margin-top: 15px; 
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>🎉 Congratulations, {{ $data['name'] }}!</h2>
        </div>
        <div class="content">
            <p>Welcome to our platform! We are thrilled to have you on board.</p>

            <p>Here are your account details:</p>
            <ul>
                <li>
                    <strong>Name:</strong> {{ $data['name'] }}</li>
                <li>
                    <strong>Email:</strong> {{ $data['email'] }}</li>
                <li>
                    <strong>Mobile:</strong> {{ $data['mobile'] }}</li>
                <li>
                    <strong>Role:</strong> {{ $data['role'] }}</li>
                @if($data['designation'])
                    <li>
                        <strong>Designation:</strong> {{ $data['designation'] }}</li>
                @endif
                <li>
                    <strong>Password:</strong> {{ $data['password'] }}
                </li>
            </ul>
            <div class="alert">
                ⚠️ Keep your password secure. Do NOT share it with anyone.
            </div>
            <a href="{{ url('/') }}" class="btn">Login Now</a>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} {{ $fromName }}. All rights reserved.
        </div>
    </div>
</body>
</html>
