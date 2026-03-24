@extends('layouts.app')

@section('title', session('software_type') === 'lead_management' ? 'Product Category | Pro-leadexpertz' : 'Project Category | Pro-leadexpertz')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-flex align-items-center justify-content-between">
                        <h4 class="mb-0"> {{ session('software_type') === 'lead_management' ? 'Product Category' : 'Project Category' }}</h4>
                        <button class="btn btn-primary btn-small px-4 py-1 rounded-pill fw-bold text-white shadow-lg add-project"
                        data-bs-toggle="modal"
                        data-bs-target="#Modalbox"
                        data-action="{{ route('project_category.store') }}"
                        data-type="Create"  data-modal="Category">
                            <i class="fa fa-plus"></i> Add
                        </button>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h4 class="card-title mb-0">Category</h4>
                            </div>

                            <div class="table-responsive">
                                <table class="data-table table table-hover table-bordered dt-responsive nowrap w-100">
                                    <thead class="table-light">
                                        <tr>
                                            <th>S.No</th>
                                            <th>Type</th>
                                            <th>Name</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if($categories)
                                        @foreach($categories as $category)
                                        <tr>
                                            <td>{{$loop->iteration}}</td>
                                            <td>{{$category->type}}</td>
                                            <td>{{$category->name}}</td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                   <button 
                                                        class="btn btn-sm btn-outline-primary edit-btn"
                                                        data-id="{{ $category->id }}"
                                                        data-type="{{$category->type}}"
                                                        data-name="{{ $category->name }}"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#Modalbox" data-action="{{ url('project-category/update') }}" data-type="Update" data-modal="Project">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-flex justify-content-end mt-3">
                                {!! $categories->links('pagination::bootstrap-5') !!}
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