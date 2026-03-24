@extends('layouts.app')

@section('title', 'Expense Management | Pro-leadexpertz')

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Expense Management</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">
                                <a href="{{ url('/dashboard') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item active">Expense Management</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Filter Options</h5>
                        <form method="GET" action="{{ route('expense.index') }}" class="row g-3" id="filterForm">
                            <div class="col-md-4">
                                <label for="fromDt" class="form-label">Start Date</label>
                                <input type="date" class="form-control" id="fromDt" name="fromDt" value="{{ request('fromDt') }}">
                            </div>
                            <div class="col-md-4">
                                <label for="toDt" class="form-label">End Date</label>
                                <input type="date" class="form-control" id="toDt" name="toDt" value="{{ request('toDt') }}">
                            </div>
                            @if(in_array(Session::get('user_type'), ['super_admin', 'divisional_head']))
                            <div class="col-md-4">
                                <label for="users" class="form-label">Employee</label>
                                <select name="users" id="users" class="form-control">
                                    <option value="">-- ALL --</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ request('users') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @endif
                            <div class="col-md-4">
                                <label for="excategory" class="form-label">Category</label>
                                <select name="excategory" id="excategory" class="form-control">
                                    <option value="">-- ALL --</option>
                                    <option value="TA" {{ request('excategory') == 'TA' ? 'selected' : '' }}>TA</option>
                                    <option value="DA" {{ request('excategory') == 'DA' ? 'selected' : '' }}>DA</option>
                                    <option value="Hotels" {{ request('excategory') == 'Hotels' ? 'selected' : '' }}>Hotels</option>
                                    <option value="Others" {{ request('excategory') == 'Others' ? 'selected' : '' }}>Others</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label for="status" class="form-label">Status</label>
                                <select name="status" id="status_exp" class="form-control">
                                    <option value="">-- ALL --</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="accepted" {{ request('status') == 'accepted' ? 'selected' : '' }}>Accepted</option>
                                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                </select>
                            </div>

                            <div class="col-md-4 d-flex align-items-end">
                                <div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ri-search-line align-middle me-1"></i> Search
                                    </button>
                                    <a href="{{ route('expense.index') }}" class="btn btn-outline-secondary ms-2">
                                        <i class="ri-refresh-line align-middle me-1"></i> Reset
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="card-title mb-0">Expense details</h5>
                            <div class="d-flex gap-3">
                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#expenseModal">
                                    <i class="ri-add-line align-middle me-1"></i> Add Expense
                                </button>
                            </div>
                        </div>
                        <form method="POST" action="{{ route('expense.bulk-accept') }}" id="acceptForm">
                            @csrf
                            <div class="table-responsive">
                                <table id="table" class="table table-hover table-bordered dt-responsive nowrap w-100">
                                    <thead class="table-light">
                                        <tr>
                                            @if(Session::get('user_type') == 'super_admin' && (!request('status') || request('status') == 'pending'))
                                            <th>
                                                <input type="checkbox" id="selectAll">
                                            </th>
                                            @endif
                                            <th>S.No</th>
                                            <th>Users</th>
                                            <th>Title</th>
                                            <th>Category</th>
                                            <th>Amount</th>
                                            <th>Comments</th>
                                            <th>Date</th>
                                            <th>Status</th>
                                            <th>Images</th>
                                            @if(Session::get('user_type') == 'super_admin' && (!request('status') || request('status') == 'pending'))
                                                <th class="text-center">Action</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($expenses as $index => $expense)
                                            <tr>
                                                @if(Session::get('user_type') == 'super_admin' && (!request('status') || request('status') == 'pending'))
                                                <td>
                                                    @if(Session::get('user_type') == 'super_admin' && $expense->status == 'pending')
                                                    <input type="checkbox" name="ids[]" value="{{ $expense->id }}">
                                                    @endif
                                                </td>
                                                @endif
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $expense->users }}</td>
                                                <td>{{ $expense->title }}</td>
                                                <td>{{ $expense->category }}</td>
                                                <td>{{ number_format($expense->amount, 2) }}</td>
                                                <td>{{ $expense->comments }}</td>
                                                <td>{{ date('d-M-Y', strtotime($expense->exp_date)) }}</td>
                                                <td>
                                                   @if($expense->status === 'pending')
                                                        <span class="badge bg-warning text-white rounded-pill">Pending</span>
                                                    @elseif($expense->status === 'accepted')
                                                        <span class="badge bg-info rounded-pill">Accepted</span>
                                                    @elseif($expense->status === 'rejected')
                                                        <span class="badge bg-danger rounded-pill">Rejected</span>
                                                    @elseif($expense->status === 'clear')
                                                        <span class="badge bg-success rounded-pill">Cleared</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @php
                                                        $imageCount = DB::table('expense_img')
                                                            ->where('expense_id', $expense->id)
                                                            ->count();
                                                    @endphp
                                                    @if($imageCount > 0)
                                                        <button type="button" class="btn btn-warning btn-sm view-images" data-expense-id="{{ $expense->id }}">
                                                            <i class="ri-image-line align-middle me-1"></i> View ({{ $imageCount }})
                                                        </button>
                                                    @else
                                                        <span class="text-muted">No Images</span>
                                                    @endif
                                                </td>
                                                @if(Session::get('user_type') == 'super_admin')
                                                    <td class="text-center">
                                                        @if($expense->status == 'pending')
                                                            <div class="d-flex justify-content-center gap-2">
                                                                <form method="POST" action="{{ route('expense.accept', $expense->id) }}" class="accept-form">
                                                                    @csrf
                                                                    <button type="submit" class="btn btn-info btn-sm" title="Accept">
                                                                        <i class="ri-check-line"></i> Accept
                                                                    </button>
                                                                </form>

                                                                <form method="POST" action="{{ route('expense.reject', $expense->id) }}" class="reject-form">
                                                                    @csrf
                                                                    <button type="submit" class="btn btn-danger btn-sm" title="Reject">
                                                                        <i class="ri-close-line"></i> Reject
                                                                    </button>
                                                                </form>

                                                                <form method="POST" action="{{ route('expense.clear', $expense->id) }}" class="clear-form">
                                                                    @csrf
                                                                    <button type="submit" class="btn btn-success btn-sm" title="Clear">
                                                                        <i class="ri-refresh-line"></i> Clear
                                                                    </button>
                                                                </form>
                                                            </div>

                                                        @else
                                                            <span class="badge bg-secondary">Action Completed</span>
                                                        @endif
                                                    </td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            @if(Session::get('user_type') == 'super_admin' && (!request('status') || request('status') == 'pending'))
                                            <td colspan="5" class="text-end"><strong>Total</strong></td>
                                            @else
                                            <td colspan="4" class="text-end"><strong>Total</strong></td>
                                            @endif
                                            <td><strong>{{ number_format($totalAmount, 2) }}</strong></td>
                                            <td colspan="{{ (Session::get('user_type') == 'super_admin' && (!request('status') || request('status') == 'pending')) ? 4 : 3 }}"></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            
                            @if(Session::get('user_type') == 'super_admin' && (!request('status') || request('status') == 'pending'))
                                <div class="d-flex gap-3 mt-3">
                                    <button type="submit" class="btn btn-success" id="acceptSelected">
                                        <i class="ri-check-line align-middle me-1"></i> Accept Selected
                                    </button>
                                    <button type="button" class="btn btn-danger" id="rejectSelected">
                                        <i class="ri-close-line align-middle me-1"></i> Reject Selected
                                    </button>
                                </div>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('modals.expense-image');
@include('modals.expense');
<script>
    $(document).ready(function() 
    {
        var table = $('#tbl_exporttable_to_xls').DataTable({
            scrollX: true,
            scrollCollapse: true,
            fixedColumns: {
                leftColumns: 0,
                rightColumns: 1
            }
        });
        $(document).on('click', '.view-images', function() 
        {
            const expenseId = $(this).data('expense-id');
            $.ajax({
                url: `/expense/${expenseId}/images`,
                method: 'GET',
                beforeSend: function() 
                {
                    $('#modalImages').html('<div class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>');
                },
                success: function(response) 
                {
                    const images = response.images;

                    if (images.length === 0)
                    {
                        $('#modalImages').html('<p class="text-center text-muted">No images found.</p>');
                    } 
                    else 
                    {
                        let html = '<div class="row">';
                        images.forEach(function(url) 
                        {
                            html += `
                                <div class="col-md-4 mb-3">
                                    <img src="${url}" class="img-fluid rounded border" alt="Expense Image">
                                </div>`;
                        });
                        html += '</div>';
                        $('#modalImages').html(html);
                    }

                    $('#imageModal').modal('show');
                },
                error: function() 
                {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to load images. Please try again.'
                    });
                }
            });
        });
        $('#selectAll').change(function() 
        {
            $('input[name="ids[]"]').prop('checked', $(this).prop('checked'));
        });
        $('#acceptForm').on('submit', function(e) 
        {
            e.preventDefault();
            
            const checkedCount = $('input[name="ids[]"]:checked').length;
            if (checkedCount === 0) 
            {
                Swal.fire({
                    icon: 'warning',
                    title: 'No Selection',
                    text: 'Please select at least one expense to accept.'
                });
                return;
            }
            
            Swal.fire({
                title: 'Are you sure?',
                text: `You are about to accept ${checkedCount} expense(s).`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, accept them!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) 
                {
                    this.submit();
                }
            });
        });
        $('#rejectSelected').click(function() 
        {
            const checkedCount = $('input[name="ids[]"]:checked').length;
            if (checkedCount === 0) 
            {
                Swal.fire({
                    icon: 'warning',
                    title: 'No Selection',
                    text: 'Please select at least one expense to reject.'
                });
                return;
            }
            
            Swal.fire({
                title: 'Are you sure?',
                text: `You are about to reject ${checkedCount} expense(s).`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, reject them!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) 
                {
                    const acceptForm = $('#acceptForm');
                    const rejectForm = $('<form>').attr({
                        method: 'POST',
                        action: "{{ route('expense.bulk-reject') }}"
                    });
                    
                    rejectForm.append($('<input>').attr({
                        type: 'hidden',
                        name: '_token',
                        value: $('meta[name="csrf-token"]').attr('content')
                    }));
                    
                    $('input[name="ids[]"]:checked').each(function() 
                    {
                        rejectForm.append($('<input>').attr({
                            type: 'hidden',
                            name: 'ids[]',
                            value: $(this).val()
                        }));
                    });
                    
                    $('body').append(rejectForm);
                    rejectForm.submit();
                }
            });
        });
        $('.accept-form').on('submit', function(e) 
        {
            e.preventDefault();
            const form = this;
            
            Swal.fire({
                title: 'Accept Expense?',
                text: 'Are you sure you want to accept this expense?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, accept it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) 
                {
                    form.submit();
                }
            });
        });
        
        $('.reject-form').on('submit', function(e) 
        {
            e.preventDefault();
            const form = this;
            
            Swal.fire({
                title: 'Reject Expense?',
                text: 'Are you sure you want to reject this expense?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, reject it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) 
                {
                    form.submit();
                }
            });
        });
        const urlParams = new URLSearchParams(window.location.search);
        $('#exp_date').val(new Date().toISOString().split('T')[0]);
        $('#status').val(urlParams.get('status') || '');
        $('#fromDt').val(urlParams.get('fromDt') || '');
        $('#toDt').val(urlParams.get('toDt') || '');
        $('#excategory').val(urlParams.get('excategory') || '');
        $('#users').val(urlParams.get('users') || '');
    });
</script>
@endsection