<div class="col-12 col-md-6 col-lg-3 mb-4 mb-lg-0">
    <article class="hg-profile-card">
        <div class="d-flex align-items-start justify-content-between mb-3">
            <div class="hg-avatar-box"><?php echo e($agent->initials); ?></div>
            <span class="hg-verified-pill"><i class="fas fa-check-circle"></i> <?php echo e(ucfirst($agent->verification_status)); ?></span>
        </div>
        <h3 class="h6 font-weight-bold mb-1"><?php echo e($agent->name); ?></h3>
        <div class="small text-muted mb-1"><?php echo e($agent->role_title); ?></div>
        <div class="small text-muted mb-2" style="font-size: 12px"><i class="fas fa-map-marker-alt mr-1"></i> <?php echo e($agent->city); ?>, <?php echo e($agent->state); ?></div>
        
        <div class="hg-cred-box"><i class="fas fa-shield-alt text-primary mr-1"></i> <?php echo e($agent->credentials); ?></div>
        
        <a href="<?php echo e(route('agents.index')); ?>#agent-<?php echo e($agent->slug); ?>" class="btn hg-btn-outline btn-block">Contact Agent</a>
    </article>
</div>
<?php /**PATH C:\Laravel\haygents\resources\views/partials/agent-card.blade.php ENDPATH**/ ?>