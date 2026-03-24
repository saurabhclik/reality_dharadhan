@extends('layouts.app')

@section('title', 'Ratings | Pro-leadexpertz')
@section('content')
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Customer Ratings</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Ratings</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="table" class="table table-hover table-bordered dt-responsive nowrap w-100">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Rating</th>
                                        <th>Comments</th>
                                        <th>Project</th>
                                        <th>Applicant</th>
                                        <th>Phone</th>
                                        <th>IP Address</th>
                                        <th>Submitted On</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($ratings as $index => $rate)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="fa fa-star {{ $i <= $rate->rating ? 'text-warning' : 'text-muted' }}"></i>
                                            @endfor
                                        </td>
                                        <td>{{ $rate->comments }}</td>
                                        <td>{{ $rate->project_name }}</td>
                                        <td>{{ $rate->applicant_name }}</td>
                                        <td>{{ $rate->applicant_number }}</td>
                                        <td>{{ $rate->ip_address }}</td>
                                        <td>{{ \Carbon\Carbon::parse($rate->created_at)->format('d M Y, h:i A') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-end mt-3">
                            {!! $ratings->links('pagination::bootstrap-5') !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
