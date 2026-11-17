<div class="col-12 col-md-6 col-lg-3 mb-4 mb-lg-0">
    <article class="hg-profile-card">
        <div class="d-flex align-items-start justify-content-between mb-3">
            <div class="hg-avatar-box"><?php echo e($professional->initials); ?></div>
            <span class="hg-verified-pill"><i class="fas fa-check-circle"></i> <?php echo e(ucfirst($professional->verification_status)); ?></span>
        </div>
        <h3 class="h6 font-weight-bold mb-1"><?php echo e($professional->name); ?></h3>
        <div class="small font-weight-semibold text-primary mb-1"><?php echo e($professional->category); ?> · <?php echo e($professional->years_exp); ?> yrs</div>
        <div class="small text-muted mb-2"><i class="fas fa-map-marker-alt mr-1"></i> <?php echo e($professional->city); ?>, <?php echo e($professional->state); ?></div>
        
        <div class="hg-cred-box"><i class="fas fa-certificate text-primary mr-1"></i> <?php echo e($professional->credentials); ?></div>
        <a href="<?php echo e(route('professionals.index')); ?>#prof-<?php echo e($professional->slug); ?>" class="btn hg-btn-outline btn-block">Request Service</a>
    </article>
</div>
<?php /**PATH C:\Laravel\haygents\resources\views/partials/professional-card.blade.php ENDPATH**/ ?>