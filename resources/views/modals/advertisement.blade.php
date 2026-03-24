@if($advertisement)
    <div class="modal fade" id="advertisementModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content advertisement-modal">
                <div class="modal-header position-relative border-0 pb-0">
                    <button type="button" class="btn-close position-absolute" data-bs-dismiss="modal" aria-label="Close" style="top:15px;right:15px;z-index:1;"></button>
                </div>
                <div class="modal-body p-4 pt-0">
                    @if($advertisement->badge_text)
                        <div class="advertisement-badge">{{ $advertisement->badge_text }}</div>
                    @endif
                    
                    <h3 class="advertisement-title mb-3">{{ $advertisement->title }}</h3>
                    
                    @if($advertisement->subtitle)
                        <p class="advertisement-subtitle">{{ $advertisement->subtitle }}</p>
                    @endif
                    @if($advertisement->description)
                        <div class="advertisement-description mb-4">
                            <p>{{ $advertisement->description }}</p>
                        </div>
                    @endif
                    @if($advertisement->media)
                        <div class="text-center mb-4">
                            <img src="{{ $advertisement->media }}" class="img-fluid rounded" alt="Advertisement" style="max-height: 300px; object-fit: cover;">
                        </div>
                    @endif
                    @if(!empty($advertisement->features) && is_array($advertisement->features))
                        <div class="advertisement-features mb-4">
                            <h5 class="mb-3">Key Features:</h5>
                            @foreach($advertisement->features as $feature)
                                <div class="feature-item">
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    <span>{{ $feature }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                    @if($advertisement->pricing)
                        <div class="pricing-card p-4 mb-4 text-center">
                            <h4 class="text-white mb-2">Special Offer</h4>
                            <span class="current-price">{{ $advertisement->pricing }}</span>
                        </div>
                    @endif
                    @if($advertisement->button)
                        <div class="text-center mb-3">
                            <button class="btn btn-primary btn-lg px-5 py-3">
                                <strong>{{ $advertisement->button }}</strong>
                            </button>
                        </div>
                    @endif
                    @if($advertisement->footer_note)
                        <div class="text-center mt-3">
                            <small class="text-muted">{{ $advertisement->footer_note }}</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <form id="deactivateForm" action="{{ route('advertisement.deactivate') }}" method="POST" style="display: none;">
    @csrf
        <input type="hidden" name="advertisement_id" value="{{ $advertisement->id }}">
    </form>
    <script>
        document.addEventListener('DOMContentLoaded', function() 
        {
            const modalElement = document.getElementById('advertisementModal');
            const advertisementModal = new bootstrap.Modal(modalElement);
            const advertisementId = {{ $advertisement->id }};
            setTimeout(() => {
                advertisementModal.show();
            }, 1500);
            
            function deactivateAdvertisement(adId) 
            {
                fetch('{{ route("advertisement.deactivate") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        advertisement_id: adId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) 
                    {
                        advertisementModal.hide();
                        const backdrops = document.querySelectorAll('.modal-backdrop');
                        backdrops.forEach(backdrop => {
                            backdrop.remove();
                        });
                        setTimeout(() => {
                            modalElement.remove();
                        }, 300);
                        document.body.classList.remove('modal-open');
                        document.body.style.overflow = '';
                        document.body.style.paddingRight = '';
                        
                    } 
                    else 
                    {
                        console.error('Failed to deactivate advertisement');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            }
            modalElement.addEventListener('hidden.bs.modal', function () 
            {
                deactivateAdvertisement(advertisementId);
            });
        });
    </script>
@endif