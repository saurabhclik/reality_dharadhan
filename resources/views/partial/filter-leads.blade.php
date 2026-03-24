@extends('layouts.app')

@section('title', 'Lead Classification')

@section('content')
@include('modals.view-comments')
@include('modals.status-update')
@php
    $softwareType = session('software_type', 'real_state');
    $isLeadManagement = $softwareType === 'lead_management';
@endphp
<div class="page-content">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4>{{ $leads->total() }} {{ ucfirst($selected_classification ?? 'All') }} Leads</h4>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <form action="{{ route('lead.transfer_user') }}" method="POST" id="transferForm" class="mt-3">
                    @csrf
                    <input type="hidden" name="from_user" value="{{ request('user') }}">
                    <input type="hidden" name="from_status" value="{{ request('status') }}">

                    <div class="transfer-section card p-3 mb-3">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-4">
                                <label for="to_user">Transfer To User</label>
                                <select name="to_user" id="to_user" class="form-select select2" required>
                                    <option value="">Select User</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2">
                                <button type="button" class="btn btn-primary" id="transferBtn">
                                    <span id="SubmitText">Transfer Selected</span>
                                    <span id="SubmitSpinner" class="d-none">
                                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                        Please wait...
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="card p-3">
                        <label>
                            Show
                            <select id="lengthSelect" class="form-select form-select-sm" style="width: auto; display: inline-block;">
                                @foreach([10,25,50,100,500] as $opt)
                                    <option value="{{ $opt }}" {{ $length == $opt ? 'selected' : '' }}>{{ $opt }}</option>
                                @endforeach
                            </select>
                            entries
                        </label>
                        <table id="table" class="table table-hover table-bordered dt-responsive nowrap w-100">
                            <thead class="table-light">
                                <tr>
                                    <th class="no-sort text-center" style="width:48px;">
                                        <input type="checkbox" id="all-check" class="form-check-input">
                                    </th>
                                    <th>#</th>
                                    <th>Lead ID</th>
                                    <th>Lead Details</th>
                                    <th>Agent</th>
                                    <th>Source</th>
                                    <th>Campaign</th>
                                    <th>Status</th>
                                    <th>Budget</th>
                                    <th>{{ $isLeadManagement ? 'Product' : 'Project' }}</th>
                                    <th>Email</th>
                                    <th>Lead Date</th>
                                    <th>Follow Up</th>
                                    <th>Last Comment</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($leads as $lead)
                                <tr>
                                    <td class="text-center align-middle">
                                        <input type="checkbox" class="form-check-input checked" name="checked[]" value="{{ $lead->id }}">
                                    </td>
                                    <td class="align-middle">{{ $loop->iteration + ($leads->currentPage()-1) * $leads->perPage() }}</td>
                                    <td class="align-middle">{{ $lead->id }}</td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <h6 class="mb-1">{{ $lead->name }}</h6>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="text-muted small">{{ $lead->phone }}</span>
                                                <div class="d-flex gap-1">
                                                    <a href="tel:{{ $lead->phone }}" class="btn btn-xs btn-soft-light" data-bs-toggle="tooltip" title="Call">
                                                        <i class="fas fa-phone text-primary"></i>
                                                    </a>
                                                    <a href="https://wa.me/91{{ $lead->phone }}" target="_blank" class="btn btn-xs btn-soft-light" data-bs-toggle="tooltip" title="WhatsApp">
                                                        <i class="fab fa-whatsapp text-success"></i>
                                                    </a>
                                                    <a href="{{ route('lead.edit', $lead->id) }}" class="btn btn-xs btn-soft-light" data-bs-toggle="tooltip" title="Edit">
                                                        <i class="fas fa-edit text-warning"></i>
                                                    </a>
                                                    <button class="btn btn-xs btn-soft-light" 
                                                        onclick="showStatusUpdateModal({{ $lead->id }}, '{{ $lead->status }}')"
                                                        data-bs-toggle="tooltip" title="Update Status">
                                                        <i class="fas fa-sync-alt text-info"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="align-middle">{{ $lead->agent }}</td>
                                    <td class="align-middle"><span class="cust-badge bg-soft-info text-info">{{ $lead->source }}</span></td>
                                    <td class="align-middle">{{ $lead->campaign }}</td>
                                    <td class="align-middle">{{ $lead->status }}</td>
                                    <td class="align-middle">{{ $lead->budget }}</td>
                                    <td class="align-middle">{{ $lead->project_name }}</td>
                                    <td class="align-middle">
                                        <a href="mailto:{{ $lead->email }}" class="text-truncate d-inline-block" style="max-width: 200px;">{{ $lead->email }}</a>
                                    </td>
                                    <td class="align-middle">{{ \Carbon\Carbon::parse($lead->updated_date)->format('d M Y') }}</td>
                                    <td class="align-middle">{{ \Carbon\Carbon::parse($lead->updated_date)->format('d M Y') }}</td>
                                    <td class="align-middle">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0 me-2">
                                                <i class="fas fa-comment-alt text-muted"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                @php
                                                    $comment = strip_tags($lead->last_comment ?? '');
                                                    $short = \Illuminate\Support\Str::limit($comment, 30);
                                                @endphp
                                                <span class="d-block" data-bs-toggle="tooltip" title="{{ $comment }}">
                                                    {!! $short !!}
                                                </span>
                                                <a href="javascript:void(0);" onclick="showComment({{ $lead->id }})" class="text-primary small">
                                                    View more
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div class="mt-3 d-flex justify-content-end">
                            {{ $leads->links('vendor.pagination.bootstrap-5') }}
                        </div>
                        <div class="modal fade" id="transferConfirmModal" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Confirm Transfer</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p>You are about to transfer <strong><span id="transferCount">0</span></strong> lead(s).</p>
                                        <p>From: <strong>{{ $selected_user_name ?? request('user') ?? '-' }}</strong> ({{ request('status') ?? '-' }})</p>
                                        <p>To: <strong id="toUserName"></strong></p>
                                        <p class="text-danger">This action cannot be undone!</p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="button" id="confirmTransferBtn" class="btn btn-primary">Confirm Transfer</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> 
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() 
    {
        let allLeadIds = {!! isset($transfer_leads) ? json_encode($transfer_leads->pluck('id')->toArray()) : json_encode($leads->pluck('id')->toArray()) !!};
        let totalLeads = {!! isset($transfer_leads) ? $transfer_leads->total() : $leads->total() !!};
        $('#all-check, #selectAll').on('change', function() 
        {
            const isChecked = $(this).prop('checked');
            $('.checked').prop('checked', isChecked);

            if(isChecked) 
            {
                $('#transferForm').find('input[name="all_lead_ids[]"]').remove();
                if(allLeadIds && allLeadIds.length) 
                {
                    $('#transferForm').append(
                        allLeadIds.map(id => `<input type="hidden" name="all_lead_ids[]" value="${id}">`).join('')
                    );
                }
            } else {
                $('#transferForm').find('input[name="all_lead_ids[]"]').remove();
            }
            $('#all-check').prop('checked', isChecked);
            $('#selectAll').prop('checked', isChecked);
        });

        $(document).on('change', '.checked', function() 
        {
            if (!$(this).prop('checked')) 
            {
                $('#all-check, #selectAll').prop('checked', false);
                $('#transferForm').find('input[name="all_lead_ids[]"]').remove();
            }
        });

        $('#transferBtn').on('click', function() 
        {
            const checkedCount = $('.checked:checked').length;
            const bulkCount = $('#transferForm').find('input[name="all_lead_ids[]"]').length;

            if(checkedCount === 0 && bulkCount === 0) {
                toastr.error('Please select at least one lead to transfer');
                return;
            }

            if(!$('#to_user').val()) {
                toastr.error('Please select a user to transfer to');
                return;
            }

            const count = bulkCount > 0 ? totalLeads : checkedCount;
            $('#transferCount').text(count);
            $('#toUserName').text($('#to_user option:selected').text());

            const modal = new bootstrap.Modal(document.getElementById('transferConfirmModal'));
            modal.show();
        });
        $('#confirmTransferBtn').on('click', function() 
        {
            $(this).prop('disabled', true).html(`
                <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> 
                Transferring...
            `);
            $('#transferBtn').prop('disabled', true);
            $('#SubmitText').addClass('d-none');
            $('#SubmitSpinner').removeClass('d-none');
            $('#transferForm').submit();
        });
    });

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
                                    <th class="py-2">Agent</th>
                                    <th class="py-2">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                    `;

                    response.comments.forEach(function(comment, index) 
                    {
                        html += `
                            <tr>
                                <td class="py-2">${index + 1}</td>
                                <td class="py-2">${comment.comment}</td>
                                <td class="py-2"><span class="cust-badge bg-${comment.status === 'Positive' ? 'success' : (comment.status === 'Negative' ? 'danger' : 'info')}">${comment.status || '-'}</span></td>
                                <td class="py-2">${comment.user_name || 'System'}</td>
                                <td class="py-2">${new Date(comment.created_date).toLocaleString()}</td>
                            </tr>
                        `;
                    });

                    html += `
                                </tbody>
                            </table>
                        </div>
                    `;
                } 
                else 
                {
                    html = `
                        <div class="text-center py-4">
                            <h5 class="mb-1">No comments found</h5>
                            <p class="text-muted">This lead doesn't have any comments yet.</p>
                        </div>
                    `;
                }

                $('#commentsModalBody').html(html);
            },
            error: function() 
            {
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
    
</script>
@endsection