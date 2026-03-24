@extends('layouts.app')

@section('title', 'MIS Points | Pro-leadexpertz')

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Manage MIS Points</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('dashboard') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item active">Integrations</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="card-title mb-0">Manage MIS Points</h4>
                            <button class="btn btn-primary btn-small px-4 py-1 rounded-pill fw-bold text-white shadow-lg add-mis"
                                data-bs-toggle="modal"
                                data-bs-target="#misModal"
                                data-action="{{ route('mis.points.store') }}"
                                data-type="Create"
                                data-modal="MIS Point">
                                <i class="fa fa-plus"></i> Add Point
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table id="table" class="table table-hover table-bordered dt-responsive nowrap w-100">
                                <thead class="table-light">
                                    <tr>
                                        <th>S.No</th>
                                        <th>Associated User</th>
                                        <th>Point Name</th>
                                        <th>Description</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($points as $point)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ implode(', ', $point->associated_users) }}</td>
                                            <td>{{ $point->point_name }}</td>
                                            <td>{{ $point->description }}</td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary edit-btn"
                                                        data-id="{{ $point->id }}"
                                                        data-name="{{ $point->point_name }}"
                                                        data-description="{{ $point->description }}"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#misModal"
                                                        data-action="{{ route('mis.points.update', $point->id) }}"
                                                        data-type="Update"
                                                        data-modal="MIS Point">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-danger delete-btn"
                                                        data-id="{{ $point->id }}"
                                                        data-name="{{ $point->point_name }}"
                                                        data-url="{{ route('mis.points.destroy', $point->id) }}"
                                                        data-type="MIS Point">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                                <form id="delete-form-{{ $point->id }}" 
                                                      action="{{ route('mis.points.destroy', $point->id) }}" 
                                                      method="POST" class="d-none">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-end mt-3">
                            {!! $points->links('pagination::bootstrap-5') !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="misModal" tabindex="-1" aria-labelledby="misModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="misModalLabel"><span id="modalType">Create</span> MIS Point</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="misForm" method="POST">
                @csrf
                <div id="methodField"></div>
                <div class="modal-body">
                    <input type="hidden" id="id" name="id">
                    <div class="mb-3">
                        <label for="user" class="form-label">Associated User</label>
                        <select name="associated_user[]" id="associated-user" class="form-select select2" required multiple>
                            <option value="">Select User</option>
                            @foreach($users as $user)
                                @if($user->role != 'super_admin')
                                <option value="{{$user->id}}">{{$user->name}}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="name" class="form-label">Point Name</label>
                        <input type="text" class="form-control" id="name" name="point_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="SubmitBtn">
                        <span id="SubmitText">Save</span>
                        <span id="SubmitSpinner" class="d-none">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Please wait...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() 
    {
        $('.add-mis, .edit-btn').click(function() 
        {
            const modal = $('#misModal');
            modal.find('form').attr('action', $(this).data('action'));
            modal.find('#modalType').text($(this).data('type'));
            modal.find('#methodField').html($(this).data('type') === 'Update' ? '<input type="hidden" name="_method" value="PUT">' : '');
            modal.find('#id').val($(this).data('id') || '');
            modal.find('#name').val($(this).data('name') || '');
            modal.find('#description').val($(this).data('description') || '');
        });

        $('.delete-btn').click(function() 
        {
            const id = $(this).data('id');
            const name = $(this).data('name');
            const url = $(this).data('url');
            const type = $(this).data('type');
            
            Swal.fire({
                title: 'Are you sure?',
                text: `You are about to delete "${name}". This action cannot be undone!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel',
                reverseButtons: true,
                customClass: {
                    confirmButton: 'btn btn-danger',
                    cancelButton: 'btn btn-secondary'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) 
                {
                    const form = $('<form>', {
                        'method': 'POST',
                        'action': url
                    });
                    
                    form.append($('<input>', {
                        'type': 'hidden',
                        'name': '_token',
                        'value': '{{ csrf_token() }}'
                    }));
                    
                    form.append($('<input>', {
                        'type': 'hidden',
                        'name': '_method',
                        'value': 'DELETE'
                    }));
                    
                    $('body').append(form);
                    form.submit();
                }
            });
        });
        $('#misForm').on('submit', function() 
        {
            $('#SubmitBtn').prop('disabled', true);
            $('#SubmitText').addClass('d-none');
            $('#SubmitSpinner').removeClass('d-none');
        });
    });
</script>
@endsection
