<div class="accordion" id="haygentsFaqAccordion">
    @foreach($faqs as $index => $faq)
        @php $collapseId = 'faqCollapse' . $faq->id; @endphp
        <div class="card hg-faq-card">
            <div id="faqHeading{{ $faq->id }}">
                <button class="hg-faq-btn" type="button" data-toggle="collapse" data-target="#{{ $collapseId }}" aria-expanded="{{ $index === 0 ? 'true' : 'false' }}" aria-controls="{{ $collapseId }}">
                    <span>{{ $faq->question }}</span>
                    <i class="fas fa-chevron-down hg-faq-icon"></i>
                </button>
            </div>
            <div id="{{ $collapseId }}" class="collapse {{ $index === 0 ? 'show' : '' }}" aria-labelledby="faqHeading{{ $faq->id }}" data-parent="#haygentsFaqAccordion">
                <div class="hg-faq-body">{{ $faq->answer }}</div>
            </div>
        </div>
    @endforeach
</div>
