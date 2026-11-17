@php
    $isFavorited = in_array($property->id, $favoriteIds ?? []);
@endphp

<div class="col-12 col-md-6 col-lg-3 mb-4">
    <article class="hg-property-card">
        <div class="hg-property-media">
            <a href="{{ route('properties.show', $property->slug) }}" class="d-block h-100 w-100">
                @php
                    $img = json_decode($property->image_path);
                @endphp
                @if (count($img) > 0)
                <img src="{{ asset($img[0]) }}" alt="{{ $property->title }} in {{ $property->address_area }}, {{ $property->city }}" loading="lazy">
                @else
                <img src="{{ asset('assets/images/property/default.png') }}" alt="{{ $property->title }} in {{ $property->address_area }}, {{ $property->city }}" loading="lazy">
                @endif
            </a>
            <div class="hg-property-badges">
                <span class="hg-tag">For {{ ucfirst($property->purpose) }}</span>
                {{-- @if($property->is_featured)
                    <span class="hg-tag hg-tag-featured">Featured</span>
                @endif --}}
            </div>
            <button type="button" class="hg-fav-btn js-favorite-btn {{ $isFavorited ? 'is-favorited' : '' }}" data-property-id="{{ $property->id }}" aria-label="Save property">
                <i class="{{ $isFavorited ? 'fas' : 'far' }} fa-heart"></i>
            </button>
        </div>

        <div class="hg-property-body">
            <div class="hg-property-location">{{ $property->address_area }}, {{ $property->city }}</div>
            <h3 class="hg-property-title">
                <a href="{{ route('properties.show', $property->slug) }}" class="text-reset text-decoration-none">{{ $property->title }}</a>
            </h3>
            <div class="hg-property-specs tabular-nums">
                @if($property->bedrooms > 0) {{ $property->bedrooms }} beds · @endif
                @if($property->bathrooms > 0) {{ $property->bathrooms }} baths · @endif
                {{ number_format($property->size_sqm) }} sqm
            </div>
            <div class="hg-property-price tabular-nums">
                {{ $property->formatted_price }}
                @if($property->price_period) <span>/ {{ $property->price_period }}</span> @endif
            </div>
            {{-- <div class="hg-property-footer">
                @if($property->verification_status === 'verified')
                    <span class="hg-verif-mini hg-verif-verified"><i class="fas fa-check-circle"></i> Verified</span>
                @elseif($property->verification_status === 'pending')
                    <span class="hg-verif-mini hg-verif-pending"><i class="fas fa-clock"></i> Pending</span>
                @else
                    <span class="hg-verif-mini hg-verif-unverified"><i class="fas fa-minus-circle"></i> Unverified</span>
                @endif
                <a href="{{ route('properties.show', $property->slug) }}" class="btn btn-sm hg-btn-outline py-1 px-2">View Property</a>
            </div> --}}
        </div>
    </article>
</div>
