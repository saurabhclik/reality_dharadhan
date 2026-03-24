@extends('layouts.app')

@section('title', session('software_type') === 'lead_management' ? 'Product Name | Pro-leadexpertz' : 'Project Name | Pro-leadexpertz')

@section('content')
<div class="page-content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">
                        {{ session('software_type') === 'lead_management' ? 'Manage Product' : 'Manage Project' }}
                    </h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                            <li class="breadcrumb-item active">
                                {{ session('software_type') === 'lead_management' ? 'Products' : 'Projects' }}
                            </li>
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
                            <h4 class="card-title mb-0">
                                {{ session('software_type') === 'lead_management' ? 'Product Name' : 'Project Name' }}
                            </h4>

                            <button class="btn btn-primary btn-small px-4 py-1 rounded-pill fw-bold text-white shadow-lg add-project"
                                data-bs-toggle="modal"
                                data-bs-target="#Modalbox"
                                data-action="{{ route('project.store') }}"
                                data-type="Create"
                                data-modal="{{ session('software_type') === 'lead_management' ? 'Product' : 'Project' }}">
                                <i class="fa fa-plus"></i>
                                Add {{ session('software_type') === 'lead_management' ? 'Product' : 'Project' }}
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover table-bordered dt-responsive nowrap w-100 data-table">
                                <thead class="table-light">
                                    <tr>
                                        <th>S.No</th>
                                        <th>
                                            {{ session('software_type') === 'lead_management' ? 'Product Name' : 'Project Name' }}
                                        </th>
                                        <th>Update</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($projects as $project)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $project->project_name }}</td>
                                        <td>
                                            <button 
                                                class="btn btn-sm btn-outline-primary edit-btn"
                                                data-id="{{ $project->id }}"
                                                data-name="{{ $project->project_name }}"
                                                data-bs-toggle="modal"
                                                data-bs-target="#Modalbox"
                                                data-action="{{ url('project/update') }}"
                                                data-type="Update"
                                 >
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-end mt-3">
                            {!! $projects->links('pagination::bootstrap-5') !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @include('modals.master')
    </div>
</div>

<script>
$(document).ready(function() 
{
    $('#table').DataTable({
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
