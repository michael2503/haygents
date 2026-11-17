{{-- <div class="col-12 col-md-6 col-lg-3 mb-4 mb-lg-0">
    <article class="hg-profile-card">
        <div class="d-flex align-items-start justify-content-between mb-3">
            <div class="hg-avatar-box hg-avatar-dark">{{ $company->initials }}</div>
            <span class="hg-verified-pill"><i class="fas fa-check-circle"></i> {{ ucfirst($company->verification_status) }}</span>
        </div>
        <h3 class="h6 font-weight-bold mb-1">{{ $company->name }}</h3>
        <div class="small text-muted mb-1">{{ $company->specialty }}</div>
        <div class="small text-muted mb-3"><i class="fas fa-map-marker-alt mr-1"></i> {{ $company->city }}</div>
        <div class="hg-cred-box mt-auto"><i class="fas fa-check-circle text-success mr-1"></i> {{ $company->credentials }}</div>
        <div class="d-flex align-items-center justify-content-between mb-3 small">
            <span class="text-muted">Available properties</span>
            <strong>{{ $company->properties_count ?? 0 }} listings</strong>
        </div>
        <a href="{{ route('companies.index') }}#company-{{ $company->slug }}" class="btn hg-btn-outline btn-block">View Company</a>
    </article>
</div> --}}


<div
    class="hg-testimonial-slider"
    id="companySlider"
    role="region"
    aria-roledescription="carousel"
    aria-label="Customer testimonials"
>
    <div class="d-flex justify-content-end mb-3" style="gap: 0.5rem;">
        <button type="button" class="hg-slider-arrow" id="companyPrevBtn" aria-label="Previous testimonial">
            <i class="fas fa-chevron-left"></i>
        </button>
        <button type="button" class="hg-slider-arrow" id="companyNextBtn" aria-label="Next testimonial">
            <i class="fas fa-chevron-right"></i>
        </button>
    </div>

    <div class="hg-testimonial-viewport">
        <div class="hg-testimonial-track" id="companyTrack">
            @foreach($companies as $comp)
                <div class="hg-testimonial-slide">
                    <img src="{{ asset($comp->logo_path) }}" alt="">
                </div>
            @endforeach
        </div>
    </div>

    <div class="hg-slider-dots" id="companyDots" role="tablist" aria-label="Testimonial slides"></div>
</div>
