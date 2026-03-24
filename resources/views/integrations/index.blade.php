@extends('layouts.app')

@section('title', 'Integration | Pro-leadexpertz')

@section('content')
<style>
    .modal-slide-right .modal-dialog 
    {
        transform: translateX(100%);
        transition: transform 0.3s ease-in-out;
    }
    .modal-slide-right.show .modal-dialog 
    {
        transform: translateX(0);
    }
    .accordion-button 
    {
        font-weight: 500;
        color: #333;
        background-color: #f8f9fa !important;
        border-radius: 6px !important;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }
    .accordion-button:not(.collapsed) 
    {
        color: #a5c6e6ff;
        background-color: #e9f7ef !important;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    .accordion-item 
    {
        border-radius: 6px;
        overflow: hidden;
    }
    .modal-content 
    {
        border-radius: 12px;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
    }
    .modal-header 
    {
        border-radius: 12px 12px 0 0;
    }
    .list-group-item 
    {
        background: transparent;
        padding: 0.5rem 0;
    }
    .alert 
    {
        border-radius: 6px;
        font-size: 0.9rem;
    }
    .dataTables_paginate 
    {
        display:block !important;
    }

    .card 
    {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    
    .card:hover 
    {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.08) !important;
    }
    
    .modal-slide-right 
    {
        padding-right: 0 !important;
    }
    
    .modal-slide-right.show .modal-dialog 
    {
        transform: translateX(0);
    }
    
    .modal-content 
    {
        border-radius: 0;
        height: 100%;
        overflow-y: auto;
    }

    .avatar-sm 
    {
        width: 40px;
        height: 40px;
    }
    
    .avatar-xs 
    {
        width: 24px;
        height: 24px;
        font-size: 0.7rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    
    .avatar-group 
    {
        display: flex;
        flex-wrap: wrap;
    }
    
    .avatar-group .avatar 
    {
        margin-right: -8px;
        border: 2px solid #fff;
        position: relative;
    }
    
    .table th 
    {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 0.5px;
    }
    
    .btn 
    {
        border-radius: 6px;
        font-weight: 500;
    }
    
    .form-control, .input-group-text 
    {
        border-radius: 6px;
    }
    
    .badge 
    {
        border-radius: 4px;
        font-weight: 500;
    }
    
    .sticky-top 
    {
        position: sticky;
        top: 0;
        z-index: 10;
    }
    .modal-header 
    {
        height: 100%;
        border-radius: 0 !important;
    }
    .form-switch .form-check-input 
    {
        height: 0.8rem;
        width: 1.8rem;
    }

    .form-switch .form-check-input:checked 
    {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }

    .form-check-label 
    {
        font-size: 0.875rem;
        margin-left: 0.5rem;
    }
    .premium-feature-card 
    {
        position: relative;
        overflow: hidden;
    }
    
    .premium-feature-card::before 
    {
        content: "PREMIUM";
        position: absolute;
        top: 10px;
        right: -30px;
        background: linear-gradient(45deg, #ff6b6b, #ffa726);
        color: white;
        padding: 4px 30px;
        font-size: 0.7rem;
        font-weight: bold;
        transform: rotate(45deg);
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        z-index: 1;
    }
    
    .premium-feature-card .card-body 
    {
        opacity: 0.7;
        filter: grayscale(0.3);
    }
    
    .upgrade-banner 
    {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 10px;
        color: white;
        padding: 20px;
        margin-bottom: 30px;
    }
</style>
<div class="page-content">
    <div class="container">
        <div class="row mb-4">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Connected Services</h4>
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="#">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item active">Integrations</li>
                    </ol>
                </nav>
            </div>
        </div>
        @if(session('message'))
            <div class="alert alert-success">{{ session('message') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        <div class="row g-4">
            @foreach($activeIntegration as $active)
                @if($active->feature_name == 'housing')
                    @include('partial.housing')
                @elseif($active->feature_name == 'facebook')
                    @include('partial.facebook')
                @elseif($active->feature_name == 'gmail')
                    @include('partial.gmail')
                @elseif($active->feature_name == 'magic_bricks')
                    @include('partial.magic')
                @elseif($active->feature_name == '99_acres')
                    @include('partial.99acres')
                @elseif($active->feature_name == 'google_sheets')
                    @include('partial.google-sheets')
                @elseif($active->feature_name == 'google_form')
                    @include('partial.google-form')
                @endif
            @endforeach
            @foreach($inactiveIntegration as $key => $integration)
                <div class="col-xl-4 col-lg-4 col-md-6">
                    <div class="card premium-feature-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="avatar-sm flex-shrink-0">
                                    <span class="avatar-title bg-light text-primary rounded-circle fs-4">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5 class="card-title mb-1">{{ $integration->feature_name }}</h5>
                                    <p class="text-muted mb-0"><span class="badge bg-secondary">Premium</span></p>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" disabled>
                                    <label class="form-check-label text-muted">Auto Sync</label>
                                </div>
                            </div>
                            
                            <p class="text-muted small mb-3">
                                Connect your {{ $integration->feature_name }} account to sync leads (Premium Feature)
                            </p>
                            
                            <div class="row text-center mb-3">
                                <div class="col-6">
                                    <div class="border-end">
                                        <h5 class="text-muted mb-0">0</h5>
                                        <small class="text-muted">Leads Today</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <h5 class="text-muted mb-0">0</h5>
                                    <small class="text-muted">Total Leads</small>
                                </div>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <a href="{{route('premium.features', ['integrate' => true])}}" class="btn btn-outline-primary">
                                    <i class="fas fa-crown me-1"></i> Upgrade to Access
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach 
        </div>
    </div>
</div>
@include('modals.gmail-integration-guide')
@include('modals.fb-pages')
@include('modals.fb-accounts')
@include('modals.fb-token')
@include('modals.fb-campaign')
@include('modals.fb-integration-guide')
@include('modals.magic-brick-integration-guide')
@include('modals.99acres-integration-guide')

<script>
    document.addEventListener('DOMContentLoaded', function () 
    {
        const syncHousingBtn = document.getElementById('syncNowHousingBtn');
        const syncFbBtn = document.getElementById('syncNowFbBtn');
        const housingSyncResult = document.getElementById('housingSyncResult');
        const facebookSyncResult = document.getElementById('facebookSyncResult');

        syncHousingBtn?.addEventListener('click', function () 
        {
            syncHousingBtn.disabled = true;
            housingSyncResult.innerHTML = '<div class="text-primary">Syncing Housing leads... <span class="spinner-border spinner-border-sm"></span></div>';

            fetch("{{ route('integrations.housing.sync') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                syncHousingBtn.disabled = false;
                if (data.success) 
                {
                    if (data.inserted === 0) 
                    {
                        housingSyncResult.innerHTML = `<div class="alert alert-warning mt-2">No new Housing leads were added. Data may already be synced.</div>`;
                    } 
                    else 
                    {
                        housingSyncResult.innerHTML = `<div class="alert alert-success mt-2">Successfully synced ${data.inserted} Housing leads.</div>`;
                    }
                } 
                else if (data.error) 
                {
                    housingSyncResult.innerHTML = `<div class="alert alert-danger mt-2">${data.error}</div>`;
                }
            })
            .catch(error => {
                console.error(error);
                syncHousingBtn.disabled = false;
                housingSyncResult.innerHTML = '<div class="alert alert-danger mt-2">An unexpected error occurred during Housing sync.</div>';
            });
        });

        syncFbBtn?.addEventListener('click', function () 
        {
            const originalText = syncFbBtn.innerHTML;
            syncFbBtn.disabled = true;

            facebookSyncResult.innerHTML = `
                <div class="alert alert-info">
                    Syncing Facebook leads...<br>
                    <strong>Please wait, it may take up to 2 minutes. Be patient...</strong>
                    <div class="spinner-border spinner-border-sm text-primary ms-2" role="status"></div>
                </div>
            `;

            fetch("{{ route('integrations.facebook.sync') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 200) 
                {
                    setTimeout(() => {
                        checkFacebookSyncStatus();
                        syncFbBtn.disabled = false;
                        syncFbBtn.innerHTML = originalText;
                    }, 120000); 
                } 
                else 
                {
                    syncFbBtn.disabled = false;
                    syncFbBtn.innerHTML = originalText;
                    facebookSyncResult.innerHTML = `
                        <div class="alert alert-danger">
                            <strong>Sync Failed:</strong><br>
                            ${data.message}
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Facebook sync error:', error);
                syncFbBtn.disabled = false;
                syncFbBtn.innerHTML = originalText;
                facebookSyncResult.innerHTML = `
                    <div class="alert alert-danger">
                        An unexpected error occurred.
                    </div>
                `;
            });
        });

        function checkFacebookSyncStatus() 
        {
            fetch("{{ route('integrations.facebook.status') }}")
                .then(response => response.json())
                .then(data => {
                    if (data.status === 200) 
                    {
                        facebookSyncResult.innerHTML = `
                            <div class="alert alert-success">
                                <strong>Sync Status:</strong><br>
                                Today's Leads: ${data.data.leads_today}<br>
                                Total Leads: ${data.data.total_leads}<br>
                                Last Sync: ${data.data.last_sync_time || 'Never'}
                            </div>`;
                    } 
                    else 
                    {
                        facebookSyncResult.innerHTML = `
                            <div class="alert alert-danger">
                                <strong>Sync Failed:</strong><br>
                                ${data.message}<br>
                                ${data.data.error ? 'Error: ' + data.data.error : ''}
                            </div>`;
                    }
                })
                .catch(error => {
                    console.error('Status check error:', error);
                    facebookSyncResult.innerHTML = `
                        <div class="alert alert-danger">
                            <strong>Failed to check sync status:</strong><br>
                            ${error.message}
                        </div>`;
                });
        }

        document.querySelectorAll('.auto-sync-toggle').forEach(toggle => {
            toggle.addEventListener('change', function() {
                const integrationType = this.getAttribute('data-integration-type');
                const isEnabled = this.checked;
                const originalState = this.checked;
                this.disabled = true;
                
                fetch(`/integration/auto-sync/${integrationType}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({
                        auto_sync: isEnabled
                    })
                })
                .then(response => response.json())
                .then(data => {
                    this.disabled = false;
                    if (data.success) 
                    {
                        toastr.success(data.message);
                    } 
                    else 
                    {
                        this.checked = !originalState;
                        toastr.error(data.message);
                    }
                })
                .catch(error => {
                    // console.error('Error:', error);
                    this.disabled = false;
                    this.checked = !originalState;
                    toastr.error('An error occurred while updating auto sync setting');
                });
            });
        });

        const initTableSearch = (inputId, tableSelector) => {
            const searchInput = document.getElementById(inputId);
            if (searchInput) 
            {
                searchInput.addEventListener('keyup', function() 
                {
                    const filter = this.value.toLowerCase();
                    const table = document.querySelector(tableSelector);
                    const rows = table.getElementsByTagName('tr');
                    
                    for (let i = 1; i < rows.length; i++) 
                    {
                        const row = rows[i];
                        let found = false;
                        const cells = row.getElementsByTagName('td');
                        
                        for (let j = 0; j < cells.length; j++) 
                        {
                            const cell = cells[j];
                            if (cell.textContent.toLowerCase().indexOf(filter) > -1) 
                            {
                                found = true;
                                break;
                            }
                        }
                        row.style.display = found ? '' : 'none';
                    }
                });
            }
        };

        initTableSearch('pageSearch', '#managePagesModal table');
        initTableSearch('groupSearch', '#manageGroupsModal table');
        initTableSearch('accountSearch', '#manageAccountsModal table');

        document.querySelectorAll('#refreshPages, #refreshAccounts').forEach(btn => {
            btn.addEventListener('click', function() 
            {
                const icon = this.querySelector('i');
                const originalClass = icon.className;
                
                icon.className = 'fas fa-spinner fa-spin';
                this.disabled = true;

                setTimeout(() => {
                    icon.className = originalClass;
                    this.disabled = false;
                    const toastElement = document.createElement('div');
                    toastElement.className = 'toast align-items-center text-white bg-success position-fixed top-0 end-0 m-3';
                    toastElement.setAttribute('role', 'alert');
                    toastElement.innerHTML = `
                        <div class="d-flex">
                            <div class="toast-body">
                                <i class="fas fa-check-circle me-2"></i> Data refreshed successfully
                            </div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                        </div>
                    `;
                    document.body.appendChild(toastElement);
                    new bootstrap.Toast(toastElement).show();
                    setTimeout(() => toastElement.remove(), 3000);
                }, 1500);
            });
        });

        document.querySelectorAll('.btn-outline-secondary i.fa-eye').forEach(icon => {
            icon.addEventListener('click', function() 
            {
                const input = this.closest('.input-group').querySelector('input');
                if (input.type === 'password') 
                {
                    input.type = 'text';
                    this.className = 'fas fa-eye-slash';
                } 
                else 
                {
                    input.type = 'password';
                    this.className = 'fas fa-eye';
                }
            });
        });
        
        document.querySelectorAll('.btn-outline-secondary i.fa-copy').forEach(icon => {
            icon.addEventListener('click', function() 
            {
                const input = this.closest('.input-group').querySelector('input');
                input.select();
                document.execCommand('copy');
                const toastElement = document.createElement('div');
                toastElement.className = 'toast align-items-center text-white bg-info position-fixed top-0 end-0 m-3';
                toastElement.setAttribute('role', 'alert');
                toastElement.innerHTML = `
                    <div class="d-flex">
                        <div class="toast-body">
                            <i class="fas fa-copy me-2"></i> Copied to clipboard
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                `;
                document.body.appendChild(toastElement);
                new bootstrap.Toast(toastElement).show();
                
                setTimeout(() => toastElement.remove(), 2000);
            });
        });
    });
    
    document.getElementById('toggleToken')?.addEventListener('click', function () 
    {
        const input = document.getElementById('shortToken');
        if (input) 
        {
            input.type = input.type === 'password' ? 'text' : 'password';
        }
    });

    document.getElementById('generateTokenBtn')?.addEventListener('click', function () 
    {
        const shortTokenInput = document.getElementById('shortToken');
        const responseBox = document.getElementById('responseBox');
        const accessTokenInput = document.getElementById('access-token');
        const generateBtn = this;

        if (!shortTokenInput) 
        {
            toastr.error("Short token input not found");
            return;
        }

        const shortToken = shortTokenInput.value;
        responseBox.innerHTML = '';

        const originalBtnHTML = generateBtn.innerHTML;

        generateBtn.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span>Generating...`;
        generateBtn.disabled = true;

        fetch("{{ route('facebook.token.exchange') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            body: JSON.stringify({ short_lived_token: shortToken })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 200) 
            {
                responseBox.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
                if (accessTokenInput) 
                {
                    accessTokenInput.value = data.data;
                }
            } 
            else 
            {
                responseBox.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
            }
        })
        .catch(error => {
            responseBox.innerHTML = `<div class="alert alert-danger">Error: ${error.message}</div>`;
        })
        .finally(() => {
            generateBtn.innerHTML = originalBtnHTML;
            generateBtn.disabled = false;
        });
    });

    $(document).ready(function() 
    {
        $('#managePagesModal').on('show.bs.modal', function () 
        {
            const tbody = $('#fbPagesTableBody');
            tbody.html(`
                <tr>
                    <td colspan="5" class="text-center py-5">
                        <div class="spinner-border text-primary mb-3" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="text-muted">Loading pages...</p>
                    </td>
                </tr>
            `);

            if ($.fn.DataTable.isDataTable('#fbPagesTable')) 
            {
                $('#fbPagesTable').DataTable().destroy();
            }

            $.ajax({
                url: "{{ route('facebook.pages.fetch') }}",
                method: "POST",
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                success: function(response) 
                {
                    const pages = response.data;
                    tbody.empty();

                    if (!Array.isArray(pages) || pages.length === 0) 
                    {
                        tbody.html(`<tr><td colspan="5" class="text-center py-4 text-muted">No pages found.</td></tr>`);
                        return;
                    }

                    pages.forEach((page, index) => {
                        const tasksFormatted = page.tasks ? page.tasks.join('<br>') : '-';
                        const row = `
                            <tr>
                                <td class="ps-4">${index + 1}</td>
                                <td>${page.id}</td>
                                <td>${page.name}</td>
                                <td>${page.category || '-'}</td>
                                <td class="text-center">${tasksFormatted}</td>
                            </tr>
                        `;
                        tbody.append(row);
                    });
                    
                    $('#fbPagesTable').DataTable({
                        paging: true,
                        searching: false,
                        ordering: true,
                        info: true,
                        lengthChange: true,
                        pageLength: 10 
                    });
                },
                error: function(xhr, status, error) 
                {
                    tbody.html(`<tr><td colspan="5" class="text-center text-danger py-4">Failed to load pages.</td></tr>`);
                    toastr.error("Error loading pages: " + error);
                }
            });
        });

        $('#manageAccountsModal').on('show.bs.modal', function () 
        {
            const tbody = $('#fbGroupTableBody');
            tbody.html(`
                <tr>
                    <td colspan="6" class="text-center py-5">
                        <div class="spinner-border text-primary mb-3" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="text-muted">Loading ad accounts...</p>
                    </td>
                </tr>
            `);
            
            if ($.fn.DataTable.isDataTable('#fbGroupTable')) 
            {
                $('#fbGroupTable').DataTable().destroy();
            }

            $.ajax({
                url: "{{ route('facebook.group.fetch') }}",
                method: "POST",
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                success: function(response) 
                {
                    const groups = response.data;
                    tbody.empty();

                    if (!Array.isArray(groups) || groups.length === 0) 
                    {
                        tbody.html(`<tr><td colspan="6" class="text-center py-4 text-muted">No ad accounts found.</td></tr>`);
                        return;
                    }

                    groups.forEach((group, index) => {
                        const statusText = group.account_status === 1 ? 'Active' : 'Inactive';
                        const row = `
                            <tr>
                                <td class="ps-4">${index + 1}</td>
                                <td>
                                    <strong>${group.name}</strong><br>
                                    <small class="text-muted">ID: ${group.id}</small>
                                </td>
                                <td>${group.business_name || '-'}</td>
                                <td class="text-center">
                                    <span class="badge ${group.account_status === 1 ? 'bg-success' : 'bg-secondary'}">
                                        ${statusText}
                                    </span>
                                </td>
                                <td>${group.created_time || '-'}</td>
                                <td>
                                    <i class="fa fa-eye text-primary cursor-pointer view-campaigns" data-id="${group.id}" style="cursor: pointer;"></i>
                                </td>
                            </tr>
                        `;
                        tbody.append(row);
                    });

                    $('#fbGroupTable').DataTable({
                        paging: true,
                        searching: false,
                        ordering: true,
                        info: true,
                        lengthChange: true,
                        pageLength: 10
                    });
                },
                error: function(xhr, status, error) 
                {
                    tbody.html(`<tr><td colspan="6" class="text-center text-danger py-4">Failed to load ad accounts.</td></tr>`);
                    toastr.error("Error loading ad accounts");
                }
            });
        });

        $(document).on('click', '.view-campaigns', function () 
        {
            const accountId = $(this).data('id'); 
            $('#campaignsTableBody').html(`
                <tr>
                    <td colspan="4" class="text-center py-4">
                        <div class="spinner-border text-primary" role="status"></div>
                        <p class="mt-2 text-muted">Loading campaigns...</p>
                    </td>
                </tr>
            `);
            $('#campaignsModal').modal('show');

            if ($.fn.DataTable.isDataTable('#campaignsTable')) 
            {
                $('#campaignsTable').DataTable().destroy();
            }

            $.ajax({
                url: "{{ route('facebook.campaigns.fetch') }}",
                method: "POST",
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                data: {
                    account_id: accountId
                },
                success: function (response) 
                {
                    const campaigns = response.data;
                    if (!Array.isArray(campaigns) || campaigns.length === 0) 
                    {
                        $('#campaignsTableBody').html(`<tr><td colspan="4" class="text-center text-muted">No campaigns found.</td></tr>`);
                        return;
                    }
                    const rows = campaigns.map((campaign, index) => `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${campaign.id}</td>
                            <td>${campaign.name}</td>
                            <td>${campaign.status}</td>
                        </tr>
                    `).join('');
    
                    $('#campaignsTableBody').html(rows);
                    $('#campaignsTable').DataTable({
                        paging: true,
                        searching: false,
                        ordering: true,
                        info: true,
                        lengthChange: true,
                        pageLength: 10
                    });
                },
                error: function () 
                {
                    $('#campaignsTableBody').html(`<tr><td colspan="4" class="text-center text-danger">Failed to load campaigns.</td></tr>`);
                }
            });
        });
    });
</script>
@endsection