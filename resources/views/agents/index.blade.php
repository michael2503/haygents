@extends('layouts.app')

@section('title', 'Verified Agents in Nigeria — HAYGENTS')

@section('content')
@php
    $activeType = $filters['agentType'] ?? 'all';
    $activeState = $filters['state'] ?? 'all';
    $activeCity = $filters['city'] ?? 'all';
    $activeArea = $filters['area'] ?? '';
    $activeYears = $filters['years'] ?? 'any';
    $rawSpecs = $filters['specialisation'] ?? [];
    $activeSpecs = is_array($rawSpecs)
        ? array_map('strtolower', $rawSpecs)
        : array_filter(array_map(fn($s) => strtolower(trim($s)), explode(',', (string) $rawSpecs)));

    $quickSpecialisations = [
        ['slug' => 'sales', 'label' => 'Sales'],
        ['slug' => 'rentals', 'label' => 'Rentals'],
        ['slug' => 'shortlets', 'label' => 'Shortlets'],
        ['slug' => 'land', 'label' => 'Land'],
        ['slug' => 'commercial', 'label' => 'Commercial'],
        ['slug' => 'off-plan', 'label' => 'Off-plan'],
    ];

    $quickStates = [
        ['slug' => 'Lagos', 'label' => 'Lagos'],
        ['slug' => 'FCT', 'label' => 'Abuja (FCT)'],
        ['slug' => 'Rivers', 'label' => 'Rivers'],
        ['slug' => 'Ogun', 'label' => 'Ogun'],
        ['slug' => 'Oyo', 'label' => 'Oyo'],
        ['slug' => 'Enugu', 'label' => 'Enugu'],
    ];

    $agentTypePills = [
        ['slug' => 'all', 'label' => 'All'],
        ['slug' => 'estate-agent', 'label' => 'Estate agent'],
        ['slug' => 'realtor', 'label' => 'Realtor'],
        ['slug' => 'broker', 'label' => 'Broker'],
    ];

    $specOptions = [
        ['slug' => 'sales', 'label' => 'Residential sales'],
        ['slug' => 'rentals', 'label' => 'Rentals and leases'],
        ['slug' => 'shortlets', 'label' => 'Shortlets'],
        ['slug' => 'land', 'label' => 'Land'],
        ['slug' => 'commercial', 'label' => 'Commercial property'],
        ['slug' => 'off-plan', 'label' => 'Off-plan projects'],
    ];

    $browseStates = [
        ['name' => 'Lagos', 'filter' => 'Lagos', 'count' => 72],
        ['name' => 'Abuja (FCT)', 'filter' => 'FCT', 'count' => 34],
        ['name' => 'Rivers', 'filter' => 'Rivers', 'count' => 14],
        ['name' => 'Ogun', 'filter' => 'Ogun', 'count' => 9],
        ['name' => 'Oyo', 'filter' => 'Oyo', 'count' => 7],
        ['name' => 'Enugu', 'filter' => 'Enugu', 'count' => 5],
        ['name' => 'Delta', 'filter' => 'Delta', 'count' => 4],
        ['name' => 'Edo', 'filter' => 'Edo', 'count' => 3],
    ];

    $agentFaqs = [
        [
            'q' => 'What does "Verified" mean for an agent?',
            'a' => 'The agent has passed Level 1 identity checks and Level 2 professional checks: state licence or registration, SCUML certificate and proof of business address. Only agents who pass both show the Verified badge.',
        ],
        [
            'q' => "Why can't I see the agent's phone number?",
            'a' => 'To protect users from off-platform fraud, initial communication and viewing requests start safely through HAYGENTS.',
        ],
        [
            'q' => 'How do I request an agent?',
            'a' => 'Click Contact Agent next to any verified agent card, or submit a request describing your target location and budget.',
        ],
        [
            'q' => 'Do I pay the agent directly?',
            'a' => 'No. Always inspect the property first and verify instructions on HAYGENTS before making any transfer.',
        ],
        [
            'q' => 'Can I report a problem with an agent?',
            'a' => 'Yes. Visit Contact Us or click Report a listing on any profile or property to alert our compliance team immediately.',
        ],
        [
            'q' => 'Can I join HAYGENTS as an agent?',
            'a' => 'Yes. Estate agents and realtors with active LASRERA, REDAN, or state licensing and SCUML registration can apply.',
        ],
    ];
@endphp

<section class="pt-4 pb-5 bg-white">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent px-0 py-0 mb-2 small">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('professionals.index') }}">Find a Professional</a></li>
                <li class="breadcrumb-item active" aria-current="page">Verified agents</li>
            </ol>
        </nav>

        <h1 class="h2 font-weight-bold mb-2" style="color: var(--hg-navy);">Verified agents in Nigeria</h1>
        <p class="text-secondary mb-4" style="max-width: 680px; font-size: 0.96rem;">
            <span class="tabular-nums font-weight-semibold text-dark">{{ $agents->total() }}</span> estate agents and realtors who have passed identity checks and professional verification, including state licensing and SCUML registration.
        </p>

        <div class="mb-3">
            <div class="hg-quick-filter-label">Specialisation</div>
            <div class="d-flex flex-wrap" style="gap: 0.45rem;">
                @foreach($quickSpecialisations as $sp)
                    <a
                        href="{{ route('agents.index', ['specialisation' => $sp['slug']]) }}"
                        class="hg-quick-pill {{ in_array($sp['slug'], $activeSpecs, true) ? 'active' : '' }}"
                    >
                        {{ $sp['label'] }}
                    </a>
                @endforeach
            </div>
        </div>

        <div class="mb-4 pb-2">
            <div class="hg-quick-filter-label">State</div>
            <div class="d-flex flex-wrap" style="gap: 0.45rem;">
                @foreach($quickStates as $st)
                    <a
                        href="{{ route('agents.index', ['state' => $st['slug']]) }}"
                        class="hg-quick-pill {{ strtolower($activeState) === strtolower($st['slug']) ? 'active' : '' }}"
                    >
                        {{ $st['label'] }}
                    </a>
                @endforeach
            </div>
        </div>

        <div class="d-lg-none mb-3">
            <button type="button" class="btn hg-btn-outline btn-block" data-toggle="collapse" data-target="#agentSidebarFiltersCollapse">
                <i class="fas fa-sliders-h mr-2"></i> Show / Hide Filters
            </button>
        </div>

        <div class="row">
            <div class="col-12 col-lg-8 mb-4 mb-lg-0">
                @forelse($agents as $idx => $agent)
                    @php
                        $descLower = strtolower($agent->description);
                        $tags = [];
                        if (str_contains($descLower, 'duplex') || str_contains($descLower, 'home') || str_contains($descLower, 'buy')) $tags[] = 'Sales';
                        if (str_contains($descLower, 'lease') || str_contains($descLower, 'tenant') || str_contains($descLower, 'serviced')) $tags[] = 'Rentals';
                        if (str_contains($descLower, 'shortlet')) $tags[] = 'Shortlets';
                        if (str_contains($descLower, 'plot') || str_contains($descLower, 'land')) $tags[] = 'Land';
                        if (str_contains($descLower, 'commercial')) $tags[] = 'Commercial';
                        if (str_contains($descLower, 'estate')) $tags[] = 'Off-plan';
                        if (empty($tags)) $tags = ['Sales', 'Rentals'];
                        $tags = array_slice($tags, 0, 3);
                    @endphp

                    <article class="hg-pro-dir-card" id="agent-{{ $agent->slug }}">
                        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between" style="gap: 1.25rem;">
                            <div class="d-flex align-items-start flex-grow-1" style="gap: 1rem;">
                                <div class="hg-pro-avatar-md">{{ $agent->initials }}</div>
                                <div class="flex-grow-1">
                                    <div class="d-flex flex-wrap align-items-center mb-1" style="gap: 0.5rem;">
                                        <h2 class="h6 font-weight-bold mb-0" style="color: var(--hg-navy);">{{ $agent->name }}</h2>
                                        <span class="hg-verified-pill"><i class="fas fa-check-circle"></i> Verified</span>
                                    </div>
                                    <div class="small text-secondary mb-1">{{ $agent->role_title }}</div>
                                    <div class="small text-muted d-flex flex-wrap align-items-center mb-2" style="gap: 0.85rem;">
                                        <span><i class="fas fa-map-marker-alt mr-1"></i> {{ $agent->city }}, {{ $agent->state }}</span>
                                        <span><i class="far fa-check-circle text-primary mr-1"></i> {{ $agent->credentials }}</span>
                                    </div>
                                    <div class="d-flex flex-wrap" style="gap: 0.4rem;">
                                        @foreach($tags as $tag)
                                            <span class="hg-dir-chip">{{ $tag }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="hg-dir-actions">
                                <a
                                    href="{{ route('contact', ['topic' => 'Contact Agent: ' . $agent->name . ' (' . $agent->city . ')']) }}"
                                    class="btn btn-sm hg-btn-outline py-2 px-3"
                                >
                                    Contact Agent
                                </a>
                            </div>
                        </div>
                    </article>

                    @if($idx === 2 || ($agents->count() < 3 && $loop->last))
                        <div class="hg-mid-cta-banner d-flex flex-column flex-md-row align-items-md-center justify-content-between">
                            <div class="mb-3 mb-md-0 pr-md-3">
                                <div class="h5 font-weight-bold text-white mb-1">Not sure which agent to choose?</div>
                                <p class="small mb-0" style="color: rgba(255, 255, 255, 0.78);">
                                    Tell us what you need in the HAYGENTS app and our team will contact you.
                                </p>
                            </div>
                            <a href="{{ route('contact', ['topic' => 'Request an Agent Match']) }}" class="btn btn-light font-weight-bold px-4 flex-shrink-0">
                                Request an agent
                            </a>
                        </div>
                    @endif
                @empty
                    <div class="p-5 text-center border rounded bg-light mb-3" style="border-radius: 14px;">
                        <h3 class="h5 font-weight-bold mb-2" style="color: var(--hg-navy);">No agents matched your filters</h3>
                        <p class="small text-muted mb-3">Clear your active filters to see all verified agents.</p>
                        <a href="{{ route('agents.index') }}" class="btn hg-btn-primary">Reset All Filters</a>
                    </div>
                @endforelse

                <div class="mt-4 d-flex justify-content-center">{{ $agents->links() }}</div>
            </div>

            <!-- Right Sidebar Filters -->
            <div class="col-12 col-lg-4">
                <div class="collapse d-lg-block" id="agentSidebarFiltersCollapse">
                    <div class="hg-filter-sidebar mb-3">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="font-weight-bold" style="color: var(--hg-navy);">
                                <i class="fas fa-sliders-h mr-2 text-primary"></i>Filters
                            </div>
                            <a href="{{ route('agents.index') }}" class="small font-weight-semibold">Clear all</a>
                        </div>

                        <form action="{{ route('agents.index') }}" method="GET" id="agentSidebarFilterForm">
                            <div class="mb-3">
                                <label class="small font-weight-bold d-block">Agent type</label>
                                <input type="hidden" name="agentType" id="agentFilterTypeInput" value="{{ $activeType }}">
                                <div class="d-flex flex-wrap" style="gap: 0.35rem;">
                                    @foreach($agentTypePills as $item)
                                        <button
                                            type="button"
                                            class="hg-seg-btn {{ $activeType === $item['slug'] ? 'active' : '' }}"
                                            data-seg-target="#agentFilterTypeInput"
                                            data-seg-value="{{ $item['slug'] }}"
                                        >
                                            {{ $item['label'] }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="small font-weight-bold">Country</label>
                                <select class="form-control form-control-sm" disabled>
                                    <option selected>Nigeria</option>
                                </select>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-6">
                                    <label class="small font-weight-bold">State</label>
                                    <select name="state" class="form-control form-control-sm">
                                        <option value="all" {{ $activeState === 'all' ? 'selected' : '' }}>Any state</option>
                                        @foreach(['Lagos', 'FCT', 'Rivers', 'Oyo'] as $st)
                                            <option value="{{ $st }}" {{ $activeState === $st ? 'selected' : '' }}>{{ $st === 'FCT' ? 'Abuja (FCT)' : $st }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-6">
                                    <label class="small font-weight-bold">City</label>
                                    <select name="city" class="form-control form-control-sm">
                                        <option value="all" {{ $activeCity === 'all' ? 'selected' : '' }}>Any city</option>
                                        @foreach(['Lekki', 'Ikoyi', 'Wuse 2', 'Bodija'] as $ct)
                                            <option value="{{ $ct }}" {{ $activeCity === $ct ? 'selected' : '' }}>{{ $ct }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="small font-weight-bold">Area</label>
                                <input type="text" name="area" class="form-control form-control-sm" value="{{ $activeArea }}" placeholder="Any area">
                            </div>

                            <div class="mb-3">
                                <label class="small font-weight-bold d-block">Years of experience</label>
                                <input type="hidden" name="years" id="agentFilterYearsInput" value="{{ $activeYears }}">
                                <div class="d-flex flex-wrap" style="gap: 0.35rem;">
                                    @foreach(['any', '3', '5', '10', '15'] as $yr)
                                        <button
                                            type="button"
                                            class="hg-seg-btn {{ $activeYears === $yr ? 'active' : '' }}"
                                            data-seg-target="#agentFilterYearsInput"
                                            data-seg-value="{{ $yr }}"
                                        >
                                            {{ $yr === 'any' ? 'Any' : $yr . '+' }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="small font-weight-bold d-block">Specialisation</label>
                                @foreach($specOptions as $idx => $sp)
                                    <div class="custom-control custom-checkbox mb-1">
                                        <input
                                            type="checkbox"
                                            class="custom-control-input"
                                            id="agentSpec{{ $idx }}"
                                            name="specialisation[]"
                                            value="{{ $sp['slug'] }}"
                                            {{ in_array($sp['slug'], $activeSpecs, true) ? 'checked' : '' }}
                                        >
                                        <label class="custom-control-label small text-secondary" for="agentSpec{{ $idx }}">{{ $sp['label'] }}</label>
                                    </div>
                                @endforeach
                            </div>

                            <button type="submit" class="btn hg-btn-primary btn-block py-2 tabular-nums">
                                Show {{ $agents->total() }} agents
                            </button>
                        </form>
                    </div>

                    <div class="hg-contact-app-card">
                        <h3 class="h6 font-weight-bold mb-1" style="color: var(--hg-navy);">Track your requests</h3>
                        <p class="small text-secondary mb-3">
                            Follow every request you send to an agent, step by step. Available in the HAYGENTS app.
                        </p>
                        <div class="d-flex flex-wrap" style="gap: 0.5rem;">
                            <a href="#app-store" class="btn btn-sm btn-dark px-3 py-2"><i class="fab fa-apple mr-2"></i>App Store</a>
                            <a href="#google-play" class="btn btn-sm btn-dark px-3 py-2"><i class="fab fa-google-play mr-2"></i>Google Play</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Browse by State -->
<section class="hg-section hg-section-alt">
    <div class="container">
        <div class="hg-eyebrow">Browse by state</div>
        <h2 class="hg-section-title">Agents by state</h2>
        <p class="hg-section-subtitle mb-4">Choose a state to see verified agents who work there.</p>

        <div class="row">
            @foreach($browseStates as $st)
                <div class="col-12 col-sm-6 col-lg-3 mb-3">
                    <a href="{{ route('agents.index', ['state' => $st['filter']]) }}" class="hg-state-browse-card">
                        <div class="d-flex align-items-center" style="gap: 0.85rem;">
                            <span class="hg-state-icon-circle"><i class="fas fa-map-marker-alt"></i></span>
                            <div>
                                <div class="font-weight-bold small" style="color: var(--hg-navy);">{{ $st['name'] }}</div>
                                <div class="text-muted tabular-nums" style="font-size: 0.76rem;">{{ $st['count'] }} verified agents</div>
                            </div>
                        </div>
                        <i class="fas fa-chevron-right text-muted small"></i>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="hg-section bg-white">
    <div class="container">
        <div class="row">
            <div class="col-12 col-lg-5 pr-lg-5 mb-4 mb-lg-0">
                <div class="hg-eyebrow">FAQ</div>
                <h2 class="hg-section-title mb-3">Questions about working with an agent</h2>
                <p class="hg-section-subtitle">Still unsure? Our support team is happy to help.</p>
            </div>
            <div class="col-12 col-lg-7">
                <div id="agentFaqAccordion">
                    @foreach($agentFaqs as $idx => $faq)
                        <div class="hg-faq-card">
                            <button
                                type="button"
                                class="hg-faq-btn"
                                data-toggle="collapse"
                                data-target="#agentFaq{{ $idx }}"
                                aria-expanded="{{ $idx === 0 ? 'true' : 'false' }}"
                            >
                                <span>{{ $faq['q'] }}</span>
                                <i class="fas fa-plus hg-faq-icon"></i>
                            </button>
                            <div id="agentFaq{{ $idx }}" class="collapse {{ $idx === 0 ? 'show' : '' }}" data-parent="#agentFaqAccordion">
                                <div class="hg-faq-body">{{ $faq['a'] }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Royal Blue Directory Band -->
<section class="hg-blue-directory-band">
    <div class="container">
        <h2>More verified agents</h2>
        <div class="row">
            <div class="col-6 col-md-3 mb-4 mb-md-0">
                <h6>By state</h6>
                <ul class="hg-blue-dir-links">
                    <li><a href="{{ route('agents.index', ['state' => 'Lagos']) }}">Agents in Lagos</a></li>
                    <li><a href="{{ route('agents.index', ['state' => 'FCT']) }}">Agents in Abuja</a></li>
                    <li><a href="{{ route('agents.index', ['state' => 'Rivers']) }}">Agents in Port Harcourt</a></li>
                    <li><a href="{{ route('agents.index', ['state' => 'Oyo']) }}">Agents in Ibadan</a></li>
                    <li><a href="{{ route('agents.index', ['state' => 'Ogun']) }}">Agents in Abeokuta</a></li>
                </ul>
            </div>
            <div class="col-6 col-md-3 mb-4 mb-md-0">
                <h6>Popular areas</h6>
                <ul class="hg-blue-dir-links">
                    <li><a href="{{ route('agents.index', ['area' => 'Lekki']) }}">Agents in Lekki</a></li>
                    <li><a href="{{ route('agents.index', ['area' => 'Ikoyi']) }}">Agents in Ikoyi</a></li>
                    <li><a href="{{ route('agents.index', ['area' => 'Wuse']) }}">Agents in Wuse 2</a></li>
                    <li><a href="{{ route('agents.index', ['area' => 'Maitama']) }}">Agents in Maitama</a></li>
                    <li><a href="{{ route('agents.index', ['area' => 'Ikeja']) }}">Agents in Ikeja</a></li>
                </ul>
            </div>
            <div class="col-6 col-md-3">
                <h6>By specialisation</h6>
                <ul class="hg-blue-dir-links">
                    <li><a href="{{ route('agents.index', ['specialisation' => 'sales']) }}">Sales agents</a></li>
                    <li><a href="{{ route('agents.index', ['specialisation' => 'rentals']) }}">Rental agents</a></li>
                    <li><a href="{{ route('agents.index', ['specialisation' => 'land']) }}">Land agents</a></li>
                    <li><a href="{{ route('agents.index', ['specialisation' => 'commercial']) }}">Commercial agents</a></li>
                </ul>
            </div>
            <div class="col-6 col-md-3">
                <h6>Other professionals</h6>
                <ul class="hg-blue-dir-links">
                    <li><a href="{{ route('companies.index') }}">Property companies</a></li>
                    <li><a href="{{ route('professionals.index', ['category' => 'property-lawyers']) }}">Property lawyers</a></li>
                    <li><a href="{{ route('professionals.index', ['category' => 'land-surveyors']) }}">Surveyors</a></li>
                    <li><a href="{{ route('professionals.index', ['category' => 'property-valuers']) }}">Valuers</a></li>
                </ul>
            </div>
        </div>
    </div>
</section>
@endsection
