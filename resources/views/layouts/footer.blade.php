            <div class="floating-filter">
                <button type="button" class="btn btn-primary btn-floating" id="toggleDashboardFilter">
                    <i class="fas fa-filter text-light"></i>
                </button>

                <div class="floating-box" id="filterDashboardBox">
                    <div class="filter-header d-flex justify-content-between align-items-center">
                        <span>Filters</span>
                        <button type="button" class="btn-close btn-close-white btn-sm" id="closeDashboardFilter"></button>
                    </div>

                    <div class="filter-section w-100">
                        <h6>Classification</h6>
                        <div class="d-flex flex-wrap gap-2">
                            @php
                                $leadClassifications = \DB::table('leads')
                                    ->whereNotNull('classification')
                                    ->where('classification', '!=', '')
                                    ->distinct()
                                    ->pluck('classification');

                                $leadSources = \DB::table('leads')
                                    ->whereNotNull('source')
                                    ->where('source', '!=', '')
                                    ->distinct()
                                    ->pluck('source');

                                $leadCampaigns = \DB::table('leads')
                                    ->whereNotNull('campaign')
                                    ->where('campaign', '!=', '')
                                    ->distinct()
                                    ->pluck('campaign');
                            @endphp

                            @foreach($leadClassifications as $classification)
                                @php
                                    $queryParams = request()->query();
                                    $queryParams['classification'] = $classification;
                                    $url = route('leads.filter.leads') . '?' . http_build_query($queryParams);
                                @endphp
                                <a href="{{ $url }}" class="filter-option option-classification {{ request('classification') == $classification ? 'active' : '' }}">
                                    {{ $classification }}
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <div class="filter-section mt-3 w-100">
                        <h6>Source</h6>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($leadSources as $source)
                                @php
                                    $queryParams = request()->query();
                                    $queryParams['source'] = $source;
                                    $url = route('leads.filter.leads') . '?' . http_build_query($queryParams);
                                @endphp
                                <a href="{{ $url }}" class="filter-option option-source {{ request('source') == $source ? 'active' : '' }}">
                                    {{ $source }}
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <div class="filter-section mt-3 w-100">
                        <h6>Campaign</h6>
                        <select id="campaignFilter" class="form-control select2">
                            <option value="">-- Select Campaign --</option>
                            @foreach($leadCampaigns as $campaign)
                                <option value="{{ $campaign }}" {{ request('campaign') == $campaign ? 'selected' : '' }}>
                                    {{ $campaign }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="floating-calendar-toggle" id="calendarToggle">
                <i class="fas fa-calendar-alt"></i>
            </div>

            <div class="floating-calendar" id="floatingCalendar">
                <div class="floating-calendar-header">
                    <div class="floating-calendar-title">
                        <i class="fas fa-calendar-day me-2"></i>Schedule Calendar
                    </div>
                    <button class="floating-calendar-close" id="closeCalendar">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="floating-calendar-body">
                    <div id="calendarWidget"></div>
                </div>
            </div>

            @include('modals.bulk-import')
            <div class="rightbar-overlay"></div>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/4.7.0/apexcharts.min.js" integrity="sha512-xHtgQEymzQrphqglnjawC6hqXjIlzaGwK4h5xfYKIr/rM5CI9RXazkGZHzn5lIBxzU7vCb8uacAe5ACUGyz/Ow==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
            <script src="{{asset('js/bootstrap.bundle.min.js')}}"></script>
            <script src="{{asset('js/metisMenu.min.js')}}"></script>
            <script src="{{asset('js/simplebar.min.js')}}"></script>
            <script src="{{asset('js/waves.min.js')}}"></script>
            <script src="{{asset('js/dashboard.init.js')}}"></script>
            <script src="{{asset('js/app.js')}}"></script>
            <script src="{{asset('js/tinymce.min.js')}}"></script>
            <script src="{{asset('js/task-create.init.js')}}"></script>
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
            <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/@flasher/flasher@1.2.4/dist/flasher.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/@flasher/flasher-toastr@1.2.4/dist/flasher-toastr.min.js"></script>
            <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
            <script src="https://cdn.datatables.net/fixedcolumns/4.3.0/js/dataTables.fixedColumns.min.js"></script>
            <script defer src="https://cdn.jsdelivr.net/npm/@flasher/flasher@1.2.4/dist/flasher.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>
            <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">   
            <script>
                $(document).ready(function() 
                {
                    let currentSelectedDate = null;
                    let isCalendarVisible = false;
                    $('#floatingCalendar').addClass('floating-calendar');
                    $('#calendarToggle').css({
                        'display': 'flex',
                        'z-index': '9999',
                        'cursor': 'pointer',
                        'width': '50px',
                        'height': '50px',
                        'background': '#CF5D3B',
                        'color': 'white',
                        'border-radius': '50%',
                        'align-items': 'center',
                        'justify-content': 'center',
                        'box-shadow': '0 4px 12px rgba(55, 98, 184, 0.3)',
                        'transition': 'all 0.3s'
                    }).hover(
                        function() { $(this).css('transform', 'scale(1.1)'); },
                        function() { $(this).css('transform', 'scale(1)'); }
                    );
                    $('#calendarToggle').off('click').on('click', function(e)
                    {
                        e.preventDefault();
                        e.stopPropagation();
                        
                        if ($('#floatingCalendar').is(':visible')) 
                        {
                            $('#floatingCalendar').fadeOut(200);
                            isCalendarVisible = false;
                        } 
                        else 
                        {
                            $('#floatingCalendar').fadeIn(200);
                            isCalendarVisible = true;
                            renderFullCalendar();
                        }
                    });

                    $('#closeCalendar').off('click').on('click', function(e) 
                    {
                        e.preventDefault();
                        e.stopPropagation();
                        $('#floatingCalendar').fadeOut(200);
                        isCalendarVisible = false;
                    });

                    // $(document).on('click', function(e) 
                    // {
                    //     if (!$(e.target).closest('#floatingCalendar').length && 
                    //         !$(e.target).closest('#calendarToggle').length && 
                    //         isCalendarVisible) 
                    //         {
                    //             $('#floatingCalendar').fadeOut(200);
                    //             isCalendarVisible = false;
                    //     }
                    // });

                    function renderFullCalendar() 
                    {
                        const now = new Date();
                        renderMonth(now.getFullYear(), now.getMonth());
                    }

                    function renderMonth(year, month)
                    {
                        const firstDay = new Date(year, month, 1);
                        const lastDay = new Date(year, month + 1, 0);
                        const startingDay = firstDay.getDay();
                        const totalDays = lastDay.getDate();
                        const today = new Date();
                        
                        const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
                            'July', 'August', 'September', 'October', 'November', 'December'];
                        
                        let html = `
                            <div class="calendar-header">
                                <button class="prev-month">
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                                <h5>${monthNames[month]} ${year}</h5>
                                <button class="next-month">
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                            </div>
                            <div class="calendar-weekdays">
                                <div>Su</div>
                                <div>Mo</div>
                                <div>Tu</div><div>We</div><div>Th</div><div>Fr</div><div>Sa</div>
                            </div>
                            <div class="calendar-days">
                        `;
                        for (let i = 0; i < startingDay; i++) 
                        {
                            html += '<div class="calendar-day empty"></div>';
                        }
                        for (let day = 1; day <= totalDays; day++) 
                        {
                            const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                            const eventsOnDate = window.calendarEvents[dateStr] || [];
                            const hasEvents = eventsOnDate.length > 0;
                            const hasMissed = eventsOnDate.some(e => e.is_missed === 1 || e.is_overdue === 1);
                            const isToday = isSameDay(today, new Date(year, month, day));
                            
                            let dayClass = 'calendar-day';
                            if (isToday) dayClass += ' today';
                            if (hasEvents) dayClass += ' has-events';
                            if (hasMissed) dayClass += ' has-missed';
                            if (eventsOnDate.length >= 3) dayClass += ' has-many-events';
                            let tooltipContent = '';
                            if (hasEvents)
                            {
                                const displayEvents = eventsOnDate.slice(0, 3);
                                displayEvents.forEach(event => {
                                    const isLead = event.event_type === 'lead';
                                    const icon = isLead ? 'fa-user' : 'fa-tasks';
                                    const borderColor = (event.is_missed === 1 || event.is_overdue === 1) ? '#dc3545' : (isLead ? '#CF5D3B' : '#28a745');
                                    const missedClass = (event.is_missed === 1 || event.is_overdue === 1) ? 'missed' : '';
                                });
                            }
                            
                            html += `
                                <div class="${dayClass}" data-date="${dateStr}" onclick="handleDateClick('${dateStr}')">
                                    ${day}
                                    ${hasEvents ? `<span class="event-count">${eventsOnDate.length}</span>` : ''}
                                    ${tooltipContent}
                                </div>
                            `;
                        }
                        
                        html += '</div>';
                        
                        $('#floatingCalendar .floating-calendar-body').html(html);
                        $('.prev-month').off('click').on('click', function(e) 
                        {
                            e.preventDefault();
                            e.stopPropagation();
                            let newMonth = month - 1;
                            let newYear = year;
                            if (newMonth < 0) 
                            {
                                newMonth = 11;
                                newYear--;
                            }
                            renderMonth(newYear, newMonth);
                        });
                        
                        $('.next-month').off('click').on('click', function(e)
                        {
                            e.preventDefault();
                            e.stopPropagation();
                            let newMonth = month + 1;
                            let newYear = year;
                            if (newMonth > 11) 
                            {
                                newMonth = 0;
                                newYear++;
                            }
                            renderMonth(newYear, newMonth);
                        });
                    }

                    function isSameDay(date1, date2) 
                    {
                        return date1.getFullYear() === date2.getFullYear() &&
                            date1.getMonth() === date2.getMonth() &&
                            date1.getDate() === date2.getDate();
                    }
                });

                window.handleDateClick = function(dateStr) 
                {
                    const events = window.calendarEvents[dateStr] || [];
                    
                    if (events.length === 0) 
                    {
                        return;
                    }
                    showDayEvents(dateStr);
                };

                window.showDayEvents = function(dateStr) 
                {
                    const events = window.calendarEvents[dateStr] || [];
                    const date = new Date(dateStr);
                    const formattedDate = date.toLocaleDateString('en-US', { 
                        weekday: 'long', 
                        year: 'numeric', 
                        month: 'long', 
                        day: 'numeric' 
                    });
                    
                    $('#dayEventsDate').text(formattedDate);
                    
                    let eventsHtml = '';
                    
                    if (events.length === 0) 
                    {
                        eventsHtml = '<p class="text-center text-muted py-4">No events for this day</p>';
                    } 
                    else 
                    {
                        events.sort((a, b) => {
                            const aMissed = a.is_missed === 1 || a.is_overdue === 1;
                            const bMissed = b.is_missed === 1 || b.is_overdue === 1;
                            if (aMissed && !bMissed) return -1;
                            if (!aMissed && bMissed) return 1;
                            return 0;
                        });
                        
                        events.forEach(event => {
                            const isLead = event.event_type === 'lead';
                            const icon = isLead ? 'fa-user' : 'fa-tasks';
                            const borderColor = (event.is_missed === 1 || event.is_overdue === 1) ? '#dc3545' : (isLead ? '#CF5D3B' : '#28a745');
                            const missedText = (event.is_missed === 1 || event.is_overdue === 1) ? 'Missed' : '';
                            const missedClass = (event.is_missed === 1 || event.is_overdue === 1) ? 'missed' : '';
                            
                            eventsHtml += `
                                <div class="day-event-item ${missedClass}" 
                                    onclick="showEventDetails(${event.id}, '${event.event_type}', '${event.status || event.lead_status || ''}')" 
                                    style="border-left-color: ${borderColor}; cursor: pointer;">
                                    <div class="event-header">
                                        <i class="fas ${icon}" style="color: ${borderColor};"></i>
                                        <span class="event-title">${event.title || 'Untitled'}</span>
                                        ${missedText ? '<span class="badge bg-danger ms-2">Missed</span>' : ''}
                                    </div>
                                    <div class="event-details">
                                        <span><i class="far fa-clock"></i> ${event.remind_time || 'All day'}</span>
                                        <span><i class="fas fa-tag"></i> ${event.status || event.lead_status || 'No status'}</span>
                                        ${event.project_name ? `<span><i class="fas fa-building"></i> ${event.project_name}</span>` : ''}
                                    </div>
                                    <div class="event-badge" style="background: ${borderColor}20; color: ${borderColor}; border: 1px solid ${borderColor}">
                                        ${event.event_type}
                                    </div>
                                </div>
                            `;
                        });
                    }
                    
                    $('#dayEventsList').html(eventsHtml);
                    $('#dayEventsModal').modal('show');
                };

                window.showEventDetails = function(id, type, status) 
                {
                    $('#dayEventsModal').modal('hide');
                    
                    if (type === 'lead' && id && status) 
                    {
                        redirectToLeadStatusPage(status, id);
                    } 
                    else 
                    {
                        $('#calendarEventModal').modal('show');
                        
                        $.ajax({
                            url: '/task/' + id + '/details?type=task',
                            type: 'GET',
                            success: function(response) 
                            {
                                let content = '<div class="container-fluid p-3">';
                                content += '<h5>' + (response.title || response.name || 'Details') + '</h5>';
                                content += '<hr>';
                                
                                const fields = {
                                    'description': 'Description',
                                    'priority': 'Priority',
                                    'task_status': 'Task Status',
                                    'start_date': 'Start Date',
                                    'end_date': 'End Date',
                                    'assigned_by': 'Assigned By',
                                    'project_name': 'Project'
                                };
                                
                                for (let key in fields) 
                                {
                                    if (response[key] && response[key] !== 'null' && response[key] !== '') 
                                    {
                                        content += `<p><strong>${fields[key]}:</strong> ${response[key]}</p>`;
                                    }
                                }
                                
                                content += '</div>';
                                $('#calendarEventModalBody').html(content);
                            },
                            error: function() 
                            {
                                $('#calendarEventModalBody').html('<div class="alert alert-danger">Error loading details</div>');
                            }
                        });
                    }
                };

                function redirectToLeadStatusPage(status, leadId) 
                {
                    const statusRouteMap = {
                        'CALL SCHEDULED': 'lead.call-scheduled',
                        'VISIT SCHEDULED': 'lead.visit-scheduled',
                        'WHATSAPP': 'lead.whatsapp',
                        'INTERESTED': 'lead.interested',
                        'MEETING SCHEDULED': 'lead.meeting-scheduled',
                        'PROCESSING': 'lead.processing',
                        'NOT PICKED': 'lead.not-picked',
                        'FUTURE LEAD': 'lead.future',
                        'PENDING': 'lead.pending',
                        'TRANSFER LEAD': 'lead.transfer-lead',
                        'BOOKED': 'lead.booked',
                        'COMPLETED': 'lead.completed',
                        'CANCELLED': 'lead.cancelled',
                        'LOST': 'lead.lost',
                        'NOT INTERESTED': 'lead.not-interested',
                        'WRONG NUMBER': 'lead.wrong-number',
                        'NOT REACHABLE': 'lead.not-reachable',
                        'NEW LEAD': 'lead.new',
                        'ALLOCATED LEAD': 'lead.allocate',
                        'UNALLOCATED LEAD': 'lead.unallocated',
                        'CHANNEL PARTNER': 'lead.channel-partner',
                        'SITE_VISIT': 'lead.visit-scheduled',
                        'OPPORTUNITY': 'lead.interested',
                        'REVISIT_SCHEDULED': 'lead.visit-scheduled'
                    };
                    const normalizedStatus = status.toUpperCase().trim();
                    const routeName = statusRouteMap[normalizedStatus];
                    
                    if (routeName) 
                    {
                        const baseUrl = '{{ url("") }}';
                        const urlPath = routeName.replace(/\./g, '/');
                        let url = baseUrl + '/' + urlPath;
                        url += '?search=' + leadId;
                        
                        window.location.href = url;
                    } 
                    else 
                    {
                        showLeadInModal(leadId);
                    }
                }

                function showLeadInModal(leadId) 
                {
                    $('#calendarEventModal').modal('show');
                    $.ajax({
                        url: '/lead/' + leadId + '/details?type=lead',
                        type: 'GET',
                        success: function(response) 
                        {
                            let content = '<div class="container-fluid p-3">';
                            content += '<h5>' + (response.name || 'Lead Details') + '</h5>';
                            content += '<hr>';
                            
                            const fields = {
                                'name': 'Name',
                                'phone': 'Phone',
                                'whatsapp_no': 'WhatsApp',
                                'email': 'Email',
                                'status': 'Status',
                                'remind_date': 'Reminder Date',
                                'remind_time': 'Reminder Time',
                                'agent_name': 'Agent',
                                'project_name': 'Project',
                                'source': 'Source',
                                'campaign': 'Campaign',
                                'classification': 'Classification',
                                'last_comment': 'Last Comment'
                            };
                            
                            for (let key in fields) 
                            {
                                if (response[key] && response[key] !== 'null' && response[key] !== '') 
                                {
                                    content += `<p><strong>${fields[key]}:</strong> ${response[key]}</p>`;
                                }
                            }
                            
                            content += '</div>';
                            $('#calendarEventModalBody').html(content);
                        },
                        error: function() 
                        {
                            $('#calendarEventModalBody').html('<div class="alert alert-danger">Error loading lead details</div>');
                        }
                    });
                }
                window.calendarEvents = @json($calendarEvents ?? []);
            </script>
            <script>
               $(document).ready(function() 
                {
                    const $filterBtn = $('#toggleDashboardFilter');
                    const $filterBox = $('#filterDashboardBox');
                    const $closeBtn = $('#closeDashboardFilter');
                    $filterBtn.on('click', function(e) 
                    {
                        e.stopPropagation();
                        $filterBox.toggle();
                    });
                    $closeBtn.on('click', function(e) 
                    {
                        e.stopPropagation();
                        $filterBox.hide();
                    });
                    $filterBox.on('click', function(e) 
                    {
                        e.stopPropagation();
                    });
                    $(document).on('click', function() 
                    {
                        $filterBox.hide();
                    });
                });

                var options = 
                {
                    series: [67],
                    chart: 
                    {
                        height: 350,
                        type: 'radialBar',
                        offsetY: -10
                    },
                    plotOptions: 
                    {
                        radialBar: 
                        {
                            startAngle: -135,
                            endAngle: 135,
                            dataLabels: 
                            {
                            name: 
                            {
                                fontSize: '16px',
                                color: undefined,
                                offsetY: 120
                            },
                            value: 
                            {
                                offsetY: 76,
                                fontSize: '22px',
                                color: undefined,
                                formatter: function (val) 
                                {
                                    return val + "%";
                                }
                            }
                            }
                        }
                    },
                    fill: 
                    {
                        type: 'gradient',
                        gradient: 
                        {
                            shade: 'dark',
                            shadeIntensity: 0.15,
                            inverseColors: false,
                            opacityFrom: 1,
                            opacityTo: 1,
                            stops: [0, 50, 65, 91]
                        },
                    },
                    stroke: 
                    {
                        dashArray: 4
                    },
                        labels: ['Median Ratio'],
                    };
                    var chart = new ApexCharts(document.querySelector("#radialBar-chart"), options);
                    chart.render();
            </script>
            <script>
                const monthsReport = @json(isset($monthsReport) ? $monthsReport : 0);
                var options = {
                    series: [{
                        name: 'Leads',
                        data: monthsReport
                    }],
                    chart: {
                        type: 'bar',
                        height: 350
                    },
                    plotOptions: {
                        bar: {
                            horizontal: false,
                            columnWidth: '55%',
                            borderRadius: 5,
                            borderRadiusApplication: 'end'
                        },
                    },
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        show: true,
                        width: 2,
                        colors: ['transparent']
                    },
                    xaxis: {
                        categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
                    },
                    yaxis: {
                        title: {
                            text: 'Total Leads'
                        }
                    },
                    fill: {
                        opacity: 1
                    },
                    tooltip: {
                        y: {
                            formatter: function (val) {
                                return val + " leads";
                            }
                        }
                    }
                };
                var chart = new ApexCharts(document.querySelector("#monthly-chart"), options);
                chart.render();
            </script>
            <script>
                $(document).ready(function () 
                {
                    $('.select2').select2({
                        placeholder: 'Select',
                        width: '100%'
                    });
                    $('#sourcesTable').DataTable({
                        paging: true,
                        searching: true,
                        info: false,
                        lengthChange: false,
                        pageLength: 5,
                        language: {
                            emptyTable: "No sources available."
                        }
                    });

                    $('#bulkCampaignsTable').DataTable({
                        paging: true,
                        searching: true,
                        info: false,
                        lengthChange: false,
                        pageLength: 5,
                        language: {
                            emptyTable: "No campaigns available."
                        }
                    });
                    const urlParams = new URLSearchParams(window.location.search);
                    const length = parseInt(urlParams.get('length')) || 10;  
                    const page = parseInt(urlParams.get('page')) || 1;    
                    const table = $('#table').DataTable({
                        scrollX: true,
                        scrollCollapse: true,
                        responsive: true,
                        paging: false,
                        fixedColumns: {
                            leftColumns: 2,
                            rightColumns: 1
                        },
                    });
                    $('.data-table').DataTable({
                        scrollX: true,
                        scrollCollapse: true,
                        fixedColumns: {
                            leftColumns: 0,
                            rightColumns: 0
                        },
                        responsive: true,
                        autoWidth: false,
                        dom: '<"top"lf>rt<"bottom"ip>', 
                        columnDefs: [
                            { width: "20%", targets: 0 }, 
                            { width: "15%", targets: 1 }  
                        ]
                    });               
                    $('#togglePassword').on('click', function () 
                    {
                        const $password = $('#password');
                        const type = $password.attr('type') === 'password' ? 'text' : 'password';
                        $password.attr('type', type);
                        const $icon = $(this).find('i');
                        $icon.toggleClass('fa-eye fa-eye-slash');
                    });

                    $('.delete-user-btn').click(function() 
                    {
                        const userId = $(this).data('user-id');

                        Swal.fire({
                            title: 'Are you sure?',
                            text: "You won't be able to revert this!",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Yes, delete it!'
                        }).then((result) => {
                            if (result.isConfirmed) 
                            {
                                $.ajax({
                                    url: "/users/" + userId + "/check-delete",
                                    method: 'DELETE',
                                    data: {
                                        _token: "{{ csrf_token() }}"
                                    },
                                    success: function(response) 
                                    {
                                        if (response.success) 
                                        {
                                            toastr.success('User deleted successfully');
                                            setTimeout(() => location.reload(), 1500);
                                        } 
                                        else if (response.hasLeads) 
                                        {
                                            Swal.fire({
                                                title: 'Cannot Delete User',
                                                html: `This user has <b>${response.leadsCount}</b> lead(s).<br>
                                                    You must transfer them first.`,
                                                icon: 'info',
                                                showCancelButton: true,
                                                confirmButtonText: 'Transfer Leads',
                                                cancelButtonText: 'Cancel'
                                            }).then((r) => {
                                                if(r.isConfirmed)
                                                {
                                                    window.location.href = response.transferUrl;
                                                }
                                            });
                                        } 
                                        else 
                                        {
                                            toastr.error(response.message || 'Failed to delete user');
                                        }
                                    },
                                    error: function(xhr) 
                                    {
                                        toastr.error('Error deleting user');
                                    }
                                });
                            }
                        });
                    });
                    
                    $('.edit-btn').on('click', function () 
                    {
                        let id = $(this).data('id');
                        let name = $(this).data('name');
                        let type = $(this).data('type');
                        let action = $(this).data('action');
                        let modal = $(this).data('modal');

                        if(modal == 'Checklist')
                        {
                            $('.type-field').addClass('d-none');
                        }
                        else if(modal == 'Category')
                        {
                            $('.cat-type').removeClass('d-none');
                        }
                        $('#id').val(id);
                        $('#name').val(name);
                        $('#action').attr('action', action + '/' + id);
                        $('#modal-type').html(type);
                        $('#ProjectModalLabel').html('Edit' + modal);
                        $('#modal-name').html(modal);
                        $('#ModalboxLabel').html(modal + ' ' + type);
                        $('#type').val(type);
                        $('#cat_type').val(type);
                        if ($('#action').find('input[name="_method"]').length === 0) 
                        {
                            $('#action').append('<input type="hidden" name="_method" value="PUT">');
                        }
                    });

                    $('.add-project').on('click', function () 
                    {
                        let action = $(this).data('action');
                        $("#action")[0].reset();
                        let type = $(this).data('type'); 
                        let modal = $(this).data('modal');
                        if(modal == 'Checklist')
                        {
                            $('.type-field').addClass('d-none');
                        }
                        else if(modal == 'Category')
                        {
                            $('.cat-type').removeClass('d-none');
                        }
                        $('#id').val('');
                        $('#name').val('');
                        $('#type').val('');
                        $('#action').attr('action', action);
                        $('#modal-type').html(type);
                        $('#ProjectModalLabel').html('Create' + modal);
                        $('#ModalboxLabel').html(modal + ' ' + type);
                        $('#modal-name').html(modal);
                    });
                    let isProcessing = false;
                    let originalState = $('#attendanceToggle').prop('checked');

                    $('#attendanceToggle').on('change', function () 
                    {
                        if (isProcessing) return;
                        const newState = this.checked;
                        const action = newState ? 'start' : 'end';
                        $(this).prop('checked', originalState);
                        const dialogConfig = {
                            start: {
                                title: 'Begin Work Session',
                                html: `<div class="swal-work-start">
                                        <i class="fas fa-business-time fa-3x mb-3" style="color:#CF5D3B"></i>
                                        <p>Ready to start tracking your productive time?</p>
                                    </div>`,
                                confirmButtonColor: '#CF5D3B',
                                confirmText: 'Clock In'
                            },
                            end: {
                                title: 'End Work Session',
                                html: `<div class="swal-work-end">
                                        <i class="fas fa-clock fa-3x mb-3" style="color:#ff7675"></i>
                                        <p>Are you sure you want to end your session?</p>
                                    </div>`,
                                confirmButtonColor: '#ff7675',
                                confirmText: 'Clock Out'
                            }
                        };

                        Swal.fire({
                            title: dialogConfig[action].title,
                            html: dialogConfig[action].html,
                            icon: null,
                            showCancelButton: true,
                            confirmButtonText: dialogConfig[action].confirmText,
                            confirmButtonColor: dialogConfig[action].confirmButtonColor,
                            cancelButtonText: 'Cancel',
                            showClass: {
                                popup: 'animate__animated animate__fadeIn animate__faster'
                            },
                            hideClass: {
                                popup: 'animate__animated animate__fadeOut animate__faster'
                            }
                        }).then((result) => {
                            if (result.isConfirmed) 
                            {
                                isProcessing = true;
                                markAttendance(action, newState);
                            } 
                            else 
                            {
                                updateToggleVisuals(originalState);
                            }
                        });
                    });

                    function markAttendance(action, targetState) 
                    {
                        const $toggle = $('#attendanceToggle');
                        $toggle.prop('disabled', true);
                        $toggle.next().find('.attendance-toggle-handle').css({
                            'box-shadow': '0 0 0 5px rgba(79, 172, 254, 0.3)',
                            'transition': 'all 0.5s ease'
                        });
                        if (navigator.geolocation) 
                        {
                            navigator.geolocation.getCurrentPosition(function (position) 
                            {
                                const latitude = position.coords.latitude;
                                const longitude = position.coords.longitude;
                                $.ajax({
                                    url: '{{ route("attendance.toggle") }}',
                                    method: 'POST',
                                    data: {
                                        _token: '{{ csrf_token() }}',
                                        action: action,
                                        latitude: latitude,
                                        longitude: longitude
                                    },
                                    success: (response) => {
                                        originalState = targetState;
                                        updateToggleVisuals(targetState);
                                        showSuccessAlert(response.message);
                                    },
                                    error: (xhr) => {
                                        let errorMessage = 'Failed to update attendance.';
                                        if (xhr.responseJSON?.message) 
                                        {
                                            errorMessage = xhr.responseJSON.message;
                                        }
                                        showErrorAlert(errorMessage);
                                        updateToggleVisuals(originalState);
                                    },
                                    complete: () => {
                                        $toggle.prop('disabled', false);
                                        $toggle.next().find('.attendance-toggle-handle').css({
                                            'box-shadow': '',
                                            'transition': ''
                                        });
                                        isProcessing = false;
                                    }
                                });

                            }, function (error) 
                            {
                                showErrorAlert('Unable to access location. Please allow location access and try again.');
                                updateToggleVisuals(originalState);
                                $toggle.prop('disabled', false);
                                $toggle.next().find('.attendance-toggle-handle').css({
                                    'box-shadow': '',
                                    'transition': ''
                                });
                                isProcessing = false;
                            });
                        } 
                        else 
                        {
                            showErrorAlert('Geolocation is not supported by this browser.');
                            updateToggleVisuals(originalState);
                            $toggle.prop('disabled', false);
                            $toggle.next().find('.attendance-toggle-handle').css({
                                'box-shadow': '',
                                'transition': ''
                            });
                            isProcessing = false;
                        }
                    }

                    function updateToggleVisuals(state) 
                    {
                        $('#attendanceToggle').prop('checked', state);
                    }

                    function showSuccessAlert(message) 
                    {
                        Swal.fire({
                            title: 'Success',
                            text: message,
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false,
                            timerProgressBar: true
                        });
                    }

                    function showErrorAlert(message) 
                    {
                        Swal.fire({
                            title: 'Error',
                            text: message,
                            icon: 'error',
                            confirmButtonColor: '#ff7675'
                        });
                    }
                    $(document).on('submit', '.lead-allocate-form', function () 
                    {
                        $('.submit-btn').prop('disabled', true);
                        $('.submit-text').addClass('d-none');
                        $('#SubmitSpinner').removeClass('d-none');
                    });
                });
            </script>
            <script>
                function showStatusUpdateModal(leadId, currentStatus) 
                {
                    $('#leadId').val(leadId);
                    $('#currentStatus').text(currentStatus);
                    $('#newStatus').val(currentStatus);
                    $('#conversionType').val('Completed');
                    $('#conversionTypeField').hide();
                    $('#comment').val('');
                    $('#remindDate').val('');
                    $('#remindTime').val('');
                    $('#remindDate').attr('min', new Date().toISOString().split('T')[0]);
                    $('#projectSelectionField').hide();
                    $('#visitProjects').val(null).trigger('change');
                    if ($.fn.select2) 
                    {
                        $('#visitProjects').select2({
                            placeholder: 'Select Project(s) for Visit',
                            allowClear: true,
                            width: '100%'
                        });
                    }
                    
                    $('#selectedProjectsPreview').hide();
                    
                    var modal = new bootstrap.Modal(document.getElementById('statusUpdateModal'));
                    modal.show();
                    setTimeout(function() 
                    {
                        if ($.fn.select2) 
                        {
                            $('.select2').select2({
                                placeholder: 'Select',
                                width: '100%'
                            });
                        }
                    }, 500);
                    
                    $('#newStatus').trigger('change');
                }

                function toggleApplicantFields() 
                {
                    var status = $('#newStatus').val();
                    var conversionType = $('#conversionType').val();

                    if (status === 'CONVERTED' && conversionType === 'Completed') 
                    {
                        $('.applicant_div').show();
                    } 
                    else 
                    {
                        $('.applicant_div').hide();
                    }
                }

                function toggleProjectSelection() 
                {
                    var status = $('#newStatus').val();
                    const visitStatuses = ['VISIT SCHEDULED', 'VISIT DONE'];
                    
                    if (visitStatuses.includes(status)) 
                    {
                        $('#projectSelectionField').show();
                        $('#visitProjects').prop('required', true);
                        loadLeadProjects();
                    } 
                    else 
                    {
                        $('#projectSelectionField').hide();
                        $('#visitProjects').prop('required', false);
                        $('#selectedProjectsPreview').hide();
                    }
                }

                function loadLeadProjects() 
                {
                    var leadId = $('#leadId').val();
                    var $select = $('#visitProjects');
                    
                    if (!leadId) return;
                    var currentOptions = $select.html();
                    
                    $select.html('<option value="">Loading projects...</option>');
                    
                    $.ajax({
                        url: '{{ route("lead.get-lead-projects") }}',
                        type: 'POST',
                        data: {
                            lead_id: leadId,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) 
                        {
                            if (response.success && response.projects.length > 0) 
                            {
                                var options = '<option value="">--- Select Project(s) for Visit ---</option>';
                                response.projects.forEach(function(project)
                                {
                                    options += '<option value="' + project.id + '">' + project.project_name + '</option>';
                                });
                                
                                $select.html(options);s
                                if ($.fn.select2) 
                                {
                                    $select.select2({
                                        placeholder: 'Select Project(s) for Visit',
                                        allowClear: true,
                                        width: '100%'
                                    });
                                }
                                
                                updateSelectedProjectsPreview();
                            } 
                            else 
                            {
                                $select.html(currentOptions);
                            }
                        },
                        error: function() 
                        {
                            $select.html(currentOptions);
                        }
                    });
                }

                function updateSelectedProjectsPreview() 
                {
                    var selectedProjects = $('#visitProjects').val();
                    var previewContainer = $('#selectedProjectsPreview');
                    var projectsList = $('#selectedProjectsList');
                    
                    projectsList.empty();
                    
                    if (selectedProjects && selectedProjects.length > 0) 
                    {
                        previewContainer.show();
                        $.ajax({
                            url: '{{ route("lead.get-project-names") }}',
                            type: 'POST',
                            data: {
                                project_ids: selectedProjects,
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) 
                            {
                                if (response.success) 
                                {
                                    projectsList.empty();
                                    response.projectNames.forEach(function(projectName, index) 
                                    {
                                        var projectId = selectedProjects[index];
                                        var badge = $('<span class="selected-project-badge"></span>');
                                        badge.html(projectName + 
                                            '<button type="button" class="remove-btn" onclick="removeProjectFromSelection(\'' + projectId + '\')">×</button>');
                                        projectsList.append(badge);
                                    });
                                }
                            },
                            error: function() 
                            {
                                selectedProjects.forEach(function(projectId) 
                                {
                                    var badge = $('<span class="selected-project-badge"></span>');
                                    badge.html('Project ID: ' + projectId + 
                                        '<button type="button" class="remove-btn" onclick="removeProjectFromSelection(\'' + projectId + '\')">×</button>');
                                    projectsList.append(badge);
                                });
                            }
                        });
                    } 
                    else 
                    {
                        previewContainer.hide();
                    }
                }

                function removeProjectFromSelection(projectId) 
                {
                    var currentValues = $('#visitProjects').val();
                    var updatedValues = currentValues.filter(function(id) 
                    {
                        return id !== projectId;
                    });
                    $('#visitProjects').val(updatedValues).trigger('change');
                }

                $(document).ready(function() 
                {
                    $('.applicant_div').hide();
                    $('#conversionTypeField').hide();
                    $('#projectSelectionField').hide();

                    $('#newStatus').on('change', function() 
                    {
                        var status = $(this).val();
                        if (status === 'CONVERTED') 
                        {
                            $('#conversionTypeField').show();
                        } 
                        else if(status === 'LOST')
                        {
                            $('#reminderFields').hide();
                        }
                        else if(status === 'VISIT DONE')
                        {
                            $('.followUp').html('Next Follow Up Date');
                        }
                        else
                        {
                            $('#conversionTypeField').hide();
                            $('#reminderFields').show();
                            $('.applicant_div').hide();
                            $('.followUp').html('Follow Up Date');
                        }
                        toggleProjectSelection();
                        toggleApplicantFields();
                    });

                    $('#conversionType').on('change', function() 
                    {
                        toggleApplicantFields();
                    });

                    $('#visitProjects').on('change', function() 
                    {
                        updateSelectedProjectsPreview();
                    });
                });

                function updateLeadStatus() 
                {
                    const leadId = $('#leadId').val();
                    const newStatus = $('#newStatus').val();
                    const conversionType = $('#conversionType').val();
                    const comment = $('#comment').val();
                    const remindDate = $('#remindDate').val();
                    const remindTime = $('#remindTime').val();
                    const prj_id = $('#prj_id').val();
                    const prop_size = $('#prop_size').val();
                    const final_price = $('#final_price').val();
                    const app_name = $('#app_name').val();
                    const app_contact = $('#app_contact').val();
                    const app_city = $('#app_city').val();
                    const app_dob = $('#app_dob').val();
                    const app_doa = $('#app_doa').val();
                    const visitProjects = $('#visitProjects').val();
                
                    if (!newStatus) 
                    {
                        flasher.error('Please select a new status');
                        return;
                    }
                    const visitStatuses = ['VISIT SCHEDULED', 'VISIT DONE'];
                    if (visitStatuses.includes(newStatus)) 
                    {
                        if (!visitProjects || visitProjects.length === 0) 
                        {
                            flasher.error('Please select at least one project for the visit');
                            return;
                        }
                    }

                    if (newStatus === 'CONVERTED' && !conversionType) 
                    {
                        flasher.error('Please select a conversion type');
                        return;
                    }

                    if (['CALL SCHEDULED', 'VISIT SCHEDULED', 'INTERESTED'].includes(newStatus)) 
                    {
                        if (!remindDate) 
                        {
                            flasher.error('Please select a reminder date');
                            return;
                        }
                        if (!remindTime) 
                        {
                            flasher.error('Please select a reminder time');
                            return;
                        }

                        const reminderDateTime = new Date(`${remindDate}T${remindTime}`);
                        if (reminderDateTime < new Date()) 
                        {
                            flasher.error('Reminder date/time cannot be in the past');
                            return;
                        }
                    }
                    const submitBtn = $('#statusUpdateModal').find('.btn-primary');
                    submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Updating...');
                    
                    const formData = {
                        _token: '{{ csrf_token() }}',
                        leadId: leadId,
                        newStatus: newStatus,
                        conversionType: conversionType,
                        comment: comment,
                        remindDate: remindDate,
                        remindTime: remindTime,
                        prj_id: prj_id,
                        prop_size: prop_size,
                        final_price: final_price,
                        app_name: app_name,
                        app_contact: app_contact,
                        app_city: app_city,
                        app_dob: app_dob,
                        app_doa: app_doa
                    };
                    if (visitStatuses.includes(newStatus) && visitProjects && visitProjects.length > 0) {
                        formData.visitProjects = visitProjects;
                    }
                    $.ajax({
                        url: '{{ route("lead.updateStatus") }}',
                        type: 'POST',
                        data: formData,
                        success: function(response) {
                            if (response.success) {
                                flasher.success(response.message);
                                setTimeout(() => {
                                    $('#statusUpdateModal').modal('hide');
                                    location.reload();
                                }, 1000);
                            } else {
                                flasher.error(response.message);
                                submitBtn.prop('disabled', false).text('Update Status');
                            }
                        },
                        error: function(xhr) {
                            let errorMessage = 'Error updating status';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            } else if (xhr.statusText) {
                                errorMessage += `: ${xhr.statusText}`;
                            }
                            flasher.error(errorMessage);
                            submitBtn.prop('disabled', false).text('Update Status');
                        }
                    });
                }
                function formatDateDMY(dateStr) 
                {
                    const date = new Date(dateStr);
                    const day = String(date.getDate()).padStart(2, '0');
                    const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", 
                                        "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
                    const month = monthNames[date.getMonth()];
                    const year = date.getFullYear();
                    return `${day}-${month}-${year}`;
                }

                function showComment(leadId)
                {
                    var commentsModal = new bootstrap.Modal(document.getElementById('commentsModal'));
                    commentsModal.show();
                    
                    $.ajax({
                        url: '/lead/' + leadId + '/comments',
                        type: 'GET',
                        dataType: 'json',
                        success: function(response) 
                        {
                            var html = '';
                            if (response.comments.length > 0) 
                            {
                                html += `
                                    <div class="table-responsive">
                                    <table class="table table-borderless table-hover align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="py-2">#</th>
                                                <th class="py-2">Comment</th>
                                                <th class="py-2">Status</th>
                                                <th class="py-2">Reminder</th>
                                                <th class="py-2">Agent</th>
                                                <th class="py-2">Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                `;

                                response.comments.forEach(function(comment, index) {
                                    html += `
                                        <tr>
                                            <td class="py-2">${index + 1}</td>
                                            <td class="py-2">${comment.comment}</td>
                                            <td class="py-2"><span class="cust-badge bg-${comment.status === 'Positive' ? 'success' : (comment.status === 'Negative' ? 'danger' : 'info')}">${comment.status || '-'}</span></td>
                                            <td class="py-2">${comment.remind_date ? formatDateDMY(comment.remind_date) : '-'}</td>
                                            <td class="py-2">${comment.user_name || 'System'}</td>
                                            <td class="py-2">${formatDateDMY(comment.created_date)}</td>
                                        </tr>
                                    `;
                                });

                                html += `
                                        </tbody>
                                    </table>
                                    </div>
                                `;
                            } else {
                                html = `
                                    <div class="text-center py-4">
                                        <h5 class="mb-1">No comments found</h5>
                                        <p class="text-muted">This lead doesn't have any comments yet.</p>
                                    </div>
                                `;
                            }

                            $('#commentsModalBody').html(html);
                        },
                        error: function() {
                            $('#commentsModalBody').html(`
                                <div class="text-center py-4">
                                    <h5 class="mb-1">Error loading comments</h5>
                                    <p class="text-muted">Please try again later.</p>
                                    <button class="btn btn-primary" onclick="showComment(${leadId})">
                                        <i class="fas fa-sync-alt me-1"></i> Retry
                                    </button>
                                </div>
                            `);
                        }
                    });
                }

                $(function() {
                    $('[data-bs-toggle="tooltip"]').tooltip();
                });
                document.addEventListener('DOMContentLoaded', function() 
                {
                    let chart;
                    const currentYear = document.getElementById('year-filter').value;
                    initChart(currentYear);
                    document.getElementById('year-filter').addEventListener('change', function() 
                    {
                        const selectedYear = this.value;
                        initChart(selectedYear);
                    });
                    document.querySelectorAll('.time-period-btn').forEach(btn => {
                        btn.addEventListener('click', function() 
                        {
                            document.querySelectorAll('.time-period-btn').forEach(b => {
                                b.classList.remove('active', 'btn-primary');
                                b.classList.add('btn-outline-light', 'text-dark');
                            });
                            this.classList.add('active', 'btn-primary');
                            this.classList.remove('btn-outline-light', 'text-dark');
                            const period = this.getAttribute('data-period');
                        });
                    });
                    
                    document.querySelectorAll('.export-btn').forEach(btn => {
                        btn.addEventListener('click', function(e) 
                        {
                            e.preventDefault();
                            const type = this.getAttribute('data-type');
                            const year = document.getElementById('year-filter').value;
                            
                            if (type === 'print') 
                            {
                                window.print();
                            } 
                            else
                            {
                                exportChartData(year, type);
                            }
                        });
                    });
                    function initChart(year)
                    {
                        fetch(`/dashboard/get-chart-data?year=${year}`)
                            .then(response => {
                                if (!response.ok) 
                                {
                                    throw new Error('Network response was not ok');
                                }
                                return response.json();
                            })
                            .then(data => {
                                renderChart(year, data);
                            })
                            .catch(error => {
                                console.error('Error fetching chart data:', error);
                            });
                    }
                    
                    function renderChart(year, chartData) 
                    {
                        const chartOptions = {
                            chart: {
                            type: 'bar',
                            height: 350,
                            stacked: false,
                            toolbar: { show: false },
                            animations: {
                                enabled: true,
                                easing: 'easeinout',
                                speed: 800
                            },
                            events: {
                                    dataPointMouseEnter: function(event, chartContext, config) 
                                    {
                                        const tooltip = document.getElementById('chart-tooltip');
                                        const month = config.w.globals.labels[config.dataPointIndex];
                                        const value = config.w.config.series[config.seriesIndex].data[config.dataPointIndex];

                                        document.getElementById('tooltip-title').textContent = month;
                                        document.getElementById('tooltip-value').textContent = value;
                                        document.getElementById('tooltip-bullet').style.backgroundColor = config.w.config.colors[config.seriesIndex];

                                        const chartRect = document.querySelector("#lead-monthly-report").getBoundingClientRect();
                                        const offsetX = event.clientX - chartRect.left;
                                        const offsetY = event.clientY - chartRect.top;

                                        tooltip.classList.remove('d-none');
                                        tooltip.style.left = (offsetX - tooltip.offsetWidth / 2) + 'px';
                                        tooltip.style.top = (offsetY - tooltip.offsetHeight - 10) + 'px';
                                    },
                                    dataPointMouseLeave: function() 
                                    {
                                        document.getElementById('chart-tooltip').classList.add('d-none');
                                    }
                                }
                            },
                            series: [
                                { 
                                    name: year, data: chartData
                                }
                            ],
                            colors: ['#4e54c8'],
                            dataLabels: { enabled: false },
                            plotOptions: {
                                bar: 
                                {
                                    horizontal: false,
                                    columnWidth: '55%',
                                    endingShape: 'rounded',
                                    borderRadius: 4,
                                },
                            },
                            stroke: {
                                show: true,
                                width: 2,
                                colors: ['transparent']
                            },
                            xaxis: {
                                categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                                axisBorder: { show: false },
                                axisTicks: { show: false },
                                labels: { 
                                    style: 
                                    { 
                                        colors: '#6c757d',
                                        fontFamily: 'inherit'
                                    } 
                                }
                            },
                            yaxis: {
                                labels: { 
                                    style: { 
                                        colors: '#6c757d',
                                        fontFamily: 'inherit'
                                    } 
                                },
                                min: 0,
                                forceNiceScale: true
                            },
                            grid: {
                                borderColor: '#f1f1f1',
                                strokeDashArray: 3,
                                padding: { top: 0, right: 10, bottom: 0, left: 10 }
                            },
                            tooltip: { enabled: false },
                            legend: { show: false },
                            fill: {
                                opacity: 1
                            },
                            responsive: [{
                                breakpoint: 768,
                                options: {
                                    plotOptions: {
                                        bar: {
                                            columnWidth: '70%'
                                        }
                                    }
                                }
                            }]
                        };

                        if (chart) 
                        {
                            chart.updateOptions(chartOptions);
                        } 
                        else 
                        {
                            chart = new ApexCharts(
                                document.querySelector("#lead-monthly-report"),
                                chartOptions
                            );
                            chart.render();
                        }
                    }
                    function exportChartData(year, type) 
                    {
                        if (type === 'csv') 
                        {
                            window.location.href = `/dashboard/export-chart?year=${year}&type=csv`;
                            return;
                        }
                        if (type === 'png' && chart) 
                        {
                            chart.dataURI().then(({ imgURI }) => {
                                const link = document.createElement('a');
                                link.href = imgURI;
                                link.download = `lead-conversion-${year}.png`;
                                document.body.appendChild(link);
                                link.click();
                                document.body.removeChild(link);
                            }).catch(error => {
                                console.error('Error exporting chart as PNG:', error);
                            });
                        }
                    }
                });
                $(document).ready(function() 
                {
                    $('.no-editDelete-right').click(function()
                    {
                        toastr.error("You don’t have permission to edit or delete this task");
                    });
                    function updateFormProgress() 
                    {
                        const requiredFields = $('#projectForm').find('[required]');
                        const totalRequired = requiredFields.length;
                        let completed = 0;
                        
                        requiredFields.each(function() 
                        {
                            if ($(this).val() && $(this).val().toString().trim() !== '') 
                            {
                                completed++;
                            }
                        });
                        
                        const progressPercentage = Math.round((completed / totalRequired) * 100);
                        $('#formProgressBar').css('width', progressPercentage + '%');
                        $('#completedFieldsBadge').text(progressPercentage + '% Complete');
                        $('#formProgressText').text(progressPercentage === 100 ? 'Ready to submit!' : `${completed} of ${totalRequired} required fields completed`);
                        
                        const tabs = ['basic', 'location', 'media', 'specifications', 'amenities', 'contact'];
                        tabs.forEach(tab => {
                            const tabFields = $(`#${tab}-info`).find('[required]');
                            let tabCompleted = 0;
                            
                            tabFields.each(function() 
                            {
                                if ($(this).val() && $(this).val().toString().trim() !== '') 
                                {
                                    tabCompleted++;
                                }
                            });
                            
                            const tabPercentage = tabFields.length > 0 ? Math.round((tabCompleted / tabFields.length) * 100) : 100;
                            if (tabPercentage === 100) 
                            {
                                $(`#${tab}-tab`).addClass('text-success');
                            } 
                            else 
                            {
                                $(`#${tab}-tab`).removeClass('text-success');
                            }
                        });
                    }
                    
                    $('#projectForm').on('change keyup', 'input, select, textarea', function() 
                    {
                        updateFormProgress();
                    });
                    
                    $('#projectModal').on('shown.bs.modal', function() 
                    {
                        updateFormProgress();
                    });
                    
                    function setupFilePreview(inputId, previewId, isMultiple = false) 
                    {
                        const input = document.getElementById(inputId);
                        const preview = document.getElementById(previewId);
                        
                        input.addEventListener('change', function(e) 
                        {
                            preview.innerHTML = '';
                            
                            if (isMultiple && input.files.length > 0) 
                            {
                                preview.innerHTML = '<div class="d-flex flex-wrap gap-2"></div>';
                                const container = preview.querySelector('div');
                                
                                for (let i = 0; i < Math.min(input.files.length, 10); i++) 
                                {
                                    const file = input.files[i];
                                    const reader = new FileReader();
                                    
                                    reader.onload = function(event) 
                                    {
                                        const div = document.createElement('div');
                                        div.className = 'uploaded-image';
                                        div.innerHTML = `
                                            <img src="${event.target.result}" alt="${file.name}">
                                            <div class="remove-image" data-index="${i}">
                                                <i class="fas fa-times"></i>
                                            </div>
                                        `;
                                        container.appendChild(div);
                                    };
                                    
                                    reader.readAsDataURL(file);
                                }
                            } 
                            else if (input.files && input.files[0]) 
                            {
                                const file = input.files[0];
                                const reader = new FileReader();
                                
                                reader.onload = function(e) 
                                {
                                    if (file.type.match('image.*')) 
                                    {
                                        preview.innerHTML = `
                                            <div class="position-relative">
                                                <img src="${e.target.result}" class="img-fluid rounded" alt="${file.name}">
                                                <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2 remove-file">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                            <div class="mt-2 small text-truncate">${file.name}</div>
                                        `;
                                    } 
                                    else if (file.type === 'application/pdf') 
                                    {
                                        preview.innerHTML = `
                                            <div class="d-flex align-items-center justify-content-center p-3 bg-light rounded">
                                                <i class="far fa-file-pdf fa-3x text-danger me-3"></i>
                                                <div>
                                                    <div class="fw-medium">${file.name}</div>
                                                    <div class="small text-muted">${(file.size / 1024).toFixed(1)} KB</div>
                                                </div>
                                                <button type="button" class="btn btn-sm btn-danger ms-auto remove-file">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        `;
                                    } 
                                    else 
                                    {
                                        preview.innerHTML = `
                                            <div class="d-flex align-items-center justify-content-center p-3 bg-light rounded">
                                                <i class="far fa-file fa-3x text-primary me-3"></i>
                                                <div>
                                                    <div class="fw-medium">${file.name}</div>
                                                    <div class="small text-muted">${(file.size / 1024).toFixed(1)} KB</div>
                                                </div>
                                                <button type="button" class="btn btn-sm btn-danger ms-auto remove-file">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        `;
                                    }
                                };
                                
                                reader.readAsDataURL(file);
                            }
                        });

                        preview.addEventListener('click', function(e) 
                        {
                            if (e.target.closest('.remove-file') || e.target.closest('.remove-image')) 
                            {
                                e.preventDefault();
                                input.value = '';
                                preview.innerHTML = `
                                    <div class="upload-placeholder">
                                        <i class="fas fa-image fs-4"></i>
                                        <span class="d-block mt-2">Upload ${inputId.replace(/_/g, ' ')}</span>
                                    </div>
                                `;
                                updateFormProgress();
                            }
                        });
                    }
                    setupFilePreview('logo', 'logoPreview');
                    setupFilePreview('cover_image', 'coverImagePreview');
                    setupFilePreview('floor_plan', 'floorPlanPreview');
                    setupFilePreview('site_map', 'siteMapPreview');
                    setupFilePreview('price_list', 'priceListPreview');
                    setupFilePreview('brochure', 'brochurePreview');
                    setupFilePreview('gallery_images', 'galleryPreviews', true);
                    $('#addCustomAmenity').click(function() 
                    {
                        const amenity = $('#customAmenityInput').val().trim();
                        if (amenity)
                        {
                            const badge = $(`
                                <div class="custom-amenity-badge">
                                    <input type="hidden" name="amenities[]" value="${amenity}">
                                    ${amenity}
                                    <span class="remove-amenity">
                                        <i class="fas fa-times"></i>
                                    </span>
                                </div>
                            `);
                            
                            $('#customAmenitiesContainer').append(badge);
                            $('#customAmenityInput').val('');
                            
                            badge.find('.remove-amenity').click(function() 
                            {
                                badge.remove();
                            });
                        }
                    });
                    
                    $('#customAmenityInput').keypress(function(e) 
                    {
                        if (e.which === 13) 
                        {
                            $('#addCustomAmenity').click();
                            return false;
                        }
                    });
                });

                function applyWebsiteStyles(styles) 
                {
                    function hexToRgb(hex) 
                    {
                        const r = parseInt(hex.slice(1, 3), 16);
                        const g = parseInt(hex.slice(3, 5), 16);
                        const b = parseInt(hex.slice(5, 7), 16);
                        return `${r}, ${g}, ${b}`;
                    }
                    document.documentElement.style.setProperty('--primary', styles.primary_color || '#0d1b2a');
                    document.documentElement.style.setProperty('--primary-rgb', hexToRgb(styles.primary_color || '#0d1b2a'));
                    document.documentElement.style.setProperty('--secondary', styles.secondary_color || '#1b263b');
                    document.documentElement.style.setProperty('--accent', styles.accent_color || '#e0aa3e');
                    document.documentElement.style.setProperty('--text-color', styles.text_color || '#333333');
                    document.documentElement.style.setProperty('--light-bg', styles.light_bg || '#f9f9f9');
                    document.documentElement.style.setProperty('--border-radius', `${styles.border_radius || 16}px`);
                    if (styles.font_primary) 
                    {
                        const primaryFont = document.createElement('link');
                        primaryFont.href = `https://fonts.googleapis.com/css2?family=${encodeURIComponent(styles.font_primary)}:wght@300;400;500;600;700&display=swap`;
                        primaryFont.rel = 'stylesheet';
                        document.head.appendChild(primaryFont);
                        document.body.style.fontFamily = `'${styles.font_primary}', sans-serif`;
                    }

                    if (styles.font_secondary) 
                    {
                        const secondaryFont = document.createElement('link');
                        secondaryFont.href = `https://fonts.googleapis.com/css2?family=${encodeURIComponent(styles.font_secondary)}:wght@400;500;600;700&display=swap`;
                        secondaryFont.rel = 'stylesheet';
                        document.head.appendChild(secondaryFont);
                        
                        document.querySelectorAll('h1, h2, h3, h4, h5, h6').forEach(el => {
                            el.style.fontFamily = `'${styles.font_secondary}', serif`;
                        });
                    }

                    const buttons = document.querySelectorAll('.btn-premium, .btn');
                    buttons.forEach(btn => {
                        btn.style.borderRadius = 
                            styles.button_style === 'pill' ? '50px' : 
                            styles.button_style === 'square' ? '0' : 
                            'var(--border-radius)';
                    });
                    const navbar = document.querySelector('.navbar');
                    if (navbar) 
                    {
                        if (styles.nav_style === 'transparent') 
                        {
                            navbar.style.background = 'transparent';
                            navbar.style.boxShadow = 'none';
                        } 
                        else if (styles.nav_style === 'glass') 
                        {
                            navbar.style.background = 'rgba(var(--primary-rgb), 0.7)';
                            navbar.style.backdropFilter = 'blur(10px)';
                        } 
                        else 
                        {
                            navbar.style.background = 'rgba(var(--primary-rgb), 0.95)';
                        }
                    }
                    if (styles.custom_css) 
                    {
                        const style = document.createElement('style');
                        style.innerHTML = styles.custom_css;
                        document.head.appendChild(style);
                    }
                }
                document.getElementById('projectForm').addEventListener('submit', function(e) 
                {
                    e.preventDefault();
                    const formData = {
                        primary_color: this.elements.primary_color.value,
                        secondary_color: this.elements.secondary_color.value,
                        accent_color: this.elements.accent_color.value,
                        text_color: this.elements.text_color.value,
                        light_bg: this.elements.light_bg.value,
                        border_radius: this.elements.border_radius.value,
                        font_primary: this.elements.font_primary.value,
                        font_secondary: this.elements.font_secondary.value,
                        button_style: this.elements.button_style.value,
                        nav_style: this.elements.nav_style.value,
                        custom_css: this.elements.custom_css.value
                    };
                    saveStylesToBackend(formData);
                    applyWebsiteStyles(formData);
                });
                function loadSavedStyles() 
                {
                    fetch('/api/project-styles')
                        .then(response => response.json())
                        .then(applyWebsiteStyles);
                }

                document.addEventListener('DOMContentLoaded', loadSavedStyles);
                function applyBannerSettings(settings) 
                {
                    const hero = document.querySelector('.hero');
                    if (hero) 
                    {
                        hero.style.backgroundSize = settings.banner_size || 'cover';
                        hero.style.backgroundPosition = settings.banner_position || 'center';
                        hero.style.backgroundRepeat = settings.banner_repeat || 'no-repeat';
                        if (settings.banner_size === 'custom' && settings.banner_width && settings.banner_height) 
                        {
                            hero.style.backgroundSize = `${settings.banner_width}px ${settings.banner_height}px`;
                        }
                    }
                }

                function generateImageSizes(settings) 
                {
                    const style = document.createElement('style');
                    style.innerHTML = `
                        .card-img-container {
                            height: ${settings.thumb_height || 200}px;
                        }
                        
                        .gallery-item {
                            aspect-ratio: ${settings.thumb_width || 300} / ${settings.thumb_height || 200};
                        }
                        
                        .hero-slide img {
                            object-fit: ${settings.banner_size === 'contain' ? 'contain' : 'cover'};
                            object-position: ${settings.banner_position || 'center'};
                        }
                        
                        /* Apply thumbnail cropping */
                        .card-img-top {
                            object-fit: ${settings.thumb_crop === 'fill' ? 'cover' : 
                                        settings.thumb_crop === 'fit' ? 'contain' : 'cover'};
                        }
                    `;
                    document.head.appendChild(style);
                }

                document.getElementById('projectForm').addEventListener('submit', function(e) 
                {
                    e.preventDefault();
                    
                    const formData = {
                        banner_size: this.elements.banner_size.value,
                        banner_position: this.elements.banner_position.value,
                        banner_repeat: this.elements.banner_repeat.value,
                        thumb_width: this.elements.thumb_width.value,
                        thumb_height: this.elements.thumb_height.value,
                        thumb_crop: this.elements.thumb_crop.value,
                        thumb_quality: this.elements.thumb_quality.value
                    };
                    applyBannerSettings(formData);
                    generateImageSizes(formData);
                    saveImageSettings(formData);
                }); 
            </script>
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
                document.getElementById('lengthSelect').addEventListener('change', function() 
                {
                    const length = this.value;
                    const url = new URL(window.location.href);
                    url.searchParams.set('length', length);
                    url.searchParams.set('page', 1);
                    window.location.href = url.toString();
                });
            </script>
            <script>
                document.addEventListener("DOMContentLoaded", function () 
                {
                    $(document).on("select2:select select2:clear change", "#campaignFilter", function () {
                        let campaign = $(this).val();
                        let queryParams = new URLSearchParams(window.location.search);
                        if (campaign) 
                        {
                            queryParams.set("campaign", campaign);
                        } 
                        else 
                        {
                            queryParams.delete("campaign");
                        }
                        let url = "{{ route('leads.filter.leads') }}" + "?" + queryParams.toString();
                        // console.log("Redirecting to:", url);
                        window.location.href = url;
                    });
                });
                document.getElementById('lengthSelect').addEventListener('change', function() 
                {
                    const length = this.value;
                    const url = new URL(window.location.href);
                    url.searchParams.set('length', length);
                    url.searchParams.set('page', 1);
                    window.location.href = url.toString();
                });
                $(document).ready(function() 
                {
                    $('#table_filter input').attr('placeholder', 'Enter search term');
                    $('#table_filter input').on('keypress', function(e) 
                    {
                        if (e.which === 13) 
                        {
                            const searchValue = $(this).val();
                            const url = new URL(window.location.href);
                            url.searchParams.set('search', searchValue);
                            url.searchParams.set('page', 1);
                            window.location.href = url.toString();
                        }
                    });
                    $('#table_filter input').on('input', function() 
                    {
                        if ($(this).val() === '') 
                        {
                            window.location.href = '/lead/all-lead';
                        }
                    });
                    const urlParams = new URLSearchParams(window.location.search);
                    const searchParam = urlParams.get('search');
                    if (searchParam) {
                        $('#table_filter input').val(searchParam);
                    }
                });

                $('#btnExportExcel').on('click', function() 
                {
                    var $btn = $(this);
                    var originalHtml = $btn.html();
                    $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> Please wait...');

                    setTimeout(function() 
                    {
                        var exportTable = document.createElement('table');
                        var thead = document.createElement('thead');
                        var tbody = document.createElement('tbody');
                        var headerRow = document.createElement('tr');

                        $('#table thead th').each(function(index) 
                        {
                            var thText = $(this).text().trim();
                            if (!$(this).hasClass('no-sort')) 
                            {
                                var th = document.createElement('th');
                                th.textContent = thText;
                                headerRow.appendChild(th);
                            }
                        });
                        thead.appendChild(headerRow);
                        exportTable.appendChild(thead);

                        $('#table tbody tr').each(function() 
                        {
                            var tr = document.createElement('tr');
                            $(this).find('td').each(function(index) 
                            {
                                if ((index > 0 || !$(this).find('input[type="checkbox"]').length)) 
                                {
                                    var td = document.createElement('td');
                                    if ($(this).find('.comment-text').length) 
                                    {
                                        td.textContent = $(this).find('.comment-text').text().trim();
                                    } 
                                    else if ($(this).find('h6').length) 
                                    {
                                        td.textContent = $(this).find('h6').text().trim() + ' - ' + $(this).find('.text-muted').text().trim();
                                    } 
                                    else if ($(this).find('.cust-badge').length) 
                                    {
                                        td.textContent = $(this).find('.cust-badge').text().trim();
                                    } 
                                    else if ($(this).find('a').length && $(this).find('a').attr('href')?.startsWith('mailto:')) 
                                    {
                                        td.textContent = $(this).find('a').text().trim();
                                    } 
                                    else 
                                    {
                                        td.textContent = $(this).text().trim();
                                    }
                                    tr.appendChild(td);
                                }
                            });
                            tbody.appendChild(tr);
                        });
                        exportTable.appendChild(tbody);

                        var wb = XLSX.utils.table_to_book(exportTable, { sheet: "Reports" });
                        XLSX.writeFile(wb, "reports.xlsx");

                        $btn.prop('disabled', false).html(originalHtml);
                    }, 100);
                });

                $('#btnExportPDF').on('click', function() 
                {
                    var $btn = $(this);
                    var originalHtml = $btn.html();
                    $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> Please wait...');

                    setTimeout(function() 
                    {
                        var { jsPDF } = window.jspdf;
                        var doc = new jsPDF('l', 'pt', 'a4');
                        doc.setFontSize(16);
                        doc.text("Leads Report", 40, 40);
                        doc.setFontSize(10);
                        doc.text("Generated on: " + new Date().toLocaleString(), 40, 60);

                        var headers = [];
                        $('#table thead th').each(function() 
                        {
                            var thText = $(this).text().trim();
                            if (!$(this).hasClass('no-sort')) headers.push(thText);
                        });

                        var data = [];
                        $('#table tbody tr').each(function() 
                        {
                            var row = [];
                            $(this).find('td').each(function(index) 
                            {
                                if ((index > 0 || !$(this).find('input[type="checkbox"]').length)) 
                                {
                                    if ($(this).find('.comment-text').length) 
                                    {
                                        row.push($(this).find('.comment-text').text().trim());
                                    } 
                                    else if ($(this).find('h6').length) 
                                    {
                                        row.push($(this).find('h6').text().trim() + ' - ' + $(this).find('.text-muted').text().trim());
                                    } 
                                    else if ($(this).find('.cust-badge').length) 
                                    {
                                        row.push($(this).find('.cust-badge').text().trim());
                                    } 
                                    else if ($(this).find('a').length && $(this).find('a').attr('href')?.startsWith('mailto:')) 
                                    {
                                        row.push($(this).find('a').text().trim());
                                    } 
                                    else 
                                    {
                                        row.push($(this).text().trim());
                                    }
                                }
                            });
                            data.push(row);
                        });

                        doc.autoTable({
                            head: [headers],
                            body: data,
                            startY: 80,
                            styles: { fontSize: 8 },
                            headStyles: { fillColor: [75, 108, 183] }
                        });
                        doc.save("reports.pdf");
                        $btn.prop('disabled', false).html(originalHtml);
                    }, 100);
                });
            </script>
            <script>
                $(document).ready(function()
                {
                    $('.ReportbtnExportExcel').on('click', function() 
                    {
                        var $btn = $(this);
                        var originalHtml = $btn.html();
                        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> Please wait...');

                        setTimeout(function() 
                        {
                            var exportTable = document.createElement('table');
                            var thead = document.createElement('thead');
                            var tbody = document.createElement('tbody');
                            var headerRow = document.createElement('tr');

                            $('#table thead th').each(function(index) 
                            {
                                var thText = $(this).text().trim();
                                var th = document.createElement('th');
                                th.textContent = thText;
                                headerRow.appendChild(th);
                            });
                            thead.appendChild(headerRow);
                            exportTable.appendChild(thead);

                            $('#table tbody tr').each(function() 
                            {
                                var tr = document.createElement('tr');
                                $(this).find('td').each(function(index) 
                                {
                                    var td = document.createElement('td');
                                    td.textContent = $(this).text().trim();
                                    tr.appendChild(td);
                                });
                                tbody.appendChild(tr);
                            });
                            exportTable.appendChild(tbody);

                            var wb = XLSX.utils.table_to_book(exportTable, { sheet: "Leads" });
                            XLSX.writeFile(wb, "leads.xlsx");

                            $btn.prop('disabled', false).html(originalHtml);
                        }, 100);
                    });

                    $('.ReportbtnExportPDF').on('click', function() 
                    {
                        var $btn = $(this);
                        var originalHtml = $btn.html();
                        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> Please wait...');

                        setTimeout(function() 
                        {
                            var { jsPDF } = window.jspdf;
                            var doc = new jsPDF('l', 'pt', 'a4');
                            doc.setFontSize(16);
                            doc.text("Leads Report", 40, 40);
                            doc.setFontSize(10);
                            doc.text("Generated on: " + new Date().toLocaleString(), 40, 60);

                            var headers = [];
                            $('#table thead th').each(function() 
                            {
                                headers.push($(this).text().trim());
                            });

                            var data = [];
                            $('#table tbody tr').each(function() 
                            {
                                var row = [];
                                $(this).find('td').each(function(index) 
                                {
                                    row.push($(this).text().trim());
                                });
                                data.push(row);
                            });

                            doc.autoTable({
                                head: [headers],
                                body: data,
                                startY: 80,
                                styles: { fontSize: 8 },
                                headStyles: { fillColor: [75, 108, 183] }
                            });

                            doc.save("leads.pdf");
                            $btn.prop('disabled', false).html(originalHtml);
                        }, 100);
                    });
                    $('.select2-multiple').select2({
                        placeholder: "Search and select users to share with...",
                        allowClear: true,
                        width: '100%',
                        closeOnSelect: false
                    });
                });
                $(document).ready(function() 
                {
                    let selectedTaskId = null;
                    let selectedStatus = null;
                    function filterTasksByProject(projectId) 
                    {
                        if (!projectId || projectId === 'all') 
                        {
                            $('.task-row').show();
                            $('.filter-task-btn[data-filter="all"]').addClass('filter-btn-active');
                        } 
                        else 
                        {
                            $('.task-row').hide();
                            $('.task-row.project-task[data-project-id="' + projectId + '"]').show();
                            $('.filter-task-btn').removeClass('filter-btn-active');
                            $('.filter-task-btn[data-filter="project"]').addClass('filter-btn-active');
                        }
                    }

                    $(document).on('click', '.status-option', function () 
                    {
                        selectedStatus = $(this).data('status');
                        const dropdown = $(this).closest('.dropdown');
                        selectedTaskId = dropdown.find('.status-btn').data('task-id');

                        if(selectedStatus === 'completed') 
                        {
                            $('.completed-file').removeClass('d-none');
                        } 
                        else 
                        {
                            $('.completed-file').addClass('d-none');
                        }
                        $('#selectedStatusText').text(selectedStatus.replace('_', ' ').toUpperCase());
                        $('#statusComment').val('');
                        $('#statusCommentModal').modal('show');
                    });

                    $(document).on('click', '.update-project-status', function(e) 
                    {
                        e.preventDefault();
                        const projectId = $(this).data('project-id');
                        const status = $(this).data('status');
                        
                        $.ajax({
                            url: `/task/project/update-status/${projectId}`,
                            method: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                status: status
                            },
                            success: function(response) 
                            {
                                if (response.status === 200) 
                                {
                                    flasher.success(response.message);
                                    setTimeout(() => location.reload(), 1000);
                                } 
                                else
                                {
                                    flasher.error('Error', response.message || 'Failed to update project status');
                                }
                            },
                            error: function() 
                            {
                                flasher.error('Error', 'Failed to update project status');
                            }
                        });
                    });

                    $(document).on('click', '.delete-project', function(e) 
                    {
                        e.preventDefault();
                        const projectId = $(this).data('project-id');
                        const projectName = $(this).data('project-name');
                        
                        Swal.fire({
                            title: 'Are you sure?',
                            html: `You are about to delete project: <strong>"${projectName}"</strong><br>This action cannot be undone!`,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Yes, delete it!',
                            backdrop: 'rgba(0,0,0,0.4)'
                        }).then((result) => {
                            if (result.isConfirmed) 
                            {
                                $.ajax({
                                    url: `/task/project/delete/${projectId}`,
                                    type: 'DELETE',
                                    data: {
                                        _token: '{{ csrf_token() }}'
                                    },
                                    success: function(response) 
                                    {
                                        if (response.status === 200) 
                                        {
                                            flasher.success(response.message);
                                            $(`.project-row[data-project-id="${projectId}"]`).remove();
                                        } 
                                        else 
                                        {
                                            flasher.error('Error', response.message || 'Failed to delete project');
                                        }
                                    },
                                    error: function(xhr) 
                                    {
                                        flasher.error('Error', 'Failed to delete project');
                                    }
                                });
                            }
                        });
                    });

                    $('.filter-task-btn').click(function() 
                    {
                        $('.filter-task-btn').removeClass('filter-btn-active');
                        $(this).addClass('filter-btn-active');

                        const filter = $(this).data('filter');
                        const currentProjectFilter = $('#projectTaskFilter').val();
                        if (filter !== 'all' && filter !== 'project') 
                        {
                            $('#projectTaskFilter').val('all');
                        }
                        
                        $('.task-row').show();

                        if (filter === 'project') 
                        {
                            $('.individual-task').hide();
                            if (currentProjectFilter !== 'all') 
                            {
                                $('.project-task').hide();
                                $('.project-task[data-project-id="' + currentProjectFilter + '"]').show();
                            }
                        } 
                        else if (filter === 'individual') 
                        {
                            $('.project-task').hide();
                        } 
                        else if (filter === 'today') 
                        {
                            const today = new Date().toDateString();
                            $('.task-row').each(function() 
                            {
                                const dueDate = new Date($(this).find('td:eq(4) span').text()).toDateString();
                                if (dueDate !== today) $(this).hide();
                            });
                        } 
                        else if (filter === 'overdue') 
                        {
                            const today = new Date();
                            $('.task-row').each(function() 
                            {
                                const dueDate = new Date($(this).find('td:eq(4) span').text());
                                const taskStatus = $(this).data('task-status');
                                if (dueDate >= today || taskStatus === 'completed') 
                                {
                                    $(this).hide();
                                }
                            });
                        } 
                        else if (filter === 'pending') 
                        {
                            $('.task-row').each(function() 
                            {
                                const taskStatus = $(this).data('task-status');
                                if (taskStatus !== 'pending') 
                                {
                                    $(this).hide();
                                }
                            });
                        } 
                        else if (filter === 'processing') 
                        {
                            $('.task-row').each(function() 
                            {
                                const taskStatus = $(this).data('task-status');
                                if (taskStatus !== 'in_progress') 
                                {
                                    $(this).hide();
                                }
                            });
                        }
                        else if (filter === 'completed') 
                        {
                            $('.task-row').each(function() 
                            {
                                const taskStatus = $(this).data('task-status');
                                if (taskStatus !== 'completed') 
                                {
                                    $(this).hide();
                                }
                            });
                        }
                    });

                    $('#projectTaskFilter').change(function() 
                    {
                        const projectId = $(this).val();
                        
                        if (projectId === 'all') 
                        {
                            $('.task-row').show();
                            $('.filter-task-btn[data-filter="all"]').click();
                        } 
                        else 
                        {
                            $('.task-row').hide();
                            $('.task-row.project-task[data-project-id="' + projectId + '"]').show();
                            $('.filter-task-btn').removeClass('filter-btn-active');
                            $('.filter-task-btn[data-filter="project"]').addClass('filter-btn-active');
                        }
                    });

                    $('.task-search').on('keyup', function() 
                    {
                        const value = $(this).val().toLowerCase();
                        $('.task-row').filter(function() {
                            $(this).toggle($(this).find('.task-name').text().toLowerCase().indexOf(value) > -1);
                        });
                    });

                    $('.project-search').on('keyup', function()
                    {
                        const value = $(this).val().toLowerCase();
                        $('.project-row').filter(function() {
                            $(this).toggle($(this).find('strong').text().toLowerCase().indexOf(value) > -1);
                        });
                    });

                    $(document).on('click', '.view-project-tasks', function(e) 
                    {
                        e.preventDefault();
                        const projectId = $(this).data('project-id');
                        const projectName = $(this).closest('tr').find('strong').text().trim();
                        $('#dashboardTabs a[href="#tasks"]').tab('show');
                        $('#projectTaskFilter').val(projectId);
                        filterTasksByProject(projectId);
                    });

                    $(document).on('click', '.edit-project', function(e) 
                    {
                        e.preventDefault();
                        const projectId = $(this).data('project-id');
                        const projectName = $(this).data('project-name');
                        const projectDescription = $(this).data('project-description');
                        const projectStatus = $(this).data('project-status');
                        const projectPriority = $(this).data('project-priority');
                        
                        $('#edit_project_id').val(projectId);
                        $('#edit_project_name').val(projectName);
                        $('#edit_project_description').val(projectDescription);
                        $('#edit_project_status').val(projectStatus);
                        $('#edit_project_priority').val(projectPriority);
                        
                        $('#editProjectModal').modal('show');
                    });

                    $('#editProjectForm').on('submit', function(e) 
                    {
                        e.preventDefault();
                        const formData = $(this).serialize();
                        const projectId = $('#edit_project_id').val();
                        $.ajax({
                            url: `/task/project/update/${projectId}`,
                            type: 'POST',
                            data: formData,
                            beforeSend: function() 
                            {
                                $('#editProjectModal').modal('hide');
                            },
                            success: function(response) 
                            {
                                if (response.status === 200) 
                                {
                                    flasher.success(response.message);
                                    setTimeout(() => location.reload(), 1000);
                                } 
                                else 
                                {
                                    flasher.error('Error', response.message || 'Failed to update project');
                                }
                            },
                            error: function(xhr) 
                            {
                                flasher.error('Error', 'Failed to update project');
                            }
                        });
                    });
                });
            </script>
            <script>
                function togglePin(leadId, pinStatus) 
                {
                    let button = $('.pin-item').filter(function() 
                    {
                        return $(this).closest('tr').find('td:eq(1)').text().trim() == leadId;
                    }).first();

                    if (button.length === 0) 
                    {
                        button = $(event.target).closest('.pin-item');
                    }

                    const isPinning = pinStatus === 1;
                    const originalHTML = button.html();
                    button.html('<i class="fas fa-spinner fa-spin"></i>');
                    button.css('pointer-events', 'none');

                    $.ajax({
                        url: '{{ route("lead.toggle-pin") }}',
                        method: 'POST',
                        data: {
                            lead_id: leadId,
                            is_pinned: pinStatus,
                            _token: '{{ csrf_token() }}'
                        },
                        dataType: 'json',
                        success: function(data) 
                        {
                            if (data.status === 200) 
                            { 
                                button.toggleClass('pinned', isPinning);
                                button.attr('title', isPinning ? 'Unpin Lead' : 'Pin Lead');
                                button.html('<i class="fas fa-thumbtack"></i>');
                                if (isPinning) 
                                {
                                    if (!button.next('.pinned-badge').length) 
                                    {
                                        const badge = $('<span class="pinned-badge">Pinned</span>');
                                        button.after(badge);
                                    }
                                } 
                                else 
                                {
                                    button.next('.pinned-badge').remove();
                                }

                                toastr.success(data.message);
                                setTimeout(() => {
                                    location.reload();
                                }, 800);
                            } 
                            else 
                            {
                                toastr.error(data.message);
                                button.html(originalHTML);
                            }
                        },
                        error: function(xhr, status, error) 
                        {
                            toastr.error('An error occurred while updating pin status');
                            button.html(originalHTML);
                        },
                        complete: function() 
                        {
                            button.css('pointer-events', 'auto');
                        }
                    });
                }

                function setupPinHandlers() 
                {
                    $(document).on('click', '.pin-item', function() 
                    {
                        const button = $(this);
                        const leadId = button.closest('tr').find('td:eq(1)').text().trim();
                        const isCurrentlyPinned = button.hasClass('pinned');
                        const newPinStatus = isCurrentlyPinned ? 0 : 1;

                        togglePin(leadId, newPinStatus);
                    });
                }

                $(document).ready(function() 
                {
                    setupPinHandlers();
                });
            </script>
            <script>
                $(document).ready(function() 
                {
                    $('#modalTeamSelect').on('change', function() 
                    {
                        var teamId = $(this).val();
                        if (teamId)
                        {
                            loadTeamPoints(teamId);
                        } 
                        else 
                        {
                            $('#pointsContainer').html('<div class="alert alert-warning">Please select a team first</div>');
                        }
                    });

                    function loadTeamPoints(teamId) 
                    {
                        $.ajax({
                            url: "{{ route('mis.get.team.points') }}",
                            method: "GET",
                            data: {
                                team_id: teamId
                            },
                            beforeSend: function()
                            {
                                $('#pointsContainer').html(`
                                    <div class="text-center py-4">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <p class="mt-2">Loading team points...</p>
                                    </div>
                                `);
                            },
                            success: function(response) 
                            {
                                if (response.success && response.points.length > 0) 
                                {
                                    var html = `
                                        <div class="table-responsive">
                                            <table class="table table-bordered align-middle">
                                                <thead class="table-light text-center">
                                                    <tr>
                                                        <th style="width:10%">Week</th>
                                                        <th style="width:60%">Task / Goal</th>
                                                        <th style="width:20%">Target</th>
                                                        <th style="width:10%">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="target-rows">
                                                    <tr class="target-row">
                                                        <td class="text-center">
                                                            <input type="number" name="week[]" class="form-control week-input" value="1" min="1" max="53" required>
                                                        </td>
                                                        <td>
                                                            <select name="tasks[]" class="form-select" required>
                                                                <option value="">-- Select Task --</option>
                                                                ${response.points.map(point => 
                                                                    `<option value="${point}">${point}</option>`
                                                                ).join('')}
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <input type="number" name="targets[]" class="form-control" placeholder="Target" min="0" required>
                                                        </td>
                                                        <td class="text-center">
                                                            <div class="btn-group btn-group-sm gap-2" role="group">
                                                                <button type="button" class="btn btn-success add-row">
                                                                    <i class="fa-solid fa-plus"></i>
                                                                </button>
                                                                <button type="button" class="btn btn-danger remove-row">
                                                                    <i class="fa-solid fa-trash"></i>
                                                                </button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    `;
                                    $('#pointsContainer').html(html);
                                    initializeTargetRows();
                                } 
                                else 
                                {
                                    $('#pointsContainer').html(`
                                        <div class="alert alert-warning">
                                            <i class="fa fa-exclamation-triangle me-2"></i>
                                            No points found for this team. Please assign points to the team first.
                                        </div>
                                    `);
                                }
                            },
                            error: function() 
                            {
                                $('#pointsContainer').html(`
                                    <div class="alert alert-danger">
                                        <i class="fa fa-times-circle me-2"></i>
                                        Error loading team points. Please try again.
                                    </div>
                                `);
                            }
                        });
                    }

                    function initializeTargetRows() 
                    {
                        const container = document.querySelector('.target-rows');
                        
                        function updateTaskOptions() 
                        {
                            const selectedTasks = Array.from(container.querySelectorAll('select[name="tasks[]"]'))
                                .map(s => s.value)
                                .filter(v => v !== "");

                            container.querySelectorAll('select[name="tasks[]"]').forEach(select => {
                                const currentValue = select.value;
                                select.querySelectorAll('option').forEach(option => {
                                    if (option.value === "") return;
                                    if (option.value === currentValue) 
                                    {
                                        option.disabled = false;
                                    } 
                                    else if (selectedTasks.includes(option.value)) 
                                    {
                                        option.disabled = true;
                                    } 
                                    else 
                                    {
                                        option.disabled = false;
                                    }
                                });
                            });
                        }

                        container.addEventListener('click', function(e) 
                        {
                            const row = e.target.closest('.target-row');
                            if (e.target.closest('.add-row')) 
                            {
                                const clone = row.cloneNode(true);
                                clone.querySelectorAll('select[name="tasks[]"]').forEach(s => s.selectedIndex = 0);
                                clone.querySelectorAll('input[name="targets[]"]').forEach(i => i.value = '');
                                clone.querySelectorAll('input[name="week[]"]').forEach(i => i.value = row.querySelector('input[name="week[]"]').value);
                                container.appendChild(clone);
                                updateTaskOptions();
                            }

                            if (e.target.closest('.remove-row')) 
                            {
                                if (container.querySelectorAll('.target-row').length > 1) 
                                {
                                    row.remove();
                                    updateTaskOptions();
                                } 
                                else 
                                {
                                    toastr.error("At least one task is required.");
                                }
                            }
                        });

                        container.addEventListener('change', function(e) 
                        {
                            if (e.target.matches('select[name="tasks[]"]')) 
                            {
                                updateTaskOptions();
                            }
                        });

                        updateTaskOptions();
                    }

                    $('#setTargetModal').on('shown.bs.modal', function() 
                    {
                        var teamId = $('#modalTeamSelect').val();
                        if (teamId) 
                        {
                            loadTeamPoints(teamId);
                        }
                    });

                    $('#quickEntryBtn').click(function() 
                    {
                        $('#quickEntryModal').modal('show');
                    });
                });
                function openWABusiness(phone) 
                {
                    let business = "whatsapp-business://send?phone=91" + phone;
                    let normal = "https://wa.me/91" + phone;
                    window.location.href = business;
                    setTimeout(function () {
                    window.location.href = normal;
                    }, 1200);
                }
            </script>
            <script>
                $(document).ready(function() 
                {
                    const csrfToken = $('meta[name="csrf-token"]').attr('content');
                    toastr.options = {
                        "closeButton": true,
                        "debug": false,
                        "newestOnTop": true,
                        "progressBar": true,
                        "positionClass": "toast-top-right",
                        "preventDuplicates": false,
                        "onclick": null,
                        "showDuration": "300",
                        "hideDuration": "1000",
                        "timeOut": "5000",
                        "extendedTimeOut": "1000",
                        "showEasing": "swing",
                        "hideEasing": "linear",
                        "showMethod": "fadeIn",
                        "hideMethod": "fadeOut"
                    };
                    function setDefaultDates() 
                    {
                        const now = new Date();
                        const tomorrow = new Date(now);
                        tomorrow.setDate(tomorrow.getDate() + 1);
                        
                        $('#start_date').val(now.toISOString().slice(0, 16));
                        $('#end_date').val(tomorrow.toISOString().slice(0, 16));
                    }

                    function resetForm() 
                    {
                        $('#exhibitionForm')[0].reset();
                        $('#exhibition_id').val('');
                        $('#_method').val('POST');
                        $('#exhibitionModalLabel').html('<i class="fas fa-plus-circle me-2"></i>Create New Exhibition');
                        $('#saveExhibitionBtn').html('<i class="fas fa-save me-1"></i> Save Exhibition');
                        
                        $('.form-control').removeClass('is-invalid');
                        $('.invalid-feedback').text('');
                        
                        setDefaultDates();
                    }

                    $('#createExhibitionBtn').click(function() 
                    {
                        resetForm();
                        $('#exhibitionModal').modal('show');
                    });

                    $(document).on('click', '.edit-exhibition', function() 
                    {
                        const id = $(this).data('id');
                        const name = $(this).data('name');
                        const description = $(this).data('description');
                        const startDate = $(this).data('start-date');
                        const endDate = $(this).data('end-date');
                        const location = $(this).data('location');
                        const isActive = $(this).data('is-active');
                        
                        $('#exhibition_id').val(id);
                        $('#name').val(name);
                        $('#description').val(description);
                        $('#start_date').val(startDate);
                        $('#end_date').val(endDate);
                        $('#location').val(location);
                        $('#is_active').prop('checked', isActive == 1);
                        $('#_method').val('PUT');
                        
                        $('#exhibitionModalLabel').html('<i class="fas fa-edit me-2"></i>Edit Exhibition');
                        $('#saveExhibitionBtn').html('<i class="fas fa-sync-alt me-1"></i> Update Exhibition');
                        
                        $('#exhibitionModal').modal('show');
                    });

                    $('#exhibitionForm').submit(function(e) 
                    {
                        e.preventDefault();
                        
                        const formData = $(this).serialize();
                        const id = $('#exhibition_id').val();
                        const method = $('#_method').val();
                        
                        let url = '/exhibitions';
                        if (method === 'PUT' && id) 
                        {
                            url = '/exhibitions/' + id;
                        }
                        $('.form-control').removeClass('is-invalid');
                        $('.invalid-feedback').text('');
                        const startDate = new Date($('#start_date').val());
                        const endDate = new Date($('#end_date').val());
                        
                        if (endDate <= startDate) 
                        {
                            $('#end_date').addClass('is-invalid');
                            $('#end_date-error').text('End date must be after start date.');
                            return;
                        }
                        const saveBtn = $('#saveExhibitionBtn');
                        const originalText = saveBtn.html();
                        saveBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> ' + (method === 'PUT' ? 'Updating...' : 'Saving...'));
                        
                        $.ajax({
                            url: url,
                            type: 'POST',
                            data: formData,
                            dataType: 'json',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            success: function(response) 
                            {
                                // console.log(response);
                                if (response.success) 
                                {
                                    toastr.success(response.message || 'Operation completed successfully!');
                                    $('#exhibitionModal').modal('hide');
                                    setTimeout(function() 
                                    {
                                        location.reload();
                                    }, 1500);
                                } 
                                else 
                                {
                                    if (response.errors) 
                                    {
                                        $.each(response.errors, function(key, value) 
                                        {
                                            $('#' + key).addClass('is-invalid');
                                            $('#' + key + '-error').text(value[0]);
                                        });
                                    } 
                                    else 
                                    {
                                        toastr.error(response.message || 'Operation failed!');
                                    }
                                }
                            },
                            error: function(xhr) 
                            {
                                if (xhr.status === 422) 
                                {
                                    const errors = xhr.responseJSON.errors;
                                    $.each(errors, function(key, value) 
                                    {
                                        $('#' + key).addClass('is-invalid');
                                        $('#' + key + '-error').text(value[0]);
                                    });
                                } 
                                else if (xhr.status === 419) 
                                {
                                    toastr.error('Session expired. Please refresh the page.');
                                    setTimeout(function() 
                                    {
                                        location.reload();
                                    }, 2000);
                                } 
                                else 
                                {
                                    toastr.error('An error occurred. Please try again.');
                                }
                            },
                            complete: function() 
                            {
                                saveBtn.prop('disabled', false).html(originalText);
                            }
                        });
                    });

                    $(document).on('click', '.activate-exhibition', function() 
                    {
                        const id = $(this).data('id');
                        const name = $(this).data('name');
                        
                        Swal.fire({
                            title: 'Activate Exhibition?',
                            html: `Are you sure you want to activate <strong>${name}</strong>?<br><small>This will deactivate any currently active exhibition.</small>`,
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonColor: '#28a745',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: 'Yes, activate it!',
                            cancelButtonText: 'Cancel'
                        }).then((result) => {
                            if (result.isConfirmed) 
                            {
                                $.ajax({
                                    url: '/exhibitions/' + id + '/activate',
                                    type: 'POST',
                                    dataType: 'json',
                                    data: {
                                        _token: csrfToken
                                    },
                                    headers: {
                                        'X-CSRF-TOKEN': csrfToken,
                                        'X-Requested-With': 'XMLHttpRequest'
                                    },
                                    beforeSend: function() 
                                    {
                                        $('.activate-exhibition[data-id="' + id + '"]')
                                            .prop('disabled', true)
                                            .html('<i class="fas fa-spinner fa-spin"></i>');
                                    },
                                    success: function(response) 
                                    {
                                        if (response.success) 
                                        {
                                            toastr.success(response.message || 'Exhibition activated successfully!');
                                            setTimeout(function() {
                                                location.reload();
                                            }, 1500);
                                        } 
                                        else 
                                        {
                                            toastr.error(response.message || 'Failed to activate exhibition.');
                                        }
                                    },
                                    error: function(xhr) 
                                    {
                                        if (xhr.status === 419) 
                                        {
                                            toastr.error('Session expired. Please refresh the page.');
                                            setTimeout(function() 
                                            {
                                                location.reload();
                                            }, 2000);
                                        } 
                                        else 
                                        {
                                            toastr.error(xhr.responseJSON?.message || 'Failed to activate exhibition.');
                                        }
                                        $('.activate-exhibition[data-id="' + id + '"]')
                                            .prop('disabled', false)
                                            .html('<i class="fas fa-toggle-on"></i>');
                                    }
                                });
                            }
                        });
                    });

                    $(document).on('click', '.delete-exhibition', function()
                    {
                        const id = $(this).data('id');
                        const name = $(this).data('name');
                        const csrfToken = $('meta[name="csrf-token"]').attr('content');
                        Swal.fire({
                            title: 'Are you sure?',
                            html: `You are about to delete <strong>${name}</strong>. This action cannot be undone!`,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: 'Yes, delete it!',
                            cancelButtonText: 'Cancel',
                            showLoaderOnConfirm: true,
                            preConfirm: function() {
                                return new Promise(function(resolve, reject) {
                                    $.ajax({
                                        url: '/exhibitions/' + id,
                                        type: 'POST',
                                        dataType: 'json',
                                        data: {
                                            _token: csrfToken,
                                            _method: 'DELETE'
                                        },
                                        headers: {
                                            'X-CSRF-TOKEN': csrfToken,
                                            'X-Requested-With': 'XMLHttpRequest'
                                        },
                                        success: function(response) {
                                            resolve(response);
                                        },
                                        error: function(xhr) 
                                        {
                                            if (xhr.status === 419) 
                                            {
                                                reject('Session expired. Please refresh the page.');
                                            } 
                                            else 
                                            {
                                                reject(xhr.responseJSON?.message || 'Delete failed');
                                            }
                                        }
                                    });
                                });
                            },
                            allowOutsideClick: () => !Swal.isLoading()
                        }).then((result) => {
                            if (result.isConfirmed) 
                            {
                                $('#exhibition-row-' + id).fadeOut(300, function() 
                                {
                                    $(this).remove();
                                });
                                
                                toastr.success('Exhibition deleted successfully!');
                                
                                setTimeout(function() 
                                {
                                    location.reload();
                                }, 2000);
                            }
                        }).catch((error) => {
                            if (error) 
                            {
                                Swal.fire('Error!', error, 'error');
                            }
                        });
                    });

                    $('#exhibitionModal').on('hidden.bs.modal', function() 
                    {
                        resetForm();
                    });

                    $('[title]').tooltip({
                        trigger: 'hover',
                        placement: 'top'
                    });
                    setDefaultDates();
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                });
            </script>
            <script>
                $(document).ready(function() 
                {
                    $('[title]').tooltip();
                    $('#selectAllLeads').on('change', function() 
                    {
                        $('.lead-checkbox').prop('checked', this.checked);
                    });

                    $('.lead-checkbox').on('change', function() 
                    {
                        if ($('.lead-checkbox:checked').length === $('.lead-checkbox').length) 
                        {
                            $('#selectAllLeads').prop('checked', true);
                        } 
                        else 
                        {
                            $('#selectAllLeads').prop('checked', false);
                        }
                    });

                    $(document).on('click', '.edit-lead-btn', function() 
                    {
                        const leadId = $(this).data('id');
                        $('#editLeadForm').attr('action', `/exhibitions/leads/${leadId}`);
                        $('#edit_lead_id').val(leadId);
                        $('#edit_name').val($(this).data('name'));
                        $('#edit_phone').val($(this).data('phone'));
                        $('#edit_whatsapp').val($(this).data('whatsapp'));
                        $('#edit_email').val($(this).data('email'));
                        $('#edit_company').val($(this).data('company'));
                        $('#edit_website').val($(this).data('website'));
                        $('#edit_fax').val($(this).data('fax'));
                        $('#edit_country').val($(this).data('country'));
                        $('#edit_address').val($(this).data('address'));
                        $('#edit_type').val($(this).data('type'));
                        $('#edit_description').val($(this).data('description'));
                        $('#edit_remarks').val($(this).data('remarks'));
                        const operatingCountry = $(this).data('operating-country');
                        const type = $(this).data('type');
                        if (operatingCountry) 
                        {
                            try 
                            {
                                const parsed = JSON.parse(operatingCountry);
                                $('#edit_operating_country').val(JSON.stringify(parsed, null, 2));
                            } 
                            catch (e) 
                            {
                                $('#edit_operating_country').val(operatingCountry);
                            }
                        }
                        else 
                        {
                            $('#edit_operating_country').val('');
                        }

                        const reminderDate = $(this).data('reminder-date');
                        if (reminderDate && reminderDate !== 'N/A') 
                        {
                            const date = new Date(reminderDate);
                            const formattedDate = date.toISOString().slice(0, 16);
                            $('#edit_reminder_date').val(formattedDate);
                        } 
                        else 
                        {
                            $('#edit_reminder_date').val('');
                        }
                        
                        const visitCard = $(this).data('visit-card');
                        const currentVisitCardDiv = $('#current_visit_card');
                        currentVisitCardDiv.empty();
                        
                        if (visitCard) 
                        {
                            let imageUrl = visitCard;
                            if (!visitCard.startsWith('http')) 
                            {
                                imageUrl = `/storage/${visitCard}`;
                            }
                            currentVisitCardDiv.html(`
                                <p class="mb-1"><strong>Current Visit Card:</strong></p>
                                <a href="${imageUrl}" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i> View Current
                                </a>
                            `);
                        }
                    });

                    $(document).on('click', '.convert-lead-btn', function() 
                    {
                        const leadId = $(this).data('id');
                        const leadName = $(this).data('name');
                        
                        Swal.fire({
                            title: 'Convert Lead to CRM?',
                            html: `Are you sure you want to convert <strong>"${leadName}"</strong> to CRM Lead?`,
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Yes, convert it!',
                            cancelButtonText: 'Cancel',
                            showLoaderOnConfirm: true,
                            preConfirm: () => {
                                return $.ajax({
                                    url: `/exhibitions/${leadId}/convert`,
                                    type: 'POST',
                                    data: {
                                        _token: '{{ csrf_token() }}'
                                    },
                                    dataType: 'json'
                                }).catch(error => {
                                    Swal.showValidationMessage(
                                        `Request failed: ${error.responseJSON?.message || 'Unknown error'}`
                                    );
                                });
                            }
                        }).then((result) => {
                            if (result.isConfirmed) 
                            {
                                if (result.value.success) 
                                {
                                    Swal.fire({
                                        title: 'Success!',
                                        text: result.value.message,
                                        icon: 'success',
                                        confirmButtonText: 'OK'
                                    }).then(() => {
                                        window.location.reload();
                                    });
                                } 
                                else 
                                {
                                    Swal.fire({
                                        title: 'Error!',
                                        text: result.value.message,
                                        icon: 'error',
                                        confirmButtonText: 'OK'
                                    });
                                }
                            }
                        });
                    });

                    $('#confirmConvertMultiple').on('click', function() 
                    {
                        const selectedLeads = [];
                        $('.lead-checkbox:checked').each(function() 
                        {
                            selectedLeads.push($(this).val());
                        });

                        if (selectedLeads.length === 0) 
                        {
                            Swal.fire({
                                title: 'No Leads Selected',
                                text: 'Please select at least one lead to convert.',
                                icon: 'warning',
                                confirmButtonText: 'OK'
                            });
                            return;
                        }

                        Swal.fire({
                            title: 'Convert Multiple Leads?',
                            html: `Are you sure you want to convert <strong>${selectedLeads.length}</strong> selected leads to CRM?`,
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Yes, convert them!',
                            cancelButtonText: 'Cancel',
                            showLoaderOnConfirm: true,
                            preConfirm: () => {
                                return $.ajax({
                                    url: '{{ route("exhibition.leads.convert.multiple") }}',
                                    type: 'POST',
                                    data: {
                                        _token: '{{ csrf_token() }}',
                                        lead_ids: selectedLeads
                                    },
                                    dataType: 'json'
                                }).catch(error => {
                                    Swal.showValidationMessage(
                                        `Request failed: ${error.responseJSON?.message || 'Unknown error'}`
                                    );
                                });
                            }
                        }).then((result) => {
                            if (result.isConfirmed) 
                            {
                                if (result.value.success) 
                                {
                                    Swal.fire({
                                        title: 'Success!',
                                        html: result.value.message + `<br>Converted: ${result.value.converted_count}<br>Failed: ${result.value.failed_count}`,
                                        icon: 'success',
                                        confirmButtonText: 'OK'
                                    }).then(() => {
                                        window.location.reload();
                                    });
                                } 
                                else 
                                {
                                    Swal.fire({
                                        title: 'Error!',
                                        text: result.value.message,
                                        icon: 'error',
                                        confirmButtonText: 'OK'
                                    });
                                }
                            }
                        });
                    });

                    // $(document).on('click', '.delete-lead-btn', function() 
                    // {
                    //     const leadId = $(this).data('id');
                    //     const deviceId = $(this).data('device-id');
                    //     const exhibitionId = $(this).data('exhibition-id');
                    //     const leadName = $(this).data('name');

                    //     Swal.fire({
                    //         title: 'Delete Lead?',
                    //         html: `Are you sure you want to delete <strong>"${leadName}"</strong>?<br>
                    //             <small class="text-danger">This action cannot be undone.</small>`,
                    //         icon: 'warning',
                    //         showCancelButton: true,
                    //         confirmButtonColor: '#d33',
                    //         cancelButtonColor: '#3085d6',
                    //         confirmButtonText: 'Yes, delete it!',
                    //         cancelButtonText: 'Cancel',
                    //         showLoaderOnConfirm: true,
                    //         preConfirm: () => {
                    //             return $.ajax({
                    //                 url: `/exhibitions/${leadId}/${exhibitionId}`,
                    //                 type: 'DELETE',
                    //                 data: {
                    //                     _token: '{{ csrf_token() }}'
                    //                 },
                    //                 dataType: 'json'
                    //             }).catch(error => {
                    //                 Swal.showValidationMessage(
                    //                     error.responseJSON?.message || 'Delete failed'
                    //                 );
                    //             });
                    //         }
                    //     }).then((result) => {
                    //         if (result.isConfirmed && result.value?.status === 200) 
                    //         {
                    //             Swal.fire('Deleted!', result.value.message, 'success')
                    //                 .then(() => location.reload());
                    //         }
                    //     });
                    // });


                    $('#editLeadForm').on('submit', function(e) {
                        e.preventDefault();

                        Swal.fire({
                            title: 'Updating Lead...',
                            text: 'Please wait while we update the lead.',
                            allowOutsideClick: false,
                            didOpen: () => Swal.showLoading()
                        });

                        const formData = new FormData(this);

                        $.ajax({
                            url: $(this).attr('action'),
                            type: 'POST',
                            data: formData,
                            processData: false,
                            contentType: false,
                            headers: {
                                'X-HTTP-Method-Override': 'PUT'
                            },
                            success: function(response) {
                                Swal.close();
                                Swal.fire({
                                    title: 'Success!',
                                    text: response.message,
                                    icon: 'success',
                                    confirmButtonText: 'OK'
                                }).then(() => location.reload());
                            },
                            error: function(xhr) 
                            {
                                Swal.close();
                                let errorMessage = 'Failed to update lead.';
                                if (xhr.responseJSON && xhr.responseJSON.errors) 
                                {
                                    errorMessage = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                                } 
                                else if (xhr.responseJSON && xhr.responseJSON.message) 
                                {
                                    errorMessage = xhr.responseJSON.message;
                                }
                                Swal.fire({
                                    title: 'Error!',
                                    html: errorMessage,
                                    icon: 'error',
                                    confirmButtonText: 'OK'
                                });
                            }
                        });
                    });

                    $(document).on('click', '.share-exhibition', function() 
                    {
                        const exhibitionId = $(this).data('id');
                        const name = $(this).data('name');

                        $('#shareExhibitionId').val(exhibitionId);

                        $('#shareExhibitionModal').modal('show');
                    });

                    $('#generateShareLinkBtn').on('click', function() 
                    {
                        const exhibitionId = $('#shareExhibitionId').val();
                        const csrfToken = $('meta[name="csrf-token"]').attr('content');

                        $(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Generating...');

                        $.ajax({
                            url: `/exhibitions/${exhibitionId}/share/create`,
                            type: 'POST',
                            data: {
                                _token: csrfToken,
                            },
                            dataType: 'json',
                            success: function(response) 
                            {
                                if (response.success) 
                                {
                                    $('#share_link_input').val(response.data.share_link);
                                    toastr.success(response.message);
                                } 
                                else 
                                {
                                    toastr.error(response.message || 'Failed to generate link');
                                }
                            },
                            error: function(xhr) 
                            {
                                toastr.error(xhr.responseJSON?.message || 'Something went wrong');
                            },
                            complete: function() 
                            {
                                $('#generateShareLinkBtn').prop('disabled', false).html('<i class="fas fa-link me-1"></i> Generate Share Link');
                            }
                        });
                    });
                });
            </script>
            
            <script>
                $('#postSaleModal').on('shown.bs.modal', function () 
                {
                    $('.selectPostSale').select2({
                        placeholder: 'Select',
                        width: '100%',
                        dropdownParent: $('#postSaleModal')
                    });
                });
                $('#postSaleModal').on('shown.bs.modal', function (){
                    $('.selectSalesPerson').select2({
                        placeholder: 'Select',
                        width: '100%',
                        dropdownParent: $('#postSaleModal')
                    });
                });
                $('#postSaleModal').on('shown.bs.modal', function (){
                    $('.selectProjectCat').select2({
                        placeholder: 'Select',
                        width: '100%',
                        dropdownParent: $('#postSaleModal')
                    });
                });
                $('#postSaleModal').on('shown.bs.modal', function (){
                    $('.selectPostSubCat').select2({
                        placeholder: 'Select',
                        width: '100%',
                        dropdownParent: $('#postSaleModal')
                    });
                });
                $('#postSaleModal').on('shown.bs.modal', function()
                {
                    $('.selectProject').select2({
                        placeholder: 'select',
                        width: '100%',
                        dropdownParent: $('#postSaleModal')
                    });
                });
            </script>
        </div>
    </body>
</html>