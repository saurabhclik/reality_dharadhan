@extends('mobile.layouts.app')
@section('content')

<style>
    .profile-page 
    {
        margin-top: 140px;
        padding: 15px;
    }

    .profile-card 
    {
        background: #fff;
        border-radius: 12px;
        padding: 15px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        margin-bottom: 15px;
        border-left: 5px solid #CF5D3B; 
    }

    .profile-header 
    {
        text-align: center;
        margin-bottom: 15px;
    }

    .profile-header h4 
    {
        font-weight: 600;
        color: #333;
    }

    .profile-item 
    {
        display: flex;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px solid #f1f1f1;
    }

    .profile-item:last-child 
    {
        border-bottom: none;
    }

    .item-icon 
    {
        width: 45px;
        height: 45px;
        background: #f0f4ff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        color: #CF5D3B;
        margin-right: 12px;
    }

    .item-text 
    {
        display: flex;
        flex-direction: column;
    }

    .item-label 
    {
        font-size: 13px;
        color: #777;
    }

    .item-value 
    {
        font-size: 15px;
        font-weight: 600;
        color: #333;
    }
</style>

<button class="back-button text-decoration-none" onclick="window.history.back()">
    <i class="fas fa-arrow-left"></i>
</button>

<div class="profile-page">
    <div class="profile-card">
        <div class="profile-header">
            <h4>{{ $profile->role }}</h4>
        </div>
        <div class="profile-item">
            <div class="item-icon"><i class="fas fa-user"></i></div>
            <div class="item-text">
                <span class="item-label">Full Name</span>
                <span class="item-value">{{ $profile->name }}</span>
            </div>
        </div>
        <div class="profile-item">
            <div class="item-icon"><i class="fas fa-phone"></i></div>
            <div class="item-text">
                <span class="item-label">Phone</span>
                <span class="item-value">{{ $profile->mobile }}</span>
            </div>
        </div>
        <div class="profile-item">
            <div class="item-icon"><i class="fas fa-envelope"></i></div>
            <div class="item-text">
                <span class="item-label">Email</span>
                <span class="item-value">{{ $profile->email }}</span>
            </div>
        </div>
    </div>
</div>

<div class="fab" onclick="openPasswordSheet()">
    <i class="fas fa-key"></i>
</div>
<div class="overlay" id="overlay"></div>

<div class="bottom-sheet-form" id="passwordSheet">
    <div class="sheet-header">
        <div class="handle"></div>
        <h5 class="modal-title m-0">Change Password</h5>
        <button type="button" class="btn-close" onclick="closePasswordSheet()">&times;</button>
    </div>
    <form method="POST" action="{{ route('mobile.profile.update') }}" class="p-3">
    @csrf
        <input type="hidden" name="action" value="update_password">
        <div class="form-group mb-3">
            <label class="form-label">New Password</label>
            <input type="password" class="form-control" name="new_password" required>
        </div>
        <div class="form-group mb-3">
            <label class="form-label">Confirm Password</label>
            <input type="password" class="form-control" name="confirm_password" required>
        </div>
        <div class="modal-actions">
            <button type="submit" class="btn btn-primary" id="SubmitBtn">
                <span id="SubmitText">Update</span>
                <span id="SubmitSpinner" class="d-none">
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Please wait...
                </span>
            </button>
        </div>
    </form>
</div>

<script>
    function openPasswordSheet() 
    {
        document.getElementById('passwordSheet').classList.add('show');
        document.getElementById('overlay').classList.add('show');
        document.body.style.overflow = 'hidden';
    }
    function closePasswordSheet() 
    {
        document.getElementById('passwordSheet').classList.remove('show');
        document.getElementById('overlay').classList.remove('show');
        document.body.style.overflow = 'auto';
    }
    document.getElementById('overlay').addEventListener('click', closePasswordSheet);
    $('#SubmitBtn').closest('form').on('submit', function () 
    {
        $('#SubmitBtn').prop('disabled', true);
        $('#SubmitText').addClass('d-none');
        $('#SubmitSpinner').removeClass('d-none');
    });
</script>
@endsection
