@extends('layouts.app')

@section('title', 'Source | Pro-leadexpertz')
@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-flex align-items-center justify-content-between">
                        <h4 class="mb-0">Manage Source Platform</h4>
                        <button class="btn btn-primary btn-small px-4 py-1 rounded-pill fw-bold text-white shadow-lg add-project"
                            data-bs-toggle="modal"
                            data-bs-target="#Modalbox"
                            data-action="{{ route('source.store') }}"
                            data-type="Create" data-modal="Source">
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
                                <h4 class="card-title mb-0">Source Platform</h4>
                            </div>

                            <div class="table-responsive">
                                <table class="table data-table table-hover table-bordered dt-responsive nowrap w-100">
                                    <thead class="table-light">
                                        <tr>
                                            <th>S.No</th>
                                            <th>Source Name</th>
                                            <th>Update</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if($sources)
                                        @foreach($sources as $source)
                                        <tr>
                                            <td>{{$loop->iteration}}</td>
                                            <td>{{$source->name}}</td>
                                            <td>
                                                <button 
                                                    class="btn btn-sm btn-outline-primary edit-btn"
                                                    data-id="{{ $source->id }}"
                                                    data-name="{{ $source->name }}"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#Modalbox" data-action="{{ url('source/update') }}" data-type="Update" data-modal="Source">
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
                                {!! $sources->links('pagination::bootstrap-5') !!}
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