<footer class="hg-footer">
    <div class="container">
        <div class="row">
            <div class="col-12 col-lg-4 mb-4 mb-lg-0 pr-lg-5">
                <a class="hg-logo mb-3" href="{{ route('home') }}" style="color: #ffffff !important;">
                    <img src="{{ asset('assets/images/logowhite.png') }}" alt="">
                </a>
                <p class="mb-4">HAYGENTS connects property buyers, tenants, landlords, developers, agents, lawyers, surveyors, and valuers with transparent verification.</p>

                <div class="d-flex justify-content-start mt-4">
                    <div class="mr-3">
                        <a href="javascript:()">
                            <img src="{{ asset('assets/images/google.png') }}" class="appicon" alt="" >
                        </a>
                    </div>
                    <div>
                        <a href="javascript:()">
                            <img src="{{ asset('assets/images/appstore.png') }}" class="appicon" alt="">
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3 col-lg-2 mb-4 mb-md-0">
                <h6>Explore</h6>
                <ul class="hg-footer-links">
                    <li><a href="{{ route('properties.index', ['purpose' => 'sale']) }}">Buy Property</a></li>
                    <li><a href="{{ route('properties.index', ['purpose' => 'rent']) }}">Rent Property</a></li>
                    <li><a href="{{ route('properties.index', ['purpose' => 'shortlet']) }}">Shortlet</a></li>
                    <li><a href="{{ route('properties.index', ['category' => 'land-plots']) }}">Land &amp; Plots</a></li>
                    <li><a href="{{ route('properties.index', ['category' => 'commercial']) }}">Commercial Properties</a></li>
                </ul>
            </div>
            <div class="col-6 col-md-3 col-lg-2 mb-4 mb-md-0">
                <h6>For Professionals</h6>
                <ul class="hg-footer-links">
                    <li><a href="{{ route('agents.index') }}">Become an Agent</a></li>
                    <li><a href="{{ route('companies.index') }}">Register Your Company</a></li>
                    <li><a href="{{ route('professionals.index', ['category' => 'property-lawyers']) }}">Property Lawyers</a></li>
                    <li><a href="{{ route('professionals.index', ['category' => 'land-surveyors']) }}">Surveyors</a></li>
                    <li><a href="{{ route('professionals.index', ['category' => 'property-valuers']) }}">Valuers</a></li>
                </ul>
            </div>
            <div class="col-6 col-md-3 col-lg-2">
                <h6>Company</h6>
                <ul class="hg-footer-links">
                    <li><a href="{{ route('about') }}">About Us</a></li>
                    <li><a href="{{ route('contact') }}">Contact Us</a></li>
                    <li><a href="{{ route('home') }}#faq-section">FAQs</a></li>
                    <li><a href="#privacy">Privacy Policy</a></li>
                    <li><a href="#terms">Terms and Conditions</a></li>
                </ul>
            </div>
            <div class="col-6 col-md-3 col-lg-2">
                <h6>Support</h6>
                <ul class="hg-footer-links">
                    <li><a href="{{ route('home') }}#faq-section">Help Center</a></li>
                    <li><a href="{{ route('contact') }}">Contact Support</a></li>
                    <li><a href="{{ route('contact', ['topic' => 'Report a listing']) }}">Report a Property</a></li>
                    <li><a href="{{ route('about') }}#verification-process">Safety and Verification</a></li>
                </ul>
            </div>
        </div>
        <div class="hg-footer-bottom d-flex flex-column flex-md-row align-items-center justify-content-between">
            <div>&copy; {{ date('Y') }} HAYGENTS Real Estate Marketplace. All rights reserved.</div>
            <div class="d-flex align-items-center" style="gap: 1.15rem;">
                <a href="#" class="text-white-50"><i class="fab fa-facebook-f"></i></a>
                <a href="#" class="text-white-50"><i class="fab fa-instagram"></i></a>
                <a href="#" class="text-white-50"><i class="fab fa-linkedin-in"></i></a>
                <a href="#" class="text-white-50"><i class="fab fa-twitter"></i></a>
            </div>
        </div>
    </div>
</footer>
