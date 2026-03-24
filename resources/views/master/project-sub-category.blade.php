@extends('layouts.app')

@section('title', session('software_type') === 'lead_management' ? 'Product Sub Category | Pro-leadexpertz' : 'Project Sub Category | Pro-leadexpertz')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-flex align-items-center justify-content-between">
                        <h4 class="mb-0">{{ session('software_type') === 'lead_management' ? 'Product Sub Category' : 'Project Sub Category' }}</h4>
                        <button class="btn btn-primary btn-small px-4 py-1 rounded-pill fw-bold text-white shadow-lg"
                            data-bs-toggle="modal"
                            data-bs-target="#editSubCategoryModal"
                            id="addSubCategoryBtn"
                            data-action="{{ route('sub_category.store') }}"
                            data-method="POST">
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
                                            <th>Category Name</th>
                                            <th>Sub Category Name</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if($project_sub_categories)
                                        @foreach($project_sub_categories as $project_sub_category)
                                        <tr>
                                            <td>{{$loop->iteration}}</td>
                                            <td>{{$project_sub_category->type}}</td>
                                            <td>{{$project_sub_category->cat_name}}</td>
                                            <td>{{$project_sub_category->name}}</td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <button 
                                                        class="btn btn-sm btn-outline-primary edit-btn"
                                                        data-id="{{ $project_sub_category->id }}"
                                                        data-name="{{ $project_sub_category->name }}"
                                                        data-category="{{ $project_sub_category->catg_id }}"
                                                        data-action="{{ route('sub_category.update', $project_sub_category->id) }}"
                                                        data-method="PUT"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#editSubCategoryModal">
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
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>  
    @include('modals.project-sub-category');
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
            $('#addSubCategoryBtn').on('click', function () 
            {
                $('#editSubCategoryModalLabel').text('Add Sub Category');
                $('#submitBtn').text('Create');
                $('#subCategoryForm').attr('action', $(this).data('action'));
                $('#subCategoryForm').find('input[name="_method"]').remove();
                $('#id').val('');
                $('#name').val('');
                $('#category').val('');
            });
            $('.edit-btn').on('click', function () 
            {
                $('#editSubCategoryModalLabel').text('Edit Sub Category');
                $('#submitBtn').text('Update');

                const action = $(this).data('action');
                const id = $(this).data('id');
                const name = $(this).data('name');
                const category = $(this).data('category');

                $('#subCategoryForm').attr('action', action);
                if (!$('#subCategoryForm input[name="_method"]').length) 
                {
                    $('#subCategoryForm').append('<input type="hidden" name="_method" value="PUT">');
                }

                $('#id').val(id);
                $('#name').val(name);
                $('#category').val(category);
            });
        });
    </script>
@endsection