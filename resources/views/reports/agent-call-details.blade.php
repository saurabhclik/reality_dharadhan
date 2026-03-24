@extends('layouts.app')

@section('content')
<div class="page-content">
    <div class="container-fluid mt-4">
        <div class="row mb-3">
            <div class="col-md-12">
                <a href="{{ route('report.communication_reports') }}?fromDate={{ $fromDate }}&toDate={{ $toDate }}" 
                   class="btn btn-secondary">
                    ← Back to Communication Reports
                </a>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="mb-4">{{ $agent->name }} - Communication Details</h4>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="card bg-primary text-white">
                                    <div class="card-body">
                                        <h5 class="card-title text-white">Phone Calls</h5>
                                        <h2>{{ $totalCalls }}</h2>
                                        <p class="mb-0">Total Calls</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-success text-white">
                                    <div class="card-body">
                                        <h5 class="card-title text-white">WhatsApp</h5>
                                        <h2>{{ $totalWhatsapp }}</h2>
                                        <p class="mb-0">Total Whatsapp</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-info text-white">
                                    <div class="card-body">
                                        <h5 class="card-title text-white">Conversions</h5>
                                        <h2>{{ $conversions->total() }}</h2>
                                        <p class="mb-0">Total conversions</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-warning text-white">
                                    <div class="card-body">
                                        <h5 class="card-title text-white">Total Comms</h5>
                                        <h2>{{ $totalCalls + $totalWhatsapp }}</h2>
                                        <p class="mb-0">All communications</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <ul class="nav nav-tabs" id="agentTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="calls-tab" data-toggle="tab" href="#calls" role="tab">
                            Latest Phone Calls ({{ $uniqueCalls->total() }})
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="whatsapp-tab" data-toggle="tab" href="#whatsapp" role="tab">
                            Latest WhatsApp ({{ $uniqueWhatsapp->total() }})
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="conversions-tab" data-toggle="tab" href="#conversions" role="tab">
                            Conversions ({{ $conversions->total() }})
                        </a>
                    </li>
                </ul>
                
                <div class="tab-content mt-3" id="agentTabsContent">
                    <div class="tab-pane fade show active" id="calls" role="tabpanel">
                        <div class="card">
                            <div class="card-body">
                                <h5>Latest Call per Client</h5>
                                <p class="text-muted">Showing most recent call for each client</p>
                                
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped w-100" id="data-table-agent-report-phone">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>Client</th>
                                                <th>Phone</th>
                                                <th>Latest Comment</th>
                                                <th>Latest Call Time</th>
                                                <th>Total Calls</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($uniqueCalls as $index => $call)
                                            <tr>
                                                <td>{{ $loop->iteration + (($uniqueCalls->currentPage() - 1) * $uniqueCalls->perPage()) }}</td>
                                                <td>{{ $call->client_name }}</td>
                                                <td>{{ $call->phone }}</td>
                                                <td>{{ Str::limit($call->comment, 50) }}</td>
                                                <td>{{ date('d-m-Y H:i', strtotime($call->call_time)) }}</td>
                                                <td>
                                                    <span class="badge badge-info">{{ $call->total_calls }}</span>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-primary" onclick="showClientCalls({{ $call->lead_id }}, '{{ addslashes($call->client_name) }}')">
                                                        View All Calls
                                                    </button>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                {{ $uniqueCalls->links('pagination::bootstrap-5') }}
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="whatsapp" role="tabpanel">
                        <div class="card">
                            <div class="card-body">
                                <h5>Latest WhatsApp per Client</h5>
                                <p class="text-muted">Showing most recent WhatsApp message for each client</p>
                                
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped w-100" id="data-table-agent-report-whatsapp">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>Client</th>
                                                <th>Phone</th>
                                                <th>Latest Message</th>
                                                <th>Latest Time</th>
                                                <th>Total Messages</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($uniqueWhatsapp as $index => $message)
                                            <tr>
                                                <td>{{ $loop->iteration + (($uniqueWhatsapp->currentPage() - 1) * $uniqueWhatsapp->perPage()) }}</td>
                                                <td>{{ $message->client_name }}</td>
                                                <td>{{ $message->phone }}</td>
                                                <td>{{ Str::limit($message->comment, 50) }}</td>
                                                <td>{{ date('d-m-Y H:i', strtotime($message->call_time)) }}</td>
                                                <td>
                                                    <span class="badge badge-success">{{ $message->total_messages }}</span>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-success" onclick="showClientWhatsapp({{ $message->lead_id }}, '{{ addslashes($message->client_name) }}')">
                                                        View All Messages
                                                    </button>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                
                                {{ $uniqueWhatsapp->links('pagination::bootstrap-5') }}
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="conversions" role="tabpanel">
                        <div class="card">
                            <div class="card-body">
                                <h5>Conversions</h5>
                                
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped w-100" id="data-table-agent-report-conversion">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>Client</th>
                                                <th>Phone</th>
                                                <th>Email</th>
                                                <th>Type</th>
                                                <th>Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($conversions as $index => $conversion)
                                            <tr>
                                                <td>{{ $loop->iteration + (($conversions->currentPage() - 1) * $conversions->perPage()) }}</td>
                                                <td>{{ $conversion->client_name }}</td>
                                                <td>{{ $conversion->phone }}</td>
                                                <td>{{ $conversion->email }}</td>
                                                <td>
                                                    <span class="badge badge-{{ $conversion->conversion_type == 'Booked' ? 'success' : ($conversion->conversion_type == 'Completed' ? 'primary' : 'danger') }}">
                                                        {{ $conversion->conversion_type }}
                                                    </span>
                                                </td>
                                                <td>{{ date('d-m-Y', strtotime($conversion->conversion_date)) }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                
                                {{ $conversions->links('pagination::bootstrap-5') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="clientCallsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="clientCallsModalLabel">Client Call History</h5>
            </div>
            <div class="modal-body" id="clientCallsContent">
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="clientWhatsappModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="clientWhatsappModalLabel">Client WhatsApp History</h5>
            </div>
            <div class="modal-body" id="clientWhatsappContent">
            </div>
        </div>
    </div>
</div>
<script>
    function showClientCalls(leadId, clientName) {
    $('#clientCallsModalLabel').text('Call History: ' + clientName);
    $('#clientCallsContent').html(`
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status"></div>
            <p>Loading call history...</p>
        </div>
    `);
    $('#clientCallsModal').modal('show');

    $.ajax({
        url: "{{ route('report.client_communications') }}",
        type: "GET",
        data: {
            lead_id: leadId,
            agent_id: {{ $agent->id }},
            type: 'calls',
            fromDate: "{{ $fromDate }}",
            toDate: "{{ $toDate }}"
        },
        success: function(response) {
            let html = `
                <div class="table-responsive">
                    <table class="table table-bordered table-striped w-100">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Client</th>
                                <th>Phone</th>
                                <th>Comment</th>
                                <th>Time</th>
                            </tr>
                        </thead>
                        <tbody>
            `;

            response.forEach((c, index) => {
                html += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${c.client_name}</td>
                        <td>${c.phone}</td>
                        <td>${c.comment}</td>
                        <td>${new Date(c.call_time).toLocaleString()}</td>
                    </tr>
                `;
            });

            html += `</tbody></table></div>`;
            $('#clientCallsContent').html(html);
        },
        error: function() {
            $('#clientCallsContent').html(`
                <div class="alert alert-danger">
                    Error loading call history. Please try again.
                </div>
            `);
        }
    });
}

function showClientWhatsapp(leadId, clientName) {
    $('#clientWhatsappModalLabel').text('WhatsApp History: ' + clientName);
    $('#clientWhatsappContent').html(`
        <div class="text-center py-4">
            <div class="spinner-border text-success" role="status"></div>
            <p>Loading WhatsApp history...</p>
        </div>
    `);
    $('#clientWhatsappModal').modal('show');

    $.ajax({
        url: "{{ route('report.client_communications') }}",
        type: "GET",
        data: {
            lead_id: leadId,
            agent_id: {{ $agent->id }},
            type: 'whatsapp',
            fromDate: "{{ $fromDate }}",
            toDate: "{{ $toDate }}"
        },
        success: function(response) {
            let html = `
                <div class="table-responsive">
                    <table class="table table-bordered table-striped w-100">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Client</th>
                                <th>Phone</th>
                                <th>Message</th>
                                <th>Time</th>
                            </tr>
                        </thead>
                        <tbody>
            `;

            response.forEach((c, index) => {
                html += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${c.client_name}</td>
                        <td>${c.phone}</td>
                        <td>${c.comment}</td>
                        <td>${new Date(c.call_time).toLocaleString()}</td>
                    </tr>
                `;
            });

            html += `</tbody></table></div>`;
            $('#clientWhatsappContent').html(html);
        },
        error: function() {
            $('#clientWhatsappContent').html(`
                <div class="alert alert-danger">
                    Error loading WhatsApp history. Please try again.
                </div>
            `);
        }
    });
}


    $(document).ready(function() {
        $('#agentTabs a').on('click', function (e) 
        {
            e.preventDefault();
            $(this).tab('show');
        });
    });
</script>
@endsection