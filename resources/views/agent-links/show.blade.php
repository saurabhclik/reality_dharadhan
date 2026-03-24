@extends('layouts.app')

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fa-solid fa-check-circle"></i> Universal Link Generated Successfully</h5>
                    </div>

                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        <div class="row">
                            <div class="col-md-6">
                                <div class="card mb-3">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0"><i class="fa-solid fa-user-tie"></i> Agent Details</h6>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-sm">
                                            <tr>
                                                <th width="40%">Name:</th>
                                                <td><strong>{{ $agentLink->agent_name }}</strong></td>
                                            </tr>
                                            @if($agentLink->agent_email)
                                            <tr>
                                                <th>Email:</th>
                                                <td>{{ $agentLink->agent_email }}</td>
                                            </tr>
                                            @endif
                                            @if($agentLink->agent_phone)
                                            <tr>
                                                <th>Mobile:</th>
                                                <td>{{ $agentLink->agent_phone }}</td>
                                            </tr>
                                            @endif
                                            <tr>
                                                <th>Unique ID:</th>
                                                <td><code>{{ $agentLink->unique_identifier }}</code></td>
                                            </tr>
                                            <tr>
                                                <th>Created:</th>
                                                <td>{{ date('d M Y h:i A', strtotime($agentLink->created_at)) }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card mb-3">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0"><i class="fa-solid fa-link"></i> Generated Link & Codes</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Universal Link:</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" value="{{ url($agentLink->link_url) }}" id="linkUrl" readonly>
                                                <button class="btn btn-outline-primary" type="button" onclick="copyToClipboard()">
                                                    <i class="fa-solid fa-copy"></i>
                                                </button>
                                                <button class="btn btn-outline-success" type="button" onclick="window.open('{{ url($agentLink->link_url) }}', '_blank')">
                                                    <i class="fa-solid fa-external-link-alt"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="row text-center">
                                            <div class="col-6">
                                                <p class="fw-bold mb-2">QR Code</p>
                                                @if($agentLink->qr_code_path && file_exists(public_path($agentLink->qr_code_path)))
                                                    <img src="{{ asset($agentLink->qr_code_path) }}" alt="QR Code" class="img-fluid mb-2" style="max-width: 150px; border: 1px solid #ddd; padding: 5px;">
                                                    <br>
                                                    <a href="{{ asset($agentLink->qr_code_path) }}" download="qrcode_{{ $agentLink->unique_identifier }}.png" class="btn btn-sm btn-primary mt-2">
                                                        <i class="fa-solid fa-download"></i> Download QR
                                                    </a>
                                                @else
                                                    <p class="text-muted">QR Code not available</p>
                                                @endif
                                            </div>
                                            <div class="col-6">
                                                <p class="fw-bold mb-2">Barcode</p>
                                                @if($agentLink->barcode_path && file_exists(public_path($agentLink->barcode_path)))
                                                    <img src="{{ asset($agentLink->barcode_path) }}" alt="Barcode" class="img-fluid mb-2" style="max-width: 150px; border: 1px solid #ddd; padding: 5px;">
                                                    <br>
                                                    <a href="{{ route('agent-links.download-barcode', $agentLink->id) }}" class="btn btn-sm btn-primary mt-2">
                                                        <i class="fa-solid fa-download"></i> Download Barcode
                                                    </a>
                                                @else
                                                    <p class="text-muted">Barcode not available</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 text-center">
                            <button onclick="window.print()" class="btn btn-secondary">
                                <i class="fa-solid fa-print"></i> Print Details
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function copyToClipboard() 
    {
        var copyText = document.getElementById("linkUrl");
        copyText.select();
        copyText.setSelectionRange(0, 99999);
        document.execCommand("copy");
        toastr.success("Link copied to clipboard!");
    }
</script>
@endsection