@extends('layouts.app')

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-secondary">
                    <div class="card-header bg-gray">
                        <h3 class="card-title">Logo Change</h3>
                    </div>
                    
                    <div class="card-body bg-light-gray">
                        @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif
                        
                        @if(session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif
                        
                        <form method="post" action="{{ route('setting.update_logo') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 m-auto">
                                        <p>Upload JPG or PNG image only. Size- Width: 354 Height: 75</p>
                                        <div class="form-group">
                                            <label for="">Logo File</label>
                                            <input type="file" name="file" class="form-control form-control-gm" required>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-12 text-center">
                                        <hr>    
                                        <input type="submit" name="btnSubmit" class="btn btn-primary" value="Save Logo">
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection