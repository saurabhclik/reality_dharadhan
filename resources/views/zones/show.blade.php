@extends('layouts.app')

@section('title', 'Zone Details | Pro-leadexpertz')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">Zone Details: {{ $zone->zone_name }}</h5>
                            <div>
                                <a href="{{ route('zone.edit', $zone->id) }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <a href="{{ route('zone.index') }}" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-arrow-left"></i> Back to List
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th width="30%">Zone ID</th>
                                            <td>{{ $zone->id }}</td>
                                        </tr>
                                        <tr>
                                            <th>Zone Name</th>
                                            <td>{{ $zone->zone_name }}</td>
                                        </tr>
                                        <tr>
                                            <th>District</th>
                                            <td>{{ $zone->district_name ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>State</th>
                                            <td>{{ $zone->state ?? 'Rajasthan' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Sub Area</th>
                                            <td>{{ $zone->sub_area ?? 'N/A' }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th width="30%">Pincode</th>
                                            <td>{{ $zone->pincode ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Display Order</th>
                                            <td>{{ $zone->zone_order }}</td>
                                        </tr>
                                        <tr>
                                            <th>Status</th>
                                            <td>
                                                @if($zone->status)
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-danger">Inactive</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Created At</th>
                                            <td>{{ $zone->created_at ? date('d M Y, h:i A', strtotime($zone->created_at)) : 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Last Updated</th>
                                            <td>{{ $zone->updated_at ? date('d M Y, h:i A', strtotime($zone->updated_at)) : 'N/A' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection