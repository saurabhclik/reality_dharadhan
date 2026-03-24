<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dharadhan Estates & Finance - Agent: {{ $agentLink->agent_name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">   
    <style>
        :root 
        {
            --primary: #f25727;
            --primary-dark: #cc5c3a;
            --primary-light: #fff0eb;
        }

        body 
        {
            display: flex;
            margin: 0;
            font-family: Arial, sans-serif;
        }

        .left 
        {
            width: 45%;
            background: url('{{ asset("images/side-banner.png") }}') no-repeat center/cover;
            color: #fff;
            padding: 40px;
            position: relative;
        }

        .left-content 
        {
            position: relative;
            z-index: 2;
        }

        .logo 
        {
            margin-bottom: 40px;
        }

        .tagline 
        {
            margin-bottom: 30px;
        }

        .call-box 
        {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 12px 20px;
            border-radius: 30px;
            border: 1px rgba(255, 255, 255, 0.15) solid;
            position: absolute;
            top: 231px;
            left: 20px;
            font-size: 12px;
        }

        .phone-icon 
        {
            background: #D85A34;
            border-radius: 50%;
            padding: 5px;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .beautiful-girl 
        {
            position: absolute;
            top: 165.86px;
            left: 52.24px;
            width: 495px;
            height: 379px;
        }

        .right 
        {
            width: 55%;
            padding: 40px;
            background: #f8f9fb;
            overflow-y: auto;
        }

        .steps 
        {
            display: flex;
            gap: 10px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }

        .step-indicator 
        {
            padding: 8px 15px;
            border-radius: 20px;
            background: #e5e7eb;
            font-size: 14px;
            cursor: default;
            flex: 1;
            text-align: center;
        }

        .step-indicator.active 
        {
            background: #FFE7E0 !important;
            color: #D85A34;
            border: 1px #D85A34 solid !important;
        }

        .step-indicator.completed 
        {
            background: #D85A34;
            color: white;
        }

        .grid 
        {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-bottom: 30px;
        }

        .service-card 
        {
            position: relative;
            border-radius: 12px;
            overflow: hidden;
            cursor: pointer;
            border: 1px solid transparent;
            transition: all 0.3s ease;
        }

        .service-card:hover 
        {
            transform: translateY(-4px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }

        .service-card.active
        {
            border-color: var(--primary);
        }

        .service-card img 
        {
            width: 100%;
            height: 140px;
            object-fit: cover;
        }

        .service-card .overlay 
        {
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(0, 0, 0, 0) 0%, rgba(0, 0, 0, 0.7) 100%);
        }

        .service-card span 
        {
            position: absolute;
            bottom: 10px;
            left: 10px;
            color: #fff;
            font-weight: bold;
            z-index: 2;
        }

        .service-card span i 
        {
            margin-right: 5px;
        }

        .service-card.active::after
        {
            content: "✓";
            position: absolute;
            top: 10px;
            right: 10px;
            background: var(--primary);
            color: #fff;
            border-radius: 50%;
            width: 25px;
            height: 25px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            z-index: 2;
        }

        .step-page 
        {
            display: none;
        }

        .step-page.active 
        {
            display: block;
            animation: fadeIn 0.5s ease;
        }

        .service-fields 
        {
            display: none;
        }

        .service-fields.active 
        {
            display: block;
        }

        @keyframes fadeIn 
        {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .form-section 
        {
            /* background: white; */
            border-radius: 12px;
            padding: 25px;
            margin: 20px 0;
            /* border: 1px solid #e9ecef; */
        }

        .form-section h5 
        {
            color: var(--primary);
            font-weight: 600;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-label 
        {
            font-weight: 500;
            color: #495057;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .form-control, .form-select 
        {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 12px 16px;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus 
        {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(242,87,39,0.1);
        }

        .radio-group 
        {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 15px;
        }

        .radio-option
        {
            flex: 1;
            min-width: 120px;
        }

        .radio-option input[type="radio"] 
        {
            display: none;
        }

        .radio-option label 
        {
            display: block;
            padding: 12px 20px;
            background: white;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .radio-option input[type="radio"]:checked + label 
        {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border-color: var(--primary);
            color: white;
        }

        .btn-nav 
        {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: #fff;
            border-radius: 10px;
            padding: 12px 35px;
            border: none;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
        }

        .btn-nav:hover 
        {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(242,87,39,0.3);
        }

        .btn-nav:disabled 
        {
            background: #ccc;
            cursor: not-allowed;
        }

        .btn-secondary 
        {
            background: #f8f9fa;
            color: #6c757d;
            border: 2px solid #dee2e6;
            border-radius: 10px;
            padding: 12px 35px;
            font-weight: 600;
            cursor: pointer;
        }

        .btn-submit 
        {
            background: #28a745;
        }

        .thank 
        {
            text-align: center;
            padding: 40px 20px;
        }

        .thank i 
        {
            font-size: 80px;
            color: var(--primary);
            margin-bottom: 20px;
        }

        .dependent-field 
        {
            margin-top: 20px;
            padding: 20px;
            background: white;
            border-radius: 10px;
            border-left: 4px solid var(--primary);
        }

        @media screen and (min-width: 1241px) 
        {
            body 
            {
                overflow-y:hidden !important;
            }
            .right 
            {
                max-height: 100vh;
            }
        }
        @media(max-width: 900px) 
        {
            body 
            {
                flex-direction: column;
            }
            .left, .right 
            {
                width: 100%;
            }
            .left 
            {
                height: 450px;
            }
            .grid 
            {
                grid-template-columns: repeat(2, 1fr);
            }
            .beautiful-girl 
            {
                width: 292px;
                height: 267px;
                top: 143.86px;
                left: 52.24px;
            }
            .call-box
            {
                top:188px;
                left:-21px;
            }
            .logo 
            {
                margin-bottom: 20px;
            }
            .form-section
            {
                padding:0px !important;
            }
        }

        @media(max-width: 500px) 
        {
            .grid 
            {
                grid-template-columns: repeat(2, 1fr);
            }
            .steps 
            {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
            }
            .step-indicator 
            {
                font-size: 11px;
                padding: 5px;
            }
            .right 
            {
                padding: 20px;
            }
        }
        .select2-selection
        {
            border: 2px solid #e9ecef !important;
            border-radius: 10px !important;
            padding: 12px 16px !important;
            transition: all 0.3s ease !important;
        }
    </style>
</head>
<body>
        @include('agent-links.layouts.header')    
            @yield('content')
        @include('agent-links.layouts.footer')