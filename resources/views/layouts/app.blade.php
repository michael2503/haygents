<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>@yield('title', 'HAYGENTS — Find Property You Can Trust')</title>
    <meta name="description" content="@yield('meta_description', 'Discover verified properties, connect with trusted real estate professionals, and make confident property decisions on HAYGENTS.')">
    <meta property="og:title" content="@yield('title', 'HAYGENTS — Real Estate Marketplace')">
    <meta property="og:description" content="@yield('meta_description', 'Discover verified properties and connect with real estate professionals across Nigeria.')">
    <meta property="og:type" content="website">
    <meta name="twitter:card" content="summary_large_image">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fira+Sans:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('assets/css/haygents.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/extras.css') }}">
</head>
<body>
    @include('partials.navbar')

    <main>
        @yield('content')
    </main>

    @include('partials.footer')

    {{-- <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script> --}}

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flexslider/2.7.2/jquery.flexslider-min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('assets/js/haygents.js') }}"></script>



    <script>
    $(window).on('load', function() {
        $('.flexslider').flexslider({
            animation: "slide",
            animationLoop: true,
            itemWidth: 210,
            itemMargin: 5,
            move: 1,
            minItems: 2,
            maxItems: 5,
            controlNav: false,
            directionNav: false,
            slideshow: true,
            slideshowSpeed: 2000,
            animationSpeed: 600,
            pauseOnHover: true,
            touch: true,
            mousewheel: false,

            // Responsive settings
            start: function(slider) {
                updateSliderItems(slider);
            }
        });

        function updateSliderItems(slider) {
            var width = $(window).width();

            if (width <= 576) {
                slider.vars.minItems = 2;
                slider.vars.maxItems = 2;
            } else if (width <= 991) {
                slider.vars.minItems = 3;
                slider.vars.maxItems = 3;
            } else {
                slider.vars.minItems = 5;
                slider.vars.maxItems = 5;
            }

            slider.resize();
        }

        $(window).on('resize', function() {
            $('.flexslider').each(function() {
                updateSliderItems($(this).data('flexslider'));
            });
        });
    });
    </script>

    @stack('scripts')
</body>
</html>
