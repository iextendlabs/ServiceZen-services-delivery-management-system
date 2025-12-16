<!doctype html>
<html lang="en">

<head>
    <base href="{{ env('APP_URL') }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $metaTitle ?? config('app.name') }}</title>
    <meta name="description" content="{{ $metaDescription ?? 'Default description' }}">
    <meta name="keywords" content="{{ $metaKeywords ?? 'default,keywords' }}">

    <!-- Bootstrap core CSS -->
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css"
        integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk&family=Titillium+Web:wght@300&display=swap"
        rel="stylesheet">
    <link href="{{ asset('css/site.css') }}?v={{ config('app.version') }}" rel="stylesheet">

    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-TEMW2WSQE1"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/19.2.19/css/intlTelInput.css"
        integrity="sha512-MqSNU3ahHjuMbcLdeu1dngxB4OaOS7vnnLjnADs9E+0OhS0qk8CZ8nxvA+xyiU+Qy12+0vl2ove9F9ssWZpeuQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/19.2.19/js/intlTelInput.min.js"
        integrity="sha512-IxRltlh4EpT/il+hOEpD3g4jlXswVbSyH5vbqw6aF40CUsJTRAnr/7MxmPlKRsv9dYgBPcDSVNrf1P/keoBx+Q=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.3/themes/base/jquery-ui.css">
    
    <!-- Select dropdown -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'G-TEMW2WSQE1');
    </script>
    <!-- Owl Carousel CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    {!! $head_tag !!}

    {{-- AdSense Code from Section --}}
    @yield('adsense_head')
</head>
@if (session()->has('bookingData'))
    @php
        $cart_product = count(Session::get('bookingData'));
    @endphp
@else
    @php
        $cart_product = 0;
    @endphp
@endif

<style>
    .ui-autocomplete{
        border-radius: 20px !important;
        padding: 15px !important;
    }
    a{
        text-decoration: none !important;
    }
    .ui-menu-item :hover{
        border-color: #187485;
        border-radius: 10px;
        background-color: #187485;
        color: white !important;
    }
        
    .navbar-dark .navbar-nav .nav-link {
        color: rgba(255, 255, 255, 1) !important;
    }

    .sub-item {
        padding-left: 40px;
        /* Add indentation for subcategories */
        color: #888;
        /* Apply different color to subcategories */
    }

    .sub_category {
        display: none;
    }

    #app-link-section {
        background-color: #ff3366;
        /* Pink background */
        color: #ffffff;
        /* White text */
        padding: 20px;
        text-align: center;
    }

    #app-link-section p {
        font-size: 1.2em;
        margin-bottom: 10px;
    }

    #app-link-section a {
        display: inline-block;
        padding: 10px 20px;
        background-color: #ffffff;
        /* White background for the button */
        color: #ff3366;
        /* Pink text for the button */
        text-decoration: none;
        font-weight: bold;
        border-radius: 5px;
        transition: background-color 0.3s ease;
    }

    #app-link-section a:hover {
        background-color: #ff4d80;
        /* Lighter pink on hover */
    }
    .owl-nav {
        display: flex;
        justify-content: center;
        position: relative;
        top: -25px;
        margin-top: 10px
    }
    .owl-nav button {
        background: #9c9b9b !important;
        border: 1px solid #ccc !important;
        border-radius: 50%;
        width: 35px;
        height: 35px;
        text-align: center;
        line-height: 33px;
    }
    /* toggle button sizing */
    #searchToggle { width:40px; height:40px; display:flex; align-items:center; justify-content:center; }

    /* Animated floating bar: hidden by default (visibility + transform), shown via .open */
    #floatingSearchBar {
        position: fixed;
        left: 0;
        right: 0;
        top: 0; /* will be set dynamically on open */
        z-index: 60;

        /* animation & visibility */
        visibility: hidden;
        opacity: 0;
        transform: translateY(-8px) scale(0.98);
        transition: transform 240ms cubic-bezier(.2,.9,.2,1), opacity 240ms ease, visibility 240ms;
        pointer-events: none;

        /* visual */
        box-shadow: 0 6px 18px rgba(0,0,0,0.08);
        background: rgba(249, 243, 255, 0.98); /* subtle purple tint */
    }

    #floatingSearchBar.open {
        visibility: visible;
        opacity: 1;
        transform: translateY(0) scale(1);
        pointer-events: auto;
    }

    /* Decrease overall width: center inner content and cap max width */
    #floatingSearchBar .search-inner {
        width: 100%;
        max-width: 880px; /* reduced width */
        margin: 0 auto;
        align-items: center;
    }

    /* jQuery UI autocomplete styling */
    .ui-autocomplete {
        max-height: 320px;
        overflow-y: auto;
        overflow-x: hidden;
        padding: 6px;
        border-radius: 10px;
        box-shadow: 0 8px 30px rgba(0,0,0,0.12);
        border: 1px solid #e5e7eb;
        background: #fff;
    }
    .ui-menu-item-wrapper {
        padding: 8px 10px;
        border-radius: 8px;
        cursor: pointer;
    }
    .ui-menu-item-wrapper:hover {
        background: #f3e8ff; /* subtle purple hover */
        color: #4c1d95;
    }
    .ui-autocomplete .title {
        font-weight: 600;
        color: #111827;
    }
    .ui-autocomplete .meta {
        font-size: 12px;
        color: #6b7280;
        margin-top: 3px;
    }

    /* make sure appendTo inside the bar stays above */
    #floatingSearchBar .ui-autocomplete {
        z-index: 1000;
    }
</style>

<body>


    <header class="bg-white shadow-sm sticky top-0 z-40 ">
        <div class="container px-4 pt-2">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-8">
                    <a href="/" class="text-2xl font-bold text-purple-800">{{ env('APP_NAME') }}</a>
                    <nav class="hidden md:flex space-x-6">
                        <button id="servicesBtn" class="font-medium text-gray-700 hover:text-purple-800 focus:outline-none">
                            Services
                        </button>

                        <!-- Services Sidebar (hidden by default, shown on button click) -->
                        <div id="servicesSidebar" class="sidebar fixed top-0 left-0 h-full w-80 bg-white shadow-xl z-50 transform -translate-x-full transition-transform duration-300 ease-in-out md:block hidden">
                            <div class="p-6 border-b border-gray-200">
                                <div class="flex items-center justify-between">
                                    <h2 class="text-xl font-bold text-gray-800">All Services</h2>
                                    <button id="closeServicesSidebar" class="p-2 hover:bg-gray-100 rounded-lg">
                                        <i class="fas fa-times text-gray-600"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="p-6 space-y-4 overflow-y-scroll h-[calc(100vh-96px)]" style="scrollbar-width:none; -ms-overflow-style:none;">
                                <!-- Service Categories -->
                                <style>
                                    #servicesSidebar a {
                                        text-decoration: none !important;
                                    }
                                    #servicesSidebar summary::-webkit-details-marker {
                                        display: none;
                                    }
                                    #servicesSidebar summary {
                                        list-style: none;
                                    }
                                </style>
                                <div class="space-y-3">
                                    @foreach ($categories as $category)
                                        @if ($category->status == '1')
                                            @if ($category->id == 10 || $category->id == 11)
                                                @continue
                                            @endif
                                            @if (is_null($category->parent_id) && $category->childCategories->isNotEmpty())
                                                <div class="p-4 bg-pink-50 rounded-lg cursor-pointer hover:bg-pink-100 transition-colors">
                                                    <details class="group">
                                                        <summary class="flex items-center space-x-3 font-medium text-gray-800 cursor-pointer">
                                                            <i class="fas fa-cut text-pink-600 text-lg"></i>
                                                            <span>{{ $category->title }}</span>
                                                            <svg class="ml-auto w-4 h-4 text-gray-400 group-open:rotate-90 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                                                        </summary>
                                                        <ul class="ml-4 mt-2 space-y-1">
                                                            @foreach ($category->childCategories as $subcategory)
                                                                @if ($subcategory->status == '1')
                                                                    @if ($subcategory->childCategories->isNotEmpty())
                                                                        <li>
                                                                            <details class="group">
                                                                                <summary class="flex items-center space-x-2 text-gray-600 hover:text-purple-800 cursor-pointer">
                                                                                    <span>{{ $subcategory->title }}</span>
                                                                                    <svg class="ml-auto w-3 h-3 text-gray-400 group-open:rotate-90 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                                                                                </summary>
                                                                                <ul class="ml-4 mt-1 space-y-1">
                                                                                    @foreach ($subcategory->childCategories as $child_category)
                                                                                        @if ($child_category->status == '1')
                                                                                            <li>
                                                                                                <a class="text-gray-500 hover:text-purple-800" href="{{ route('category.show',$child_category->slug) }}">- {{ $child_category->title }}</a>
                                                                                            </li>
                                                                                        @endif
                                                                                    @endforeach
                                                                                    <li><a class="text-gray-700 hover:text-purple-800" href="{{ route('category.show',$subcategory->slug) }}">Show All {{ $subcategory->title }}</a></li>
                                                                                </ul>
                                                                            </details>
                                                                        </li>
                                                                    @else
                                                                        <li>
                                                                            <a class="text-gray-600 hover:text-purple-800" href="{{ route('category.show',$subcategory->slug) }}">{{ $subcategory->title }}</a>
                                                                        </li>
                                                                    @endif
                                                                @endif
                                                            @endforeach
                                                            <li><a class="text-gray-700 hover:text-purple-800" href="{{ route('category.show',$category->slug) }}">Show All {{ $category->title }}</a></li>
                                                        </ul>
                                                    </details>
                                                </div>
                                            @elseif (is_null($category->parent_id) && $category->childCategories->isEmpty())
                                                <div class="p-4 bg-pink-50 rounded-lg cursor-pointer hover:bg-pink-100 transition-colors">
                                                    <a class="flex items-center space-x-3 font-medium text-gray-800" href="{{ route('category.show',$category->slug) }}">
                                                        <i class="fas fa-cut text-pink-600 text-lg"></i>
                                                        <span>{{ $category->title }}</span>
                                                    </a>
                                                </div>
                                            @endif
                                        @endif
                                    @endforeach
                                </div>
                                <div class="pt-6">
                                    <a href="{{ route('categories.index') }}" class="w-full block px-4 py-3 bg-purple-800 text-white rounded-lg hover:bg-purple-900 transition-colors font-medium text-center">
                                        View All Services
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="relative">
                            <button class="flex items-center space-x-1 font-medium text-gray-700 hover:text-purple-800 focus:outline-none">
                                <span>
                                    @if (isset($address))
                                        <a id="change-address"> <i class="fas fa-map-marker-alt text-sm "></i>
                                            {{ $address['area'] }} {{ $address['city'] }}</a>
                                    @else
                                        <a id="change-address"> <i class="fas fa-map-marker-alt text-sm"></i> Set your
                                            location</a>
                                    @endif
                                </span>
                                <i class="fas fa-chevron-down text-xs"></i>
                            </button>
                        </div>
                    </nav>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="relative">
                        <button id="searchToggle" class="p-1 rounded-full bg-purple-800 text-white hover:bg-purple-900 focus:outline-none" aria-label="Open search">
                            <i class="fas fa-search ml-1"></i>
                        </button>
                    </div>

                    <!-- Floating search bar (animated & narrower) -->
                    <div id="floatingSearchBar" class="bg-purple-100 px-4">
                        <div class="search-inner container mx-auto flex items-center gap-3">
                            <div class="flex-1 relative">
                                <input id="search_product" name="search_product" type="text" placeholder="Search services..." autocomplete="off"
                                    class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-0 focus:border-purple-500 bg-transparent" />
                                <i class="fas fa-search absolute right-3 top-2.5 text-gray-400"></i>

                                <!-- Autocomplete dropdown will be injected by jQuery UI -->
                            </div>
                            <button id="closeFloatingSearch" class="px-4 py-2 bg-gray-100 rounded-lg hover:bg-gray-200">Close</button>
                        </div>
                    </div>
                    <a href="{{ config('app.packageUrl') }}" class="font-medium text-gray-700 hover:text-purple-800">Packages</a>
                    <a href="{{ config('app.addOnUrl') }}" class="font-medium text-gray-700 hover:text-purple-800">Beauty Add-Ons</a>                        
                    <a href="{{ route('cart.index') }}" class="p-2 text-gray-700 hover:text-purple-800">
                        <i class="far fa-heart text-lg"></i>
                    </a>
                    <a href="{{ route('customerProfile.edit', auth()->user()->id ?? 0) }}" class="p-2 text-gray-700 hover:text-purple-800">
                        <i class="fas fa-user text-lg"></i>
                    </a>
                    <a href="{{route('checkBooking')}}" class="px-4 py-2 bg-purple-800 text-white rounded-lg hover:bg-purple-900 transition-colors">
                        Book Now
                    </a>
                </div>
            </div>
            <div class="mt-4 flex flex-wrap gap-4">
                <nav class="flex flex-wrap gap-4">
                    @if (count($top_information_page) > 0)
                        <div class="relative group">
                            <button class="font-medium text-gray-700 hover:text-purple-800 flex items-center">
                                Other <i class="fas fa-chevron-down text-xs ml-1"></i>
                            </button>
                            <div class="absolute left-0 mt-2 w-48 bg-white border rounded-lg shadow-lg z-10 hidden group-hover:block">
                                @foreach ($top_information_page as $page)
                                    <a class="block px-4 py-2 text-gray-700 hover:bg-purple-100" href="{{ route('siteInformationPage.show', $page->id) }}">{{ $page->name }}</a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </nav>
            </div>
        </div>
        @include('site.layout.locationPopup')
        <div id="addToCartPopup"></div>
        <div id="quotePopup"></div>
        <!-- Right side drawer for booking/quote (injected via AJAX) -->
        <div id="rightSideDrawer" class="fixed right-0 top-0 h-full w-0 overflow-hidden z-60 transition-all duration-300 bg-white shadow-xl">
            <div class="flex items-center justify-between p-4 border-b">
                <h3 id="rightSideDrawerTitle" class="text-lg font-semibold">Details</h3>
                <button id="closeRightDrawer" class="p-2 text-gray-600 hover:text-gray-800"><i class="fas fa-times"></i></button>
            </div>
            <div id="rightSideDrawerContent" class="p-4 overflow-y-auto h-[calc(100vh-64px)]"></div>
        </div>
        <style>
            #rightSideDrawer.open { width: 420px; }
            @media (max-width: 768px) { #rightSideDrawer.open { width: 100%; } }

            /* Hide native scrollbar but keep scrolling functional */
            #rightSideDrawerContent {
                -ms-overflow-style: none; /* IE and Edge */
                scrollbar-width: none; /* Firefox */
            }
            #rightSideDrawerContent::-webkit-scrollbar { display: none; } /* WebKit */
        </style>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var drawer = document.getElementById('rightSideDrawer');
                var closeBtn = document.getElementById('closeRightDrawer');
                if (closeBtn && drawer) {
                    closeBtn.addEventListener('click', function() {
                        drawer.classList.remove('open');
                        setTimeout(function(){ document.getElementById('rightSideDrawerContent').innerHTML = ''; }, 300);
                    });

                    // close when clicking outside the drawer
                    document.addEventListener('click', function(e) {
                        if (!drawer.classList.contains('open')) return;
                        var target = e.target;
                        if (!drawer.contains(target) && !target.closest('[onclick^="openBookingPopup"]') && !target.closest('[onclick^="openQuotePopup"]')) {
                            drawer.classList.remove('open');
                            setTimeout(function(){ document.getElementById('rightSideDrawerContent').innerHTML = ''; }, 300);
                        }
                    });
                }
            });
        </script>
    </header>

    <main role="main">

        @yield('content')

    </main>

    <footer class="bg-gray-900 text-white py-12">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-xl font-bold mb-4 text-white">{{ env('APP_NAME') }}</h3>
                    <p class="text-white-400 text-sm">Your trusted marketplace for professional home services.</p>
                </div>
                
                <div>
                    <h4 class="font-semibold mb-4 text-white">Quick Links</h4>
                    <ul class="space-y-2 text-sm text-white-400">
                        @if (count($bottom_information_page) > 0)
                            @foreach ($bottom_information_page as $page)
                                <li>
                                    <a href="{{ route('siteInformationPage.show', $page->id) }}" 
                                       class="hover:text-purple-400 transition-colors duration-200">{{ $page->name }}</a>
                                </li>
                            @endforeach
                        @endif
                    </ul>
                </div>
                
                <div>
                    <h4 class="font-semibold mb-4 text-white">Support</h4>
                    <ul class="space-y-2 text-sm text-white-400">
                        <li><a href="/privacyPolicy" class="hover:text-purple-400 transition-colors duration-200">Privacy Policy</a></li>
                        <li><a href="/termsCondition" class="hover:text-purple-400 transition-colors duration-200">Terms and Conditions</a></li>
                        <li><a href="{{ route('siteFAQs.index') }}" class="hover:text-purple-400 transition-colors duration-200">FAQs</a></li>
                        <li><a href="{{ route('contactUs') }}" class="hover:text-purple-400 transition-colors duration-200">Contact</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="font-semibold mb-4 text-white">Reviews & Support</h4>
                    <ul class="space-y-2 text-sm text-white-400">
                        <li><a href="{{ route('siteReviews.index') }}" class="hover:text-purple-400 transition-colors duration-200">Customer Reviews</a></li>
                        <li><a href="{{ route('contactUs') }}" class="hover:text-purple-400 transition-colors duration-200">Get Support</a></li>
                    </ul>
                    <div class="flex  space-x-6 mt-8 mb-4">
                        <a href="#" class="text-gray-400 hover:text-purple-400 transition-colors duration-200">
                            <i class="fab fa-facebook-f text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-purple-400 transition-colors duration-200">
                            <i class="fab fa-instagram text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-purple-400 transition-colors duration-200">
                            <i class="fab fa-twitter text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-purple-400 transition-colors duration-200">
                            <i class="fab fa-linkedin-in text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-purple-400 transition-colors duration-200">
                            <i class="fab fa-youtube text-xl"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-sm text-gray-400">
                © {{ date('Y') }} {{ env('APP_NAME') }}. All rights reserved.
            </div>
        </div>
    </footer>
    <!-- Bootstrap core JavaScript
    ================================================== -->
    <script src="{{ asset('js/vendor/popper.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/vendor/holder.min.js') }}"></script>
    <script src="{{ asset('js/popup.js') }}?v={{ config('app.version') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script
        src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAP_KEY') }}&libraries=places&callback=mapReady&type=address">
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>

    <!-- Select dropdown -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>
    <script>
        // Sidebar open/close logic
        document.addEventListener('DOMContentLoaded', function() {
            var sidebar = document.getElementById('servicesSidebar');
            var openBtn = document.getElementById('servicesBtn');
            var closeBtn = document.getElementById('closeServicesSidebar');
            if (openBtn && sidebar) {
                openBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    sidebar.classList.remove('-translate-x-full');
                });
            }
            if (closeBtn && sidebar) {
                closeBtn.addEventListener('click', function() {
                    sidebar.classList.add('-translate-x-full');
                });
            }
            // Optional: close sidebar when clicking outside
            document.addEventListener('click', function(event) {
                if (sidebar && !sidebar.classList.contains('-translate-x-full')) {
                    if (!sidebar.contains(event.target) && event.target !== openBtn) {
                        sidebar.classList.add('-translate-x-full');
                    }
                }
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggle = document.getElementById('searchToggle');
            const bar = document.getElementById('floatingSearchBar');
            const header = document.querySelector('header');
            const input = document.getElementById('search_product');
            const closeBtn = document.getElementById('closeFloatingSearch');

            function setBarTop() {
                if (!header || !bar) return;
                const rect = header.getBoundingClientRect();
                // place it right under header (account for page scroll)
                const top = window.scrollY + rect.bottom;
                bar.style.top = top + 'px';
            }

            function openBar() {
                setBarTop();
                bar.classList.add('open');
                // focus after visible
                setTimeout(() => {
                    if (input) input.focus();
                    if (input && window.jQuery && $(input).autocomplete) {
                        $(input).autocomplete('search', input.value || '');
                    }
                }, 40);
            }

            function closeBar() {
                bar.classList.remove('open');
            }

            toggle.addEventListener('click', function (e) {
                e.stopPropagation();
                if (!bar) return;
                if (bar.classList.contains('open')) closeBar();
                else openBar();
            });

            if (closeBtn) {
                closeBtn.addEventListener('click', function (e) {
                    e.stopPropagation();
                    closeBar();
                });
            }

            // Close when clicking outside
            document.addEventListener('click', function (e) {
                if (!bar) return;
                if (bar.classList.contains('open') && !bar.contains(e.target) && e.target !== toggle) {
                    closeBar();
                }
            });

            // Close on Escape
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') closeBar();
            });

            // Reposition on resize/scroll (keeps it right under header)
            window.addEventListener('resize', function () {
                if (bar.classList.contains('open')) setBarTop();
            });
            window.addEventListener('scroll', function () {
                if (bar.classList.contains('open')) setBarTop();
            });

            // Initialize jQuery UI Autocomplete with remote source (keeps existing behavior)
            if (window.jQuery && $.ui && $(input).autocomplete) {
                $(input).autocomplete({
                    minLength: 0, // allow empty search to show suggestions on click/open
                    delay: 150,
                    source: function(request, response) {
                        $.ajax({
                            url: "/service-list",
                            method: "GET",
                            data: { term: request.term },
                            success: function(data) {
                                const mapped = (data || []).map(function(item) {
                                    if (typeof item === 'string') {
                                        return { label: item, value: item };
                                    }
                                    return {
                                        label: item.label || item.name || item.title || item.value,
                                        value: item.value || item.slug || item.label || item.name,
                                        url: item.url || item.link || null,
                                        meta: item.meta || item.description || null
                                    };
                                });
                                response(mapped);
                            },
                            error: function() {
                                response([]);
                            }
                        });
                    },
                    appendTo: "#floatingSearchBar",
                    focus: function(event, ui) {
                        event.preventDefault();
                    },
                    select: function(event, ui) {
                        event.preventDefault();
                        const item = ui.item;
                        if (item && item.url) {
                            window.location.href = item.url;
                        } else {
                            const query = encodeURIComponent(item.value || item.label || '');
                            window.location.href = '/search?query=' + query;
                        }
                    }
                }).autocomplete("instance")._renderItem = function(ul, item) {
                    const $li = $("<li>");
                    const $wrap = $("<div>").addClass("ui-menu-item-wrapper");
                    const $title = $("<div>").addClass("title").text(item.label || item.value);
                    $wrap.append($title);
                    if (item.meta) {
                        $wrap.append($("<div>").addClass("meta").text(item.meta));
                    }
                    $li.append($wrap).appendTo(ul);
                    return $li;
                };

                // show dropdown when input is clicked (even if empty)
                $(input).on('click', function(e) {
                    e.stopPropagation();
                    $(this).autocomplete('search', $(this).val() || '');
                });

                // keyboard: enter triggers select if suggestion highlighted, else go to search results
                $(input).on('keydown', function(e) {
                    if (e.key === 'Enter') {
                        const val = $(this).val().trim();
                        if (val.length) {
                            setTimeout(function() {
                                const active = $('.ui-menu .ui-state-focus');
                                if (!active.length) {
                                    window.location.href = '/search?query=' + encodeURIComponent(val);
                                }
                            }, 10);
                        }
                    }
                });
            }
        });
    </script>    
    <!-- Owl Carousel Initialization -->
    <script>
        $(document).ready(function(){
            $(".owl-carousel").owlCarousel({
                loop: true,
                margin: 15,
                nav: true,  // Hide navigation arrows
                dots: false,  // Show dots only
                autoplay: true,
                autoplayTimeout: 10000,
                items: 3,
                responsive: {
                    0: {
                        items: 1
                    },
                    600: {
                        items: 3
                    },
                    1000: {
                        items: 3
                    }
                },
                navText: [
                    '<i class="fa fa-chevron-left"></i>', // Left navigation arrow
                    '<i class="fa fa-chevron-right"></i>' // Right navigation arrow
                ]
            });
        });
    </script>
    <script>
        $(document).ready(function($) {
            var availableTags = [];

            $.ajax({
                method: "GET",
                url: "/service-list",
                success: function(response) {
                    availableTags = response;
                    startAutocomplete(availableTags);
                }
            });

            function startAutocomplete(tags) {
                var showMore = false;

                $("#search_product").autocomplete({
                    source: function(request, response) {
                        var results = $.ui.autocomplete.filter(tags, request.term);
                        if (!showMore) {
                            results = results.slice(0, 15);
                        }
                        response(results);
                    },
                    
                }).autocomplete("instance")._renderItem = function(ul, item) {
                    return $("<li>")
                        .append("<div>" + item.label + "</div>")
                        .appendTo(ul);
                };
            }
        });
    </script>
    <script>
        var Dropdowns = function() {
            var t = $(".dropdown"),
                e = $(".dropdown-menu"),
                r = $(".dropdown-menu .dropdown-menu");
            $(".dropdown-menu .dropdown-toggle").on("click", function() {
                    var a;
                    return (a = $(this)).closest(t).siblings(t).find(e).removeClass("show"),
                        a.next(r).toggleClass("show"),
                        !1
                }),
                t.on("hide.bs.dropdown", function() {
                    var a, t;
                    a = $(this),
                        (t = a.find(r)).length && t.removeClass("show")
                })
        }()
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const numberInputField = document.querySelector("#number");
            const whatsappInputField = document.querySelector("#whatsapp");
            const numberCountryInputField = document.querySelector("#number_country_code");
            const whatsappCountryInputField = document.querySelector("#whatsapp_country_code");

            const numberInput = window.intlTelInput(numberInputField, {
                showSelectedDialCode: true,
                initialCountry: "ae",
                utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/19.2.19/js/utils.js?1707906286003",
            });

            const whatsappInput = window.intlTelInput(whatsappInputField, {
                showSelectedDialCode: true,
                initialCountry: "ae",
                utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/19.2.19/js/utils.js?1707906286003",
            });

            const initialNumberCountryCode = numberInput.getSelectedCountryData().dialCode;
            numberCountryInputField.value = `+${initialNumberCountryCode}`;

            const initialWhatsappCountryCode = whatsappInput.getSelectedCountryData().dialCode;
            whatsappCountryInputField.value = `+${initialWhatsappCountryCode}`;

            numberInputField.addEventListener("countrychange", function() {
                numberInputField.value = "";
                const selectedCountryData = numberInput.getSelectedCountryData();
                const countryCode = selectedCountryData.dialCode;
                numberCountryInputField.value = `+${countryCode}`;
            });

            whatsappInputField.addEventListener("countrychange", function() {
                whatsappInputField.value = "";
                const selectedCountryData = whatsappInput.getSelectedCountryData();
                const countryCode = selectedCountryData.dialCode;
                whatsappCountryInputField.value = `+${countryCode}`;
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#number').on('input', function() {
                $(this).val($(this).val().replace(/\D/g, ''));
            });

            $('#whatsapp').on('input', function() {
                $(this).val($(this).val().replace(/\D/g, ''));
            });
        });
    </script>
    </script>
    <script>
        $(document).ready(function() {
            if (navigator.userAgent.match(/Android/i)) {
                var appLinkSection = '<section id="app-link-section">\
                                                                                <p>🚀 Elevate your experience with our Android App! 🚀</p>\
                                                                                <a target="_blank" href="https://play.google.com/store/apps/details?id=com.lipslay.Customerapp" >Download Now</a>\
                                                                              </section>';
                $('body').prepend(appLinkSection);
            } else if (navigator.userAgent.match(/iPhone/i)) {
                var appLinkSection = '<section id="app-link-section">\
                                                                                <p>🚀 Elevate your experience with our iPhone App! 🚀</p>\
                                                                                <a target="_blank" href="https://apps.apple.com/be/app/lipslay/id6477719247">Download Now</a>\
                                                                              </section>';
                $('body').prepend(appLinkSection);
            }
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#number').on('input', function() {
                $(this).val($(this).val().replace(/\D/g, ''));
            });

            $('#whatsapp').on('input', function() {
                $(this).val($(this).val().replace(/\D/g, ''));
            });
        });
    </script>
</body>

</html>
