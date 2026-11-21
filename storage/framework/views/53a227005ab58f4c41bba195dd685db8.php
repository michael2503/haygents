<?php $__env->startSection('title', $professional->name . ' — Verified ' . $professional->category . ' | HAYGENTS'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $firstName = explode(' ', trim($professional->name))[0];
    $isLawyer = $professional->category_slug === 'property-lawyers' || str_contains(strtolower($professional->category), 'lawyer');
    $isSurveyor = $professional->category_slug === 'land-surveyors' || str_contains(strtolower($professional->category), 'surveyor');

    $profileId = $professional->slug === 'chidinma-nwosu'
        ? 'HGT-PL-0377'
        : ($isLawyer ? 'HGT-PL-0' . (370 + $professional->id) : ($isSurveyor ? 'HGT-LS-0' . (410 + $professional->id) : 'HGT-PV-0' . (520 + $professional->id)));

    $tags = $isLawyer
        ? ['Title search', 'Conveyancing', "Governor's consent"]
        : ($isSurveyor ? ['Survey plan', 'Boundary survey', 'Charting check'] : ['Valuation report', 'Property inspection', 'Rent review']);

    $jobsCompleted = $professional->slug === 'chidinma-nwosu' ? 57 : ($professional->slug === 'bola-akande' ? 71 : 42);
    $admittedLabel = $isLawyer ? 'Called to Bar' : ($isSurveyor ? 'SURCON registered' : 'ESVARBON licensed');
    $admittedYear = 2026 - (int) $professional->years_exp;
    $sealLabel = $isLawyer ? 'NBA seal' : ($isSurveyor ? 'SURCON seal' : 'ESVARBON seal');
    $practiceType = $isLawyer ? 'Law firm partner' : ($isSurveyor ? 'Registered survey practice' : 'Principal valuation firm');
    $turnaround = $isSurveyor ? 'From 3 working days' : 'From 5 working days';
    $statesCovered = $professional->state === 'Lagos' ? 'Lagos, Ogun' : $professional->state . ', Abuja (FCT)';

    $serviceGroups = $isLawyer
        ? [
            ['title' => 'Title checks', 'items' => ['Title search at the land registry', 'Survey plan check', 'Litigation and charge search', 'Written title report']],
            ['title' => 'Documents', 'items' => ['Deed of assignment', 'Contract of sale', 'Lease agreements', 'Power of attorney']],
            ['title' => 'Consent and registration', 'items' => ["Governor's consent", 'Registration of title', 'Perfection of documents', 'Certified true copies']],
            ['title' => 'Advice', 'items' => ['Off-plan contract review', 'Advice before you pay', 'Family land and estates', 'Dispute referral']],
        ]
        : ($isSurveyor
            ? [
                ['title' => 'Land charting & verification', 'items' => ['Registry charting check', 'Acquisition & excision check', 'Red Copy confirmation', 'Coordinate verification']],
                ['title' => 'Field surveys', 'items' => ['Perimeter boundary survey', 'Beacon placement & re-establishment', 'Topographic layout survey', 'Subdivision survey']],
                ['title' => 'Documentation', 'items' => ['SURCON-sealed survey plan', 'Composite site plan', 'Deed plan preparation', 'GIS coordinate report']],
                ['title' => 'Advisory', 'items' => ['Pre-purchase site visit', 'Right-of-way setback audit', 'Estate layout demarcation', 'Dispute boundary report']],
            ]
            : [
                ['title' => 'Valuation reports', 'items' => ['Open market valuation', 'Forced sale & mortgage appraisal', 'Insurance replacement value', 'Probate & asset valuation']],
                ['title' => 'Commercial & rental', 'items' => ['Fair rental value review', 'Commercial yield analysis', 'Service charge audit', 'Lease renewal assessment']],
                ['title' => 'Technical due diligence', 'items' => ['Pre-purchase condition check', 'Bill of quantities review', 'Completion certificate check', 'Defect snags report']],
                ['title' => 'Advisory', 'items' => ['Portfolio advisory', 'Development feasibility study', 'Diaspora investment audit', 'Compulsory acquisition claim']],
            ]);

    $verificationRows = [
        'Identity verified (Level 1)',
        $isLawyer ? 'Call to Bar certificate confirmed' : ($isSurveyor ? 'SURCON registration confirmed' : 'ESVARBON licence confirmed'),
        $sealLabel . ' confirmed',
        'Practice address confirmed',
        'Professional references checked',
    ];

    $reviews = [
        ['author' => 'Olumide F.', 'context' => 'Title search in ' . $professional->city, 'date' => 'Oct 2026', 'quote' => 'The report was clear and arrived in 4 days. It stopped us buying land with a problem title.'],
        ['author' => 'Grace E.', 'context' => 'Documentation in Lekki', 'date' => 'Sep 2026', 'quote' => 'Explained every clause clearly, and the documents were ready on time.'],
        ['author' => 'Yusuf B.', 'context' => "Verification in " . $professional->city, 'date' => 'Jul 2026', 'quote' => 'Kept us updated in the HAYGENTS app at every stage.'],
    ];
?>

<section class="pt-4 pb-5 bg-white">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent px-0 py-0 mb-3 small">
                <li class="breadcrumb-item"><a href="<?php echo e(route('home')); ?>">Home</a></li>
                <li class="breadcrumb-item"><a href="<?php echo e(route('professionals.index')); ?>">Find a Professional</a></li>
                <li class="breadcrumb-item"><a href="<?php echo e(route('professionals.index', ['category' => $professional->category_slug])); ?>">Lawyers, surveyors and valuers</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?php echo e($professional->name); ?></li>
            </ol>
        </nav>

        <!-- Profile Hero Header -->
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-3 pb-1">
            <div class="d-flex align-items-center mb-3 mb-md-0" style="gap: 1.15rem;">
                <div class="hg-prof-hero-avatar"><?php echo e($professional->initials); ?></div>
                <div>
                    <div class="small firaMedium text-primary mb-1">Verified <?php echo e(strtolower($professional->category)); ?></div>
                    <h1 class="h2 firaSemiBold mb-1" style="color: var(--hg-navy);"><?php echo e($professional->name); ?></h1>
                    <div class="small text-muted d-flex flex-wrap align-items-center" style="gap: 0.55rem;">
                        <span><i class="fas fa-map-marker-alt mr-1"></i> <?php echo e($professional->city); ?>, <?php echo e($professional->state); ?></span>
                        <span>&middot;</span>
                        <span class="tabular-nums">Profile ID: <strong><?php echo e($profileId); ?></strong></span>
                        <?php if($professional->verification_status === 'verified'): ?>
                            <span class="hg-verified-pill"><i class="fas fa-check-circle"></i> Verified</span>
                        <?php else: ?>
                            <span class="hg-pending-pill"><i class="fas fa-clock"></i> Pending</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="d-flex align-items-center" style="gap: 0.6rem;">
                <a href="<?php echo e(route('contact', ['topic' => 'Share / Enquire: ' . $professional->name, 'ref' => $profileId])); ?>" class="btn btn-sm hg-btn-outline px-3">
                    <i class="fas fa-share-alt mr-1"></i> Share
                </a>
                <a href="#request-service" class="btn btn-sm hg-btn-outline px-3">
                    <i class="far fa-heart mr-1"></i> Save
                </a>
            </div>
        </div>

        <div class="d-flex flex-wrap mb-4" style="gap: 0.45rem;">
            <?php $__currentLoopData = $tags; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <span class="hg-dir-chip"><?php echo e($tag); ?></span>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <div class="row">
            <div class="col-12 col-lg-8 mb-4 mb-lg-0">
                <!-- 6-Box KPI Strip -->
                <div class="row mb-4">
                    <div class="col-6 col-sm-4 col-md-2 mb-3 mb-md-0 px-2">
                        <div class="hg-prof-kpi-box">
                            <div class="small text-muted mb-1"><i class="fas fa-briefcase"></i></div>
                            <div class="firaSemiBold tabular-nums" style="color: var(--hg-navy);"><?php echo e($professional->years_exp); ?> years</div>
                            <div class="text-muted" style="font-size: 0.72rem;"><?php echo e($admittedLabel); ?></div>
                        </div>
                    </div>
                    <div class="col-6 col-sm-4 col-md-2 mb-3 mb-md-0 px-2">
                        <div class="hg-prof-kpi-box">
                            <div class="small text-muted mb-1"><i class="far fa-check-circle"></i></div>
                            <div class="firaSemiBold tabular-nums" style="color: var(--hg-navy);"><?php echo e($jobsCompleted); ?></div>
                            <div class="text-muted" style="font-size: 0.72rem;">Completed</div>
                        </div>
                    </div>
                    <div class="col-6 col-sm-4 col-md-2 mb-3 mb-md-0 px-2">
                        <div class="hg-prof-kpi-box">
                            <div class="small text-muted mb-1"><i class="far fa-calendar"></i></div>
                            <div class="firaSemiBold tabular-nums" style="color: var(--hg-navy);">5 days</div>
                            <div class="text-muted" style="font-size: 0.72rem;">Turnaround</div>
                        </div>
                    </div>
                    <div class="col-6 col-sm-4 col-md-2 mb-3 mb-sm-0 px-2">
                        <div class="hg-prof-kpi-box">
                            <div class="small text-muted mb-1"><i class="far fa-star"></i></div>
                            <div class="firaSemiBold tabular-nums" style="color: var(--hg-navy);">4.9</div>
                            <div class="text-muted" style="font-size: 0.72rem;">Customer rating</div>
                        </div>
                    </div>
                    <div class="col-6 col-sm-4 col-md-2 mb-3 mb-sm-0 px-2">
                        <div class="hg-prof-kpi-box">
                            <div class="small text-muted mb-1"><i class="fas fa-map-marker-alt"></i></div>
                            <div class="firaSemiBold tabular-nums" style="color: var(--hg-navy);">2 states</div>
                            <div class="text-muted" style="font-size: 0.72rem;">States covered</div>
                        </div>
                    </div>
                    <div class="col-6 col-sm-4 col-md-2 px-2">
                        <div class="hg-prof-kpi-box">
                            <div class="small text-muted mb-1"><i class="fas fa-shield-alt"></i></div>
                            <div class="firaSemiBold tabular-nums" style="color: var(--hg-navy);">2026</div>
                            <div class="text-muted" style="font-size: 0.72rem;"><?php echo e($sealLabel); ?></div>
                        </div>
                    </div>
                </div>

                <!-- About -->
                <div class="pb-4 mb-4 border-bottom">
                    <h2 class="h5 firaSemiBold mb-2" style="color: var(--hg-navy);">About <?php echo e($firstName); ?></h2>
                    <p class="text-secondary mb-2" style="line-height: 1.7;">
                        <?php echo e($professional->name); ?> is a <?php echo e(strtolower($professional->category)); ?> in <?php echo e($professional->city); ?> with <?php echo e($professional->years_exp); ?> years of experience. <?php echo e($professional->description); ?> You request services through HAYGENTS, and our team confirms the scope and price with you before any work starts.
                    </p>
                    <div id="profBioExtra" class="d-none text-secondary small mb-2" style="line-height: 1.65;">
                        All inspections, registry filings, and document reviews are tracked end-to-end inside HAYGENTS so buyers and landlords receive transparent milestone confirmations before any closing funds move.
                    </div>
                    <button type="button" class="btn btn-link p-0 small firaMedium text-dark" data-action="toggle-prof-bio">
                        Read more
                    </button>
                </div>

                <!-- Professional details grid -->
                <div class="pb-4 mb-4 border-bottom">
                    <h2 class="h5 firaSemiBold mb-3" style="color: var(--hg-navy);">Professional details</h2>
                    <div class="row">
                        <div class="col-12 col-md-6 pr-md-4">
                            <div class="hg-details-grid-row"><span class="text-muted">Profession</span><span class="firaMedium" style="color: var(--hg-navy);"><?php echo e($professional->category); ?></span></div>
                            <div class="hg-details-grid-row"><span class="text-muted"><?php echo e($sealLabel); ?></span><span class="firaMedium" style="color: var(--hg-navy);">Verified for 2026</span></div>
                            <div class="hg-details-grid-row"><span class="text-muted">Service mode</span><span class="firaMedium" style="color: var(--hg-navy);">On-site and remote</span></div>
                            <div class="hg-details-grid-row"><span class="text-muted">Turnaround</span><span class="firaMedium" style="color: var(--hg-navy);"><?php echo e($turnaround); ?></span></div>
                        </div>
                        <div class="col-12 col-md-6 pl-md-4">
                            <div class="hg-details-grid-row"><span class="text-muted"><?php echo e($admittedLabel); ?></span><span class="firaMedium tabular-nums" style="color: var(--hg-navy);"><?php echo e($admittedYear); ?></span></div>
                            <div class="hg-details-grid-row"><span class="text-muted">Practice</span><span class="firaMedium" style="color: var(--hg-navy);"><?php echo e($practiceType); ?></span></div>
                            <div class="hg-details-grid-row"><span class="text-muted">States covered</span><span class="firaMedium" style="color: var(--hg-navy);"><?php echo e($statesCovered); ?></span></div>
                            <div class="hg-details-grid-row"><span class="text-muted">Verified since</span><span class="firaMedium" style="color: var(--hg-navy);">February 2024</span></div>
                        </div>
                    </div>
                </div>

                <!-- Services -->
                <div class="pb-4 mb-4 border-bottom">
                    <h2 class="h5 firaSemiBold mb-3" style="color: var(--hg-navy);">Services</h2>
                    <div class="row">
                        <?php $__currentLoopData = $serviceGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $grp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="col-12 col-sm-6 mb-3">
                                <h3 class="small firaMedium mb-2" style="color: var(--hg-navy);"><?php echo e($grp['title']); ?></h3>
                                <ul class="list-unstyled small text-secondary mb-0">
                                    <?php $__currentLoopData = $grp['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li class="mb-2"><i class="fas fa-check text-success mr-2"></i><?php echo e($item); ?></li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>

                <!-- How it works through HAYGENTS -->
                <div class="pb-4 mb-4 border-bottom">
                    <h2 class="h5 firaSemiBold mb-3" style="color: var(--hg-navy);">How it works through HAYGENTS</h2>
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <div class="hg-process-step-row"><span class="hg-process-step-icon"><i class="fas fa-mobile-alt"></i></span><span>Request the service in the HAYGENTS app</span></div>
                            <div class="hg-process-step-row"><span class="hg-process-step-icon"><i class="far fa-user"></i></span><span><?php echo e($firstName); ?> is assigned to your job</span></div>
                            <div class="hg-process-step-row"><span class="hg-process-step-icon"><i class="fas fa-shield-alt"></i></span><span>Pay only through HAYGENTS</span></div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="hg-process-step-row"><span class="hg-process-step-icon"><i class="far fa-check-circle"></i></span><span>HAYGENTS confirms the scope and price</span></div>
                            <div class="hg-process-step-row"><span class="hg-process-step-icon"><i class="far fa-clock"></i></span><span>Follow progress in the app</span></div>
                            <div class="hg-process-step-row"><span class="hg-process-step-icon"><i class="far fa-file-alt"></i></span><span>Get your report in the app</span></div>
                        </div>
                    </div>
                </div>

                <!-- Verification summary -->
                <div class="pb-4 mb-4 border-bottom">
                    <h2 class="h5 firaSemiBold mb-1" style="color: var(--hg-navy);">Verification summary</h2>
                    <p class="small text-muted mb-2">What HAYGENTS has checked for this professional.</p>
                    <ul class="hg-check-list mb-2">
                        <?php $__currentLoopData = $verificationRows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rowLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="hg-check-row">
                                <span class="hg-check-left"><i class="fas fa-check-circle text-success"></i><span><?php echo e($rowLabel); ?></span></span>
                                <span class="hg-status-badge text-success">Completed</span>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                    <p class="small text-muted mb-0">Only professionals who pass Level 1 and Level 2 checks show the Verified badge.</p>
                </div>

                <!-- Customer reviews -->
                <div class="pb-4 mb-4">
                    <h2 class="h5 firaSemiBold mb-1" style="color: var(--hg-navy);">Customer reviews</h2>
                    <p class="small text-muted mb-3">4.9 out of 5 from 31 HAYGENTS customers. These reviews are samples.</p>
                    <?php $__currentLoopData = $reviews; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rev): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="hg-review-card">
                            <div class="small text-muted mb-1">
                                <span class="firaMedium" style="color: var(--hg-navy);"><?php echo e($rev['author']); ?></span>
                                &nbsp;&middot;&nbsp; <?php echo e($rev['context']); ?>

                                &nbsp;&middot;&nbsp; <?php echo e($rev['date']); ?>

                                &nbsp;&middot;&nbsp; 5 out of 5
                            </div>
                            <p class="small text-secondary mb-0"><?php echo e($rev['quote']); ?></p>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

                <div class="p-3 rounded bg-light border" style="border-radius: 12px;">
                    <div class="small firaMedium mb-1" style="color: var(--hg-navy);">Good to know</div>
                    <div class="small text-secondary">Contact details are private. Never pay a professional directly: every payment goes through the HAYGENTS app.</div>
                </div>
            </div>

            <!-- Right Sticky Request CTA -->
            <div class="col-12 col-lg-4" id="request-service">
                <div class="hg-sticky-cta-card">
                    <div class="small text-muted mb-1">Request a service from</div>
                    <h2 class="h4 font-weight-bold mb-2" style="color: var(--hg-navy);"><?php echo e($professional->name); ?></h2>
                    <p class="small text-secondary mb-4" style="line-height: 1.55;">
                        Choose a service in the HAYGENTS app. HAYGENTS confirms the scope and price with you before any work starts, and you get the report in the app. Contact details are never shared.
                    </p>
                    <a
                        href="<?php echo e(route('contact', ['topic' => 'Request Service: ' . $professional->name, 'ref' => $profileId])); ?>"
                        class="btn hg-btn-primary btn-block py-2 mb-3"
                    >
                        Request Service
                    </a>
                    <div class="small text-muted d-flex align-items-center" style="font-size: 0.78rem;">
                        <i class="fas fa-mobile-alt mr-2"></i>
                        <span>Requests continue in the HAYGENTS app.</span>
                    </div>
                </div>
            </div>
        </div>

        <?php if($others->count() > 0): ?>
            <hr class="my-5">
            <h2 class="h4 font-weight-bold mb-4" style="color: var(--hg-navy);">Other verified professionals</h2>
            <div class="row">
                <?php $__currentLoopData = $others; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $other): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-12 col-sm-6 col-lg-3 mb-4 mb-lg-0">
                        <article class="hg-other-pro-card">
                            <div class="d-flex align-items-start justify-content-between mb-3">
                                <div class="hg-avatar-box"><?php echo e($other->initials); ?></div>
                                <span class="hg-verified-pill"><i class="fas fa-check-circle"></i> Verified</span>
                            </div>
                            <h3 class="h6 firaSemiBold mb-1" style="color: var(--hg-navy);"><?php echo e($other->name); ?></h3>
                            <div class="small text-muted mb-1"><?php echo e($other->category); ?> &middot; <?php echo e($other->years_exp); ?> years</div>
                            <div class="small text-muted mb-3"><i class="fas fa-map-marker-alt mr-1"></i> <?php echo e($other->city); ?>, <?php echo e($other->state); ?></div>
                            <div class="hg-cred-box mt-auto"><i class="far fa-check-circle text-primary mr-1"></i> <?php echo e($other->credentials); ?></div>
                            <a href="<?php echo e(route('professionals.show', $other->slug)); ?>" class="btn btn-sm hg-btn-outline btn-block py-2">
                                Request through HAYGENTS
                            </a>
                        </article>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Laravel\haygents\resources\views/professionals/show.blade.php ENDPATH**/ ?>