@if(isset($advertisement))
    @if($advertisement->badge_text)
        <div class="advertisement-badge">{{ $advertisement->badge_text }}</div>
    @endif
    
    <h3 class="advertisement-title mb-3">{{ $advertisement->title }}</h3>
    
    @if(isset($advertisement->subtitle) && $advertisement->subtitle)
        <p class="advertisement-subtitle">{{ $advertisement->subtitle }}</p>
    @endif
    
    @if(isset($advertisement->description) && $advertisement->description)
        <div class="advertisement-description mb-4">
            <p>{{ $advertisement->description }}</p>
        </div>
    @endif
    
    @if(isset($advertisement->media) && $advertisement->media)
        <div class="text-center mb-4">
            @if(str_contains($advertisement->media, 'youtu') || str_contains($advertisement->media, 'vimeo'))
                <div class="ratio ratio-16x9">
                    <iframe src="{{ $advertisement->media }}" 
                            frameborder="0" 
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                            allowfullscreen>
                    </iframe>
                </div>
            @else
                <img src="{{ $advertisement->media }}" class="img-fluid rounded" alt="Advertisement" style="max-height: 300px; object-fit: cover;">
            @endif
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
    
    @if(isset($advertisement->pricing) && $advertisement->pricing)
        <div class="pricing-card p-4 mb-4 text-center">
            <h4 class="text-white mb-2">Special Offer</h4>
            <span class="current-price">{{ $advertisement->pricing }}</span>
        </div>
    @endif
    
    @if(isset($advertisement->button) && $advertisement->button)
        <div class="text-center mb-3">
            <button class="btn btn-primary btn-lg px-5 py-3">
                <strong>{{ $advertisement->button }}</strong>
            </button>
        </div>
    @endif
    
    @if(isset($advertisement->footer_note) && $advertisement->footer_note)
        <div class="text-center mt-3">
            <small class="text-muted">{{ $advertisement->footer_note }}</small>
        </div>
    @endif
    
    <div class="text-end mt-2">
        <small class="badge bg-{{ $advertisement->source === 'local' ? 'info' : 'warning' }}">
            {{ $advertisement->source === 'local' ? 'Local' : 'API' }}
        </small>
    </div>
@endif