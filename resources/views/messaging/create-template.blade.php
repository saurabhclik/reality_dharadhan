@extends('layouts.app')
@section('title', isset($template) ? 'Edit Template' : 'Create Template')

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">
                        <i class="fas fa-layer-group me-2"></i>
                        {{ isset($template) ? 'Edit Template' : 'Create Template' }}
                    </h4>
                    <div class="page-title-right">
                        @if(isset($template))
                        <button type="button" class="btn btn-danger me-2" onclick="deleteTemplate()">
                            <i class="fas fa-trash me-1"></i> Delete
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ isset($template) ? route('messaging.templates.update', $template->id) : route('messaging.templates.store') }}" method="POST">
                            @csrf
                            @if(isset($template))
                                @method('PUT')
                            @endif
                            
                            <input type="hidden" name="channel" value="{{ $channel }}">
                            
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Channel</label>
                                        <input type="text" class="form-control bg-light" value="{{ ucfirst($channel) }}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Template Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="name" 
                                               value="{{ isset($template) ? $template->name : old('name') }}" 
                                               required>
                                    </div>
                                </div>
                            </div>

                            @if($channel == 'email')
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">Email Subject <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="subject" 
                                               value="{{ isset($template) ? $template->subject : old('subject') }}" 
                                               required>
                                    </div>
                                </div>
                            </div>
                            @endif

                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">Template Body <span class="text-danger">*</span></label>
                                        <textarea class="form-control" name="body" rows="10" required>{{ isset($template) ? $template->body : old('body') }}</textarea>
                                        <div class="form-text">
                                            @verbatim
                                            Use {{variable_name}} for dynamic variables. Example: Hello {{customer_name}}, your order {{order_id}} is ready.
                                            @endverbatim
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                <div class="col-md-12">
                                    <div class="card border">
                                        <div class="card-header bg-light">
                                            <h6 class="mb-0">
                                                <i class="fas fa-paperclip me-2"></i>Attachments
                                                <small class="text-muted">(Max 10MB each, multiple files allowed)</small>
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            @if(isset($template) && !empty($template->attachments))
                                                <div class="mb-3">
                                                    <label class="form-label">Current Attachments:</label>
                                                    @php
                                                        $existingAttachments = explode(',', $template->attachments);
                                                    @endphp
                                                    <div class="row">
                                                        @foreach($existingAttachments as $attachment)
                                                            @if($attachment)
                                                            <div class="col-md-3 mb-2">
                                                                <div class="card border">
                                                                    <div class="card-body p-2">
                                                                        <div class="d-flex justify-content-between align-items-center">
                                                                            <div class="text-truncate" style="max-width: 150px;">
                                                                                <i class="fas fa-file me-1"></i>
                                                                                {{ $attachment }}
                                                                            </div>
                                                                            <div class="form-check">
                                                                                <input class="form-check-input" type="checkbox" name="remove_attachments[]" value="{{ $attachment }}" id="remove_{{ $loop->index }}">
                                                                                <label class="form-check-label small" for="remove_{{ $loop->index }}">Remove</label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif

                                            <div class="mb-3">
                                                <label class="form-label">Add New Attachments:</label>
                                                <input type="file" class="form-control @error('attachments.*') is-invalid @enderror" 
                                                       name="attachments[]" multiple 
                                                       accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.txt,.mp4,.avi,.mov">
                                                @error('attachments.*')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <div class="form-text">
                                                    Supported files: Images, PDF, Documents, Videos. Max 10MB per file.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            </div>
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-header bg-light">
                                            <h6 class="mb-0">
                                                <i class="fas fa-code me-2"></i>Template Variables
                                                <button type="button" class="btn btn-sm btn-primary float-end" id="addVariableBtn">
                                                    <i class="fas fa-plus me-1"></i> Add Variable
                                                </button>
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <div id="variablesContainer">
                                                @if(isset($variables) && count($variables) > 0)
                                                    @foreach($variables as $index => $variable)
                                                    <div class="variable-row row" id="variableRow{{ $index + 1 }}">
                                                        <div class="col-md-4">
                                                            <div class="mb-3">
                                                                <label class="form-label">Variable Name</label>
                                                                <input type="text" class="form-control" name="variables[{{ $index + 1 }}][name]" 
                                                                       value="{{ $variable->variable_name ?? '' }}" required>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="mb-3">
                                                                <label class="form-label">Type</label>
                                                                <select class="form-control" name="variables[{{ $index + 1 }}][type]">
                                                                    <option value="text" {{ ($variable->variable_type ?? 'text') == 'text' ? 'selected' : '' }}>Text</option>
                                                                    <option value="number" {{ ($variable->variable_type ?? '') == 'number' ? 'selected' : '' }}>Number</option>
                                                                    <option value="date" {{ ($variable->variable_type ?? '') == 'date' ? 'selected' : '' }}>Date</option>
                                                                    <option value="phone" {{ ($variable->variable_type ?? '') == 'phone' ? 'selected' : '' }}>Phone</option>
                                                                    <option value="email" {{ ($variable->variable_type ?? '') == 'email' ? 'selected' : '' }}>Email</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="mb-3">
                                                                <label class="form-label">Example Value</label>
                                                                <input type="text" class="form-control" name="variables[{{ $index + 1 }}][example]"
                                                                       value="{{ $variable->example_value ?? '' }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="mb-3">
                                                                <label class="form-label">Required</label>
                                                                <select class="form-control" name="variables[{{ $index + 1 }}][required]">
                                                                    <option value="1" {{ ($variable->is_required ?? 1) == 1 ? 'selected' : '' }}>Yes</option>
                                                                    <option value="0" {{ ($variable->is_required ?? 1) == 0 ? 'selected' : '' }}>No</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <button type="button" class="btn btn-sm btn-danger" onclick="removeVariable({{ $index + 1 }})">
                                                                <i class="fas fa-trash"></i> Remove
                                                            </button>
                                                        </div>
                                                    </div>
                                                    @endforeach
                                                    <script>
                                                        $(document).ready(function() 
                                                        {
                                                            variableCount = {{ count($variables) }};
                                                        });
                                                    </script>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="d-flex justify-content-between">
                                        <a href="{{ route('messaging.index') }}" class="btn btn-secondary">
                                            <i class="fas fa-times me-1"></i> Cancel
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-1"></i> 
                                            {{ isset($template) ? 'Update Template' : 'Save Template' }}
                                        </button>
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

@if(isset($template))
<script>
function deleteTemplate() 
{
    if (confirm('Are you sure you want to delete this template?')) 
    {
        fetch('{{ route('messaging.templates.destroy', $template->id) }}?channel={{ $channel }}', {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) 
            {
                alert(data.message);
                window.location.href = '{{ route('messaging.index') }}';
            } 
            else 
            {
                alert(data.message || 'Error deleting template');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting template');
        });
    }
}
</script>
@endif
<style>
    .variable-row 
    {
        border: 1px solid #dee2e6;
        border-radius: 5px;
        padding: 15px;
        margin-bottom: 10px;
        background: #f8f9fa;
    }
</style>
<script>
    $(document).ready(function() 
    {
        let variableCount = {{ isset($variables) ? count($variables) : 0 }};
        $('#addVariableBtn').click(function() 
        {
            variableCount++;
            const html = `
                <div class="variable-row row" id="variableRow${variableCount}">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Variable Name</label>
                            <input type="text" class="form-control" name="variables[${variableCount}][name]" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">Type</label>
                            <select class="form-control" name="variables[${variableCount}][type]">
                                <option value="text">Text</option>
                                <option value="number">Number</option>
                                <option value="date">Date</option>
                                <option value="phone">Phone</option>
                                <option value="email">Email</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">Example Value</label>
                            <input type="text" class="form-control" name="variables[${variableCount}][example]">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="mb-3">
                            <label class="form-label">Required</label>
                            <select class="form-control" name="variables[${variableCount}][required]">
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <button type="button" class="btn btn-sm btn-danger" onclick="removeVariable(${variableCount})">
                            <i class="fas fa-trash"></i> Remove
                        </button>
                    </div>
                </div>
            `;
            
            $('#variablesContainer').append(html);
        });
    });

    function removeVariable(id) 
    {
        $('#variableRow' + id).remove();
    }
</script>
@endsection