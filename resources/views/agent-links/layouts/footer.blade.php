
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script>
    (function loadSelect2() 
    {
        var script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js';
        script.onload = function() 
        {
            document.dispatchEvent(new Event('select2-loaded'));
        };
        script.onerror = function() 
        {
            var fallbackScript = document.createElement('script');
            fallbackScript.src = 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/js/select2.min.js';
            fallbackScript.onload = function() 
            {
                document.dispatchEvent(new Event('select2-loaded'));
            };
            fallbackScript.onerror = function() 
            {
                document.dispatchEvent(new Event('select2-failed'));
            };
            document.head.appendChild(fallbackScript);
        };
        document.head.appendChild(script);
    })();
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
    (function initialize() 
    {
        let select2Loaded = false;
        let initAttempts = 0;
        const MAX_ATTEMPTS = 50; 
        function checkAndInitialize() 
        {
            initAttempts++;
            if (typeof jQuery !== 'undefined' && typeof jQuery.fn.select2 !== 'undefined') 
            {
                select2Loaded = true;
                performInitialization();
                return true;
            }
            if (initAttempts >= MAX_ATTEMPTS) 
            {
                useFallbackMultiSelect();
                performInitialization(true);
                return false;
            }
            setTimeout(checkAndInitialize, 100);
        }
        function useFallbackMultiSelect() 
        {
            $('#locationSelect').attr({
                'size': '5',
                'multiple': 'multiple'
            }).css({
                'height': 'auto',
                'min-height': '120px',
                'width': '100%',
                'padding': '8px',
                'border': '1px solid #ced4da',
                'border-radius': '4px'
            });
        }

        function performInitialization(usingFallback = false) 
        {
            if (typeof toastr !== 'undefined') 
            {
                toastr.options = {
                    "closeButton": true,
                    "progressBar": true,
                    "positionClass": "toast-top-right"
                };
            }
            
            if (!usingFallback && typeof jQuery.fn.select2 !== 'undefined') 
            {
                try 
                {
                    $('#locationSelect').select2({
                        placeholder: "Select Location",
                        allowClear: true,
                        width: '100%'
                    });
                    $('#locationName').select2({
                        placeholder: "Select Projects",
                        allowClear: true,
                        width: '100%'
                    });
                } 
                catch (e) 
                {
                    useFallbackMultiSelect();
                }
            }
            
            loadLocation(usingFallback);
            initializeEventHandlers();
            loadStates();
            loadProjects();
        }
        checkAndInitialize();
    })();

    let currentStep = 1;
    let selectedService = 'realestate';

    function initializeEventHandlers() 
    {
        const serviceCards = document.querySelectorAll('.service-card');
        if (serviceCards.length > 0) 
        {
            serviceCards.forEach(card => {
                card.addEventListener('click', function() 
                {
                    document.querySelectorAll('.service-card').forEach(c => c.classList.remove('active'));
                    this.classList.add('active');
                    selectedService = this.dataset.service;
                    const serviceTypeInput = document.getElementById('service_type');
                    if (serviceTypeInput) 
                    {
                        serviceTypeInput.value = selectedService;
                    }
                });
            });
        }

        $(document).ready(function() 
        {
            loadCategories('Residential');
            $(document).on('change', 'input[name="property_type"]', function() 
            {
                const type = $(this).val();
                loadCategories(type);
                $('#subCategoryDiv').hide();
            });
            $(document).on('change', '#stateSelect', function() 
            {
                const state = $(this).val();
                if (state) 
                {
                    loadCities(state);
                } 
                else 
                {
                    $('#citySelect').html('<option value="">Select City</option>').prop('disabled', true);
                }
            });
            $(document).on('change', '#categorySelect', function() 
            {
                const catgId = $(this).val();
                if (catgId) 
                {
                    loadSubCategories(catgId);
                } 
                else 
                {
                    $('#subCategoryDiv').hide();
                }
            });
            $(document).on('change', '#citySelect', function() 
            {
                const cityId = $(this).val();
                if (cityId) 
                {
                    loadLocation(false, cityId);
                } 
                else 
                {
                    $('#locationSelect').html('<option value="">Select Location</option>');
                }
            });
        });
    }

    function loadLocation(usingFallback = false, cityId = null) 
    {
        let url = '/api/realestate/get-location';
        if (cityId) 
        {
            url += '?city_id=' + cityId;
        }
        
        $.ajax({
            url: url,
            type: 'GET',
            success: function(response) 
            {
                if (response.status === 200 && response.data && response.data.length > 0) 
                {
                    let options = '';
                    response.data.forEach(item => {
                        options += `<option value="${item.id}">${item.zone_name} - ${item.sub_area} (${item.pincode})</option>`;
                    });
                    
                    $('#locationSelect').html(options);
                    if (!usingFallback && typeof jQuery.fn.select2 !== 'undefined') 
                    {
                        try 
                        {
                            $('#locationSelect').select2({
                                placeholder: "Select Location",
                                allowClear: true,
                                width: '100%'
                            });
                        } 
                        catch (e) 
                        {
                            console.error('Failed to reinitialize Select2:', e);
                        }
                    }
                } 
                else 
                {
                    $('#locationSelect').html('<option value="">No locations available</option>');
                }
            },
            error: function(xhr, status, error) 
            {
                $('#locationSelect').html('<option value="">Error loading locations</option>');
            }
        });
    }

    function goToStep2() 
    {
        document.querySelectorAll('.service-fields').forEach(f => f.classList.remove('active'));
        const selectedFields = document.getElementById(selectedService + '-fields');
        if (selectedFields) 
        {
            selectedFields.classList.add('active');
        }
        goToStep(2);
    }

    function goToStep3() 
    {
        if (validateRequirements()) 
        {
            goToStep(3);
        }
    }

    function goToStep(step) 
    {
        document.querySelectorAll('.step-page').forEach(page => {
            page.classList.remove('active');
        });

        const targetPage = document.getElementById(`step${step}`);
        if (targetPage) 
        {
            targetPage.classList.add('active');
        }

        for (let i = 1; i <= 4; i++) 
        {
            const indicator = document.getElementById(`step${i}-indicator`);
            if (indicator) 
            {
                if (i < step) 
                {
                    indicator.classList.add('completed');
                    indicator.classList.remove('active');
                } 
                else if (i === step) 
                {
                    indicator.classList.add('active');
                    indicator.classList.remove('completed');
                } 
                else 
                {
                    indicator.classList.remove('active', 'completed');
                }
            }
        }
        currentStep = step;
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function validateRequirements() 
    {
        let isValid = true;
        let errorMessage = '';
        
        if (selectedService === 'realestate') 
        {
            const propertyType = document.querySelector('input[name="property_type"]:checked');
            const category = document.getElementById('categorySelect')?.value;
            
            if (!propertyType) 
            {
                isValid = false;
                errorMessage = 'Please select property type';
            } 
            else if (!category) 
            {
                isValid = false;
                errorMessage = 'Please select category';
            }
        }
        
        if (isValid) 
        {
            if (typeof toastr !== 'undefined') 
            {
                toastr.success('Moving to contact details');
            } 
            else 
            {
                alert('Moving to contact details');
            }
            return true;
        }
        else 
        {
            if (typeof toastr !== 'undefined') 
            {
                toastr.error(errorMessage || 'Please fill in all required fields');
            } 
            else 
            {
                alert(errorMessage || 'Please fill in all required fields');
            }
            return false;
        }
    }

    function submitLead() 
    {
        const name = document.getElementById('contactName')?.value;
        const phone = document.getElementById('contactPhone')?.value;
        const email = document.getElementById('contactEmail')?.value;
        const additionalMessage = document.getElementById('contactNotes')?.value;
        if (!/^\d{10}$/.test(phone)) 
        {
            if (typeof toastr !== 'undefined') 
            {
                toastr.error('Please enter a valid 10-digit phone number');
            } 
            else 
            {
                toastr.error('Please enter a valid 10-digit phone number');
            }
            return;
        }
        if (!name || !phone) 
        {
            if (typeof toastr !== 'undefined') 
            {
                toastr.error('Please fill in all required fields');
            } 
            else 
            {
                toastr.error('Please fill in all required fields');
            }
            return;
        }
        const submitBtn = event.target;
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Submitting...';
        const step2Message = document.querySelector('textarea[name="notes"]')?.value || '';
        let combinedMessage = '';
        const dateTime = new Date().toLocaleString();
        
        if (step2Message && additionalMessage) 
        {
            combinedMessage = `[${dateTime}] Requirements: ${step2Message} | Additional Info: ${additionalMessage}`;
        } 
        else if (step2Message) 
        {
            combinedMessage = `[${dateTime}] Requirements: ${step2Message}`;
        } 
        else if (additionalMessage) 
        {
            combinedMessage = `[${dateTime}] Additional Info: ${additionalMessage}`;
        } 
        else 
        {
            combinedMessage = `[${dateTime}] Lead submitted via API`;
        }
        const locationSelect = document.getElementById('locationSelect');
        const selectedLocations = locationSelect ? Array.from(locationSelect.selectedOptions).map(opt => opt.value) : [];
        const zoneIds = selectedLocations.join(',');
        const projectSelect = document.getElementById('locationName');
        const selectedProjects = projectSelect ? Array.from(projectSelect.selectedOptions).map(opt => opt.value) : [];
        const projectIds = selectedProjects.join(',');
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
        const formData = {
            service_type: selectedService,
            name: name,
            email: email || '',
            phone: phone,
            step2_message: step2Message,
            step3_message: additionalMessage,
            last_comment: combinedMessage,
            unique_id: '{{ $agentLink->unique_identifier ?? '' }}',
            property_type: document.querySelector('input[name="property_type"]:checked')?.value,
            catg_id: document.getElementById('categorySelect')?.value,
            sub_catg_id: document.getElementById('subCategorySelect')?.value,
            property_state: document.getElementById('stateSelect')?.value,
            property_city: document.getElementById('citySelect')?.value,
            property_location: zoneIds,
            name_of_location: projectIds,
            zone_id: zoneIds,
            projects: projectIds
        };
        $.ajax({
            url: '/api/realestate/submit-lead',
            type: 'POST',
            data: JSON.stringify(formData),
            contentType: 'application/json',
            dataType: 'json',
            headers: {
                'Authorization': 'Bearer OXpROcBEl0JYqCO6XwW4',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            success: function(response, textStatus, xhr) 
            {
                if (response.message && response.message.includes('already exists')) 
                {
                    if (typeof toastr !== 'undefined') 
                    {
                        toastr.error('This phone number is already registered. Our agent will contact you soon.');
                    } 
                    else 
                    {
                        toastr.error('This phone number is already registered. Our agent will contact you soon.');
                    }
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
                else if (response.status === 200) 
                {
                    if (typeof toastr !== 'undefined') 
                    {
                        toastr.success(response.message || 'Lead submitted successfully!');
                    } 
                    else 
                    {
                        toastr.error('Lead submitted successfully!');
                    }
                    goToStep(4);
                }
                else
                {
                    if (typeof toastr !== 'undefined') 
                    {
                        toastr.info(response.message || 'Form submitted');
                    }
                    goToStep(4);
                }
            },
            error: function(xhr, status, error) 
            {
                console.error('AJAX Error:', {
                    status: status,
                    error: error,
                    response: xhr.responseText,
                    responseJSON: xhr.responseJSON,
                    httpStatus: xhr.status
                });
                
                let errorMsg = 'Submission failed. Please try again.';
                let isDuplicate = false;
                if (xhr.status === 409) 
                {
                    isDuplicate = true;
                    errorMsg = 'This phone number is already registered. Our agent will contact you soon.';
                }
                else if (xhr.responseJSON) 
                {
                    if (xhr.responseJSON.message) 
                    {
                        errorMsg = xhr.responseJSON.message;
                        if (errorMsg.includes('already exists')) 
                        {
                            isDuplicate = true;
                        }
                    }
                    else if (xhr.responseJSON.error) 
                    {
                        errorMsg = xhr.responseJSON.error;
                        if (errorMsg.includes('already exists')) 
                        {
                            isDuplicate = true;
                        }
                    }
                }
                else if (xhr.status === 0)
                {
                    errorMsg = 'Network error. Please check your connection.';
                }
                else if (xhr.status === 404)
                {
                    errorMsg = 'API endpoint not found. Please check the URL.';
                }
                else if (xhr.status === 500)
                {
                    errorMsg = 'Server error. Please try again later.';
                }
                
                if (isDuplicate) 
                {
                    if (typeof toastr !== 'undefined') 
                    {
                        toastr.error(errorMsg);
                    } 
                    else 
                    {
                        toastr.error(errorMsg);
                    }
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
                else
                {
                    if (typeof toastr !== 'undefined') 
                    {
                        toastr.error(errorMsg);
                    } 
                    else 
                    {
                        toastr.error(errorMsg);
                    }
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
            }
        });
    }

    function resetForm() 
    {
        document.querySelectorAll('input, select, textarea').forEach(field => {
            if (field.type !== 'button' && field.type !== 'submit' && field.type !== 'select-multiple') 
            {
                field.value = '';
            }
        });
        
        document.querySelectorAll('input[type="radio"]').forEach(radio => {
            radio.checked = false;
        });

        const defaultRadio = document.querySelector('input[name="property_type"][checked]');
        if (defaultRadio) 
        {
            defaultRadio.checked = true;
        }
        
        selectedService = 'realestate';
        const serviceTypeInput = document.getElementById('service_type');
        if (serviceTypeInput) 
        {
            serviceTypeInput.value = 'realestate';
        }
        
        document.querySelectorAll('.service-card').forEach(card => {
            if (card.dataset.service === 'realestate') 
            {
                card.classList.add('active');
            } 
            else 
            {
                card.classList.remove('active');
            }
        });
        
        document.querySelectorAll('.service-fields').forEach(f => f.classList.remove('active'));
        const realestateFields = document.getElementById('realestate-fields');
        if (realestateFields) 
        {
            realestateFields.classList.add('active');
        }
        
        $('#stateSelect').val('');
        $('#citySelect').html('<option value="">Select City</option>').prop('disabled', true);
        $('#categorySelect').val('');
        $('#subCategorySelect').val('');
        $('#subCategoryDiv').hide();

        const projectSelect = document.getElementById('locationName');
        if (projectSelect) 
        {
            Array.from(projectSelect.options).forEach(option => {
                option.selected = false;
            });
            if (typeof jQuery.fn.select2 !== 'undefined') 
            {
                $(projectSelect).trigger('change');
            }
        }
        
        const locationSelect = document.getElementById('locationSelect');
        if (locationSelect) 
        {
            Array.from(locationSelect.options).forEach(option => {
                option.selected = false;
            });
            if (typeof jQuery.fn.select2 !== 'undefined') 
            {
                $(locationSelect).trigger('change');
            }
        }
        
        goToStep(1);
    }

    function loadStates() 
    {
        $.ajax({
            url: '/api/realestate/states',
            type: 'GET',
            success: function(response) 
            {
                if (response.status === 200 && response.data) 
                {
                    let options = '<option value="">Select State</option>';
                    response.data.forEach(item => {
                        let selected = item.state === 'Rajasthan' ? 'selected' : '';
                        options += `<option value="${item.state}" ${selected}>${item.state}</option>`;
                    });
                    $('#stateSelect').html(options);
                    loadCities('Rajasthan');
                }
            },
            error: function() 
            {
                $('#stateSelect').html('<option value="">Error loading states</option>');
            }
        });
    }

    $(document).ready(function() 
    {
        $('#stateSelect').html('<option value="Rajasthan" selected>Rajasthan</option>');
        loadCities('Rajasthan');
        $('#stateSelect').prop('disabled', true);
    });

    function loadCities(state) 
    {
        $.ajax({
            url: '/api/realestate/districts/' + encodeURIComponent(state),
            type: 'GET',
            success: function(response) 
            {
                if (response.status === 200 && response.data) 
                {
                    let options = '<option value="">Select City</option>';
                    response.data.forEach(item => {
                        options += `<option value="${item.id}">${item.District}</option>`;
                    });
                    $('#citySelect').html(options).prop('disabled', false);
                }
            },
            error: function() 
            {
                $('#citySelect').html('<option value="">Error loading cities</option>');
            }
        });
    }

    function loadCategories(type) 
    {
        $.ajax({
            url: '/api/realestate/categories/' + encodeURIComponent(type),
            type: 'GET',
            success: function(response) 
            {
                if (response.status === 200 && response.data) 
                {
                    let options = '<option value="">Select Category</option>';
                    response.data.forEach(item => {
                        options += `<option value="${item.id}">${item.name}</option>`;
                    });
                    $('#categorySelect').html(options);
                }
            },
            error: function() 
            {
                $('#categorySelect').html('<option value="">Error loading categories</option>');
            }
        });
    }

    function loadSubCategories(catgId) 
    {
        $.ajax({
            url: '/api/realestate/subcategories/' + catgId,
            type: 'GET',
            success: function(response) 
            {
                if (response.status === 200 && response.data && response.data.length > 0) 
                {
                    let options = '<option value="">Select Sub Category</option>';
                    response.data.forEach(item => {
                        options += `<option value="${item.id}">${item.name}</option>`;
                    });
                    $('#subCategorySelect').html(options);
                    $('#subCategoryDiv').show();
                } 
                else 
                {
                    $('#subCategoryDiv').hide();
                }
            },
            error: function() 
            {
                $('#subCategoryDiv').hide();
            }
        });
    }

    function loadProjects() 
    {
        $.ajax({
            url: '/api/realestate/get-projects',
            type: 'GET',
            success: function(response) 
            {
                if (response.status === 200 && response.data && response.data.length > 0) 
                {
                    let options = '';
                    response.data.forEach(item => {
                        options += `<option value="${item.id}">${item.project_name}</option>`;
                    });
                    
                    $('#locationName').html(options);
                    if (typeof jQuery.fn.select2 !== 'undefined') 
                    {
                        try 
                        {
                            $('#locationName').select2({
                                placeholder: "Select Projects",
                                allowClear: true,
                                width: '100%'
                            });
                        } 
                        catch (e) 
                        {
                            $('#locationName').attr('size', '4').css({
                                'height': 'auto',
                                'min-height': '100px'
                            });
                        }
                    } 
                    else 
                    {
                        $('#locationName').attr('size', '4').css({
                            'height': 'auto',
                            'min-height': '100px'
                        });
                    }
                } 
                else 
                {
                    $('#locationName').html('<option value="">No projects available</option>');
                }
            },
            error: function() 
            {
                $('#locationName').html('<option value="">Error loading projects</option>');
            }
        });
    }
</script>