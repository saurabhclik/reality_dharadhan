
    <footer class="app-footer">
        <div class="container-fluid">
            <nav class="footer-nav">
                <a href="{{route('mobile.dashboard')}}" class="footer-nav-item notification-item">
                    <i class="fa-solid fa-home position-relative"></i>
                    <span>Home</span>
                </a>
                <a href="{{route('mobile.all-leads')}}" class="footer-nav-item notification-item">
                    <i class="fa fa-list-ul"></i>
                    <span>All Leads</span>
                </a>
                <a href="{{route('mobile.tasks')}}" class="footer-nav-item notification-item">
                    <i class="fa fa-bars-progress"></i>
                    <span>Task</span>
                </a>
                <a href="#" class="footer-nav-item" id="menuButton">
                    <i class="fa fa-gears"></i>
                    <span>Setting</span>
                </a>
            </nav>
        </div>
    </footer>
    <div class="bottom-sheet">
        <div class="sheet-header remove-bottom-sheet" style="cursor:pointer;">
            <div class="handle"></div>
            <h5>Menu</h5>
        </div>
        <div class="sheet-content" id="mainAccordion">
            <a href="{{route('mobile.user.profile')}}" class="menu-item">
                <i class="fas fa-user-cog"></i>
                <span>My Profile</span>
            </a>
            <div class="sidebar-divider mt-4">
                <a href="{{route('mobile.logout')}}" class="sidebar-item">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </div>
        </div>
    </div>
    <div class="overlay"></div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
    <script src="../assets/mobile/js/script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    <script>
        $(document).ready(function() 
        {
            $('.select2').select2({
                width: '100%',
                placeholder: $(this).data('placeholder'),
                allowClear: true,
                dropdownParent: $('.app-form-container')
            });
            $('#menuButton').on('click', function(e) 
            {
                e.preventDefault();
                $('.bottom-sheet, .overlay').toggleClass('active');
            });
            
            $('.overlay').on('click', function() 
            {
                $('.bottom-sheet, .overlay').removeClass('active');
            });
            $('.remove-bottom-sheet').on('click', function()
            {
                $('.bottom-sheet, .overlay').removeClass('active');
            });
            
            let startY, currentY;
            
            $('.bottom-sheet').on('touchstart', function(e) 
            {
                startY = e.touches[0].clientY;
            });
            
            $('.bottom-sheet').on('touchmove', function(e) 
            {
                currentY = e.touches[0].clientY;
                
                if (currentY > startY && (currentY - startY) > 50) 
                {
                    $('.bottom-sheet, .overlay').removeClass('active');
                }
            });

            $('#SubmitBtn').closest('form').on('submit', function ()
            {
                $('#SubmitBtn').prop('disabled', true);
                $('#SubmitText').addClass('d-none');
                $('#SubmitSpinner').removeClass('d-none');
            });

            $('#state').change(function() 
            {
                var state = $(this).val();
                if (state) 
                {
                    $.ajax({
                        url: '/lead/get-cities/' + state,
                        type: 'GET',
                        dataType: 'json',
                        success: function(data)
                        {
                            $('#city').empty().append('<option value="">-- Select City --</option>');
                            $.each(data, function(i, v) 
                            {
                                $('#city').append('<option value="'+v.District+'">'+v.District+'</option>');
                            });
                        },
                        error: function() 
                        {
                            toastr.error('Failed to fetch cities.');
                        }
                    });
                }
                else 
                {
                    $('#city').empty().append('<option value="">-- Select City --</option>');
                }
            });

            $('#type').change(function() 
            {
                var type = $(this).val();
                if(type) 
                {
                    $.ajax({
                        url: '/lead/get-categories/' + type,
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) 
                        {
                            $('#category').empty();
                            $('#category').append('<option value="">-- Select Category --</option>');
                            $.each(data, function(key, value) 
                            {
                                $('#category').append('<option value="'+ value.id +'">'+ value.name +'</option>');
                            });
                        }
                    });
                } 
                else 
                {
                    $('#category').empty();
                    $('#category').append('<option value="">-- Select Category --</option>');
                    $('#sub_category').empty();
                    $('#sub_category').append('<option value="">-- Select Sub Category --</option>');
                }
            });

            $('#category').change(function() 
            {
                var categoryId = $(this).val();
                if(categoryId) 
                {
                    $.ajax({
                        url: '/lead/get-subcategories/' + categoryId,
                        type: 'GET',
                        dataType: 'json',
                        success: function(data)
                        {
                            $('#sub_category').empty();
                            $('#sub_category').append('<option value="">-- Select Sub Category --</option>');
                            $.each(data, function(key, value) 
                            {
                                $('#sub_category').append('<option value="'+ value.id +'">'+ value.name +'</option>');
                            });
                            @if(isset($lead))
                                if(categoryId == initialCategoryId) 
                                {
                                    $('#sub_category').val(initialSubCategoryId);
                                }
                            @endif
                        }
                    });
                } 
                else 
                {
                    $('#sub_category').empty();
                    $('#sub_category').append('<option value="">-- Select Sub Category --</option>');
                }
            });

        });

        function openBottomSheet() 
        {
            document.getElementById('leadSheet').classList.add('show');
        }

        function closeBottomSheet() 
        {
            document.getElementById('leadSheet').classList.remove('show');
        }
    </script>
    <?php
        if (isset($_SESSION['message']) && !empty($_SESSION['message'])) 
        {
            $status = $_SESSION['status'];
            $message = $_SESSION['message'];
            switch ($status) 
            {
                case '200': 
                    $toastType = 'success';
                    $title = 'Success';
                    break;
                case '300':
                    $toastType = 'warning';
                    $title = 'Warning';
                break;
                case '400':
                    $toastType = 'error';
                    $title = 'Error';
                    break;
                case '500':
                    $toastType = 'error';
                    $title = 'Error';
                break;
                default:
                    $toastType = 'info';
                    $title = 'Info';
                    break;
            }

            echo '<script>
                toastr.options = {
                    "closeButton": true,
                    "progressBar": true,
                    "positionClass": "toast-top-right",
                    "timeOut": "5000",
                    "extendedTimeOut": "1000"
                };
                toastr.' . $toastType . '("' . addslashes($message) . '", "' . addslashes($title) . '");
            </script>';
            unset($_SESSION['status'], $_SESSION['message'], $_SESSION['data']);
        }
    ?>
    <script>
        window.addEventListener('load', function () 
        {
            const loader = document.getElementById('page-loader');
            if (loader) 
            {
                loader.style.opacity = '0';
                setTimeout(() => loader.style.display = 'none', 300); 
            }
        });
        document.addEventListener("DOMContentLoaded", function () 
        {
            const loader = document.getElementById("locationLoader");
            const toggleContainer = document.getElementById("attendanceToggleContainer");
            const latInput = document.getElementById("latitude");
            const lngInput = document.getElementById("longitude");

            function showLoader(msg) 
            {
                if (loader) loader.classList.remove("d-none");
                const span = loader?.querySelector("span");
                if (span) span.textContent = msg;
                if (toggleContainer) toggleContainer.classList.add("d-none");
            }

            function hideLoader() 
            {
                if (loader) loader.classList.add("d-none");
                if (toggleContainer) toggleContainer.classList.remove("d-none");
            }

            function setLocation(lat, lng) 
            {
                if (latInput) latInput.value = lat;
                if (lngInput) lngInput.value = lng;
                sessionStorage.setItem("latitude", lat);
                sessionStorage.setItem("longitude", lng);
            }

            function success(position) 
            {
                setLocation(position.coords.latitude, position.coords.longitude);
                hideLoader();
                checkAttendanceStatus();
            }

            function error(err) 
            {
                if (err.code === 1) 
                {
                    showLoader("Location access denied. Please enable GPS.");
                } 
                else 
                {
                    showLoader("Fetching location...");
                    setTimeout(requestLocation, 2000);
                }
            }

            function requestLocation() 
            {
                if (navigator.geolocation) 
                {
                    navigator.geolocation.getCurrentPosition(success, error, {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 0
                    });
                } 
                else 
                {
                    showLoader("Geolocation not supported.");
                }
            }

            function checkAttendanceStatus() 
            {
                $.ajax({
                    url: "{{ route('mobile.attendance.status') }}",
                    type: "GET",
                    success: function(res) 
                    {
                        $('#attendanceToggle').prop('checked', res.action === 'start');
                        hideLoader();
                    },
                    error: function() 
                    {
                        $('#attendanceToggle').prop('checked', false);
                        hideLoader();
                    }
                });
            }
            const cachedLat = sessionStorage.getItem("latitude");
            const cachedLng = sessionStorage.getItem("longitude");

            if (cachedLat && cachedLng) 
            {
                setLocation(cachedLat, cachedLng);
                hideLoader();
                checkAttendanceStatus();
            } 
            else 
            {
                showLoader("Fetching location...");
                requestLocation();
            }
        });
        $(document).ready(function () 
        {
           $('#attendanceToggle').change(function () 
           {
                const lat = $('#latitude').val();
                const lng = $('#longitude').val();
                if (!lat || !lng) 
                {
                    toastr.error("Location not available. Please enable GPS.");
                    $(this).prop('checked', false);
                    return;
                }
                const action = $(this).is(':checked') ? 'start' : 'end';
                $.ajax({
                    url: "{{ route('mobile.attendance.mark') }}",
                    type: "POST",
                    data: {
                        latitude: lat,
                        longitude: lng,
                        action: action,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function (response) 
                    {
                        toastr.success(response.message);
                        if (response.status === 'exists' || response.status === 'success') 
                        {
                            $('#attendanceToggle').prop('checked', action === 'start');
                        }
                    },
                    error: function (xhr) 
                    {
                        toastr.error(xhr.responseJSON?.message || "Failed to update attendance.");
                        $('#attendanceToggle').prop('checked', action === 'end');
                    }
                });
            });
            $(document).ready(function() 
            {
                var reminderStatuses = ["INTERESTED", "CALL SCHEDULED", "MEETING SCHEDULED", "VISIT SCHEDULED"];

                function handleStatusChange() 
                {
                    var selectedStatus = $('#statusSelect').val();
                    var selectedConversion = $('#conversionType').val();
                    if (selectedStatus === "CONVERTED") 
                    {
                        $('#conversionFields').show();
                    } 
                    else 
                    {
                        $('#conversionFields').hide();
                        $('#conversionType').val(""); 
                    }
                    if (selectedStatus === "CONVERTED" && selectedConversion === "Completed") 
                    {
                        $('.applicant_div').show();
                    } 
                    else if(selectedStatus === 'VISIT SCHEDULED' || selectedStatus === 'VISIT DONE')
                    {
                        $('.mobile-schedule-project').show();
                    }
                    else 
                    {
                        $('.applicant_div').hide();
                        $('.mobile-schedule-project').hide();
                    }
                }
                $('#statusSelect').on('change', handleStatusChange);
                $('#conversionType').on('change', handleStatusChange);
                handleStatusChange();
                $('#cancelStatusUpdate').on('click', function() 
                {
                    $('#statusModal').hide();
                });
            });
        });
        document.addEventListener('DOMContentLoaded', function () 
        {
            const projectSelect = document.getElementById('prj_id');

            if (projectSelect) 
            {
                new Choices(projectSelect, {
                    removeItemButton: true,
                    placeholder: true,
                    placeholderValue: 'Select Project(s)',
                    searchPlaceholderValue: 'Search project...',
                    shouldSort: false,
                    itemSelectText: '',
                });
            }
        });
    </script>
</body>
</html>