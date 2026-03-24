@extends('layouts.app')
@section('title', 'Post Sales')
@section('content')
@include('modals.post-sale')
@include('modals.documents')
@include('modals.showShareLinks')

<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Post Sales</h4>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#postSaleModal">
                        <i class="fas fa-plus me-2"></i>Add Customer
                    </button>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <table class="table table-bordered dt-responsive nowrap w-100" id="table">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Applicant</th>
                            <th>Contact</th>
                            <th>Project</th>
                            <th>Unit</th>
                            <th>Sales Person</th>
                            <th>Category</th>
                            <th>DOA</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($postSales as $i => $ps)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $ps->applicant_name }}</td>
                            <td>{{ $ps->applicant_number }}</td>
                            <td>{{ $ps->project_name }}</td>
                            <td>{{ $ps->unit_number }}</td>
                            <td>{{ $ps->sales_person_name }}</td>
                            <td>{{ $ps->project_category }}</td>
                            <td>{{ $ps->doa ? \Carbon\Carbon::parse($ps->doa)->format('d/m/Y') : '-' }}</td>
                            <td>
                                <button class="btn btn-sm btn-primary edit-btn" id="editPostSale" data-id="{{ $ps->id }}">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-info" onclick="showDocuments({{ $ps->id }})">
                                    <i class="fas fa-file-upload"></i>
                                </button>
                                <button class="btn btn-sm btn-success" onclick="showShareLinks({{ $ps->id }})" title="Manage Share Links">
                                    <i class="fas fa-link"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="deletePostSale({{ $ps->id }})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
    let currentPostSaleId = null;

    $('#project_category_id').change(function() 
    {
        var categoryName = $(this).val();
        if (categoryName) 
        {
            $.ajax({
                url: "{{ url('post-sale/subcategories') }}/" + encodeURIComponent(categoryName),
                type: "GET",
                success: function(response) 
                {
                    $('#project_subcategory_id').empty().append('<option value="">Select Subcategory</option>');
                    if (response.success && response.data.length > 0) 
                    {
                        $.each(response.data, function(key, value) 
                        {
                            $('#project_subcategory_id').append('<option value="'+value.name+'">'+value.name+'</option>');
                        });
                    } 
                    else 
                    {
                        $('#project_subcategory_id').append('<option value="">No subcategories found</option>');
                    }
                    const selectedSubcategory = $('#project_subcategory_id').attr('data-selected');
                    if (selectedSubcategory) 
                    {
                        $('#project_subcategory_id').val(selectedSubcategory);
                        $('#project_subcategory_id').removeAttr('data-selected');
                    }
                },
                error: function(xhr) 
                {
                    console.error('Error fetching subcategories:', xhr.responseText);
                    $('#project_subcategory_id').empty().append('<option value="">Error loading subcategories</option>');
                }
            });
        } 
        else 
        {
            $('#project_subcategory_id').empty().append('<option value="">Select Subcategory</option>');
        }
    });

    $(document).on('click', '#editPostSale', function() 
    {
        var id = $(this).attr('data-id');
        $.ajax({
            url: "{{ url('post-sale') }}/" + id + "/edit",
            type: "GET",
            success: function(response) 
            {
                if (response && response.success && response.data) 
                {
                    const data = response.data;
                    $('#post-sale-id').val(data.id);
                    $('#lead_id').val(data.lead_id).trigger('change');
                    $('#sales_person_id').val(data.sales_person_id).trigger('change');
                    $('#applicant_name').val(data.applicant_name);
                    $('#applicant_number').val(data.applicant_number);
                    $('#project_name').val(data.project_name);
                    $('#unit_number').val(data.unit_number);
                    $('#project_category_id').val(data.project_category).trigger('change');
                    $('#dob').val(data.dob ? data.dob.split('T')[0] : '');
                    $('#doa').val(data.doa ? data.doa.split('T')[0] : '');
                    $('#email').val(data.email);
                    $('#permanent_address').val(data.permanent_address);
                    $('#current_address').val(data.current_address);

                    if (data.checklist) 
                    {
                        $('.checklist-item').prop('checked', false);
                        var checklistItems = JSON.parse(data.checklist);
                        checklistItems.forEach(function(item) 
                        {
                            $('#checklist_' + item).prop('checked', true);
                        });
                    }
                    $('#project_subcategory_id').attr('data-selected', data.project_sub_category);

                    $('#postSaleModalLabel').text('Edit Customer');
                    $('#postSaleForm').attr('action', "{{ url('post-sale') }}/" + id);
                    $('#postSaleForm').find('input[name="_method"]').remove();
                    $('#postSaleForm').append('<input type="hidden" name="_method" value="PUT">');
                    $('#postSaleModal').modal('show');
                }
            },
            error: function(xhr) 
            {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Failed to load post sale data.',
                    showConfirmButton: true
                });
            }
        });
    });

    $('#postSaleForm').on('submit', function(e) 
    {
        if (this.checkValidity() === false) 
        {
            e.preventDefault();
            e.stopPropagation();
        }
        this.classList.add('was-validated');
    });

    $('[data-bs-target="#postSaleModal"]').click(function() 
    {
        $('#postSaleForm')[0].reset();
        $('#postSaleForm').find('input[name="_method"]').remove();
        $('#postSaleForm').attr('action', "{{ url('post-sale') }}");
        $('#postSaleModalLabel').text('Add Customer');
        $('#project_subcategory_id').empty().append('<option value="">Select Subcategory</option>');
        $('#project_subcategory_id').removeAttr('data-selected');
        $('.checklist-item').prop('checked', false);
        $('#post-sale-id').val('');
    });

    function showDocuments(id) 
    {
        currentPostSaleId = id;
        $('#post_sale_id').val(id);
        $('#documentsModal').modal('show');
        loadDocuments();
    }

    function showShareLinks(id) 
    {
        currentPostSaleId = id;
        $('#shareLink').val('Generating link...');
        $('#shareLinksModal').modal('show');
        generateShareLink();
    }

    function generateShareLink() 
    {
        $.ajax({
            url: '{{ route("post-sale.rate-link") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                id: currentPostSaleId
            },
            success: function(response) 
            {
                if (response.link) 
                {
                    $('#shareLink').val(response.link);
                    updateShareButtons(response.link);
                    toastr.success('Share link generated! This link expires in 7 days.');
                } 
                else 
                {
                    $('#shareLink').val('Failed to generate link');
                    toastr.error('Failed to generate share link');
                }
            },
            error: function(xhr) 
            {
                $('#shareLink').val('Error generating link');
                toastr.error('Error generating share link: ' + (xhr.responseJSON?.message || 'Unknown error'));
            }
        });
    }

    function updateShareButtons(link) 
    {
        const encodedLink = encodeURIComponent(link);
        const text = encodeURIComponent('Please rate our service using this link');
        
        $('#shareWhatsApp').attr('href', `https://wa.me/?text=${text}%20${encodedLink}`);
        $('#shareEmail').attr('href', `mailto:?subject=Rate Our Service&body=${text}%20${encodedLink}`);
        $('#shareTelegram').attr('href', `https://t.me/share/url?url=${encodedLink}&text=${text}`);
        $('#shareFacebook').attr('href', `https://www.facebook.com/sharer/sharer.php?u=${encodedLink}`);
        $('#shareLinkedIn').attr('href', `https://www.linkedin.com/sharing/share-offsite/?url=${encodedLink}`);
        $('#shareTwitter').attr('href', `https://twitter.com/intent/tweet?url=${encodedLink}&text=${text}`);
    }

    $('#copyLink').click(function() 
    {
        const link = $('#shareLink').val();
        navigator.clipboard.writeText(link).then(function() 
        {
            toastr.success('Link copied to clipboard!');
        }).catch(function() 
        {
            $('#shareLink').select();
            document.execCommand('copy');
            toastr.success('Link copied to clipboard!');
        });
    });

    function loadDocuments() 
    {
        $.get(`/post-sale/${currentPostSaleId}/documents`, function(res) 
        {
            const list = $('#documentsList');
            list.empty();
            if (res.data.length === 0) 
            {
                list.append(`<tr><td colspan="4" class="text-center text-muted">No documents</td></tr>`);
                return;
            }
            res.data.forEach(doc => {
                const size = (doc.file_size / 1024).toFixed(1) + ' KB';
                list.append(`
                    <tr>
                        <td>${doc.document_name}</td>
                        <td><span class="badge bg-info">${doc.file_type.toUpperCase()}</span></td>
                        <td>${size}</td>
                        <td>
                            <a href="/storage/${doc.file_path}" target="_blank" class="btn btn-sm btn-primary">
                                <i class="fas fa-eye"></i>
                            </a>
                            <button onclick="deleteDocument(${doc.id})" class="btn btn-sm btn-danger">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `);
            });
        });
    }

    $('#uploadForm').on('submit', function(e) 
    {
        e.preventDefault();
        const formData = new FormData(this);
        $.ajax({
            url: `/post-sale/${currentPostSaleId}/upload-document`,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(res) 
            {
                Swal.fire('Success', res.message, 'success');
                $('#uploadForm')[0].reset();
                loadDocuments();
            },
            error: function(xhr) 
            {
                Swal.fire('Error', xhr.responseJSON?.message || 'Upload failed', 'error');
            }
        });
    });

    function deleteDocument(id) 
    {
        Swal.fire({
            title: 'Delete?',
            text: 'This cannot be undone!',
            icon: 'warning',
            showCancelButton: true,
        }).then(result => {
            if (result.isConfirmed) 
            {
                $.ajax({
                    url: `/post-sale/document/${id}`,
                    type: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: () => {
                        Swal.fire('Deleted!', '', 'success');
                        loadDocuments();
                    },
                    error: () => Swal.fire('Error', 'Delete failed', 'error')
                });
            }
        });
    }

    function deletePostSale(id) 
    {
        Swal.fire({
            title: 'Delete Customer?',
            icon: 'warning',
            showCancelButton: true,
        }).then(result => {
            if (result.isConfirmed) 
            {
                $.ajax({
                    url: `/post-sale/${id}`,
                    type: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: () => {
                        Swal.fire('Deleted!', '', 'success').then(() => location.reload());
                    }
                });
            }
        });
    }
</script>
@endsection