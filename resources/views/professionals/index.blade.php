@extends('layouts.app')

@section('title', 'Property Lawyers, Surveyors and Valuers — HAYGENTS')

@section('content')
@php
    $activeCat = $filters['category'] ?? ($category ?? 'all');
    $activeState = $filters['state'] ?? 'all';
    $activeCity = $filters['city'] ?? 'all';
    $activeArea = $filters['area'] ?? '';
    $activeYears = $filters['years'] ?? 'any';
    $rawServices = $filters['service'] ?? [];
    $activeServices = is_array($rawServices)
        ? array_map('strtolower', $rawServices)
        : array_filter(array_map(fn($s) => strtolower(trim($s)), explode(',', (string) $rawServices)));

    $quickProfessions = [
        ['slug' => 'lawyers', 'label' => 'Property lawyers'],
        ['slug' => 'surveyors', 'label' => 'Land surveyors'],
        ['slug' => 'valuers', 'label' => 'Estate valuers'],
    ];

    $quickStates = [
        ['slug' => 'Lagos', 'label' => 'Lagos'],
        ['slug' => 'FCT', 'label' => 'Abuja (FCT)'],
        ['slug' => 'Rivers', 'label' => 'Rivers'],
        ['slug' => 'Ogun', 'label' => 'Ogun'],
        ['slug' => 'Oyo', 'label' => 'Oyo'],
        ['slug' => 'Enugu', 'label' => 'Enugu'],
    ];

    $segmentedProfs = [
        ['slug' => 'all', 'label' => 'All'],
        ['slug' => 'property-lawyers', 'label' => 'Lawyer'],
        ['slug' => 'land-surveyors', 'label' => 'Surveyor'],
        ['slug' => 'property-valuers', 'label' => 'Valuer'],
    ];

    $serviceOptions = [
        'Title search',
        'Deed of assignment',
        "Governor's consent",
        'Survey plan',
        'Valuation report',
        'Contract review',
    ];

    $browseStates = [
        ['name' => 'Lagos', 'filter' => 'Lagos', 'count' => 38],
        ['name' => 'Abuja (FCT)', 'filter' => 'FCT', 'count' => 21],
        ['name' => 'Rivers', 'filter' => 'Rivers', 'count' => 11],
        ['name' => 'Ogun', 'filter' => 'Ogun', 'count' => 9],
        ['name' => 'Oyo', 'filter' => 'Oyo', 'count' => 7],
        ['name' => 'Enugu', 'filter' => 'Enugu', 'count' => 4],
        ['name' => 'Delta', 'filter' => 'Delta', 'count' => 3],
        ['name' => 'Edo', 'filter' => 'Edo', 'count' => 3],
    ];

    $proFaqs = [
        [
            'q' => 'Which professional do I need?',
            'a' => 'A lawyer checks the title and prepares documents such as a deed of assignment. A land surveyor prepares or confirms the survey plan. An estate surveyor and valuer tells you what the property is worth. Not sure? Tell us in the HAYGENTS app and we will advise.',
        ],
        [
            'q' => 'How much does a service cost?',
            'a' => 'Service fees depend on property value, state registry location, and scope. HAYGENTS confirms a transparent scope and fixed fee quote before any work begins.',
        ],
        [
            'q' => 'How is a professional verified?',
            'a' => 'Every professional completes Level 1 identity KYC, practice address confirmation, and active regulator checks with NBA, SURCON, or ESVARBON.',
        ],
        [
            'q' => 'Can I choose a specific professional?',
            'a' => 'Yes. Browse profiles by state, specialization, and years of practice, then click View profile or Request Service directly from their page.',
        ],
        [
            'q' => 'How do I get the report or documents?',
            'a' => 'Signed title reports, charted survey plans, and valuation certificates are uploaded securely to your HAYGENTS account with milestone updates at every step.',
        ],
        [
            'q' => 'Can I join HAYGENTS as a professional?',
            'a' => 'Yes. Qualified property lawyers, SURCON surveyors, and ESVARBON valuers can sign up and submit active practicing credentials for verification.',
        ],
    ];
@endphp

<section class="pt-4 pb-5 bg-white">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent px-0 py-0 mb-2 small">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('professionals.index') }}">Find a Professional</a></li>
                <li class="breadcrumb-item active" aria-current="page">Lawyers, surveyors and valuers</li>
            </ol>
        </nav>

        <h1 class="h2 font-weight-bold mb-2" style="color: var(--hg-navy);">Property lawyers, surveyors and valuers</h1>
        <p class="text-secondary mb-4" style="max-width: 720px; font-size: 0.96rem;">
            <span class="tabular-nums font-weight-semibold text-dark">{{ $professionals->total() }}</span> verified professionals for title searches, property documents, survey plans and valuations. Each has passed identity checks and professional checks with their regulator (NBA, SURCON or ESVARBON).
        </p>

        <div class="mb-3">
            <div class="hg-quick-filter-label">Profession</div>
            <div class="d-flex flex-wrap" style="gap: 0.45rem;">
                @foreach($quickProfessions as $item)
                    <a
                        href="{{ route('professionals.index', ['category' => $item['slug']]) }}"
                        class="hg-quick-pill {{ $activeCat === $item['slug'] ? 'active' : '' }}"
                    >
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </div>
        </div>

        <div class="mb-4 pb-2">
            <div class="hg-quick-filter-label">State</div>
            <div class="d-flex flex-wrap" style="gap: 0.45rem;">
                @foreach($quickStates as $st)
                    <a
                        href="{{ route('professionals.index', ['state' => $st['slug']]) }}"
                        class="hg-quick-pill {{ strtolower($activeState) === strtolower($st['slug']) ? 'active' : '' }}"
                    >
                        {{ $st['label'] }}
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Mobile Filters Toggle -->
        <div class="d-lg-none mb-3">
            <button type="button" class="btn hg-btn-outline btn-block" data-toggle="collapse" data-target="#proSidebarFiltersCollapse">
                <i class="fas fa-sliders-h mr-2"></i> Show / Hide Filters
            </button>
        </div>

        <div class="row">
            <div class="col-12 col-lg-8 mb-4 mb-lg-0">
                @forelse($professionals as $idx => $professional)
                    @php
                        $tags = match ($professional->category_slug) {
                            'property-lawyers' => ['Title search', 'Deed of assignment', "Governor's consent"],
                            'land-surveyors' => ['Survey plan', 'Boundary survey', 'Charting check'],
                            default => ['Valuation report', 'Property inspection', 'Rent review'],
                        };
                        $jobsCount = match ($professional->slug) {
                            'chidinma-nwosu' => 57,
                            'bola-akande' => 71,
                            'kunle-ogunleye' => 48,
                            default => 39,
                        };
                    @endphp

                    <article class="hg-pro-dir-card" id="prof-{{ $professional->slug }}">
                        <div class="d-flex flex-column flex-md-row align-items-start justify-content-between" style="gap: 1.25rem;">
                            <div class="d-flex align-items-start flex-grow-1" style="gap: 1rem;">
                                <div class="hg-pro-avatar-md">{{ $professional->initials }}</div>
                                <div class="flex-grow-1">
                                    <div class="d-flex flex-wrap align-items-center mb-1" style="gap: 0.5rem;">
                                        <h2 class="h6 font-weight-bold mb-0" style="color: var(--hg-navy);">
                                            <a href="{{ route('professionals.show', $professional->slug) }}" class="text-reset text-decoration-none">
                                                {{ $professional->name }}
                                            </a>
                                        </h2>
                                        @if($professional->verification_status === 'verified')
                                            <span class="hg-verified-pill"><i class="fas fa-check-circle"></i> Verified</span>
                                        @else
                                            <span class="hg-pending-pill"><i class="fas fa-clock"></i> Pending</span>
                                        @endif
                                    </div>
                                    <div class="small text-secondary mb-1">{{ $professional->category }} &middot; {{ $professional->years_exp }} years</div>
                                    <div class="small text-muted d-flex flex-wrap align-items-center mb-2" style="gap: 0.85rem;">
                                        <span><i class="fas fa-map-marker-alt mr-1"></i> {{ $professional->city }}, {{ $professional->state }}</span>
                                        <span><i class="far fa-check-circle text-primary mr-1"></i> {{ $professional->credentials }}</span>
                                    </div>
                                    <div class="d-flex flex-wrap mb-2" style="gap: 0.4rem;">
                                        @foreach($tags as $tag)
                                            <span class="hg-dir-chip">{{ $tag }}</span>
                                        @endforeach
                                    </div>
                                    <p class="small text-secondary mb-0" style="line-height: 1.5;">
                                        {{ $professional->description }} {{ $jobsCount }} jobs completed through HAYGENTS.
                                    </p>
                                </div>
                            </div>
                            <div class="hg-dir-actions">
                                <a href="{{ route('professionals.show', $professional->slug) }}" class="btn btn-sm hg-btn-primary py-2 px-3">
                                    View profile
                                </a>
                                <a
                                    href="{{ route('contact', ['topic' => 'Request Service: ' . $professional->name]) }}"
                                    class="btn btn-sm hg-btn-outline py-2 px-3"
                                >
                                    Request Service
                                </a>
                            </div>
                        </div>
                    </article>

                    @if($idx === 2 || ($professionals->count() < 3 && $loop->last))
                        <div class="hg-mid-cta-banner d-flex flex-column flex-md-row align-items-md-center justify-content-between">
                            <div class="mb-3 mb-md-0 pr-md-3">
                                <div class="h5 font-weight-bold text-white mb-1">Not sure which professional you need?</div>
                                <p class="small mb-0" style="color: rgba(255, 255, 255, 0.78);">
                                    Tell us about the property in the HAYGENTS app and our team will recommend the right service.
                                </p>
                            </div>
                            <a href="{{ route('contact', ['topic' => 'Professional Service Recommendation']) }}" class="btn btn-light font-weight-bold px-4 flex-shrink-0">
                                Request a service
                            </a>
                        </div>
                    @endif
                @empty
                    <div class="p-5 text-center border rounded bg-light mb-3" style="border-radius: 14px;">
                        <h3 class="h5 font-weight-bold mb-2" style="color: var(--hg-navy);">No professionals matched your filters</h3>
                        <p class="small text-muted mb-3">Try resetting your state, years of practice, or service filters.</p>
                        <a href="{{ route('professionals.index') }}" class="btn hg-btn-primary">Reset All Filters</a>
                    </div>
                @endforelse

                <div class="mt-4 d-flex justify-content-center">{{ $professionals->links() }}</div>
            </div>

            <!-- Right Sidebar Filters -->
            <div class="col-12 col-lg-4">
                <div class="collapse d-lg-block" id="proSidebarFiltersCollapse">
                    <div class="hg-filter-sidebar mb-3">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="font-weight-bold" style="color: var(--hg-navy);">
                                <i class="fas fa-sliders-h mr-2 text-primary"></i>Filters
                            </div>
                            <a href="{{ route('professionals.index') }}" class="small font-weight-semibold">Clear all</a>
                        </div>

                        <form action="{{ route('professionals.index') }}" method="GET" id="proSidebarFilterForm">
                            <div class="mb-3">
                                <label class="small font-weight-bold d-block">Profession</label>
                                <input type="hidden" name="category" id="proFilterCatInput" value="{{ $activeCat }}">
                                <div class="d-flex flex-wrap" style="gap: 0.4rem;">
                                    @foreach($segmentedProfs as $item)
                                        <button
                                            type="button"
                                            class="hg-seg-btn {{ $activeCat === $item['slug'] ? 'active' : '' }}"
                                            data-seg-target="#proFilterCatInput"
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
                                        @foreach(['Lagos', 'FCT', 'Rivers', 'Ogun', 'Oyo'] as $st)
                                            <option value="{{ $st }}" {{ $activeState === $st ? 'selected' : '' }}>{{ $st === 'FCT' ? 'Abuja (FCT)' : $st }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-6">
                                    <label class="small font-weight-bold">City</label>
                                    <select name="city" class="form-control form-control-sm">
                                        <option value="all" {{ $activeCity === 'all' ? 'selected' : '' }}>Any city</option>
                                        @foreach(['Ikeja', 'Ikoyi', 'Maitama', 'Wuse 2', 'Abeokuta', 'GRA'] as $ct)
                                            <option value="{{ $ct }}" {{ $activeCity === $ct ? 'selected' : '' }}>{{ $ct === 'GRA' ? 'Port Harcourt' : $ct }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="small font-weight-bold">Area</label>
                                <input type="text" name="area" class="form-control form-control-sm" value="{{ $activeArea }}" placeholder="Any area">
                            </div>

                            <div class="mb-3">
                                <label class="small font-weight-bold d-block">Years of practice</label>
                                <input type="hidden" name="years" id="proFilterYearsInput" value="{{ $activeYears }}">
                                <div class="d-flex flex-wrap" style="gap: 0.35rem;">
                                    @foreach(['any', '3', '5', '10', '15'] as $yr)
                                        <button
                                            type="button"
                                            class="hg-seg-btn {{ $activeYears === $yr ? 'active' : '' }}"
                                            data-seg-target="#proFilterYearsInput"
                                            data-seg-value="{{ $yr }}"
                                        >
                                            {{ $yr === 'any' ? 'Any' : $yr . '+' }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="small font-weight-bold d-block">Service</label>
                                @foreach($serviceOptions as $idx => $svc)
                                    <div class="custom-control custom-checkbox mb-1">
                                        <input
                                            type="checkbox"
                                            class="custom-control-input"
                                            id="proSvc{{ $idx }}"
                                            name="service[]"
                                            value="{{ $svc }}"
                                            {{ in_array(strtolower($svc), $activeServices, true) ? 'checked' : '' }}
                                        >
                                        <label class="custom-control-label small text-secondary" for="proSvc{{ $idx }}">{{ $svc }}</label>
                                    </div>
                                @endforeach
                            </div>

                            <button type="submit" class="btn hg-btn-primary btn-block py-2 tabular-nums">
                                Show {{ $professionals->total() }} professionals
                            </button>
                        </form>
                    </div>

                    <div class="hg-contact-app-card">
                        <h3 class="h6 font-weight-bold mb-1" style="color: var(--hg-navy);">Track your service</h3>
                        <p class="small text-secondary mb-3">
                            Follow your title search, survey or valuation step by step, and get the report in the HAYGENTS app.
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
        <h2 class="hg-section-title">Professionals by state</h2>
        <p class="hg-section-subtitle mb-4">Choose a state to see verified professionals who work there.</p>

        <div class="row">
            @foreach($browseStates as $st)
                <div class="col-12 col-sm-6 col-lg-3 mb-3">
                    <a href="{{ route('professionals.index', ['state' => $st['filter']]) }}" class="hg-state-browse-card">
                        <div class="d-flex align-items-center" style="gap: 0.85rem;">
                            <span class="hg-state-icon-circle"><i class="fas fa-map-marker-alt"></i></span>
                            <div>
                                <div class="font-weight-bold small" style="color: var(--hg-navy);">{{ $st['name'] }}</div>
                                <div class="text-muted tabular-nums" style="font-size: 0.76rem;">{{ $st['count'] }} verified professionals</div>
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
                <h2 class="hg-section-title mb-3">Questions about professional services</h2>
                <p class="hg-section-subtitle">Still unsure? Our support team is happy to help.</p>
            </div>
            <div class="col-12 col-lg-7">
                <div id="proFaqAccordion">
                    @foreach($proFaqs as $idx => $faq)
                        <div class="hg-faq-card">
                            <button
                                type="button"
                                class="hg-faq-btn"
                                data-toggle="collapse"
                                data-target="#proFaq{{ $idx }}"
                                aria-expanded="{{ $idx === 0 ? 'true' : 'false' }}"
                            >
                                <span>{{ $faq['q'] }}</span>
                                <i class="fas fa-plus hg-faq-icon"></i>
                            </button>
                            <div id="proFaq{{ $idx }}" class="collapse {{ $idx === 0 ? 'show' : '' }}" data-parent="#proFaqAccordion">
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
        <h2>More property professionals</h2>
        <div class="row">
            <div class="col-6 col-md-3 mb-4 mb-md-0">
                <h6>By profession</h6>
                <ul class="hg-blue-dir-links">
                    <li><a href="{{ route('professionals.index', ['category' => 'property-lawyers']) }}">Property lawyers</a></li>
                    <li><a href="{{ route('professionals.index', ['category' => 'land-surveyors']) }}">Land surveyors</a></li>
                    <li><a href="{{ route('professionals.index', ['category' => 'property-valuers']) }}">Estate surveyors and valuers</a></li>
                    <li><a href="{{ route('professionals.index', ['category' => 'independent-valuers']) }}">Independent valuers</a></li>
                    <li><a href="{{ route('professionals.index') }}">All professionals</a></li>
                </ul>
            </div>
            <div class="col-6 col-md-3 mb-4 mb-md-0">
                <h6>By service</h6>
                <ul class="hg-blue-dir-links">
                    <li><a href="{{ route('professionals.index', ['service' => 'Title search']) }}">Title search</a></li>
                    <li><a href="{{ route('professionals.index', ['service' => 'Deed of assignment']) }}">Deed of assignment</a></li>
                    <li><a href="{{ route('professionals.index', ['service' => "Governor's consent"]) }}">Governor's consent</a></li>
                    <li><a href="{{ route('professionals.index', ['service' => 'Survey plan']) }}">Survey plan</a></li>
                    <li><a href="{{ route('professionals.index', ['service' => 'Valuation report']) }}">Valuation report</a></li>
                </ul>
            </div>
            <div class="col-6 col-md-3">
                <h6>By state</h6>
                <ul class="hg-blue-dir-links">
                    <li><a href="{{ route('professionals.index', ['state' => 'Lagos']) }}">Professionals in Lagos</a></li>
                    <li><a href="{{ route('professionals.index', ['state' => 'FCT']) }}">Professionals in Abuja</a></li>
                    <li><a href="{{ route('professionals.index', ['state' => 'Rivers']) }}">Professionals in Port Harcourt</a></li>
                    <li><a href="{{ route('professionals.index', ['state' => 'Ogun']) }}">Professionals in Ogun</a></li>
                </ul>
            </div>
            <div class="col-6 col-md-3">
                <h6>Other directories</h6>
                <ul class="hg-blue-dir-links">
                    <li><a href="{{ route('agents.index') }}">Verified agents</a></li>
                    <li><a href="{{ route('companies.index') }}">Property companies</a></li>
                    <li><a href="{{ route('properties.index', ['purpose' => 'sale']) }}">Properties for sale</a></li>
                    <li><a href="{{ route('properties.index', ['purpose' => 'rent']) }}">Properties for rent</a></li>
                </ul>
            </div>
        </div>
    </div>
</section>
@endsection
