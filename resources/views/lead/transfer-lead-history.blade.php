@extends('layouts.app')

@section('title', 'Transfer Leads | Pro-leadexpertz')

@section('content')
<style>
    .cust-badge {
        white-space: normal;
        padding: 6px 10px;
        font-size: 0.9rem;
        line-height: 1.4;
    }
    #table_filter {
        margin:10px;
    }
    .filter-section {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 20px;
    }
</style>

<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <h4 class="mb-0 text-gradient-primary">
                            <i class="fas fa-exchange-alt me-2"></i>Transfer Leads History
                        </h4>
                        <span class="cust-badge text-dark bg-soft-primary ms-2">{{ $leads->total() }} Records</span>
                    </div>
                </div>
            </div>
            
            <div class="col-12">
                <div class="filter-section">
                    <form action="{{ route('lead.transfer_history') }}" method="GET">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="from_user">From User</label>
                                <select name="from_user" id="from_user" class="form-select select2">
                                    <option value="">All Users</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ request('from_user') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-md-3">
                                <label for="to_user">To User</label>
                                <select name="to_user" id="to_user" class="form-select select2">
                                    <option value="">All Users</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ request('to_user') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-md-2">
                                <label for="from_date">From Date</label>
                                <input type="date" name="from_date" id="from_date" 
                                    value="{{ request('from_date') }}" class="form-control">
                            </div>
                            
                            <div class="col-md-2">
                                <label for="to_date">To Date</label>
                                <input type="date" name="to_date" id="to_date" 
                                    value="{{ request('to_date') }}" class="form-control">
                            </div>
                            
                            <div class="col-md-1 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter me-1"></i> Filter
                                </button>
                            </div>
                            
                            <div class="col-md-1 d-flex align-items-end">
                                <a href="{{ route('lead.transfer_history') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Clear
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="col-12">
                <div class="card p-3">
                    <table id="table" class="table table-hover table-bordered dt-responsive nowrap w-100">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Lead ID</th>
                                <th>Lead Details</th>
                                <th>Transferred From</th>
                                <th>Transferred To</th>
                                <th>Previous Status</th>
                                <th>New Status</th>
                                <th>Transfer Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($leads as $index => $transfer)
                            <tr>
                                <td>{{ ($leads->currentPage() - 1) * $leads->perPage() + $loop->iteration }}</td>
                                <td>{{ $transfer->lead_id }}</td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <div class="d-flex align-items-center mb-1">
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0">{{ $transfer->lead_name  ?? 'N/A' }}</h6>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="text-muted small">{{ $transfer->lead_phone ?? 'N/A' }}</span>
                                            <div class="d-flex">
                                                @if($transfer->lead_phone ?? false)
                                                <a href="tel:{{ $transfer->lead_phone }}" class="btn btn-xs btn-soft-light" data-bs-toggle="tooltip" title="Call">
                                                    <i class="fas fa-phone text-primary"></i>
                                                </a>
                                                <a href="https://wa.me/91{{ $transfer->lead_phone }}" target="_blank" class="btn btn-xs btn-soft-light" data-bs-toggle="tooltip" title="WhatsApp">
                                                    <i class="fab fa-whatsapp text-success"></i>
                                                </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $transfer->from_user_name  ?? 'N/A' }}</td>
                                <td>{{ $transfer->to_user_name  ?? 'N/A' }}</td>
                                <td>
                                    <span class="cust-badge text-dark">
                                        {{ $transfer->previous_status ?? 'N/A' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="cust-badge text-dark">
                                        {{ $transfer->new_status ?? 'N/A' }}
                                    </span>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($transfer->created_at)->format('d M Y H:i') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    
                    <div class="d-flex justify-content-end mt-3">
                        {{ $leads->appends([
                            'from_user' => request('from_user'),
                            'to_user' => request('to_user'),
                            'from_date' => request('from_date'),
                            'to_date' => request('to_date')
                        ])->links('vendor.pagination.bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection