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
            @foreach($testimonials as $testimonial)
                <div class="hg-testimonial-slide">
                    <article class="hg-testimonial-card">
                        <div class="hg-stars">
                            @for($i = 0; $i < $testimonial->rating; $i++)
                                <i class="fas fa-star mr-1"></i>
                            @endfor
                        </div>
                        <p class="hg-testimonial-quote">{{ $testimonial->quote }}</p>
                        <div class="d-flex align-items-center">
                            <div class="hg-avatar-box mr-3" style="width: 40px; height: 40px; font-size: 0.82rem;">
                                {{ strtoupper(substr($testimonial->customer_name, 0, 2)) }}
                            </div>
                            <div>
                                <div class="firaSemiBold small">{{ $testimonial->customer_name }}</div>
                                <div class="text-muted" style="font-size: 0.78rem;">{{ $testimonial->customer_role }}, {{ $testimonial->location }}</div>
                            </div>
                        </div>
                    </article>
                </div>
            @endforeach
        </div>
    </div>

    <div class="hg-slider-dots" id="testimonialDots" role="tablist" aria-label="Testimonial slides"></div>
</div>
