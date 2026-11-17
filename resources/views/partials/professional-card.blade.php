<div class="col-12 col-md-6 col-lg-3 mb-4 mb-lg-0">
    <article class="hg-profile-card">
        <div class="d-flex align-items-start justify-content-between mb-3">
            <div class="hg-avatar-box">{{ $professional->initials }}</div>
            <span class="hg-verified-pill"><i class="fas fa-check-circle"></i> {{ ucfirst($professional->verification_status) }}</span>
        </div>
        <h3 class="h6 font-weight-bold mb-1">{{ $professional->name }}</h3>
        <div class="small font-weight-semibold text-primary mb-1">{{ $professional->category }} · {{ $professional->years_exp }} yrs</div>
        <div class="small text-muted mb-2"><i class="fas fa-map-marker-alt mr-1"></i> {{ $professional->city }}, {{ $professional->state }}</div>
        {{-- <p class="small text-secondary mb-3" style="flex: 1;">{{ $professional->description }}</p> --}}
        <div class="hg-cred-box"><i class="fas fa-certificate text-primary mr-1"></i> {{ $professional->credentials }}</div>
        <a href="{{ route('professionals.index') }}#prof-{{ $professional->slug }}" class="btn hg-btn-outline btn-block">Request Service</a>
    </article>
</div>
