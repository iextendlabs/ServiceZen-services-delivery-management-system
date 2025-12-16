<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Services Delivery Management System') }}</title>

    {{-- <script src="{{ asset('js/app.js') }}"></script> --}}

<!-- Fonts -->
<link rel="dns-prefetch" href="//fonts.gstatic.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">
<script src="https://unpkg.com/lucide@latest"></script>
{{-- bootstrap 5 --}}
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Bootstrap CSS -->
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

<!-- Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css">

<!-- Fullcalendar -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css" />

<!-- intlTelInput -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/19.2.19/css/intlTelInput.css" />

<!-- Summernote -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">

<!-- Select2 -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />

<!-- Bootstrap Select -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">


    <style>
        :root {
          --primary: #4e73df;
          --light-bg: #f8f9fc;
          --text: #6e707e;
        }

        body {
          font-family: 'Roboto', sans-serif;
          background-color: var(--light-bg);
          overflow-x: hidden;
        }

        .dashboard-wrapper {
          display: flex;
          width: 100%;
        }

        /* Sidebar */
        #sidebar {
          width: 250px;
          background: #fff;
          box-shadow: 0 0.15rem 1.75rem rgba(58, 59, 69, 0.15);
          transition: all 0.3s ease;
        }

        #sidebar.collapsed {
          width: 70px;
          text-align: center;
        }

        .sidebar-header {
          padding: 20px;
          border-bottom: 1px solid #e3e6f0;
          background: #fff;
          cursor: pointer;
        }

        .sidebar-header h3 {
          color: var(--primary);
          font-weight: 700;
          margin: 0;
          display: flex;
          align-items: center;
          justify-content: center;
          font-family: 'Poppins', sans-serif;
        }

        #sidebar.collapsed .sidebar-header h3 span {
          display: none;
        }

        .sidebar-content {
          padding: 15px 0;
        }

        .sidebar-menu {
          list-style: none;
          margin: 0;
          padding: 0;
        }

        .sidebar-menu a {
          display: flex;
          align-items: center;
          padding: 12px 20px;
          color: var(--text);
          text-decoration: none;
          font-weight: 500;
          border-left: 4px solid transparent;
          transition: all 0.3s;
          position: relative;
        }

        .sidebar-menu a i {
          margin-right: 12px;
          width: 22px;
          text-align: center;
          font-size: 16px;
        }

        /* Collapsed icons alignment */
        #sidebar.collapsed .sidebar-menu a {
          justify-content: center;
          padding: 14px 10px;
          border-left: none;
        }

        #sidebar.collapsed .sidebar-menu a i {
          margin: 0;
          font-size: 18px;
        }

        #sidebar.collapsed .sidebar-menu a span,
        #sidebar.collapsed .dropdown-btn::after {
          display: none !important;
        }

        .sidebar-menu a.active {
          background: var(--light-bg);
          border-left: 4px solid var(--primary);
          color: var(--primary);
        }

        .sidebar-menu a:hover {
          background: var(--light-bg);
          color: var(--primary);
        }

        /* Dropdown */
        .dropdown-btn::after {
          content: '\f107';
          font-family: "Font Awesome 5 Free";
          font-weight: 900;
          margin-left: auto;
          transition: transform 0.3s;
        }

        .dropdown-btn.active::after {
          transform: rotate(180deg);
        }

        .dropdown-container {
          max-height: 0;
          overflow: hidden;
          transition: max-height 0.3s ease;
          background: var(--light-bg);
        }

        .dropdown-container.show {
          max-height: 650px;
        }

        .dropdown-container a {
            padding: 10px 20px 10px 50px;
            display: flex;
            align-items: center;
            color: var(--text);
            text-decoration: none;
            border-left: 3px solid transparent;
        }

        .dropdown-container a:hover {
          background: #e9ecef;
          color: var(--primary);
          border-left: 3px solid var(--primary);
        }
        
        .dropdown-container a.active {
            background: #e0f2fe;
            color: #0d6efd;
            font-weight: 500;
        }

        /* Tooltip */
        #sidebar.collapsed .sidebar-menu a span {
          display: none;
        }

        /* Main Content */
        #content {
          flex: 1;
          padding: 20px;
          min-height: 100vh;
          transition: all 0.3s;
        }

        .navbar {
          background: #fff;
          box-shadow: 0 0.15rem 1.75rem rgba(58, 59, 69, 0.15);
          padding: 15px 20px;
          margin-bottom: 30px;
        }

        .btn-toggle {
          background: transparent;
          border: none;
          font-size: 20px;
          color: var(--text);
          cursor: pointer;
        }

        /* Blue Theme */
        .theme-blue #sidebar {
          background: linear-gradient(135deg, #000000 0%, #333333 100%);
          color: #fff;
        }
        
        .theme-blue .sidebar-header {
            background: transparent;
            border-bottom: 1px solid rgba(255,255,255,0.2);
        }

        .theme-blue .sidebar-header h3 {
          color: #fff;
        }

        .theme-blue .sidebar-menu a {
          color: #e0e6ff;
        }

        .theme-blue .sidebar-menu a:hover,
        .theme-blue .sidebar-menu a.active {
          background-color: rgba(255, 255, 255, 0.1);
          border-left: 4px solid #fff;
          color: #fff;
        }

        .theme-blue .dropdown-container {
          background-color: rgba(0, 0, 0, 0.2);
        }

        .theme-blue .dropdown-container a {
          color: #e0e6ff;
        }

        .theme-blue .dropdown-container a:hover {
          color: #fff;
          background-color: rgba(255, 255, 255, 0.1);
          border-left: 3px solid #fff;
        }

        /* Responsive */
        @media (max-width: 992px) {
            /* Stack the layout on medium/smaller screens so content becomes full-width */
            .dashboard-wrapper {
                display: block;
            }
        }

        @media (max-width: 768px) {
            /* Use transform for smoother slide-in and avoid layout reflow */
            #sidebar {
                position: fixed;
                left: 0;
                top: 0;
                height: 100%;
                z-index: 1060;
                width: 250px;
                transform: translateX(-100%);
                transition: transform 0.25s ease;
            }
            #sidebar.active {
                transform: translateX(0);
            }

            /* Ensure overlay sits below sidebar but above page content */
            #sidebarOverlay {
                z-index: 1055;
            }

            /* Content should be full width on small screens */
            #content {
                width: 100%;
                padding: 12px;
            }

            /* Ensure the toggle button is visible on small screens */
            #sidebarCollapse {
                display: inline-block;
            }

            /* Make navbar elements stack nicely */
            .navbar .navbar-nav { display: flex; gap: .5rem; }
            .navbar .btn-store-view { width: auto; }
        }
        
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
        .container {
            max-width: 100% !important;
            padding: 0 !important;
            margin: 0 auto !important;
        }
        /* Mobile overlay used when sidebar is open on small screens */
        #sidebarOverlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.45);
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.25s ease, visibility 0.25s ease;
            z-index: 998;
        }

        #sidebarOverlay.visible {
            opacity: 1;
            visibility: visible;
        }

        /* Make long tables horizontally scrollable on small screens */
        .table-responsive-custom {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        /* Navbar adjustments for small screens */
        @media (max-width: 576px) {
          .navbar .btn-store-view { font-size: 12px; padding: .25rem .5rem; }
          .sidebar-header h3 { font-size: 18px; }
          .sidebar-menu a { padding: 10px 12px; }
        }
    </style>
</head>

<body class="theme-blue">
    <div id="app" class="dashboard-wrapper">
        @guest
        <main class="w-100">
            @yield('content')
        </main>
        @else
        <!-- Sidebar -->
        <nav id="sidebar">
            <div class="sidebar-header" id="sidebarHeader">
                <h3><a class="navbar-brand p-0 text-center fw-bold fs-4" href="{{ url('/admin') }}">
                        <span>Lipslay Admin</span>
                    </a></h3>
            </div>

            <div class="sidebar-content">
                <ul class="sidebar-menu">
                    {{-- Add Funds --}}
                    @if(auth()->user()->hasRole('Staff'))
                    <li>
                        <a class="{{ request()->routeIs('stripe.staff.form') ? 'active' : '' }}"
                        href="{{ route('stripe.staff.form') }}" data-toggle="tooltip" data-placement="right" title="Add Funds">
                            <i class="fas fa-wallet"></i> <span>Add Funds</span>
                        </a>
                    </li>
                    @endif

                    {{-- Join Affiliate --}}
                    @if(Auth::user()->affiliate_program == null && auth()->user()->hasRole("Staff") && !auth()->user()->hasRole("Affiliate"))
                    <li>
                        <a class="{{ request()->routeIs('apply.affiliateProgram') ? 'active' : '' }}"
                        href="{{ route('apply.affiliateProgram') }}" data-toggle="tooltip" data-placement="right" title="Join Affiliate Program">
                            <i class="fas fa-user-tag"></i> <span>Join Affiliate Program</span>
                        </a>
                    </li>
                    @endif
                    {{-- Dashboard --}}
                    <li>
                        <a class="{{ request()->is('admin') ? 'active' : '' }}" href="{{ url('/admin') }}" data-toggle="tooltip" data-placement="right" title="Dashboard">
                           <i class="fas fa-tachometer-alt"></i><span>Dashboard</span>
                        </a>
                    </li>

                    {{-- Sales --}}
                    @can('menu-sales')
                    <li>
                        <a class="dropdown-btn {{ request()->is('orders*') || request()->is('cashCollection*') || request()->is('coupons*') || request()->is('withdraws*') ? 'active' : '' }}" data-toggle="tooltip" data-placement="right" title="Sales">
                            <i class="fas fa-shopping-cart"></i> <span>Sales</span>
                        </a>
                        <div class="dropdown-container {{ request()->is('orders*') || request()->is('cashCollection*') || request()->is('coupons*') || request()->is('withdraws*') ? 'show' : '' }}">
                            @can('order-list')
                            <a class="{{ request()->is('orders*') ? 'active' : '' }}" href="{{ route('orders.index') }}"><i class="fas fa-box-open"></i> Orders</a>
                            @endcan
                            @can('cash-collection-list')
                            <a class="{{ request()->is('cashCollection*') ? 'active' : '' }}" href="{{ route('cashCollection.index') }}"><i class="fas fa-money-bill-wave"></i> Cash Collections</a>
                            @endcan
                            @if(auth()->user()->hasRole("Staff"))
                            <a class="{{ request()->is('staffCashCollection') ? 'active' : '' }}" href="{{ route('staffCashCollection') }}"><i class="fas fa-money-check-alt"></i> Cash Collections</a>
                            @endif
                            @can('coupon-list')
                            <a class="{{ request()->is('coupons*') ? 'active' : '' }}" href="{{ route('coupons.index') }}"><i class="fas fa-ticket-alt"></i> Coupons</a>
                            @endcan
                            @can('withdraw-list')
                            <a class="{{ request()->is('withdraws*') ? 'active' : '' }}" href="{{ route('withdraws.index') }}"><i class="fas fa-hand-holding-usd"></i> Withdraws</a>
                            @endcan
                            @can('quote-list')
                            <a class="{{ request()->is('quotes*') ? 'active' : '' }}" href="{{ route('quotes.index') }}"><i class="fas fa-file-invoice-dollar"></i> Quotes</a>
                            @endcan                            
                        </div>
                    </li>
                    @endcan

                    {{-- Catalog --}}
                    @can('menu-catalog')
                    <li>
                        <a class="dropdown-btn {{ request()->is('timeSlots*') || request()->is('services*') || request()->is('serviceCategories*') || request()->is('countries*') || request()->is('staffZones*') || request()->is('FAQs*') || request()->is('reviews*') || request()->is('information*') || request()->is('complaints*') || request()->is('membershipPlans*') || request()->is('quotes*') || request()->is('crms*') || request()->is('subTitles*') || request()->is('freelancerGroups*') ? 'active' : '' }}" data-toggle="tooltip" data-placement="right" title="Catalog">
                            <i class="fas fa-book"></i> <span>Catalog</span>
                        </a>
                        <div class="dropdown-container {{ request()->is('timeSlots*') || request()->is('services*') || request()->is('serviceCategories*') || request()->is('countries*') || request()->is('staffZones*') || request()->is('FAQs*') || request()->is('reviews*') || request()->is('information*') || request()->is('complaints*') || request()->is('membershipPlans*') || request()->is('quotes*') || request()->is('crms*') || request()->is('subTitles*') || request()->is('freelancerGroups*') ? 'show' : '' }}">
                            @can('time-slot-list')
                            <a class="{{ request()->is('timeSlots*') ? 'active' : '' }}" href="{{ route('timeSlots.index') }}"><i class="fas fa-clock"></i> Time Slots</a>
                            @endcan
                            @can('service-list')
                            <a class="{{ request()->is('services*') ? 'active' : '' }}" href="{{ route('services.index') }}"><i class="fas fa-concierge-bell"></i> Services</a>
                            @endcan
                            @can('service-category-list')
                            <a class="{{ request()->is('serviceCategories*') ? 'active' : '' }}" href="{{ route('serviceCategories.index') }}"><i class="fas fa-list-alt"></i> Service Categories</a>
                            @endcan
                            @can('country-list')
                            <a class="{{ request()->is('countries*') ? 'active' : '' }}" href="{{ route('countries.index') }}"><i class="fas fa-globe"></i> Countries</a>
                            @endcan
                            @can('staff-zone-list')
                            <a class="{{ request()->is('staffZones*') ? 'active' : '' }}" href="{{ route('staffZones.index') }}"><i class="fas fa-map-marker-alt"></i> Staff Zones</a>
                            @endcan
                            @can('FAQs-list')
                            <a class="{{ request()->is('FAQs*') ? 'active' : '' }}" href="{{ route('FAQs.index') }}"><i class="fas fa-question-circle"></i> FAQs</a>
                            @endcan
                            @can('review-list')
                            <a class="{{ request()->is('reviews*') ? 'active' : '' }}" href="{{ route('reviews.index') }}"><i class="fas fa-star"></i> Reviews</a>
                            @endcan
                            @can('information-list')
                            <a class="{{ request()->is('information*') ? 'active' : '' }}" href="{{ route('information.index') }}"><i class="fas fa-info-circle"></i> Information Page</a>
                            @endcan
                            @can('complaint-list')
                            <a class="{{ request()->is('complaints*') ? 'active' : '' }}" href="{{ route('complaints.index') }}"><i class="fas fa-exclamation-circle"></i> Complaints</a>
                            @endcan
                            @can('membership-plan-list')
                            <a class="{{ request()->is('membershipPlans*') ? 'active' : '' }}" href="{{ route('membershipPlans.index') }}"><i class="fas fa-id-card"></i> Membership Plans</a>
                            @endcan
                            @can('crm-list')
                            <a class="{{ request()->is('crms*') ? 'active' : '' }}" href="{{ route('crms.index') }}"><i class="fas fa-address-book"></i> CRM</a>
                            @endcan
                            @can('staff-designation-list')
                            <a class="{{ request()->is('subTitles*') ? 'active' : '' }}" href="{{ route('subTitles.index') }}"><i class="fas fa-user-tie"></i> Sub Title / Designation</a>
                            @endcan
                            @can('freelancer-group-list')
                            <a class="{{ request()->is('freelancerGroups*') ? 'active' : '' }}" href="{{ route('freelancerGroups.index') }}"><i class="fas fa-users-cog"></i> Freelancer Groups</a>
                            @endcan
                        </div>
                    </li>
                    @endcan                    

                    {{-- Campaigns --}}
                    @can('campaign-list')
                    <li>
                        <a class="{{ request()->is('campaigns*') ? 'active' : '' }}" href="{{ route('campaigns.index')}}" data-toggle="tooltip" data-placement="right" title="Campaigns">
                            <i class="fas fa-bullhorn"></i> <span>Campaigns</span>
                        </a>
                    </li>
                    @endcan

                    {{-- Customer Support --}}
                    @can('chat-list')
                    <li>
                        <a class="{{ request()->is('chats*') ? 'active' : '' }}" href="{{ route('chats.index')}}" data-toggle="tooltip" data-placement="right" title="Customer Support">
                            <i class="fas fa-comments"></i> <span>Customer Support</span>
                        </a>
                    </li>
                    @endcan

                    {{-- Rota --}}
                    <li>
                        <a class="{{ request()->is('rota') ? 'active' : '' }}" href="/rota" data-toggle="tooltip" data-placement="right" title="Rota">
                            <i class="fas fa-calendar-alt"></i> <span>Rota</span>
                        </a>
                    </li>

                    {{-- Joinee Program --}}
                    @can('menu-new-joinee')
                    <li>
                        <a class="dropdown-btn {{ request()->is('affiliateProgram*') || request()->is('freelancerProgram*') ? 'active' : '' }}" data-toggle="tooltip" data-placement="right" title="Joinee Program">
                            <i class="fas fa-users"></i><span>Joinee Program</span>
                        </a>
                        <div class="dropdown-container {{ request()->is('affiliateProgram*') || request()->is('freelancerProgram*') ? 'show' : '' }}">
                            @can('affiliate-program-list')
                            <a class="{{ request()->is('affiliateProgram*') ? 'active' : '' }}"
                            href="{{ route('affiliateProgram.index') }}">
                                <i class="fas fa-user-friends"></i> Affiliate
                            </a>
                            @endcan
                            @can('freelancer-program-list')
                            <a class="{{ request()->is('freelancerProgram*') ? 'active' : '' }}"
                            href="{{ route('freelancerProgram.index') }}">
                                <i class="fas fa-user-clock"></i> Freelancer
                            </a>
                            @endcan
                        </div>
                    </li>
                    @endcan

                    {{-- Store Config --}}
                    @can('menu-store-config')
                    <li>
                        <a class="dropdown-btn {{ request()->is('settings*') || request()->is('currencies*') || request()->is('holidays*') || request()->is('staffHolidays*') || request()->is('longHolidays*') || request()->is('shortHolidays*') || request()->is('staffGeneralHolidays*') ? 'active' : '' }}" data-toggle="tooltip" data-placement="right" title="Store Config">
                            <i class="fas fa-cogs"></i> <span>Store Config</span>
                        </a>
                        <div class="dropdown-container {{ request()->is('settings*') || request()->is('currencies*') || request()->is('holidays*') || request()->is('staffHolidays*') || request()->is('longHolidays*') || request()->is('shortHolidays*') || request()->is('staffGeneralHolidays*') ? 'show' : '' }}">
                            @can('setting-list')
                            <a class="{{ request()->is('settings*') ? 'active' : '' }}" href="{{ route('settings.index') }}"><i class="fas fa-sliders-h"></i> Settings</a>
                            @endcan
                            @can('currency-list')
                            <a class="{{ request()->is('currencies*') ? 'active' : '' }}" href="{{ route('currencies.index') }}"><i class="fas fa-coins"></i> Currencies</a>
                            @endcan
                            @can('holiday-list')
                            <a class="{{ request()->is('holidays*') ? 'active' : '' }}" href="/holidays"><i class="fas fa-umbrella-beach"></i> Holidays</a>
                            @endcan
                            @can('staff-holiday-list')
                            <a class="{{ request()->is('staffHolidays*') ? 'active' : '' }}" href="{{ route('staffHolidays.index') }}"><i class="fas fa-user-clock"></i> Staff Holiday</a>
                            <a class="{{ request()->is('longHolidays*') ? 'active' : '' }}" href="{{ route('longHolidays.index') }}"><i class="fas fa-calendar-plus"></i> Long Holiday</a>
                            <a class="{{ request()->is('shortHolidays*') ? 'active' : '' }}" href="{{ route('shortHolidays.index') }}"><i class="fas fa-calendar-minus"></i> Short Holiday</a>
                            <a class="{{ request()->is('staffGeneralHolidays*') ? 'active' : '' }}" href="{{ route('staffGeneralHolidays.index') }}"><i class="fas fa-calendar-check"></i> Staff General Holiday</a>
                            @endcan
                        </div>
                    </li>
                    @endcan
                    {{-- Holidays
                    @can('menu-holidays')
                    <li>
                        <a class="{{ request()->is('staffHolidays*') ? 'active' : '' }}" href="{{ route('staffHolidays.index') }}" data-toggle="tooltip" data-placement="right" title="Holidays">
                            <i class="fas fa-umbrella-beach"></i> <span>Holidays</span>
                        </a>
                        <div class="dropdown-container {{ request()->is('staffHolidays*') || request()->is('longHolidays*') || request()->is('shortHolidays*') || request()->is('staffGeneralHolidays*') ? 'show' : '' }}">
                            @can('staff-holiday-list')
                            <a class="{{ request()->is('staffHolidays*') ? 'active' : '' }}" href="{{ route('staffHolidays.index') }}"><i class="fas fa-user-clock"></i> Staff Holiday</a>
                            <a class="{{ request()->is('longHolidays*') ? 'active' : '' }}" href="{{ route('longHolidays.index') }}"><i class="fas fa-calendar-plus"></i> Long Holiday</a>
                            <a class="{{ request()->is('shortHolidays*') ? 'active' : '' }}" href="{{ route('shortHolidays.index') }}"><i class="fas fa-calendar-minus"></i> Short Holiday</a>
                            <a class="{{ request()->is('staffGeneralHolidays*') ? 'active' : '' }}" href="{{ route('staffGeneralHolidays.index') }}"><i class="fas fa-calendar-check"></i> Staff General Holiday</a>
                            @endcan
                        </div>
                    </li>
                    @endcan                     --}}
                    {{-- Users --}}
                    @can('menu-user')
                    <li>
                        <a class="dropdown-btn {{ request()->is('serviceStaff*') || request()->is('customers*') || request()->is('affiliates*') || request()->is('managers*') || request()->is('supervisors*') || request()->is('assistantSupervisors*') || request()->is('drivers*') || request()->is('dataEntry*') || request()->is('users*') ? 'active' : '' }}" data-toggle="tooltip" data-placement="right" title="Users">
                            <i class="fas fa-user-friends"></i> <span>Users</span>
                        </a>
                        <div class="dropdown-container {{ request()->is('serviceStaff*') || request()->is('customers*') || request()->is('affiliates*') || request()->is('managers*') || request()->is('supervisors*') || request()->is('assistantSupervisors*') || request()->is('drivers*') || request()->is('dataEntry*') || request()->is('users*') ? 'show' : '' }}">
                            @can('service-staff-list')
                            <a class="{{ request()->is('serviceStaff*') ? 'active' : '' }}" href="{{ route('serviceStaff.index') }}"><i class="fas fa-user"></i> Staff</a>
                            @endcan
                            @can('customer-list')
                            <a class="{{ request()->is('customers*') ? 'active' : '' }}" href="{{ route('customers.index') }}"><i class="fas fa-user-tag"></i> Customer</a>
                            @endcan
                            @can('affiliate-list')
                            <a class="{{ request()->is('affiliates*') ? 'active' : '' }}" href="{{ route('affiliates.index') }}"><i class="fas fa-user-tie"></i> Affiliate</a>
                            @endcan
                            @can('manager-list')
                            <a class="{{ request()->is('managers*') ? 'active' : '' }}" href="{{ route('managers.index') }}"><i class="fas fa-user-shield"></i> Manager</a>
                            @endcan
                            @can('supervisor-list')
                            <a class="{{ request()->is('supervisors*') ? 'active' : '' }}" href="{{ route('supervisors.index') }}"><i class="fas fa-user-check"></i> Supervisor</a>
                            @endcan
                            @can('assistant-supervisor-list')
                            <a class="{{ request()->is('assistantSupervisors*') ? 'active' : '' }}" href="{{ route('assistantSupervisors.index') }}"><i class="fas fa-user-clock"></i> Assistant Supervisor</a>
                            @endcan
                            @can('driver-list')
                            <a class="{{ request()->is('drivers*') ? 'active' : '' }}" href="{{ route('drivers.index') }}"><i class="fas fa-car"></i> Drivers</a>
                            @endcan
                            <a class="{{ request()->is('dataEntry*') ? 'active' : '' }}" href="{{ route('dataEntry.index') }}"><i class="fas fa-keyboard"></i> Data Entry User</a>
                            <a class="{{ request()->is('users*') && request('role') == 'Support team' ? 'active' : '' }}" href="{{ route('users.index') }}?role=Support team"><i class="fas fa-headset"></i> Support team</a>
                        </div>
                    </li>
                    @endcan
                    {{-- Maintenance --}}
                    @can('menu-maintenance')
                    <li>
                        <a class="dropdown-btn {{ request()->is('logs*') ? 'active' : '' }}" data-toggle="tooltip" data-placement="right" title="Maintenance">
                            <i class="fas fa-tools"></i> <span>Maintenance</span>
                        </a>
                        <div class="dropdown-container {{ request()->is('logs*') ? 'show' : '' }}">
                            <a class="{{ request()->is('logs/laravel') ? 'active' : '' }}" href="{{ route('logs.view', ['file' => 'laravel']) }}"><i class="fas fa-file-alt"></i> Laravel Log</a>
                            <a class="{{ request()->is('logs/app_error') ? 'active' : '' }}" href="{{ route('logs.view', ['file' => 'app_error']) }}"><i class="fas fa-exclamation-triangle"></i> Error Log</a>
                            <a class="{{ request()->is('logs/order_request') ? 'active' : '' }}" href="{{ route('logs.view', ['file' => 'order_request']) }}"><i class="fas fa-clipboard-list"></i> Order Request Log</a>
                            <a class="{{ auth()->user()->hasRole("Admin") ? 'active' : '' }}" href="{{ route('backups.index') }}"><i class="fas fa-database me-2"></i> Database Backups</a>
                        </div>
                    </li>
                    @endcan                    
                </ul>
            </div>
        </nav>

        <!-- Content -->
        <div id="content">
            <nav class="navbar navbar-expand-md">
                <button type="button" id="sidebarCollapse" class="btn-toggle"><i class="fas fa-bars"></i></button>
                <button class="btn-toggle d-md-none" type="button" id="showSidebarBtn">
                    <i class="fas fa-bars"></i>
                </button>
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item me-3 mb-2 d-flex align-items-center">
                        <a class="btn btn-sm mt-2 ml-2 btn-store-view" href="https://lipslay.com/?cache=false" target="_blank">
                            <i class="fas fa-store"></i> View Store
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <h3 class="mt-2 text-center">@yield('page_title')</h3>
                    </li>
                </ul>
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <div class="mt-2 align-items-center">
                            @if (auth()->user()->hasRole('Admin'))
                                <a class="btn" href="{{ route('appData') }}"> Refresh App</a>
                                <a id="cacheClearBtn" class="btn mr-2" href="javascript:void(0)"> Cache Clear</a>
                            @endif
                            @if (auth()->user()->hasRole('Affiliate'))
                                <a class="btn" href="{{ route('affiliate_dashboard.index') }}">Affiliate DashBorad</a>
                            @endif
                        </div>
                    </li>                    
                    <li class="nav-item dropdown mt-2">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" data-toggle="dropdown">
                            <i class="fas fa-user-circle"></i> {{ Auth::user()->name }}
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="{{ route('profile', Auth::user()->id) }}">
                                <i class="fas fa-id-badge me-2"></i> Profile
                            </a>
                            <a class="dropdown-item" target="_blank" href="/sitemap.xml">
                                <i class="fas fa-sitemap me-2"></i> Sitemap
                            </a>
                            @can('user-list')
                            <a class="dropdown-item" href="{{ route('users.index') }}">
                                <i class="fas fa-users me-2"></i> Users
                            </a>
                            @endcan
                            @can('role-list')
                            <a class="dropdown-item" href="{{ route('roles.index') }}">
                                <i class="fas fa-user-tag me-2"></i> Role
                            </a>
                            @endcan
                            <div class="dropdown-divider"></div>
                            <form id="logout-form-header" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                            <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form-header').submit();">
                                <i class="fas fa-sign-out-alt me-2"></i> Logout
                            </a>
                        </div>
                    </li>
                </ul>
            </nav>
            
            <main class="">
                @include('site.layout.locationPopup')
                @yield('content')
                <div id="addToCartPopup"></div>
            </main>
        </div>
        @endguest
    </div>
    <!-- Mobile sidebar overlay -->
    <div id="sidebarOverlay" aria-hidden="true"></div>
    <footer class="text-muted">
    </footer>
        <!-- Compiled front-end bundle disabled here because it imports Popper v2 / Bootstrap 5
            which conflicts with the admin layout that uses Bootstrap 4 (data-toggle="dropdown").
            If you want to enable this, ensure the compiled bundle uses the same Bootstrap/Popper
            versions as the rest of the page (or migrate the admin markup to Bootstrap 5).
        -->
        <!-- <script src="{{ asset('js/app.js') }}"></script> -->
<!-- jQuery (one version only) -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- jQuery UI -->
<script src="https://code.jquery.com/ui/1.13.3/jquery-ui.js"></script>

<!-- Popper (one version only) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.1/umd/popper.min.js"></script>

<!-- Bootstrap JS (one version only) -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<!-- intlTelInput -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/19.2.19/js/intlTelInput.min.js"></script>

<!-- Moment -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>

<!-- Summernote -->
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
<script src="https://cdn.jsdelivr.net/gh/DiemenDesign/summernote-image-attributes/summernote-image-attributes.js"></script>

<!-- Fullcalendar -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js"></script>

<!-- Select2 -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

<!-- Bootstrap Select -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>
    @yield('scripts')

    <script>
                $(function () {
                    $('[data-toggle="tooltip"]').tooltip(); // Enable tooltips

                    const $sidebar = $('#sidebar');
                    const $overlay = $('#sidebarOverlay');

                    function lockBodyScroll() {
                        $('body').css('overflow', 'hidden');
                    }
                    function unlockBodyScroll() {
                        $('body').css('overflow', '');
                    }

                    // Sidebar collapse / expand for desktop
                    $('#sidebarCollapse').on('click', function () {
                        $sidebar.toggleClass('collapsed');
                        if ($sidebar.hasClass('collapsed')) {
                                $('.dropdown-btn').removeClass('active');
                                $('.dropdown-container').removeClass('show');
                        }
                    });

                    // Show sidebar on small screens
                    $('#showSidebarBtn').on('click', function() {
                            $sidebar.addClass('active');
                            $overlay.addClass('visible');
                            lockBodyScroll();
                    });

                    // Clicking overlay closes sidebar on mobile
                    $overlay.on('click', function() {
                            $sidebar.removeClass('active');
                            $overlay.removeClass('visible');
                            unlockBodyScroll();
                    });

                    // Ensure sidebar closes when window is resized above mobile breakpoint
                    $(window).on('resize', function() {
                            if ($(window).width() > 768) {
                                    if ($sidebar.hasClass('active')) {
                                            $sidebar.removeClass('active');
                                            $overlay.removeClass('visible');
                                            unlockBodyScroll();
                                    }
                            }
                    }).trigger('resize');

                    // Dropdown toggle
                    $('.dropdown-btn').on('click', function (e) {
                        e.preventDefault();
                        if ($sidebar.hasClass('collapsed')) {
                            $sidebar.removeClass('collapsed');
                        }
                        const isActive = $(this).hasClass('active');
                        // Close all other dropdowns
                        $('.dropdown-btn').not(this).removeClass('active');
                        $('.dropdown-container').not($(this).next('.dropdown-container')).removeClass('show');
            
                        // Toggle current dropdown
                        if (!isActive) {
                            $(this).addClass('active');
                            $(this).next('.dropdown-container').addClass('show');
                        } else {
                            $(this).removeClass('active');
                            $(this).next('.dropdown-container').removeClass('show');
                        }
                    });

                    // Theme toggle (Default ↔ Blue)
                    $('#sidebarHeader').on('click', function () {
                        $('body').toggleClass('theme-blue theme-default');
                    });
                });
    </script>
    <script>
        // Compatibility shim: map Bootstrap 5 data attributes to Bootstrap 4 equivalents
        // This allows pages that accidentally use `data-bs-toggle` / `data-bs-target`
        // to keep working with the Admin layout which loads Bootstrap 4.
        (function($) {
            $(function() {
                // map data-bs-toggle -> data-toggle, data-bs-target -> data-target
                $('[data-bs-toggle]').each(function() {
                    var $el = $(this);
                    var toggle = $el.attr('data-bs-toggle');
                    if (toggle && !$el.attr('data-toggle')) {
                        $el.attr('data-toggle', toggle);
                    }
                });

                $('[data-bs-target]').each(function() {
                    var $el = $(this);
                    var target = $el.attr('data-bs-target');
                    if (target && !$el.attr('data-target')) {
                        $el.attr('data-target', target);
                    }
                });

                // Ensure dropdown toggles have the bootstrap class so styling & handlers work
                $('[data-toggle="dropdown"]').each(function() {
                    var $el = $(this);
                    if (!$el.hasClass('dropdown-toggle')) $el.addClass('dropdown-toggle');
                });
            });
        })(jQuery);
    </script>
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

            function dialCodeToCountryCode(dialCode) {
                const countryData = window.intlTelInputGlobals.getCountryData();
                for (const country of countryData) {
                    if (country.dialCode === dialCode.replace('+', '')) {
                        return country.iso2;
                    }
                }
                return "ae"; // default if not found
            }

            const initialNumberCountry = numberCountryInputField?.value
                ? dialCodeToCountryCode(numberCountryInputField.value)
                : 'ae';

            const initialWhatsappCountry = whatsappCountryInputField?.value
                ? dialCodeToCountryCode(whatsappCountryInputField.value)
                : 'ae';

            if(numberInputField) {
                const numberInput = window.intlTelInput(numberInputField, {
                    showSelectedDialCode: true,
                    initialCountry: initialNumberCountry,
                    utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/19.2.19/js/utils.js",
                });
                if (!numberCountryInputField.value) {
                    numberCountryInputField.value = `+${numberInput.getSelectedCountryData().dialCode}`;
                }
                numberInputField.addEventListener("countrychange", function () {
                    const selectedCountryData = numberInput.getSelectedCountryData();
                    numberCountryInputField.value = `+${selectedCountryData.dialCode}`;
                });
            }
            
            if(whatsappInputField) {
                const whatsappInput = window.intlTelInput(whatsappInputField, {
                    showSelectedDialCode: true,
                    initialCountry: initialWhatsappCountry,
                    utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/19.2.19/js/utils.js",
                });

                if (!whatsappCountryInputField.value) {
                    whatsappCountryInputField.value = `+${whatsappInput.getSelectedCountryData().dialCode}`;
                }

                whatsappInputField.addEventListener("countrychange", function () {
                    const selectedCountryData = whatsappInput.getSelectedCountryData();
                    whatsappCountryInputField.value = `+${selectedCountryData.dialCode}`;
                });
            }
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
            $('#number, #whatsapp').on('input', function() {
                $(this).val($(this).val().replace(/\D/g, ''));
            });
        });
    </script>
    <script>
        (function($) {
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
    
            function uploadImage(file) {
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
        })(jQuery);
    </script>
</body>
</html>