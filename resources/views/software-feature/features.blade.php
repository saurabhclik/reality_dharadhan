    @extends('layouts.app')
    @section('title', 'Dashboard | Pro-leadexpertz')
    @section('content')
    <div class="page-content">
        <div class="container-fluid">
            <section class="hero">
                <div class="container text-center">
                    <h2>Unlock Premium Features</h2>
                    <p>Take your business to the next level with our exclusive premium features designed to maximize productivity and growth.</p>
                </div>
            </section>
            
            <section class="container">
                <div class="feature-tabs">
                    <div class="tab-content" id="featureTabsContent">
                        <div class="tab-pane fade show active" id="all" role="tabpanel">
                            <div class="row">
                                @foreach($features as $feature)
                                    @if($feature->integration_status === 0)
                                        <div class="col-md-6 col-lg-4 mb-4">
                                            <div class="card feature-card">
                                                <div class="card-body position-relative">
                                                    @if($feature->status === 'active')
                                                        <div class="feature-badge">Active</div>
                                                    @elseif(in_array($feature->feature_name, $activeFeatures))
                                                        <div class="feature-badge">Request Sent</div>
                                                    @endif
                                                    <div class="feature-icon">
                                                        @php
                                                            $icons = [
                                                                'expense_management' => 'fas fa-receipt',
                                                                'employee_tracking' => 'fas fa-user-check',
                                                                'task_management' => 'fas fa-tasks',
                                                                'inventory_management' => 'fas fa-boxes',
                                                                'integration' => 'fas fa-puzzle-piece',
                                                                'shared_lead_form' => 'fas fa-share-alt',
                                                                'mis_management' => 'fas fa-chart-line',
                                                                'project_detail_page' => 'fas fa-building'
                                                            ];
                                                            $icon = $icons[$feature->feature_name] ?? 'fas fa-star';
                                                        @endphp
                                                        <i class="{{ $icon }}"></i>
                                                    </div>                                  
                                                    @if($feature->video_url)
                                                    <div class="video-container mt-2">
                                                        <iframe src="{{ preg_replace('/watch\\?v=/', 'embed/', $feature->video_url) }}" 
                                                                title="{{ $feature->feature_name }} Demo" allowfullscreen></iframe>
                                                    </div>
                                                    @endif
                                                    <h5 class="card-title">{{ ucwords(str_replace('_', ' ', $feature->feature_name)) }}</h5>

                                                    @php
                                                        $meta = json_decode($feature->meta, true) ?? [];
                                                        $description = $meta['description'] ?? 'No description found';
                                                        $keyBenefits = $meta['key_benefits'] ?? 'No Key Benefits provided.';
                                                        $analytics = $meta['analytics'] ?? 'No Analytics info provided.';
                                                    @endphp

                                                    <span class="text-muted">{{ $description }}</span>

                                                    <div class="feature-detail">
                                                        <h6><i class="fas fa-check-circle me-2 text-success"></i> Key Benefits</h6>
                                                        <ul>
                                                            @foreach(explode('<br />', $keyBenefits) as $benefit)
                                                                @if(trim($benefit) !== '')
                                                                    <li>{!! $benefit !!}</li>
                                                                @endif
                                                            @endforeach
                                                        </ul>

                                                        <h6><i class="fas fa-chart-line me-2 text-success"></i> Analytics</h6>
                                                        <ul>
                                                            @foreach(explode('<br />', $analytics) as $item)
                                                                @if(trim($item) !== '')
                                                                    <li>{!! $item !!}</li>
                                                                @endif
                                                            @endforeach
                                                        </ul>
                                                    </div>

                                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                                        <span class="price-tag">₹{{ number_format($feature->price, 2) }} /year</span>
                                                        <img src="{{ asset('images/free-trial_5578823.png') }}" 
                                                            alt="Free Trial" 
                                                            width="80" height="60"
                                                            class="start-trial-btn-feature" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#startTrialModal"
                                                            data-feature-id="{{ $feature->id }}"
                                                            data-feature-name="{{ ucwords(str_replace('_', ' ', $feature->feature_name)) }}" style="cursor:pointer;">
                                                    </div>
                                                    <div class="mt-3">
                                                        @if($feature->status === 'active')
                                                            <button class="btn btn-success w-100" disabled>
                                                                <i class="fas fa-check me-1"></i> Activated
                                                            </button>
                                                        @elseif(in_array($feature->feature_name, $activeFeatures))
                                                            <button class="btn btn-warning w-100" disabled>
                                                                <i class="fas fa-paper-plane me-1"></i> Request Sent
                                                            </button>
                                                        @else
                                                        @if($userType == 'super_admin')
                                                            <button type="button" class="btn feature-request-btn w-100" 
                                                                    id="submitBtn-{{ $feature->id }}" 
                                                                    data-feature="{{ $feature->feature_name }}" 
                                                                    data-id="{{ $feature->id }}">
                                                                <span class="submit-text">Request Access</span>
                                                                <span class="submit-spinner d-none">
                                                                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Please wait...
                                                                </span>
                                                            </button>
                                                        @endif
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif              
                                @endforeach
                                
                                <div class="col-md-6 col-lg-4 mb-4">
                                    <div class="card feature-card advanced-integration-card">
                                        <div class="card-body position-relative text-center d-flex flex-column justify-content-center align-items-center">
                                            <div class="feature-icon mb-3">
                                                <i class="fas fa-puzzle-piece fa-3x text-primary"></i>
                                            </div>
                                            <h5 class="card-title">Advanced Integrations</h5>
                                            <p class="text-muted mb-4">Connect with your favorite tools and platforms to streamline your workflow</p>
                                            <button type="button" class="btn btn-primary mt-auto" data-bs-toggle="modal" data-bs-target="#advancedIntegrationModal">
                                                <i class="fas fa-external-link-alt me-2"></i> Explore Integrations
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            
            <div class="modal fade" id="advancedIntegrationModal" tabindex="-1" aria-labelledby="advancedIntegrationModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-fullscreen modal-dialog-centered modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-light">
                            <h5 class="modal-title" id="advancedIntegrationModalLabel">
                                <i class="fas fa-puzzle-piece me-2"></i> Advanced Integrations
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                @foreach($features as $feature)
                                    @if($feature->integration_status === 1)
                                        <div class="col-md-6 col-lg-4 mb-4">
                                            <div class="card feature-card">
                                                <div class="card-body position-relative">
                                                    @if($feature->status === 'active')
                                                        <div class="feature-badge">Active</div>
                                                    @elseif(in_array($feature->feature_name, $activeFeatures))
                                                        <div class="feature-badge">Request Sent</div>
                                                    @endif
                                                    <div class="feature-icon">
                                                        @php
                                                            $icons = [                                             
                                                                'MagicBricks' => 'fas fa-building',
                                                                '99acres' => 'fas fa-home',
                                                                'Google Sheets' => 'fab fa-google-drive',
                                                                'Google Form' => 'fab fa-google',
                                                                'Zapier' => 'fas fa-bolt',
                                                                'HubSpot' => 'fas fa-hubspot',
                                                                'Salesforce' => 'fas fa-cloud'
                                                            ];
                                                            $icon = $icons[$feature->feature_name] ?? 'fas fa-star';
                                                        @endphp
                                                        <i class="{{ $icon }}"></i>
                                                    </div>
                                                    @if($feature->video_url)
                                                    <div class="video-container mt-2">
                                                        <iframe src="{{ preg_replace('/watch\\?v=/', 'embed/', $feature->video_url) }}" 
                                                                title="{{ $feature->feature_name }} Demo" allowfullscreen></iframe>
                                                    </div>
                                                    @endif
                                                    <h5 class="card-title">{{ ucwords(str_replace('_', ' ', $feature->feature_name)) }}</h5>

                                                   @php
                                                        $meta = json_decode($feature->meta, true) ?? [];
                                                        $description = $meta['description'] ?? 'No description found';
                                                        $keyBenefits = $meta['key_benefits'] ?? 'No Key Benefits provided.';
                                                        $analytics = $meta['analytics'] ?? 'No Analytics info provided.';
                                                    @endphp

                                                    <span class="text-muted">{{ $description }}</span>

                                                    <div class="feature-detail">
                                                        <h6><i class="fas fa-check-circle me-2 text-success"></i> Key Benefits</h6>
                                                        <ul>
                                                            @foreach(explode('<br />', $keyBenefits) as $benefit)
                                                                @if(trim($benefit) !== '')
                                                                    <li>{!! $benefit !!}</li>
                                                                @endif
                                                            @endforeach
                                                        </ul>

                                                        <h6><i class="fas fa-chart-line me-2 text-success"></i> Analytics</h6>
                                                        <ul>
                                                            @foreach(explode('<br />', $analytics) as $item)
                                                                @if(trim($item) !== '')
                                                                    <li>{!! $item !!}</li>
                                                                @endif
                                                            @endforeach
                                                        </ul>
                                                    </div>  
                                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                                        <span class="price-tag">₹{{ number_format($feature->price, 2) }} /year</span>
                                                        <span class="status-badge {{ $feature->status === 'active' ? 'status-active' : 'status-inactive' }}">
                                                            {{ ucfirst($feature->status) }}
                                                        </span>
                                                    </div>

                                                    <div class="mt-3">
                                                        @if($feature->status === 'active')
                                                            <button class="btn btn-success w-100" disabled>
                                                                <i class="fas fa-check me-1"></i> Activated
                                                            </button>
                                                        @elseif(in_array($feature->feature_name, $activeFeatures))
                                                            <button class="btn btn-warning w-100" disabled>
                                                                <i class="fas fa-paper-plane me-1"></i> Request Sent
                                                            </button>
                                                        @else
                                                        @if($userType == 'super_admin')
                                                            <button type="button" class="btn feature-request-btn w-100" 
                                                                    id="modal-submitBtn-{{ $feature->id }}" 
                                                                    data-feature="{{ $feature->feature_name }}" 
                                                                    data-id="{{ $feature->id }}">
                                                                <span class="submit-text">Request Access</span>
                                                                <span class="submit-spinner d-none">
                                                                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Please wait...
                                                                </span>
                                                            </button>
                                                        @endif
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>

            <section class="container">
                <div class="section-title">
                    <h2>Frequently Asked Questions</h2>
                    <p>Find answers to common questions about our premium features and pricing</p>
                </div>

                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        @if(!empty($faqs))
                            @foreach($faqs as $faq)
                            <div class="faq-item">
                                <div class="faq-question">
                                    {{ $faq->question }}
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                                <div class="faq-answer">
                                    {{ $faq->answer }}
                                </div>
                            </div>
                            @endforeach
                        @else
                            <div class="alert alert-info text-center">
                                No FAQs available at the moment. Please check back later or contact support.
                            </div>
                        @endif
                    </div>
                </div>
            </section>

            <section class="container text-center my-5 py-5">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <h2 class="mb-4">Ready to Transform Your Business?</h2>
                        <p class="mb-4">Join thousands of businesses that have already upgraded to our premium features and are seeing remarkable results.</p>
                        @php
                            $isAdmin = $userType === 'super_admin';
                        @endphp
                        <button type="button" class="btn btn-primary" {{ $isAdmin ? '' : 'disabled' }}data-bs-toggle="modal" data-bs-target="#startTrialModal">
                            Start Free Trial
                        </button>
                        <div class="modal fade" id="startTrialModal" tabindex="-1" aria-labelledby="startTrialModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header bg-primary text-light">
                                        <h5 class="modal-title" id="startTrialModalLabel">Start Your Free Trial</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form method="POST" action="{{ route('start-free-trial') }}" id="freeTrialForm">
                                            @csrf
                                            <div class="mb-3">
                                                <label for="email" class="form-label">Email Address</label>
                                                <input type="email" name="email" id="email" class="form-control" placeholder="Enter your email" required value="{{$active_user_email}}">
                                            </div>
                                            <div class="mb-3">
                                                <label for="software" class="form-label">Software Feature</label>
                                                <select name="feature_id" class="select2 form-control" id="feature-software">
                                                    <option value="">Choose feature..</option>
                                                    @foreach($features as $feature)
                                                        @if($feature->integration_status === 0)
                                                            <option value="{{$feature->id}}">{{$feature->feature_name}}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
<input type="hidden" name="feature_id" id="feature-software-hidden">
                                            </div>
                                            <button type="submit" class="btn btn-primary w-100" id="submitTrialBtn">
                                                <span class="submit-trial-text">Start Free Trial</span>
                                                <span class="submit-trial-spinner d-none">
                                                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Please wait...
                                                </span>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        document.querySelectorAll('.faq-question').forEach(question => {
            question.addEventListener('click', () => {
                const faqItem = question.parentElement;
                faqItem.classList.toggle('active');
            });
        });

        function handleFeatureRequest(button) 
        {
            const feature = button.getAttribute('data-feature');
            const featureId = button.getAttribute('data-id');
            const featureName = feature.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
            const submitText = button.querySelector('.submit-text');
            const submitSpinner = button.querySelector('.submit-spinner');

            submitText.classList.add('d-none');
            submitSpinner.classList.remove('d-none');
            button.disabled = true;

            fetch('{{ route("premium-features.request") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    feature: feature,
                    feature_id: featureId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) 
                {
                    button.innerHTML = '<i class="fas fa-check me-1"></i> Request Sent';
                    button.classList.remove('feature-request-btn');
                    button.classList.add('btn-success');
                    button.disabled = true;
                    toastr.success(`Feature access requested successfully. Please contact Clikzop to complete your request`);
                } 
                else 
                {
                    submitText.classList.remove('d-none');
                    submitSpinner.classList.add('d-none');
                    button.disabled = false;
                    toastr.error('Error sending request. Please try again.', 'error');
                }
            })
            .catch(error => {
                submitText.classList.remove('d-none');
                submitSpinner.classList.add('d-none');
                button.disabled = false;
                toastr.error('Error sending request. Please try again.', 'error');
            });
        }

        document.querySelectorAll('.feature-request-btn').forEach(button => {
            button.addEventListener('click', function() 
            {
                handleFeatureRequest(this);
            });
        });

        document.addEventListener('DOMContentLoaded', function() 
        {
            const urlParams = new URLSearchParams(window.location.search);
            const integrate = urlParams.get('integrate');

            const modalElement = document.getElementById('advancedIntegrationModal');
            if (modalElement) 
            {
                const modal = new bootstrap.Modal(modalElement, {
                    backdrop: 'static', 
                    keyboard: true    
                });

                if (integrate === '1') 
                {
                    modal.show();
                }
                modalElement.addEventListener('hidden.bs.modal', function () 
                {
                    const currentUrl = new URL(window.location.href);
                    currentUrl.searchParams.delete('integrate');
                    window.history.replaceState({}, document.title, currentUrl.toString());
                });
            }
        });

        const freeTrialForm = document.getElementById('freeTrialForm');
        if (freeTrialForm) 
        {
            freeTrialForm.addEventListener('submit', function(e) 
            {
                const submitBtn = document.getElementById('submitTrialBtn');
                const submitText = submitBtn.querySelector('.submit-trial-text');
                const submitSpinner = submitBtn.querySelector('.submit-trial-spinner');
                submitText.classList.add('d-none');
                submitSpinner.classList.remove('d-none');
                submitBtn.disabled = true;
            });
        }
        document.querySelectorAll('.start-trial-btn-feature').forEach(button => {
            button.addEventListener('click', function() 
            {
                const featureId = this.getAttribute('data-feature-id');
                const featureName = this.getAttribute('data-feature-name');

                const select = document.getElementById('feature-software');
                select.innerHTML = `<option value="${featureId}" selected>${featureName}</option>`;

                const hiddenInput = document.getElementById('feature-software-hidden');
                hiddenInput.value = featureId;
            });
        });

        const trialModal = document.getElementById('startTrialModal');
        if (trialModal) 
        {
            trialModal.addEventListener('hidden.bs.modal', function() 
            {
                const select = document.getElementById('feature-software');
                if (select) 
                {
                    select.removeAttribute('disabled');
                    select.innerHTML = `<option value="">Choose feature..</option>
                        @foreach($features as $feature)
                            @if($feature->integration_status === 0)
                                <option value="{{$feature->id}}">{{$feature->feature_name}}</option>
                            @endif
                        @endforeach`;
                }
            });
        }
    </script>
    @endsection