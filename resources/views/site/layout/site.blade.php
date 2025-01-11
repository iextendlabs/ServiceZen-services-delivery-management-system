<!doctype html>
<html lang="en">

<head>
    <base href="{{ env('APP_URL') }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ env('APP_NAME') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=EB+Garamond:ital,wght@0,400..800;1,400..800&family=Jost:ital,wght@0,100..900;1,100..900&family=Libre+Baskerville:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
    <link href="{{ asset('css/tailwind-output.css') }}?v={{ config('app.version') }}" rel="stylesheet">
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
<body>
    
    @include('site.layout.header')

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
        $(document).ready(function() {
            // Open mobile menu
            $('#mobile-menu-toggle').on('click', function() {
                $('#mobile-menu').css('transform', 'translateX(0)');
            });

            // Close mobile menu
            $('#mobile-menu-close').on('click', function() {
                $('#mobile-menu').css('transform', 'translateX(-100%)');
            });

            $("#menu-toggle").click(function() {
                $("#menu").toggleClass("opacity-0 invisible");
            });

            $(document).ready(function(){
                $("#mega-menu-dropdown-button").click(function(){
                    $("#mega-menu-dropdown").toggleClass("hidden");
                });
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
    {{-- <script>
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
    </script> --}}
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
