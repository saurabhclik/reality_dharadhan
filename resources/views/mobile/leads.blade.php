@extends('mobile.layouts.app')
@section('content')
<button class="back-button text-decoration-none" id="backButton" onclick="window.history.back()">
    <i class="fas fa-arrow-left"></i>
</button>
<div class="dashboard mt-md-0 pt-md-0 mt-5 pt-2">
    <div class="filter-section">
        <div class="filter-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Filters</h4>
            <div class="d-flex align-items-center">
                <button class="select-all-btn" id="selectAllBtn" style="display: none; background: none; border: none; font-size: 1.2rem; color: #CF5D3B;">
                    <i class="fas fa-check-double"></i>
                </button>
                <button class="share-btn me-2 ms-4" id="shareBtn" style="display: none; background: none; border: none; font-size: 1.2rem; color: #CF5D3B;">
                    <i class="fas fa-share-alt"></i>
                </button>
                <button class="filter-toggle" id="filterToggle" style="background: none; border: none; font-size: 1.2rem; color: #CF5D3B;">
                    <i class="fas fa-sliders-h"></i>
                </button>
            </div>
        </div>
        <div class="filter-controls" id="filterControls">
            <div class="filter-group">
                <label for="searchQuery">
                    <i class="fas fa-search"></i> Search
                </label>
                <input type="text" id="searchQuery" class="form-control" placeholder="Search anything about leads...">
            </div>
            <div class="filter-group">
                <label for="date-range">
                    <i class="far fa-calendar-alt"></i> Date Range
                </label>
                <div class="date-range-picker d-flex align-items-center">
                    <input type="date" id="startDate" class="form-control" placeholder="Start Date">
                    <span class="mx-2">to</span>
                    <input type="date" id="endDate" class="form-control" placeholder="End Date">
                </div>
            </div>
            <div class="d-flex gap-2 mt-2">
                <button class="apply-filters-btn flex-grow-1" id="applyFilters">
                    <i class="fas fa-check"></i> Apply
                </button>
                <button class="reset-filters-btn flex-grow-1" id="resetFilters">
                    <i class="fas fa-undo"></i> Reset
                </button>
            </div>
        </div>
    </div>
    <div class="lead-container" id="leadContainer">
    </div>
    <div class="loader-container text-center py-3" id="loader">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-2">Loading more leads...</p>
    </div>
    
    <div class="no-more-leads text-center py-3 text-muted" id="noMoreLeads" style="display: none;">
        <p>No leads to show</p>
    </div>
</div>
@include('mobile.modals.share')
@include('mobile.modals.status')
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
@endsection
@push('scripts')
<script>
    var viewCommentsBaseUrl = "{{ url('mobile/view-comments') }}";
    document.addEventListener('DOMContentLoaded', function() 
    {
        let filtersOpen = false;
        const filterToggle = document.getElementById('filterToggle');
        const icon = filterToggle.querySelector('i');
        const filterControls = document.getElementById('filterControls');
        const shareBtn = document.getElementById('shareBtn');
        const selectAllBtn = document.getElementById('selectAllBtn');
        
        const statusModal = document.getElementById('statusModal');
        const statusUpdateForm = document.getElementById('statusUpdateForm');
        const statusSelect = document.getElementById('statusSelect');
        const reminderFields = document.getElementById('reminderFields');
        const conversionFields = document.getElementById('conversionFields');

        function toggleFilters(e) 
        {
            e.stopPropagation();
            e.preventDefault();
            filtersOpen = !filtersOpen;
            if (filtersOpen) 
            {
                filterControls.classList.add('open');
            } 
            else 
            {
                filterControls.classList.remove('open');
            }
            icon.classList.toggle('fa-sliders-h', !filtersOpen);
            icon.classList.toggle('fa-xmark', filtersOpen);
            setTimeout(() => {
                if (filtersOpen) 
                {
                    filterControls.classList.add('open');
                }
            }, 100);
        }

        filterToggle.addEventListener('click', toggleFilters);
        filterToggle.addEventListener('touchstart', toggleFilters);

        document.addEventListener('click', function(e)
        {
            if (filtersOpen && !filterControls.contains(e.target) && !filterToggle.contains(e.target)) 
            {
                filtersOpen = false;
                filterControls.classList.remove('open');
                icon.classList.remove('fa-xmark');
                icon.classList.add('fa-sliders-h');
            }
        });

        const leadTypeRoutes = { 
            new: "{{ route('mobile.new-leads') }}",
            allocate: "{{ route('mobile.allocated-leads') }}",
            pending: "{{ route('mobile.pending-leads') }}",
            processing: "{{ route('mobile.processing-leads') }}",
            call: "{{ route('mobile.call-leads') }}",
            visit: "{{ route('mobile.visit-leads') }}",
            converted: "{{ route('mobile.converted-leads') }}",
            lost: "{{ route('mobile.lost-leads') }}",
            booked: "{{ route('mobile.booked') }}",
            all_lead: "{{ route('mobile.all-leads') }}",
            interested: "{{ route('mobile.interested-leads') }}",
            'not-picked': "{{ route('mobile.not-picked-leads') }}",
            'visit-done': "{{ route('mobile.visit-done-leads') }}",
            cancelled: "{{ route('mobile.cancelled-leads') }}",
            'not-interested': "{{ route('mobile.not-interested-leads') }}",
            'not-reachable': "{{ route('mobile.not-reachable-leads') }}",
            'wrong-number': "{{ route('mobile.wrong-number-leads') }}",
            future: "{{ route('mobile.future-leads') }}",
            'channel-partner': "{{ route('mobile.channel-partner-leads') }}",
            whatsapp: "{{ route('mobile.whatsapp') }}",
            partially_complete: "{{ route('mobile.partially-complete') }}",
            completed: "{{ route('mobile.completed') }}",
            transfer: "{{ route('mobile.transfer') }}",
            'meeting-scheduled': "{{ route('mobile.meeting-scheduled-leads') }}" 
        };

        const leadContainer = document.getElementById('leadContainer');
        const loader = document.getElementById('loader');
        const noMoreLeads = document.getElementById('noMoreLeads');
        let isLoading = false;
        let currentPage = 1;
        let hasMoreLeads = {{ $hasMoreInitial ? 'true' : 'false' }};
        let totalLeadsFetched = {{ count($initialLeads ?? []) }};
        const maxLeads = {{ $totalLeadsCount ?? 0 }};
        const initialLeads = @json($initialLeads ?? []);
        let activeFilters = {};
        let selectedLeads = [];
        let currentLeadType = '{{ $leadType ?? "new" }}';
        if (initialLeads.length === 0) 
        {
            noMoreLeads.style.display = 'block';
            hasMoreLeads = false;
            loader.style.display = 'none';
        }
        else 
        {
            renderLeads(initialLeads);
            loader.style.display = 'none';
            if (!hasMoreLeads && initialLeads.length > 0) 
            {
                noMoreLeads.style.display = 'block';
            }
        }

        fetchUsers();
        statusSelect.addEventListener('change', function() 
        {
            const selectedStatus = this.value;
            const showReminderFields = ['CALL SCHEDULED', 'VISIT SCHEDULED', 'FUTURE LEAD'].includes(selectedStatus);
            reminderFields.style.display = 'block'; 
        });
        statusUpdateForm.addEventListener('submit', function(e) 
        {
            e.preventDefault();
            updateLeadStatus();
        });
        statusModal.addEventListener('click', function(e) 
        {
            if (e.target === statusModal) 
            {
                statusModal.style.display = 'none';
            }
        });

        document.getElementById('applyFilters').addEventListener('click', function() 
        {
            activeFilters = {
                search: document.getElementById('searchQuery').value,
                startDate: document.getElementById('startDate').value,
                endDate: document.getElementById('endDate').value
            };
            
            currentPage = 1;
            totalLeadsFetched = 0;
            leadContainer.innerHTML = '';
            hasMoreLeads = true;
            noMoreLeads.style.display = 'none';
            clearSelection();
            loadMoreLeads();
        });

        document.getElementById('resetFilters').addEventListener('click', function() 
        {
            document.getElementById('searchQuery').value = '';
            document.getElementById('startDate').value = '';
            document.getElementById('endDate').value = '';
            activeFilters = {};
            currentPage = 1;
            totalLeadsFetched = 0;
            leadContainer.innerHTML = '';
            hasMoreLeads = true;
            noMoreLeads.style.display = 'none';
            clearSelection();
            loadMoreLeads();
        });

        document.getElementById('searchQuery').addEventListener('input', function() 
        {
            activeFilters.search = this.value;
            currentPage = 1;
            totalLeadsFetched = 0;
            leadContainer.innerHTML = '';
            hasMoreLeads = true;
            noMoreLeads.style.display = 'none';
            clearSelection();
            loadMoreLeads();
        });

        shareBtn.addEventListener('click', function() 
        {
            showShareModal();
        });

        document.getElementById('cancelShare').addEventListener('click', function() 
        {
            document.getElementById('shareModal').style.display = 'none';
        });

        document.getElementById('confirmShare').addEventListener('click', function() 
        {
            const userId = document.getElementById('shareUser').value;
            if (userId) 
            {
                shareLeads(userId);
            } 
            else 
            {
                toastr.error('Please select a user');
            }
        });

        document.getElementById('cancelStatusUpdate').addEventListener('click', function() 
        {
            statusModal.style.display = 'none';
        });

        selectAllBtn.addEventListener('click', function() 
        {
            const allCheckboxes = document.querySelectorAll('.lead-checkbox');
            const allSelected = Array.from(allCheckboxes).every(checkbox => checkbox.checked);
            const newCheckedState = !allSelected;

            allCheckboxes.forEach(checkbox => {
                checkbox.checked = newCheckedState;
                const leadId = checkbox.getAttribute('data-lead-id');
                const leadCard = checkbox.closest('.lead-card');

                if (checkbox.checked) 
                {
                    leadCard.classList.add('selected');
                    if (!selectedLeads.includes(leadId)) 
                    {
                        selectedLeads.push(leadId);
                    }
                } 
                else 
                {
                    leadCard.classList.remove('selected');
                    const index = selectedLeads.indexOf(leadId);
                    if (index !== -1) 
                    {
                        selectedLeads.splice(index, 1);
                    }
                }
            });

            updateSelectionUI();
        });

        function fetchUsers() 
        {
            fetch("{{ route('mobile.get-users') }}")
            .then(response => response.json())
            .then(users => {
                const userSelect = document.getElementById('shareUser');
                userSelect.innerHTML = '<option value="">Select User</option>';
                users.forEach(user => {
                    const option = document.createElement('option');
                    option.value = user.id;
                    option.textContent = user.name;
                    userSelect.appendChild(option);
                });
            });
        }

        function showShareModal() 
        {
            document.getElementById('shareModal').style.display = 'flex';
        }

        function shareLeads(userId) 
        {
            fetch("{{ route('mobile.share-leads') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    lead_ids: selectedLeads,
                    user_id: userId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) 
                {
                    toastr.success('Leads shared successfully!');
                    document.getElementById('shareModal').style.display = 'none';
                    clearSelection();
                    currentPage = 1;
                    leadContainer.innerHTML = '';
                    loadMoreLeads();
                } 
                else 
                {
                    toastr.error('Error sharing leads: ' + data.message);
                }
            })
            .catch(error => {
                toastr.error('Error sharing leads');
            });
        }

        function clearSelection() 
        {
            selectedLeads = [];
            updateSelectionUI();
            
            document.querySelectorAll('.lead-card.selected').forEach(card => {
                card.classList.remove('selected');
            });
            
            document.querySelectorAll('.lead-checkbox').forEach(checkbox => {
                checkbox.checked = false;
            });
        }

        function updateSelectionUI() 
        {
            const show = selectedLeads.length > 0;
            shareBtn.style.display = show ? 'block' : 'none';
            selectAllBtn.style.display = show ? 'block' : 'none';
        }

        function getFetchUrl() 
        {
            let baseUrl = leadTypeRoutes[currentLeadType] || leadTypeRoutes['new'];
            let url = `${baseUrl}?page=${currentPage}`;
            
            if (activeFilters.search) 
            {
                url += `&search=${encodeURIComponent(activeFilters.search)}`;
            }
            if (activeFilters.startDate) 
            {
                url += `&start_date=${activeFilters.startDate}`;
            }
            if (activeFilters.endDate) 
            {
                url += `&end_date=${activeFilters.endDate}`;
            }
            return url;
        }

        function debounce(func, wait) 
        {
            let timeout;
            return function executedFunction(...args) 
            {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

        const dashboard = document.querySelector('.dashboard');
        if (dashboard) 
        {
            dashboard.addEventListener('scroll', debounce(function() 
            {
                if (isLoading || !hasMoreLeads) 
                {
                    return;
                }

                const scrollTop = dashboard.scrollTop;
                const scrollHeight = dashboard.scrollHeight;
                const clientHeight = dashboard.clientHeight;

                if (scrollTop + clientHeight >= scrollHeight - 100) 
                {
                    loadMoreLeads();
                }
            }, 200));
        }

        function loadMoreLeads() 
        {
            if (isLoading || !hasMoreLeads) 
            {
                return;
            }
            
            isLoading = true;
            loader.style.display = 'block';
            noMoreLeads.style.display = 'none';

            const fetchUrl = getFetchUrl();
            console.log('Fetching URL:', fetchUrl);

            fetch(fetchUrl, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => {
                console.log('Response status:', response.status, response.statusText);
                if (!response.ok) 
                {
                    throw new Error('Network response was not ok: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                if (data.leads && data.leads.length > 0) 
                {
                    renderLeads(data.leads);
                    totalLeadsFetched += data.leads.length;
                    if (data.hasOwnProperty('current_page') && data.hasOwnProperty('last_page')) 
                    {
                        currentPage = data.current_page + 1;
                        hasMoreLeads = data.current_page < data.last_page;
                    } 
                    else if (data.hasOwnProperty('hasMore')) 
                    {
                        currentPage = data.nextPage || currentPage + 1;
                        hasMoreLeads = data.hasMore;
                    }
                    else 
                    {
                        currentPage++;
                        hasMoreLeads = data.leads.length === 10;
                    }
                    
                    if (!hasMoreLeads) 
                    {
                        noMoreLeads.style.display = 'block';
                        loader.style.display = 'none';
                    }
                    else 
                    {
                        loader.style.display = 'none';
                    }
                } 
                else 
                {
                    hasMoreLeads = false;
                    noMoreLeads.style.display = 'block';
                    loader.style.display = 'none';
                }
                isLoading = false;
            })
            .catch(error => {
                hasMoreLeads = false;
                noMoreLeads.style.display = 'block';
                loader.style.display = 'none';
                isLoading = false;
                toastr.error('Error loading leads: ' + error.message);
            });
        }

        function renderLeads(leads) 
        {
            leads.forEach(lead => {
                const leadHtml = `
                    <div class="lead-card mobile-card mt-3">
                        <div class="select-checkbox">
                            <input type="checkbox" class="lead-checkbox" data-lead-id="${lead.id}">
                        </div>
                        <div class="lead-header">
                            <div class="lead-badge-container">
                                <span class="lead-badge status-${lead.name ? lead.name.toLowerCase().replace(' ', '-') : '----'}">
                                    ${lead.name || '----'}
                                </span>
                                <span class="lead-time">${lead.lead_date ? formatDate(lead.lead_date) : '---'}</span>
                            </div>
                            <div class="lead-actions">
                                <a href="tel:${lead.phone || ''}" class="action-btn phone" target="_blank" rel="noopener noreferrer">
                                    <i class="fas fa-phone text-light cust-size pt-2"></i>
                                </a>
                                <a href="https://wa.me/${lead.phone || ''}" class="action-btn whatsapp" target="_blank" rel="noopener noreferrer">
                                    <i class="fa-brands fa-whatsapp text-light cust-size pt-2"></i>
                                </a>
                                <a class="action-btn edit-btn" data-lead-id="${lead.id}">
                                    <i class="fas fa-edit text-light cust-size pt-2"></i>
                                </a>
                                <a class="action-btn status-btn" data-lead-id="${lead.id}">
                                    <i class="fas fa-sync-alt text-light cust-size pt-2"></i>
                                </a>
                            </div>
                        </div>
                        <div class="lead-content">
                            <div class="lead-main-info">
                                <h3 class="lead-title">${lead.status ? lead.status.charAt(0).toUpperCase() + lead.status.slice(1).toLowerCase() : '---'}</h3>
                                <p class="lead-location">
                                    <i class="fas fa-map-marker-alt"></i> 
                                    ${lead.field1 || '---'}, ${lead.field2 || '---'}
                                </p>
                            </div>
                            <div class="lead-meta-grid">
                                <div class="meta-item">
                                    <span class="meta-label">Lead Manager</span>
                                    <span class="meta-value">${lead.agent || '---'}</span>
                                </div>
                                <div class="meta-item">
                                    <span class="meta-label">Source</span>
                                    <span class="meta-value">${lead.source || '---'}</span>
                                </div>
                                <div class="meta-item">
                                    <span class="meta-label">Type</span>
                                    <span class="meta-value">${lead.type || '---'}</span>
                                </div>
                                <div class="meta-item">
                                    <span class="meta-label">Category</span>
                                    <span class="meta-value">${lead.category || '---'}</span>
                                </div>
                                <div class="meta-item">
                                    <span class="meta-label">Sub Category</span>
                                    <span class="meta-value">${lead.sub_category || '---'}</span>
                                </div>
                                <div class="meta-item">
                                    <span class="meta-label">Classification</span>
                                    <span class="meta-value">${lead.classification || '---'}</span>
                                </div>
                                <div class="meta-item">
                                    <span class="meta-label">Campaign</span>
                                    <span class="meta-value text-wrap" style="word-break: break-word; white-space: normal;">
                                        ${lead.campaign || '---'}
                                    </span>
                                </div>
                                <div class="meta-item">
                                    <span class="meta-label">Project</span>
                                    <span class="meta-value">${lead.project_names}</span>
                                </div>
                                <div class="meta-item">
                                    <span class="meta-label">Address</span>
                                    <span class="meta-value">${lead.field3 || '---'}</span>
                                </div>
                            </div>
                            <div class="meta-item" style="padding:0px 10px;">
                                <span class="meta-label">Last Comment</span>
                                <span class="meta-value">${lead.last_comment || '---'}</span>
                            </div>
                            <div class="lead-footer">
                                <a href="${viewCommentsBaseUrl}/${lead.id}" class="view-comment-btn">
                                    <i class="fas fa-eye"></i> View Comments
                                </a>
                            </div>
                        </div>
                    </div>`;
                leadContainer.insertAdjacentHTML('beforeend', leadHtml);
            });

            addEventListeners();
        }

        function addEventListeners() 
        {
            document.querySelectorAll('.lead-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', function() 
                {
                    const leadId = this.getAttribute('data-lead-id');
                    const leadCard = this.closest('.lead-card');
                    
                    if (this.checked) 
                    {
                        leadCard.classList.add('selected');
                        if (!selectedLeads.includes(leadId)) 
                        {
                            selectedLeads.push(leadId);
                        }
                    } 
                    else 
                    {
                        leadCard.classList.remove('selected');
                        const index = selectedLeads.indexOf(leadId);
                        if (index !== -1) 
                        {
                            selectedLeads.splice(index, 1);
                        }
                    }
                    updateSelectionUI();
                });
            });

            document.querySelectorAll('.edit-btn').forEach(btn => {
                btn.addEventListener('click', function(e) 
                {
                    e.preventDefault();
                    const leadId = this.getAttribute('data-lead-id');
                    window.location.href = `/mobile/lead-edit/${leadId}`;
                });
            });

            document.querySelectorAll('.status-btn').forEach(btn => {
                btn.addEventListener('click', function(e) 
                {
                    e.preventDefault();
                    const leadId = this.getAttribute('data-lead-id');
                    showStatusModal(leadId);
                });
            });
        }

        function showStatusModal(leadId) 
        {
            statusSelect.value = '';
            document.getElementById('statusComment').value = '';
            document.getElementById('remindDate').value = '';
            document.getElementById('remindTime').value = '';
            document.getElementById('conversionType').value = 'Completed';
            reminderFields.style.display = 'block'; 
            conversionFields.style.display = 'none';
            statusModal.setAttribute('data-lead-id', leadId);
            statusModal.style.display = 'flex';
        }

        function updateLeadStatus() 
        {
            const leadId = statusModal.getAttribute('data-lead-id');
            const status = statusSelect.value;
            const comment = document.getElementById('statusComment').value;
            const remindDate = document.getElementById('remindDate').value;
            const remindTime = document.getElementById('remindTime').value;
            const conversionType = document.getElementById('conversionType').value;

            if (!status) 
            {
                toastr.error('Please select a status');
                return;
            }

            const requestData = {
                leadId: leadId,
                newStatus: status,
                comment: comment
            };

            if (remindDate && remindTime) 
            {
                requestData.remindDate = remindDate;
                requestData.remindTime = remindTime;
            }

            if (status === 'CONVERTED') 
            {
                requestData.conversionType = conversionType;
                requestData.app_name = document.getElementById('app_name')?.value || '';
                requestData.app_contact = document.getElementById('app_contact')?.value || '';
                requestData.app_city = document.getElementById('app_city')?.value || '';
                requestData.app_dob = document.getElementById('app_dob')?.value || '';
                requestData.app_doa = document.getElementById('app_doa')?.value || '';
                requestData.final_price = document.getElementById('final_price')?.value || '';
            }
            const projectSelect = document.getElementById('prj_id');
            const selectedProjects = Array.from(projectSelect.selectedOptions).map(opt => opt.value);
            requestData.prj_id = selectedProjects;
            requestData.prop_size = document.getElementById('prop_size')?.value || '';

            fetch("{{ route('mobile.update-lead-status') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(requestData)
            })
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                if (data.success) 
                {
                    toastr.success('Status updated successfully!');
                    statusModal.style.display = 'none';
                    currentPage = 1;
                    totalLeadsFetched = 0;
                    leadContainer.innerHTML = '';
                    hasMoreLeads = true;
                    noMoreLeads.style.display = 'none';
                    loadMoreLeads();
                }
                else 
                {
                    toastr.error('Error updating status: ' + data.message);
                }
            })
            .catch(error => {
                toastr.error('Error updating status: ' + error.message);
            });
        }

        function formatDate(dateString) 
        {
            try 
            {
                const date = new Date(dateString);
                if (isNaN(date.getTime())) 
                {
                    return '---';
                }
                const options = { weekday: 'short', month: 'short', day: 'numeric' };
                return date.toLocaleDateString('en-US', options);
            } 
            catch (e) 
            {
                return '---';
            }
        }
    });
</script>
@endpush