@extends('layouts.app')

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header ">
                        <h5 class="mb-0"><i class="fa-solid fa-link"></i> Agent Universal Links Management</h5>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table id="table" class="table-hover table-bordered dt-responsive nowrap w-100">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Agent Name</th>
                                        <th>Contact Info</th>
                                        <th>Role</th>
                                        <th>Link Status</th>
                                        <th>Generated Link</th>
                                        <th>QR Code</th>
                                        <th>Clicks</th>
                                        <th>Created Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($agents as $agent)
                                        @php
                                            $hasLink = isset($agentLinks[$agent->id]);
                                            $link = $hasLink ? $agentLinks[$agent->id] : null;
                                        @endphp
                                        <tr>
                                            <td>{{ $agent->id }}
                                                <div class="d-flex gap-1 ">
                                                    @if($hasLink && $link)
                                                        <a href="{{ route('agent-links.show', $link->id) }}" 
                                                           class="btn btn-sm btn-info" title="View Details">
                                                            <i class="fa-solid fa-eye"></i>
                                                        </a>
                                                        <a href="{{ url($link->link_url) }}" 
                                                           class="btn btn-sm btn-success" title="Open Public Form" target="_blank">
                                                            <i class="fa-solid fa-external-link-alt"></i>
                                                        </a>
                                                        <button type="button" 
                                                                class="btn btn-sm btn-danger" 
                                                                onclick="deleteLink({{ $link->id }}, '{{ $agent->name }}')"
                                                                title="Delete Link">
                                                            <i class="fa-solid fa-trash"></i>
                                                        </button>
                                                    @else
                                                        <button type="button" 
                                                                class="btn btn-sm btn-primary" 
                                                                onclick="generateLinkForAgent({{ $agent->id }}, '{{ $agent->name }}')"
                                                                title="Generate Link">
                                                            <i class="fa-solid fa-magic"></i> Generate
                                                        </button>
                                                    @endif
                                                </div></td>
                                            <td>
                                                <strong>{{ $agent->name }}</strong>
                                            </td>
                                            <td>
                                                @if($agent->email)
                                                    <i class="fa-solid fa-envelope text-muted me-1"></i>{{ $agent->email }}<br>
                                                @endif
                                                @if($agent->mobile)
                                                    <i class="fa-solid fa-phone text-muted me-1"></i>{{ $agent->mobile }}
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ $agent->role ?? 'Agent' }}</span>
                                            </td>
                                            <td>
                                                @if($hasLink)
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-secondary">No Link</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($hasLink && $link)
                                                    <div class="d-flex align-items-center">
                                                        <code class="text-truncate" style="max-width: 120px;">{{ $link->unique_identifier }}</code>
                                                        <button class="btn btn-xs btn-outline-secondary ms-1" 
                                                                onclick="copyLink('{{ url($link->link_url) }}')"
                                                                title="Copy Link">
                                                            <i class="fa-solid fa-copy"></i>
                                                        </button>
                                                    </div>
                                                @else
                                                    <span class="text-muted">Not generated</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if($hasLink && $link && $link->qr_code_path)
                                                    <a href="{{ asset($link->qr_code_path) }}" target="_blank">
                                                        <img src="{{ asset($link->qr_code_path) }}" alt="QR" width="40" height="40">
                                                    </a>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if($hasLink && $link)
                                                    <span class="badge bg-primary">{{ $link->clicks ?? 0 }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($hasLink && $link)
                                                    {{ date('d-m-Y', strtotime($link->created_at)) }}
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="generateLinkModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fa-solid fa-magic"></i> Generate Universal Link</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('agent-links.store') }}" id="generateForm">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="agent_id" id="modal_agent_id">
                    
                    <div class="text-center mb-3">
                        <div class="spinner-border text-primary d-none" id="modalSpinner" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fa-solid fa-info-circle"></i> 
                        You are about to generate a universal link for: <strong id="modal_agent_name"></strong>
                    </div>
                    
                    <p class="mb-0">This will create a unique link that customers can use to contact this agent directly.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="confirmGenerateBtn">
                        <i class="fa-solid fa-magic"></i> Generate Link
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteLinkModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fa-solid fa-trash"></i> Delete Universal Link</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="delete_link_id">
                
                <div class="text-center mb-3">
                    <i class="fa-solid fa-exclamation-triangle text-danger" style="font-size: 48px;"></i>
                </div>
                
                <p class="text-center">Are you sure you want to delete the universal link for:</p>
                <h5 class="text-center fw-bold" id="delete_agent_name"></h5>
                
                <div class="alert alert-warning mt-3">
                    <i class="fa-solid fa-exclamation-circle"></i>
                    <strong>Warning:</strong> This action cannot be undone. The public form will no longer be accessible.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                    <i class="fa-solid fa-trash"></i> Delete Permanently
                </button>
            </div>
        </div>
    </div>
</div>

<form id="delete-form" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
    function copyLink(link) 
    {
        navigator.clipboard.writeText(link).then(function() 
        {
            if (typeof toastr !== 'undefined') 
            {
                toastr.success('Link copied to clipboard!');
            } 
            else 
            {
                toastr.success('Link copied to clipboard!');
            }
        }, function() 
        {
            if (typeof toastr !== 'undefined') 
            {
                toastr.error('Failed to copy link');
            } 
            else 
            {
                toastr.error('Failed to copy link');
            }
        });
    }

    function generateLinkForAgent(agentId, agentName) 
    {
        document.getElementById('modal_agent_id').value = agentId;
        document.getElementById('modal_agent_name').textContent = agentName;
        var myModal = new bootstrap.Modal(document.getElementById('generateLinkModal'));
        myModal.show();
    }

    function deleteLink(linkId, agentName) 
    {
        document.getElementById('delete_link_id').value = linkId;
        document.getElementById('delete_agent_name').textContent = agentName;
        var myModal = new bootstrap.Modal(document.getElementById('deleteLinkModal'));
        myModal.show();
    }

    document.getElementById('confirmDeleteBtn').addEventListener('click', function() 
    {
        const linkId = document.getElementById('delete_link_id').value;
        const form = document.getElementById('delete-form');
        form.action = '/agent-links/' + linkId;
        form.submit();
    });

    document.getElementById('generateForm').addEventListener('submit', function(e) 
    {
        const submitBtn = document.getElementById('confirmGenerateBtn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Generating...';
    });

    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
    tooltipTriggerList.forEach(function(tooltipTriggerEl)
    {
        new bootstrap.Tooltip(tooltipTriggerEl);
    });
</script>
@endsection