@extends('layouts.app')
@section('content')
<style>
    body {
      font-family: 'Inter', sans-serif;
      background-color: #f4f6f8;
    }
    .dribbble-card {
      background-color: #fff;
      border-radius: 16px;
      border: 1px solid rgba(220, 220, 220, 0.5);
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08), 0 4px 10px rgba(0, 0, 0, 0.05);
      transition: all 0.2s ease-in-out;
    }
    .dribbble-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    }
    .metric-value {
      font-size: 1.5rem;
      font-weight: 800;
    }
    .section-heading {
      border-left: 4px solid #4f46e5;
      padding-left: 0.5rem;
    }
    .bg-white-10 {
        background-color: rgba(255, 255, 255, 0.1);
    }
    .bg-success-10 {
        background-color: rgba(40, 167, 69, 0.1);
    }
    .bg-danger-10 {
        background-color: rgba(220, 53, 69, 0.1);
    }
</style>

<div class="container p-4">
    @section('page_title')
       <h3 class="font-weight-bold text-dark mt-2 text-center">Dashboard</h3>
    @endsection

    @if(isset(Auth::user()->affiliate_program) && Auth::user()->affiliate_program == 0)
    <div class="alert alert-warning">
        <span>Your request to join the affiliate program has been submitted and sent to the administrator for review.</span>
    </div>
    @endif
    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <span>{{ $message }}</span>
            <button type="button" class="btn-close float-end" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @can('dashboard-report')
        @if (auth()->user()->hasRole('Admin'))
        <!-- Financial Metrics -->
        <section class="mb-5">
            <h2 class="h3 text-secondary mb-4 section-heading">Financial Performance</h2>
            <div class="row">
                <div class="col-sm-6 col-lg-3 mb-4">
                    <div class="dribbble-card p-4">
                        <p class="text-uppercase small text-muted mb-3">Total Sales</p>
                        <div class="d-flex justify-content-between align-items-end">
                            <p class="metric-value text-danger">@currency($sale,true)</p>
                            <i data-lucide="line-chart" class="w-8 h-8 text-danger" style="opacity:0.6"></i>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3 mb-4">
                    <div class="dribbble-card p-4">
                        <p class="text-uppercase small text-muted mb-3">Affiliate Commission</p>
                        <div class="d-flex justify-content-between align-items-end">
                            <p class="metric-value text-success">@currency($affiliate_commission,true)</p>
                            <i data-lucide="user-check" class="text-success" style="opacity:0.6"></i>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3 mb-4">
                    <div class="dribbble-card p-4">
                        <p class="text-uppercase small text-muted mb-3">Staff Commission</p>
                        <div class="d-flex justify-content-between align-items-end">
                            <p class="metric-value text-primary">@currency($staff_commission,true)</p>
                            <i data-lucide="users" class="text-primary" style="opacity:0.6"></i>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3 mb-4">
                    <div class="dribbble-card p-3">
                            <p class="text-uppercase small text-muted mb-3">CRM Quotes Today</p>
                            <div class="d-flex justify-content-between align-items-end">
                                <p class="metric-value text-warning">{{ $todayCrms }}</p>
                                <i data-lucide="file-text" class="text-warning" style="opacity:0.6"></i>
                            </div>
                            <a href="{{ route('crms.index') }}" class="small text-warning font-weight-bold d-block mt-2">See All Quotes →</a>
                    </div>
                </div>
            </div>
        </section>

        <!-- User & Orders & Staff -->
        <section class="row mb-5">
            <div class="col-lg-4 mb-4">
                <h2 class="h3 text-secondary mb-4 section-heading">User & Order Today</h2>
                <div class="mb-4 dribbble-card text-center p-4">
                    <i data-lucide="users-2" class="mb-3 text-muted"></i>
                    <p class="text-uppercase small text-muted">Total Registered Users</p>
                    <p class="display-4 font-weight-bold text-dark">{{ $todayAppUser }}</p>
                </div>
                <div class="mb-4 dribbble-card text-center p-4">
                    <i data-lucide="zap" class="mb-3 text-muted"></i>
                    <p class="text-uppercase small text-muted">Active Users</p>
                    <p class="display-4 font-weight-bold text-dark">{{ $todayLoginAppUser }}</p>
                </div>
                <div class="dribbble-card text-center p-4">
                    <i data-lucide="shopping-cart" class="mb-3 text-muted"></i>
                    <p class="text-uppercase small text-muted">Today's Orders</p>
                    <p class="display-4 font-weight-bold text-dark">{{ $todayAppOrder }}</p>
                </div>
            </div>

            <div class="col-lg-8">
                <h2 class="h3 text-secondary mb-4 section-heading">New Joinee Report</h2>
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <div class="dribbble-card p-4 text-white" style="background: linear-gradient(135deg, #6366f1, #4338ca);">
                            <div class="d-flex justify-content-between">
                                <h3 class="h5 font-weight-bold"><i data-lucide="monitor" class="mr-2"></i> Freelancers</h3>
                                <a href="{{ route('freelancerProgram.index') }}" class="badge badge-light">Total {{ $totalFreelancer }}</a>
                            </div>
                            <div class="row text-center mt-4">
                                <div class="col p-2 bg-white-10 rounded">
                                    <a href="{{ route('freelancerProgram.index', ['status' => '2']) }}" class="text-white text-decoration-none">
                                        <p class="h3 font-weight-bold">{{ $newFreelancer }}</p>
                                        <small>New</small>
                                    </a>
                                </div>
                                <div class="col p-2 bg-white-10 rounded">
                                    <a href="{{ route('freelancerProgram.index', ['status' => '1']) }}" class="text-white text-decoration-none">
                                        <p class="h3 font-weight-bold">{{ $acceptedFreelancer }}</p>
                                        <small>Accepted</small>
                                    </a>
                                </div>
                                <div class="col p-2 bg-white-10 rounded">
                                    <a href="{{ route('freelancerProgram.index', ['status' => '0']) }}" class="text-white text-decoration-none">
                                        <p class="h3 font-weight-bold">{{ $rejectedFreelancer }}</p>
                                        <small>Rejected</small>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-4">
                        <div class="dribbble-card p-4 text-white" style="background: linear-gradient(135deg, #8b5cf6, #ec4899);">
                            <div class="d-flex justify-content-between">
                                <h3 class="h5 font-weight-bold"><i data-lucide="link" class="mr-2"></i> Affiliates</h3>
                                <a href="{{ route('affiliateProgram.index') }}" class="badge badge-light">Total {{ $totalAffiliate }}</a>
                            </div>
                            <div class="row text-center mt-4">
                                <div class="col p-2">
                                    <a href="{{ route('affiliateProgram.index', ['status' => '2']) }}" class="text-white text-decoration-none">
                                        <p class="h3 font-weight-bold">{{ $newAffiliate }}</p>
                                        <small>New</small>
                                    </a>
                                </div>
                                <div class="col p-2">
                                    <a href="{{ route('affiliateProgram.index', ['status' => '1']) }}" class="text-white text-decoration-none">
                                        <p class="h3 font-weight-bold">{{ $acceptedAffiliate }}</p>
                                        <small>Accepted</small>
                                    </a>
                                </div>
                                <div class="col p-2">
                                    <a href="{{ route('affiliateProgram.index', ['status' => '0']) }}" class="text-white text-decoration-none">
                                        <p class="h3 font-weight-bold">{{ $rejectedAffiliate }}</p>
                                        <small>Rejected</small>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <h2 class="h3 text-secondary mb-4 section-heading">Staff Availability</h2>
                <div class="dribbble-card p-4">
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-3">
                        <p class="text-muted mb-0">Total Staff Count</p>
                        <span class="display-4 font-weight-bold text-dark">{{ $staffs->total() }}</span>
                    </div>
                    <ul class="list-unstyled mb-0">
                        <li class="d-flex justify-content-between align-items-center bg-success-10 p-3 mb-2 rounded">
                            <div class="text-success font-weight-bold d-flex align-items-center">
                                <i data-lucide="check-circle" class="mr-2"></i> Online
                            </div>
                            <span class="h5 mb-0 text-dark">{{ $onlineCount }}</span>
                        </li>
                        <li class="d-flex justify-content-between align-items-center bg-danger-10 p-3 mb-2 rounded">
                            <div class="text-danger font-weight-bold d-flex align-items-center">
                                <i data-lucide="x-circle" class="mr-2"></i> Offline
                            </div>
                            <span class="h5 mb-0 text-dark">{{ $offlineCount }}</span>
                        </li>
                    </ul>
                    <div class="border-top pt-3 mt-3">
                        <a href="{{ route('serviceStaff.index', ['assignedZone' => 1]) }}" class="d-flex justify-content-between align-items-center text-primary font-weight-bold">
                            Staff with No Zone
                            <span class="badge badge-primary">{{ $unassignedZoneCount }}</span>
                        </a>
                        <a href="{{ route('serviceStaff.index', ['assignedTimeSlot' => 1]) }}" class="d-flex justify-content-between align-items-center text-primary font-weight-bold mt-2">
                            Staff with No TimeSlot
                            <span class="badge badge-primary">{{ $unassignedTimeSlotCount }}</span>
                        </a>
                    </div>
                </div>
            </div>
        </section>
        @endif

        @if (auth()->user()->hasRole('Staff'))
            <section class="mb-5">
                <h2 class="h3 text-secondary mb-4 section-heading">Staff Dashboard</h2>
                <div class="row">
                    <div class="col-md-4 mb-4">
                        <div class="dribbble-card p-4">
                            <p class="text-uppercase small text-muted mb-3">Salary</p>
                            <div class="d-flex justify-content-between align-items-end">
                                <p class="metric-value text-primary">@currency(auth()->user()->staff->fix_salary,true)</p>
                                <i data-lucide="wallet" class="text-primary" style="opacity:0.6"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="dribbble-card p-4">
                            <p class="text-uppercase small text-muted mb-3">Total Balance</p>
                            <div class="d-flex justify-content-between align-items-end">
                                <p class="metric-value text-primary">@currency($staff_total_balance,true)</p>
                                <i data-lucide="credit-card" class="text-primary" style="opacity:0.6"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="dribbble-card p-4">
                            <p class="text-uppercase small text-muted mb-3">Product Sale of {{ now()->format('F') }}</p>
                            <div class="d-flex justify-content-between align-items-end">
                                <p class="metric-value text-success">@currency($staff_product_sales,true)</p>
                                <i data-lucide="dollar-sign" class="text-success" style="opacity:0.6"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="dribbble-card p-4">
                            <p class="text-uppercase small text-muted mb-3">Total Bonus of {{ now()->format('F') }}</p>
                            <div class="d-flex justify-content-between align-items-end">
                                <p class="metric-value text-success">@currency($staff_bonus,true)</p>
                                <i data-lucide="award" class="text-success" style="opacity:0.6"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="dribbble-card p-4">
                            <p class="text-uppercase small text-muted mb-3">Total Order Commission of {{ now()->format('F') }}</p>
                            <div class="d-flex justify-content-between align-items-end">
                                <p class="metric-value text-success">@currency($staff_order_commission,true)</p>
                                <i data-lucide="briefcase" class="text-success" style="opacity:0.6"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="dribbble-card p-4">
                            <p class="text-uppercase small text-muted mb-3">Other Income of {{ now()->format('F') }}</p>
                            <div class="d-flex justify-content-between align-items-end">
                                <p class="metric-value text-success">@currency($staff_other_income,true)</p>
                                <i data-lucide="trending-up" class="text-success" style="opacity:0.6"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        @endif
    @endcan

    @if(auth()->user()->hasRole('Supervisor'))
    <section class="mb-5">
        <h2 class="h3 text-secondary mb-4 section-heading">My Staff</h2>
        <div class="dribbble-card p-4">
            <div class="row">
                @php
                    $supervisorStaffIds = auth()->user()->getSupervisorStaffIds();
                @endphp
                @forelse ($staffs->whereIn('id', $supervisorStaffIds) as $staff)
                    @if ($staff->staff)
                        <div class="col-md-3 mb-3">
                            <div class="p-3 border rounded text-center">
                                <i data-lucide="user" class="mb-2"></i>
                                <div>{{ $staff->name }}</div>
                            </div>
                        </div>
                    @endif
                @empty
                    <div class="col-12 text-center text-muted py-5">No staff available.</div>
                @endforelse
            </div>
        </div>
    </section>
    @endif

    @if(auth()->user()->hasRole('Manager'))
    <section class="mb-5">
        <h2 class="h3 text-secondary mb-4 section-heading">Supervisor and Staff</h2>
        <div class="dribbble-card p-4">
            @if(count(auth()->user()->managerSupervisors) > 0)
                @foreach (auth()->user()->managerSupervisors as $managerSupervisor)
                    @php
                        $supervisor = $managerSupervisor->supervisor;
                        $supervisorStaffIds = $supervisor ? $supervisor->getSupervisorStaffIds() : [];
                    @endphp

                    @if ($supervisor)
                        <div class="mb-4">
                            <h5 class="font-weight-bold d-flex align-items-center"><i data-lucide="user-check" class="mr-2"></i> Supervisor: {{ $supervisor->name }}</h5>
                            @if (count($supervisorStaffIds) > 0)
                                <div class="row mt-3">
                                    @foreach ($staffs->whereIn('id', $supervisorStaffIds) as $staff)
                                        @if ($staff->staff)
                                            <div class="col-md-3 mb-3">
                                                <div class="p-3 border rounded text-center">
                                                    <i data-lucide="user" class="mb-2"></i>
                                                    <div>{{ $staff->name }}</div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted">No staff available for this supervisor.</p>
                            @endif
                        </div>
                    @else
                        <p class="text-muted">No supervisor available.</p>
                    @endif
                @endforeach
            @else
                <p class="text-muted">No supervisors available for this manager.</p>
            @endif
        </div>
    </section>
    @endif

    @if(auth()->user()->hasRole('Staff'))
    <section class="mb-5">
        <h2 class="h3 text-secondary mb-4 section-heading">My Commissions</h2>
        <div class="dribbble-card p-4">
            @if(isset(auth()->user()->staff) && auth()->user()->staff->commission)
                <div class="alert alert-info">
                    <strong>Global Commission:</strong> {{ auth()->user()->staff->commission }}% applied.
                </div>
            @endif

            @if(auth()->user()->affiliateCategories->isNotEmpty())
                <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>Category</th>
                            <th>Services</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach(auth()->user()->affiliateCategories as $category)
                            <tr>
                                <td>{{ $category->category->title }}</td>
                                <td>
                                    @if($category->services->isNotEmpty())
                                        <table class="table table-sm table-bordered mb-2">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th>Service</th>
                                                    <th>Commission</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($category->services as $service)
                                                    <tr>
                                                        <td>{{ $service->service->name }}</td>
                                                        <td>
                                                            {{ $service->commission ?: auth()->user()->staff->commission }}
                                                            {{ $service->commission_type == 'percentage' ? '%' : 'Fixed' }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        <div class="alert alert-info p-2">
                                            {{ $category->commission }} {{ $category->commission_type == 'percentage' ? '%' : 'Fixed' }} commission on all other services.
                                        </div>
                                    @else
                                        <div class="alert alert-info p-2">
                                            {{ $category->commission }} {{ $category->commission_type == 'percentage' ? '%' : 'Fixed' }} commission on all services.
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @elseif(!isset(auth()->user()->staff) || !auth()->user()->staff->commission)
                <div class="alert alert-warning text-center">
                    No commissions available.
                </div>
            @endif
        </div>
    </section>
    @endif

    @can('order-list')
    <section>
        <h2 class="h3 text-secondary mb-4 section-heading">Today's Bookings ({{ $orderCountToday }})</h2>
        <div class="dribbble-card p-3">
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                <div class="col-md-4">
                    <div class="input-group">
                        <input type="text" id="staffSearch" class="form-control" placeholder="Search orders...">
                        <div class="input-group-append">
                            <button id="searchButton" class="btn btn-primary" type="button"><i data-lucide="search"></i></button>
                        </div>
                    </div>
                </div>
                <div class="d-flex flex-wrap justify-content-end">
                    @can('order-create')
                        <a class="btn btn-primary btn-sm mb-2 mr-2" href="{{ route('orders.create') }}"><i data-lucide="plus" class="mr-1"></i> Create Order</a>
                    @endcan

                    <div class="dropdown mb-2 mr-2">
                        <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" id="exportDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i data-lucide="download" class="mr-1"></i> Export
                        </button>
                        <div class="dropdown-menu" aria-labelledby="exportDropdown">
                            <a class="dropdown-item" href="/orders?print=1"><i data-lucide="file-text" class="mr-1"></i> PDF</a>
                            <a class="dropdown-item" href="/orders?csv=1"><i data-lucide="file-spreadsheet" class="mr-1"></i> Excel</a>
                        </div>
                    </div>
                    <div class="dropdown mb-2 mr-2">
                        <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" id="todayViewsDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i data-lucide="calendar-day" class="mr-1"></i> Today's Views
                        </button>
                        <div class="dropdown-menu" aria-labelledby="todayViewsDropdown">
                            <a class="dropdown-item" href="{{ route('orders.index') }}?appointment_date={{ date('Y-m-d') }}"><i data-lucide="calendar-clock" class="mr-1"></i> All Today's Orders</a>
                            <a class="dropdown-item" href="{{ route('orders.index') }}?appointment_date={{ date('Y-m-d') }}&driver_dropped=true"><i data-lucide="calendar" class="mr-1"></i> Dropped Orders</a>
                            <a class="dropdown-item" href="{{ route('orders.index') }}?appointment_date={{ date('Y-m-d') }}&status=Canceled"><i data-lucide="calendar-x" class="mr-1"></i> Canceled Orders</a>
                            <a class="dropdown-item" href="{{ route('orders.index') }}?appointment_date={{ date('Y-m-d') }}&status=Complete"><i data-lucide="calendar-check" class="mr-1"></i> Completed Orders</a>
                        </div>
                    </div>

                    <div class="dropdown mb-2 mr-2">
                        <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" id="statusFilterDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i data-lucide="filter" class="mr-1"></i> Filter by Status
                        </button>
                        <div class="dropdown-menu" aria-labelledby="statusFilterDropdown">
                            @if (auth()->user()->hasRole('Admin'))
                                <a class="dropdown-item" href="/orders"><i data-lucide="list" class="mr-1"></i> All</a>
                                <a class="dropdown-item" href="/orders?status=Canceled"><i data-lucide="x" class="mr-1"></i> Canceled</a>
                            @endif
                            @if (!auth()->user()->hasRole('Staff'))
                                <a class="dropdown-item" href="/orders?status=Pending"><i data-lucide="clock" class="mr-1"></i> Pending</a>
                                <a class="dropdown-item" href="/orders?status=Rejected"><i data-lucide="x-circle" class="mr-1"></i> Rejected</a>
                                <a class="dropdown-item" href="/orders?status=Inprogress"><i data-lucide="hourglass" class="mr-1"></i> Inprogress</a>
                                <a class="dropdown-item" href="/orders?status=Complete"><i data-lucide="check-circle" class="mr-1"></i> Complete</a>
                                <a class="dropdown-item" href="/orders?status=Accepted"><i data-lucide="check" class="mr-1"></i> Accepted</a>
                                <a class="dropdown-item" href="/orders?status=Confirm"><i data-lucide="check-check" class="mr-1"></i> Confirm</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                @include('orders.list')
            </div>
        </div>
    </section>
    @endcan
</div>

<script>
    // Wait for the DOM to be ready
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Lucide icons
        lucide.createIcons();

        // Your existing script logic
        $('#searchButton').on('click', function(e) {
            if (e.type === 'click') {
                updateSearch();
            }
        });

        $('.filter-btn').on('click', function() {
            const filter = $(this).data('filter');
            $('.filter-btn').removeClass('active');
            $(this).addClass('active');
            updateSearch(filter === 'all' ? '' : filter);
        });

        $('#clearButton').on('click', function() {
            $('#staffSearch').val('');
            updateSearch();
        });

        $('#clearAllFilters').on('click', function() {
            window.location.href = window.location.pathname;
        });

        $('#cacheClearBtn').on('click', function() {
            var req1 = $.ajax({
                url: '/delete-lipslay-cache',
                type: 'GET'
            });
            var req2 = $.ajax({
                url: '/clear-cache',
                type: 'GET'
            });

            $.when(req1, req2).done(function(r1, r2) {
                alert('Cache cleared successfully!');
            }).fail(function() {
                alert('Failed to clear cache.');
            });
        });

        function updateSearch(status = '{{ request('status') }}') {
            const search = $('#staffSearch').val();
            const params = new URLSearchParams(window.location.search);

            if (search) params.set('search', search);
            else params.delete('search');

            if (status) params.set('status', status);
            else params.delete('status');

            params.delete('page');

            window.location.href = window.location.pathname + '?' + params.toString();
        }
    });
</script>
@endsection