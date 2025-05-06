@extends('layouts.app')
<style>
    .analytic {
        line-height: 48px;
    }

    .analytic i {
        font-size: 3rem;
        opacity: 0.5;
    }

    .analytic span {
        font-size: 2.5rem;
        display: inline;
    }
</style>
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <h2>Dashboard</h2>
            </div>
            <div class="col-md-6">
                @if (auth()->user()->hasRole('Admin'))
                    <a class="btn btn-success float-end" href="{{ route('appData') }}"> Refresh App</a>
                    <a class="btn btn-danger float-end mr-2" href="{{ route('cache.clear') }}"> Cache Clear</a>
                @endif
                @if (auth()->user()->hasRole('Affiliate'))
                    <a class="btn btn-success float-end" href="{{ route('affiliate_dashboard.index') }}">Affiliate DashBorad</a>
                @endif
            </div>
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
                <div class="row">
                    <div class="col-md-8">
                        <div class="row">
                            <div class="col-md-6 mt-5">
                                <div class="card">
                                    <div class="card-header">TOTAL SALES</div>
                                    <div class="card-body analytic">
                                        <i class="fa fa-credit-card"></i>
                                        <span class="float-md-end">@currency($sale,true)</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mt-5">
                                <div class="card">
                                    <div class="card-header">TOTAL AFFILIATE COMMISSION</div>
                                    <div class="card-body analytic">
                                        <i class="fa fa-dollar-sign"></i>
                                        <span class="float-md-end">@currency($affiliate_commission,true)</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mt-5">
                                <div class="card">
                                    <div class="card-header">TOTAL STAFF COMMISSION</div>
                                    <div class="card-body analytic">
                                        <i class="fa fa-dollar-sign"></i>
                                        <span class="float-md-end">@currency($staff_commission,true)</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mt-5">
                                <div class="card">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <span>CRM Quotes Today</span>
                                        <a href="{{ route('crms.index') }}" class="small text-primary text-decoration-none">See All</a>
                                    </div>
                                    <div class="card-body analytic">
                                        <i class="fa fa-chart-bar"></i>
                                        <span class="float-md-end">{{ $todayCrms }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                    <div class="col-md-4 mt-3">
                        <div class="card h-100"> <!-- Added h-100 for consistent height -->
                            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                <span class="fw-bold">Today's Application Report</span>
                                <i class="fas fa-calendar-day"></i>
                            </div>
                            <div class="card-body">
                                <div class="row g-4"> <!-- Added gutter spacing -->
                                    <!-- Users Stats -->
                                    <div class="col-12">
                                        <div class="d-flex align-items-center p-2 bg-light rounded">
                                            <div class="bg-primary bg-opacity-10 p-2 rounded me-3">
                                                <i class="fa fa-users text-white"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 fw-bold">Total Register Users</h6>
                                                <p class="mb-0 fs-5">{{ $todayAppUser }}</p>
                                            </div>
                                        </div>
                                    </div>
                    
                                    <!-- Logged-in Users -->
                                    <div class="col-12">
                                        <div class="d-flex align-items-center p-2 bg-light rounded">
                                            <div class="bg-success bg-opacity-10 p-2 rounded me-3">
                                                <i class="fas fa-user-check text-white"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 fw-bold">Active Users</h6>
                                                <p class="mb-0 fs-5">{{ $todayLoginAppUser }}</p>
                                            </div>
                                        </div>
                                    </div>
                    
                                    <!-- Orders -->
                                    <div class="col-12">
                                        <div class="d-flex align-items-center p-2 bg-light rounded">
                                            <div class="bg-warning bg-opacity-10 p-2 rounded me-3">
                                                <i class="fa fa-shopping-cart text-white"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 fw-bold">Today's Orders</h6>
                                                <p class="mb-0 fs-5">{{ $todayAppOrder }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                    
                    
                @endif

                @if (auth()->user()->hasRole('Staff'))
                    <div class="col-md-4 py-2">
                        <div class="card">
                            <div class="card-header">Salary</div>
                            <div class="card-body analytic">
                                <i class="fa fa-credit-card"></i>
                                <span class="float-md-end">@currency(auth()->user()->staff->fix_salary,true)</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 py-2">
                        <div class="card">
                            <div class="card-header">Total Balance</div>
                            <div class="card-body analytic">
                                <i class="fa fa-credit-card"></i>
                                <span class="float-md-end">@currency($staff_total_balance,true)</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 py-2">
                        <div class="card">
                            <div class="card-header">Product Sale of {{ now()->format('F') }}</div>
                            <div class="card-body analytic">
                                <i class="fa fa-dollar-sign"></i>
                                <span class="float-md-end">@currency($staff_product_sales,true)</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 py-2">
                        <div class="card">
                            <div class="card-header">Total Bonus of {{ now()->format('F') }}</div>
                            <div class="card-body analytic">
                                <i class="fa fa-dollar-sign"></i>
                                <span class="float-md-end">@currency($staff_bonus,true)</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 py-2">
                        <div class="card">
                            <div class="card-header">Total Order Commission of {{ now()->format('F') }}</div>
                            <div class="card-body analytic">
                                <i class="fa fa-dollar-sign"></i>
                                <span class="float-md-end">@currency($staff_order_commission,true)</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 py-2">
                        <div class="card">
                            <div class="card-header">Other Income of {{ now()->format('F') }}</div>
                            <div class="card-body analytic">
                                <i class="fa fa-dollar-sign"></i>
                                <span class="float-md-end">@currency($staff_other_income,true)</span>
                            </div>
                        </div>
                    </div>
                @endif
            @endcan
        </div>
        @if(auth()->user()->hasRole('Admin'))
        <div class="row pt-5">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">Staff Status</div>
                    <div class="card-body">
                        <div class="row">
                            @forelse ($staffs as $staff)
                                @if ($staff->staff) {{-- Ensure staff relationship exists --}}
                                    <div class="col-md-4 mb-3"> {{-- Each staff takes 1/3 of the row --}}
                                        <div class="list-group">
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                {{ $staff->name }}
                                                <span class="badge {{ $staff->staff->online ? 'bg-success' : 'bg-danger' }}">
                                                    {{ $staff->staff->online ? 'Online' : 'Offline' }}
                                                </span>
                                            </li>
                                        </div>
                                    </div>
                                @endif
                            @empty
                                <li class="list-group-item text-center">No staff available.</li>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
        @if(auth()->user()->hasRole('Supervisor'))
        <div class="row pt-3">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">Staffs</div>
                    <div class="card-body">
                        <div class="row">
                            @php
                                // Get the supervisor staff IDs
                                $supervisorStaffIds = auth()->user()->getSupervisorStaffIds(); // Assuming you're using auth to get current user
                            @endphp
        
                            @forelse ($staffs->whereIn('id', $supervisorStaffIds) as $staff)
                                @if ($staff->staff) {{-- Ensure staff relationship exists --}}
                                    <div class="col-md-3 mb-3"> <!-- 4 per row -->
                                        <li class="list-group-item">
                                            {{ $staff->name }}
                                        </li>
                                    </div>
                                @endif
                            @empty
                                <li class="list-group-item text-center">No staff available.</li>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        @if(auth()->user()->hasRole('Manager'))
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">Supervisor and Their Staff</div>
                    <div class="card-body">
                        @if(count(auth()->user()->managerSupervisors) > 0)
                            @foreach (auth()->user()->managerSupervisors as $managerSupervisor)
                                @php
                                    $supervisor = $managerSupervisor->supervisor;
                                    $supervisorStaffIds = $supervisor ? $supervisor->getSupervisorStaffIds() : [];
                                @endphp
        
                                @if ($supervisor)
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <strong>Supervisor:</strong> {{ $supervisor->name }}
                                        </div>
                                    </div>
        
                                    @if (count($supervisorStaffIds) > 0)
                                        @php
                                            $chunkedStaff = $staffs->whereIn('id', $supervisorStaffIds)->chunk(4);
                                        @endphp
        
                                        @foreach ($chunkedStaff as $staffGroup)
                                            <div class="col-md-12">
                                                <div class="row">
                                                    @foreach ($staffGroup as $staff)
                                                        @if ($staff->staff)
                                                            <div class="col-md-3 mb-3">
                                                                <div class="list-group-item">
                                                                    {{ $staff->name }}
                                                                </div>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="row mb-3">
                                            <div class="col-md-12">
                                                <em>No staff available for this supervisor.</em>
                                            </div>
                                        </div>
                                    @endif
                                @else
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <em>No supervisor available.</em>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        @else
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <em>No supervisor available for this manager.</em>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif
        
        @if(auth()->user()->hasRole('Staff'))
        <div class="row py-5">
            <div class="col-md-12">
                <h4>Staff Commissions</h4>
        
                @if(isset(auth()->user()->staff) && auth()->user()->staff->commission)
                    <div class="alert alert-info">
                        <strong>Global Commission:</strong> {{ auth()->user()->staff->commission }}% 
                        applied.
                    </div>
                @endif
        
                @if(auth()->user()->affiliateCategories->isNotEmpty()) 
                    <table class="table table-bordered table-striped">
                        <thead>
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
                                            <table class="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Service</th>
                                                        <th>Service Commission</th>
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
                                            <div class="alert alert-info">
                                                {{ $category->commission }} {{ $category->commission_type == 'percentage' ? '%' : 'Fixed' }} commission on all other services.
                                            </div>
                                        @else
                                            <div class="alert alert-info">
                                                {{ $category->commission }} {{ $category->commission_type == 'percentage' ? '%' : 'Fixed' }} commission on all services.
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @elseif(!isset(auth()->user()->staff) || !auth()->user()->staff->commission)
                    <div class="alert alert-warning">
                        No commissions available.
                    </div>
                @endif
            </div>
        </div>        
        @endcan
        @can('order-list')
            <div class="py-2"></div>
            <div class="row">
                <div class="col-md-12 text-center mb-3">
                    <h2>Orders</h2>
                </div>
                <div class="col-md-12 mb-3">
                    <div class="d-flex flex-wrap justify-content-md-end">
                        @if (!auth()->user()->hasRole('Supervisor'))
                            @can('order-download')
                                <a class="btn btn-danger mb-2" href="/orders?print=1"><i class="fa fa-print"></i> PDF</a>
                                <a href="/orders?csv=1" class="btn btn-success mb-2 ms-md-2"><i class="fa fa-download"></i>
                                    Excel</a>
                            @endcan
                        @endif

                        @if (auth()->user()->hasRole('Admin'))
                            <a class="btn btn-secondary mb-2 ms-md-2" href="/orders">
                                <i class="fas fa-list"></i> All
                            </a>
                            <a class="btn btn-danger mb-2 ms-md-2" href="/orders?status=Canceled">
                                <i class="fas fa-times"></i> Canceled
                            </a>
                        @endif

                        @if (!auth()->user()->hasRole('Staff'))
                            <a class="btn btn-primary mb-2 ms-md-2" href="/orders?status=Pending">
                                <i class="fas fa-clock"></i> Pending
                            </a>
                            <a class="btn btn-warning mb-2 ms-md-2" href="/orders?status=Rejected">
                                <i class="fas fa-times"></i> Rejected
                            </a>
                            <a class="btn btn-info mb-2 ms-md-2" href="/orders?status=Inprogress">
                                <i class="fas fa-hourglass-split"></i> Inprogress
                            </a>
                            <a class="btn btn-success mb-2 ms-md-2" href="/orders?status=Complete">
                                <i class="fas fa-check"></i> Complete
                            </a>
                            <a class="btn btn-success mb-2 ms-md-2" href="/orders?status=Accepted">
                                <i class="fas fa-check"></i> Accepted
                            </a>
                            <a class="btn btn-info mb-2 ms-md-2" href="/orders?status=Confirm">
                                <i class="fas fa-check"></i> Confirm
                            </a>
                        @endif

                        

                        @can('order-create')
                            <a class="btn btn-success mb-2 ms-md-2" href="{{ route('orders.create') }}">
                                <i class="fas fa-plus"></i> Create Order
                            </a>
                        @endcan
                        <a class="btn btn-warning mb-2 ms-md-2"
                            href="{{ route('orders.index') }}?appointment_date={{ date('Y-m-d') }}&driver_dropped=true">
                            <i class="fas fa-calendar"></i> Todays Drop Order
                        </a>
                        <a class="btn btn-danger mb-2 ms-md-2"
                            href="{{ route('orders.index') }}?appointment_date={{ date('Y-m-d') }}&status=Canceled">
                            <i class="fas fa-calendar"></i> Todays Canceled Order
                        </a>
                        <a class="btn btn-success mb-2 ms-md-2"
                            href="{{ route('orders.index') }}?appointment_date={{ date('Y-m-d') }}&status=Complete">
                            <i class="fas fa-calendar"></i> Todays Complete Order
                        </a>
                        <a class="btn btn-secondary mb-2 ms-md-2"
                            href="{{ route('orders.index') }}?appointment_date={{ date('Y-m-d') }}">
                            <i class="fas fa-calendar"></i> Todays Order
                        </a>
                    </div>
                </div>
            </div>


            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            @include('orders.list')
                        </div>
                    </div>
                </div>
            </div>
        @endcan
    </div>
@endsection
