@extends('layouts.app')

@section('title', 'User Management | Pro-leadexpertz')
@section('content')
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Designation Management</h4>
                    <button class="btn btn-primary btn-small px-4 py-1 rounded-pill fw-bold text-white shadow-lg add-designation"
                        data-bs-toggle="modal" data-bs-target="#designationModal" data-mode="create">
                        <i class="fa fa-plus"></i> Add Designation
                    </button>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="card-title mb-0">Designation List</h4>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered dt-responsive nowrap w-100 data-table">
                                <thead class="table-light">
                                    <tr>
                                        <th>S.No</th>
                                        <th>Designation</th>
                                        <th>Created Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if($designations)
                                    @foreach($designations as $des)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $des->designation }}</td>
                                        <td>{{ $des->created_at }}</td>
                                        <td>
                                            <button
                                                class="btn btn-sm btn-outline-primary edit-btn"
                                                data-id="{{ $des->id }}"
                                                data-designation="{{ $des->designation }}"
                                                data-bs-toggle="modal"
                                                data-bs-target="#designationModal"
                                                data-mode="edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-end mt-3">
                            {!! $designations->links('pagination::bootstrap-5') !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> 
</div>

<div class="modal fade" id="designationModal" tabindex="-1" aria-labelledby="designationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" id="designationForm">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="designationModalLabel">Add Designation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="designationInput" class="form-label">Designation</label>
                        <input type="text" class="form-control" id="designationInput" name="designation" required maxlength="255">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">Save Designation</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () 
    {
        var modal = document.getElementById('designationModal');
        var form = document.getElementById('designationForm');
        var modalTitle = document.getElementById('designationModalLabel');
        var designationInput = document.getElementById('designationInput');
        var submitBtn = document.getElementById('submitBtn');

        modal.addEventListener('show.bs.modal', function (event) 
        {
            var button = event.relatedTarget;
            var mode = button.getAttribute('data-mode');

            if (mode === 'create') 
            {
                modalTitle.textContent = 'Add Designation';
                form.action = '{{ route("designation.store") }}';
                designationInput.value = '';
                submitBtn.textContent = 'Create Designation';
            } 
            else if (mode === 'edit') 
            {
                var id = button.getAttribute('data-id');
                var designation = button.getAttribute('data-designation');

                modalTitle.textContent = 'Edit Designation';
                form.action = '{{ url("designation/update") }}/' + id;
                designationInput.value = designation;
                submitBtn.textContent = 'Update Designation';
            }
        });
    });
</script>
@endsection
