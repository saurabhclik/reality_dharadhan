@extends('layouts.app')

@section('title', 'API Integration Settings | Pro-leadexpertz')

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">API Integration Settings</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Integrations</li>
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
                            <h4 class="card-title mb-0">Integration Settings</h4>
                            <button class="btn btn-primary btn-small px-4 py-1 rounded-pill fw-bold text-white shadow-lg"
                                data-bs-toggle="modal"
                                data-bs-target="#integrationModal"
                                data-action="{{ route('integration.store') }}"
                                data-type="Create"
                                data-modal="Integration">
                                <i class="fa fa-plus"></i> Add Integration
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table id="table" class="table table-hover table-bordered dt-responsive nowrap w-100">
                                <thead class="table-light">
                                    <tr>
                                        <th>S.No</th>
                                        <th>Integration Type</th>
                                        <th>Settings</th>
                                        <th>Encrypted</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($integrations as $integration)
                                        @php
                                            $settings = json_decode($integration->settings, true);
                                            $displaySettings = is_array($settings) ? $settings : [];
                                        @endphp
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $integrationTypes[$integration->integration_type] ?? ucfirst($integration->integration_type) }}</td>
                                            <td>
                                                @if(!empty($displaySettings))
                                                    <ul class="list-unstyled mb-0">
                                                        @foreach($displaySettings as $key => $value)
                                                            <li><strong>{{ ucfirst($key) }}:</strong> 
                                                                @if($integration->is_encrypted && in_array($key, ['api_token', 'token', 'secret', 'password', 'key', 'private_key', 'app_secret', 'access_token', 'mail_password']))
                                                                    ********
                                                                @else
                                                                    {{ is_array($value) ? json_encode($value) : (is_string($value) ? $value : json_encode($value)) }}
                                                                @endif
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @else
                                                    No settings configured
                                                @endif
                                            </td>
                                            <td>
                                                @if($integration->is_encrypted)
                                                    <span class="badge bg-success">Yes</span>
                                                @else
                                                    <span class="badge bg-secondary">No</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($integration->status == 'active')
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-danger">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary edit-btn"
                                                    data-id="{{ $integration->id }}"
                                                    data-integration_type="{{ $integration->integration_type }}"
                                                    data-settings="{{ $integration->settings }}"
                                                    data-is_encrypted="{{ $integration->is_encrypted }}"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#integrationModal"
                                                    data-action="{{ route('integration.update', $integration->id) }}"
                                                    data-type="Update"
                                                    data-modal="Integration" data-status="{{ $integration->status }}">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-danger delete-btn"
                                                    data-id="{{ $integration->id }}"
                                                    data-type="{{ $integrationTypes[$integration->integration_type] ?? ucfirst($integration->integration_type) }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                                <form id="delete-form-{{ $integration->id }}" 
                                                      action="{{ route('integration.destroy', $integration->id) }}" 
                                                      method="POST" 
                                                      class="d-none">
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
                            {!! $integrations->links('pagination::bootstrap-5') !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="integrationModal" tabindex="-1" aria-labelledby="integrationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="integrationModalLabel"><span id="modalType">Create</span> Integration Settings</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="integrationForm" method="POST">
                @csrf
                <div id="methodField"></div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="integration_type" class="form-label">Integration Type</label>
                        <select class="form-select" id="integration_type" name="integration_type" required>
                            <option value="">Select Integration Type</option>
                            @foreach($integrationTypes as $key => $type)
                                <option value="{{ $key }}">{{ $type }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-control" id="integration_status" name="status" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <div id="settingsContainer">
                        <div class="mb-3">
                            <label class="form-label">Settings</label>
                            <div id="settingsFields">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="is_encrypted" name="is_encrypted" value="1">
                        <label class="form-check-label" for="is_encrypted">Encrypt sensitive data</label>
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
        const fieldTemplates = {
            'housing': [
                {name: 'api_id', label: 'API ID', type: 'text', required: true},
                {name: 'api_token', label: 'API Token', type: 'password', required: true},
                {name: 'base_url', label: 'Base URL', type: 'url', required: true}
            ],
            'facebook': [
                {name: 'app_id', label: 'App ID', type: 'text', required: true},
                {name: 'app_secret', label: 'App Secret', type: 'password', required: true},
                {name: 'access_token', label: 'Access Token', type: 'password', required: false}
            ],
            'gmail': [
                {name: 'mail_mailer', label: 'Mailer', type: 'text', required: true, value: 'smtp'},
                {name: 'mail_host', label: 'Host', type: 'text', required: true, value: 'smtp.gmail.com'},
                {name: 'mail_port', label: 'Port', type: 'number', required: true, value: 587},
                {name: 'mail_username', label: 'Username', type: 'email', required: true},
                {name: 'mail_password', label: 'Password', type: 'text', required: true},
                {name: 'mail_encryption', label: 'Encryption', type: 'text', required: true, value: 'tls'},
                {name: 'mail_from_address', label: 'From Address', type: 'email', required: true},
                {name: 'mail_from_name', label: 'From Name', type: 'text', required: true, value: 'leadmanagement'}
            ],
            'magicbricks': [
                {name: 'api_key', label: 'API Key', type: 'text', required: true},
                {name: 'username', label: 'Username', type: 'text', required: true},
                {name: 'password', label: 'Password', type: 'password', required: true},
                {name: 'base_url', label: 'Base URL', type: 'url', required: true}
            ],
            '99acres': [
                {name: 'api_key', label: 'API Key', type: 'text', required: true},
                {name: 'username', label: 'Username', type: 'text', required: true},
                {name: 'password', label: 'Password', type: 'password', required: true},
                {name: 'base_url', label: 'Base URL', type: 'url', required: true}
            ],
            'firebase': [
                {name: 'api_key',          label: 'API Key',           type: 'text', required: true},
                {name: 'project_id',       label: 'Project ID',       type: 'text', required: true},
                {name: 'auth_domain',      label: 'Auth Domain',      type: 'text', required: false},
                {name: 'storage_bucket',   label: 'Storage Bucket',   type: 'text', required: false},
                {name: 'messagingSenderId',label: 'Messaging Sender ID', type: 'text', required: true},
                {name: 'app_id',           label: 'App ID',           type: 'text', required: false},
                {name: 'vapidKey',         label: 'VAPID Key',        type: 'text', required: false},
                {name: 'measurementId',    label: 'Measurement ID',   type: 'text', required: false}
            ],
            'other': [
                {name: 'key1', label: 'Key 1', type: 'text', required: false},
                {name: 'value1', label: 'Value 1', type: 'text', required: false}
            ]
        };
        $('.delete-btn').on('click', function() 
        {
            const integrationId = $(this).data('id');
            const integrationType = $(this).data('type');
            
            Swal.fire({
                title: 'Are you sure?',
                text: `You are about to delete the "${integrationType}" integration settings. This action cannot be undone!`,
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
                    Swal.fire({
                        title: 'Deleting...',
                        text: 'Please wait while we delete the integration settings.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading()
                        }
                    });
                    $(`#delete-form-${integrationId}`).submit();
                }
            });
        });

        $('#integration_type').change(function() 
        {
            const type = $(this).val();
            const settingsFields = $('#settingsFields');
            settingsFields.empty();
            
            if (type && fieldTemplates[type]) 
            {
                fieldTemplates[type].forEach(field => {
                    settingsFields.append(`
                        <div class="mb-2">
                            <label for="settings_${field.name}" class="form-label">${field.label}</label>
                            <input type="${field.type}" 
                                class="form-control"
                                id="settings_${field.name}"
                                name="settings[${field.name}]"
                                value="${field.value ?? ''}"
                                ${field.required ? 'required' : ''}>
                        </div>
                    `);
                });

            } 
            else if (type) 
            {
                for (let i = 1; i <= 5; i++) 
                {
                    settingsFields.append(`
                        <div class="row mb-2">
                            <div class="col-md-5">
                                <input type="text" class="form-control" placeholder="Key ${i}" name="settings_keys[]">
                            </div>
                            <div class="col-md-7">
                                <input type="text" class="form-control" placeholder="Value ${i}" name="settings_values[]">
                            </div>
                        </div>
                    `);
                }
            }
        });

        $('#integrationModal').on('show.bs.modal', function(event) 
        {
            const button = $(event.relatedTarget);
            const modal = $(this);
            const action = button.data('action');
            const type = button.data('type');
            const integrationType = button.data('integration_type');
            const settings = button.data('settings');
            const isEncrypted = button.data('is_encrypted');
            const status = button.data('status');
            
            modal.find('.modal-title #modalType').text(type);
            modal.find('form').attr('action', action);
            if (type === 'Update') 
            {
                modal.find('#methodField').html('<input type="hidden" name="_method" value="PUT">');
                if (integrationType)
                {
                    modal.find('#integration_type').val(integrationType).trigger('change');
                    setTimeout(() => {
                        try 
                        {
                            const settingsData = typeof settings === 'string' ? JSON.parse(settings) : settings;
                            for (const key in settingsData) 
                            {
                                $(`#settings_${key}`).val(settingsData[key]);
                            }
                        } 
                        catch (e) 
                        {
                            console.error('Error parsing settings:', e);
                        }
                    }, 300);
                }

                modal.find('#is_encrypted').prop('checked', isEncrypted == 1); 
                if (status) 
                {
                    modal.find('#integration_status').val(status);
                }

            } 
            else 
            {
                modal.find('#methodField').empty();
                modal.find('form')[0].reset();
                modal.find('#integration_type').val('').trigger('change');
                modal.find('#integration_status').val('active'); 
                modal.find('#is_encrypted').prop('checked', false);
            }
        });

        $('#integrationForm').on('submit', function() 
        {
            const integrationType = $('#integration_type').val();
            
            if (!fieldTemplates[integrationType]) 
            {
                const keys = $('input[name="settings_keys[]"]').map(function() 
                {
                    return $(this).val();
                }).get();
                
                const values = $('input[name="settings_values[]"]').map(function() 
                {
                    return $(this).val();
                }).get();
                
                const settingsObj = {};
                
                keys.forEach((key, index) => {
                    if (key && values[index]) 
                    {
                        settingsObj[key] = values[index];
                    }
                });
                $('input[name="settings_keys[]"], input[name="settings_values[]"]').remove();
                $('<input>').attr({
                    type: 'hidden',
                    name: 'settings',
                    value: JSON.stringify(settingsObj)
                }).appendTo('#integrationForm');
            }
        });

        $('#SubmitBtn').closest('form').on('submit', function () 
        {
            $('#SubmitBtn').prop('disabled', true);
            $('#SubmitText').addClass('d-none');
            $('#SubmitSpinner').removeClass('d-none');
        });
    });
</script>
@endsection