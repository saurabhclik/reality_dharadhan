<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="shortcut icon" href="{{ asset(Session::get('logo')) }}">
  <title>Custom Sale Expertz</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link href="{{ asset('mobile/css/dashboard.css') }}" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
  <style>
    .app-form-container 
    {
      max-width: 100%;
      margin: 15px auto;
      padding: 0 15px;
    }

    .form-section-card 
    {
      background-color: white;
      border-radius: var(--card-radius);
      box-shadow: 0 2px 8px var(--shadow-color);
      margin-bottom: 20px;
      padding: 20px;
      transition: var(--transition);
    }

    .section-header 
    {
      display: flex;
      align-items: center;
      margin-bottom: 20px;
      padding-bottom: 10px;
      border-bottom: 1px solid var(--border-color);
    }

    .section-icon 
    {
      font-size: 16px;
      color: var(--primary-color);
      margin-right: 10px;
    }

    .section-title 
    {
      font-size: 16px;
      font-weight: 600;
      margin: 0;
      color: var(--dark-color);
    }

    .form-group 
    {
      margin-bottom: 20px;
    }

    .form-label 
    {
      display: block;
      margin-bottom: 8px;
      font-weight: 500;
      color: var(--dark-color);
      font-size: 14px;
    }

    .form-control 
    {
      width: 100%;
      padding: 12px 15px;
      border: 1px solid var(--border-color);
      border-radius: var(--input-radius);
      font-size: 15px;
      transition: var(--transition);
      background-color: var(--light-color);
    }

    .form-control:focus 
    {
      outline: none;
      border-color: var(--primary-color);
      background-color: white;
      box-shadow: 0 0 0 3px var(--primary-light);
    }

    textarea.form-control 
    {
      min-height: 80px;
      resize: vertical;
    }

    .select2-container 
    {
      width: 100% !important;
    }

    .select2-container--default .select2-selection--single 
    {
      border: 1px solid var(--border-color);
      border-radius: var(--input-radius);
      height: auto;
      padding: 10px 15px;
      background-color: var(--light-color);
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow 
    {
      height: 100%;
      right: 10px;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered 
    {
      padding-left: 0;
      color: var(--dark-color);
    }

    .select2-container--default.select2-container--focus .select2-selection--single 
    {
      border-color: var(--primary-color);
      box-shadow: 0 0 0 3px var(--primary-light);
    }

    .select2-dropdown 
    {
      border: 1px solid var(--border-color);
      border-radius: var(--input-radius);
      box-shadow: 0 2px 8px var(--shadow-color);
    }

    .radio-group 
    {
      display: flex;
      gap: 15px;
      margin-top: 5px;
    }

    .radio-option 
    {
      display: flex;
      align-items: center;
      cursor: pointer;
    }

    .radio-input 
    {
      position: absolute;
      opacity: 0;
    }

    .radio-checkmark 
    {
      display: inline-block;
      width: 18px;
      height: 18px;
      border: 2px solid var(--border-color);
      border-radius: 50%;
      margin-right: 8px;
      position: relative;
      transition: var(--transition);
    }

    .radio-input:checked ~ .radio-checkmark 
    {
      border-color: var(--primary-color);
    }

    .radio-input:checked ~ .radio-checkmark::after 
    {
      content: '';
      position: absolute;
      width: 10px;
      height: 10px;
      background-color: var(--primary-color);
      border-radius: 50%;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
    }

    .radio-label 
    {
      font-size: 14px;
      user-select: none;
    }

    .form-row 
    {
      display: flex;
      gap: 15px;
    }

    .form-row .form-group 
    {
      flex: 1;
      margin-bottom: 0;
    }

    .form-actions 
    {
      margin-top: 30px;
      margin-bottom: 30px;
    }

    .submit-button 
    {
      width: 100%;
      padding: 15px;
      background-color: var(--primary-color);
      color: white;
      border: none;
      border-radius: var(--input-radius);
      font-size: 16px;
      font-weight: 500;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: var(--transition);
    }

    .submit-button:hover 
    {
      background-color: var(--secondary-color);
      transform: translateY(-1px);
    }

    .submit-button:active 
    {
      transform: translateY(0);
    }

    .button-icon 
    {
      margin-right: 10px;
    }

    .button-text 
    {
      display: inline-block;
    }

    .invalid-feedback 
    {
      color: var(--danger-color);
      font-size: 12px;
      margin-top: 5px;
      display: none;
    }

    .was-validated .form-control:invalid ~ .invalid-feedback,
    .was-validated .form-control:invalid ~ .select2-container ~ .invalid-feedback 
    {
      display: block;
    }

    .was-validated .form-control:invalid,
    .was-validated .select2-container--invalid .select2-selection--single 
    {
      border-color: var(--danger-color);
    }

    @media (max-width: 576px) 
    {
        .form-row 
        {
          flex-direction: column;
          gap: 20px;
        }
        
        .header-title 
        {
          font-size: 16px;
        }
        
        .section-title 
        {
          font-size: 15px;
        }
        
        .form-control, 
        .select2-container--default .select2-selection--single 
        {
          padding: 10px 12px;
        }
        
        .submit-button 
        {
          padding: 12px;
          font-size: 15px;
        }
    }

    @keyframes fadeIn 
    {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .form-section-card 
    {
        animation: fadeIn 0.3s ease-out forwards;
    }

    .form-section-card:nth-child(2) 
    {
      animation-delay: 0.1s;
    }
    .bottom-sheet-form 
    {
      position: fixed;
      bottom: -100%;
      left: 0;
      width: 100%;
      background: #fff;
      border-radius: 20px 20px 0 0;
      box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.2);
      z-index: 1050;
      transition: all 0.3s ease-in-out;
    }

    .bottom-sheet-form.show 
    {
      bottom: 0;
    }

    .sheet-header 
    {
      padding: 1rem;
      border-bottom: 1px solid rgba(0, 0, 0, 0.1);
      position: relative;
    }

    .sheet-header .handle 
    {
      width: 40px;
      height: 4px;
      background: #ccc;
      border-radius: 2px;
      margin: 0 auto 8px auto;
    }

    .btn-close 
    {
      position: absolute;
      top: 1rem;
      right: 1rem;
    }
    .sheet-header h5
    {
      font-size:1rem;
    }

    .floating-form 
    {
      position: relative;
      margin-bottom: 1.5rem;
    }

    .floating-form input 
    {
      width: 100%;
      padding: 23px 0 6px 0;
      border: none;
      border-bottom: 2px solid #ccc;
      background: transparent;
      font-size: 1rem;
      transition: border-color 0.3s;
    }

    .floating-form input:focus 
    {
      outline: none;
      border-bottom: 2px solid #CF5D3B;
    }

    .label-name 
    {
      position: absolute;
      top: 12px;
      left: 0;
      pointer-events: none;
      transition: all 0.3s ease;
    }

    .content 
    {
        color: #999;
        transition: all 0.3s ease;
        font-size: 1rem;
    }

    .floating-form input:focus + .label-name .content,
    .floating-form input:not(:placeholder-shown) + .label-name .content 
    {
      transform: translateY(-20px);
      font-size: 0.75rem;
      color: #CF5D3B;
    }
    
    .overlay 
    {
      position: fixed;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background: rgba(0,0,0,0.5);
      z-index: 1040;
      display: none;
    }
    .overlay.show 
    { 
      display: block;
    }
    #filterControls 
    {
      display: none; 
    }
    #filterControls.open 
    {
      display: block !important; 
      visibility: visible !important;
      opacity: 1 !important;
    }
    .comment-card 
    {
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
      margin-bottom: 12px;
      overflow: hidden;
      border-left: 4px solid #CF5D3B;
    }

    .comment-card.pending 
    { 
      border-left-color: #ffc107; 
    }  

    .comment-card.followup 
    { 
      border-left-color: #CF5D3B; 
    }

    .comment-card.closed 
    { 
      border-left-color: #28a745; 
    }  

    .comment-card.lost 
    { 
      border-left-color: #dc3545; 
    }   

    .comment-header 
    {
      display: flex;
      justify-content: space-between;
      align-items: center;
      background: #f8f9fa;
      padding: 10px 12px;
    }

    .comment-badge 
    {
      background: #CF5D3B;
      color: #fff;
      font-size: 0.8rem;
      padding: 4px 8px;
      border-radius: 6px;
    }

    .comment-time 
    {
      font-size: 0.75rem;
      color: #666;
      margin-left: 8px;
    }

    .comment-content 
    {
        padding: 12px;
    }

    .meta-item 
    {
      margin-bottom: 6px;
    }

    .meta-label 
    {
      font-weight: 600;
      margin-right: 4px;
    }

    .comment-footer 
    {
      border-top: 1px solid #eee;
      padding: 8px 12px;
      font-size: 0.8rem;
      color: #666;
      display: flex;
      justify-content: flex-end;
    }
    
    .comment-card.comment-content:before 
    {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 4px;
      height: 100%;
      background: linear-gradient(to bottom, #4361ee, #CF5D3B);
    }
    .notification-card 
    {
      background: #fff;
      border-radius: 10px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
      padding: 12px;
      margin-bottom: 12px;
      position: relative;
    }
    .notification-card.unread 
    {
      border-left: 4px solid #CF5D3B;
      background: #f9fbff;
    }
    .notification-header 
    {
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .notification-time 
    {
      font-size: 0.8rem;
      color: #888;
    }
    .notification-content 
    {
      margin-top: 6px;
      font-size: 0.95rem;
      line-height: 1.4;
    }
  </style>
  <body>
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
  