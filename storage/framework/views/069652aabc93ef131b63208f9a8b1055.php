<div
    class="hg-testimonial-slider"
    id="testimonialSlider"
    role="region"
    aria-roledescription="carousel"
    aria-label="Customer testimonials"
>
    <div class="d-flex justify-content-end mb-3" style="gap: 0.5rem;">
        <button type="button" class="hg-slider-arrow" id="testimonialPrevBtn" aria-label="Previous testimonial">
            <i class="fas fa-chevron-left"></i>
        </button>
        <button type="button" class="hg-slider-arrow" id="testimonialNextBtn" aria-label="Next testimonial">
            <i class="fas fa-chevron-right"></i>
        </button>
    </div>

    <div class="hg-testimonial-viewport">
        <div class="hg-testimonial-track" id="testimonialTrack">
            <?php $__currentLoopData = $testimonials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $testimonial): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="hg-testimonial-slide">
                    <article class="hg-testimonial-card">
                        <div class="hg-stars">
                            <?php for($i = 0; $i < $testimonial->rating; $i++): ?>
                                <i class="fas fa-star mr-1"></i>
                            <?php endfor; ?>
                        </div>
                        <p class="hg-testimonial-quote"><?php echo e($testimonial->quote); ?></p>
                        <div class="d-flex align-items-center">
                            <div class="hg-avatar-box mr-3" style="width: 40px; height: 40px; font-size: 0.82rem;">
                                <?php echo e(strtoupper(substr($testimonial->customer_name, 0, 2))); ?>

                            </div>
                            <div>
                                <div class="font-weight-bold small"><?php echo e($testimonial->customer_name); ?></div>
                                <div class="text-muted" style="font-size: 0.78rem;"><?php echo e($testimonial->customer_role); ?>, <?php echo e($testimonial->location); ?></div>
                            </div>
                        </div>
                    </article>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>

    <div class="hg-slider-dots" id="testimonialDots" role="tablist" aria-label="Testimonial slides"></div>
</div>
<?php /**PATH C:\Laravel\haygents\resources\views/partials/testimonials.blade.php ENDPATH**/ ?>