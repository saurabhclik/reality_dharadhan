{{-- resources/views/agent-links/thank-you.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You - Dharadhan Estates & Finance</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            min-height: 100vh;
            background: url('https://images.unsplash.com/photo-1486406146926-c627a92ad1ab') center/cover no-repeat;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            font-family: system-ui;
        }

        .logo {
            text-align: center;
            margin-bottom: 20px;
        }

        .logo h4 {
            color: #f0c14b;
            font-weight: 700;
            letter-spacing: 1px;
        }

        .wrapper {
            background: #fff;
            border-radius: 20px;
            width: 100%;
            max-width: 600px;
            padding: 35px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }

        .thank {
            text-align: center;
            padding: 30px;
        }

        .thank i {
            font-size: 80px;
            color: #5a6ff0;
            margin-bottom: 20px;
        }

        .thank h2 {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 15px;
        }

        .agent-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
        }

        .btn-home {
            background: #111;
            color: #fff;
            border-radius: 10px;
            padding: 12px 30px;
            text-decoration: none;
            display: inline-block;
        }

        .btn-home:hover {
            background: #000;
            color: #fff;
        }

        .trust {
            margin-top: 25px;
            display: flex;
            justify-content: center;
            gap: 40px;
            flex-wrap: wrap;
            font-size: 14px;
            color: #6c757d;
        }

        .trust i {
            margin-right: 6px;
            color: #4c5ce0;
        }
    </style>
</head>
<body>
    <div>
        <div class="logo">
            <h4>DHARADHAN</h4>
            <small style="color:#fff">Estates & Finance</small>
        </div>

        <div class="wrapper">
            <div class="thank">
                <i class="fa-solid fa-circle-check"></i>
                <h2>Thank You!</h2>
                <p>Your request has been submitted successfully.</p>
                
                @if($agentLink)
                <div class="agent-info">
                    <p><strong>Your assigned agent:</strong> {{ $agentLink->agent_name }}</p>
                    @if($agentLink->agent_phone)
                        <p><i class="fa-solid fa-phone"></i> {{ $agentLink->agent_phone }}</p>
                    @endif
                    @if($agentLink->agent_email)
                        <p><i class="fa-solid fa-envelope"></i> {{ $agentLink->agent_email }}</p>
                    @endif
                </div>
                @endif
                
                <p>Our expert will contact you shortly.</p>
                
                <a href="/" class="btn-home mt-3">
                    <i class="fa-solid fa-home"></i> Back to Home
                </a>
            </div>

            <div class="trust">
                <div><i class="fa-solid fa-certificate"></i> RBI Registered</div>
                <div><i class="fa-solid fa-database"></i> Data Protected</div>
                <div><i class="fa-solid fa-users"></i> Expert Guidance</div>
            </div>
        </div>
    </div>
</body>
</html>