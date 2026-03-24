@extends('layouts.app')

@section('title', 'Settings | Pro-leadexpertz')

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Settings</h5>
                    </div>
                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                {!! implode('<br>', $errors->all()) !!}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('settings.update') }}">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="field1" class="form-label">Field 1</label>
                                        <input type="text" name="field1" class="form-control" value="{{ old('field1', $settings->field1) }}" required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="is_rpt_field3" name="is_rpt_field3" value="1"
                                    {{ $settings->is_rpt_field3 ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_rpt_field3">
                                    Display field report.
                                </label>
                            </div>
                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary px-4 py-2">
                                    <i class="bi bi-save me-2"></i> Save
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
