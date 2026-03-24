
<div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg rounded-4">
      <div class="modal-header border-0 justify-content-center position-relative pt-4">
        <h5 class="modal-title fw-semibold text-center" id="staticBackdropLabel">Forgot Your Password?</h5>
        <button type="button" class="btn-close position-absolute end-0 top-0 m-3" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body px-4 pt-0 pb-4">
        <p class="text-muted text-center mb-4">Enter your email address below and we'll send you a link to reset your password.</p>
        <form id="resetPasswordForm" method="POST" action="{{ route('password.email') }}" novalidate>
          @csrf
          <div class="mb-3">
            <label for="resetEmail" class="form-label fw-medium">Email address</label>
            <input 
              type="email" 
              class="form-control form-control-lg" 
              id="resetEmail" 
              name="email" 
              placeholder="you@example.com" 
              required 
              autofocus
            >
          </div>
          <div class="d-grid mt-4">
            <button type="submit" id="resetBtn" class="btn btn-primary btn-lg">
              <span id="btnText">Send Reset Link</span>
              <span id="btnLoader" class="spinner-border spinner-border-sm ms-2 d-none" role="status" aria-hidden="true"></span>
            </button>
          </div>
        </form>
      </div>
      <div class="modal-footer border-0 justify-content-center pb-4">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
      </div>
    </div>
  </div>
</div>
