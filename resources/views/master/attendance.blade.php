@extends('layouts.app')

@section('title', 'Attendance Master | Pro-leadexpertz')

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Attendance Master</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Attendance</li>
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
                            <h4 class="card-title mb-0">Attendance Types</h4>
                            <!-- <button class="btn btn-primary btn-small px-4 py-1 rounded-pill fw-bold text-white shadow-lg"
                                data-bs-toggle="modal"
                                data-bs-target="#attendanceModal"
                                data-action="{{ route('attendance.store') }}"
                                data-type="Create"
                                data-modal="Attendance">
                                <i class="fa fa-plus"></i> Add
                            </button> -->
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover table-bordered dt-responsive nowrap w-100 data-table">
                                <thead class="table-light">
                                    <tr>
                                        <th>S.No</th>
                                        <th>Type</th>
                                        <th>Hours</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($attendanceTypes as $type)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $type->type }}</td>
                                            <td>{{ $type->hours }}</td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary edit-btn"
                                                    data-id="{{ $type->id }}"
                                                    data-name="{{ $type->type }}"
                                                    data-hours="{{ $type->hours }}"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#attendanceModal"
                                                    data-action="{{ route('attendance.update', $type->id) }}"
                                                    data-type="Update"
                                                    data-modal="Attendance">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-end mt-3">
                            {!! $attendanceTypes->links('pagination::bootstrap-5') !!}
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

@include('modals.attendance')

<script>
    $(document).ready(function () 
    {
        $('.edit-btn').on('click', function () 
        {
            let modal = $('#attendanceModal');
            modal.find('form').attr('action', $(this).data('action'));
            modal.find('input[name=type]').val($(this).data('name'));
            modal.find('input[name=hours]').val($(this).data('hours'));
            modal.find('input[name=_method]').val('PUT');
        });

        $('#attendanceModal').on('hidden.bs.modal', function () 
        {
            $(this).find('form')[0].reset();
            $(this).find('input[name=_method]').val('');
        });
    });
</script>
@endsection
