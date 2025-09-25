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

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://code.jquery.com/ui/1.13.3/jquery-ui.js"></script>

    <!-- Bootstrap JS (Popper included) -->
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

    <!-- Other CSS and JS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/19.2.19/css/intlTelInput.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/19.2.19/js/intlTelInput.min.js" integrity="sha512-IxRltlh4EpT/il+hOEpD3g4jlXswVbSyH5vbqw6aF40CUsJTRAnr/7MxmPlKRsv9dYgBPcDSVNrf1P/keoBx+Q==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.3/themes/base/jquery-ui.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>

    <!-- Summernote -->
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/DiemenDesign/summernote-image-attributes/summernote-image-attributes.js"></script>
    
    <!-- Select dropdown -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">


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

        .select2-container .select2-selection--single {
            height: calc(2.25rem + 2px); /* Match Bootstrap form-control height */
            padding: .375rem .75rem;
            border: 1px solid #ced4da;
            border-radius: .25rem;
            font-size: 1rem;
            line-height: 1.5;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 1.5;
            padding-left: 0;
        }

        .select2-container--default .select2-selection--single:focus {
            outline: none;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, .25); /* Bootstrap focus shadow */
        }

        .select2-container .select2-search__field {
            width: 100% !important;
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #ced4da;
        }
        .bootstrap-select .dropdown-menu.inner {
            overflow-y: auto !important;
            max-height: 300px !important; /* Slightly less than container */
        }
        .brand-stack {
            min-width: 160px;
        }
        .brand-stack .btn-store-view {
            width: 100%;
            box-sizing: border-box;
            text-align: left;
        }
    </style>
</head>

<body>
    <!-- Header Start -->
    <header class="bg-light shadow-sm mb-3">
        <div class="container-fluid">
            <div class="d-flex flex-wrap align-items-center justify-content-between py-2">
                <!-- Brand & Store View -->
                <div class="d-flex flex-column align-items-center brand-stack mb-0">
                    <a class="navbar-brand p-0 text-center fw-bold fs-4 text-primary" href="{{ url('/admin') }}">
                        Lipslay Admin
                    </a>
                    @auth
                    <a class="btn btn-outline-primary btn-sm mt-2 btn-store-view w-100" href="https://lipslay.com/?cache=false" target="_blank">
                        <i class="fas fa-store"></i> Store View
                    </a>
                    @endauth
                </div>
                <!-- Auth/Guest Links -->
                <div>
                    @guest
                        @if (Route::has('login'))
                        <a class="btn btn-outline-primary me-2" href="{{ route('login') }}">
                            <i class="fas fa-sign-in-alt me-2"></i> {{ __('Login') }}
                        </a>
                        @endif
                        @if (Route::has('register'))
                        <a class="btn btn-primary" href="{{ route('register') }}">
                            <i class="fas fa-user-plus me-2"></i> {{ __('Register') }}
                        </a>
                        @endif
                    @else
                        <div class="dropdown d-inline-block">
                            <a class="btn btn-outline-secondary dropdown-toggle" href="#" id="profileMenuHeader" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user-circle me-2"></i> {{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileMenuHeader">
                                <li>
                                    <a class="dropdown-item" href="{{ route('profile', Auth::user()->id) }}">
                                        <i class="fas fa-id-badge me-2"></i> Profile
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" target="_blank" href="/sitemap.xml">
                                        <i class="fas fa-sitemap me-2"></i> Sitemap
                                    </a>
                                </li>
                                @if(auth()->user()->hasRole("Admin"))
                                <li>
                                    <a class="dropdown-item" href="{{ route('backups.index') }}">
                                        <i class="fas fa-database me-2"></i> Database Backups
                                    </a>
                                </li>
                                @endif
                                @can('user-list')
                                <li>
                                    <a class="dropdown-item" href="{{ route('users.index') }}">
                                        <i class="fas fa-users me-2"></i> Users
                                    </a>
                                </li>
                                @endcan
                                @can('role-list')
                                <li>
                                    <a class="dropdown-item" href="{{ route('roles.index') }}">
                                        <i class="fas fa-user-tag me-2"></i> Role
                                    </a>
                                </li>
                                @endcan
                                <li>
                                    <form id="logout-form-header" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                    <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form-header').submit();">
                                        <i class="fas fa-sign-out-alt me-2"></i> {{ __('Logout') }}
                                    </a>
                                </li>
                            </ul>
                        </div>
                    @endguest
                </div>
            </div>
        </div>
    </header>
    <!-- Header End -->

    <div id="app">
        <div class="container-fluid">
            <div class="row">
                @guest
                @else
                <aside id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse sidebar-sticky no-print shadow-sm opencart-sidebar" style="min-height: 100vh;">
                    <div class="position-sticky pt-3">
                        <ul class="nav flex-column opencart-menu">
                            
                                @if(auth()->user()->hasRole('Staff'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('stripe.staff.form') }}">
                                        <button class="btn btn-primary w-100"><i class="fas fa-wallet me-2"></i> Add Funds</button>
                                    </a>
                                </li>
                                @endif
                                @if(Auth::user()->affiliate_program == null && auth()->user()->hasRole("Staff") && !auth()->user()->hasRole("Affiliate"))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('apply.affiliateProgram') }}">
                                        <i class="fas fa-user-tag me-2"></i> Join Affiliate Program
                                    </a>
                                </li>
                                @endif
                                <li class="nav-item">
                                    <a class="nav-link" href="/rota">
                                        <i class="fas fa-calendar-alt me-2"></i> Rota
                                    </a>
                                </li>
                                @can('menu-new-joinee')
                                <li class="nav-item">
                                    <a class="nav-link collapsed" data-bs-toggle="collapse" href="#joineeProgramMenu" role="button" aria-expanded="false" aria-controls="joineeProgramMenu">
                                        <i class="fas fa-users me-2"></i> Joinee Program <span class="float-end"><i class="fas fa-angle-down"></i></span>
                                    </a>
                                    <div class="collapse" id="joineeProgramMenu">
                                        <ul class="nav flex-column ms-3">
                                            @can('affiliate-program-list')
                                            <li><a class="nav-link" href="{{ route('affiliateProgram.index') }}"><i class="fas fa-user-friends me-2"></i> Affiliate</a></li>
                                            @endcan
                                            @can('freelancer-program-list')
                                            <li><a class="nav-link" href="{{ route('freelancerProgram.index') }}"><i class="fas fa-user-clock me-2"></i> Freelancer</a></li>
                                            @endcan
                                        </ul>
                                    </div>
                                </li>
                                @endcan
                                @can('menu-maintenance')
                                <li class="nav-item">
                                    <a class="nav-link collapsed" data-bs-toggle="collapse" href="#maintenanceMenu" role="button" aria-expanded="false" aria-controls="maintenanceMenu">
                                        <i class="fas fa-tools me-2"></i> Maintenance <span class="float-end"><i class="fas fa-angle-down"></i></span>
                                    </a>
                                    <div class="collapse" id="maintenanceMenu">
                                        <ul class="nav flex-column ms-3">
                                            <li><a class="nav-link" href="{{ route('logs.view', ['file' => 'laravel']) }}"><i class="fas fa-file-alt me-2"></i> Laravel Log</a></li>
                                            <li><a class="nav-link" href="{{ route('logs.view', ['file' => 'app_error']) }}"><i class="fas fa-exclamation-triangle me-2"></i> Error Log</a></li>
                                            <li><a class="nav-link" href="{{ route('logs.view', ['file' => 'order_request']) }}"><i class="fas fa-clipboard-list me-2"></i> Order Request Log</a></li>
                                        </ul>
                                    </div>
                                </li>
                                @endcan
                                @can('campaign-list')
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('campaigns.index')}}">
                                        <i class="fas fa-bullhorn me-2"></i> Campaigns
                                    </a>
                                </li>
                                @endcan
                                @can('chat-list')
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('chats.index')}}">
                                        <i class="fas fa-comments me-2"></i> Customer Support
                                    </a>
                                </li>
                                @endcan
                                @can('menu-sales')
                                <li class="nav-item">
                                    <a class="nav-link collapsed" data-bs-toggle="collapse" href="#salesMenu" role="button" aria-expanded="false" aria-controls="salesMenu">
                                        <i class="fas fa-shopping-cart me-2"></i> Sales <span class="float-end"><i class="fas fa-angle-down"></i></span>
                                    </a>
                                    <div class="collapse" id="salesMenu">
                                        <ul class="nav flex-column ms-3">
                                            @can('order-list')
                                            <li><a class="nav-link" href="{{ route('orders.index') }}"><i class="fas fa-box-open me-2"></i> Orders</a></li>
                                            @endcan
                                            @can('cash-collection-list')
                                            <li><a class="nav-link" href="{{ route('cashCollection.index') }}"><i class="fas fa-money-bill-wave me-2"></i> Cash Collections</a></li>
                                            @endcan
                                            @if(auth()->user()->hasRole("Staff"))
                                            <li><a class="nav-link" href="{{ route('staffCashCollection') }}"><i class="fas fa-money-check-alt me-2"></i> Cash Collections</a></li>
                                            @endif
                                            @can('coupon-list')
                                            <li><a class="nav-link" href="{{ route('coupons.index') }}"><i class="fas fa-ticket-alt me-2"></i> Coupons</a></li>
                                            @endcan
                                            @can('withdraw-list')
                                            <li><a class="nav-link" href="{{ route('withdraws.index') }}"><i class="fas fa-hand-holding-usd me-2"></i> Withdraws</a></li>
                                            @endcan
                                        </ul>
                                    </div>
                                </li>
                                @endcan
                                @can('menu-catalog')
                                <li class="nav-item">
                                    <a class="nav-link collapsed" data-bs-toggle="collapse" href="#catalogMenu" role="button" aria-expanded="false" aria-controls="catalogMenu">
                                        <i class="fas fa-book me-2"></i> Catalog <span class="float-end"><i class="fas fa-angle-down"></i></span>
                                    </a>
                                    <div class="collapse" id="catalogMenu">
                                        <ul class="nav flex-column ms-3">
                                            @can('time-slot-list')
                                            <li><a class="nav-link" href="{{ route('timeSlots.index') }}"><i class="fas fa-clock me-2"></i> Time Slots</a></li>
                                            @endcan
                                            @can('service-list')
                                            <li><a class="nav-link" href="{{ route('services.index') }}"><i class="fas fa-concierge-bell me-2"></i> Services</a></li>
                                            @endcan
                                            @can('service-category-list')
                                            <li><a class="nav-link" href="{{ route('serviceCategories.index') }}"><i class="fas fa-list-alt me-2"></i> Service Categories</a></li>
                                            @endcan
                                            @can('country-list')
                                            <li><a class="nav-link" href="{{ route('countries.index') }}"><i class="fas fa-globe me-2"></i> Countries</a></li>
                                            @endcan
                                            @can('staff-zone-list')
                                            <li><a class="nav-link" href="{{ route('staffZones.index') }}"><i class="fas fa-map-marker-alt me-2"></i> Staff Zones</a></li>
                                            @endcan
                                            @can('FAQs-list')
                                            <li><a class="nav-link" href="{{ route('FAQs.index') }}"><i class="fas fa-question-circle me-2"></i> FAQs</a></li>
                                            @endcan
                                            @can('review-list')
                                            <li><a class="nav-link" href="{{ route('reviews.index') }}"><i class="fas fa-star me-2"></i> Reviews</a></li>
                                            @endcan
                                            @can('information-list')
                                            <li><a class="nav-link" href="{{ route('information.index') }}"><i class="fas fa-info-circle me-2"></i> Information Page</a></li>
                                            @endcan
                                            @can('complaint-list')
                                            <li><a class="nav-link" href="{{ route('complaints.index') }}"><i class="fas fa-exclamation-circle me-2"></i> Complaints</a></li>
                                            @endcan
                                            @can('membership-plan-list')
                                            <li><a class="nav-link" href="{{ route('membershipPlans.index') }}"><i class="fas fa-id-card me-2"></i> Membership Plans</a></li>
                                            @endcan
                                            @can('quote-list')
                                            <li><a class="nav-link" href="{{ route('quotes.index') }}"><i class="fas fa-file-invoice-dollar me-2"></i> Quotes</a></li>
                                            @endcan
                                            @can('crm-list')
                                            <li><a class="nav-link" href="{{ route('crms.index') }}"><i class="fas fa-address-book me-2"></i> CRM</a></li>
                                            @endcan
                                            @can('staff-designation-list')
                                            <li><a class="nav-link" href="{{ route('subTitles.index') }}"><i class="fas fa-user-tie me-2"></i> Sub Title / Designation</a></li>
                                            @endcan
                                            @can('freelancer-group-list')
                                            <li><a class="nav-link" href="{{ route('freelancerGroups.index') }}"><i class="fas fa-users-cog me-2"></i> Freelancer Groups</a></li>
                                            @endcan
                                        </ul>
                                    </div>
                                </li>
                                @endcan
                                @can('menu-store-config')
                                <li class="nav-item">
                                    <a class="nav-link collapsed" data-bs-toggle="collapse" href="#storeConfigMenu" role="button" aria-expanded="false" aria-controls="storeConfigMenu">
                                        <i class="fas fa-cogs me-2"></i> Store Config <span class="float-end"><i class="fas fa-angle-down"></i></span>
                                    </a>
                                    <div class="collapse" id="storeConfigMenu">
                                        <ul class="nav flex-column ms-3">
                                            @can('setting-list')
                                            <li><a class="nav-link" href="{{ route('settings.index') }}"><i class="fas fa-sliders-h me-2"></i> Settings</a></li>
                                            @endcan
                                            @can('currency-list')
                                            <li><a class="nav-link" href="{{ route('currencies.index') }}"><i class="fas fa-coins me-2"></i> Currencies</a></li>
                                            @endcan
                                            @can('holiday-list')
                                            <li><a class="nav-link" href="/holidays"><i class="fas fa-umbrella-beach me-2"></i> Holidays</a></li>
                                            @endcan
                                            @can('staff-holiday-list')
                                            <li><a class="nav-link" href="{{ route('staffHolidays.index') }}"><i class="fas fa-user-clock me-2"></i> Staff Holiday</a></li>
                                            <li><a class="nav-link" href="{{ route('longHolidays.index') }}"><i class="fas fa-calendar-plus me-2"></i> Long Holiday</a></li>
                                            <li><a class="nav-link" href="{{ route('shortHolidays.index') }}"><i class="fas fa-calendar-minus me-2"></i> Short Holiday</a></li>
                                            <li><a class="nav-link" href="{{ route('staffGeneralHolidays.index') }}"><i class="fas fa-calendar-day me-2"></i> Staff General Holiday</a></li>
                                            @endcan
                                        </ul>
                                    </div>
                                </li>
                                @endcan
                                @can('menu-user')
                                <li class="nav-item">
                                    <a class="nav-link collapsed" data-bs-toggle="collapse" href="#usersMenu" role="button" aria-expanded="false" aria-controls="usersMenu">
                                        <i class="fas fa-user-friends me-2"></i> Users <span class="float-end"><i class="fas fa-angle-down"></i></span>
                                    </a>
                                    <div class="collapse" id="usersMenu">
                                        <ul class="nav flex-column ms-3">
                                            @can('service-staff-list')
                                            <li><a class="nav-link" href="{{ route('serviceStaff.index') }}"><i class="fas fa-user me-2"></i> Staff</a></li>
                                            @endcan
                                            @can('customer-list')
                                            <li><a class="nav-link" href="{{ route('customers.index') }}"><i class="fas fa-user-tag me-2"></i> Customer</a></li>
                                            @endcan
                                            @can('affiliate-list')
                                            <li><a class="nav-link" href="{{ route('affiliates.index') }}"><i class="fas fa-user-tie me-2"></i> Affiliate</a></li>
                                            @endcan
                                            @can('manager-list')
                                            <li><a class="nav-link" href="{{ route('managers.index') }}"><i class="fas fa-user-shield me-2"></i> Manager</a></li>
                                            @endcan
                                            @can('supervisor-list')
                                            <li><a class="nav-link" href="{{ route('supervisors.index') }}"><i class="fas fa-user-check me-2"></i> Supervisor</a></li>
                                            @endcan
                                            @can('assistant-supervisor-list')
                                            <li><a class="nav-link" href="{{ route('assistantSupervisors.index') }}"><i class="fas fa-user-clock me-2"></i> Assistant Supervisor</a></li>
                                            @endcan
                                            @can('driver-list')
                                            <li><a class="nav-link" href="{{ route('drivers.index') }}"><i class="fas fa-car me-2"></i> Drivers</a></li>
                                            @endcan
                                            <li><a class="nav-link" href="{{ route('dataEntry.index') }}"><i class="fas fa-keyboard me-2"></i> Data Entry User</a></li>
                                            <li><a class="nav-link" href="{{ route('users.index') }}?role=Support team"><i class="fas fa-headset me-2"></i> Support team</a></li>
                                        </ul>
                                    </div>
                                </li>
                            @endcan
                        </ul>
                        <button class="btn btn-outline-secondary d-md-none mt-3" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle sidebar">
                            <i class="fas fa-bars"></i> Menu
                        </button>
                    </div>
                </aside>
                @endif
                
                @guest
                <main class="col-md-12 ms-sm-auto col-lg-12 px-md-4 py-4">
                @else
                <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                @endif
                    @include('site.layout.locationPopup')
                    @yield('content')
                    <div id="addToCartPopup"></div>
                </main>
            </div>
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
        <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js"></script>
    <!-- Select dropdown -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.selectpicker').selectpicker();
        });
    </script>
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                placeholder: 'Search...',
                allowClear: true,
                width: '100%',
                language: {
                    searching: function() {
                        return "Type to search...";
                    }
                }
            }).on('select2:open', function() {
                setTimeout(() => {
                    let searchBox = document.querySelector('.select2-search__field');
                    if (searchBox) {
                        searchBox.placeholder = "Type to search...";
                        searchBox.focus();
                    }
                }, 100);
            });
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const numberInputField = document.querySelector("#number");
            const whatsappInputField = document.querySelector("#whatsapp");
            const numberCountryInputField = document.querySelector("#number_country_code");
            const whatsappCountryInputField = document.querySelector("#whatsapp_country_code");

            // Function to convert dial code (+971) to country code (ae)
            function dialCodeToCountryCode(dialCode) {
                const countryData = window.intlTelInputGlobals.getCountryData();
                for (const country of countryData) {
                    if (country.dialCode === dialCode.replace('+', '')) {
                        return country.iso2;
                    }
                }
                return "ae"; // default if not found
            }

            // Get initial country based on hidden field value or default to 'ae'
            const initialNumberCountry = numberCountryInputField?.value
                ? dialCodeToCountryCode(numberCountryInputField.value)
                : 'ae';

            const initialWhatsappCountry = whatsappCountryInputField?.value
                ? dialCodeToCountryCode(whatsappCountryInputField.value)
                : 'ae';


            // Initialize intl-tel-input
            const numberInput = window.intlTelInput(numberInputField, {
                showSelectedDialCode: true,
                initialCountry: initialNumberCountry,
                utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/19.2.19/js/utils.js",
            });

            const whatsappInput = window.intlTelInput(whatsappInputField, {
                showSelectedDialCode: true,
                initialCountry: initialWhatsappCountry,
                utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/19.2.19/js/utils.js",
            });

            // Set initial values if empty (shouldn't be needed if hidden fields have defaults)
            if (!numberCountryInputField.value) {
                numberCountryInputField.value = `+${numberInput.getSelectedCountryData().dialCode}`;
            }
            if (!whatsappCountryInputField.value) {
                whatsappCountryInputField.value = `+${whatsappInput.getSelectedCountryData().dialCode}`;
            }

            // Handle country change events
            numberInputField.addEventListener("countrychange", function () {
                const selectedCountryData = numberInput.getSelectedCountryData();
                numberCountryInputField.value = `+${selectedCountryData.dialCode}`;
            });

            whatsappInputField.addEventListener("countrychange", function () {
                const selectedCountryData = whatsappInput.getSelectedCountryData();
                whatsappCountryInputField.value = `+${selectedCountryData.dialCode}`;
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
