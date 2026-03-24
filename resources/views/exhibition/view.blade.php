@extends('layouts.app')

@section('title', $exhibition->name . ' Leads | leadexpertz')

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">{{ $exhibition->name }} - Leads</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('dashboard') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('exhibition.index') }}">Exhibitions</a>
                            </li>
                            <li class="breadcrumb-item active">Leads</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h4 class="card-title">Exhibition Leads</h4>
                                <p class="card-title-desc mb-0">
                                    <strong>Exhibition:</strong> {{ $exhibition->name }} | 
                                    <strong>Location:</strong> {{ $exhibition->location }} | 
                                    <strong>Total Leads:</strong> {{ $leads->total() }}
                                </p>
                            </div>
                            <div class="d-flex">
                                <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#convertMultipleModal">
                                    <i class="fas fa-exchange-alt me-1"></i> Convert Multiple
                                </button>
                                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#bulkImportModal">
                                    <i class="fas fa-file-upload me-1"></i> Bulk Import Leads
                                </button>
                            </div>
                        </div>

                        <div class="card mb-4">
                            <div class="card-body">
                                <form method="GET" action="{{ route('exhibition.view', $exhibition->id) }}" id="filterForm">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label for="name" class="form-label">Name</label>
                                            <input type="text" 
                                                class="form-control" 
                                                id="name" 
                                                name="name" 
                                                value="{{ request('name') }}" 
                                                placeholder="Search by name">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="country" class="form-label">Country</label>
                                            <select class="form-select select2" id="country" name="country">
                                                <option value="">All Countries</option>
                                                @forelse($countries ?? [] as $country)
                                                    <option value="{{ $country->name }}" {{ request('country') == $country->name ? 'selected' : '' }}>
                                                        {{ $country->name }}
                                                    </option>
                                                @empty
                                                    <option value="" disabled>No countries found</option>
                                                @endforelse
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="type" class="form-label">Type</label>
                                            <select class="form-select select2" id="type" name="type">
                                                <option value="">All Types</option>
                                                @forelse($types ?? [] as $typeItem)
                                                    <option value="{{ $typeItem->name }}" {{ request('type') == $typeItem->name ? 'selected' : '' }}>
                                                        {{ ucfirst($typeItem->name) }}
                                                    </option>
                                                @empty
                                                    <option value="" disabled>No types found</option>
                                                @endforelse
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="operating_country" class="form-label">Operating Country</label>
                                            <select class="form-select select2" id="operating_country" name="operating_country">
                                                <option value="">All Operating Countries</option>
                                                @forelse($operatingCountries ?? [] as $opCountry)
                                                    @if(!empty($opCountry) && $opCountry !== 'null')
                                                        <option value="{{ $opCountry }}" {{ request('operating_country') == $opCountry ? 'selected' : '' }}>
                                                            {{ $opCountry }}
                                                        </option>
                                                    @endif
                                                @empty
                                                    <option value="" disabled>No operating countries found</option>
                                                @endforelse
                                            </select>
                                        </div>
                                        <div class="col-md-2 mt-4 pt-3">
                                            @foreach(request()->except('per_page', 'page') as $key => $value)
                                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                            @endforeach
                                            <label class="d-flex align-items-center gap-2">
                                                Show
                                                <select name="per_page"
                                                    class="form-select form-select-sm"
                                                    onchange="this.form.submit()">
                                                    @foreach([10,20,50,100,500,1000] as $size)
                                                        <option value="{{ $size }}"
                                                            {{ request('per_page', 10) == $size ? 'selected' : '' }}>
                                                            {{ $size }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                entries
                                            </label>
                                        </div>
                                        <div class="col-md-6 mt-4 pt-3">
                                            <div class="d-flex justify-content-end">
                                                <button type="submit" class="btn btn-primary me-2" id="applyFilterBtn">
                                                    <span class="btn-text"><i class="fas fa-filter me-1"></i> Apply Filters</span>
                                                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                                    <span class="ms-1 d-none loader-text">Please wait...</span>
                                                </button>
                                                <a href="{{ route('exhibition.view', $exhibition->id) }}" class="btn btn-secondary" id="clearFilterBtn">
                                                    <span class="btn-text"><i class="fas fa-times me-1"></i> Clear Filters</span>
                                                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                                    <span class="ms-1 d-none loader-text">Please wait...</span>
                                                </a>
                                            </div>                    
                                        </div>                                    
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="col-md-12 mt-3">
                            <button type="button" class="btn btn-success shadow-sm" id="sendToMessagingPage">
                                <i class="fas fa-paper-plane me-1"></i>
                                Send Message to Selected Leads (<span id="selectedCount">0</span>)
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table-hover table-bordered dt-responsive nowrap w-100" id="table">
                                <thead class="table-light">
                                    <tr>
                                        <th>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="selectAllLeads">
                                            </div>
                                        </th>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Phone</th>
                                        <th>WhatsApp</th>
                                        <th>Email</th>
                                        <th>Company</th>
                                        <th>Country</th>
                                        <th>Type</th>
                                        <th>Website</th>
                                        <th>Fax</th>
                                        <th>Address</th>
                                        <th>Description</th>
                                        <th>Visit Card</th>
                                        <th>Operating Countries</th>
                                        <th>Remarks</th>
                                        <th>Reminder Date</th>
                                        <th>Date Added</th>
                                        <th>Device ID</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($leads as $lead)
                                    <tr>
                                        <td class="text-center">
                                            <input 
                                                type="checkbox" 
                                                class="row-checkbox"
                                                value="{{ $lead->id }}"
                                            >
                                        </td>
                                        <td>{{ $loop->iteration + ($leads->currentPage() - 1) * $leads->perPage() }}</td>
                                        <td>
                                            <strong>{{ $lead->name ?? 'N/A' }}</strong>
                                        </td>
                                        <td>{{ $lead->phone ?? 'N/A' }}</td>
                                        <td>{{ $lead->whatsapp ?? 'N/A' }}</td>
                                        <td>{{ $lead->email ?? 'N/A' }}</td>
                                        <td>{{ $lead->company ?? 'N/A' }}</td>
                                        <td>{{ $lead->country ?? 'N/A' }}</td>
                                        <td>                                      
                                            @if(!empty($lead->type))
                                                <div class="d-flex flex-wrap gap-1">
                                                    @foreach(json_decode($lead->type) as $type)
                                                        <span class="badge bg-info">{{ ucfirst($type) }}</span>
                                                    @endforeach
                                                </div>
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td>
                                            @if($lead->website)
                                            <a href="{{ $lead->website }}" target="_blank" title="Visit website">
                                                <i class="fas fa-external-link-alt"></i> Link
                                            </a>
                                            @else
                                            N/A
                                            @endif
                                        </td>
                                        <td>{{ $lead->fax ?? 'N/A' }}</td>
                                        <td>
                                            @if($lead->address)
                                            <small>{{ Str::limit($lead->address, 50) }}</small>
                                            @else
                                            N/A
                                            @endif
                                        </td>
                                        <td>
                                            @if($lead->description)
                                            <small>{{ Str::limit($lead->description, 50) }}</small>
                                            @else
                                            N/A
                                            @endif
                                        </td>
                                        <td>
                                            @if($lead->visit_card)
                                                @php
                                                    $cards = json_decode($lead->visit_card, true);
                                                @endphp

                                                @if(is_array($cards))
                                                    <div class="d-flex flex-wrap gap-1">
                                                        @foreach($cards as $card)
                                                            <a href="{{ asset('storage/' . $card) }}" 
                                                            target="_blank"
                                                            class="btn btn-sm btn-outline-primary">
                                                                <i class="fas fa-image"></i>
                                                            </a>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <a href="{{ asset('storage/' . $lead->visit_card) }}" target="_blank">
                                                        View
                                                    </a>
                                                @endif
                                            @else
                                                N/A
                                            @endif
                                        </td>

                                        <td>
                                            @if($lead->operating_country)
                                                @php
                                                    $countries = json_decode($lead->operating_country, true);
                                                    if (is_array($countries)) 
                                                    {
                                                        echo '<small>' . implode(', ', $countries) . '</small>';
                                                    } 
                                                    else 
                                                    {
                                                        echo $lead->operating_country;
                                                    }
                                                @endphp
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td>
                                            @if($lead->remarks)
                                            <small>{{ Str::limit($lead->remarks, 50) }}</small>
                                            @else
                                            N/A
                                            @endif
                                        </td>
                                        <td>
                                            @if($lead->reminder_date)
                                            {{ \Carbon\Carbon::parse($lead->reminder_date)->format('d M, Y h:i A') }}
                                            @else
                                            N/A
                                            @endif
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($lead->created_at)->format('d M, Y h:i A') }}</td>
                                        <td>
                                            @if($lead->device_id)
                                            <small class="text-muted">{{ Str::limit($lead->device_id, 10) }}</small>
                                            @else
                                            Web
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button type="button" 
                                                    class="btn btn-sm convert-lead-btn"
                                                    data-id="{{ $lead->id }}"
                                                    data-name="{{ $lead->name }}"
                                                    title="Convert to CRM Lead">
                                                    <i class="fas fa-exchange-alt text-success"></i>
                                                </button>
                                                
                                                <button type="button" 
                                                    class="btn btn-sm edit-lead-btn"
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editLeadModal"
                                                    data-id="{{ $lead->id }}"
                                                    data-name="{{ $lead->name }}"
                                                    data-phone="{{ $lead->phone }}"
                                                    data-whatsapp="{{ $lead->whatsapp }}"
                                                    data-email="{{ $lead->email }}"
                                                    data-company="{{ $lead->company }}"
                                                    data-website="{{ $lead->website }}"
                                                    data-fax="{{ $lead->fax }}"
                                                    data-country="{{ $lead->country }}"
                                                    data-address="{{ $lead->address }}"
                                                    data-type="{{ $lead->type ?? '[]' }}"
                                                    data-description="{{ $lead->description }}"
                                                    data-reminder-date="{{ $lead->reminder_date }}"
                                                    data-remarks="{{ $lead->remarks }}"
                                                    data-visit-card="{{ $lead->visit_card }}"
                                                    data-operating-country="{{ $lead->operating_country }}"
                                                    title="Edit Lead">
                                                    <i class="fas fa-edit text-warning"></i>
                                                </button>
                                    
                                                <button type="button" 
                                                    class="btn btn-sm delete-lead-btn"
                                                    data-id="{{ $lead->id }}"
                                                    data-device-id="{{ $lead->device_id }}"
                                                    data-exhibition-id="{{ $lead->exhibition_id }}"
                                                    data-name="{{ $lead->name }}"
                                                    title="Delete Lead">
                                                    <i class="fas fa-trash text-danger"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if($leads->hasPages())
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        Showing {{ $leads->firstItem() }} to {{ $leads->lastItem() }} 
                                        of {{ $leads->total() }} entries
                                    </div>
                                    <nav aria-label="Page navigation">
                                        {{ $leads->links('pagination::bootstrap-5') }}
                                    </nav>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="editLeadModal" tabindex="-1" aria-labelledby="editLeadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning text-light">
                <h5 class="modal-title" id="editLeadModalLabel">
                    <i class="fas fa-edit me-2"></i>Edit Lead
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editLeadForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <input type="hidden" id="edit_lead_id" name="id">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_name" class="form-label">Name *</label>
                                <input type="text" class="form-control" id="edit_name" name="name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_type" class="form-label">Type</label>
                                <input type="text" class="form-control" id="edit_type" name="type" min="1" step="1" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_phone" class="form-label">Phone</label>
                                <input type="tel" class="form-control" id="edit_phone" name="phone">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_whatsapp" class="form-label">WhatsApp</label>
                                <input type="tel" class="form-control" id="edit_whatsapp" name="whatsapp">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="edit_email" name="email">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_company" class="form-label">Company</label>
                                <input type="text" class="form-control" id="edit_company" name="company">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_website" class="form-label">Website</label>
                                <input type="url" class="form-control" id="edit_website" name="website">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_fax" class="form-label">Fax</label>
                                <input type="text" class="form-control" id="edit_fax" name="fax">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_country" class="form-label">Country</label>
                                <input type="text" class="form-control" id="edit_country" name="country">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_operating_country" class="form-label">Operating Countries</label>
                                <textarea class="form-control" id="edit_operating_country" name="operating_country" rows="2" placeholder="Enter countries separated by comma"></textarea>
                                <small class="text-muted">Enter countries separated by commas: USA, Canada, UK</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="edit_address" class="form-label">Address</label>
                                <textarea class="form-control" id="edit_address" name="address" rows="2"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_reminder_date" class="form-label">Reminder Date</label>
                                <input type="datetime-local" class="form-control" id="edit_reminder_date" name="reminder_date">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_visit_card" class="form-label">Visit Card</label>
                                <input type="file" class="form-control" id="edit_visit_card" name="visit_card" accept="image/*">
                                <small class="text-muted">Leave empty to keep current image</small>
                                <div id="current_visit_card" class="mt-2"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="edit_description" class="form-label">Description</label>
                                <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="edit_remarks" class="form-label">Remarks</label>
                                <textarea class="form-control" id="edit_remarks" name="remarks" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Update Lead</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="convertMultipleModal" tabindex="-1" aria-labelledby="convertMultipleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-light">
                <h5 class="modal-title" id="convertMultipleModalLabel">
                    <i class="fas fa-exchange-alt me-2"></i>Convert Multiple Leads
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to convert the selected leads to CRM?</p>
                <p class="text-muted"><small>Selected leads will be marked as converted and added to the CRM system.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmConvertMultiple">Convert Selected</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="bulkImportModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('exhibition.leads.import', $exhibition->id) }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-upload me-2"></i> Bulk Import Leads
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Upload CSV File</label>
                        <input type="file" name="file" class="form-control" accept=".csv" required>
                        <small class="text-muted">
                            Columns: name, phone, whatsapp, email, company, country, type, remarks
                        </small>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save me-1"></i> Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() 
    {
        const filterForm = document.getElementById('filterForm');

        const applyBtn = document.getElementById('applyFilterBtn');
        applyBtn.addEventListener('click', function(e) 
        {
            e.preventDefault();
            showLoader(applyBtn);
            setTimeout(() => {
                filterForm.submit();
            }, 800);
        });

        const clearBtn = document.getElementById('clearFilterBtn');
        clearBtn.addEventListener('click', function(e) 
        {
            e.preventDefault(); 
            showLoader(clearBtn);
            setTimeout(() => {
                window.location.href = clearBtn.href;
            }, 800);
        });

        function showLoader(button) 
        {
            const text = button.querySelector('.btn-text');
            const spinner = button.querySelector('.spinner-border');
            const loaderText = button.querySelector('.loader-text');

            text.classList.add('d-none');
            spinner.classList.remove('d-none');
            loaderText.classList.remove('d-none');
            button.disabled = true;
        }
    });
    document.addEventListener('DOMContentLoaded', function() 
    {
        let selectedLeads = [];
        function updateSelectedCount() 
        {
            const selectedCount = selectedLeads.length;
            document.getElementById('selectedCount').textContent = `(${selectedCount})`;
        }

        document.getElementById('selectAllLeads').addEventListener('change', function() 
        {
            const isChecked = this.checked;
            const checkboxes = document.querySelectorAll('.row-checkbox');
            
            checkboxes.forEach(checkbox => {
                checkbox.checked = isChecked;
                const leadId = checkbox.value;
                
                if (isChecked && !selectedLeads.includes(leadId)) 
                {
                    selectedLeads.push(leadId);
                }
                else if (!isChecked && selectedLeads.includes(leadId)) 
                {
                    const index = selectedLeads.indexOf(leadId);
                    selectedLeads.splice(index, 1);
                }
            });
            
            updateSelectedCount();
        });

        document.addEventListener('change', function(e) 
        {
            if (e.target.classList.contains('row-checkbox')) 
            {
                const leadId = e.target.value;
                
                if (e.target.checked && !selectedLeads.includes(leadId)) 
                {
                    selectedLeads.push(leadId);
                } 
                else if (!e.target.checked && selectedLeads.includes(leadId)) 
                {
                    const index = selectedLeads.indexOf(leadId);
                    selectedLeads.splice(index, 1);
                }
                
                updateSelectedCount();
            }
        });

        document.getElementById('sendToMessagingPage').addEventListener('click', function(e) 
        {
            e.preventDefault();
            
            if (selectedLeads.length === 0) 
            {
                toastr.error('Please select at least one lead');
                return;
            }

            const exhibitionId = {{ $exhibition->id }};
            const leadIds = selectedLeads.join(',');
            window.location.href = `/exhibition/${exhibitionId}/message?lead_ids=${leadIds}`;
        });
        function resetSelection() 
        {
            selectedLeads = [];
            updateSelectedCount();
            document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = false);
            document.getElementById('selectAllLeads').checked = false;
        }
        resetSelection();
    });
</script>
@endsection