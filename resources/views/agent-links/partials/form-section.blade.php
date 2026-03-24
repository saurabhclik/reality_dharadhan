<div class="form-section">
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Full Name *</label>
            <input class="form-control form-control-lg @error('name') is-invalid @enderror" 
                    name="name" required placeholder="Enter your full name">
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-6">
            <label class="form-label">Email Address *</label>
            <input type="email" class="form-control form-control-lg @error('email') is-invalid @enderror" 
                    name="email" required placeholder="your@email.com">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-6">
            <label class="form-label">Phone Number *</label>
            <input class="form-control form-control-lg @error('phone') is-invalid @enderror" 
                    name="phone" required placeholder="91 *********">
            @error('phone')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-12">
            <label class="form-label">Additional Message</label>
            <textarea class="form-control" name="message" rows="3" placeholder="Tell us about your requirements..."></textarea>
        </div>
    </div>
</div>