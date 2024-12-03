<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Services Delivery Management System') }}</title>

    @vite(['resources/js/app.js'])

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />

    <!-- Summernote CSS -->
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://code.jquery.com/ui/1.13.3/jquery-ui.js"></script>

    <!-- Bootstrap JS (Popper included) -->
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

    <!-- Summernote JS -->
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>

    <!-- Other CSS and JS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/19.2.19/css/intlTelInput.css" integrity="sha512-MqSNU3ahHjuMbcLdeu1dngxB4OaOS7vnnLjnADs9E+0OhS0qk8CZ8nxvA+xyiU+Qy12+0vl2ove9F9ssWZpeuQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/19.2.19/js/intlTelInput.min.js" integrity="sha512-IxRltlh4EpT/il+hOEpD3g4jlXswVbSyH5vbqw6aF40CUsJTRAnr/7MxmPlKRsv9dYgBPcDSVNrf1P/keoBx+Q==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.3/themes/base/jquery-ui.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <style>
        .table td,
        .table th {
            vertical-align: middle;
            text-align: center;
        }

        .scroll-div {
            height: 330px;
            overflow: hidden;
            overflow-y: scroll;
        }

        .badge {
            padding: 0em 1em;
            font-size: 85%;
            line-height: 2;
        }

        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm no-print">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/admin') }}">
                    Lip Slay Home Salon
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto"></ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Authentication Links -->
                        @guest
                        @if (Route::has('login'))
                        <li class="nav-item">
                            <a class="dropdown-item" href="{{ route('login') }}">{{ __('Login') }}</a>
                        </li>
                        @endif
                        @if (Route::has('register'))
                        <li class="nav-item">
                            <a class="dropdown-item" href="{{ route('register') }}">{{ __('Register') }}</a>
                        </li>
                        @endif
                        @else

                        @if(Auth::user()->affiliate_program == null && auth()->user()->hasRole("Staff") && !auth()->user()->hasRole("Affiliate"))
                        <li class="nav-item">
                            <a class="nav-link" aria-current="page" href="{{ route('apply.affiliateProgram') }}">Join Affiliate Program</a>
                        </li>
                        @endif
                        <li class="nav-item">
                            <a class="nav-link" aria-current="page" href="/rota">Rota</a>
                        </li>
                        @if(auth()->user()->hasRole("Admin"))
                        
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                Joinee Program
                            </a>

                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="{{ route('affiliateProgram.index') }}">Affiliate</a>
                                <a class="dropdown-item" href="{{ route('freelancerProgram.index') }}">Freelancer</a>
                            </div>
                        </li>
                        @endif
                        @can('campaign-list')
                        <li class="nav-item">
                            <a class="nav-link" aria-current="page" href="{{ route('campaigns.index')}}">Campaigns</a>
                        </li>
                        @endcan
                        @can('chat-list')
                        <li class="nav-item">
                            <a class="nav-link" aria-current="page" href="{{ route('chats.index')}}">Customer Support</a>
                        </li>
                        @endcan
                        @can('menu-sales')
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                Sales
                            </a>

                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                @can('order-list')
                                <a class="dropdown-item" href="{{ route('orders.index') }}">Orders</a>
                                @endcan
                                @can('cash-collection-list')
                                <a class="dropdown-item" href="{{ route('cashCollection.index') }}">Cash Collections</a>
                                @endcan
                                @if(auth()->user()->hasRole("Staff"))
                                <a class="dropdown-item" href="{{ route('staffCashCollection') }}">Cash Collections</a>
                                @endif
                                @can('coupon-list')
                                <a class="dropdown-item" href="{{ route('coupons.index') }}">Coupons</a>
                                @endcan
                                @can('withdraw-list')
                                <a class="dropdown-item" href="{{ route('withdraws.index') }}">Withdraws</a>
                                @endcan
                            </div>
                        </li>
                        @endcan
                        @can('menu-catalog')
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                Catalog
                            </a>
                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">

                                @can('time-slot-list')
                                <a class="dropdown-item" href="{{ route('timeSlots.index') }}">Time Slots</a>
                                @endcan
                                @can('service-list')
                                <a class="dropdown-item" href="{{ route('services.index') }}">Services</a>
                                @endcan
                                @can('service-category-list')
                                <a class="dropdown-item" href="{{ route('serviceCategories.index') }}">Service Categories</a>
                                @endcan
                                @can('staff-zone-list')
                                <a class="dropdown-item" href="{{ route('staffZones.index') }}">Staff Zones</a>
                                @endcan
                                @can('staff-group-list')
                                <a class="dropdown-item" href="{{ route('staffGroups.index') }}">Staff Groups</a>
                                @endcan
                                @can('FAQs-list')
                                <a class="dropdown-item" href="{{ route('FAQs.index') }}">FAQs</a>
                                @endcan
                                @can('review-list')
                                <a class="dropdown-item" href="{{ route('reviews.index') }}">Reviews</a>
                                @endcan
                                @can('information-list')
                                <a class="dropdown-item" href="{{ route('information.index') }}">Information Page</a>
                                @endcan
                                @can('complaint-list')
                                <a class="dropdown-item" href="{{ route('complaints.index') }}">Complaints</a>
                                @endcan
                                @can('membership-plan-list')
                                <a class="dropdown-item" href="{{ route('membershipPlans.index') }}">Membership Plans</a>
                                @endcan
                            </div>
                        </li>
                        @endcan
                        @can('menu-store-config')
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                Store Config
                            </a>
                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                @can('setting-list')
                                <a class="dropdown-item" href="{{ route('settings.index') }}">Settings</a>
                                @endcan
                                @can('currency-list')
                                <a class="dropdown-item" href="{{ route('currencies.index') }}">Currencies</a>
                                @endcan
                                @can('holiday-list')
                                <a class="dropdown-item" href="/holidays">Holidays</a>
                                @endcan
                                @can('staff-holiday-list')
                                <a class="dropdown-item" href="{{ route('staffHolidays.index') }}">Staff Holiday</a>
                                <a class="dropdown-item" href="{{ route('longHolidays.index') }}">Long Holiday</a>
                                <a class="dropdown-item" href="{{ route('shortHolidays.index') }}">Short Holiday</a>
                                <a class="dropdown-item" href="{{ route('staffGeneralHolidays.index') }}">Staff General Holiday</a>
                                @endcan
                            </div>
                        </li>
                        @endcan
                        @can('menu-user')
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                Users
                            </a>

                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                @can('service-staff-list')
                                <a class="dropdown-item" href="{{ route('serviceStaff.index') }}">Staff</a>
                                @endcan
                                @can('customer-list')
                                <a class="dropdown-item" href="{{ route('customers.index') }}">Customer</a>
                                @endcan
                                @can('affiliate-list')
                                <a class="dropdown-item" href="{{ route('affiliates.index') }}">Affiliate</a>
                                @endcan
                                @can('manager-list')
                                <a class="dropdown-item" href="{{ route('managers.index') }}">Manager</a>
                                @endcan
                                @can('supervisor-list')
                                <a class="dropdown-item" href="{{ route('supervisors.index') }}">Supervisor</a>
                                @endcan
                                @can('assistant-supervisor-list')
                                <a class="dropdown-item" href="{{ route('assistantSupervisors.index') }}">Assistant Supervisor</a>
                                @endcan
                                @can('driver-list')
                                <a class="dropdown-item" href="{{ route('drivers.index') }}">Drivers</a>
                                @endcan
                                <a class="dropdown-item" href="{{ route('users.index') }}?role=Data Entry">Data Entry User</a>
                                <a class="dropdown-item" href="{{ route('users.index') }}?role=Support team">Support team</a>
                            </div>
                        </li>
                        @endcan
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                {{ Auth::user()->name }}
                            </a>

                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="{{ route('profile', Auth::user()->id) }}">Profile</a>
                                <a class="dropdown-item" target="_blank" href="/">Your Store</a>
                                @if(auth()->user()->hasRole("Admin"))
                                <a class="dropdown-item" href="{{ route('backups.index') }}">Database Backups</a>
                                @endif
                                @can('user-list')
                                <a class="dropdown-item" href="{{ route('users.index') }}">Users</a>
                                @endcan
                                @can('role-list')
                                <a class="dropdown-item" href="{{ route('roles.index') }}">Role</a>
                                @endcan
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </div>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('logout') }}" onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">{{ __('Logout') }}</a>
                        </li>
                        @endif

                    </ul>
                </div>

            </div>
        </nav>
        @include('site.layout.locationPopup')
        <main class="py-4">
                @yield('content')
        </main>
    </div>
    <footer class="text-muted">
        <div class="container">
            <button class="btn btn-secondary" onclick="window.history.back()">Back</button>
            <p class="float-right">
                {{ date('Y-m-d H:i:s') }}
                © 2023 {{ env('APP_NAME') }}
            </p>
        </div>
    </footer>

    @yield('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
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

        numberInputField.addEventListener("countrychange", function () {
        numberInputField.value = "";
        const selectedCountryData = numberInput.getSelectedCountryData();
        const countryCode = selectedCountryData.dialCode;
        numberCountryInputField.value = `+${countryCode}`;
        });

        whatsappInputField.addEventListener("countrychange", function () {
        whatsappInputField.value = "";
        const selectedCountryData = whatsappInput.getSelectedCountryData();
        const countryCode = selectedCountryData.dialCode;
        whatsappCountryInputField.value = `+${countryCode}`;
        });
    });
    </script>
    <script>
        $(document).ready(function() {
            $(".smsId").click(function() {
                $('.btn-close').css('display', 'none')
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
    <script>
        // Use a self-executing anonymous function to encapsulate your jQuery code
        (function($) {
            // Document ready function
            $(document).ready(function() {
                if (typeof $.fn.summernote !== 'undefined') {
                    $('#summernote').summernote({
                        tabsize: 2,
                        height: 250,
                        callbacks: {
                            onImageUpload: function(files) {
                                uploadImage(files[0]);
                            }
                        }
                    });
                } else {
                    console.error('Summernote is not loaded or initialized.');
                }
            });
    
            // Function to upload image
            function uploadImage(file) {
                console.log(typeof $.fn.summernote);
    
                if (typeof $.fn.summernote !== 'undefined') {
                    let data = new FormData();
                    data.append("file", file);
                    data.append("_token", "{{ csrf_token() }}");
    
                    $.ajax({
                        url: "{{ route('summerNote.upload') }}",
                        method: "POST",
                        data: data,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            $('#summernote').summernote('insertImage', response.url);
                        },
                        error: function(response) {
                            console.error(response);
                        }
                    });
                } else {
                    console.error('Summernote is not loaded or initialized.');
                }
            }
        })(jQuery); // Pass jQuery as an argument to the anonymous function
    </script>
    
</body>

</html>
