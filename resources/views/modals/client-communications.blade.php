<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <h5>{{ $client->name }}</h5>
            <p>
                <strong>Phone:</strong> {{ $client->phone }}<br>
                <strong>Email:</strong> {{ $client->email }}<br>
            </p>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">{{ $title }} ({{ $communications->count() }})</h6>
                </div>
                <div class="card-body">
                    @if($communications->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Date & Time</th>
                                    <th>Message/Comment</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($communications as $index => $comm)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td width="150">{{ date('d-m-Y H:i', strtotime($comm->comm_time)) }}</td>
                                    <td>{{ $comm->comment }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <p class="text-muted">No {{ strtolower($title) }} found for this period.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>