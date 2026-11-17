<div class="accordion" id="haygentsFaqAccordion">
    <?php $__currentLoopData = $faqs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $faq): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php $collapseId = 'faqCollapse' . $faq->id; ?>
        <div class="card hg-faq-card">
            <div id="faqHeading<?php echo e($faq->id); ?>">
                <button class="hg-faq-btn" type="button" data-toggle="collapse" data-target="#<?php echo e($collapseId); ?>" aria-expanded="<?php echo e($index === 0 ? 'true' : 'false'); ?>" aria-controls="<?php echo e($collapseId); ?>">
                    <span><?php echo e($faq->question); ?></span>
                    <i class="fas fa-chevron-down hg-faq-icon"></i>
                </button>
            </div>
            <div id="<?php echo e($collapseId); ?>" class="collapse <?php echo e($index === 0 ? 'show' : ''); ?>" aria-labelledby="faqHeading<?php echo e($faq->id); ?>" data-parent="#haygentsFaqAccordion">
                <div class="hg-faq-body"><?php echo e($faq->answer); ?></div>
            </div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php /**PATH C:\Laravel\haygents\resources\views/partials/faq-accordion.blade.php ENDPATH**/ ?>