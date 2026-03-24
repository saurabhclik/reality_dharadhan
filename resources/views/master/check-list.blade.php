@extends('layouts.app')

@section('title', 'Check List | Pro-leadexpertz')
@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-flex align-items-center justify-content-between">
                        <h4 class="mb-0">Check List</h4>
                        <button class="btn btn-primary btn-small px-4 py-1 rounded-pill fw-bold text-white shadow-lg add-project"
                        data-bs-toggle="modal"
                        data-bs-target="#Modalbox"
                        data-action="{{ route('checklist.store') }}"
                        data-type="Create"  data-modal="Checklist">
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
                                <h4 class="card-title mb-0">Check List</h4>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered dt-responsive nowrap w-100 data-table">
                                    <thead class="table-light">
                                        <tr>
                                            <th>S.No</th>
                                            <th>Type</th>
                                            <th>Name</th>
                                            <th>Update</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if($checklists)
                                        @foreach($checklists as $checklist)
                                        <tr>
                                            <td>{{$loop->iteration}}</td>
                                            <td>{{$checklist->type}}</td>
                                            <td>{{$checklist->name}}</td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                   <button 
                                                        class="btn btn-sm btn-outline-primary edit-btn"
                                                        data-id="{{ $checklist->id }}"
                                                        data-type="{{$checklist->type}}"
                                                        data-name="{{ $checklist->name }}"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#Modalbox"
                                                        data-action="{{ url('checklist/update') }}" data-type="Update" data-modal="Checklist">
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
                                {!! $checklists->links('pagination::bootstrap-5') !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> 
   @include('modals.master');
@endsection