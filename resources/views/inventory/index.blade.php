@extends('layouts.app')

@section('title', 'Inventory Management')

@section('content')
<div class="page-content">
    <div class="container-fluid px-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0 text-capitalize">
                <i class="fas fa-boxes me-2"></i>Inventory Overview
            </h4>
            <div>
                <span class="badge bg-primary fs-6">
                    Project: <strong>{{ $selectedProject->project_name ?? 'N/A' }}</strong>
                </span>
            </div>
        </div>

        <div class="row mb-4">
            @php
                $statuses = [
                    'pending' => ['color' => 'info', 'icon' => 'fas fa-clock'],
                    'cancel' => ['color' => 'danger', 'icon' => 'fas fa-times-circle'],
                    'hold' => ['color' => 'warning', 'icon' => 'fas fa-pause-circle'],
                    'sold' => ['color' => 'success', 'icon' => 'fas fa-check-circle']
                ];
            @endphp

            @foreach ($statuses as $status => $data)
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-{{ $data['color'] }} shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col me-2">
                                    <div class="text-xs font-weight-bold text-{{ $data['color'] }} text-uppercase mb-1">
                                        {{ ucfirst($status) }}
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ $statusCounts->$status ?? 0 }}
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="{{ $data['icon'] }} fa-2x text-{{ $data['color'] }}"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="d-flex justify-content-between mb-4">
            <div class="btn-group">
                <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-project-diagram me-1"></i> Switch Project
                </button>
                <ul class="dropdown-menu">
                    @foreach ($projects as $project)
                        <li>
                            <a class="dropdown-item" href="{{ route('inventory.index', ['id' => $project->id]) }}">
                                {{ $project->project_name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
            <div class="btn-group gap-2 d-flex">
                @if($user_type == 'super_admin' || $user_type == 'divisional_head')
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addInventoryModal">
                    <i class="fas fa-plus-circle me-1"></i> Add Unit
                </button>
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#bulkImportModal">
                    <i class="fas fa-file-import me-1"></i> Bulk Import
                </button>
                <a href="{{ route('inventory.downloadTemplate') }}" class="btn btn-outline-success">
                    <i class="fas fa-download me-1"></i> Template
                </a>
                @endif
            </div>
        </div>

        <div class="row g-3">
            @foreach ($inventoryDetails as $unit)
                @php
                    $color = match($unit->status) {
                        'pending' => 'info',
                        'sold' => 'success',
                        'hold' => 'warning',
                        'cancel' => 'danger',
                        default => 'secondary'
                    };
                @endphp
                
                <div class="col-xxl-1 col-xl-2 col-lg-3 col-md-4 col-sm-6 col-xs-12">
                    <div class="card bg-{{ $color }} text-white shadow-sm inventory-unit">
                        <div class="card-header p-1 d-flex justify-content-between">
                            <span class="badge bg-dark">{{ $unit->unit_no }}</span>
                            
                            <div class="dropdown">
                                <button class="btn btn-sm text-white p-0" data-bs-toggle="dropdown">
                                    <i class="fas fa-ellipsis-v text-dark"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    @if($unit->status !== 'sold')
                                    <li>
                                        <button class="dropdown-item sale-btn" 
                                            data-id="{{ $unit->id }}"
                                            data-name="{{ $unit->name }}"
                                            data-email="{{ $unit->email }}"
                                            data-number="{{ $unit->number }}"
                                            data-status="{{ $unit->status }}"
                                            data-sales_person_id="{{ $unit->sales_person_id }}">
                                            <i class="fas fa-shopping-cart me-1"></i> Sale
                                        </button>
                                    </li>
                                    @endif
                                    <li>
                                        <button class="dropdown-item history-btn" data-id="{{ $unit->id }}">
                                            <i class="fas fa-history me-1"></i> History
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="card-body p-2 text-center">
                            <div class="inventory-size">{{ $unit->size }}</div>
                            @if($unit->status !== 'pending')
                                <div class="inventory-customer mt-1 small text-truncate">
                                    {{ $unit->name ?: 'No customer' }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        @if($inventoryDetails->isEmpty())
        <div class="text-center py-5 bg-light rounded-3">
            <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
            <h4 class="text-muted">No Inventory Units Found</h4>
            <p class="text-muted">Add your first inventory unit by clicking the button above</p>
        </div>
        @endif
        @include('modals.add-inventory')
        @include('modals.sale')
        @include('modals.sale-history')
        @include('modals.bulk-import-inventory')
    </div>
</div>

<script>
    $(document).ready(function() 
    {
        $('.sale-btn').on('click', function() 
        {
            $('#sale_id').val($(this).data('id'));
            $('#name').val($(this).data('name'));
            $('#email').val($(this).data('email'));
            $('#number').val($(this).data('number'));
            $('#status').val($(this).data('status'));
            $('#sales_person_id').val($(this).data('sales_person_id'));
            
            var saleModal = new bootstrap.Modal(document.getElementById('saleModal'));
            saleModal.show();
        });

        $('.history-btn').on('click', function() 
        {
            const id = $(this).data('id');
            $.ajax({
                url: "{{ route('inventory.saleHistory') }}",
                method: 'POST',
                data: {
                    id: id,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) 
                {
                    let html = '';
                    
                    if(response.length > 0) 
                    {
                        response.forEach((row, index) => {
                            html += `
                                <tr>
                                    <td>${index + 1}</td>
                                    <td>${row.name || 'N/A'}</td>
                                    <td>${row.email || 'N/A'}</td>
                                    <td>${row.number || 'N/A'}</td>
                                    <td>
                                        <span class="badge bg-${getStatusColor(row.status)}">
                                            ${row.status}
                                        </span>
                                    </td>
                                    <td>${row.sales_person_name || 'N/A'}</td>
                                    <td>${new Date(row.created_at).toLocaleString()}</td>
                                </tr>
                            `;
                        });
                    } 
                    else 
                    {
                        html = `
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    <i class="fas fa-info-circle me-2"></i> No history found for this unit
                                </td>
                            </tr>
                        `;
                    }
                    
                    $('#saleHistoryBody').html(html);
                    var historyModal = new bootstrap.Modal(document.getElementById('saleHistoryModal'));
                    historyModal.show();
                },
                error: function(xhr) 
                {
                    console.error(xhr);
                    alert('An error occurred while loading history.');
                }
            });
        });
        
        function getStatusColor(status) 
        {
            switch(status) 
            {
                case 'pending': return 'info';
                case 'sold': return 'success';
                case 'hold': return 'warning';
                case 'cancel': return 'danger';
                default: return 'secondary';
            }
        }
        $('#SubmitBtn').closest('form').on('submit', function () 
        {
            $('#SubmitBtn').prop('disabled', true);
            $('#SubmitText').addClass('d-none');
            $('#SubmitSpinner').removeClass('d-none');
        });
        $('#SubmitBtnBulk').closest('form').on('submit', function () 
        {
            $('#SubmitBtnBulk').prop('disabled', true);
            $('#SubmitTextBulk').addClass('d-none');
            $('#SubmitSpinnerBulk').removeClass('d-none');
        });
    });
</script>
@endsection