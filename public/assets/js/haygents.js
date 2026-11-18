$(function () {
    var $win = $(window);
    var $nav = $('#mainNavbar');

    // Sticky navbar shadow on scroll
    $win.on('scroll', function () {
        if ($nav.length) {
            $nav.toggleClass('is-scrolled', $win.scrollTop() > 12);
        }
    });

    // Responsive mega-dropdown toggle on mobile / tablet (< 992px)
    $(document).on('click', '[data-mega-toggle]', function (e) {
        if (window.innerWidth < 992) {
            e.preventDefault();
            var $dropdown = $(this).closest('.hg-mega-dropdown');
            var willOpen = !$dropdown.hasClass('is-open');

            $('.hg-mega-dropdown')
                .removeClass('is-open')
                .find('[data-mega-toggle]')
                .attr('aria-expanded', 'false');

            if (willOpen) {
                $dropdown.addClass('is-open');
                $(this).attr('aria-expanded', 'true');
            }
        }
    });

    $(document).on('click', function (e) {
        if (!$(e.target).closest('.hg-mega-dropdown').length) {
            $('.hg-mega-dropdown')
                .removeClass('is-open')
                .find('[data-mega-toggle]')
                .attr('aria-expanded', 'false');
        }
    });

    $win.on('resize', function () {
        if (window.innerWidth >= 992) {
            $('.hg-mega-dropdown')
                .removeClass('is-open')
                .find('[data-mega-toggle]')
                .attr('aria-expanded', 'false');
        }
    });

    // =========================================================
    // Testimonial Auto & Manual Slider (Home Page)
    // =========================================================
    function initTestimonialSlider() {
        var sliderSection = document.getElementById('testimonials');
        var slider = document.getElementById('testimonialSlider');
        var track = document.getElementById('testimonialTrack');
        var dotsWrap = document.getElementById('testimonialDots');
        var prevBtn = document.getElementById('testimonialPrevBtn');
        var nextBtn = document.getElementById('testimonialNextBtn');

        if (!slider || !track || !dotsWrap) return;

        var slides = track.querySelectorAll('.hg-testimonial-slide');
        var total = slides.length;
        if (total === 0) return;

        var currentIndex = 0;
        var isHovered = false;
        var timerId = null;

        function getVisibleCount() {
            if (window.innerWidth >= 992) return 3;
            if (window.innerWidth >= 768) return 2;
            return 1; // One testimonial per slide on mobile
        }

        function getMaxIndex() {
            return Math.max(0, total - getVisibleCount());
        }

        function renderDots() {
            var maxIdx = getMaxIndex();
            var dotCount = maxIdx + 1;
            var html = '';
            for (var i = 0; i < dotCount; i++) {
                var isActive = i === currentIndex;
                html +=
                    '<button type="button" class="hg-slider-dot ' +
                    (isActive ? 'active' : '') +
                    '" data-slide-index="' +
                    i +
                    '" role="tab" aria-selected="' +
                    (isActive ? 'true' : 'false') +
                    '" aria-label="Show testimonial slide ' +
                    (i + 1) +
                    '"></button>';
            }
            dotsWrap.innerHTML = html;
        }

        function updateTrack() {
            var visible = getVisibleCount();
            var maxIdx = getMaxIndex();
            if (currentIndex > maxIdx) currentIndex = maxIdx;
            if (currentIndex < 0) currentIndex = 0;

            var shiftPercent = (currentIndex * 100) / visible;
            track.style.transform = 'translateX(-' + shiftPercent + '%)';

            var dots = dotsWrap.querySelectorAll('.hg-slider-dot');
            for (var i = 0; i < dots.length; i++) {
                var active = i === currentIndex;
                dots[i].classList.toggle('active', active);
                dots[i].setAttribute('aria-selected', active ? 'true' : 'false');
            }
        }

        function goTo(nextIdx) {
            var maxIdx = getMaxIndex();
            if (maxIdx === 0) {
                currentIndex = 0;
            } else if (nextIdx > maxIdx) {
                currentIndex = 0;
            } else if (nextIdx < 0) {
                currentIndex = maxIdx;
            } else {
                currentIndex = nextIdx;
            }
            updateTrack();
        }

        function stopAutoSlide() {
            if (timerId !== null) {
                window.clearInterval(timerId);
                timerId = null;
            }
        }

        function startAutoSlide() {
            stopAutoSlide();
            if (isHovered || getMaxIndex() === 0) return;
            timerId = window.setInterval(function () {
                goTo(currentIndex + 1);
            }, 4500);
        }

        if (prevBtn) {
            prevBtn.addEventListener('click', function () {
                goTo(currentIndex - 1);
                startAutoSlide();
            });
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', function () {
                goTo(currentIndex + 1);
                startAutoSlide();
            });
        }

        dotsWrap.addEventListener('click', function (e) {
            var target = e.target.closest ? e.target.closest('[data-slide-index]') : null;
            if (!target) return;
            var idx = parseInt(target.getAttribute('data-slide-index'), 10);
            if (!isNaN(idx)) {
                goTo(idx);
                startAutoSlide();
            }
        });

        // Pause auto-slide on mouse hover or keyboard focus; resume on leave
        var hoverTarget = sliderSection || slider;
        hoverTarget.addEventListener('mouseenter', function () {
            isHovered = true;
            stopAutoSlide();
        });
        hoverTarget.addEventListener('mouseleave', function () {
            isHovered = false;
            startAutoSlide();
        });
        hoverTarget.addEventListener('focusin', function () {
            isHovered = true;
            stopAutoSlide();
        });
        hoverTarget.addEventListener('focusout', function () {
            isHovered = false;
            startAutoSlide();
        });

        // Mobile touch swipe support
        var touchStartX = 0;
        var touchStartY = 0;
        track.addEventListener(
            'touchstart',
            function (e) {
                stopAutoSlide();
                touchStartX = e.changedTouches[0].clientX;
                touchStartY = e.changedTouches[0].clientY;
            },
            { passive: true }
        );
        track.addEventListener(
            'touchend',
            function (e) {
                var dx = e.changedTouches[0].clientX - touchStartX;
                var dy = e.changedTouches[0].clientY - touchStartY;
                if (Math.abs(dx) > 40 && Math.abs(dx) > Math.abs(dy)) {
                    goTo(dx < 0 ? currentIndex + 1 : currentIndex - 1);
                }
                startAutoSlide();
            },
            { passive: true }
        );

        // Desktop mouse drag support
        var mouseDownX = null;
        track.addEventListener('mousedown', function (e) {
            mouseDownX = e.clientX;
        });
        window.addEventListener('mouseup', function (e) {
            if (mouseDownX === null) return;
            var dx = e.clientX - mouseDownX;
            mouseDownX = null;
            if (Math.abs(dx) > 45) {
                goTo(dx < 0 ? currentIndex + 1 : currentIndex - 1);
            }
        });

        window.addEventListener('resize', function () {
            renderDots();
            updateTrack();
        });

        renderDots();
        updateTrack();
        startAutoSlide();
    }

    // =========================================================
    // Property Details Modal Photo Carousel Slider
    // =========================================================
    function initPropertyGalleryModal() {
        var $modal = $('#photoGalleryModal');
        var $carousel = $('#propertyGalleryCarousel');

        if (!$modal.length || !$carousel.length) return;

        $carousel.carousel({
            interval: false,
            ride: false,
            wrap: true
        });

        function syncGalleryMeta(activeIdx) {
            var $items = $carousel.find('.carousel-item');
            var total = $items.length;
            $('#gallerySlideCounter').text(activeIdx + 1 + ' / ' + total);
            $('.hg-modal-thumb')
                .removeClass('active')
                .filter('[data-slide-to="' + activeIdx + '"]')
                .addClass('active');
        }

        // Jump straight to the clicked bento tile when modal opens
        $modal.on('show.bs.modal', function (e) {
            var trigger = e.relatedTarget;
            var idx = 0;
            if (trigger) {
                var raw = $(trigger).attr('data-photo-index');
                if (typeof raw !== 'undefined' && raw !== '') {
                    idx = parseInt(raw, 10) || 0;
                }
            }
            $carousel.carousel(idx);
            syncGalleryMeta(idx);
        });

        // Update counter and active thumbnail whenever carousel slides
        $carousel.on('slid.bs.carousel', function (e) {
            var idx = typeof e.to === 'number' ? e.to : $carousel.find('.carousel-item.active').index();
            syncGalleryMeta(idx);
        });

        // Clicking a thumbnail moves carousel to that photo
        $(document).on('click', '.hg-modal-thumb', function () {
            var idx = parseInt($(this).attr('data-slide-to'), 10) || 0;
            $carousel.carousel(idx);
            syncGalleryMeta(idx);
        });

        // Keyboard left/right arrows while modal is open
        $(document).on('keydown', function (e) {
            if (!$modal.hasClass('show')) return;
            if (e.key === 'ArrowLeft') {
                $carousel.carousel('prev');
            } else if (e.key === 'ArrowRight') {
                $carousel.carousel('next');
            }
        });

        // Mobile touch swipe inside modal carousel
        var touchX = 0;
        var carouselEl = $carousel.get(0);
        if (carouselEl) {
            carouselEl.addEventListener(
                'touchstart',
                function (e) {
                    touchX = e.changedTouches[0].clientX;
                },
                { passive: true }
            );
            carouselEl.addEventListener(
                'touchend',
                function (e) {
                    var dx = e.changedTouches[0].clientX - touchX;
                    if (Math.abs(dx) > 40) {
                        $carousel.carousel(dx < 0 ? 'next' : 'prev');
                    }
                },
                { passive: true }
            );
        }
    }

    initTestimonialSlider();
    initPropertyGalleryModal();

    // Segmented filter buttons inside sidebar filter forms
    $(document).on('click', '[data-seg-target]', function () {
        var targetSelector = $(this).attr('data-seg-target');
        var val = $(this).attr('data-seg-value');
        if (targetSelector) {
            $(targetSelector).val(val);
            $('[data-seg-target="' + targetSelector + '"]').removeClass('active');
            $(this).addClass('active');
        }
    });

    // Professional profile bio read more toggle
    $(document).on('click', '[data-action="toggle-prof-bio"]', function () {
        var $extra = $('#profBioExtra');
        if (!$extra.length) return;
        var isHidden = $extra.hasClass('d-none');
        $extra.toggleClass('d-none', !isHidden);
        $(this).text(isHidden ? 'Read less' : 'Read more');
    });
});
