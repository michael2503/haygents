


<div
    class="hg-testimonial-slider"
    id="companySlider"
    role="region"
    aria-roledescription="carousel"
    aria-label="Customer testimonials"
>
    <div class="d-flex justify-content-end mb-3" style="gap: 0.5rem;">
        <button type="button" class="hg-slider-arrow" id="companyPrevBtn" aria-label="Previous testimonial">
            <i class="fas fa-chevron-left"></i>
        </button>
        <button type="button" class="hg-slider-arrow" id="companyNextBtn" aria-label="Next testimonial">
            <i class="fas fa-chevron-right"></i>
        </button>
    </div>

    <div class="hg-testimonial-viewport">
        <div class="hg-testimonial-track" id="companyTrack">
            <?php $__currentLoopData = $companies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $comp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="hg-testimonial-slide">
                    <img src="<?php echo e(asset($comp->logo_path)); ?>" alt="">
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>

    <div class="hg-slider-dots" id="companyDots" role="tablist" aria-label="Testimonial slides"></div>
</div>
<?php /**PATH C:\Laravel\haygents\resources\views/partials/company-card.blade.php ENDPATH**/ ?>