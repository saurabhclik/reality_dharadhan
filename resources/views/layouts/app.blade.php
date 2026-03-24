
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>@yield('title', 'Dashboard | Pro-leadexpertz')</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="Pro-leadexpertz" name="description">
        <meta content="saurabh" name="author">
        <link rel="shortcut icon" href="{{ asset(Session::get('logo')) }}">
        <link href="{{ asset('css/bootstrap.min.css') }}" id="bootstrap-style" rel="stylesheet" type="text/css">
        <link href="{{ asset('css/icons.min.css') }}" rel="stylesheet" type="text/css">
        <link href="{{ asset('css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css">
        <link href="{{ asset('css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-bs4.min.css" rel="stylesheet">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-bs4.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@flasher/flasher@1.2.4/dist/flasher.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@flasher/flasher-toastr@1.2.4/dist/flasher-toastr.min.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/fixedcolumns/4.3.0/css/fixedColumns.dataTables.min.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css">
        <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
        <style>
            :root {
                --primary-color: #CF5D3B;
                --secondary-color: #f7b84b;
                --success-color: #1cbb8c;
                --dark-color: #343a40;
                --light-color: #f8f9fa;
                --gradient: linear-gradient(135deg, #CF5D3B 0%, #f7b84b 100%);
                --ultra-gradient: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            }
            .btn-primary
            {
                background-color:#CF5D3B !important;
            }
            .hero {
                color: black;
                padding: 2rem 0;
                position: relative;
                overflow: hidden;
            }
            
            .hero h2 {
                font-weight: 700;
                font-size: 2rem;
                margin-bottom: 1rem;
            }
            
            .hero p {
                font-size: 1rem;
                opacity: 0.9;
                max-width: 600px;
                margin: 0 auto 2rem;
            }
            
            .feature-card {
                border: none;
                border-radius: 20px;
                overflow: hidden;
                transition: all 0.3s ease;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
                height: 100%;
                background: white;
                position: relative;
            }
            
            .feature-card:hover {
                transform: translateY(-10px);
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
            }
            
            .feature-card {
                border-top: 5px solid var(--secondary-color);
            }
            
            .feature-card.ultra {
                border-top: 5px solid var(--primary-color);
                transform: scale(1.02);
            }
            
            .feature-card.ultra:hover {
                transform: scale(1.02) translateY(-10px);
            }
            
            .feature-icon {
                font-size: 3rem;
                margin-bottom: 1.5rem;
                background: var(--gradient);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
            }
            
            .price-tag {
                background: var(--gradient);
                color: white;
                padding: 8px 20px;
                border-radius: 50px;
                font-weight: 700;
                font-size: 1.1rem;
                display: inline-block;
            }
            
            .feature-badge {
                position: absolute;
                top: 20px;
                right: 20px;
                background: var(--success-color);
                color: white;
                padding: 8px 15px;
                border-radius: 50px;
                font-size: 0.9rem;
                font-weight: 600;
                z-index: 2;
            }
            
            .video-container {
                position: relative;
                padding-bottom: 56.25%;
                height: 0;
                overflow: hidden;
                border-radius: 15px;
                margin: 1.5rem 0;
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            }
            
            .video-container iframe {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                border: 0;
            }
            
            .feature-request-btn {
                background: transparent;
                border: 2px solid var(--primary-color);
                color: var(--primary-color);
                transition: all 0.3s ease;
                border-radius: 50px;
                padding: 10px 25px;
                font-weight: 600;
                width: 100%;
            }
            
            .feature-request-btn:hover {
                background: var(--primary-color);
                color: white;
                transform: translateY(-2px);
            }
            
            .status-badge {
                padding: 8px 15px;
                border-radius: 50px;
                font-size: 0.9rem;
                font-weight: 600;
            }
            
            .status-active {
                background: rgba(28, 187, 140, 0.2);
                color: var(--success-color);
            }
            
            .status-inactive {
                background: rgba(220, 53, 69, 0.2);
                color: #dc3545;
            }
            
            .package-card {
                border-radius: 25px;
                overflow: hidden;
                transition: all 0.3s ease;
                border: 3px solid transparent;
                background: white;
                box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            }
            
            .package-card:hover {
                transform: translateY(-15px);
                border-color: var(--primary-color);
            }
            
            .package-header {
                padding: 3rem 2rem;
                text-align: center;
                position: relative;
                overflow: hidden;
            }
            
            .package .package-header {
                background: var(--gradient);
                color: white;
            }
            
            .package-ultra .package-header {
                background: var(--ultra-gradient);
                color: white;
            }
            
            .package-price {
                font-size: 3.5rem;
                font-weight: 800;
                margin: 1.5rem 0;
            }
            
            .package-period {
                font-size: 1rem;
                opacity: 0.8;
            }
            
            .package-features {
                padding: 2.5rem;
            }
            
            .package-features ul {
                list-style: none;
                padding: 0;
            }
            
            .package-features li {
                padding: 0.8rem 0;
                border-bottom: 1px solid #eee;
                display: flex;
                align-items: center;
            }
            
            .package-features li:last-child {
                border-bottom: none;
            }
            
            .package-features li i {
                margin-right: 15px;
                color: var(--success-color);
                font-size: 1.2rem;
            }
            
            .comparison-table th {
                background: var(--gradient);
                color: white;
                padding: 1.2rem;
                font-weight: 600;
            }
            
            .comparison-table td {
                padding: 1.2rem;
                vertical-align: middle;
            }
            
            .section-title {
                text-align: center;
                margin-bottom: 3rem;
                position: relative;
            }
            
            .section-title h2 {
                font-weight: 700;
                font-size: 1.5rem;
                margin-bottom: 1rem;
                color: var(--dark-color);
            }
            
            .section-title p {
                font-size: 1rem;
                color: #6c757d;
                max-width: 700px;
                margin: 0 auto;
            }
            
            .section-title::after {
                content: '';
                display: block;
                width: 80px;
                height: 4px;
                background: var(--gradient);
                margin: 1.5rem auto;
                border-radius: 2px;
            }

            .feature-description {
                color: #6c757d;
                line-height: 1.7;
                margin-bottom: 1.5rem;
            }
            
            .feature-detail {
                padding: 1rem 0;
            }
            
            .feature-detail h5 {
                font-weight: 600;
                margin-bottom: 0.5rem;
                color: var(--dark-color);
            }
            
            .feature-detail p {
                color: #6c757d;
                margin-bottom: 1rem;
            }
            
            .feature-tabs {
                background: white;
                border-radius: 20px;
                padding: 2rem;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
                margin-bottom: 3rem;
            }
            
            .testimonial-card {
                background: white;
                border-radius: 20px;
                padding: 2rem;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
                margin: 1rem 0;
                transition: all 0.3s ease;
            }
            
            .testimonial-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 15px 35px rgba(0, 0, 0, 0.12);
            }
            
            .testimonial-text {
                font-style: italic;
                color: #6c757d;
                line-height: 1.7;
                margin-bottom: 1.5rem;
            }
            
            .testimonial-author {
                display: flex;
                align-items: center;
            }
            
            .testimonial-author img {
                width: 50px;
                height: 50px;
                border-radius: 50%;
                margin-right: 15px;
                object-fit: cover;
            }
            
            .testimonial-author h6 {
                margin-bottom: 0;
                font-weight: 600;
            }
            
            .testimonial-author p {
                margin-bottom: 0;
                color: #6c757d;
                font-size: 0.9rem;
            }
            
            .faq-item {
                margin-bottom: 1.5rem;
                border-radius: 15px;
                overflow: hidden;
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            }
            
            .faq-question {
                background: white;
                padding: 1.5rem;
                font-weight: 600;
                cursor: pointer;
                display: flex;
                justify-content: space-between;
                align-items: center;
                transition: all 0.3s ease;
            }
            
            .faq-question:hover {
                background: #f8f9fa;
            }
            
            .faq-answer {
                background: #f8f9fa;
                padding: 1.5rem;
                display: none;
                color: #6c757d;
                line-height: 1.7;
            }
            
            .faq-item.active .faq-answer {
                display: block;
            }
            
            .faq-item.active .faq-question i {
                transform: rotate(180deg);
            }
            
            @media (max-width: 768px) {
                .hero h1 {
                    font-size: 2.5rem;
                }
                
                .hero p {
                    font-size: 1rem;
                }
                
                .section-title h2 {
                    font-size: 2rem;
                }
                
                .package-card {
                    margin-bottom: 2rem;
                }
            }
            .floating-filter 
            {
                position: fixed;
                bottom: 30px;
                right: 30px;
                z-index: 9999;
                font-family: "Inter", sans-serif;
            }

            @keyframes pulse 
            {
                0% { opacity: 1; transform: scale(1); }
                50% { opacity: 0.7; transform: scale(1.05); }
                100% { opacity: 1; transform: scale(1); }
            }

            .premium-badge 
            {
                display: inline-block;
                animation: pulse 1.5s infinite;
            }

            .dataTables_filter 
            {
                position: sticky;
                top: 0;
                background: white;
                z-index: 10;
                padding: 10px 0;
            }

            .cursor-pointer 
            {
                cursor: pointer;
            }

            .btn-floating 
            {
                border-radius: 50%;
                width: 60px;
                height: 60px;
                font-size: 24px;
                display: flex;
                align-items: center;
                justify-content: center;
                background: linear-gradient(135deg, #CF5D3B, #CF5D3B);
                color: #fff;
                box-shadow: 0 6px 20px rgba(0,0,0,0.25);
                transition: all 0.3s ease;
            }
            #daily-attendance_paginate 
            {
                display:none !important;
            }
            .btn-floating:hover 
            {
                transform: scale(1.1);
                box-shadow: 0 8px 24px rgba(0,0,0,0.3);
            }

            .floating-box 
            {
                position: absolute;
                bottom: 70px;
                right: 0;
                width: 280px;
                background: #ffffff;
                border-radius: 16px;
                box-shadow: 0 16px 40px rgba(0,0,0,0.2);
                padding: 20px;
                display: none;
                flex-direction: column;
                gap: 15px;
                animation: fadeIn 0.25s ease-in-out;
                max-height: 450px;
                overflow-y: auto;
            }

            .floating-box::-webkit-scrollbar 
            {
                width: 6px;
            }

            .floating-box::-webkit-scrollbar-thumb 
            {
                border-radius: 3px;
            }

            .floating-box h6 
            {
                font-size: 14px;
                font-weight: 600;
                color: #333;
                margin-bottom: 8px;
            }

            .filter-option 
            {
                padding: 8px 16px;
                border-radius: 30px;
                text-align: center;
                cursor: pointer;
                font-size: 13px;
                font-weight: 500;
                border: 1px solid transparent;
                transition: all 0.25s ease-in-out;
                white-space: nowrap;
                color: #495057;
                background: #f1f3f5;
            }

            .filter-option:hover 
            {
                transform: translateY(-2px) scale(1.05);
                background: #e6f0ff;
                color: #CF5D3B;
            }

            .filter-option.active 
            {
                background: #CF5D3B;
                color: #fff;
                border: none;
            }

            .option-hot { background: #ff6b6b; color: #fff; }
            .option-warm { background: #ff9f43; color: #fff; }
            .option-cold { background: #1e90ff; color: #fff; }
            .option-source { background: #6c757d; color: #fff; }

            .filter-header 
            {
                font-weight: 600;
                font-size: 14px;
                color: #444;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .btn-close-white 
            {
                filter: invert(1);
            }

            @keyframes fadeIn 
            {
                from { opacity: 0; transform: translateY(10px); }
                to { opacity: 1; transform: translateY(0); }
            }

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
            .dropdown-menu
            {
                z-index:9999 !important;
            }
            .inventory-unit .card-header 
            {
                border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            }
            .inventory-size 
            {
                font-weight: 600;
                font-size: 1.1rem;
            }
            .inventory-customer 
            {
                font-size: 0.8rem;
                opacity: 0.9;
            }
            .dropdown-menu 
            {
                min-width: 10rem;
            }
            .table-responsive 
            {
                max-height: 70vh;
                overflow-y: auto;
            }
            .schedule
            {
                height:80vh;
            }
            .table-container 
            {
                position: relative;
                width: 100%;
            }
            
            .table-fixed-columns table,
            .table-scrollable-columns table
            {
                margin-bottom: 0;
                white-space: nowrap;
            }
            
            .table-fixed-columns th,
            .table-scrollable-columns th 
            {
                font-weight: 600;
                text-transform: uppercase;
                font-size: 0.8rem;
                letter-spacing: 0.5px;
                color: #6c757d;
                border-top: none;
                border-bottom: 1px solid #eff2f7;
                height: 56px; 
            }
            
            .lead-row 
            {
                transition: all 0.2s ease;
                height: 72px;
            }
            
            .lead-row:hover 
            {
                background-color: #f8f9fa;
            }
            
            .btn-soft-light 
            {
                background-color: rgba(248, 249, 250, 0.5);
                border: none;
            }
            
            .btn-soft-light:hover 
            {
                background-color: rgba(248, 249, 250, 0.8);
            }
            .btn-xs 
            {
                padding: 0.15rem 0.3rem;
                font-size: 0.7rem;
                line-height: 1.2;
                border-radius: 0.2rem;
            }
            
            .btn-soft-light 
            {
                background-color: rgba(248, 249, 250, 0.5);
                border: none;
            }
            .tooltip-inner 
            {
                font-size: 0.75rem;
                padding: 0.25rem 0.5rem;
            }
            
            .dataTables_scrollBody 
            {
                overflow-x: auto !important;
                scrollbar-width: none; 
                -ms-overflow-style: none; 
            }
            .dataTables_scrollBody::-webkit-scrollbar 
            {
                display: none;
            }
            #leadsTable 
            {
                width: 100% !important;
                table-layout: auto;
            }
            #leadsTable th, #leadsTable td 
            {
                padding: 12px 15px;
                line-height: 1.5;
                vertical-align: middle;
                min-height: 60px;
            }
            #leadsTable tr 
            {
                min-height: 60px;
            }
            .DTFC_LeftWrapper, .DTFC_RightWrapper 
            {
                background-color: #fff;
                z-index: 1;
            }
            .text-truncate 
            {
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
                max-width: 200px;
            }
            .btn-xs 
            {
                padding: 4px 8px;
                margin-right: 4px;
            }
            .text-muted 
            {
                font-size: 0.85rem;
            }
            #commentsModalBody table td, #commentsModalBody table th 
            {
                padding: 10px;
                line-height: 1.5;
            }
            .dataTables_length 
            {
                margin-bottom: 1rem;
            }

            .dataTables_length label 
            {
                display: flex;
                align-items: center;
                gap: 0.5rem;
                font-size: 0.875rem;
                color: #6c757d;
            }

            .dataTables_length select 
            {
                width: auto;
                display: inline-block;
                padding: 0.25rem 1.75rem 0.25rem 0.5rem;
                font-size: 0.875rem;
                border: 1px solid #dee2e6;
                border-radius: 0.25rem;
                background-color: #fff;
                background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
                background-repeat: no-repeat;
                background-position: right 0.5rem center;
                background-size: 16px 12px;
                appearance: none;
            }

            .dataTables_filter 
            {
                margin-bottom: 1rem;
                text-align: right;
            }

            .dataTables_filter label 
            {
                display: flex;
                align-items: center;
                gap: 0.5rem;
                font-size: 0.875rem;
                color: #6c757d;
            }

            .dataTables_filter input 
            {
                display: inline-block;
                padding: 0.25rem 0.5rem;
                font-size: 0.875rem;
                border: 1px solid #dee2e6;
                border-radius: 0.25rem;
                transition: border-color 0.15s ease-in-out;
            }

            .dataTables_filter input:focus 
            {
                border-color: #86b7fe;
                outline: 0;
                box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
            }

            @media (max-width: 767.98px) 
            {
                .dataTables_length,
                .dataTables_filter 
                {
                    text-align: left;
                    margin-bottom: 0.5rem;
                }
                
                .dataTables_wrapper .row 
                {
                    flex-direction: column;
                }
            }
            .dataTables_length select 
            {
                padding: 0.4rem 2rem 0.4rem 0.8rem;
                border-radius: 0.5rem;
                border: 1px solid #ccc;
                background: #f9f9f9 url("data:image/svg+xml,%3Csvg viewBox='0 0 10 6' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M0 0l5 6 5-6z' fill='%23666'/%3E%3C/svg%3E") no-repeat right 0.8rem center;
                background-size: 10px 6px;
                appearance: none;
                -webkit-appearance: none;
                -moz-appearance: none;
                font-weight: 500;
                min-width: 80px;
            }
            .cust-badge 
            {
                white-space: normal;
                padding: 6px 10px;
                font-size: 0.9rem;
                line-height: 1.4;
            }
            .dataTables_scroll 
            {
                overflow: auto;
            }
            .dataTables_scrollHead 
            {
                position: sticky;
                top: 0;
                z-index: 10;
                background: white;
            }
            .dataTables_scrollBody
            {
                max-height:100% !important;
            }
            #table_filter
            {
                margin:10px;
            }
            /* .dataTables_paginate 
            {
                display:none !important;
            } */
            #table th,
            #table td 
            {
                padding: 0.75rem 1rem;    
                vertical-align: middle;  
                line-height: 1.5;        
                font-size: 0.92rem;    
            }

            #table td h6,
            #table td span,
            #table td a,
            #table td small 
            {
                line-height: 1.4 !important;
            }

            .table > :not(:last-child) > :last-child > * {
                border-bottom-color: #dee2e6;
            }

            #table tbody tr
            {
                transition: background-color 0.2s ease;
            }
            #table tbody tr:hover 
            {
                background-color: #f9f9f9;
            }
            .select2-container--open 
            {
                z-index: 9999 !important;
            }
            .checklist-card {
                transition: all 0.3s ease;
                border: 1px solid #dee2e6;
                border-radius: 0.375rem;
            }
            .checklist-card:hover {
                transform: translateY(-2px);
                box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
            }
            .checklist-card .form-check-input {
                position: absolute;
                margin-top: 0.6rem;
                margin-left: 0.25rem;
            }
            .checklist-card .form-check-label {
                padding-left: 1.75rem;
                cursor: pointer;
            }
            .modal-content 
            {
                border: none;
            }
            .select2-container--default .select2-selection--single 
            {
                height: 38px;
                border: 1px solid #ced4da;
            }
            .select2-container--default .select2-selection--single .select2-selection__rendered 
            {
                line-height: 40px;
            }
            .select2-container--default .select2-selection--single .select2-selection__arrow 
            {
                height: 36px;
            }
            .select2-container .select2-selection--single 
            {
                box-sizing: border-box;
                cursor: pointer;
                display: block;
                height: 37px !important;
                user-select: none;
                -webkit-user-select: none;
            }
            .select2-container--default .select2-selection--single .select2-selection__rendered 
            {
                color: #444;
                line-height: 36px !important;
            }
            .attendance-toggle-wrapper 
            {
                display: inline-block;
                margin-left: 1rem;
            }

            .attendance-toggle-container 
            {
                position: relative;
                display: flex;
                align-items: center;
            }

            .attendance-toggle-checkbox 
            {
                position: absolute;
                opacity: 0;
                width: 0;
                height: 0;
            }

            .attendance-toggle-label 
            {
                cursor: pointer;
                display: block;
            }

            .attendance-toggle-track 
            {
                position: relative;
                display: block;
                width: 125px;
                height: 35px;
                background: linear-gradient(145deg, #f0f0f0, #ffffff);
                border-radius: 30px;
                box-shadow: 
                    0 4px 12px rgba(0, 0, 0, 0.1),
                    inset 0 1px 0 rgba(255, 255, 255, 0.5);
                transition: all 0.3s cubic-bezier(0.23, 1, 0.32, 1);
                overflow: hidden;
            }

            .attendance-toggle-checkbox:checked + .attendance-toggle-label .attendance-toggle-track 
            {
                background: linear-gradient(145deg, #CF5D3B , #a5c2fcff );
                box-shadow: 
                    0 4px 20px rgba(79, 172, 254, 0.4),
                    inset 0 1px 0 rgba(255, 255, 255, 0.2);
            }

            .attendance-toggle-handle 
            {
                position: absolute;
                top: 4px;
                left: 10px;
                width: 35px;
                height: 30px;
                background: white;
                border-radius: 50%;
                box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2), 0 4px 8px rgba(0, 0, 0, 0.1);
                transition: all 0.4s cubic-bezier(0.23, 1, 0.32, 1);
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 2;
            }

            .attendance-toggle-checkbox:checked + .attendance-toggle-label .attendance-toggle-handle 
            {
                transform: translateX(72px);
                box-shadow: 
                    0 2px 6px rgba(0, 0, 0, 0.2),
                    0 4px 12px rgba(0, 242, 254, 0.3);
            }

            .attendance-icon 
            {
                color: #555;
                font-size: 16px;
                transition: all 0.3s ease;
            }

            .attendance-toggle-checkbox:checked + .attendance-toggle-label .attendance-icon 
            {
                color: #03af2a9c;
                transform: rotate(360deg);
            }

            .attendance-status-text 
            {
                position: absolute;
                width: 100%;
                height: 100%;
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 0 16px;
                box-sizing: border-box;
                font-family: 'Segoe UI', sans-serif;
                font-weight: 600;
                font-size: 12px;
                letter-spacing: 0.5px;
                text-transform: uppercase;
                color: #888;
            }

            .status-on, .status-off 
            {
                transition: all 0.3s ease;
                opacity: 0;
                transform: scale(0.8);
            }

            .status-on 
            {
                color: white;
                text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
            }

            .attendance-toggle-checkbox:checked + .attendance-toggle-label .status-on 
            {
                opacity: 1;
                transform: scale(1);
            }

            .attendance-toggle-checkbox:not(:checked) + .attendance-toggle-label .status-off 
            {
                opacity: 1;
                transform: scale(1);
            }
            /* pi */
            .modal-xxl 
            {
                max-width: 1200px;
            }

            .bg-gradient-primary 
            {
                background: linear-gradient(135deg, #CF5D3B 0%, #b8c9e2ff 100%);
            }

            .file-upload-card
            {
                border: 1px dashed #dee2e6;
                border-radius: 0.5rem;
                transition: all 0.3s ease;
            }

            .file-upload-card:hover 
            {
                border-color: #3a7bd5;
                background-color: rgba(58, 123, 213, 0.05);
            }

            .file-upload-label 
            {
                display: block;
                cursor: pointer;
                padding: 1.5rem;
                text-align: center;
            }

            .file-upload-preview 
            {
                min-height: 150px;
                display: flex;
                align-items: center;
                justify-content: center;
                position: relative;
                overflow: hidden;
                border-radius: 0.25rem;
            }

            .upload-placeholder 
            {
                text-align: center;
                color: #6c757d;
                padding: 1rem;
            }

            .upload-placeholder i 
            {
                font-size: 2rem;
                color: #adb5bd;
            }

            .file-upload-preview img 
            {
                max-width: 100%;
                max-height: 200px;
                object-fit: contain;
            }

            .nav-pills .nav-link 
            {
                border-radius: 0;
                padding: 0.75rem 1rem;
                color: #495057;
                border-left: 3px solid transparent;
                display: flex;
                align-items: center;
            }

            .nav-pills .nav-link.active 
            {
                background-color: transparent;
                color: #3a7bd5;
                border-left-color: #3a7bd5;
                font-weight: 500;
            }

            .nav-pills .nav-link i 
            {
                width: 20px;
                text-align: center;
            }

            .bullet 
            {
                width: 12px;
                height: 12px;
                border-radius: 50%;
                display: inline-block;
            }

            .amenity-group 
            {
                background-color: #f8f9fa;
                border-radius: 0.5rem;
                padding: 1.25rem;
                height: 100%;
            }

            .form-switch .form-check-input 
            {
                width: 2.5em;
                margin-left: -0.5em;
            }

            .form-floating label:not(.d-flex) 
            {
                display: flex;
                align-items: center;
            }

            #formProgressBar 
            {
                transition: width 0.5s ease;
            }

            #galleryPreviews 
            {
                min-height: 120px;
            }

            #galleryPreviews .uploaded-image 
            {
                position: relative;
                width: 120px;
                height: 90px;
                border-radius: 0.25rem;
                overflow: hidden;
            }

            #galleryPreviews .uploaded-image img 
            {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }

            #galleryPreviews .uploaded-image .remove-image 
            {
                position: absolute;
                top: 0;
                right: 0;
                background: rgba(0,0,0,0.5);
                color: white;
                width: 24px;
                height: 24px;
                display: flex;
                align-items: center;
                justify-content: center;
                border-radius: 0 0 0 0.25rem;
                cursor: pointer;
            }

            .custom-amenity-badge 
            {
                padding: 0.35em 0.65em;
                background-color: #e9ecef;
                border-radius: 50rem;
                display: inline-flex;
                align-items: center;
            }

            .custom-amenity-badge .remove-amenity 
            {
                margin-left: 0.5em;
                cursor: pointer;
                color: #6c757d;
            }

            .custom-amenity-badge .remove-amenity:hover 
            {
                color: #dc3545;
            }
            .bg-primary
            {
                background-color: #CF5D3B !important;
            }
            #table_paginate
            {
                display:none !important;
            }
            .advertisement-modal 
            {
                border-radius: 16px;
                overflow: hidden;
                box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
                border: none;
            }
            .advertisement-modal .modal-content 
            {
                background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            }
            .advertisement-badge 
            {
                display: inline-block;
                background: linear-gradient(90deg, #ff6b6b, #ff8e53);
                color: white;
                font-size: 0.75rem;
                font-weight: 600;
                padding: 4px 12px;
                border-radius: 20px;
                margin-bottom: 16px;
                letter-spacing: 0.5px;
            }
            .advertisement-title 
            {
                font-weight: 700;
                color: #2c3e50;
                font-size: 1.5rem;
            }
            .advertisement-subtitle 
            {
                color: #7f8c8d;
                font-size: 1rem;
                margin-bottom: 1.5rem;
            }
            .advertisement-features 
            {
                background-color: white;
                border-radius: 12px;
                padding: 1.5rem;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            }
            .feature-item 
            {
                padding: 8px 0;
                display: flex;
                align-items: center;
            }
            .pricing-card 
            {
                background: linear-gradient(135deg, #3b76e1 0%, #5a8dee 100%);
                border-radius: 12px;
                color: white;
            }
            .original-price 
            {
                font-size: 0.9rem;
            }
            .current-price 
            {
                font-size: 1.5rem;
            }
            .discount-badge 
            {
                font-size: 0.75rem;
                font-weight: 600;
            }
            .advertisement-cta 
            {
                background: linear-gradient(90deg, #3b76e1, #5a8dee);
                border: none;
                border-radius: 50px;
                padding: 12px 24px;
                font-weight: 600;
                font-size: 1.1rem;
                box-shadow: 0 4px 15px rgba(59, 118, 225, 0.4);
                transition: all 0.3s ease;
            }
            .advertisement-cta:hover 
            {
                transform: translateY(-2px);
                box-shadow: 0 6px 20px rgba(59, 118, 225, 0.5);
            }
            @keyframes modalAppear 
            {
                from 
                {
                    opacity: 0;
                    transform: translateY(30px) scale(0.95);
                }
                to 
                {
                    opacity: 1;
                    transform: translateY(0) scale(1);
                }
            }

            .modal.fade .modal-dialog 
            {
                animation: modalAppear 0.4s ease-out;
            }

            .dropdown-header 
            {
                background-color: #f8f9fa;
                border-bottom: 1px solid #e9ecef;
            }

            .icon-wrapper 
            {
                width: 48px;
                height: 48px;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: all 0.2s ease;
            }

            .dropdown-icon-item:hover .icon-wrapper 
            {
                background-color: #e7f1ff !important;
                transform: translateY(-2px);
            }

            .dropdown-icon-item 
            {
                text-decoration: none;
                color: #495057;
                transition: all 0.2s ease;
                border-radius: 8px;
            }

            .dropdown-icon-item:hover 
            {
                background-color: #f8f9fa;
                color: #0d6efd;
            }

            @media (max-width: 576px) 
            {
                .header-item .d-none.d-sm-inline-block 
                {
                    display: none !important;
                }
            }
            #DataTables_Table_0_paginate, #DataTables_Table_0_info
            {
                display:none !important;
            }
            .dataTables_info
            {
                display:none !important;
            }
            .blur-content {
                filter: blur(4px);
                user-select: none;
                pointer-events: none;
                display: inline-block;
                background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
                background-size: 200% 100%;
                animation: loading 1.5s infinite;
                border-radius: 4px;
                min-width: 80px;
            }

            @keyframes loading {
                0% { background-position: 200% 0; }
                100% { background-position: -200% 0; }
            }

            .restricted-access {
                position: relative;
                cursor: not-allowed;
            }

            .restricted-badge {
                background-color: #dc3545;
                color: white;
                font-size: 0.7rem;
                padding: 2px 6px;
                border-radius: 10px;
                margin-left: 5px;
                display: inline-block;
            }

            .restricted-icon {
                color: #dc3545;
                margin-left: 5px;
                cursor: help;
            }
        </style>
    </head>
    <body data-sidebar="dark">
        <div id="page-loader" style="
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(18, 18, 18, 0.6);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            z-index: 99999;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            transition: opacity 0.4s ease;
            overflow: hidden;
        ">
            <div class="ultra-loader" style="margin-bottom: 15px;">
                <div class="glow-ring"></div>
            </div>
            <div style="color: white; font-size: 1.2rem; font-weight: 500; user-select: none;">
                Loading, please wait...
            </div>
        </div>

        <div id="layout-wrapper">
            @include('layouts.header')
            
            @include('layouts.sidebar')
            
            <div class="main-content">
                @yield('content')
            </div>
            
            @include('layouts.footer')

