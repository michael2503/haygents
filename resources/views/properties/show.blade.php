@extends('layouts.app')

@section('title', $property->title . ' — HAYGENTS')

@section('content')
@php
    $askingPrice = (int) round($property->price / 1.05);
    $platformFee = $property->price - $askingPrice;
    $toilets = $property->bathrooms > 0 ? $property->bathrooms + 1 : 0;
    $parking = $property->parking_spaces ?? 2;
    $buildArea = $property->size_sqm > 0 ? (int) round($property->size_sqm * 0.82) . ' sqm' : 'N/A';
    $landArea = $property->size_sqm > 0 ? $property->size_sqm . ' sqm' : 'N/A';
    $galleryPhotos = json_decode($property->image_path);
@endphp

<section class="pt-4 pb-5 bg-white">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent px-0 py-0 mb-3 small">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('properties.index', ['purpose' => $property->purpose]) }}">For {{ $property->purpose }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('properties.index', ['city' => $property->city]) }}">{{ $property->city }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $property->address_area }}</li>
            </ol>
        </nav>

        <div class="d-flex flex-column flex-md-row align-items-md-end justify-content-between mb-4">
            <div class="mb-3 mb-md-0">
                <div class="hg-eyebrow">For {{ $property->purpose }}</div>
                <h1 class="h2 firaSemiBold mb-1" style="color: var(--hg-navy);">{{ $property->title }}</h1>
                <p class="small text-muted mb-0">
                    <i class="fas fa-map-marker-alt text-primary mr-1"></i>
                    {{ $property->address_area }}, {{ $property->city }}, {{ $property->state }}
                    &nbsp;&middot;&nbsp; Ref: <strong>{{ $property->ref_code }}</strong>
                </p>
            </div>
            <div class="d-flex align-items-center" style="gap: 0.6rem;">
                <a href="{{ route('contact', ['topic' => 'A property listing', 'ref' => $property->ref_code]) }}" class="btn btn-sm hg-btn-outline px-3">
                    <i class="fas fa-share-alt mr-1"></i> Share / Enquire
                </a>
            </div>
        </div>

        <!-- 5-Photo Bento Gallery -->
        <div class="hg-bento-gallery mb-4">
            @php
                $img = json_decode($property->image_path);
            @endphp


            <div class="hg-bento-main" data-toggle="modal" data-target="#photoGalleryModal" data-photo-index="0">
                <img src="{{ asset($img[0]) }}" alt="{{ $property->title }}">
            </div>
            <div class="hg-bento-subgrid">
                @php
                    $count = 1;
                @endphp
                @forelse ($galleryPhotos as $im)
                @if($count > 1 && $count < 6)

                @if($count < 5)
                <div class="hg-bento-tile" data-toggle="modal" data-target="#photoGalleryModal" data-photo-index="{{ $count }}">
                    <img src="{{ asset($im) }}" alt="Living area">
                </div>
                @else
                <div class="hg-bento-tile" data-toggle="modal" data-target="#photoGalleryModal">
                    <img src="{{ asset(asset($im)) }}" alt="Exterior detail">
                    <button type="button" class="hg-show-photos-btn" data-toggle="modal" data-target="#photoGalleryModal" data-photo-index="0">
                        <i class="far fa-images mr-1"></i> Show all {{ count($galleryPhotos) }} photos
                    </button>
                </div>
                @endif

                @endif

                @php
                    $count++;
                @endphp
                @empty

                @endforelse


                {{-- <div class="hg-bento-tile" data-toggle="modal" data-target="#photoGalleryModal" data-photo-index="1">
                    <img src="{{ $galleryPhotos[1] }}" alt="Living area">
                </div>
                <div class="hg-bento-tile" data-toggle="modal" data-target="#photoGalleryModal" data-photo-index="2">
                    <img src="{{ $galleryPhotos[2] }}" alt="Modern kitchen">
                </div>
                <div class="hg-bento-tile" data-toggle="modal" data-target="#photoGalleryModal" data-photo-index="3">
                    <img src="{{ $galleryPhotos[3] }}" alt="Master bedroom">
                </div>
                <div class="hg-bento-tile" data-toggle="modal" data-target="#photoGalleryModal" data-photo-index="4">
                    <img src="{{ $galleryPhotos[4] }}" alt="Exterior detail">
                    <button type="button" class="hg-show-photos-btn" data-toggle="modal" data-target="#photoGalleryModal" data-photo-index="0">
                        <i class="far fa-images mr-1"></i> Show all {{ count($galleryPhotos) }} photos
                    </button>
                </div> --}}
            </div>
        </div>

        <div class="row">
            <div class="col-12 col-lg-8 mb-4 mb-lg-0">
                <div class="d-flex flex-wrap align-items-center mb-4" style="gap: 0.5rem;">
                    <span class="badge badge-light border px-3 py-2 font-weight-semibold text-dark">For {{ $property->purpose }}</span>
                    <span class="badge badge-light border px-3 py-2 font-weight-semibold text-dark">{{ $property->category->name ?? 'Property' }}</span>
                    <span class="badge badge-light border px-3 py-2 font-weight-semibold text-dark">{{ $property->furnishing }}</span>
                </div>

                <!-- 6-Box Specs Strip -->
                <div class="row mb-4">
                    <div class="col-6 col-sm-4 col-md-2 mb-3 mb-md-0 px-2">
                        <div class="hg-spec-tile">
                            <div class="small text-primary mb-1"><i class="fas fa-bed"></i></div>
                            <div class="font-weight-bold tabular-nums">{{ $property->bedrooms ?: 'N/A' }}</div>
                            <div class="text-muted" style="font-size: 0.74rem;">Bedrooms</div>
                        </div>
                    </div>
                    <div class="col-6 col-sm-4 col-md-2 mb-3 mb-md-0 px-2">
                        <div class="hg-spec-tile">
                            <div class="small text-primary mb-1"><i class="fas fa-bath"></i></div>
                            <div class="font-weight-bold tabular-nums">{{ $property->bathrooms ?: 'N/A' }}</div>
                            <div class="text-muted" style="font-size: 0.74rem;">Bathrooms</div>
                        </div>
                    </div>
                    <div class="col-6 col-sm-4 col-md-2 mb-3 mb-md-0 px-2">
                        <div class="hg-spec-tile">
                            <div class="small text-primary mb-1"><i class="fas fa-toilet"></i></div>
                            <div class="font-weight-bold tabular-nums">{{ $toilets ?: 'N/A' }}</div>
                            <div class="text-muted" style="font-size: 0.74rem;">Toilets</div>
                        </div>
                    </div>
                    <div class="col-6 col-sm-4 col-md-2 mb-3 mb-sm-0 px-2">
                        <div class="hg-spec-tile">
                            <div class="small text-primary mb-1"><i class="fas fa-car"></i></div>
                            <div class="font-weight-bold tabular-nums">{{ $parking }}</div>
                            <div class="text-muted" style="font-size: 0.74rem;">Parking</div>
                        </div>
                    </div>
                    <div class="col-6 col-sm-4 col-md-2 mb-3 mb-sm-0 px-2">
                        <div class="hg-spec-tile">
                            <div class="small text-primary mb-1"><i class="fas fa-expand"></i></div>
                            <div class="font-weight-bold tabular-nums" style="font-size: 0.88rem;">{{ $landArea }}</div>
                            <div class="text-muted" style="font-size: 0.74rem;">Land area</div>
                        </div>
                    </div>
                    <div class="col-6 col-sm-4 col-md-2 px-2">
                        <div class="hg-spec-tile">
                            <div class="small text-primary mb-1"><i class="far fa-building"></i></div>
                            <div class="font-weight-bold tabular-nums" style="font-size: 0.88rem;">{{ $buildArea }}</div>
                            <div class="text-muted" style="font-size: 0.74rem;">Building area</div>
                        </div>
                    </div>
                </div>

                <!-- About this property -->
                <div class="pb-4 mb-4 border-bottom">
                    <h2 class="h5 font-weight-bold mb-2" style="color: var(--hg-navy);">About this property</h2>
                    <p class="text-secondary mb-0" style="line-height: 1.7;">{{ $property->description }}</p>
                </div>

                <!-- Features and amenities -->
                <div class="pb-4 mb-4 border-bottom">
                    <h2 class="h5 font-weight-bold mb-3" style="color: var(--hg-navy);">Features and amenities</h2>
                    <div class="row small text-secondary">
                        <div class="col-12 col-sm-6 mb-3">
                            <h3 class="small font-weight-bold mb-2" style="color: var(--hg-navy);">Interior &amp; Features</h3>
                            <ul class="list-unstyled mb-0">
                                @foreach(($property->features ?? ['Fitted kitchen', 'En-suite bedrooms']) as $feat)
                                    <li class="mb-2"><i class="fas fa-check text-muted mr-2"></i>{{ $feat }}</li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="col-12 col-sm-6">
                            <h3 class="small font-weight-bold mb-2" style="color: var(--hg-navy);">Utilities &amp; Security</h3>
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2"><i class="fas fa-check text-muted mr-2"></i>24-hour power backup</li>
                                <li class="mb-2"><i class="fas fa-check text-muted mr-2"></i>Borehole &amp; water treatment</li>
                                <li class="mb-2"><i class="fas fa-check text-muted mr-2"></i>Gated perimeter security</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Where it is -->
                <div class="pb-4 mb-4 border-bottom">
                    <h2 class="h5 font-weight-bold mb-3" style="color: var(--hg-navy);">Where it is</h2>
                    <div class="hg-area-map-box mb-2">
                        <div class="hg-area-circle">
                            <div class="hg-area-pin">
                                <i class="fas fa-map-marker-alt text-primary mr-1"></i>
                                {{ $property->address_area }}, {{ $property->city }}
                            </div>
                        </div>
                    </div>
                    <p class="small text-muted mb-0">For your security, HAYGENTS shows the area only. A verified representative accompanies you during inspection.</p>
                </div>

                <!-- Verification summary (7 rows) -->
                <div class="pb-4 mb-4 border-bottom">
                    <h2 class="h5 font-weight-bold mb-1" style="color: var(--hg-navy);">Verification summary</h2>
                    <p class="small text-muted mb-2">What has been checked for this listing.</p>
                    <ul class="hg-check-list mb-2">
                        <li class="hg-check-row"><span>Provider identity (Level 1 and 2 KYC)</span><strong>{{ ucfirst($property->agent->verification_status ?? 'verified') }}</strong></li>
                        <li class="hg-check-row"><span>Authority to list</span><strong>{{ ucfirst($property->verification_status) }}</strong></li>
                        <li class="hg-check-row"><span>Ownership documents</span><strong>{{ ucfirst($property->ownership_doc_status) }}</strong></li>
                        <li class="hg-check-row"><span>Survey documents (SURCON seal)</span><strong>{{ ucfirst($property->title_doc_status) }}</strong></li>
                        <li class="hg-check-row"><span>Physical inspection</span><strong>{{ ucfirst($property->inspection_status) }}</strong></li>
                        <li class="hg-check-row"><span>Location &amp; neighbourhood check</span><strong>{{ ucfirst($property->location_check_status) }}</strong></li>
                        <li class="hg-check-row"><span>Legal &amp; final HAYGENTS review</span><strong>{{ ucfirst($property->legal_doc_status) }}</strong></li>
                    </ul>
                    <p class="small text-muted mb-0">Checks do not guarantee legal title. We recommend independent legal advice before you buy.</p>
                </div>

                <!-- Documents & Verified Agent -->
                <div class="row mb-4">
                    <div class="col-12 col-md-6 mb-3 mb-md-0">
                        <div class="p-4 border rounded h-100" style="border-radius: 12px;">
                            <h3 class="h6 font-weight-bold mb-3" style="color: var(--hg-navy);">Documents on file</h3>
                            <ul class="list-unstyled small text-dark mb-3">
                                <li class="mb-2"><i class="fas fa-lock text-muted mr-2"></i>Certificate of Occupancy</li>
                                <li class="mb-2"><i class="fas fa-lock text-muted mr-2"></i>Survey Plan (SURCON seal)</li>
                                <li><i class="fas fa-lock text-muted mr-2"></i>Deed of Assignment</li>
                            </ul>
                            <div class="text-muted" style="font-size: 0.76rem;">Private. Shared with authorized parties during a transaction.</div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="p-4 border rounded h-100" style="border-radius: 12px;">
                            <h3 class="h6 font-weight-bold mb-2" style="color: var(--hg-navy);">
                                Listed by {{ $property->agent->name ?? ($property->company->name ?? 'a verified agent') }}
                            </h3>
                            <p class="small text-secondary mb-2">{{ $property->agent->credentials ?? 'Verified HAYGENTS Partner' }}</p>
                            <p class="small text-muted mb-0">An approved HAYGENTS partner handles every enquiry, inspection and documentation step.</p>
                        </div>
                    </div>
                </div>

                <div class="p-3 rounded bg-light border" style="border-radius: 12px;">
                    <div class="small font-weight-bold mb-1" style="color: var(--hg-navy);">Good to know</div>
                    <div class="small text-secondary">Never pay anyone directly or in cash at the property. Always confirm instructions on HAYGENTS.</div>
                </div>
            </div>

            <!-- Right Sticky Price & Inspection Sidebar -->
            <div class="col-12 col-lg-4">
                <div class="hg-price-sidebar">
                    <div class="small text-muted mb-1">{{ $property->purpose === 'sale' ? 'Total price' : 'Total package' }}</div>
                    <div class="h3 font-weight-bold tabular-nums mb-3" style="color: var(--hg-navy);">
                        {{ $property->formatted_price }}
                        @if($property->price_period)<span class="h6 text-muted font-weight-normal">/ {{ $property->price_period }}</span>@endif
                    </div>

                    <div class="d-flex justify-content-between small mb-2">
                        <span class="text-secondary">Provider asking price</span>
                        <span class="font-weight-semibold tabular-nums">₦{{ number_format($askingPrice) }}</span>
                    </div>
                    <div class="d-flex justify-content-between small mb-3 pb-3 border-bottom">
                        <span class="text-secondary">Platform &amp; verification fee</span>
                        <span class="font-weight-semibold tabular-nums">₦{{ number_format($platformFee) }}</span>
                    </div>
                    <div class="d-flex justify-content-between small font-weight-bold mb-3">
                        <span>Total</span>
                        <span class="tabular-nums">{{ $property->formatted_price }}</span>
                    </div>

                    <a href="{{ route('contact', ['topic' => 'A property listing', 'ref' => $property->ref_code]) }}" class="btn hg-btn-primary btn-block py-2 mb-3">
                        Express interest
                    </a>

                    <h3 class="h6 font-weight-bold mb-2" style="color: var(--hg-navy);">Request a viewing</h3>
                    <form action="{{ route('inspections.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="property_id" value="{{ $property->id }}">
                        <div class="form-group mb-2"><input type="text" name="full_name" class="form-control form-control-sm" placeholder="Full name" required></div>
                        <div class="form-group mb-2"><input type="email" name="email" class="form-control form-control-sm" placeholder="Email address" required></div>
                        <div class="form-group mb-2"><input type="tel" name="phone" class="form-control form-control-sm" placeholder="Phone (+234...)" required></div>
                        <div class="form-group mb-3"><input type="date" name="preferred_date" class="form-control form-control-sm" required></div>
                        <button type="submit" class="btn hg-btn-outline btn-block py-2">Request a viewing</button>
                    </form>
                </div>
            </div>
        </div>

        @if($related->count() > 0)
            <hr class="my-5">
            <h2 class="h4 font-weight-bold mb-4" style="color: var(--hg-navy);">Similar properties in {{ $property->city }}</h2>
            <div class="row">
                @foreach($related as $sim)
                    @include('partials.property-card', ['property' => $sim, 'favoriteIds' => $favoriteIds])
                @endforeach
            </div>
        @endif
    </div>
</section>

<!-- Photo Gallery Slider Modal -->
<div class="modal fade" id="photoGalleryModal" tabindex="-1" role="dialog" aria-labelledby="photoGalleryModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 14px;">
            <div class="modal-header align-items-center">
                <div class="d-flex align-items-center" style="gap: 0.75rem;">
                    <h5 class="modal-title font-weight-bold mb-0" id="photoGalleryModalTitle">{{ $property->title }}</h5>
                    <span class="hg-carousel-counter" id="gallerySlideCounter">1 / {{ count($galleryPhotos) }}</span>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-3">
                <div id="propertyGalleryCarousel" class="carousel slide hg-modal-carousel" data-ride="carousel" data-interval="false">
                    <ol class="carousel-indicators">
                        @foreach($galleryPhotos as $index => $photoUrl)
                            <li data-target="#propertyGalleryCarousel" data-slide-to="{{ $index }}" class="{{ $index === 0 ? 'active' : '' }}"></li>
                        @endforeach
                    </ol>
                    <div class="carousel-inner rounded" style="background: #061525;">
                        @foreach($galleryPhotos as $index => $photoUrl)
                            <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                <img src="{{ asset($photoUrl) }}" class="d-block w-100 hg-modal-carousel-img" alt="{{ $property->title }} photo {{ $index + 1 }}">
                            </div>
                        @endforeach
                    </div>
                    <a class="carousel-control-prev" href="#propertyGalleryCarousel" role="button" data-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="sr-only">Previous</span>
                    </a>
                    <a class="carousel-control-next" href="#propertyGalleryCarousel" role="button" data-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="sr-only">Next</span>
                    </a>
                </div>

                <div class="hg-modal-thumbs mt-3">
                    @foreach($galleryPhotos as $index => $photoUrl)
                        <button
                            type="button"
                            class="hg-modal-thumb {{ $index === 0 ? 'active' : '' }}"
                            data-slide-to="{{ $index }}"
                            aria-label="View photo {{ $index + 1 }}"
                        >
                            <img src="{{ asset($photoUrl) }}" alt="Thumbnail {{ $index + 1 }}">
                        </button>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
