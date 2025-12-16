@extends('layouts.app')

@push('styles')
<style>
    /* Custom styles to mimic the original Tailwind design's look and feel */
    body { background-color: #f8f9fa; }
    
    /* Custom border radius and shadow for a softer look */
    .card, .form-control, .custom-select, .btn { border-radius: 0.75rem !important; }
    
    /* Custom Badge styles to mimic the original rounded, background-colored chips */
    .badge-chip { 
        padding: 0.5rem 1rem !important; 
        font-size: 0.8rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }

    /* Custom colors for chips to match the original scheme */
    .badge-all { background-color: #e2e6ea; color: #383d41; }
    .badge-pending { background-color: #fff3cd; color: #856404; } /* Yellow */
    .badge-confirmed { background-color: #d4edda; color: #155724; } /* Green */
    .badge-rejected { background-color: #f8d7da; color: #721c24; } /* Red */
    .badge-canceled { background-color: #f5c2c7; color: #851b4d; } /* Pink */
    .badge-inprogress { background-color: #cce5ff; color: #004085; } /* Blue */
    .badge-complete { background-color: #d1ecf1; color: #0c5460; } /* Teal */

    /* Custom scrollbar mimic for status chips */
    .scrollbar-thin { overflow-x: auto; white-space: nowrap; padding-bottom: 0.5rem; }
    .scrollbar-thin::-webkit-scrollbar { height: 6px; }
    .scrollbar-thin::-webkit-scrollbar-thumb { background-color: #ced4da; border-radius: 10px; }
    .scrollbar-thin::-webkit-scrollbar-track { background-color: #f8f9fa; }
</style>
@endpush
@section('page_title')
<h3 class="">Orders</h3>
@endsection
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div id="orders-dashboard-wrapper" class="px-2 py-sm-4 px-sm-4 py-md-0 px-md-0" style="min-width: 500px;">
                <main class="" id="main-content">

                    <!-- Title and Action Buttons -->
                    <section class="mb-4">
                        <div class="d-flex flex-column flex-sm-row justify-content-center align-items-start align-items-sm-center float-end">
                            <div class="d-flex flex-wrap align-items-center">
                                @if (!auth()->user()->hasRole('Supervisor'))
                                    @can('order-download')
                                        <a href="{{ Request::fullUrlWithQuery(['csv' => 1]) }}" class="btn btn-sm btn-light border mr-2 mb-2 mb-sm-0 text-muted">
                                            <i class="fas fa-file-excel mr-2 text-success"></i>
                                            Excel
                                        </a>
                                        <a class="btn btn-sm btn-light border mr-2 mb-2 mb-sm-0 text-muted" href="{{ Request::fullUrlWithQuery(['print' => 1]) }}">
                                            <i class="fas fa-file-pdf mr-2 text-danger"></i>
                                            PDF
                                        </a>
                                    @endcan
                                @endif
                                @can('order-create')
                                    <a class="btn btn-sm btn-primary shadow-sm text-white font-weight-bold" href="{{ route('orders.create') }}">
                                        <i class="fas fa-plus mr-2"></i>
                                        Create Order
                                    </a>
                                @endcan
                            </div>
                        </div>
                    </section>
                    <div class="py-1"></div>
                    <!-- Today's Summary Cards -->
                    <section class="mb-4 no-underline">
                        <style>
                            .no-underline a { text-decoration: none !important; color: inherit !important; }
                            .no-underline a:hover { text-decoration: none !important; color: inherit !important; }
                        </style>

                        <h2 class="h5 font-weight-bold text-secondary mb-3">Today's Performance</h2>
                        <div class="row">
                            
                            <!-- Metric Card 1: Today's Drop Orders (Green Border - Success) -->
                            <div class="col-md-3 p-2">
                                <a href="{{ route('orders.index') }}?appointment_date={{ date('Y-m-d') }}&driver_dropped=true">
                                    <div class="card shadow-sm border-0 border-bottom border-success" style="border-width: 4px !important;">
                                        <div class="card-body p-4">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <p class="mb-0 text-muted small">Today's Drop Order</p>
                                                <i class="fas fa-box-open fa-lg text-secondary"></i>
                                            </div>
                                            <div class="mt-1">
                                                <span class="h3 font-weight-bold text-dark">{{ $todaysDropOrders ?? 0 }}</span>
                                                <span class="small ml-2 text-success font-weight-medium">{{ $todaysDropOrdersPercentage ?? '+0%' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>

                            <!-- Metric Card 2: Today's Canceled Order (Red Border - Danger) -->
                            <div class="col-md-3 p-2">
                                <a href="{{ route('orders.index') }}?appointment_date={{ date('Y-m-d') }}&status=Canceled">
                                    <div class="card shadow-sm border-0 border-bottom border-danger" style="border-width: 4px !important;">
                                        <div class="card-body p-4">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <p class="mb-0 text-muted small">Today's Canceled Order</p>
                                                <i class="fas fa-times-circle fa-lg text-secondary"></i>
                                            </div>
                                            <div class="mt-1">
                                                <span class="h3 font-weight-bold text-dark">{{ $todaysCanceledOrders ?? 0 }}</span>
                                                <span class="small ml-2 text-danger font-weight-medium">{{ $todaysCanceledOrdersPercentage ?? '-0%' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </a>    
                            </div>

                            <!-- Metric Card 3: Today's Complete Order (Green Border - Success) -->
                            <div class="col-md-3 p-2">
                                <a href="{{ route('orders.index') }}?appointment_date={{ date('Y-m-d') }}&status=Complete">
                                    <div class="card shadow-sm border-0 border-bottom border-success" style="border-width: 4px !important;">
                                        <div class="card-body p-4">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <p class="mb-0 text-muted small">Today's Complete Order</p>
                                                <i class="fas fa-check-circle fa-lg text-secondary"></i>
                                            </div>
                                            <div class="mt-1">
                                                <span class="h3 font-weight-bold text-dark">{{ $todaysCompleteOrders ?? 0 }}</span>
                                                <span class="small ml-2 text-success font-weight-medium">{{ $todaysCompleteOrdersPercentage ?? '+0%' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>

                            <!-- Metric Card 4: Today's Pending Orders (Red Border - Danger) -->
                            <div class="col-md-3 p-2">
                                <a href="{{ route('orders.index') }}?appointment_date={{ date('Y-m-d') }}&status=Pending">
                                    <div class="card shadow-sm border-0 border-bottom border-danger" style="border-width: 4px !important;">
                                        <div class="card-body p-4">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <p class="mb-0 text-muted small">Today's Pending Order</p>
                                                <i class="fas fa-clock fa-lg text-secondary"></i>
                                            </div>
                                            <div class="mt-1">
                                                <span class="h3 font-weight-bold text-dark">{{ $todaysPendingOrders ?? 0 }}</span>
                                                <span class="small ml-2 text-danger font-weight-medium">{{ $todaysPendingOrdersPercentage ?? '-0%' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </a>    
                            </div>
                        </div>
                    </section>

                    <!-- Filters and Actions Section -->
                    <section class="card shadow-sm mb-5 p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h2 class="h5 font-weight-bold text-dark mb-0">Orders Status</h2>
                            <button id="filter-toggle-btn" class="btn btn-sm btn-light text-muted font-weight-medium" type="button" data-toggle="collapse" data-target="#advanced-filter-panel" aria-expanded="false" aria-controls="advanced-filter-panel">
                                <i class="fas fa-filter mr-2"></i>
                                Show Advanced Filters
                            </button>
                        </div>

                        <!-- Status Chip Filters (Horizontal Scrollable) -->
                        <div id="status-chips-container" class="scrollbar-thin mb-4 ">
                            @if (auth()->user()->hasRole('Admin'))
                                <a href="/orders" class="badge text-info badge-pill badge-chip badge-all mr-2 shadow-sm"><i class="fas fa-list-ul mr-2"></i> All</a>
                                <a href="/orders?status=Canceled" class="badge text-info badge-pill badge-chip badge-canceled mr-2 shadow-sm"><i class="fas fa-times-circle mr-2"></i> Canceled</a>
                            @endif
                            @if (!auth()->user()->hasRole('Staff'))
                                <a href="/orders?status=Pending" class="badge text-info badge-pill badge-chip badge-pending mr-2 shadow-sm"><i class="fas fa-clock mr-2"></i> Pending</a>
                                <a href="/orders?status=Rejected" class="badge text-info badge-pill badge-chip badge-rejected mr-2 shadow-sm"><i class="fas fa-times-circle mr-2"></i> Rejected</a>
                                <a href="/orders?status=Inprogress" class="badge text-info badge-pill badge-chip badge-inprogress mr-2 shadow-sm"><i class="fas fa-sync-alt mr-2"></i> Inprogress</a>
                                <a href="/orders?status=Complete" class="badge text-info badge-pill badge-chip badge-complete mr-2 shadow-sm"><i class="fas fa-check-circle mr-2"></i> Complete</a>
                                <a href="/orders?status=Accepted" class="badge text-info badge-pill badge-chip badge-confirmed mr-2 shadow-sm"><i class="fas fa-check-circle mr-2"></i> Accepted</a>
                                <a href="/orders?status=Confirm" class="badge text-info badge-pill badge-chip badge-confirmed mr-2 shadow-sm"><i class="fas fa-check-circle mr-2"></i> Confirm</a>
                            @endif
                        </div>

                        <!-- Collapsible Advanced Filter Form -->
                        <div class="collapse" id="advanced-filter-panel">
                            <div class="pt-4 border-top">
                                <form action="{{ route('orders.index') }}" method="GET" enctype="multipart/form-data">
                                    <div class="row">
                                        <div class="col-12 col-sm-6 col-lg-4 mb-3">
                                            <label class="small text-muted font-weight-medium mb-1">Order ID</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text bg-white border-right-0" style="border-radius: 0.75rem 0 0 0.75rem;"><i class="fas fa-box-open text-muted"></i></span>
                                                </div>
                                                <input type="number" name="order_id" class="form-control" value="{{ $filter['order_id'] }}" style="border-left: 0;">
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-lg-4 mb-3">
                                            <label class="small text-muted font-weight-medium mb-1">Appointment Date</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text bg-white border-right-0" style="border-radius: 0.75rem 0 0 0.75rem;"><i class="fas fa-calendar-alt text-muted"></i></span>
                                                </div>
                                                <input type="date" name="appointment_date" class="form-control" value="{{ $filter['appointment_date'] }}" style="border-left: 0;">
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-lg-4 mb-3">
                                            <label class="small text-muted font-weight-medium mb-1">Category</label>
                                            <div class="input-group position-relative">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text bg-white border-right-0" style="border-radius: 0.75rem 0 0 0.75rem;"><i class="fas fa-cog text-muted"></i></span>
                                                </div>
                                                <input type="text" id="category-autocomplete" name="category_title" class="form-control" autocomplete="off" value="{{ old('category_title', $filter['category_title']) }}" style="border-left: 0;">
                                                <input type="hidden" id="category_id" name="category_id" value="{{ $filter['category_id'] }}">
                                                <ul id="category-suggestions" class="list-group position-absolute w-100" style="z-index: 1000; display: none; max-height: 200px; overflow-y: auto; top: 100%;"></ul>
                                            </div>
                                        </div>
                                        @if (!auth()->user()->hasRole('Staff'))
                                        <div class="col-12 col-sm-6 col-lg-4 mb-3">
                                            <label class="small text-muted font-weight-medium mb-1">Staff</label>
                                            <div class="input-group position-relative">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text bg-white border-right-0" style="border-radius: 0.75rem 0 0 0.75rem;"><i class="fas fa-users text-muted"></i></span>
                                                </div>
                                                <input type="text" id="staff-autocomplete" name="staff_name" class="form-control" autocomplete="off" value="{{ old('staff_name', $filter['staff_name']) }}" style="border-left: 0;">
                                                <input type="hidden" id="staff_id" name="staff_id" value="{{ $filter['staff'] }}">
                                                <ul id="staff-suggestions" class="list-group position-absolute w-100" style="z-index: 1000; display: none; max-height: 200px; overflow-y: auto; top: 100%;"></ul>
                                            </div>
                                        </div>
                                        @endif
                                        @if (auth()->user()->hasRole('Admin'))
                                        <div class="col-12 col-sm-6 col-lg-4 mb-3">
                                            <label class="small text-muted font-weight-medium mb-1">Affiliate</label>
                                            <div class="input-group position-relative">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text bg-white border-right-0" style="border-radius: 0.75rem 0 0 0.75rem;"><i class="fas fa-users text-muted"></i></span>
                                                </div>
                                                <input type="text" id="affiliate-autocomplete" name="affiliate_name" class="form-control" autocomplete="off" value="{{ old('affiliate_name', $filter['affiliate_name']) }}" style="border-left: 0;">
                                                <input type="hidden" id="affiliate_id" name="affiliate_id" value="{{ $filter['affiliate'] }}">
                                                <ul id="affiliate-suggestions" class="list-group position-absolute w-100" style="z-index: 1000; display: none; max-height: 200px; overflow-y: auto; top: 100%;"></ul>
                                            </div>
                                        </div>
                                        @endif
                                        <div class="col-12 col-sm-6 col-lg-4 mb-3">
                                            <label class="small text-muted font-weight-medium mb-1">Customer</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text bg-white border-right-0" style="border-radius: 0.75rem 0 0 0.75rem;"><i class="fas fa-user text-muted"></i></span>
                                                </div>
                                                <input type="text" name="customer" class="form-control" value="{{ $filter['customer'] }}" placeholder="Enter Name or Email" style="border-left: 0;">
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-lg-4 mb-3">
                                            <label class="small text-muted font-weight-medium mb-1">Driver</label>
                                            <div class="input-group position-relative">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text bg-white border-right-0" style="border-radius: 0.75rem 0 0 0.75rem;"><i class="fas fa-truck text-muted"></i></span>
                                                </div>
                                                <input type="text" id="driver-autocomplete" name="driver_name" class="form-control" autocomplete="off" value="{{ old('driver_name', $filter['driver_name']) }}" style="border-left: 0;">
                                                <input type="hidden" id="driver_id" name="driver_id" value="{{ $filter['driver'] }}">
                                                <ul id="driver-suggestions" class="list-group position-absolute w-100" style="z-index: 1000; display: none; max-height: 200px; overflow-y: auto; top: 100%;"></ul>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-lg-4 mb-3">
                                            <label class="small text-muted font-weight-medium mb-1">Status</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text bg-white border-right-0" style="border-radius: 0.75rem 0 0 0.75rem;"><i class="fas fa-clock text-muted"></i></span>
                                                </div>
                                                <select name="status" class="custom-select" style="border-left: 0;">
                                                    <option value="">Select</option>
                                                    @foreach ($statuses as $status)
                                                        <option value="{{ $status }}" @if ($status == $filter['status']) selected @endif>{{ $status }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-lg-4 mb-3">
                                            <label class="small text-muted font-weight-medium mb-1">Driver Status</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text bg-white border-right-0" style="border-radius: 0.75rem 0 0 0.75rem;"><i class="fas fa-truck text-muted"></i></span>
                                                </div>
                                                <select name="driver_status" class="custom-select" style="border-left: 0;">
                                                    <option value="">Select</option>
                                                    @foreach ($driver_statuses as $status)
                                                        <option value="{{ $status }}" @if ($status == $filter['driver_status']) selected @endif>{{ $status }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-lg-4 mb-3">
                                            <label class="small text-muted font-weight-medium mb-1">Zone</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text bg-white border-right-0" style="border-radius: 0.75rem 0 0 0.75rem;"><i class="fas fa-map-marker-alt text-muted"></i></span>
                                                </div>
                                                <select name="zone" class="custom-select" style="border-left: 0;">
                                                    <option value="">Select</option>
                                                    @foreach ($zones as $zone)
                                                        <option value="{{ $zone }}"@if ($zone == $filter['zone']) selected @endif>{{ $zone }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-lg-4 mb-3">
                                            <label class="small text-muted font-weight-medium mb-1">Date From</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text bg-white border-right-0" style="border-radius: 0.75rem 0 0 0.75rem;"><i class="fas fa-calendar-alt text-muted"></i></span>
                                                </div>
                                                <input type="date" name="date_from" class="form-control" value="{{ $filter['date_from'] }}" style="border-left: 0;">
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-lg-4 mb-3">
                                            <label class="small text-muted font-weight-medium mb-1">Date To</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text bg-white border-right-0" style="border-radius: 0.75rem 0 0 0.75rem;"><i class="fas fa-calendar-alt text-muted"></i></span>
                                                </div>
                                                <input type="date" name="date_to" class="form-control" value="{{ $filter['date_to'] }}" style="border-left: 0;">
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-lg-4 mb-3">
                                            <label class="small text-muted font-weight-medium mb-1">Time Start</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text bg-white border-right-0" style="border-radius: 0.75rem 0 0 0.75rem;"><i class="fas fa-clock text-muted"></i></span>
                                                </div>
                                                <input type="time" name="time_start" class="form-control" value="{{ $filter['time_start'] }}" style="border-left: 0;">
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-lg-4 mb-3">
                                            <label class="small text-muted font-weight-medium mb-1">Time End</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text bg-white border-right-0" style="border-radius: 0.75rem 0 0 0.75rem;"><i class="fas fa-clock text-muted"></i></span>
                                                </div>
                                                <input type="time" name="time_end" class="form-control" value="{{ $filter['time_end'] }}" style="border-left: 0;">
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-lg-4 mb-3">
                                            <label class="small text-muted font-weight-medium mb-1">Payment Method</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text bg-white border-right-0" style="border-radius: 0.75rem 0 0 0.75rem;"><i class="fas fa-dollar-sign text-muted"></i></span>
                                                </div>
                                                <select name="payment_method" class="custom-select" style="border-left: 0;">
                                                    <option value="">Select</option>
                                                    @foreach ($payment_methods as $payment_method)
                                                        <option value="{{ $payment_method }}" @if ($payment_method == $filter['payment_method']) selected @endif>{{ $payment_method }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-4 d-flex justify-content-end">
                                        <a href="{{ url()->current() }}" class="btn btn-sm btn-light border mr-2 font-weight-medium">Reset</a>
                                        <button type="submit" class="btn btn-sm btn-primary shadow-sm font-weight-bold">Apply Filter</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </section>

                    @if ($hasFilters = request()->except('page'))
                    <div class="selected-filters mb-4">
                        <h5 class="mb-3">Active Filters:</h5>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach (request()->except('page') as $key => $value)
                                @if (!empty($value) && $key != '_token')
                                    @php
                                        // Only show name fields, skip id fields
                                        if (in_array($key, ['staff_id', 'affiliate_id', 'driver_id', 'category_id'])) {
                                            continue;
                                        }
                                        $cleanKey = ucwords(str_replace(['_id', '_'], ['', ' '], $key));
                                        $displayValue = $value;
                                        if (in_array($key, ['date_from', 'date_to', 'appointment_date'])) {
                                            $displayValue = \Carbon\Carbon::parse($value)->format('M d, Y');
                                        } elseif (in_array($key, ['time_start', 'time_end'])) {
                                            $displayValue = \Carbon\Carbon::parse($value)->format('h:i A');
                                        }
                                    @endphp

                                    <div class="filter-tag text-black rounded-pill px-3 py-1 d-flex align-items-center"
                                        style="background-color: #c5dcff;">
                                        <span class="me-1">{{ $cleanKey }}:</span>
                                        <strong class="me-2">{{ $displayValue }}</strong>
                                        <a href="{{ route('orders.index', array_merge(request()->except('page', $key), ['page' => 1])) }}"
                                            class="text-black" aria-label="Remove filter">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17"
                                                fill="currentColor" viewBox="0 0 16 16">
                                                <path
                                                    d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z" />
                                            </svg>
                                        </a>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @can('order-edit')
                    <!-- Bulk Actions Section -->
                    <section class="mb-5">
                        <h2 class="h5 font-weight-bold text-secondary mb-3">Bulk Actions</h2>
                        <div class="row">
                            <!-- Bulk Action Card 1 -->
                            <div class="col-12 col-md-4 mb-4">
                                <div class="card shadow-sm border border-light h-100 p-3">
                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                        <h3 class="small font-weight-bold text-dark mb-0 d-flex align-items-center">
                                            <i class="fas fa-calendar-alt mr-2 text-primary"></i>
                                            Bulk Booking Update
                                        </h3>
                                    </div>
                                    <button id="bulkBookingUpdateBtn" class="btn btn-sm btn-success font-weight-bold mt-3">
                                        Update Bookings
                                    </button>
                                </div>
                            </div>

                            <!-- Bulk Action Card 2 -->
                            <div class="col-12 col-md-4 mb-4">
                                <div class="card shadow-sm border border-light h-100 p-3">
                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                        <h3 class="small font-weight-bold text-dark mb-0 d-flex align-items-center">
                                            <i class="fas fa-box-open mr-2 text-primary"></i>
                                            Bulk Order Status Update
                                        </h3>
                                    </div>
                                    <div class="d-flex">
                                        <select name="bulk-status" class="custom-select form-control-sm mr-2 flex-grow-1">
                                            @foreach ($statuses as $status)
                                                <option value="{{ $status }}" @if ($status == $filter['status']) selected @endif>{{ $status }}</option>
                                            @endforeach
                                        </select>
                                        <button id="bulkStatusBtn" class="btn btn-sm btn-primary p-2">
                                            <i class="fas fa-chevron-up"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Bulk Action Card 3 -->
                            <div class="col-12 col-md-4 mb-4">
                                <div class="card shadow-sm border border-light h-100 p-3">
                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                        <h3 class="small font-weight-bold text-dark mb-0 d-flex align-items-center">
                                            <i class="fas fa-truck mr-2 text-primary"></i>
                                            Bulk Driver Status Update
                                        </h3>
                                    </div>
                                    <div class="d-flex">
                                        <select name="bulk-driver-status" class="custom-select form-control-sm mr-2 flex-grow-1">
                                            @foreach ($driver_statuses as $status)
                                                <option value="{{ $status }}" @if ($status == $filter['status']) selected @endif>{{ $status }}</option>
                                            @endforeach
                                        </select>
                                        <button id="bulkDriverStatusBtn" class="btn btn-sm text-white p-2" style="background-color: #20c997;">
                                            <i class="fas fa-chevron-up"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                    @endcan

                    <!-- Orders Table -->
                    <section class="mb-5">
                        <h2 class="h4 font-weight-bold text-dark mb-3">Orders ({{ $total_order }})</h2>
                        <div class="card shadow-sm">
                            <div class="table-responsive">
                                @include('orders.list')
                            </div>
                        </div>
                        {!! $orders->links() !!}
                    </section>
                </main>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Your existing script + new scripts
    $(document).ready(function() {
        $('#filter-toggle-btn').on('click', function() {
            var target = $($(this).data('target'));
            target.collapse('toggle');
            var text = $(this).text().trim();
            if (text.includes('Show')) {
                $(this).html('<i class="fas fa-filter mr-2"></i> Hide Advanced Filters');
            } else {
                $(this).html('<i class="fas fa-filter mr-2"></i> Show Advanced Filters');
            }
        });

        $('.all-item-checkbox').click(function() {
            var allCheckboxState = $(this).prop('checked');
            $('.item-checkbox').prop('checked', allCheckboxState);
        });
        $('#bulkStatusBtn').click(function() {
            const selectedItems = getSelectedItems();
            const statusValue = $('select[name="bulk-status"]').val();
            const statusText = $('select[name="bulk-status"] option:selected').text();

            if (statusValue && selectedItems.length > 0) {
                if (confirm(`Are you sure you want to set ${statusText} to the selected items?`)) {
                    editSelectedItems(selectedItems, statusValue, 'order');
                }
            } else {
                alert('Please select at least one order and choose a status to update.');
            }
        });

        $('#bulkBookingUpdateBtn').click(function() {
            const selectedItems = getSelectedItems();

            if (selectedItems.length > 0) {
                $.ajax({
                    url: '/addToCartModal/' + selectedItems + "?bulk=true",
                    type: 'GET',
                    success: function(response) {
                        $('#addToCartPopup').html(response);
                        $('#addToCartModal').modal('show');
                    }
                });

            } else {
                alert('Please select at least one order to update order booking.');
            }
        });

        $('#bulkDriverStatusBtn').click(function() {
            const selectedItems = getSelectedItems();
            const statusValue = $('select[name="bulk-driver-status"]').val();
            const statusText = $('select[name="bulk-driver-status"] option:selected').text();

            if (statusValue && selectedItems.length > 0) {
                if (confirm(`Are you sure you want to set ${statusText} to the selected items?`)) {
                    editSelectedItems(selectedItems, statusValue, 'driver');
                }
            } else {
                alert('Please select at least one order and choose a driver status to update.');
            }
        });

        function getSelectedItems() {
            return $('.item-checkbox:checked')
                .map(function() {
                    return $(this).val();
                })
                .get();
        }

        function editSelectedItems(selectedItems, status, key) {
            $.ajax({
                url: '{{ route('orders.bulkStatusEdit') }}',
                method: 'POST',
                dataType: 'json',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: JSON.stringify({
                    selectedItems,
                    status,
                    key
                }),
                success: function(data) {
                    alert(data.message);
                    window.location.reload();
                },
                error: function(error) {
                    console.error('Error:', error);
                    alert('An error occurred while processing your request. Please try again.');
                }
            });
        }

        function debounce(func, wait) {
            let timeout;
            return function(...args) {
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(this, args), wait);
            };
        }

        function setupUserAutocomplete(inputSelector, suggestionsSelector, role) {
            var $input = $(inputSelector);
            var $suggestions = $(suggestionsSelector);
            $input.on('input', debounce(function() {
                var query = $(this).val();
                if (query.length < 2) {
                    $suggestions.hide();
                    return;
                }
                $.ajax({
                    url: '{{ route('autocomplete.name') }}',
                    data: {
                        q: query,
                        role: role
                    },
                    success: function(data) {
                        $suggestions.empty();
                        if (Array.isArray(data) && data.length) {
                            data.forEach(function(user) {
                                var $li = $(
                                    '<li class="list-group-item list-group-item-action"></li>'
                                    ).text(user.text);
                                $li.on('click', function() {
                                    $input.val(user.text);
                                    $('#' + $input.attr('id').replace(
                                        '-autocomplete', '_id')).val(user.id);
                                    $suggestions.hide();
                                });
                                $suggestions.append($li);
                            });
                            $suggestions.show();
                        } else {
                            var $li = $('<li class="list-group-item text-muted"></li>').text(
                                'No data found');
                            $suggestions.append($li);
                            $suggestions.show();
                        }
                    },
                    error: function() {
                        $suggestions.empty();
                        var $li = $('<li class="list-group-item text-danger"></li>').text(
                            'Error fetching data');
                        $suggestions.append($li);
                        $suggestions.show();
                    }
                });
            }, 300));
            $input.on('blur', function() {
                setTimeout(function() {
                    $suggestions.hide();
                }, 200);
            });
        }

        function categoryAutocomplete(inputSelector, suggestionsSelector) {
            var $input = $(inputSelector);
            var $suggestions = $(suggestionsSelector);
            $input.on('input', debounce(function() {
                var query = $(this).val();
                if (query.length < 2) {
                    $suggestions.hide();
                    return;
                }
                $.ajax({
                    url: '{{ route('autocomplete.category') }}',
                    data: {
                        q: query
                    },
                    success: function(data) {
                        $suggestions.empty();
                        if (Array.isArray(data) && data.length) {
                            data.forEach(function(category) {
                                var $li = $(
                                    '<li class="list-group-item list-group-item-action"></li>'
                                    ).text(category.text);
                                $li.on('click', function() {
                                    $input.val(category.text);
                                    $('#' + $input.attr('id').replace(
                                        '-autocomplete', '_id')).val(category.id);
                                    $suggestions.hide();
                                });
                                $suggestions.append($li);
                            });
                            $suggestions.show();
                        } else {
                            var $li = $('<li class="list-group-item text-muted"></li>').text(
                                'No data found');
                            $suggestions.append($li);
                            $suggestions.show();
                        }
                    },
                    error: function() {
                        $suggestions.empty();
                        var $li = $('<li class="list-group-item text-danger"></li>').text(
                            'Error fetching data');
                        $suggestions.append($li);
                        $suggestions.show();
                    }
                });
            }, 300));
            $input.on('blur', function() {
                setTimeout(function() {
                    $suggestions.hide();
                }, 200);
            });
        }

        setupUserAutocomplete('#staff-autocomplete', '#staff-suggestions', 'Staff');
        setupUserAutocomplete('#affiliate-autocomplete', '#affiliate-suggestions', 'Affiliate');
        setupUserAutocomplete('#driver-autocomplete', '#driver-suggestions', 'Driver');
        categoryAutocomplete('#category-autocomplete', '#category-suggestions');
    });
</script>
@endpush