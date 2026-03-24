@extends('layouts.app')

@section('title', 'Project Name | Pro-leadexpertz')
@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-flex align-items-center justify-content-between">
                        <h4 class="mb-0">Manage campaigns</h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                                <li class="breadcrumb-item active">Users</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h4 class="card-title mb-0">campaigns</h4>
                                <button class="btn btn-primary btn-small px-4 py-1 rounded-pill fw-bold text-white shadow-lg add-project"
                                data-bs-toggle="modal"
                                data-bs-target="#Modalbox"
                                data-action="{{ route('campaigns.store') }}"
                                data-type="Create"  data-modal="Campaign">
                                    <i class="fa fa-plus"></i> Add
                                </button>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-hover table-bordered dt-responsive nowrap w-100 data-table">
                                    <thead class="table-light">
                                        <tr>
                                            <th>S.No</th>
                                            <th>Campaige Name</th>
                                            <th>Update</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($campaigns as $campaign)
                                        <tr>
                                            <td>{{$loop->iteration}}</td>
                                            <td>{{$campaign->name}}</td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <button 
                                                        class="btn btn-sm btn-outline-primary edit-btn"
                                                        data-id="{{ $campaign->id }}"
                                                        data-name="{{ $campaign->name }}"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#Modalbox"  data-action="{{ url('campaign/update') }}" data-type="Update" data-modal="Campaign">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-flex justify-content-end mt-3">
                                {!! $campaigns->links('pagination::bootstrap-5') !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>  
    @include('modals.master');
    <script>
        $(document).ready(function() 
        {
            var table = $('#table').DataTable({
                scrollX: true,
                scrollCollapse: true,
                fixedColumns: {
                    leftColumns: 2,
                    rightColumns: 2
                }
            });
        });
    </script>
@endsection