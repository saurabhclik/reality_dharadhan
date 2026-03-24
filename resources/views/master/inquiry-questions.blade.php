@extends('layouts.app')

@section('title', 'Inquiry Questions | Pro-leadexpertz')

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Manage Inquiry Questions</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('dashboard') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item active">Inquiry Questions</li>
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
                            <h4 class="card-title mb-0">Inquiry Questions</h4>
                            <button class="btn btn-primary btn-small px-4 py-1 rounded-pill fw-bold text-white shadow-lg add-inquiry"
                                data-bs-toggle="modal"
                                data-bs-target="#Modalbox"
                                data-action="{{ route('inquiry-question.store') }}"
                                data-type="Create"
                                data-modal="Inquiry Question">
                                <i class="fa fa-plus"></i> Add Question
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered dt-responsive nowrap w-100 data-table">
                                <thead class="table-light">
                                    <tr>
                                        <th>S.No</th>
                                        <th>Question</th>
                                        <th>Active</th>
                                        <th>Update</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($questions as $question)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $question->question_text }}</td>
                                        <td>
                                            <span class="badge bg-{{ $question->is_active ? 'success' : 'danger' }}">
                                                {{ $question->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td>
                                            <button 
                                                class="btn btn-sm btn-outline-primary edit-btn"
                                                data-id="{{ $question->id }}"
                                                data-question="{{ $question->question_text }}"
                                                data-is_active="{{ $question->is_active }}"
                                                data-bs-toggle="modal"
                                                data-bs-target="#Modalbox"
                                                data-action="{{ url('inquiry-question/update') }}"
                                                data-type="Update"
                                                data-modal="Inquiry Question">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-end mt-3">
                            {!! $questions->links('pagination::bootstrap-5') !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="Modalbox" tabindex="-1" aria-labelledby="ModalboxLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form id="inquiryForm" method="POST" action="">
                    @csrf
                    <input type="hidden" name="id" id="inquiry-id" value="">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="ModalboxLabel">Add Inquiry Question</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="question_text" class="form-label">Question Text</label>
                                <input type="text" class="form-control" id="question_text" name="question_text" placeholder="Enter inquiry question" required>
                            </div>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" checked>
                                <label class="form-check-label" for="is_active">Active</label>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary" id="SubmitBtn">
                                <span id="SubmitText">Save</span>
                                <span id="SubmitSpinner" class="d-none">
                                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Please wait...
                                </span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    const modal = document.getElementById('Modalbox');
    const modalTitle = modal.querySelector('.modal-title');
    const form = modal.querySelector('#inquiryForm');
    const questionInput = form.querySelector('#question_text');
    const isActiveCheckbox = form.querySelector('#is_active');
    const hiddenIdInput = form.querySelector('#inquiry-id');
    const submitBtn = form.querySelector('#SubmitBtn');
    const submitText = form.querySelector('#SubmitText');
    const submitSpinner = form.querySelector('#SubmitSpinner');
    modal.addEventListener('show.bs.modal', event => {
        const button = event.relatedTarget;
        const action = button.getAttribute('data-action');
        const type = button.getAttribute('data-type');
        const modalName = button.getAttribute('data-modal');
        modalTitle.textContent = `${type} ${modalName}`;
        form.action = action;
        if(type === 'Create') 
        {
            hiddenIdInput.value = '';
            questionInput.value = '';
            isActiveCheckbox.checked = true;
            submitText.textContent = 'Add Question';
        } 
        else if(type === 'Update') 
        {
            hiddenIdInput.value = button.getAttribute('data-id');
            questionInput.value = button.getAttribute('data-question');
            isActiveCheckbox.checked = button.getAttribute('data-is_active') == 1 ? true : false;
            submitText.textContent = 'Update Question';
        }
        submitBtn.disabled = false;
        submitText.classList.remove('d-none');
        submitSpinner.classList.add('d-none');
    });
    $('#SubmitBtn').closest('form').on('submit', function (e) 
    {
        e.preventDefault(); 
        submitBtn.disabled = true;
        submitText.classList.add('d-none');
        submitSpinner.classList.remove('d-none');
        setTimeout(() => {
            this.submit();
        }, 100);
    });
</script>
@endsection
