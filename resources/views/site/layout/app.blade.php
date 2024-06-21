<!doctype html>
<html lang="en">

<head>
    <base href="{{ env('APP_URL') }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ env('APP_NAME') }}</title>

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
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'G-TEMW2WSQE1');
    </script>
    {!! $head_tag !!}
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
</style>

<body>

    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark" style="background-color:#0c5460!important">
            <a class="navbar-brand" style="font-size: 30px;font-weight:bold;font-family: 'Titillium Web', sans-serif;"
                href="/">{{ env('APP_NAME') }}</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">

                    @if (isset($address))
                        <li class="nav-item">
                            <a class="nav-link" id="change-address"> <i class="fa fa-map-marker "></i>
                                {{ $address['area'] }} {{ $address['city'] }}</a>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" id="change-address"> <i class="fa fa-map-marker "></i> Set your
                                location</a>
                        </li>
                    @endif
                    {{-- <li class="nav-item">
                        <a class="nav-link" href="/bookingStep">Booking</a>
                    </li> --}}
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('checkBooking')}}">Availability</a>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Services
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                            @foreach ($categories as $category)
                                @if ($category->status == '1')
                                    @if ($category->id == 10 || $category->id == 11)
                                        @continue
                                    @endif
                                    @if (is_null($category->parent_id) && $category->childCategories->isNotEmpty())
                                        <a class="dropdown-item dropdown-toggle" href="#" id="subDropdown"
                                            role="button" data-toggle="dropdown" aria-haspopup="true"
                                            aria-expanded="false">
                                            {{ $category->title }}
                                        </a>
                                        <div class="dropdown-menu" aria-labelledby="subDropdown"
                                            style="position: relative;">
                                            @foreach ($category->childCategories as $subcategory)
                                                @if ($subcategory->status == '1')
                                                    @if ($subcategory->childCategories->isNotEmpty())
                                                        <a class="dropdown-item dropdown-toggle" href="#"
                                                            id="subDropdown" role="button" data-toggle="dropdown"
                                                            aria-haspopup="true" aria-expanded="false">
                                                            {{ $subcategory->title }}
                                                        </a>
                                                        <div class="dropdown-menu" aria-labelledby="subDropdown"
                                                            style="position: relative;">
                                                            @foreach ($subcategory->childCategories as $child_category)
                                                                @if ($child_category->status == '1')
                                                                    <a class="dropdown-item"
                                                                        href="\?id={{ $child_category->id }}">-
                                                                        {{ $child_category->title }}</a>
                                                                @endif
                                                            @endforeach
                                                            <div class="dropdown-divider"></div>
                                                            <a class="dropdown-item"
                                                                href="\?id={{ $subcategory->id }}">Show All
                                                                {{ $subcategory->title }}</a>
                                                        </div>
                                                    @else
                                                        @if ($subcategory->childCategories->isEmpty())
                                                            <a class="dropdown-item"
                                                                href="\?id={{ $subcategory->id }}">{{ $subcategory->title }}</a>
                                                        @endif
                                                    @endif
                                                @endif
                                            @endforeach
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item" href="\?id={{ $category->id }}">Show All
                                                {{ $category->title }}</a>
                                        </div>
                                    @else
                                        @if (is_null($category->parent_id) && $category->childCategories->isEmpty())
                                            <a class="dropdown-item"
                                                href="\?id={{ $category->id }}">{{ $category->title }}</a>
                                        @endif
                                    @endif
                                @endif
                            @endforeach
                            <a class="dropdown-item text-center"
                                href="{{ route('categories.index') }}"><b>All</b></a>
                        </div>
                    </li>
                    <li class="nav-item">
                        <a href="{{ config('app.packageUrl') }}" class="nav-link">Packages</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ config('app.addOnUrl') }}" class="nav-link">Beauty Add-Ons</a>
                    </li>


                    <li class="nav-item">
                        <a href="{{ route('cart.index') }}" class="nav-link">Cart({{ $cart_product }})</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Account
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                            @guest
                                <a class="dropdown-item" href="{{ route('customer.login') }}">Login</a>
                                <a class="dropdown-item" href="{{ route('customer.registration') }}">Register</a>
                                <a class="dropdown-item"
                                    href="{{ route('customer.registration') }}?type=affiliate">Register as
                                    Affiliate</a>
                            @else
                                <a class="dropdown-item"
                                    href="{{ route('customerProfile.edit', auth()->user()->id) }}">Profile</a>
                                @if (Auth::user()->hasRole('Affiliate'))
                                    <a class="dropdown-item" href="{{ route('affiliate_dashboard.index') }}">Affiliate
                                        Dashboard</a>
                                @endif
                                @if (Auth::user()->affiliate_program == null && !Auth::user()->hasRole('Affiliate'))
                                    <a class="dropdown-item" href="{{ route('apply.affiliateProgram') }}">Join Affiliate
                                        Program</a>
                                @endif
                                <a class="dropdown-item" href="{{ route('siteComplaints.index') }}">My Complaint</a>
                                <a class="dropdown-item" href="{{ route('order.index') }}">Orders</a>
                                <a class="dropdown-item" href="{{ route('customer.logout') }}">Logout</a>
                                <a class="dropdown-item" href="/deleteAccount">Delete Account</a>
                            @endguest
                        </div>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('siteFAQs.index') }}" class="nav-link">FAQs</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('siteReviews.index') }}">Reviews</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('contactUs') }}">Contact</a>
                    </li>

                    @if (count($top_information_page) > 0)
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownOther"
                                role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Other
                            </a>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownOther">
                                @foreach ($top_information_page as $page)
                                    <a class="dropdown-item"
                                        href="{{ route('siteInformationPage.show', $page->id) }}">{{ $page->name }}</a>
                                @endforeach
                            </div>
                        </li>
                    @endif
                </ul>
            </div>
        </nav>
        @include('site.layout.locationPopup')
        <div id="addToCartPopup"></div>
    </header>

    <main role="main">

        @yield('content')

    </main>

    <div class="container">
        <footer class="text-muted">

            <div class="text-center p-3">
                @if (count($bottom_information_page) > 0)
                    @foreach ($bottom_information_page as $page)
                        <a href="{{ route('siteInformationPage.show', $page->id) }}"
                            class="text-dark">{{ $page->name }}</a> |
                    @endforeach
                @endif
                <a href="https://lipslay.com/privacyPolicy" class="text-dark">Privacy Policy</a> |
                <a href="https://lipslay.com/termsCondition" class="text-dark">Terms and Conditions</a>
            </div>

            <p class="float-right">
                © 2023 {{ env('APP_NAME') }}

            </p>
        </footer>
    </div>

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <script src="./js/vendor/popper.min.js"></script>
    <script src="./js/bootstrap.min.js"></script>
    <script src="./js/vendor/holder.min.js"></script>
    <script src="{{ asset('js/popup.js') }}?v={{ config('app.version') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script
        src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAP_KEY') }}&libraries=places&callback=mapReady&type=address">
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
