@extends('layouts.app')

@section('title', 'Unified Messaging')

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">
                        <i class="fas fa-paper-plane me-2"></i> Unified Messaging
                    </h4>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <form id="messagingForm" method="POST" enctype="multipart/form-data">
                            @csrf
                            @if(isset($exhibitionLeads) && $exhibitionLeads->count() > 0)
                            <input type="hidden" name="exhibition_id" value="{{ $exhibitionId ?? '' }}">
                            @endif
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <h6 class="mb-3">Select Communication Channel</h6>
                                    <div class="d-flex gap-3">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="channel" id="whatsapp" value="whatsapp" checked>
                                            <label class="form-check-label" for="whatsapp">
                                                <i class="fab fa-whatsapp text-success me-1"></i> WhatsApp
                                            </label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="channel" id="email" value="email">
                                            <label class="form-check-label" for="email">
                                                <i class="fas fa-envelope text-danger me-1"></i> Email
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <h6 class="mb-3">Recipients</h6>
                                    
                                    @if(isset($exhibitionLeads) && $exhibitionLeads->count() > 0)
                                    <div class="table-responsive mb-3">
                                        <table class="table table-bordered" id="exhibitionRecipientsTable">
                                            <thead>
                                                <tr>
                                                    <th width="50">
                                                        <input type="checkbox" id="selectAllExhibitionRecipients">
                                                    </th>
                                                    <th>Name</th>
                                                    <th>Phone</th>
                                                    <th>WhatsApp</th>
                                                    <th>Email</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($exhibitionLeads as $lead)
                                                <tr>
                                                    <td>
                                                        <input type="checkbox" 
                                                            class="exhibition-recipient-checkbox"
                                                            data-id="{{ $lead->id }}"
                                                            data-name="{{ $lead->name }}"
                                                            data-phone="{{ $lead->phone }}"
                                                            data-whatsapp="{{ $lead->whatsapp }}"
                                                            data-email="{{ $lead->email }}">
                                                    </td>
                                                    <td>{{ $lead->name }}</td>
                                                    <td>{{ $lead->phone ?? 'N/A' }}</td>
                                                    <td>{{ $lead->whatsapp ?? 'N/A' }}</td>
                                                    <td>{{ $lead->email ?? 'N/A' }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <small class="text-muted">Selected: <span id="selectedExhibitionCount">0</span> exhibition leads</small>
                                    @endif

                                    <div class="mt-4">
                                        <h6 class="mb-3">Add More Leads</h6>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Select from Database</label>
                                                    <select class="form-control select2" id="databaseLeadsSelect" multiple style="width: 100%;">
                                                        @foreach($regularLeads as $lead)
                                                            @if($lead->phone || $lead->email)
                                                                <option value="{{ $lead->id }}" 
                                                                        data-name="{{ $lead->name }}"
                                                                        data-phone="{{ $lead->phone }}"
                                                                        data-email="{{ $lead->email }}">
                                                                    {{ $lead->name }} 
                                                                    @if($lead->phone) ({{ $lead->phone }}) @endif
                                                                </option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Custom Recipients</label>
                                                    <textarea class="form-control" id="customRecipients" 
                                                        rows="3" placeholder="Enter phone numbers or email addresses (one per line)"></textarea>
                                                    <div class="form-text" id="recipientFormat">
                                                        For WhatsApp: 989898XXXX<br>
                                                        For Email: email@example.com
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card mt-3">
                                        <div class="card-header bg-light">
                                            <h6 class="mb-0">Selected Recipients: <span id="totalRecipientCount">0</span></h6>
                                        </div>
                                        <div class="card-body">
                                            <div id="selectedRecipientsSummary" class="badge-container">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <h6 class="mb-3">Message Type</h6>
                                    <div class="d-flex gap-3">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="message_type" id="template" value="template" checked>
                                            <label class="form-check-label" for="template">Use Template</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="message_type" id="custom" value="custom">
                                            <label class="form-check-label" for="custom">Custom Message</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="templateSection">
                               <div class="row mb-4">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Select Template</label>
                                            <div class="input-group">
                                                <select class="form-control" id="templateSelect" name="template_id">
                                                    <option value="">-- Select Template --</option>
                                                    @foreach($whatsappTemplates as $template)
                                                        <option value="{{ $template->id }}">{{ $template->name }}</option>
                                                    @endforeach
                                                </select>
                                                <button type="button" class="btn btn-outline-secondary" id="editTemplateBtn">
                                                    <i class="fas fa-edit"></i> Edit
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Template Preview</label>
                                            <div id="templatePreview" class="p-3 bg-light border rounded" style="min-height: 150px;">
                                                <p class="text-muted mb-0">Select a template to preview</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="customSection" style="display: none;">
                                <div class="row mb-4">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Message Content</label>
                                            <textarea class="form-control" id="customMessage" name="custom_message" 
                                                rows="6" placeholder="Enter your message..."></textarea>
                                            <div class="form-text">
                                                Character limit: <span id="charCount">0/1000</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="emailSubjectSection" class="row mb-4" style="display: none;">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">Email Subject</label>
                                        <input type="text" class="form-control" id="emailSubject" name="subject" placeholder="Enter email subject">
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-4" id="attachmentsSection">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-header bg-light">
                                            <h6 class="mb-0">
                                                <i class="fas fa-paperclip me-2"></i>Attachments
                                                <small class="text-muted">(Optional - Max 10 files, 10MB each)</small>
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="mb-3">
                                                <label class="form-label">Upload Files</label>
                                                <input type="file" class="form-control" id="fileUpload" name="attachments[]" multiple 
                                                       accept="image/*,video/*,audio/*,.pdf,.doc,.docx,.xls,.xlsx,.txt,.ppt,.pptx,.zip">
                                                <div class="form-text">
                                                    Supported: Images, Videos, Audio, PDF, Documents, Presentations, ZIP (Max: 10MB each)
                                                </div>
                                            </div>
                                            
                                            <div id="uploadedFilesPreview" class="mt-3">
                                            </div>
                                            
                                            <div class="mt-3">
                                                <button type="button" class="btn btn-sm btn-danger" id="clearAttachmentsBtn" style="display: none;">
                                                    <i class="fas fa-trash me-1"></i> Clear All Attachments
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="d-flex justify-content-between">
                                        @if(isset($exhibitionId))
                                        <a href="{{ route('exhibition.view', $exhibitionId) }}" class="btn btn-secondary">
                                            <i class="fas fa-arrow-left me-1"></i> Back to Exhibition
                                        </a>
                                        @else
                                        <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                                            <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
                                        </a>
                                        @endif
                                        <div>
                                            <button type="submit" class="btn btn-primary" id="sendBtn">
                                                <i class="fas fa-paper-plane me-1"></i> Send Messages
                                                <span class="spinner-border spinner-border-sm d-none" id="sendSpinner"></span>
                                            </button>
                                        </div>
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

<div class="modal fade" id="previewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-eye me-2"></i>Message Preview
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            This is a preview of how your message will appear.
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <h6>Channel: <span id="previewChannel" class="badge bg-success">WhatsApp</span></h6>
                        <div id="channelPreview" class="mb-4">
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12">
                        <h6>Message Content:</h6>
                        <div id="previewContent" class="p-3 border rounded bg-light">
                        </div>
                    </div>
                </div>
                
                @if($attachments ?? false)
                <div class="row mt-3">
                    <div class="col-md-12">
                        <h6>Attachments (<span id="previewAttachmentCount">0</span>):</h6>
                        <div id="previewAttachments" class="p-2 border rounded">
                        </div>
                    </div>
                </div>
                @endif
                
                <div class="row mt-3">
                    <div class="col-md-12">
                        <h6>Recipients (<span id="previewRecipientCount">0</span>):</h6>
                        <div id="previewRecipients" class="badge-container p-2 border rounded"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="confirmSend">
                    <i class="fas fa-paper-plane me-1"></i> Confirm & Send
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    .badge-container 
    {
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
        max-height: 200px;
        overflow-y: auto;
    }
    
    .recipient-badge 
    {
        font-size: 0.75rem;
        padding: 3px 8px;
    }
    
    .select2-container 
    {
        width: 100% !important;
    }
    
    #templatePreview 
    {
        min-height: 150px;
    }
    
    .whatsapp-preview 
    {
        background: #f0f0f0;
        border-radius: 10px;
        padding: 15px;
        max-width: 400px;
        margin: 0 auto;
    }
    
    .email-preview 
    {
        border: 1px solid #ddd;
        border-radius: 5px;
        padding: 20px;
        background: white;
    }
    
    .file-preview 
    {
        border: 1px solid #dee2e6;
        border-radius: 5px;
        padding: 10px;
        margin: 5px 0;
        background: #f8f9fa;
        display: flex;
        align-items: center;
    }
    
    .file-preview img 
    {
        width: 50px;
        height: 50px;
        object-fit: cover;
        margin-right: 15px;
    }
    
    .file-icon
    {
        font-size: 2rem;
        margin-right: 15px;
    }
    
    .attachment-badge 
    {
        font-size: 0.75rem;
        padding: 3px 8px;
        margin: 2px;
    }
</style>

<script>
$(document).ready(function() 
{
    let selectedRecipients = [];
    let currentChannel = 'whatsapp';
    let attachments = [];
    $('#databaseLeadsSelect').select2({
        placeholder: 'Search and select leads...',
        allowClear: true
    });
    $('input[name="channel"]').change(function() 
    {
        currentChannel = $(this).val();
        updateChannelSettings();
        updateRecipientValidation();
        loadTemplates(currentChannel);
    });

    $('input[name="message_type"]').change(function() 
    {
        if ($(this).val() === 'template') 
        {
            $('#templateSection').show();
            $('#customSection').hide();
        } 
        else 
        {
            $('#templateSection').hide();
            $('#customSection').show();
        }
    });
    $('#customMessage').on('input', function()
    {
        const length = $(this).val().length;
        $('#charCount').text(length + '/1000');
    });
    $('#selectAllExhibitionRecipients').change(function() 
    {
        const isChecked = $(this).prop('checked');
        $('.exhibition-recipient-checkbox:not(:disabled)').prop('checked', isChecked);
        updateSelectedRecipients();
    });
    $(document).on('change', '.exhibition-recipient-checkbox', function() 
    {
        updateSelectedRecipients();
    });
    $('#databaseLeadsSelect').on('change', function() 
    {
        updateSelectedRecipients();
    });
    $('#customRecipients').on('blur', function() 
    {
        updateSelectedRecipients();
    });
    $('#templateSelect').change(function() 
    {
        const templateId = $(this).val();
        if (templateId) 
        {
            loadTemplatePreview(templateId);
        } 
        else 
        {
            $('#templatePreview').html('<p class="text-muted mb-0">Select a template to preview</p>');
        }
    });

    $('#fileUpload').on('change', function() 
    {
        previewFiles(this.files);
        updateFileCount();
    });

    $('#clearAttachmentsBtn').click(function() 
    {
        $('#fileUpload').val('');
        $('#uploadedFilesPreview').empty();
        attachments = [];
        $(this).hide();
    });

    $(document).on('click', '.remove-file-btn', function() 
    {
        const index = $(this).data('index');
        removeFile(index);
    });

    $('#previewBtn').click(function() 
    {
        generatePreview();
    });

    $('#messagingForm').submit(function(e) 
    {
        e.preventDefault();
        sendMessage();
    });

    $('#confirmSend').click(function() 
    {
        $('#previewModal').modal('hide');
        sendMessage();
    });

    function updateChannelSettings() 
    {
        let formatText = '';
        if (currentChannel === 'whatsapp')
        {
            formatText = 'Enter phone numbers in format: 989898XXXX (one per line)';
        } 
        else if (currentChannel === 'email') 
        {
            formatText = 'Enter email addresses (one per line)';
        }
        $('#recipientFormat').text(formatText);
        if (currentChannel === 'email') 
        {
            $('#emailSubjectSection').show();
            $('#emailSubject').prop('required', true);
        } 
        else 
        {
            $('#emailSubjectSection').hide();
            $('#emailSubject').prop('required', false);
        }
    }

    function updateRecipientValidation()
    {
        $('.exhibition-recipient-checkbox').each(function() 
        {
            const $checkbox = $(this);
            const phone = $checkbox.data('phone');
            const email = $checkbox.data('email');
            
            if (currentChannel === 'whatsapp') 
            {
                if (!phone || phone === 'N/A') 
                {
                    $checkbox.prop('disabled', true);
                    if ($checkbox.prop('checked')) 
                    {
                        $checkbox.prop('checked', false);
                    }
                } 
                else 
                {
                    $checkbox.prop('disabled', false);
                }
            } 
            else if (currentChannel === 'email') 
            {
                if (!email || email === 'N/A') 
                {
                    $checkbox.prop('disabled', true);
                    if ($checkbox.prop('checked')) 
                    {
                        $checkbox.prop('checked', false);
                    }
                } 
                else 
                {
                    $checkbox.prop('disabled', false);
                }
            }
        });
        
        updateSelectedRecipients();
    }

    function updateSelectedRecipients() 
    {
        selectedRecipients = [];
        $('.exhibition-recipient-checkbox:checked:not(:disabled)').each(function() 
        {
            selectedRecipients.push({
                type: 'exhibition',
                id: $(this).data('id'),
                name: $(this).data('name'),
                phone: $(this).data('phone'),
                whatsapp: $(this).data('whatsapp'),
                email: $(this).data('email')
            });
        });

        $('#databaseLeadsSelect option:selected').each(function() 
        {
            selectedRecipients.push({
                type: 'database',
                id: $(this).val(),
                name: $(this).data('name'),
                phone: $(this).data('phone'),
                whatsapp: $(this).data('whatsapp') || $(this).data('phone'),
                email: $(this).data('email')
            });
        });

        const customRecipients = $('#customRecipients').val().split('\n').filter(r => r.trim() !== '');
        customRecipients.forEach(function(recipient) 
        {
            selectedRecipients.push({
                type: 'custom',
                name: 'Custom',
                phone: currentChannel === 'whatsapp' ? recipient.trim() : '',
                whatsapp: currentChannel === 'whatsapp' ? recipient.trim() : '',
                email: currentChannel === 'email' ? recipient.trim() : '',
                contact: recipient.trim()
            });
        });
        const exhibitionCount = selectedRecipients.filter(r => r.type === 'exhibition').length;
        $('#selectedExhibitionCount').text(exhibitionCount);
        $('#totalRecipientCount').text(selectedRecipients.length);
        updateRecipientsSummary();
    }

    function updateRecipientValidation() 
    {
        $('.exhibition-recipient-checkbox').each(function() 
        {
            const $checkbox = $(this);
            const phone = $checkbox.data('phone');
            const whatsapp = $checkbox.data('whatsapp');
            const email = $checkbox.data('email');
            
            if (currentChannel === 'whatsapp') 
            {
                if ((!phone || phone === 'N/A') && (!whatsapp || whatsapp === 'N/A')) 
                {
                    $checkbox.prop('disabled', true);
                    if ($checkbox.prop('checked')) 
                    {
                        $checkbox.prop('checked', false);
                    }
                } 
                else 
                {
                    $checkbox.prop('disabled', false);
                }
            } 
            else if (currentChannel === 'email') 
            {
                if (!email || email === 'N/A') 
                {
                    $checkbox.prop('disabled', true);
                    if ($checkbox.prop('checked')) {
                        $checkbox.prop('checked', false);
                    }
                }
                else 
                {
                    $checkbox.prop('disabled', false);
                }
            }
        });
        updateSelectedRecipients();
    }

    function updateRecipientsSummary() 
    {
        let html = '';
        selectedRecipients.forEach(function(recipient, index) 
        {
            let badgeClass = 'bg-primary';
            if (recipient.type === 'exhibition') badgeClass = 'bg-success';
            if (recipient.type === 'database') badgeClass = 'bg-info';
            if (recipient.type === 'custom') badgeClass = 'bg-warning';
            
            let contact = '';
            if (currentChannel === 'whatsapp') 
            {
                contact = recipient.phone || recipient.contact || 'No phone';
            } 
            else 
            {
                contact = recipient.email || recipient.contact || 'No email';
            }
            
            html += `
                <span class="badge ${badgeClass} me-1 mb-1 recipient-badge" data-index="${index}">
                    ${recipient.name}: ${contact}
                    <button type="button" class="btn-close btn-close-white btn-sm ms-1" onclick="removeRecipient(${index})"></button>
                </span>
            `;
        });
        
        $('#selectedRecipientsSummary').html(html || '<p class="text-muted mb-0">No recipients selected</p>');
    }

    function removeRecipient(index) 
    {
        const recipient = selectedRecipients[index];
        
        if (recipient.type === 'exhibition') 
        {
            $(`.exhibition-recipient-checkbox[data-id="${recipient.id}"]`).prop('checked', false);
        } 
        else if (recipient.type === 'database') 
        {
            $(`#databaseLeadsSelect option[value="${recipient.id}"]`).prop('selected', false);
            $('#databaseLeadsSelect').trigger('change');
        } 
        else if (recipient.type === 'custom') 
        {
            const lines = $('#customRecipients').val().split('\n');
            const updatedLines = lines.filter(line => line.trim() !== recipient.contact);
            $('#customRecipients').val(updatedLines.join('\n'));
        }
        
        selectedRecipients.splice(index, 1);
        updateSelectedRecipients();
    }

    function previewFiles(files) 
    {
        const previewContainer = $('#uploadedFilesPreview');
        const fileInput = $('#fileUpload')[0];
        previewContainer.empty();
        
        if (files.length === 0) 
        {
            $('#clearAttachmentsBtn').hide();
            attachments = [];
            return;
        }
        $('#clearAttachmentsBtn').show();
        if (files.length > 10) 
        {
            toastr.warning('Maximum 10 files allowed. Only first 10 files will be uploaded.');
            files = Array.from(files).slice(0, 10);
        }
        let hasLargeFile = false;
        for (let i = 0; i < files.length; i++)
        {
            if (files[i].size > 10 * 1024 * 1024) 
            { 
                hasLargeFile = true;
                break;
            }
        }
        
        if (hasLargeFile) 
        {
            toastr.error('Maximum file size is 10MB. Please reduce file sizes.');
            $('#fileUpload').val('');
            previewContainer.empty();
            $('#clearAttachmentsBtn').hide();
            return;
        }
        attachments = Array.from(files);
        for (let i = 0; i < files.length; i++) 
        {
            const file = files[i];
            const reader = new FileReader();
            reader.onload = function(e) 
            {
                let previewHtml = '';
                const fileType = getFileType(file.type);
                const fileIcon = getFileIcon(file.type);
                
                if (fileType === 'image') 
                {
                    previewHtml = `
                        <div class="file-preview mb-2" data-index="${i}">
                            <img src="${e.target.result}" class="me-3" alt="${file.name}" style="width: 50px; height: 50px; object-fit: cover;">
                            <div class="flex-grow-1">
                                <strong>${file.name}</strong><br>
                                <small class="text-muted">${formatFileSize(file.size)} • Image</small>
                            </div>
                            <button type="button" class="btn btn-sm btn-danger remove-file-btn" data-index="${i}">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    `;
                } 
                else 
                {
                    previewHtml = `
                        <div class="file-preview mb-2" data-index="${i}">
                            <i class="${fileIcon} file-icon text-primary"></i>
                            <div class="flex-grow-1">
                                <strong>${file.name}</strong><br>
                                <small class="text-muted">${formatFileSize(file.size)} • ${fileType}</small>
                            </div>
                            <button type="button" class="btn btn-sm btn-danger remove-file-btn" data-index="${i}">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    `;
                }
                
                previewContainer.append(previewHtml);
            };
            
            reader.readAsDataURL(file);
        }
    }

    function removeFile(index) 
    {
        const fileInput = $('#fileUpload')[0];
        const dt = new DataTransfer();
        attachments.splice(index, 1);
        for (let i = 0; i < attachments.length; i++) 
        {
            dt.items.add(attachments[i]);
        }
        
        fileInput.files = dt.files;
        previewFiles(fileInput.files);
    }

    function updateFileCount() 
    {
        const fileCount = attachments.length;
        if (fileCount > 0) 
        {
            $('#clearAttachmentsBtn').show();
        } 
        else 
        {
            $('#clearAttachmentsBtn').hide();
        }
    }

    function getFileType(mimeType) 
    {
        if (mimeType.startsWith('image/')) return 'Image';
        if (mimeType.startsWith('video/')) return 'Video';
        if (mimeType.startsWith('audio/')) return 'Audio';
        if (mimeType === 'application/pdf') return 'PDF';
        if (mimeType.includes('word') || mimeType.includes('document')) return 'Document';
        if (mimeType.includes('excel') || mimeType.includes('spreadsheet')) return 'Spreadsheet';
        if (mimeType.includes('powerpoint') || mimeType.includes('presentation')) return 'Presentation';
        if (mimeType === 'application/zip') return 'ZIP Archive';
        return 'File';
    }

    function getFileIcon(mimeType) 
    {
        if (mimeType.startsWith('image/')) return 'fas fa-image';
        if (mimeType.startsWith('video/')) return 'fas fa-video';
        if (mimeType.startsWith('audio/')) return 'fas fa-music';
        if (mimeType === 'application/pdf') return 'fas fa-file-pdf';
        if (mimeType.includes('word') || mimeType.includes('document')) return 'fas fa-file-word';
        if (mimeType.includes('excel') || mimeType.includes('spreadsheet')) return 'fas fa-file-excel';
        if (mimeType.includes('powerpoint') || mimeType.includes('presentation')) return 'fas fa-file-powerpoint';
        if (mimeType === 'application/zip') return 'fas fa-file-archive';
        return 'fas fa-file';
    }

    function formatFileSize(bytes) 
    {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    function loadTemplates(channel)
    {
        $.ajax({
            url: `/messaging/templates/${channel}`,
            type: 'GET',
            success: function(response) 
            {
                if (response.success) 
                {
                    const $select = $('#templateSelect');
                    $select.empty().append('<option value="">-- Select Template --</option>');
                    response.templates.forEach(function(template) 
                    {
                        $select.append(new Option(template.name, template.id));
                    });
                }
            }
        });
    }

    function loadTemplatePreview(templateId) 
    {
        $.ajax({
            url: `/messaging/templates/${currentChannel}/${templateId}/preview`,
            type: 'GET',
            success: function(response) 
            {
                if (response.success) 
                {
                    $('#templatePreview').html(response.preview);
                } 
                else 
                {
                    $('#templatePreview').html('<p class="text-danger">Error loading preview</p>');
                }
            },
            error: function() 
            {
                $('#templatePreview').html('<p class="text-danger">Error loading preview</p>');
            }
        });
    }

    function generatePreview() 
    {
        const channel = $('input[name="channel"]:checked').val();
        const messageType = $('input[name="message_type"]:checked').val();
        
        if (selectedRecipients.length === 0) 
        {
            toastr.error('Please select at least one recipient');
            return;
        }

        if (currentChannel === 'whatsapp') 
        {
            const invalidRecipients = selectedRecipients.filter(r => !r.phone || r.phone === 'N/A');
            if (invalidRecipients.length > 0) 
            {
                toastr.error('Some selected recipients don\'t have phone numbers for WhatsApp');
                return;
            }
        } 
        else 
        {
            const invalidRecipients = selectedRecipients.filter(r => !r.email || r.email === 'N/A');
            if (invalidRecipients.length > 0) {
                toastr.error('Some selected recipients don\'t have email addresses');
                return;
            }
        }
        if (messageType === 'template' && !$('#templateSelect').val()) 
        {
            toastr.error('Please select a template');
            return;
        }

        if (messageType === 'custom' && !$('#customMessage').val().trim()) {
            toastr.error('Please enter a message');
            return;
        }

        if (currentChannel === 'email' && !$('#emailSubject').val().trim()) {
            toastr.error('Please enter an email subject');
            return;
        }
        
        // Update channel badge
        let channelBadgeClass = '';
        let channelIcon = '';
        if (channel === 'whatsapp') {
            channelBadgeClass = 'bg-success';
            channelIcon = '<i class="fab fa-whatsapp me-1"></i>';
        } else {
            channelBadgeClass = 'bg-danger';
            channelIcon = '<i class="fas fa-envelope me-1"></i>';
        }
        
        $('#previewChannel').html(channelIcon + channel.toUpperCase()).addClass('badge ' + channelBadgeClass);
        
        // Generate channel-specific preview
        let channelPreview = '';
        if (channel === 'whatsapp') {
            channelPreview = `
                <div class="whatsapp-preview">
                    <div class="d-flex align-items-center mb-3">
                        <i class="fab fa-whatsapp fa-2x text-success me-2"></i>
                        <h5 class="mb-0">WhatsApp Message</h5>
                    </div>
                    <div id="previewMessageContent"></div>
                </div>
            `;
        } else {
            const subject = messageType === 'template' ? $('#emailSubject').val() : $('#emailSubject').val();
            channelPreview = `
                <div class="email-preview">
                    <h5><i class="fas fa-envelope text-danger me-2"></i>Email Preview</h5>
                    <hr>
                    <p><strong>Subject:</strong> ${subject || 'No subject'}</p>
                    <div id="previewMessageContent" class="mt-3"></div>
                </div>
            `;
        }
        
        $('#channelPreview').html(channelPreview);
        
        // Generate message content preview
        let messageContent = '';
        if (messageType === 'template') {
            const template = $('#templateSelect option:selected');
            if (template.val()) {
                messageContent = $('#templatePreview').html();
            }
        } else {
            messageContent = $('#customMessage').val().replace(/\n/g, '<br>');
        }
        
        $('#previewContent').html(messageContent || '<p class="text-muted">No message content</p>');
        
        // Generate attachments preview
        if (attachments.length > 0) {
            let attachmentsHtml = '<div class="badge-container">';
            attachments.forEach(function(file, index) {
                const fileType = getFileType(file.type);
                attachmentsHtml += `
                    <span class="badge bg-info attachment-badge">
                        <i class="${getFileIcon(file.type)} me-1"></i>
                        ${file.name} (${formatFileSize(file.size)})
                    </span>
                `;
            });
            attachmentsHtml += '</div>';
            $('#previewAttachments').html(attachmentsHtml);
            $('#previewAttachmentCount').text(attachments.length);
        }
        
        // Generate recipients preview
        $('#previewRecipientCount').text(selectedRecipients.length);
        let recipientsHtml = '';
        selectedRecipients.forEach(function(recipient) {
            let contact = '';
            if (currentChannel === 'whatsapp') {
                contact = recipient.phone || recipient.contact || 'No phone';
            } else {
                contact = recipient.email || recipient.contact || 'No email';
            }
            
            recipientsHtml += `<span class="badge bg-secondary me-1 mb-1">${recipient.name}: ${contact}</span>`;
        });
        $('#previewRecipients').html(recipientsHtml || '<p class="text-muted">No recipients</p>');
        
        // Show preview modal
        $('#previewModal').modal('show');
    }

    function sendMessage() {
    if (selectedRecipients.length === 0) {
        toastr.error('Please select at least one recipient');
        return;
    }

    // Prepare form data
    const formData = new FormData($('#messagingForm')[0]);
    
    // Add recipients to form data
    selectedRecipients.forEach(function(recipient, index) {
        formData.append(`recipients[${index}][type]`, recipient.type);
        formData.append(`recipients[${index}][id]`, recipient.id || '');
        formData.append(`recipients[${index}][name]`, recipient.name);
        formData.append(`recipients[${index}][phone]`, recipient.phone || '');
        formData.append(`recipients[${index}][whatsapp]`, recipient.whatsapp || ''); // ADD THIS LINE
        formData.append(`recipients[${index}][email]`, recipient.email || '');
        formData.append(`recipients[${index}][contact]`, recipient.contact || '');
    });
    
    // Add attachments
    for (let i = 0; i < attachments.length; i++) {
        formData.append('attachments[]', attachments[i]);
    }
    
    // Show loading spinner
    $('#sendBtn').prop('disabled', true);
    $('#sendSpinner').removeClass('d-none');
    
    $.ajax({
        url: '{{ route("messaging.send") }}',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.success) {
                toastr.success(response.message);
                
                // Show detailed results
                if (response.detailed_results) {
                    response.detailed_results.forEach(function(result) {
                        if (result.details) {
                            result.details.forEach(function(detail) {
                                if (detail.phone_type) {
                                    console.log(`Sent to ${detail.phone_type}: ${detail.phone_number} - ${detail.success ? 'Success' : 'Failed'}`);
                                }
                            });
                        }
                    });
                }
                
                // Reset form
                $('#messagingForm')[0].reset();
                $('#databaseLeadsSelect').val(null).trigger('change');
                $('.exhibition-recipient-checkbox').prop('checked', false);
                $('#selectAllExhibitionRecipients').prop('checked', false);
                $('#customRecipients').val('');
                $('#fileUpload').val('');
                $('#uploadedFilesPreview').empty();
                $('#clearAttachmentsBtn').hide();
                
                selectedRecipients = [];
                attachments = [];
                updateSelectedRecipients();
                
            } else {
                toastr.error(response.message || 'Failed to send messages');
            }
        },
        error: function(xhr) {
            const errors = xhr.responseJSON?.errors;
            if (errors) {
                Object.values(errors).forEach(function(error) {
                    toastr.error(error[0]);
                });
            } else {
                toastr.error('An error occurred while sending messages');
            }
        },
        complete: function() {
            $('#sendBtn').prop('disabled', false);
            $('#sendSpinner').addClass('d-none');
        }
    });
}
    // Initialize
    updateChannelSettings();
    updateRecipientValidation();
    
    // Cleanup old temp files on page load
    $.ajax({
        url: '{{ route("messaging.cleanup-temp-files") }}',
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}'
        }
    });
});

// Global function for removing recipients from summary
function removeRecipient(index) {
    // This function is called from the onclick event in the HTML
    // The actual implementation is in the updateRecipientsSummary function
    const event = new Event('removeRecipient');
    event.index = index;
    document.dispatchEvent(event);
}

// Listen for remove recipient event
document.addEventListener('removeRecipient', function(e) {
    // Find and trigger removal
    const recipientElement = $(`.recipient-badge[data-index="${e.index}"]`);
    if (recipientElement.length) {
        const recipient = selectedRecipients[e.index];
        if (recipient.type === 'exhibition') {
            $(`.exhibition-recipient-checkbox[data-id="${recipient.id}"]`).prop('checked', false).trigger('change');
        } else if (recipient.type === 'database') {
            $(`#databaseLeadsSelect option[value="${recipient.id}"]`).prop('selected', false);
            $('#databaseLeadsSelect').trigger('change');
        } else if (recipient.type === 'custom') {
            const lines = $('#customRecipients').val().split('\n');
            const updatedLines = lines.filter(line => line.trim() !== recipient.contact);
            $('#customRecipients').val(updatedLines.join('\n'));
        }
        
        selectedRecipients.splice(e.index, 1);
        updateSelectedRecipients();
    }
});

// Edit Template Button Click
$('#editTemplateBtn').click(function() {
    const templateId = $('#templateSelect').val();
    if (!templateId) {
        toastr.error('Please select a template to edit');
        return;
    }
    
    // Get the channel
    const channel = $('input[name="channel"]:checked').val();
    
    // Redirect to edit page with template data
    window.location.href = `/messaging/${templateId}/edit?channel=${channel}`;
});

// Or if you want to open in new tab:
$('#editTemplateBtn').click(function() {
    const templateId = $('#templateSelect').val();
    if (!templateId) {
        toastr.error('Please select a template to edit');
        return;
    }
    
    const channel = $('input[name="channel"]:checked').val();
    window.open(`/messaging/${templateId}/edit?channel=${channel}`, '_blank');
});
</script>
@endsection