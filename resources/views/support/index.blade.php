@extends('layouts.app')
@section('title', 'Support Tickets | Pro-leadexpertz')

@section('content')
<div class="page-content">
    <div class="min-vh-100 bg-light py-4 py-md-5">
        <div class="container">
            <div class="row mb-4">
                <div class="col-12 d-flex justify-content-between align-items-center">
                    <h1 class="h3 fw-bold text-dark">Support Tickets</h1>
                    @if($userType == 'super_admin')
                    <button 
                        class="btn btn-primary" 
                        data-bs-toggle="modal" 
                        data-bs-target="#ticketModal"
                        onclick="setModalMode('create')"
                    >
                        Create New Ticket
                    </button>
                    @endif
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card p-3">
                        <table id="table" class="table-hover table-bordered dt-responsive nowrap w-100">
                            <thead class="table-light">
                                <tr>
                                    <th>Ticket ID</th>
                                    <th>Subject</th>
                                    <th>Description</th>
                                    <th>Priority</th>
                                    <th>Status</th>
                                    <th>Attachments</th>
                                    <th>Created</th>
                                    @if($userType == 'super_admin')
                                    <th>Actions</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($tickets as $ticket)
                                    <tr>
                                        <td>
                                            <div class="fw-bold">{{ $ticket->ticket_id }}</div>
                                            @if($ticket->remarks)
                                                @php
                                                    $words = explode(' ', strip_tags($ticket->remarks));
                                                    $shortRemark = count($words) > 10 ? implode(' ', array_slice($words, 0, 10)) . '...' : $ticket->remarks;
                                                @endphp
                                                <div class="mt-1 d-flex align-items-center">
                                                    <span class="badge bg-info text-light me-1">
                                                        <i class="fa-solid fa-location-dot me-1"></i>Remark
                                                    </span>
                                                    <span class="text-truncate text-muted fs-6 d-inline-block" style="max-width: 200px;"
                                                        data-bs-toggle="tooltip" 
                                                        data-bs-placement="top" 
                                                        title="{{ strip_tags($ticket->remarks) }}">
                                                        {{ $shortRemark }}
                                                    </span>
                                                </div>
                                            @endif
                                        </td>
                                        <td>{{ $ticket->subject }}</td>
                                        <td>{{ $ticket->description }}</td>
                                        <td>
                                            <span class="badge 
                                                @if ($ticket->priority == 'High') bg-danger
                                                @elseif ($ticket->priority == 'Medium') bg-warning
                                                @else bg-success @endif">
                                                {{ $ticket->priority }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge 
                                                @if ($ticket->status == 'Open') bg-primary
                                                @elseif ($ticket->status == 'In Progress') bg-info
                                                @elseif ($ticket->status == 'Resolved') bg-success
                                                @else bg-secondary @endif">
                                                {{ $ticket->status }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($ticket->attachments)
                                                @foreach(json_decode($ticket->attachments) as $file)
                                                    <a href="{{ asset($file) }}" target="_blank" class="badge bg-info text-dark mb-1">{{ basename($file) }}</a>
                                                @endforeach
                                            @else
                                                No Attachments
                                            @endif
                                        </td>
                                        <td>{{ $ticket->created_at }}</td>
                                        @if($userType == 'super_admin')
                                        <td>
                                            <div class="d-flex gap-2">
                                                <button 
                                                    class="btn btn-sm btn-outline-primary" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#ticketModal"
                                                    onclick="setModalMode('edit', {{ $ticket->id }}, '{{ $ticket->subject }}', '{{ $ticket->description }}', '{{ $ticket->priority }}', '{{ $ticket->status }}', '{{ $ticket->attachments }}')"
                                                >
                                                    Edit
                                                </button>

                                                <form action="{{ route('support.toggle', $ticket->id) }}" method="POST" class="toggle-form d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-sm toggle-btn 
                                                        @if($ticket->status=='Open') btn-outline-success 
                                                        @else btn-outline-warning @endif">
                                                        <span class="btn-text">
                                                            @if($ticket->status=='Open') Mark Resolved @else Reopen @endif
                                                        </span>
                                                        <span class="btn-spinner d-none">
                                                            <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                                            Please wait...
                                                        </span>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="ticketModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-light">
                            <h5 class="modal-title" id="modalTitle">Create New Ticket</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <form id="ticketForm" method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" id="formMethod" name="_method" value="POST">
                                <input type="hidden" id="ticketId" name="id" value="">
                                
                                <div class="mb-3">
                                    <label class="form-label">Subject</label>
                                    <input type="text" name="subject" id="subject" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Description</label>
                                    <textarea name="description" id="description" class="form-control" rows="5" required></textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Priority</label>
                                    <select name="priority" id="priority" class="form-select" required>
                                        <option value="Low">Low</option>
                                        <option value="Medium">Medium</option>
                                        <option value="High">High</option>
                                        <option value="Critical">Critical</option>
                                    </select>
                                </div>
                                <div class="mb-3" id="statusField">
                                    <label class="form-label">Status</label>
                                    <select name="status" id="status" class="form-select" required>
                                        <option value="Open">Open</option>
                                        <option value="In Progress">In Progress</option>
                                        <option value="Resolved">Resolved</option>
                                        <option value="Closed">Closed</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Attachments</label>
                                    <input type="file" name="attachments[]" class="form-control" multiple>
                                    <div id="existingAttachments" class="mt-2"></div>
                                </div>
                                <div class="text-end">
                                    <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary" id="submitBtn">
                                        <span id="submitText">Create Ticket</span>
                                        <span id="submitSpinner" class="d-none">
                                            <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                            Please wait...
                                        </span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function setModalMode(mode, id = null, subject = '', description = '', priority = '', status = '', attachments = '') 
    {
        const modalTitle = document.getElementById('modalTitle');
        const form = document.getElementById('ticketForm');
        const formMethod = document.getElementById('formMethod');
        const ticketId = document.getElementById('ticketId');
        const statusField = document.getElementById('statusField');
        const submitText = document.getElementById('submitText');
        
        if (mode === 'create') 
        {
            modalTitle.textContent = 'Create New Ticket';
            formMethod.value = 'POST';
            form.action = '{{ route("support.store") }}';
            ticketId.value = '';
            document.getElementById('subject').value = '';
            document.getElementById('description').value = '';
            document.getElementById('priority').value = 'Low';
            document.getElementById('status').value = 'Open';
            statusField.style.display = 'none';
            submitText.textContent = 'Create Ticket';
            const existingAttachmentsDiv = document.getElementById('existingAttachments');
            existingAttachmentsDiv.innerHTML = '';
        } 
        else if (mode === 'edit') 
        {
            modalTitle.textContent = 'Edit Ticket';
            formMethod.value = 'PUT';
            form.action = '/support-tickets/' + id;
            ticketId.value = id;
            document.getElementById('subject').value = subject;
            document.getElementById('description').value = description;
            document.getElementById('priority').value = priority;
            document.getElementById('status').value = status;
            statusField.style.display = 'block';
            submitText.textContent = 'Update Ticket';
            const existingAttachmentsDiv = document.getElementById('existingAttachments');
            existingAttachmentsDiv.innerHTML = '';
            if (attachments) 
            {
                const files = JSON.parse(attachments);
                if (files.length > 0) 
                {
                    const label = document.createElement('p');
                    label.className = 'mb-1 small text-muted';
                    label.textContent = 'Current attachments:';
                    existingAttachmentsDiv.appendChild(label);
                    
                    files.forEach(file => {
                        const a = document.createElement('a');
                        a.href = '/' + file;
                        a.target = '_blank';
                        a.textContent = file.split('/').pop();
                        a.className = 'badge bg-info text-dark me-1 mb-1';
                        existingAttachmentsDiv.appendChild(a);
                    });
                }
            }
        }
    }

    document.addEventListener('DOMContentLoaded', function () 
    {
        const form = document.getElementById('ticketForm');
        const submitBtn = document.getElementById('submitBtn');
        const submitText = document.getElementById('submitText');
        const submitSpinner = document.getElementById('submitSpinner');
        
        form.addEventListener('submit', function () 
        {
            submitBtn.disabled = true;
            submitText.classList.add('d-none');
            submitSpinner.classList.remove('d-none');
        });
        
        const modal = document.getElementById('ticketModal');
        modal.addEventListener('hidden.bs.modal', function () 
        {
            submitBtn.disabled = false;
            submitText.classList.remove('d-none');
            submitSpinner.classList.add('d-none');
        });
        document.querySelectorAll('.toggle-form').forEach(form => {
            form.addEventListener('submit', function (e) 
            {
                const btn = form.querySelector('.toggle-btn');
                const text = btn.querySelector('.btn-text');
                const spinner = btn.querySelector('.btn-spinner');
                btn.disabled = true;
                text.classList.add('d-none');
                spinner.classList.remove('d-none');
            });
        });
    });
</script>
@endsection