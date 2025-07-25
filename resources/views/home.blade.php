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
    .staff-status .card {
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .staff-status .filter-btn.active {
        font-weight: bold;
        border-width: 2px;
    }
    .staff-status #staffSearch:focus {
        box-shadow: none;
        border-color: #86b7fe;
    }
    .staff-status .input-group:focus-within {
        box-shadow: 0 0 0 0.25rem rgba(13,110,253,0.25);
    }
    .staff-status #clearAllFilters {
        transition: all 0.2s ease;
    }
    .staff-status #clearAllFilters:hover {
        background-color: #ffc107;
        color: #212529;
    }
    .staff-status .input-group button {
        border-left: none;
    }
    .staff-status .input-group button:last-child {
        border-left: 1px solid #ced4da;
    }
    .hover-scale {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .hover-scale:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        cursor: pointer;
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
                                                <h6 class="mb-0 fw-bold">Total Registered Users</h6>
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
        <div class="row mt-3">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <span><i class="fas fa-user-plus mr-2"></i> New Joinee Report</span>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Freelancers Section -->
                            <div class="col-md-6">
                                <div class="card mb-3 mb-md-0">
                                    <div class="card-header bg-transparent">
                                        <h6 class="mb-0"><i class="fas fa-user-tie mr-1 text-primary"></i> Freelancers</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row text-center">
                                            <div class="col-3">
                                                <a href="{{ route('freelancerProgram.index') }}" class="text-decoration-none">
                                                    <div class="p-3 border rounded hover-scale" style="background-color: #f8f9fa;">
                                                        <div class="h4 text-primary mb-0">{{ $totalFreelancer }}</div>
                                                        <small class="text-muted">Total</small>
                                                    </div>
                                                </a>
                                            </div>
                                            <div class="col-3">
                                                <a href="{{ route('freelancerProgram.index', ['status' => '2']) }}" class="text-decoration-none">
                                                    <div class="p-3 border rounded hover-scale" style="background-color: #cfe3fc">
                                                        <div class="h4 text-danger mb-0">{{ $newFreelancer }}</div>
                                                        <small class="text-muted">New</small>
                                                    </div>
                                                </a>
                                            </div>
                                            <div class="col-3">
                                                <a href="{{ route('freelancerProgram.index', ['status' => '1']) }}" class="text-decoration-none">
                                                    <div class="p-3 border rounded hover-scale" style="background-color: #cffccf;">
                                                        <div class="h4 text-success mb-0">{{ $acceptedFreelancer }}</div>
                                                        <small class="text-muted">Accepted</small>
                                                    </div>
                                                </a>
                                            </div>
                                            <div class="col-3">
                                                <a href="{{ route('freelancerProgram.index', ['status' => '0']) }}" class="text-decoration-none">
                                                    <div class="p-3 border rounded hover-scale" style="background-color: #fccfcf">
                                                        <div class="h4 text-danger mb-0">{{ $rejectedFreelancer }}</div>
                                                        <small class="text-muted">Rejected</small>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Affiliates Section -->
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header bg-transparent">
                                        <h6 class="mb-0"><i class="fas fa-handshake mr-1 text-info"></i> Affiliates</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row text-center">
                                            <div class="col-3">
                                                <a href="{{ route('affiliateProgram.index') }}" class="text-decoration-none">
                                                    <div class="p-3 border rounded hover-scale" style="background-color: #f8f9fa;">
                                                        <div class="h4 text-primary mb-0">{{ $totalAffiliate }}</div>
                                                        <small class="text-muted">Total</small>
                                                    </div>
                                                </a>
                                            </div>
                                            <div class="col-3">
                                                <a href="{{ route('affiliateProgram.index', ['status' => '2']) }}" class="text-decoration-none">
                                                    <div class="p-3 border rounded hover-scale" style="background-color: #cfe3fc;">
                                                        <div class="h4 text-danger mb-0">{{ $newAffiliate }}</div>
                                                        <small class="text-muted">New</small>
                                                    </div>
                                                </a>
                                            </div>
                                            <div class="col-3">
                                                <a href="{{ route('affiliateProgram.index', ['status' => '1']) }}" class="text-decoration-none">
                                                    <div class="p-3 border rounded hover-scale" style="background-color: #cffccf;">
                                                        <div class="h4 text-success mb-0">{{ $acceptedAffiliate }}</div>
                                                        <small class="text-muted">Accepted</small>
                                                    </div>
                                                </a>
                                            </div>
                                            <div class="col-3">
                                                <a href="{{ route('affiliateProgram.index', ['status' => '0']) }}" class="text-decoration-none">
                                                    <div class="p-3 border rounded hover-scale" style="background-color: #fccfcf">
                                                        <div class="h4 text-danger mb-0">{{ $rejectedAffiliate }}</div>
                                                        <small class="text-muted">Rejected</small>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row pt-3 staff-status">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <span>Staff Status ({{ $staffs->total() }})</span>
                        <div class="input-group" style="width: 350px;">
                            <input type="text" id="staffSearch" class="form-control" placeholder="Search staff by name..." value="{{ request('search') }}">
                            <button class="btn btn-light" type="button" id="searchButton">
                                <i class="fas fa-search"></i>
                            </button>
                            @if(request('search') || request('status'))
                            <button class="btn btn-outline-light" type="button" id="clearButton" title="Clear all filters">
                                <i class="fas fa-times"></i>
                            </button>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Status Summary -->
                        <div class="row mb-4">
                            <div class="col-md-8">
                                <div class="alert alert-info p-2">
                                    <strong>Total Staff:</strong> {{ $staffs->total() }} | 
                                    <span class="text-success"><strong>Online:</strong> {{ $onlineCount }}</span> | 
                                    <span class="text-danger"><strong>Offline:</strong> {{ $offlineCount }}</span> |
                                    <a href="{{ route('serviceStaff.index', ['assignedZone' => 1]) }}" class="btn btn-sm btn-danger py-1 px-2 hover-scale"  title="Filter unassigned zone staff">
                                    <strong>Staff With No Zone:</strong> {{ $unassignedZoneCount }}
                                </a> |
                                <a href="{{ route('serviceStaff.index', ['assignedTimeSlot' => 1]) }}" class="btn btn-sm btn-danger py-1 px-2 hover-scale" title="Filter unassigned timeslot staff">
                                    <strong>Staff With No TimeSlot:</strong> {{ $unassignedTimeSlotCount }}
                                </a>
                                </div>
                            </div>
                            <div class="col-md-4 d-flex justify-content-end align-items-center">
                                <div class="btn-group me-3" role="group">
                                    <button type="button" class="btn btn-sm btn-outline-secondary filter-btn {{ !request('status') ? 'active' : '' }}" data-filter="all">All</button>
                                    <button type="button" class="btn btn-sm btn-outline-success filter-btn {{ request('status') === 'online' ? 'active' : '' }}" data-filter="online">Online</button>
                                    <button type="button" class="btn btn-sm btn-outline-danger filter-btn {{ request('status') === 'offline' ? 'active' : '' }}" data-filter="offline">Offline</button>
                                </div>
                                @if(request('search') || request('status'))
                                <button class="btn btn-sm btn-outline-warning" id="clearAllFilters">
                                    <i class="fas fa-filter-circle-xmark me-1"></i> Clear Filters
                                </button>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Staff List -->
                        <div class="row">
                            @forelse ($staffs as $staff)
                                @if ($staff->staff)
                                    <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 mb-3">
                                        <div class="card h-100">
                                            <a href="{{ route('serviceStaff.index', 'name='.$staff->name) }}" class="text-decoration-none text-dark">
                                                <div class="card-body p-3 d-flex justify-content-between align-items-center">
                                                    <div class="staff-info">
                                                        <h6 class="mb-0 text-truncate" style="max-width: 180px;" title="{{ $staff->name }}">
                                                            {{ $staff->name }}
                                                        </h6>
                                                        <small class="text-muted">{{ $staff->email ?? '' }}</small>
                                                    </div>
                                                    <span class="badge {{ $staff->staff->online ? 'bg-success' : 'bg-danger' }} rounded-pill">
                                                        {{ $staff->staff->online ? 'Online' : 'Offline' }}
                                                    </span>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                @endif
                            @empty
                                <div class="col-12">
                                    <div class="alert alert-warning text-center">No staff found matching your criteria.</div>
                                </div>
                            @endforelse
                        </div>
                        
                        <!-- Pagination -->
                        @if($staffs->hasPages())
                            <div class="row mt-3">
                                <div class="col-md-12 d-flex justify-content-center">
                                    {{ $staffs->appends(request()->query())->links() }}
                                </div>
                            </div>
                        @endif
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
    <script>
        $(document).ready(function() {
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
            
            // Clear search input
            $('#clearButton').on('click', function() {
                $('#staffSearch').val('');
                updateSearch();
            });
            
            // Clear all filters
            $('#clearAllFilters').on('click', function() {
                window.location.href = window.location.pathname;
            });
            
            function updateSearch(status = '{{ request('status') }}') {
                const search = $('#staffSearch').val();
                const params = new URLSearchParams(window.location.search);
                
                if (search) params.set('search', search);
                else params.delete('search');
                
                if (status) params.set('status', status);
                else params.delete('status');
                
                params.delete('page'); // Reset to first page when filtering
                
                window.location.href = window.location.pathname + '?' + params.toString();
            }
        });
    </script>
@endsection
