<div class="col-12 col-md-6 col-lg-3 mb-4 mb-lg-0">
    <article class="hg-profile-card">
        <div class="d-flex align-items-start justify-content-between mb-3">
            <div class="hg-avatar-box">{{ $agent->initials }}</div>
            <span class="hg-verified-pill"><i class="fas fa-check-circle"></i> {{ ucfirst($agent->verification_status) }}</span>
        </div>
        <h3 class="h6 firaMedium mb-1">{{ $agent->name }}</h3>
        <div class="small text-muted mb-1">{{ $agent->role_title }}</div>
        <div class="small text-muted mb-2" style="font-size: 12px"><i class="fas fa-map-marker-alt mr-1"></i> {{ $agent->city }}, {{ $agent->state }}</div>
        {{-- <p class="small text-secondary mb-3" style="flex: 1;">{{ $agent->description }}</p> --}}
        <div class="hg-cred-box"><i class="fas fa-shield-alt text-primary mr-1"></i> {{ $agent->credentials }}</div>
        {{-- <div class="d-flex align-items-center justify-content-between mb-3 small">
            <span class="text-muted">Listed properties</span>
            <strong>{{ $agent->properties_count ?? 0 }} active</strong>
        </div> --}}
        <a href="{{ route('agents.index') }}#agent-{{ $agent->slug }}" class="btn hg-btn-outline btn-block">Contact Agent</a>
    </article>
</div>
