<header class="sticky-top">
    <nav class="navbar navbar-expand-lg navbar-light hg-navbar" id="mainNavbar" aria-label="Main Navigation">
        <div class="container">
            <a class="navbar-brand hg-logo" href="{{ route('home') }}">
                {{-- <svg class="hg-logo-svg" viewBox="0 0 32 32" fill="none" aria-hidden="true">
                    <path d="M4 15.5L16 5.5L28 15.5" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M8.5 12.5V25.5H23.5V12.5" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M13.5 25.5V18.5H18.5V25.5" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <span>HAYGENTS</span> --}}
                <img src="{{ asset('assets/images/logo.png') }}" alt="">
            </a>

            <button class="navbar-toggler border-0" type="button" data-toggle="collapse" data-target="#haygentsNav" aria-controls="haygentsNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="haygentsNav">
                <ul class="navbar-nav mr-auto ml-lg-4 align-items-lg-center">
                    {{-- Buy Hover Mega Dropdown --}}
                    <li class="nav-item hg-mega-dropdown">
                        <a
                            class="nav-link hg-nav-link {{ request('purpose') === 'sale' ? 'active' : '' }}"
                            href="{{ route('properties.index', ['purpose' => 'sale']) }}"
                            data-mega-toggle="buy"
                            aria-haspopup="true"
                            aria-expanded="false"
                        >
                            <span>Buy</span>
                            <i class="fas fa-chevron-down hg-nav-chevron"></i>
                        </a>
                        <div class="hg-mega-panel-wrap">
                            <div class="hg-mega-card">
                                <div class="hg-mega-list">
                                    <a href="{{ route('properties.index', ['purpose' => 'sale', 'category' => 'houses']) }}" class="hg-mega-item">
                                        <span class="hg-mega-icon"><i class="fas fa-home"></i></span>
                                        <span>
                                            <span class="d-block hg-mega-title">Houses</span>
                                            <span class="d-block hg-mega-sub">Buy houses</span>
                                        </span>
                                    </a>
                                    <a href="{{ route('properties.index', ['purpose' => 'sale', 'category' => 'flats-and-apartment']) }}" class="hg-mega-item">
                                        <span class="hg-mega-icon"><i class="far fa-building"></i></span>
                                        <span>
                                            <span class="d-block hg-mega-title">Flat / Apartments</span>
                                            <span class="d-block hg-mega-sub">Buy flats or apartments</span>
                                        </span>
                                    </a>
                                    <a href="{{ route('properties.index', ['purpose' => 'sale', 'category' => 'land']) }}" class="hg-mega-item">
                                        <span class="hg-mega-icon"><i class="fas fa-vector-square"></i></span>
                                        <span>
                                            <span class="d-block hg-mega-title">Land</span>
                                            <span class="d-block hg-mega-sub">Buy lands</span>
                                        </span>
                                    </a>
                                    <a href="{{ route('properties.index', ['purpose' => 'sale', 'category' => 'commercial']) }}" class="hg-mega-item">
                                        <span class="hg-mega-icon"><i class="fas fa-city"></i></span>
                                        <span>
                                            <span class="d-block hg-mega-title">Commercial Property</span>
                                            <span class="d-block hg-mega-sub">Buy commercial properties</span>
                                        </span>
                                    </a>
                                </div>
                                <div class="hg-mega-locations">
                                    <div class="hg-mega-loc-heading">Browse by location</div>
                                    <div class="row no-gutters">
                                        <div class="col-6"><a href="{{ route('properties.index', ['purpose' => 'sale', 'state' => 'FCT']) }}" class="hg-mega-loc-link">Abuja</a></div>
                                        <div class="col-6"><a href="{{ route('properties.index', ['purpose' => 'sale', 'state' => 'Lagos']) }}" class="hg-mega-loc-link">Lagos</a></div>
                                        <div class="col-6"><a href="{{ route('properties.index', ['purpose' => 'sale', 'state' => 'Ogun']) }}" class="hg-mega-loc-link">Ogun</a></div>
                                        <div class="col-6"><a href="{{ route('properties.index', ['purpose' => 'sale', 'state' => 'Rivers']) }}" class="hg-mega-loc-link">Rivers</a></div>
                                    </div>
                                    <a href="{{ route('properties.index', ['purpose' => 'sale']) }}" class="hg-mega-cta">View all properties for sale</a>
                                </div>
                            </div>
                        </div>
                    </li>

                    {{-- Rent Hover Mega Dropdown --}}
                    <li class="nav-item hg-mega-dropdown">
                        <a
                            class="nav-link hg-nav-link {{ request('purpose') === 'rent' ? 'active' : '' }}"
                            href="{{ route('properties.index', ['purpose' => 'rent']) }}"
                            data-mega-toggle="rent"
                            aria-haspopup="true"
                            aria-expanded="false"
                        >
                            <span>Rent</span>
                            <i class="fas fa-chevron-down hg-nav-chevron"></i>
                        </a>
                        <div class="hg-mega-panel-wrap">
                            <div class="hg-mega-card">
                                <div class="hg-mega-list">
                                    <a href="{{ route('properties.index', ['purpose' => 'rent', 'category' => 'houses']) }}" class="hg-mega-item">
                                        <span class="hg-mega-icon"><i class="fas fa-home"></i></span>
                                        <span>
                                            <span class="d-block hg-mega-title">Houses</span>
                                            <span class="d-block hg-mega-sub">Rent houses</span>
                                        </span>
                                    </a>
                                    <a href="{{ route('properties.index', ['purpose' => 'rent', 'category' => 'flats-and-apartment']) }}" class="hg-mega-item">
                                        <span class="hg-mega-icon"><i class="far fa-building"></i></span>
                                        <span>
                                            <span class="d-block hg-mega-title">Flat / Apartments</span>
                                            <span class="d-block hg-mega-sub">Rent flats or apartments</span>
                                        </span>
                                    </a>
                                    <a href="{{ route('properties.index', ['purpose' => 'rent', 'category' => 'land']) }}" class="hg-mega-item">
                                        <span class="hg-mega-icon"><i class="fas fa-vector-square"></i></span>
                                        <span>
                                            <span class="d-block hg-mega-title">Land</span>
                                            <span class="d-block hg-mega-sub">Rent lands</span>
                                        </span>
                                    </a>
                                    <a href="{{ route('properties.index', ['purpose' => 'rent', 'category' => 'commercial']) }}" class="hg-mega-item">
                                        <span class="hg-mega-icon"><i class="fas fa-city"></i></span>
                                        <span>
                                            <span class="d-block hg-mega-title">Commercial Property</span>
                                            <span class="d-block hg-mega-sub">Rent commercial properties</span>
                                        </span>
                                    </a>
                                </div>
                                <div class="hg-mega-locations">
                                    <div class="hg-mega-loc-heading">Browse by location</div>
                                    <div class="row no-gutters">
                                        <div class="col-6"><a href="{{ route('properties.index', ['purpose' => 'rent', 'state' => 'FCT']) }}" class="hg-mega-loc-link">Abuja</a></div>
                                        <div class="col-6"><a href="{{ route('properties.index', ['purpose' => 'rent', 'state' => 'Lagos']) }}" class="hg-mega-loc-link">Lagos</a></div>
                                        <div class="col-6"><a href="{{ route('properties.index', ['purpose' => 'rent', 'state' => 'Ogun']) }}" class="hg-mega-loc-link">Ogun</a></div>
                                        <div class="col-6"><a href="{{ route('properties.index', ['purpose' => 'rent', 'state' => 'Rivers']) }}" class="hg-mega-loc-link">Rivers</a></div>
                                    </div>
                                    <a href="{{ route('properties.index', ['purpose' => 'rent']) }}" class="hg-mega-cta">View all properties for rent</a>
                                </div>
                            </div>
                        </div>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link hg-nav-link {{ request('purpose') === 'shortlet' ? 'active' : '' }}" href="{{ route('properties.index', ['purpose' => 'shortlet']) }}">Shortlets</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link hg-nav-link {{ request()->routeIs('professionals.*', 'agents.*', 'companies.*') ? 'active' : '' }}" href="{{ route('professionals.index') }}">Find a Professional</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link hg-nav-link {{ request()->routeIs('about') ? 'active' : '' }}" href="{{ route('about') }}">How It Works</a>
                    </li>
                </ul>

                <div class="d-flex flex-column flex-lg-row align-items-stretch align-items-lg-center mt-3 mt-lg-0">
                    @auth
                        <div class="dropdown mr-lg-3 mb-2 mb-lg-0">
                            <button class="btn hg-btn-outline btn-sm dropdown-toggle" type="button" id="userNavDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-user-circle mr-1 text-primary"></i> {{ auth()->user()->name }}
                            </button>
                            <div class="dropdown-menu dropdown-menu-right shadow-sm border" aria-labelledby="userNavDropdown">
                                <a class="dropdown-item" href="{{ route('properties.index') }}">Browse Properties</a>
                                @if(Route::has('logout'))
                                    <form action="{{ route('logout') }}" method="POST" class="m-0">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">Log Out</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endauth

                    <a href="{{ auth()->check() ? '#listPropertyModal' : (Route::has('login') ? route('login') : '#listPropertyModal') }}" @auth data-toggle="modal" @endauth class="hg-nav-list-link mb-3 mb-lg-0">
                        List your property
                    </a>

                    <span class="hg-nav-divider d-none d-lg-inline-block" aria-hidden="true"></span>

                    <div class="d-flex flex-wrap align-items-center" style="gap: 0.5rem;">
                        <a href="#app-store" class="hg-nav-store-btn">
                            <i class="fab fa-apple"></i>
                            <span class="text-left">
                                <span class="hg-nav-store-sub">Download on the</span>
                                <span class="hg-nav-store-name">App Store</span>
                            </span>
                        </a>
                        <a href="#google-play" class="hg-nav-store-btn">
                            <i class="fab fa-google-play"></i>
                            <span class="text-left">
                                <span class="hg-nav-store-sub">Get it on</span>
                                <span class="hg-nav-store-name">Google Play</span>
                            </span>
                        </a>
                    </div>

                    @guest
                        @if(Route::has('login'))
                            <div class="d-flex d-lg-none align-items-center mt-3 pt-3 border-top" style="gap: 0.6rem;">
                                <a href="{{ route('login') }}" class="btn btn-sm hg-btn-outline flex-fill">Login</a>
                                @if(Route::has('register'))
                                    <a href="{{ route('register') }}" class="btn btn-sm hg-btn-primary flex-fill">Sign Up</a>
                                @endif
                            </div>
                        @endif
                    @endguest
                </div>
            </div>
        </div>
    </nav>
</header>
