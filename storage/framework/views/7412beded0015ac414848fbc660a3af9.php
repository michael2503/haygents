<?php
    $isFavorited = in_array($property->id, $favoriteIds ?? []);
?>

<div class="col-12 col-md-6 col-lg-3 mb-4">
    <article class="hg-property-card">
        <div class="hg-property-media">
            <a href="<?php echo e(route('properties.show', $property->slug)); ?>" class="d-block h-100 w-100">
                <?php
                    $img = json_decode($property->image_path);
                ?>
                <?php if(count($img) > 0): ?>
                <img src="<?php echo e(asset($img[0])); ?>" alt="<?php echo e($property->title); ?> in <?php echo e($property->address_area); ?>, <?php echo e($property->city); ?>" loading="lazy">
                <?php else: ?>
                <img src="<?php echo e(asset('assets/images/property/default.png')); ?>" alt="<?php echo e($property->title); ?> in <?php echo e($property->address_area); ?>, <?php echo e($property->city); ?>" loading="lazy">
                <?php endif; ?>
            </a>
            <div class="hg-property-badges">
                <span class="hg-tag">For <?php echo e(ucfirst($property->purpose)); ?></span>
                
            </div>
            <button type="button" class="hg-fav-btn js-favorite-btn <?php echo e($isFavorited ? 'is-favorited' : ''); ?>" data-property-id="<?php echo e($property->id); ?>" aria-label="Save property">
                <i class="<?php echo e($isFavorited ? 'fas' : 'far'); ?> fa-heart"></i>
            </button>
        </div>

        <div class="hg-property-body">
            <div class="hg-property-location"><?php echo e($property->address_area); ?>, <?php echo e($property->city); ?></div>
            <h3 class="hg-property-title">
                <a href="<?php echo e(route('properties.show', $property->slug)); ?>" class="text-reset text-decoration-none"><?php echo e($property->title); ?></a>
            </h3>
            <div class="hg-property-specs tabular-nums">
                <?php if($property->bedrooms > 0): ?> <?php echo e($property->bedrooms); ?> beds · <?php endif; ?>
                <?php if($property->bathrooms > 0): ?> <?php echo e($property->bathrooms); ?> baths · <?php endif; ?>
                <?php echo e(number_format($property->size_sqm)); ?> sqm
            </div>
            <div class="hg-property-price tabular-nums">
                <?php echo e($property->formatted_price); ?>

                <?php if($property->price_period): ?> <span>/ <?php echo e($property->price_period); ?></span> <?php endif; ?>
            </div>
            
        </div>
    </article>
</div>
<?php /**PATH C:\Laravel\haygents\resources\views/partials/property-card.blade.php ENDPATH**/ ?>