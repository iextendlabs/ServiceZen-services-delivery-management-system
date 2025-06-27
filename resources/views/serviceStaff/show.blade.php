@extends('layouts.app')
@section('content')
    <style>
        .bg-purple {
            background-color: #6f42c1;
        }

        .bg-teal {
            background-color: #20c997;
        }

        .bg-orange {
            background-color: #fd7e14;
        }

        .bg-indigo {
            background-color: #6610f2;
        }

        .bg-light-pink {
            background-color: #fff0f6;
        }

        .bg-light-green {
            background-color: #f0fff4;
        }

        .bg-light-blue {
            background-color: #f0f8ff;
        }

        .bg-light-yellow {
            background-color: #fffaf0;
        }

        .bg-light-purple {
            background-color: #f8f0ff;
        }

        .bg-light-gray {
            background-color: #f8f9fa;
        }

        .bg-light-red {
            background-color: #fff0f0;
        }

        .info-item h6 {
            font-size: 0.85rem;
        }

        .info-item p {
            font-size: 1rem;
        }

        .card-header {
            border-radius: 0.25rem 0.25rem 0 0 !important;
        }

        .service-item {
            transition: all 0.3s ease;
        }

        .service-item:hover {
            background-color: #e9ecef;
            transform: translateY(-2px);
        }

        .services-container {
            scrollbar-width: thin;
            scrollbar-color: #6c757d #f8f9fa;
        }

        .services-container::-webkit-scrollbar {
            width: 8px;
        }

        .services-container::-webkit-scrollbar-track {
            background: #f8f9fa;
        }

        .services-container::-webkit-scrollbar-thumb {
            background-color: #6c757d;
            border-radius: 4px;
        }

        .service-item {
            transition: all 0.2s ease;
            padding: 10px 15px;
        }

        .service-item:hover {
            background-color: #f8f9fa;
            transform: translateX(5px);
        }

        .no-gutters {
            margin-right: 0;
            margin-left: 0;
        }

        .no-gutters>.col,
        .no-gutters>[class*="col-"] {
            padding-right: 0;
            padding-left: 0;
        }

        .categories-container {
            scrollbar-width: thin;
            scrollbar-color: #6c757d #f8f9fa;
        }

        .categories-container::-webkit-scrollbar {
            width: 8px;
        }

        .categories-container::-webkit-scrollbar-track {
            background: #f8f9fa;
        }

        .categories-container::-webkit-scrollbar-thumb {
            background-color: #6c757d;
            border-radius: 4px;
        }

        .category-item {
            transition: all 0.2s ease;
            padding: 10px 15px;
        }

        .category-item:hover {
            background-color: #f8f9fa;
            transform: translateX(5px);
        }

        .subtitles-container {
            scrollbar-width: thin;
            scrollbar-color: #6c757d #f8f9fa;
        }

        .subtitles-container::-webkit-scrollbar {
            width: 8px;
        }

        .subtitles-container::-webkit-scrollbar-track {
            background: #f8f9fa;
        }

        .subtitles-container::-webkit-scrollbar-thumb {
            background-color: #6c757d;
            border-radius: 4px;
        }

        .subtitle-item {
            transition: all 0.2s ease;
            padding: 10px 15px;
        }

        .subtitle-item:hover {
            background-color: #f8f9fa;
            transform: translateX(5px);
        }
    </style>
    <div class="container">
        <!-- Header Section -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="mb-0">
                        <i class="fas fa-user-tie"></i> Service Staff Profile
                    </h2>
                    <div>
                        @if (request('freelancer_join') == 1)
                            @if ($serviceStaff->freelancer_program === '0')
                                @can('freelancer-program-edit')
                                    <a class="btn btn-success mr-2"
                                        href="{{ route('freelancerProgram.edit', $serviceStaff->id) }}?status=Accepted">
                                        <i class="fas fa-thumbs-up"></i>
                                    </a>
                                @endcan
                            @else
                                @can('freelancer-program-edit')
                                    <a class="btn btn-danger mr-2"
                                        href="{{ route('freelancerProgram.edit', $serviceStaff->id) }}?status=Rejected">
                                        <i class="fas fa-thumbs-down"></i>
                                    </a>
                                @endcan
                            @endif
                            <form id="deleteForm{{ $serviceStaff->id }}" class="d-inline"
                                action="{{ route('freelancerProgram.destroy', $serviceStaff->id) }}" method="POST">
                                @csrf
                                @can('freelancer-program-delete')
                                    @method('DELETE')
                                    <button type="button" onclick="confirmDelete('{{ $serviceStaff->id }}')"
                                        class="btn btn-danger"><i class="fa fa-trash"></i></button>
                                @endcan
                            </form>
                        @else
                            @can('service-staff-edit')
                                <a class="btn btn-primary" href="{{ route('serviceStaff.edit', $serviceStaff->id) }}"><i
                                        class="fa fa-edit"></i></a>
                            @endcan
                        @endif
                    </div>
                </div>
                <hr class="mt-2">
            </div>
        </div>

        <!-- Success/Error Messages -->
        @if ($message = Session::get('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <span>{{ $message }}</span>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show">
                <strong>Whoops!</strong> There were some problems with your input.
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Profile Card -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-id-card"></i> Basic Information
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Profile Image -->
                    <div class="col-md-3 text-center">
                        <img class="img-thumbnail rounded-circle mb-3"
                            src="/staff-images/{{ $serviceStaff->staff->image ?? '' }}" alt="Profile Image"
                            style="width: 200px; height: 200px; object-fit: cover;">
                        <div class="status-badge mb-3">
                            <span class="badge badge-{{ $serviceStaff->staff->status == 1 ? 'success' : 'secondary' }}">
                                {{ $serviceStaff->staff->status == 1 ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                        <div class="status-badge mb-3">
                            <span class="badge badge-{{ $serviceStaff->staff->online == 1 ? 'success' : 'secondary' }}">
                                {{ $serviceStaff->staff->status == 1 ? 'Online' : 'Offline' }}
                            </span>
                        </div>
                    </div>

                    <!-- Personal Info -->
                    <div class="col-md-9">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-item mb-3">
                                    <h6 class="text-muted mb-1">Full Name</h6>
                                    <p class="mb-0">{{ $serviceStaff->name }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item mb-3">
                                    <h6 class="text-muted mb-1">Email</h6>
                                    <p class="mb-0">{{ $serviceStaff->email }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item mb-3">
                                    <h6 class="text-muted mb-1">Phone</h6>
                                    <p class="mb-0">{{ $serviceStaff->staff->phone ?? 'N/A' }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item mb-3">
                                    <h6 class="text-muted mb-1">WhatsApp</h6>
                                    <p class="mb-0">{{ $serviceStaff->staff->whatsapp ?? 'N/A' }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item mb-3">
                                    <h6 class="text-muted mb-1">Commission</h6>
                                    <p class="mb-0">{{ $serviceStaff->staff->commission ?? 0 }}%</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item mb-3">
                                    <h6 class="text-muted mb-1">Additional Charges</h6>
                                    <p class="mb-0">@currency($serviceStaff->staff->charges, true)</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item mb-3">
                                    <h6 class="text-muted mb-1">Minimum Order Value</h6>
                                    <p class="mb-0">@currency($serviceStaff->staff->min_order_value, true)</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item mb-3">
                                    <h6 class="text-muted mb-1">{{ now()->format('F') }} Bonus</h6>
                                    <p class="mb-0">@currency($bonus, true)</p>
                                </div>
                            </div>
                            @if ($serviceStaff->staff->about)
                                <div class="col-md-12">
                                    <div class="info-item mb-3">
                                        <h6 class="text-muted mb-1">About</h6>
                                        <div class="border rounded p-2 bg-light">
                                            {!! $serviceStaff->staff->about !!}
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Information Sections -->
        <div class="row">
            <!-- Documents Section -->
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-file-alt"></i> Documents
                        </h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            @foreach ($documents as $field => $label)
                                <li class="list-group-item">
                                    <strong>{{ $label }}:</strong>
                                    @if ($serviceStaff->document && $serviceStaff->document->$field)
                                        <a href="{{ asset('staff-document/' . $serviceStaff->document->$field) }}"
                                            target="_blank" class="btn btn-sm btn-outline-primary float-right">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    @else
                                        <span class="text-muted float-right">Not uploaded</span>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Roles Section -->
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0">
                            <i class="fas fa-user-shield"></i> Roles & Permissions
                        </h5>
                    </div>
                    <div class="card-body">
                        @if (!empty($serviceStaff->getRoleNames()))
                            @foreach ($serviceStaff->getRoleNames() as $v)
                                <span class="badge badge-pill badge-dark mr-1">{{ $v }}</span>
                            @endforeach
                        @else
                            <p class="text-muted">No roles assigned</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Quote Information -->
        <div class="card mb-4">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-comment-dollar"></i> Quote Settings
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-item mb-3">
                            <h6 class="text-muted mb-1">Get Quote</h6>
                            <p class="mb-0">
                                <span class="badge badge-{{ $serviceStaff->staff->get_quote ? 'success' : 'secondary' }}">
                                    {{ $serviceStaff->staff->get_quote ? 'Yes' : 'No' }}
                                </span>
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item mb-3">
                            <h6 class="text-muted mb-1">Show Quote Detail</h6>
                            <p class="mb-0">
                                <span
                                    class="badge badge-{{ $serviceStaff->staff->show_quote_detail ? 'success' : 'secondary' }}">
                                    {{ $serviceStaff->staff->show_quote_detail ? 'Yes' : 'No' }}
                                </span>
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item mb-3">
                            <h6 class="text-muted mb-1">Quote Amount</h6>
                            <p class="mb-0">@currency($serviceStaff->staff->quote_amount, true)</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item mb-3">
                            <h6 class="text-muted mb-1">Quote Commission</h6>
                            <p class="mb-0">{{ $serviceStaff->staff->quote_commission ?? 0 }}%</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Address Information -->
        <div class="card mb-4">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">
                    <i class="fas fa-map-marker-alt"></i> Address Information
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-item mb-3">
                            <h6 class="text-muted mb-1">Location</h6>
                            <p class="mb-0">{{ $serviceStaff->staff->location ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item mb-3">
                            <h6 class="text-muted mb-1">Nationality</h6>
                            <p class="mb-0">{{ $serviceStaff->staff->nationality ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Relationships Sections -->
        <div class="row mb-4">
            <!-- Sub Titles / Designations -->
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header bg-orange text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-tags"></i> Sub Titles / Designations
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <!-- Add search input at the top -->
                        <div class="p-3 border-bottom">
                            <input type="text" id="subtitleSearch" class="form-control"
                                placeholder="Search Sub Titles / Designations ...">
                        </div>

                        @if (count($serviceStaff->subTitles) > 0)
                            <div class="subtitles-container" style="max-height: 300px; overflow-y: auto;">
                                <div class="row no-gutters" id="subtitlesList">
                                    @foreach ($serviceStaff->subTitles as $subTitle)
                                        <div class="subtitle-item p-3 border-bottom">
                                            <i class="fas fa-check-circle text-success mr-2"></i>
                                            <span class="subtitle-name">{{ $subTitle->name }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <div class="p-3">
                                <p class="alert alert-info mb-0">No Sub Titles / Designations assigned</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <!-- Supervisors -->
            <div class="col-md-4">
                <div class="card mb-4 h-100">
                    <div class="card-header bg-teal text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-user-friends"></i> Supervisors
                        </h5>
                    </div>
                    <div class="card-body">
                        @if (count($serviceStaff->supervisors) > 0)
                            <ul class="list-group list-group-flush">
                                @foreach ($serviceStaff->supervisors as $supervisor)
                                    <li class="list-group-item">{{ $supervisor->name }}</li>
                                @endforeach
                            </ul>
                        @else
                            <p class="alert alert-info mb-0">No supervisors assigned</p>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header bg-orange text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-tags"></i> Categories
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <!-- Add search input at the top -->
                        <div class="p-3 border-bottom">
                            <input type="text" id="categorySearch" class="form-control"
                                placeholder="Search categories...">
                        </div>

                        @if (count($serviceStaff->categories) > 0)
                            <div class="categories-container" style="max-height: 300px; overflow-y: auto;">
                                <div class="row no-gutters" id="categoriesList">
                                    @foreach ($serviceStaff->categories as $category)
                                        <div class="category-item p-3 border-bottom">
                                            <i class="fas fa-check-circle text-success mr-2"></i>
                                            <span class="category-name">{{ $category->title }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <div class="p-3">
                                <p class="alert alert-info mb-0">No categories assigned</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Services Section -->
        <div class="card mb-4">
            <div class="card-header bg-indigo text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-concierge-bell"></i> Services
                </h5>
                <div class="search-box" style="width: 250px;">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                        </div>
                        <input type="text" id="serviceSearch" class="form-control" placeholder="Search services...">
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                @if (count($serviceStaff->services) > 0)
                    <div class="services-container" style="max-height: 300px; overflow-y: auto;">
                        <div class="row no-gutters" id="servicesList">
                            @foreach ($serviceStaff->services as $service)
                                <div class="col-md-6 service-entry">
                                    <div class="service-item p-3 border-bottom">
                                        <i class="fas fa-check-circle text-success mr-2"></i>
                                        <span class="service-name">{{ $service->name }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="p-3">
                        <p class="alert alert-info mb-0">No services assigned</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Time Slots Section -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-clock"></i> Assigned Time Slots
                </h5>
            </div>
            <div class="card-body">
                @if ($serviceStaff->staffTimeSlots->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>Name</th>
                                    <th>Start Time</th>
                                    <th>End Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($serviceStaff->staffTimeSlots as $timeSlot)
                                    <tr>
                                        <td>{{ $timeSlot->name }}</td>
                                        <td>{{ \Carbon\Carbon::parse($timeSlot->time_start)->format('h:i A') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($timeSlot->time_end)->format('h:i A') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info mb-0">No time slots assigned</div>
                @endif
            </div>
        </div>

        <!-- Zones Section -->
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">
                    <i class="fas fa-map-marked-alt"></i> Assigned Zones
                </h5>
            </div>
            <div class="card-body">
                @if ($serviceStaff->staffZones->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>Name</th>
                                    <th>Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($serviceStaff->staffZones as $zone)
                                    <tr>
                                        <td>{{ $zone->name }}</td>
                                        <td>{{ $zone->description ?? 'N/A' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info mb-0">No zones assigned</div>
                @endif
            </div>
        </div>

        <!-- Driver Assignments -->
        <div class="card mb-4">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">
                    <i class="fas fa-calendar-week"></i> Weekly Driver Assignments
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="thead-light">
                            <tr>
                                <th>Day</th>
                                <th>Driver Name</th>
                                <th>Time Slot</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach (['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day)
                                @php
                                    $dayColors = [
                                        'Monday' => 'bg-light-pink',
                                        'Tuesday' => 'bg-light-green',
                                        'Wednesday' => 'bg-light-blue',
                                        'Thursday' => 'bg-light-yellow',
                                        'Friday' => 'bg-light-purple',
                                        'Saturday' => 'bg-light-gray',
                                        'Sunday' => 'bg-light-red',
                                    ];
                                    $dayClass = $dayColors[$day] ?? '';
                                @endphp
                                @php
                                    $driversForDay = $assignedDrivers[$day] ?? [];
                                @endphp
                                @if (count($driversForDay) > 0)
                                    @foreach ($driversForDay as $index => $driverData)
                                        @php
                                            $timeSlot = $timeSlots->firstWhere('id', $driverData['time_slot_id']);
                                        @endphp
                                        <tr class="{{ $dayClass }}">
                                            @if ($index === 0)
                                                <td rowspan="{{ count($driversForDay) }}"
                                                    class="align-middle text-center font-weight-bold">
                                                    {{ $day }}
                                                </td>
                                            @endif
                                            <td>{{ $driverData->driver->name ?? 'N/A' }}</td>
                                            <td>
                                                {{ $timeSlot ? \Carbon\Carbon::parse($timeSlot->time_start)->format('h:i A') . ' - ' . \Carbon\Carbon::parse($timeSlot->time_end)->format('h:i A') : 'No Time Slot Assigned' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr class="{{ $dayClass }}">
                                        <td class="text-center font-weight-bold">{{ $day }}</td>
                                        <td colspan="2" class="text-center text-muted">No Drivers Assigned</td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Transactions Section -->
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-exchange-alt"></i> Transactions
                    </h5>
                    <div>
                        <span class="badge badge-light">Current Balance: @currency($total_balance, true)</span>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if (count($transactions) != 0)
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Sr#</th>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Description</th>
                                    <th>Amount</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($transactions as $transaction)
                                    <tr>
                                        <td>{{ ++$i }}</td>
                                        <td>{{ $transaction->created_at->format('M d, Y h:i A') }}</td>
                                        <td>
                                            <span
                                                class="badge badge-{{ $transaction->type == 'Credit' ? 'success' : 'danger' }}">
                                                {{ $transaction->type }}
                                            </span>
                                        </td>
                                        <td>
                                            @if ($transaction->order_id)
                                                Order ID: #{{ $transaction->order_id }}
                                            @else
                                                {{ substr($transaction->description, 0, 70) }}
                                            @endif
                                        </td>
                                        <td
                                            class="font-weight-bold {{ $transaction->type == 'Credit' ? 'text-success' : 'text-danger' }}">
                                            @currency($transaction->amount, true)
                                        </td>
                                        <td>
                                            <form action="{{ route('transactions.destroy', $transaction->id) }}"
                                                method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                @can('order-delete')
                                                    <button type="submit" class="btn btn-sm btn-danger"
                                                        onclick="return confirm('Are you sure?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                @endcan
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center">
                        {!! $transactions->links() !!}
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-exchange-alt fa-3x text-muted mb-3"></i>
                        <h4>No Transactions Found</h4>
                        <p class="text-muted">There are no transactions recorded for this staff member.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Add Transaction Form -->
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">
                    <i class="fas fa-plus-circle"></i> Add New Transaction
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <div class="alert alert-light">
                            <div class="d-flex justify-content-between">
                                <span>Current balance: <strong>@currency($total_balance, true)</strong></span>
                                <span>With salary: <strong>@currency($total_balance + $serviceStaff->staff->fix_salary, true)</strong></span>
                            </div>
                        </div>
                    </div>
                </div>

                <form action="{{ route('transactions.store') }}" method="POST" id="pay-transactions">
                    @csrf
                    <input type="hidden" name="user_id" value="{{ $serviceStaff->id }}">
                    <input type="hidden" name="pay" value="1">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="amount" class="font-weight-bold">Amount <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">{{ config('app.currency') }}</span>
                                    </div>
                                    <input type="number" name="amount" id="amount" class="form-control"
                                        value="{{ old('amount') }}" placeholder="0.00" step="0.01" min="0">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="type" class="font-weight-bold">Type <span
                                        class="text-danger">*</span></label>
                                <select name="type" id="type" class="form-control">
                                    <option value="Credit" {{ old('type') == 'Credit' ? 'selected' : '' }}>Credit</option>
                                    <option value="Debit" {{ old('type') == 'Debit' ? 'selected' : '' }}>Debit</option>
                                    <option value="Product Sale" {{ old('type') == 'Product Sale' ? 'selected' : '' }}>
                                        Product Sale</option>
                                    <option value="Bonus" {{ old('type') == 'Bonus' ? 'selected' : '' }}>Bonus</option>
                                    <option value="Pay Salary" {{ old('type') == 'Pay Salary' ? 'selected' : '' }}>Pay
                                        Salary</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="description" class="font-weight-bold">Description <span
                                        class="text-danger">*</span></label>
                                <textarea name="description" id="description" cols="10" rows="3" class="form-control"
                                    placeholder="Enter transaction details">{{ old('description') }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <button type="submit" value="transaction" name="submit_type" class="btn btn-success btn-lg"
                                form="pay-transactions">
                                <i class="fas fa-save"></i> Add Transaction
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        function confirmDelete(Id) {
            var result = confirm("Are you sure you want to delete this Item?");
            if (result) {
                document.getElementById('deleteForm' + Id).submit();
            }
        }
    </script>
    <script>
        $(document).ready(function() {
            $('#serviceSearch').on('keyup', function() {
                const searchText = $(this).val().toLowerCase();

                $('.service-entry').each(function() {
                    const serviceName = $(this).find('.service-name').text().toLowerCase();
                    if (serviceName.includes(searchText)) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });

                // Show message if no results found
                if ($('.service-entry:visible').length === 0) {
                    $('#servicesList').append(
                        '<div class="col-12 text-center py-3 text-muted">No matching services found</div>'
                    );
                } else {
                    $('#servicesList .text-muted').remove(); // Remove "no results" message if it exists
                }
            });
        });
        $('#categorySearch').on('keyup', function() {
            const searchTerm = $(this).val().toLowerCase();

            $('.category-item').each(function() {
                const categoryText = $(this).find('.category-name').text().toLowerCase();

                if (categoryText.includes(searchTerm)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });

            // Show "No results" message if all are hidden
            if ($('.category-item:visible').length === 0) {
                if ($('#noResults').length === 0) {
                    $('#categoriesList').append(
                        '<div class="p-3 text-muted" id="noResults">No matching categories found</div>'
                    );
                }
            } else {
                $('#noResults').remove();
            }
        });

        $('#subtitleSearch').on('keyup', function() {
            const searchTerm = $(this).val().toLowerCase();

            $('.subtitle-item').each(function() {
                const subtitleText = $(this).find('.subtitle-name').text().toLowerCase();

                if (subtitleText.includes(searchTerm)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });

            // Show "No results" message if all are hidden
            if ($('.subtitle-item:visible').length === 0) {
                if ($('#noResults').length === 0) {
                    $('#subtitlesList').append(
                        '<div class="p-3 text-muted" id="noResults">No matching subtitles found</div>'
                    );
                }
            } else {
                $('#noResults').remove();
            }
        });
    </script>
@endsection
