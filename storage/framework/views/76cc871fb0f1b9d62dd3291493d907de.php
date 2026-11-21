<header class="sticky-top">
    <nav class="navbar navbar-expand-lg navbar-light hg-navbar" id="mainNavbar" aria-label="Main Navigation">
        <div class="container">
            <a class="navbar-brand hg-logo" href="<?php echo e(route('home')); ?>">
                
                <img src="<?php echo e(asset('assets/images/logo.png')); ?>" alt="">
            </a>

            <button class="navbar-toggler border-0" type="button" data-toggle="collapse" data-target="#haygentsNav" aria-controls="haygentsNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="haygentsNav">
                <ul class="navbar-nav mr-auto ml-lg-4 align-items-lg-center">
                    
                    <li class="nav-item hg-mega-dropdown">
                        <a
                            class="nav-link hg-nav-link <?php echo e(request('purpose') === 'sale' ? 'active' : ''); ?>"
                            href="<?php echo e(route('properties.index', ['purpose' => 'sale'])); ?>"
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
                                    <a href="<?php echo e(route('properties.index', ['purpose' => 'sale', 'category' => 'houses'])); ?>" class="hg-mega-item">
                                        <span class="hg-mega-icon"><i class="fas fa-home"></i></span>
                                        <span>
                                            <span class="d-block hg-mega-title">Houses</span>
                                            <span class="d-block hg-mega-sub">Buy houses</span>
                                        </span>
                                    </a>
                                    <a href="<?php echo e(route('properties.index', ['purpose' => 'sale', 'category' => 'flats-and-apartment'])); ?>" class="hg-mega-item">
                                        <span class="hg-mega-icon"><i class="far fa-building"></i></span>
                                        <span>
                                            <span class="d-block hg-mega-title">Flat / Apartments</span>
                                            <span class="d-block hg-mega-sub">Buy flats or apartments</span>
                                        </span>
                                    </a>
                                    <a href="<?php echo e(route('properties.index', ['purpose' => 'sale', 'category' => 'land'])); ?>" class="hg-mega-item">
                                        <span class="hg-mega-icon"><i class="fas fa-vector-square"></i></span>
                                        <span>
                                            <span class="d-block hg-mega-title">Land</span>
                                            <span class="d-block hg-mega-sub">Buy lands</span>
                                        </span>
                                    </a>
                                    <a href="<?php echo e(route('properties.index', ['purpose' => 'sale', 'category' => 'commercial'])); ?>" class="hg-mega-item">
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
                                        <div class="col-6"><a href="<?php echo e(route('properties.index', ['purpose' => 'sale', 'state' => 'FCT'])); ?>" class="hg-mega-loc-link">Abuja</a></div>
                                        <div class="col-6"><a href="<?php echo e(route('properties.index', ['purpose' => 'sale', 'state' => 'Lagos'])); ?>" class="hg-mega-loc-link">Lagos</a></div>
                                        <div class="col-6"><a href="<?php echo e(route('properties.index', ['purpose' => 'sale', 'state' => 'Ogun'])); ?>" class="hg-mega-loc-link">Ogun</a></div>
                                        <div class="col-6"><a href="<?php echo e(route('properties.index', ['purpose' => 'sale', 'state' => 'Rivers'])); ?>" class="hg-mega-loc-link">Rivers</a></div>
                                    </div>
                                    <a href="<?php echo e(route('properties.index', ['purpose' => 'sale'])); ?>" class="hg-mega-cta">View all properties for sale</a>
                                </div>
                            </div>
                        </div>
                    </li>

                    
                    <li class="nav-item hg-mega-dropdown">
                        <a
                            class="nav-link hg-nav-link <?php echo e(request('purpose') === 'rent' ? 'active' : ''); ?>"
                            href="<?php echo e(route('properties.index', ['purpose' => 'rent'])); ?>"
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
                                    <a href="<?php echo e(route('properties.index', ['purpose' => 'rent', 'category' => 'houses'])); ?>" class="hg-mega-item">
                                        <span class="hg-mega-icon"><i class="fas fa-home"></i></span>
                                        <span>
                                            <span class="d-block hg-mega-title">Houses</span>
                                            <span class="d-block hg-mega-sub">Rent houses</span>
                                        </span>
                                    </a>
                                    <a href="<?php echo e(route('properties.index', ['purpose' => 'rent', 'category' => 'flats-and-apartment'])); ?>" class="hg-mega-item">
                                        <span class="hg-mega-icon"><i class="far fa-building"></i></span>
                                        <span>
                                            <span class="d-block hg-mega-title">Flat / Apartments</span>
                                            <span class="d-block hg-mega-sub">Rent flats or apartments</span>
                                        </span>
                                    </a>
                                    <a href="<?php echo e(route('properties.index', ['purpose' => 'rent', 'category' => 'land'])); ?>" class="hg-mega-item">
                                        <span class="hg-mega-icon"><i class="fas fa-vector-square"></i></span>
                                        <span>
                                            <span class="d-block hg-mega-title">Land</span>
                                            <span class="d-block hg-mega-sub">Rent lands</span>
                                        </span>
                                    </a>
                                    <a href="<?php echo e(route('properties.index', ['purpose' => 'rent', 'category' => 'commercial'])); ?>" class="hg-mega-item">
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
                                        <div class="col-6"><a href="<?php echo e(route('properties.index', ['purpose' => 'rent', 'state' => 'FCT'])); ?>" class="hg-mega-loc-link">Abuja</a></div>
                                        <div class="col-6"><a href="<?php echo e(route('properties.index', ['purpose' => 'rent', 'state' => 'Lagos'])); ?>" class="hg-mega-loc-link">Lagos</a></div>
                                        <div class="col-6"><a href="<?php echo e(route('properties.index', ['purpose' => 'rent', 'state' => 'Ogun'])); ?>" class="hg-mega-loc-link">Ogun</a></div>
                                        <div class="col-6"><a href="<?php echo e(route('properties.index', ['purpose' => 'rent', 'state' => 'Rivers'])); ?>" class="hg-mega-loc-link">Rivers</a></div>
                                    </div>
                                    <a href="<?php echo e(route('properties.index', ['purpose' => 'rent'])); ?>" class="hg-mega-cta">View all properties for rent</a>
                                </div>
                            </div>
                        </div>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link hg-nav-link <?php echo e(request('purpose') === 'shortlet' ? 'active' : ''); ?>" href="<?php echo e(route('properties.index', ['purpose' => 'shortlet'])); ?>">Shortlets</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link hg-nav-link <?php echo e(request()->routeIs('professionals.*', 'agents.*', 'companies.*') ? 'active' : ''); ?>" href="<?php echo e(route('professionals.index')); ?>">Find a Professional</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link hg-nav-link <?php echo e(request()->routeIs('about') ? 'active' : ''); ?>" href="<?php echo e(route('about')); ?>">How It Works</a>
                    </li>
                </ul>

                <div class="d-flex flex-column flex-lg-row align-items-stretch align-items-lg-center mt-3 mt-lg-0">
                    <?php if(auth()->guard()->check()): ?>
                        <div class="dropdown mr-lg-3 mb-2 mb-lg-0">
                            <button class="btn hg-btn-outline btn-sm dropdown-toggle" type="button" id="userNavDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-user-circle mr-1 text-primary"></i> <?php echo e(auth()->user()->name); ?>

                            </button>
                            <div class="dropdown-menu dropdown-menu-right shadow-sm border" aria-labelledby="userNavDropdown">
                                <a class="dropdown-item" href="<?php echo e(route('properties.index')); ?>">Browse Properties</a>
                                <?php if(Route::has('logout')): ?>
                                    <form action="<?php echo e(route('logout')); ?>" method="POST" class="m-0">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="dropdown-item text-danger">Log Out</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <a href="<?php echo e(auth()->check() ? '#listPropertyModal' : (Route::has('login') ? route('login') : '#listPropertyModal')); ?>" <?php if(auth()->guard()->check()): ?> data-toggle="modal" <?php endif; ?> class="hg-nav-list-link mb-3 mb-lg-0">
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

                    <?php if(auth()->guard()->guest()): ?>
                        <?php if(Route::has('login')): ?>
                            <div class="d-flex d-lg-none align-items-center mt-3 pt-3 border-top" style="gap: 0.6rem;">
                                <a href="<?php echo e(route('login')); ?>" class="btn btn-sm hg-btn-outline flex-fill">Login</a>
                                <?php if(Route::has('register')): ?>
                                    <a href="<?php echo e(route('register')); ?>" class="btn btn-sm hg-btn-primary flex-fill">Sign Up</a>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
</header>
<?php /**PATH C:\Laravel\haygents\resources\views/partials/navbar.blade.php ENDPATH**/ ?>