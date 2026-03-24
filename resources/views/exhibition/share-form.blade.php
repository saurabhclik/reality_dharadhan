<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>@yield('title', 'Share Lead Form | leadmanagement')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="leadexpertz" name="description">
    <meta content="saurabh" name="author">
    <link rel="shortcut icon" href="{{ asset(Session::get('logo')) }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{ asset('css/bootstrap.min.css') }}" id="bootstrap-style" rel="stylesheet" type="text/css">
    <link href="{{ asset('css/icons.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@flasher/flasher@1.3.3/dist/flasher.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@flasher/flasher@1.3.3/dist/flasher.min.js"></script>
    <style>
        body 
        {
            background-color: #f8f9fa;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
        }
        .container 
        {
            max-width: 800px;
            margin: 40px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        h3 {
            color: #333;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #CF5D3B;
        }
        .form-label {
            font-weight: 500;
            color: #555;
        }
        .text-danger {
            color: #dc3545;
        }
        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
            padding: 10px 30px;
            font-weight: 500;
        }
        .btn-success:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }
        .phone-input-group {
            display: flex;
        }
        .phone-input-group .input-group-text {
            border-right: 0;
            background-color: #f8f9fa;
            min-width: 80px;
            justify-content: center;
            font-weight: 500;
        }
        .phone-input-group .phone-prefix-editable {
            border: 1px solid #ced4da;
            border-right: none;
            padding: 0.375rem 0.75rem;
            background-color: #f8f9fa;
            cursor: pointer;
            min-width: 80px;
            text-align: center;
        }
        .phone-input-group input {
            border-left: 0;
        }
        .hidden-input {
            display: none;
        }
        .error-message {
            color: #dc3545;
            font-size: 0.875em;
            margin-top: 0.25rem;
        }
        .success-message {
            background-color: #d1e7dd;
            color: #0f5132;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .error-message-container {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h3>Submit your details for <strong>{{ $exhibition->name }}</strong></h3>
        <p class="text-muted mb-4">{{ $exhibition->description }}</p>
        
        @if(session('success'))
        <div class="success-message">
            {{ session('success') }}
        </div>
        @endif
        
        @if(session('error'))
        <div class="error-message-container">
            {{ session('error') }}
        </div>
        @endif
        
        @if($errors->any())
        <div class="error-message-container">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        
        @php
            $countries = DB::table('countries')->orderBy('name')->get();
        @endphp
        
        <form id="shareLeadForm" action="{{ route('exhibition.share.submit', ['shareCode' => $shareCode]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="share_code" value="{{ $shareCode }}">
            <input type="hidden" name="exhibition_id" value="{{ $exhibition->id }}">
            <input type="hidden" name="phone_code" id="phone_code" value="+91">
            <input type="hidden" name="whatsapp_code" id="whatsapp_code" value="+91">

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
                    @error('name')
                    <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">Country <span class="text-danger">*</span></label>
                    <select class="form-select select2" name="country_id" id="country" required>
                        <option value="">Select Country</option>
                        @foreach($countries as $country)
                            <option value="{{ $country->id }}" 
                                data-phone-code="{{ $country->phonecode }}"
                                {{ old('country_id') == $country->id || $country->id == 101 ? 'selected' : '' }}>
                                {{ $country->name }} ({{ $country->phonecode }})
                            </option>
                        @endforeach
                    </select>
                    @error('country_id')
                    <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="phone" class="form-label">Phone <span class="text-danger">*</span></label>
                    <div class="phone-input-group">
                        <div class="phone-prefix-editable" 
                             id="phone-prefix" 
                             contenteditable="true"
                             data-default="+91">+91</div>
                        <input type="tel" name="phone" id="phone" class="form-control" 
                               value="{{ old('phone') }}" 
                               placeholder="Enter phone number" required>
                    </div>
                    @error('phone')
                    <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="whatsapp" class="form-label">WhatsApp</label>
                    <div class="phone-input-group">
                        <div class="phone-prefix-editable" 
                             id="whatsapp-prefix" 
                             contenteditable="true"
                             data-default="+91">+91</div>
                        <input type="tel" name="whatsapp" id="whatsapp" class="form-control" 
                               value="{{ old('whatsapp') }}" 
                               placeholder="Enter WhatsApp number">
                    </div>
                    @error('whatsapp')
                    <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}">
                    @error('email')
                    <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="company" class="form-label">Company</label>
                    <input type="text" name="company" id="company" class="form-control" value="{{ old('company') }}">
                    @error('company')
                    <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="website" class="form-label">Website</label>
                    <input type="text" name="website" id="website" class="form-control" value="{{ old('website') }}">
                    @error('website')
                    <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="fax" class="form-label">Fax</label>
                    <input type="text" name="fax" id="fax" class="form-control" value="{{ old('fax') }}">
                    @error('fax')
                    <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-12 mb-3">
                    <label for="operating_country" class="form-label">Operating Countries</label>
                    <select class="form-select select2" name="operating_country[]" id="operating_country" multiple>
                        @foreach($countries as $country)
                            <option value="{{ $country->name }}"
                                {{ in_array($country->name, old('operating_country', [])) ? 'selected' : '' }}>
                                {{ $country->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('operating_country')
                    <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <textarea class="form-control" name="address" id="address" rows="2">{{ old('address') }}</textarea>
                @error('address')
                <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="reminder_date" class="form-label">Reminder Date</label>
                <input type="datetime-local" class="form-control" name="reminder_date" id="reminder_date" value="{{ old('reminder_date') }}">
                @error('reminder_date')
                <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="visit_card" class="form-label">Visit Card</label>
                <input type="file" class="form-control" name="visit_card[]" id="visit_card" accept="image/*" multiple>
                @error('visit_card')
                <div class="error-message">{{ $message }}</div>
                @enderror
                @error('visit_card.*')
                <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" name="description" id="description" rows="3">{{ old('description') }}</textarea>
                @error('description')
                <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-success">
                <i class="fas fa-paper-plane me-1"></i> Submit
            </button>
        </form>
    </div>

    <script>
        $(document).ready(function() 
        {
            $('#operating_country').select2({
                placeholder: "Select operating countries",
                allowClear: true,
                width: '100%'
            });
            
            $('#country').select2({
                placeholder: "Select your country",
                allowClear: false,
                width: '100%'
            });
            function updateCountryCode() 
            {
                var selectedOption = $('#country').find('option:selected');
                var phoneCode = selectedOption.data('phone-code') || '+91';
                $('#phone-prefix').text(phoneCode);
                $('#whatsapp-prefix').text(phoneCode);
                $('#phone_code').val(phoneCode);
                $('#whatsapp_code').val(phoneCode);
            }
            updateCountryCode();
            $('#country').change(function() 
            {
                updateCountryCode();
            });
            function handlePrefixChange(prefixElement, hiddenFieldId) 
            {
                prefixElement.on('input', function() 
                {
                    var prefixValue = $(this).text().trim();
                    if (!prefixValue.startsWith('+')) 
                    {
                        prefixValue = '+' + prefixValue;
                        $(this).text(prefixValue);
                    }
                    $(hiddenFieldId).val(prefixValue);
                });
                prefixElement.on('blur', function() 
                {
                    var prefixValue = $(this).text().trim();
                    if (!prefixValue.match(/^\+\d{1,4}$/)) 
                    {
                        var defaultVal = $(this).data('default') || '+91';
                        $(this).text(defaultVal);
                        $(hiddenFieldId).val(defaultVal);
                    }
                });
            }
            handlePrefixChange($('#phone-prefix'), '#phone_code');
            handlePrefixChange($('#whatsapp-prefix'), '#whatsapp_code');
            $('#shareLeadForm').on('submit', function(e) 
            {
                var name = $('#name').val().trim();
                var country = $('#country').val();
                var phone = $('#phone').val().trim();
                var phonePrefix = $('#phone-prefix').text().trim();
                if (!name) 
                {
                    e.preventDefault();
                    alert('Please enter your name');
                    $('#name').focus();
                    return false;
                }
                
                if (!country) 
                {
                    e.preventDefault();
                    alert('Please select your country');
                    $('#country').select2('open');
                    return false;
                }
                
                if (!phone) 
                {
                    e.preventDefault();
                    alert('Please enter your phone number');
                    $('#phone').focus();
                    return false;
                }
                var phoneDigits = phone.replace(/\D/g, '');
                if (phoneDigits.length < 5) 
                {
                    e.preventDefault();
                    alert('Phone number must be at least 5 digits');
                    $('#phone').focus();
                    return false;
                }
                
                var whatsapp = $('#whatsapp').val().trim();
                if (whatsapp) 
                {
                    var whatsappDigits = whatsapp.replace(/\D/g, '');
                    if (whatsappDigits.length < 5) 
                    {
                        e.preventDefault();
                        alert('WhatsApp number must be at least 5 digits');
                        $('#whatsapp').focus();
                        return false;
                    }
                }
                return true;
            });
        });
    </script>
</body>
</html>