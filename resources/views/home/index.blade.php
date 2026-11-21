@extends('layouts.app')

@section('title', 'HAYGENTS — Find Property You Can Trust | Real Estate Marketplace')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flexslider/2.7.2/flexslider.min.css">
<style>
    .flexslider {
        border: none;
        box-shadow: none;
        margin: 0;
    }

    .flexslider .slides img {
        width: 100%;
        /* height: 200px; */
        object-fit: cover;
        border-radius: 8px;
    }

    .flexslider .slides > li {
        margin-right: 5px;
    }
</style>

@section('content')
    <section class="hg-hero" style="background-image: url('{{ asset('assets/images/home/heroimage.png') }}');">
        <div class="hg-hero-overlay"></div>
        <div class="container hg-hero-inner text-center">
            <div class="hg-hero-badge"><i class="fas fa-shield-alt"></i> <span>Nigeria's trusted real estate marketplace</span></div>
            <h1 class="hg-hero-title">Find Property You Can Trust</h1>
            <p class="hg-hero-subtitle">Discover verified properties, connect with trusted real estate professionals, and make confident property decisions.</p>
            @include('partials.search-form')
        </div>
    </section>

    <section class="hg-section" id="property-categories" style="background: #F4F5F7">
        <div class="container">
            <div class="mb-4">
                <div class="hg-eyebrow">Browse by type</div>
                <h2 class="hg-section-title">What Kind of Property Are You After?</h2>
                <p class="hg-section-subtitle">Jump straight into the category that fits your search.</p>
            </div>
            <div class="row">
                @foreach($categories as $category)
                    <div class="col-6 col-md-4 col-lg-2 mb-3 mb-lg-0">
                        <a href="{{ route('properties.index', ['category' => $category->slug]) }}" class="hg-category-card">
                            <div class="hg-category-icon">{!! $category->icon_class !!}</div>
                            <h3>{{ $category->name }}</h3>
                            <p>{{ $category->description }}</p>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="hg-section bg-white" id="featured-properties">
        <div class="container">
            <div class="d-flex flex-column flex-md-row align-items-md-end justify-content-between mb-4">
                <div>
                    <div class="hg-eyebrow">Featured listings</div>
                    <h2 class="hg-section-title">Featured Properties</h2>
                    <p class="hg-section-subtitle">Explore some of the best properties available on Haygents.</p>
                </div>
                <a href="{{ route('properties.index') }}" class="btn hg-btn-outline mt-3 mt-md-0">View All Properties</a>
            </div>
            <div class="row">
                @foreach($featuredProperties as $property)
                    @include('partials.property-card', ['property' => $property, 'favoriteIds' => $favoriteIds])
                @endforeach
            </div>
        </div>
    </section>

    <section class="hg-section pt-2 bg-white" id="explore-cities">
        <div class="container">
            <div class="mb-4">
                <div class="hg-eyebrow">Popular locations</div>
                <h2 class="hg-section-title">Explore Property by City</h2>
                <p class="hg-section-subtitle">From busy city centres to quiet neighbourhoods. We show the area, never the exact address.</p>
            </div>
            <div class="row">
                @foreach($cities as $city)
                    <div class="col-12 col-sm-6 col-lg-3 mb-4 mb-lg-0">
                        <a href="{{ route('properties.index', ['city' => $city->name]) }}" class="hg-city-card">
                            <div class="hg-city-media">
                                <img src="{{ asset($city->image_path) }}" alt="{{ $city->name }}" loading="lazy">
                                <div class="hg-city-count-overlay">{{ $city->properties_count }} properties</div>
                            </div>
                            <h3 class="hg-city-name">{{ $city->name }}</h3>
                            <p class="hg-city-areas">{{ $city->neighborhoods }}</p>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="hg-section hg-section-alt" id="how-haygents-works">
        <div class="container">
            <div class="mb-4">
                <div class="hg-eyebrow">How Haygents works</div>
                <h2 class="hg-section-title">From Search to Keys, Our Team Beside You</h2>
                <p class="hg-section-subtitle">AGENT manages the steps that usually go wrong, so you never deal with strangers or unverified payments.</p>
            </div>
            <div class="row">
                <div class="col-12 col-md-6 col-lg-3 mb-4 mb-lg-0"><div class="hg-step-card"><div class="hg-step-num">1</div><h3>Search and Explore</h3><p>Find properties that match your budget, location, and preferences.</p></div></div>
                <div class="col-12 col-md-6 col-lg-3 mb-4 mb-lg-0"><div class="hg-step-card"><div class="hg-step-num">2</div><h3>Request an Inspection</h3><p>Schedule a property viewing with the relevant agent or property representative.</p></div></div>
                <div class="col-12 col-md-6 col-lg-3 mb-4 mb-md-0"><div class="hg-step-card"><div class="hg-step-num">3</div><h3>Inspect and Verify</h3><p>Get assistance with property inspections and verification.</p></div></div>
                <div class="col-12 col-md-6 col-lg-3"><div class="hg-step-card"><div class="hg-step-num">4</div><h3>Pay Safely and Move In</h3><p>Proceed with your transaction confidently using the appropriate verification and professional support processes.</p></div></div>
            </div>
        </div>
    </section>

    <section class="hg-section bg-white" id="property-verification">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-12 col-lg-6 pr-lg-5 mb-4 mb-lg-0">
                    <div class="hg-eyebrow">Verification and trust</div>
                    <h2 class="hg-section-title">We Show Exactly What Has Been Checked</h2>
                    <p class="hg-section-subtitle mb-3">No vague "verified" badges. Every listing shows each check separately, so you know where it stands before you visit.</p>
                    <ul class="hg-check-list">
                        <li class="hg-check-row"><span class="hg-check-left"><i class="fas fa-check-circle text-success"></i> Provider identity (Level 1 and 2 KYC)</span><span class="hg-status-badge">Completed</span></li>
                        <li class="hg-check-row"><span class="hg-check-left"><i class="fas fa-check-circle text-success"></i> Authority to list</span><span class="hg-status-badge">Completed</span></li>
                        <li class="hg-check-row"><span class="hg-check-left"><i class="fas fa-check-circle text-success"></i> Ownership and survey documents</span><span class="hg-status-badge">Completed</span></li>
                        <li class="hg-check-row"><span class="hg-check-left"><i class="fas fa-check-circle text-success"></i> Physical inspection by our team</span><span class="hg-status-badge">Completed</span></li>
                        <li class="hg-check-row"><span class="hg-check-left"><i class="fas fa-check-circle text-success"></i> Valuation</span><span class="hg-status-badge">Completed</span></li>
                        <li class="hg-check-row"><span class="hg-check-left"><i class="fas fa-check-circle text-success"></i> Final AGENT approval</span><span class="hg-status-badge">Completed</span></li>
                    </ul>
                    <p><small class="smallerCont">
                        Checks do not guarantee legal title. We recommend independent legal advice before you buy.
                    </small></p>
                    {{-- <a href="#verificationModal" data-toggle="modal" class="btn hg-btn-primary">Learn About Verification</a> --}}
                </div>
                <div class="col-12 col-lg-6">
                    <div class="hg-photo-frame"><img src="{{ asset('assets/images/home/verification.png') }}" alt="Real estate verification inspector"></div>
                </div>
            </div>
        </div>
    </section>

    <section class="hg-section hg-section-alt" id="verified-agents">
        <div class="container">
            <div class="d-flex justify-content-between align-items-end mb-4">
                <div><div class="hg-eyebrow">Verified agents</div>
                <h2 class="hg-section-title">Work With Verified Agents</h2>
                <p class="hg-section-subtitle">Agents on AGENT have passed identity checks and professional verification, including state licensing and SCUML registration.</p></div>
                <a href="{{ route('agents.index') }}" class="btn hg-btn-outline">View All Agents</a>
            </div>
            <div class="row">@each('partials.agent-card', $agents, 'agent')</div>
        </div>
    </section>

    <section class="hg-section hg-section-alt" id="developers-companies">
        <div class="container">
            <div class="d-flex justify-content-between align-items-end mb-4">
                <div><div class="hg-eyebrow">Property companies</div>
                <h2 class="hg-section-title">Real estate companies we work with</h2>
                <p class="hg-section-subtitle">We collaborate with trusted real estate companies and industry professionals.</p></div>
                <a href="{{ route('companies.index') }}" class="btn hg-btn-outline">View All Companies</a>
            </div>
            <div>
                <div class="flexslider">
                    <ul class="slides" style="background: #f4f5f8">
                        @php
                            $countSlider = 1;
                        @endphp
                        @forelse ($companies as $commm)
                        <li>
                            <img src="{{ asset($commm->logo_path) }}" alt="Slide {{ $countSlider }}">
                        </li>
                        @php
                            $countSlider++;
                        @endphp
                        @empty

                        @endforelse
                    </ul>
                </div>
            </div>
            {{-- <div class="row">@each('partials.company-card', $companies, 'company')</div> --}}
        </div>
    </section>

    <section class="hg-section" id="property-professionals">
        <div class="container">
            <div class="d-flex justify-content-between align-items-end mb-4">
                <div>
                    <div class="hg-eyebrow">Professional services</div>
                    <h2 class="hg-section-title">Property lawyers, surveyors and valuers</h2>
                    <p class="hg-section-subtitle">Get title searches, survey plans and valuations from verified professionals. AGENT confirms the scope and price with you.</p></div>
                <a href="{{ route('professionals.index') }}" class="btn hg-btn-outline">View All Professionals</a>
            </div>
            <div class="row">@each('partials.professional-card', $professionals, 'professional')</div>
        </div>
    </section>

    <section class="hg-dark-section" id="training-certification">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-12 col-lg-6 mb-4 mb-lg-0">
                    <h2 class="hg-section-title text-white mb-3">Training and Certification for Property Professionals</h2>
                    <p class="mb-4 text-white-50">Build your real estate knowledge, improve your professional skills, and access valuable training opportunities.</p>
                    <a href="{{ route('register') }}" class="btn btn-light font-weight-bold px-4 py-2">Enroll Now</a>
                </div>
                <div class="col-12 col-lg-6">
                    <div class="d-flex flex-column" style="gap: 0.75rem;">
                        <div class="hg-training-item"><i class="fas fa-graduation-cap"></i><span>Professional development courses</span></div>
                        <div class="hg-training-item"><i class="fas fa-chart-line"></i><span>Property investment training</span></div>
                        <div class="hg-training-item"><i class="fas fa-file-signature"></i><span>Real estate documentation</span></div>
                        <div class="hg-training-item"><i class="fas fa-building"></i><span>Property management training</span></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="hg-section bg-white">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-12 col-lg-5 mb-4 mb-lg-0">
                    <div class="hg-photo-frame"><img src="{{ asset('assets/images/buyorsell.png') }}" alt="Luxury residential property in Nigeria"></div>
                </div>
                <div class="col-12 col-lg-1 mb-4 mb-lg-0">
                </div>
                <div class="col-12 col-lg-6 pl-lg-5">
                    <div class="hg-eyebrow">DIASPORA SERVICES</div>
                    <h2 class="hg-section-title mb-3">Buy or Rent in Nigeria from Anywhere</h2>
                    <p class="hg-section-subtitle mb-4">Our team will be your eyes on the ground, from inspections to documents and payments.</p>
                    <ul class="list-unstyled mb-5">
                        <li class="mb-2"><i class="fas fa-check-circle text-success mr-2"></i>Inspections handled by our team, with photo and video updates.</li>
                        <li class="mb-2"><i class="fas fa-check-circle text-success mr-2"></i>Verified lawyers for title searches and documents.</li>
                        <li><i class="fas fa-check-circle text-success mr-2"></i>Payments made only against official instructions.</li>
                    </ul>
                    <a href="{{ route('properties.index') }}" class="btn hg-btn-primary px-4 py-2">View Available Properties</a>
                </div>
            </div>
        </div>
    </section>

    <section class="hg-section hg-section-alt" id="testimonials">
        <div class="container">
            <div class="mb-4">
                <div class="hg-eyebrow">Testimonials</div>
                <h2 class="hg-section-title">What Our Customers Say</h2>
            </div>
            @include('partials.testimonials', ['testimonials' => $testimonials])
        </div>
    </section>

    <section class="hg-section bg-white" id="faq-section">
        <div class="container">
            <div class="row">
                <div class="col-12 col-lg-5 pr-lg-5 mb-4 mb-lg-0">
                    <div class="hg-eyebrow">FAQ</div>
                    <h2 class="hg-section-title mb-3">Frequently Asked Questions</h2>
                    <p class="hg-section-subtitle mb-4">Still unsure? Reach out to our support team for guidance on listings, verification, and inspections.</p>
                </div>
                <div class="col-12 col-lg-7">
                    @include('partials.faq-accordion', ['faqs' => $faqs])
                </div>
            </div>
        </div>
    </section>

    <section class="hg-section pt-2 bg-white">
        <div class="container">
            <div class="row">
                <div class="col-12 col-lg-8 mb-4 mb-lg-0">
                    <div class="hg-app-banner">
                        <h2 class="h3 firaSemiBold text-white mb-2">Continue on the HAYGENTS App</h2>
                        <p class="mb-4">Discover properties, connect with professionals, and manage your property search wherever you go.</p>
                        <div class="d-flex flex-wrap" style="gap: 0.75rem;">
                            <a href="#" class="">
                                <img src="{{ asset('assets/images/google.png') }}" alt="">
                            </a>
                            <a href="#" class="">
                                <img src="{{ asset('assets/images/appstore.png') }}" alt="">
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-4">
                    <div class="hg-owner-banner d-flex flex-column justify-content-between">
                        <div>
                            <h3 class="h5 firaSemiBold mb-2">Own Property or Represent a Company?</h3>
                            <p class="small text-secondary mb-4">Join Haygents and connect with people searching for properties.</p>
                        </div>
                        <a href="{{ route('register') }}" class="btn hg-btn-navy btn-block">Register Now</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
