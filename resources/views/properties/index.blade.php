@extends('layouts.app')

@section('title', 'Browse Properties — HAYGENTS')

@section('content')
@php
    $activePurpose = $filters['purpose'] ?? 'all';
    $activeCategory = $filters['category'] ?? 'all';
    $activeCity = $filters['city'] ?? '';
    $activeState = $filters['state'] ?? '';
    $activeLocation = $filters['location'] ?? ($filters['area'] ?? ($activeCity ?: $activeState));
    $activeSort = $filters['sort'] ?? 'newest';
    $startIdx = $properties->total() === 0 ? 0 : (($properties->currentPage() - 1) * $properties->perPage()) + 1;
    $endIdx = min($properties->total(), $properties->currentPage() * $properties->perPage());
@endphp

<section class="pt-4 pb-5 bg-white">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent px-0 py-0 mb-2 small">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('properties.index') }}">Properties</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $activeLocation ?: 'All Locations' }}</li>
            </ol>
        </nav>

        <h1 class="h2 font-weight-bold mb-1" style="color: var(--hg-navy);">
            Properties {{ $activePurpose !== 'all' ? 'for ' . $activePurpose : '' }} {{ $activeLocation ? 'in ' . $activeLocation : 'across Nigeria' }}
        </h1>
        <p class="small text-muted mb-4">
            <span class="tabular-nums font-weight-semibold text-dark">{{ $properties->total() }}</span> reviewed properties match your search. Exact street numbers are shared when you arrange an inspection.
        </p>

        <!-- Horizontal Top Search Bar -->
        <form action="{{ route('properties.index') }}" method="GET" class="hg-listing-search-bar mb-4">
            <div class="form-row align-items-center no-gutters">
                <div class="col-6 col-md-2 px-2 py-1 border-right">
                    <label class="d-block mb-0 text-muted" style="font-size: 0.7rem; font-weight: 700;">Looking to</label>
                    <select name="purpose" class="form-control form-control-sm border-0 px-0 font-weight-semibold shadow-none">
                        <option value="all" {{ $activePurpose === 'all' ? 'selected' : '' }}>Any</option>
                        <option value="sale" {{ $activePurpose === 'sale' ? 'selected' : '' }}>Buy</option>
                        <option value="rent" {{ $activePurpose === 'rent' ? 'selected' : '' }}>Rent</option>
                        <option value="lease" {{ $activePurpose === 'lease' ? 'selected' : '' }}>Lease</option>
                        <option value="shortlet" {{ $activePurpose === 'shortlet' ? 'selected' : '' }}>Shortlet</option>
                    </select>
                </div>
                <div class="col-6 col-md-3 px-2 py-1 border-right">
                    <label class="d-block mb-0 text-muted" style="font-size: 0.7rem; font-weight: 700;">Location</label>
                    <input type="text" name="location" class="form-control form-control-sm border-0 px-0 font-weight-semibold shadow-none" value="{{ $activeLocation }}" placeholder="Lekki, Ikoyi, Abuja...">
                </div>
                <div class="col-6 col-md-2 px-2 py-1 border-right">
                    <label class="d-block mb-0 text-muted" style="font-size: 0.7rem; font-weight: 700;">Property type</label>
                    <select name="category" class="form-control form-control-sm border-0 px-0 font-weight-semibold shadow-none">
                        <option value="all" {{ $activeCategory === 'all' ? 'selected' : '' }}>All types</option>
                        @forelse ($catego as $cat)
                        <option value="{{ $cat->slug }}" {{ $activeCategory === $cat->slug ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @empty

                        @endforelse
                        {{-- <option value="apartments" {{ $activeCategory === 'apartments' ? 'selected' : '' }}>Apartments</option>
                        <option value="land-plots" {{ $activeCategory === 'land-plots' ? 'selected' : '' }}>Land &amp; Plots</option>
                        <option value="commercial" {{ $activeCategory === 'commercial' ? 'selected' : '' }}>Commercial</option>
                        <option value="shortlets" {{ $activeCategory === 'shortlets' ? 'selected' : '' }}>Shortlets</option>
                        <option value="office-spaces" {{ $activeCategory === 'office-spaces' ? 'selected' : '' }}>Office Spaces</option> --}}
                    </select>
                </div>
                <div class="col-6 col-md-2 px-2 py-1 border-right">
                    <label class="d-block mb-0 text-muted" style="font-size: 0.7rem; font-weight: 700;">Price</label>
                    <select name="priceRange" class="form-control form-control-sm border-0 px-0 font-weight-semibold shadow-none">
                        <option value="any">Any price</option>
                        <option value="0-5000000" {{ ($filters['priceRange'] ?? '') === '0-5000000' ? 'selected' : '' }}>Up to ₦5M</option>
                        <option value="5000000-25000000" {{ ($filters['priceRange'] ?? '') === '5000000-25000000' ? 'selected' : '' }}>₦5M – ₦25M</option>
                        <option value="25000000-75000000" {{ ($filters['priceRange'] ?? '') === '25000000-75000000' ? 'selected' : '' }}>₦25M – ₦75M</option>
                        <option value="75000000-150000000" {{ ($filters['priceRange'] ?? '') === '75000000-150000000' ? 'selected' : '' }}>₦75M – ₦150M</option>
                        <option value="150000000+" {{ ($filters['priceRange'] ?? '') === '150000000+' ? 'selected' : '' }}>₦150M+</option>
                    </select>
                </div>
                <div class="col-6 col-md-1 px-2 py-1">
                    <label class="d-block mb-0 text-muted" style="font-size: 0.7rem; font-weight: 700;">Beds</label>
                    <select name="bedrooms" class="form-control form-control-sm border-0 px-0 font-weight-semibold shadow-none">
                        <option value="any">Any</option>
                        @foreach(['1', '2', '3', '4', '5', '6', '7'] as $b)
                            <option value="{{ $b }}" {{ ($filters['bedrooms'] ?? '') === $b ? 'selected' : '' }}>{{ $b }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2 pl-md-2">
                    <button type="submit" class="btn hg-btn-primary btn-block py-2"><i class="fas fa-search mr-1"></i> Search</button>
                </div>
            </div>
        </form>

        <!-- Mobile Filters Toggle -->
        <div class="d-lg-none mb-3">
            <button type="button" class="btn hg-btn-outline btn-block" data-toggle="collapse" data-target="#sidebarFiltersCollapse">
                <i class="fas fa-sliders-h mr-2"></i> Show / Hide Filters
            </button>
        </div>

        <div class="row">
            <div class="col-12 col-lg-8 mb-4 mb-lg-0">
                <div class="d-flex flex-wrap align-items-center justify-content-between mb-3" style="gap: 0.75rem;">
                    <div class="small font-weight-semibold text-dark tabular-nums">
                        Showing {{ $startIdx }} to {{ $endIdx }} of {{ $properties->total() }} properties
                    </div>
                    <form action="{{ route('properties.index') }}" method="GET">
                        @foreach(request()->except(['sort', 'page']) as $k => $v)
                            @if(!is_array($v))
                                <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                            @endif
                        @endforeach
                        <select name="sort" onchange="this.form.submit()" class="form-control form-control-sm font-weight-semibold" style="border-radius: 8px; min-width: 175px;">
                            <option value="newest" {{ $activeSort === 'newest' ? 'selected' : '' }}>Sort: Newest first</option>
                            <option value="price_asc" {{ $activeSort === 'price_asc' ? 'selected' : '' }}>Sort: Price low to high</option>
                            <option value="price_desc" {{ $activeSort === 'price_desc' ? 'selected' : '' }}>Sort: Price high to low</option>
                        </select>
                    </form>
                </div>

                @forelse($properties as $idx => $property)
                    <article class="hg-listing-card">
                        <div class="hg-listing-media">
                            <a href="{{ route('properties.show', $property->slug) }}" class="d-block w-100 h-100">
                                <img src="{{ asset(json_decode($property->image_path)[0]) }}" alt="{{ $property->title }}" loading="lazy">
                            </a>
                        </div>
                        <div class="hg-listing-body">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <span class="small font-weight-bold text-primary">
                                    For {{ $property->purpose }} &middot; {{ $property->category->name ?? 'Property' }}
                                </span>
                                <span class="hg-listing-ref tabular-nums">{{ $property->ref_code }}</span>
                            </div>
                            <h2 class="h6 font-weight-bold mb-1" style="color: var(--hg-navy);">
                                <a href="{{ route('properties.show', $property->slug) }}" class="text-reset text-decoration-none">{{ $property->title }}</a>
                            </h2>
                            <div class="small text-muted mb-2">
                                <i class="fas fa-map-marker-alt mr-1"></i> {{ $property->address_area }}, {{ $property->city }}
                            </div>
                            <p class="hg-listing-desc mb-2">{{ $property->description }}</p>
                            <div class="small font-weight-semibold text-dark tabular-nums mb-3">
                                {{ $property->bedrooms }} beds &middot; {{ $property->bathrooms }} baths &middot; {{ $property->size_sqm }} sqm
                            </div>
                            <div class="mt-auto d-flex flex-wrap align-items-center justify-content-between pt-2 border-top" style="gap: 0.5rem;">
                                <div class="hg-property-price mb-0 tabular-nums">
                                    {{ $property->formatted_price }}
                                    @if($property->price_period)<span>/ {{ $property->price_period }}</span>@endif
                                </div>
                                {{-- @if($property->inspection_status === 'verified')
                                    <span class="hg-verified-pill"><i class="fas fa-check-circle"></i> Physical inspection completed</span>
                                @elseif($property->inspection_status === 'pending')
                                    <span class="hg-pending-pill"><i class="fas fa-clock"></i> Inspection pending</span>
                                @else
                                    <span class="hg-verif-mini hg-verif-unverified"><i class="fas fa-minus-circle"></i> Inspection unverified</span>
                                @endif --}}
                            </div>
                        </div>
                    </article>

                    @if($idx === 2 || ($properties->count() < 3 && $loop->last))
                        <div class="hg-mid-cta-banner d-flex flex-column flex-md-row align-items-md-center justify-content-between">
                            <div class="mb-3 mb-md-0 pr-md-3">
                                <div class="h5 font-weight-bold text-white mb-1">Can't find what you want?</div>
                                <p class="small mb-0" style="color: rgba(255, 255, 255, 0.78);">Tell us your preferred location and budget and our team will source verified matches.</p>
                            </div>
                            <a href="{{ route('contact', ['topic' => 'A property listing']) }}" class="btn btn-light font-weight-bold px-4 flex-shrink-0">Request a property</a>
                        </div>
                    @endif
                @empty
                    <div class="p-5 text-center border rounded bg-light mb-3" style="border-radius: 14px;">
                        <h3 class="h5 font-weight-bold mb-2">No matching properties</h3>
                        <p>Try a wider price range, fewer bedrooms or a nearby area. Or tell us what you need and our team will search for you.</p>
                        <div class="d-flex justify-content-center">
                            <a href="{{ route('properties.index') }}" class="btn hg-btn-primary mr-3">Clear Filters</a>
                            <a href="{{ route('properties.index') }}" class="btn hg-btn-primary">Request a property from the app</a>
                        </div>
                    </div>
                @endforelse

                <div class="mt-4 d-flex justify-content-center">{{ $properties->links() }}</div>
            </div>

            <div class="col-12 col-lg-4">
                <div class="collapse d-lg-block" id="sidebarFiltersCollapse">
                    <div class="hg-filter-sidebar mb-3">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="font-weight-bold" style="color: var(--hg-navy);"><i class="fas fa-sliders-h mr-2 text-primary"></i>Filters</div>
                            <a href="{{ route('properties.index') }}" class="small font-weight-semibold">Clear all</a>
                        </div>

                        <form action="{{ route('properties.index') }}" method="GET">
                            <div class="form-group">
                                <label class="small font-weight-bold">Looking to</label>
                                <div class="form-group lookFor">
                                    <div class="looking-options">
                                        <label class="looking-option">
                                            <input type="radio" name="looking_to"
                                                value="Buy" checked>
                                            <span>Buy</span>
                                        </label>

                                        <label class="looking-option">
                                            <input type="radio" name="looking_to"
                                                value="Rent">
                                            <span>Rent</span>
                                        </label>

                                        <label class="looking-option">
                                            <input type="radio" name="looking_to"
                                                value="Lease">
                                            <span>Lease</span>
                                        </label>

                                        <label class="looking-option">
                                            <input type="radio" name="looking_to"
                                                value="Shortlet">
                                            <span>Shortlet</span>
                                        </label>
                                    </div>
                                </div>

                                {{-- <select name="purpose" class="form-control form-control-sm">
                                    <option value="all" {{ $activePurpose === 'all' ? 'selected' : '' }}>All</option>
                                    <option value="sale" {{ $activePurpose === 'sale' ? 'selected' : '' }}>Buy</option>
                                    <option value="rent" {{ $activePurpose === 'rent' ? 'selected' : '' }}>Rent</option>
                                    <option value="lease" {{ $activePurpose === 'lease' ? 'selected' : '' }}>Lease</option>
                                    <option value="shortlet" {{ $activePurpose === 'shortlet' ? 'selected' : '' }}>Shortlet</option>
                                </select> --}}
                            </div>
                            <div class="form-group">
                                <label class="small firaMedium">Country</label>
                                <select id="propertyFilterCountry" name="country" class="form-control form-control-sm" data-selected="{{ $activeCountry }}">
                                    <option value="all" {{ $activeCountry === 'all' ? 'selected' : '' }}>All Countries</option>
                                    @foreach($allCountry as $countryOpt)
                                        <option value="{{ $countryOpt }}" {{ strcasecmp($activeCountry, $countryOpt) === 0 ? 'selected' : '' }}>{{ $countryOpt }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-6">
                                    <label class="small firaMedium">State</label>
                                    <select name="state" class="form-control fieldOne">
                                        <option value="all">All States</option>
                                        @foreach(['Lagos', 'FCT', 'Rivers', 'Oyo'] as $st)
                                            <option value="{{ $st }}" {{ $activeState === $st ? 'selected' : '' }}>{{ $st }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-6">
                                    <label class="small firaMedium">City</label>
                                    <select name="city" class="form-control fieldOne">
                                        <option value="all">All Cities</option>
                                        @foreach(['Lagos', 'Abuja', 'Port Harcourt', 'Ibadan'] as $ct)
                                            <option value="{{ $ct }}" {{ $activeCity === $ct ? 'selected' : '' }}>{{ $ct }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="small firaMedium">Area / Neighbourhood</label>
                                <input type="text" name="area" class="form-control fieldOne" value="{{ $filters['area'] ?? '' }}" placeholder="e.g. Lekki, Ikoyi">
                            </div>
                            <div class="form-row">
                                <div class="form-group col-6">
                                    <label class="small firaMedium">Min price (₦)</label>
                                    <input type="number" name="minPrice" class="form-control fieldOne" value="{{ $filters['minPrice'] ?? '' }}">
                                </div>
                                <div class="form-group col-6">
                                    <label class="small firaMedium">Max price (₦)</label>
                                    <input type="number" name="maxPrice" class="form-control fieldOne" value="{{ $filters['maxPrice'] ?? '' }}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="small firaMedium">Furnishing</label>
                                <select name="furnishing" class="form-control fieldOne">
                                    <option value="any">Any</option>
                                    @foreach(['Furnished', 'Semi-Furnished', 'Unfurnished'] as $fn)
                                        <option value="{{ $fn }}" {{ ($filters['furnishing'] ?? '') === $fn ? 'selected' : '' }}>{{ $fn }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="small firaMedium d-block">Verification checks</label>
                                <div class="custom-control custom-checkbox mb-1">
                                    <input type="checkbox" class="custom-control-input" id="laraPhysical" name="checkPhysical" value="1" {{ !empty($filters['checkPhysical']) ? 'checked' : '' }}>
                                    <label class="custom-control-label small text-secondary" for="laraPhysical">Physical inspection completed</label>
                                </div>
                                <div class="custom-control custom-checkbox mb-1">
                                    <input type="checkbox" class="custom-control-input" id="laraOwnership" name="checkOwnership" value="1" {{ !empty($filters['checkOwnership']) ? 'checked' : '' }}>
                                    <label class="custom-control-label small text-secondary" for="laraOwnership">Ownership documents reviewed</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="laraSurvey" name="checkSurvey" value="1" {{ !empty($filters['checkSurvey']) ? 'checked' : '' }}>
                                    <label class="custom-control-label small text-secondary" for="laraSurvey">Survey documents reviewed</label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="small firaMedium">Keywords</label>
                                <input type="text" name="keywords" class="form-control fieldOne" value="{{ $filters['keywords'] ?? '' }}" placeholder="e.g. pool, C of O">
                            </div>
                            <div class="form-group">
                                <label class="small firaMedium">Property reference</label>
                                <input type="text" name="ref" class="form-control fieldOne" value="{{ $filters['ref'] ?? '' }}" placeholder="e.g. HGT-LK-20401">
                            </div>
                            <button type="submit" class="btn hg-btn-primary btn-block py-2">Show {{ $properties->total() }} properties</button>
                        </form>
                    </div>

                    <div class="hg-contact-app-card">
                        <h3 class="h6 firaSemiBold mb-1" style="color: var(--hg-navy);">Save searches and get alerts</h3>
                        <p class="small text-secondary mb-3">Get notified when a matching property is approved on HAYGENTS.</p>
                        <div class="d-flex flex-wrap" style="gap: 0.5rem;">
                            <a href="#app-store" class="">
                                <img src="{{ asset('assets/images/appstore2.png') }}" alt="">
                            </a>
                            <a href="#google-play" class="">
                                <img src="{{ asset('assets/images/google2.png') }}" alt="">
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
