@extends('layouts.app')

@section('content')
<div class="page-content">
    <div class="container-fluid mt-4">
        <div class="row mb-3">
            <div class="col-md-12">
                <h2>Communication Reports</h2>
                <div class="card">
                    <div class="card-body">
                        <form method="GET" action="{{ route('report.communication_reports') }}" class="row">
                            <div class="col-md-3">
                                <label>From Date</label>
                                <input type="date" name="fromDate" class="form-control" value="{{ $fromDate }}">
                            </div>
                            <div class="col-md-3">
                                <label>To Date</label>
                                <input type="date" name="toDate" class="form-control" value="{{ $toDate }}">
                            </div>
                            <div class="col-md-2 align-self-end">
                                <button type="submit" class="btn btn-primary">Filter</button>
                                <a href="{{ route('report.communication_reports') }}" class="btn btn-secondary">Reset</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Detailed Communication Report</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered dt-responsive nowrap w-100" id="table">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Agent Name</th>
                                        <th>Phone Calls</th>
                                        <th>WhatsApp</th>
                                        <th>Total Communications</th>
                                        <th>Total Converted</th>
                                        <th>Booked</th>
                                        <th>Completed</th>
                                        <th>Cancelled</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($reports as $index => $report)
                                    <tr>
                                        <td>{{ $loop->iteration + (($reports->currentPage() - 1) * $reports->perPage()) }}</td>
                                        <td>
                                            <a href="{{ route('report.agent_call_details', ['id' => $report['user_id']]) }}?fromDate={{ $fromDate }}&toDate={{ $toDate }}" 
                                            class="text-primary">
                                                {{ $report['name'] }}
                                            </a>
                                        </td>
                                        <td>{{ $report['total_calls'] }}</td>
                                        <td>{{ $report['total_whatsapp'] }}</td>
                                        <td>{{ $report['total_calls'] + $report['total_whatsapp'] }}</td>
                                        <td>{{ $report['total_converted'] }}</td>
                                        <td>{{ $report['booked'] }}</td>
                                        <td>{{ $report['completed'] }}</td>
                                        <td>{{ $report['cancelled'] }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="d-flex justify-content-between">                  
                            <div>
                                {!! $reports->links('pagination::bootstrap-5') !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection