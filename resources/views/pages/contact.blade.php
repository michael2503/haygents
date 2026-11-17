@extends('layouts.app')

@section('title', 'Contact Us — HAYGENTS')

@section('content')
<section class="pt-5 pb-4 bg-white">
    <div class="container text-center">
        <div class="hg-eyebrow">Contact</div>
        <h1 class="hg-section-title mx-auto mb-2" style="font-size: clamp(2rem, 3.6vw, 3rem);">Get in touch</h1>
        <p class="hg-section-subtitle mx-auto" style="max-width: 600px; line-height: 1.6;">
            Questions about a listing, your account or partnering with HAYGENTS? Send us a message and we will reply within one working day.
        </p>
    </div>
</section>

<section class="pb-5 pt-3 bg-white">
    <div class="container">
        @if(session('status'))
            <div class="alert alert-success mb-4">{{ session('status') }}</div>
        @endif

        <div class="row">
            <div class="col-12 col-lg-7 mb-4 mb-lg-0">
                <div class="hg-contact-form-card">
                    <h2 class="h4 mb-4" style="font-family: ;FiraSemiBold'; color: var(--hg-navy);">Send us a message</h2>

                    <form action="{{ route('contact.submit') }}" method="POST" id="contactPageForm">
                        @csrf
                        <div class="form-row">
                            <div class="form-group col-12 col-md-6">
                                <label for="contactFullName" class="small firaMedium">Full name</label>
                                <input type="text" id="contactFullName" name="full_name" class="form-control hg-input-soft @error('full_name') is-invalid @enderror" value="{{ old('full_name') }}" placeholder="Adaeze Okafor" required>
                                @error('full_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="form-group col-12 col-md-6">
                                <label for="contactEmail" class="small firaMedium">Email address</label>
                                <input type="email" id="contactEmail" name="email" class="form-control hg-input-soft @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="adaeze.okafor@gmail.com" required>
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-12 col-md-6">
                                <label for="contactPhone" class="small firaMedium">Phone number (optional)</label>
                                <input type="tel" id="contactPhone" name="phone" class="form-control hg-input-soft" value="{{ old('phone') }}" placeholder="+234 803 555 0142">
                            </div>
                            <div class="form-group col-12 col-md-6">
                                <label for="contactTopic" class="small firaMedium">Topic</label>
                                <select id="contactTopic" name="subject" class="form-control hg-input-soft" required>
                                    @foreach(['A property listing', 'Account & verification', 'Partnering as an agent or company', 'Report a listing', 'Disputes and support', 'General inquiry'] as $topic)
                                        <option value="{{ $topic }}" {{ old('subject', $prefillTopic) === $topic ? 'selected' : '' }}>{{ $topic }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="contactPropRef" class="small firaMedium">Property reference (optional)</label>
                            <input type="text" id="contactPropRef" name="property_ref" class="form-control hg-input-soft" value="{{ old('property_ref', $prefillRef) }}" placeholder="e.g. HGT-LK-20401">
                        </div>

                        <div class="form-group">
                            <label for="contactMessage" class="small firaMedium">Message</label>
                            <textarea id="contactMessage" name="message" class="form-control hg-input-soft @error('message') is-invalid @enderror" rows="5" placeholder="Tell us how we can help. Please do not include ID numbers, bank details or passwords." required>{{ old('message') }}</textarea>
                            @error('message')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between pt-2">
                            <small class="text-muted mb-3 mb-sm-0">We use your details only to reply to this message.</small>
                            <button type="submit" class="btn hg-btn-primary px-4 py-2">Send message</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-12 col-lg-5">
                <div class="d-flex flex-column" style="gap: 1.15rem;">
                    <div class="hg-contact-dark-card">
                        <h2 class="h5 firaSemiBold text-white mb-4">Contact details</h2>
                        <div class="d-flex align-items-center mb-3">
                            <div class="hg-contact-icon-circle mr-3"><i class="far fa-envelope"></i></div>
                            <div>
                                <div class="small" style="color: rgba(255,255,255,0.65);">Email</div>
                                <div class="firaMedium">support@haygents.ng</div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <div class="hg-contact-icon-circle mr-3"><i class="fas fa-phone-alt"></i></div>
                            <div>
                                <div class="small" style="color: rgba(255,255,255,0.65);">Phone</div>
                                <div class="firaMedium tabular-nums">+234 800 429 4368</div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="hg-contact-icon-circle mr-3"><i class="fas fa-map-marker-alt"></i></div>
                            <div>
                                <div class="small" style="color: rgba(255,255,255,0.65);">Office</div>
                                <div class="firaMedium">Victoria Island, Lagos, Nigeria</div>
                            </div>
                        </div>
                    </div>

                    <div class="hg-contact-hours-card">
                        <h3 class="h6 firaMedium mb-3" style="color: var(--hg-navy);">
                            <i class="far fa-clock mr-2 text-primary"></i>Support hours
                        </h3>
                        <div class="d-flex justify-content-between small mb-2">
                            <span class="text-secondary">Monday to Friday</span>
                            <span class="tabular-nums firaMedium">8:00 AM to 6:00 PM</span>
                        </div>
                        <div class="d-flex justify-content-between small mb-2">
                            <span class="text-secondary">Saturday</span>
                            <span class="tabular-nums firaMedium">10:00 AM to 2:00 PM</span>
                        </div>
                        <div class="d-flex justify-content-between small mb-3">
                            <span class="text-secondary">Sunday</span>
                            <span class="firaMedium">Closed</span>
                        </div>
                        <div class="text-muted" style="font-size: 0.76rem;">All times are West Africa Time (WAT).</div>
                    </div>

                    <div class="hg-contact-app-card">
                        <h3 class="h6 firaMedium mb-1" style="color: var(--hg-navy);">Already a customer?</h3>
                        <p class="small text-secondary mb-3">Manage your inspections, saved properties and messages directly on HAYGENTS.</p>
                        <div class="d-flex flex-wrap" style="gap: 0.6rem;">
                            <a href="#app-store">
                                <img src="{{ asset('assets/images/appstore2.png') }}" alt="">
                            </a>
                            <a href="#google-play">
                                <img src="{{ asset('assets/images/google2.png') }}" alt="">
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="hg-section hg-section-alt">
    <div class="container">
        <div class="mb-4">
            <div class="hg-eyebrow">Before you message</div>
            <h2 class="hg-section-title">You might find your answer here</h2>
        </div>

        <div class="row">
            <div class="col-12 col-md-4 mb-4 mb-md-0">
                <div class="hg-about-card">
                    <div class="hg-category-icon"><i class="far fa-question-circle"></i></div>
                    <h3 class="h6 mb-2" style="font-family: 'FiraMedium'; color: var(--hg-navy);">Browse FAQs</h3>
                    <p class="small text-secondary mb-3">Answers to the questions we hear most often.</p>
                    <a href="{{ route('home') }}#faq-section" class="small firaMedium text-primary text-decoration-none">Visit help centre <i class="fas fa-arrow-right ml-1"></i></a>
                </div>
            </div>
            <div class="col-12 col-md-4 mb-4 mb-md-0">
                <div class="hg-about-card">
                    <div class="hg-category-icon"><i class="fas fa-exclamation-triangle"></i></div>
                    <h3 class="h6 mb-2" style="font-family: 'FiraMedium'; color: var(--hg-navy);">Report a listing</h3>
                    <p class="small text-secondary mb-3">Tell us if a listing looks inaccurate or suspicious.</p>
                    <a href="{{ route('contact', ['topic' => 'Report a listing']) }}" class="small firaMedium text-primary text-decoration-none">Report a listing <i class="fas fa-arrow-right ml-1"></i></a>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="hg-about-card">
                    <div class="hg-category-icon"><i class="fas fa-balance-scale"></i></div>
                    <h3 class="h6 mb-2" style="font-family: 'FiraMedium'; color: var(--hg-navy);">Disputes and support</h3>
                    <p class="small text-secondary mb-3">Raise a concern about an inspection, agent or transaction.</p>
                    <a href="{{ route('contact', ['topic' => 'Disputes and support']) }}" class="small firaMedium text-primary text-decoration-none">Open a support case <i class="fas fa-arrow-right ml-1"></i></a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
