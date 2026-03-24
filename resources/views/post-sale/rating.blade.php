<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rate Our Service</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body 
        {
            background: #f8f9fa;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .rating-card 
        {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            border: none;
            max-width: 450px;
            width: 100%;
        }
        .rating-stars 
        {
            cursor: pointer;
        }
        .star 
        {
            color: #ddd;
            font-size: 2.5rem;
            transition: all 0.2s ease;
            margin: 0 3px;
        }
        .star:hover,
        .star.active 
        {
            color: #ffc107;
        }
        .card-header 
        {
            background: #CF5D3B;
            border: none;
            border-radius: 12px 12px 0 0 !important;
        }
        .customer-info 
        {
            background: #f8f9fa;
            border-left: 4px solid #CF5D3B;
        }
        .btn-submit 
        {
            background: #CF5D3B;
            border: none;
            padding: 12px;
            font-size: 1.1rem;
        }
        .btn-submit:hover 
        {
            background: #CF5D3B;
        }
        .btn-submit:disabled 
        {
            background: #6c757d;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-6">
                <div class="card rating-card">
                    <div class="card-header text-white text-center py-3">
                        <h4 class="mb-0"><i class="fas fa-star me-2"></i>Rate Your Experience</h4>
                    </div>
                    
                    <div class="card-body p-4">
                        @if($rated)
                            <div class="text-center py-4">
                                <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                                <h5 class="text-success mb-2">Thank You!</h5>
                                <p class="text-muted">Your rating has been recorded.</p>
                            </div>
                        @else
                            <div class="customer-info mb-4 p-3">
                                <h6 class="mb-2 text-dark"><i class="fas fa-user me-2"></i>Customer Details</h6>
                                <div class="row small">
                                    <div class="col-6">
                                        <strong>Name:</strong><br>
                                        {{ $ps->applicant_name }}
                                    </div>
                                    <div class="col-6">
                                        <strong>Project:</strong><br>
                                        {{ $ps->project_name }}
                                    </div>
                                </div>
                            </div>

                            <div class="text-center mb-4">
                                <i class="fas fa-star fa-2x text-warning mb-2"></i>
                                <h5 class="mb-1">How was your experience?</h5>
                                <p class="text-muted small">Select your rating below</p>
                            </div>

                            <form method="POST" id="ratingForm">
                                @csrf
                                <div class="mb-4 text-center">
                                    <div class="rating-stars d-flex justify-content-center mb-2">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star star mx-1" 
                                               data-rating="{{ $i }}" 
                                               onmouseover="highlightStars({{ $i }})" 
                                               onmouseout="resetStars()" 
                                               onclick="setRating({{ $i }})"></i>
                                        @endfor
                                    </div>
                                    <input type="hidden" name="rating" id="rating" required>
                                    <div class="rating-labels">
                                        <small class="text-muted" id="rating-text">Tap to rate</small>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label small fw-bold">Comments (Optional)</label>
                                    <textarea class="form-control form-control-sm" name="comment" rows="3" 
                                    placeholder="Share your feedback... (optional)"></textarea>
                                </div>

                                <button type="submit" class="btn btn-submit text-white w-100 py-2" id="submitBtn" disabled>
                                    <i class="fas fa-paper-plane me-2"></i>Submit Rating
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let selectedRating = 0;
        const ratingTexts = {
            1: 'Poor',
            2: 'Fair', 
            3: 'Good',
            4: 'Very Good',
            5: 'Excellent'
        };
        function highlightStars(rating) 
        {
            const stars = document.querySelectorAll('.star');
            stars.forEach((star, index) => {
                if (index < rating) 
                {
                    star.style.color = '#ffc107';
                } 
                else 
                {
                    star.style.color = '#ddd';
                }
            });
        }

        function resetStars() 
        {
            const stars = document.querySelectorAll('.star');
            stars.forEach((star, index) => {
                if (index < selectedRating) 
                {
                    star.style.color = '#ffc107';
                } 
                else 
                {
                    star.style.color = '#ddd';
                }
            });
        }

        function setRating(rating) 
        {
            selectedRating = rating;
            document.getElementById('rating').value = rating;
            document.getElementById('rating-text').textContent = ratingTexts[rating];
            document.getElementById('submitBtn').disabled = false;
            
            const stars = document.querySelectorAll('.star');
            stars.forEach((star, index) => {
                if (index < rating) 
                {
                    star.classList.add('active');
                } 
                else 
                {
                    star.classList.remove('active');
                }
            });
        }

        document.getElementById('ratingForm')?.addEventListener('submit', function(e) 
        {
            e.preventDefault();
            
            if (selectedRating === 0) 
            {
                toastr.error('Please select a rating by clicking on the stars');
                return;
            }
            this.submit();
        });
    </script>
</body>
</html>