@extends('agent-links.layouts.app')
@section('title', 'Public Link | Dharadhan')
@section('content')
<div class="step-page active" id="step1">
    <h2>What are you looking for?</h2>
    <p class="subtext">Choose the service you need assistance with</p>
    
    <div class="grid" id="serviceGrid">
        <div class="service-card active" data-service="realestate">
            <div class="overlay"></div>
            <img src="{{asset('images/realestate.png')}}" alt="Real Estate">
            <span><i class="fa fa-building"></i> Real Estate</span>
        </div>
        <div class="service-card" data-service="finance">
            <div class="overlay"></div>
            <img src="{{asset('images/finance.png')}}" alt="Finance">
            <span><i class="fa fa-landmark"></i> Finance / Loan</span>
        </div>
        <div class="service-card" data-service="insurance">
            <div class="overlay"></div>
            <img src="{{asset('images/insurance.png')}}" alt="Insurance">
            <span><i class="fa fa-shield"></i> Insurance</span>
        </div>
        <div class="service-card" data-service="mutual_fund">
            <div class="overlay"></div>
            <img src="{{asset('images/mutual-fund.png')}}" alt="Mutual Funds">
            <span><i class="fa fa-arrow-trend-up"></i> Mutual Funds</span>
        </div>
        <div class="service-card" data-service="solar">
            <div class="overlay"></div>
            <img src="{{asset('images/solar.png')}}" alt="Solar Energy">
            <span><i class="fa fa-sun"></i> Solar Energy</span>
        </div>
    </div>

    <button class="btn-nav" onclick="goToStep2()">Continue →</button>
</div>

<div class="step-page" id="step2">
    <h3>Tell us your requirement</h3>
    <p class="subtitle">Help us understand your needs better</p>

    <form id="requirementsForm">
        @csrf
        <input type="hidden" name="service_type" id="service_type" value="realestate">
        
        <div id="realestate-fields" class="service-fields active">
            @include('agent-links.partials.realestate-form')
        </div>
        <div id="finance-fields" class="service-fields">
            @include('agent-links.partials.finance-form')
        </div>
        <div id="insurance-fields" class="service-fields">
            @include('agent-links.partials.insurance-form')
        </div>
        <div id="mutual_fund-fields" class="service-fields">
            @include('agent-links.partials.mutual-fund-form')
        </div>
        <div id="solar-fields" class="service-fields">
            @include('agent-links.partials.solar-form')
        </div>
    </form>
    
    <div class="d-flex justify-content-between mt-4">
        <button type="button" class="btn btn-sm" onclick="goToStep(1)">← Back</button>
        <button type="button" class="btn-nav" onclick="goToStep3()">Next →</button>
    </div>
</div>

<div class="step-page" id="step3">
    <h3 class="title">Your Contact Details</h3>
    <p class="subtitle">We'll get back to you shortly</p>

    <div class="form-section">
        <h5><i class="fa-solid fa-address-card"></i> Personal Information</h5>
        <div class="row g-4">
            <div class="col-md-6">
                <label class="form-label">Full Name *</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fa-solid fa-user"></i></span>
                    <input class="form-control form-control-lg" 
                           id="contactName" 
                           name="contact_name"
                           required 
                           placeholder="Enter your full name">
                </div>
            </div>

            <div class="col-md-6">
                <label class="form-label">Email Address *</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fa-solid fa-envelope"></i></span>
                    <input type="email" 
                           class="form-control form-control-lg" 
                           id="contactEmail"
                           name="contact_email" 
                           required 
                           placeholder="your@email.com">
                </div>
            </div>

            <div class="col-md-6">
                <label class="form-label">Phone Number *</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fa-solid fa-phone"></i></span>
                    <input class="form-control form-control-lg" 
                        id="contactPhone"
                        name="contact_phone" 
                        required 
                        placeholder="91 *********"
                        maxlength="10"
                        pattern="[0-9]{10}">
                </div>
                <small class="text-muted">Enter 10-digit mobile number</small>
            </div>

            <div class="col-12">
                <label class="form-label">Additional Message <small class="text-muted">(Optional)</small></label>
                <textarea class="form-control" 
                    id="contactNotes" 
                    name="contact_notes" 
                    rows="4" 
                    placeholder="Tell us about your requirements in detail..."></textarea>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between mt-4">
        <button type="button" class="btn btn-sm" onclick="goToStep(2)">
            <i class="fa-solid fa-arrow-left me-2"></i>Back
        </button>
        <button type="button" class="btn btn-nav" onclick="submitLead()">
            Submit <i class="fa-solid fa-paper-plane ms-2"></i>
        </button>
    </div>
</div>

<div class="step-page" id="step4" style="height:100vh;">
    <div class="thank">
        <i class="fa-solid fa-circle-check"></i>
        <h2>Thank You!</h2>
        <p>Your request has been submitted successfully.</p>
        <p>Our agent <strong>{{ $agentLink->agent_name }}</strong> will contact you shortly.</p>
    </div>

<script>
    function prevStep() 
    {
        goToStep(2);
    }
</script>
@endsection