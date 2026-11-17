<form action="<?php echo e(route('properties.index')); ?>" method="GET" class="hg-search-bar mx-auto" style="max-width: 1080px;">
    <div class="row no-gutters align-items-center">
        <div class="col-12 col-lg-3 hg-search-field">
            <label for="location" class="hg-search-label">Location</label>
            <input type="text" id="location" name="location" class="hg-search-input" value="<?php echo e(request('location')); ?>" placeholder="City, state, or area">
        </div>
        <div class="col-12 col-sm-6 col-lg-2 hg-search-field">
            <label for="category" class="hg-search-label">Property Type</label>
            <select id="category" name="category" class="hg-search-input">
                <option value="all">Any type</option>
                <option value="houses">Houses</option>
                <option value="apartments">Apartments</option>
                <option value="land-plots">Land &amp; Plots</option>
                <option value="commercial">Commercial</option>
                <option value="shortlets">Shortlets</option>
                <option value="office-spaces">Office Spaces</option>
            </select>
        </div>
        <div class="col-12 col-sm-6 col-lg-2 hg-search-field">
            <label for="purpose" class="hg-search-label">Purpose</label>
            <select id="purpose" name="purpose" class="hg-search-input">
                <option value="sale">For Sale</option>
                <option value="rent">For Rent</option>
                <option value="shortlet">Shortlet</option>
                <option value="lease">For Lease</option>
            </select>
        </div>
        <div class="col-12 col-sm-6 col-lg-2 hg-search-field">
            <label for="price_range" class="hg-search-label">Price Range</label>
            <select id="price_range" name="price_range" class="hg-search-input">
                <option value="any">Any price</option>
                <option value="0-5000000">Up to ₦5M</option>
                <option value="5000000-25000000">₦5M – ₦25M</option>
                <option value="25000000-75000000">₦25M – ₦75M</option>
                <option value="75000000-150000000">₦75M – ₦150M</option>
                <option value="150000000+">₦150M+</option>
            </select>
        </div>
        <div class="col-12 col-sm-6 col-lg-1 hg-search-field">
            <label for="bedrooms" class="hg-search-label">Beds</label>
            <select id="bedrooms" name="bedrooms" class="hg-search-input">
                <option value="any">Any</option>
                <option value="1">1+</option>
                <option value="2">2+</option>
                <option value="3">3+</option>
                <option value="4">4+</option>
                <option value="5">5+</option>
            </select>
        </div>
        <div class="col-12 col-lg-2 pl-lg-2 pt-2 pt-lg-0">
            <button type="submit" class="btn hg-btn-primary hg-search-btn btn-block">
                <i class="fas fa-search mr-2"></i>Search
            </button>
        </div>
    </div>
</form>
<?php /**PATH C:\Laravel\haygents\resources\views/partials/search-form.blade.php ENDPATH**/ ?>