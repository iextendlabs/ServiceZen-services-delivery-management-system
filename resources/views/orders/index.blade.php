@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12 margin-tb">
                <div class="row">
                    <div class="col-md-6 d-flex align-items-center">
                        <h2>Orders</h2>
                    </div>
                    @can('order-edit')

                        <div class="col-md-2">
                            <div class="mb-3">
                                <strong>Bulk Booking Update:</strong>
                                <div>
                                    <button id="bulkBookingUpdateBtn" class="btn btn-success" type="button">
                                        <i class="fa fa-book"></i> Update Bookings
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <strong>Bulk Order Status Update:</strong>
                                <div class="input-group">
                                    <select name="bulk-status" class="form-control">
                                        @foreach ($statuses as $status)
                                            <option value="{{ $status }}" 
                                                @if ($status == $filter['status']) selected @endif>
                                                {{ $status }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="input-group-append">
                                        <button id="bulkStatusBtn" class="btn btn-primary" type="button">
                                            <i class="fa fa-save"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <strong>Bulk Order Driver Status Update:</strong>
                                <div class="input-group">
                                    <select name="bulk-driver-status" class="form-control">
                                        @foreach ($driver_statuses as $status)
                                            <option value="{{ $status }}" 
                                                @if ($status == $filter['status']) selected @endif>
                                                {{ $status }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="input-group-append">
                                        <button id="bulkDriverStatusBtn" class="btn btn-primary" type="button">
                                            <i class="fa fa-save"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endcan
                </div>
            </div>
            <div class="col-md-12 mb-3">
                <div class="d-flex flex-wrap justify-content-md-end">
                    @if (!auth()->user()->hasRole('Supervisor'))
                        @can('order-download')
                            <a class="btn btn-danger mb-2" href="{{ Request::fullUrlWithQuery(['print' => 1]) }}"><i
                                    class="fa fa-print"></i> PDF</a>
                            <a href="{{ Request::fullUrlWithQuery(['csv' => 1]) }}" class="btn btn-success mb-2 ms-md-2"><i
                                    class="fa fa-download"></i> Excel</a>
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
                        <a class="btn btn-secondary mb-2 ms-md-2" href="{{ route('log.show') }}">
                            Order request log
                        </a>
                    @endif
                    
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
                        href="{{ route('orders.index') }}?today_order={{ date('Y-m-d') }}">
                        <i class="fas fa-calendar"></i> Todays Order
                    </a>
                    @can('order-create')
                        <a class="btn btn-success mb-2 ms-md-2" href="{{ route('orders.create') }}">
                            <i class="fas fa-plus"></i> Create Order
                        </a>
                    @endcan

                    
                </div>
            </div>
        </div>

        @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <span>{{ $message }}</span>
                <button type="button" class="btn-close float-end" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <hr>

        <div class="row">
            @if (!auth()->user()->hasRole('Staff'))
                <!-- Second Column (Filter Form) -->
                <div class="col-md-12">
                    <h3>Filter</h3>
                    <hr>
                    <form action="{{ route('orders.index') }}" method="GET" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-4">
                                <strong>Order Id:</strong>
                                <input type="number" name="order_id" class="form-control"
                                    value="{{ $filter['order_id'] }}">
                            </div>
                            <div class="col-md-4">
                                <strong>Appointment Date:</strong>
                                <input type="date" name="appointment_date" class="form-control"
                                    value="{{ $filter['appointment_date'] }}">
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <strong>Category:</strong>
                                    <select name="category_id" class="form-control">
                                        <option value="">Select</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}"
                                                @if ($filter['category_id'] == $category->id) selected @endif>{{ $category->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @if (!auth()->user()->hasRole('Staff'))
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <strong>Staff:</strong>
                                        <select name="staff_id" class="form-control">
                                            <option value="">Select</option>
                                            @foreach ($users as $staff)
                                                @if ($staff->hasRole('Staff'))
                                                    <option value="{{ $staff->id }}"
                                                        @if ($staff->id == $filter['staff']) selected @endif>
                                                        {{ $staff->name }}
                                                    </option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            @endif

                            <!-- Add more form-groups here to create additional rows with 3 filters in each row -->
                            @if (auth()->user()->hasRole('Admin'))
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <strong>Affiliate:</strong>
                                        <select name="affiliate_id" class="form-control">
                                            <option value="">Select</option>
                                            @foreach ($users as $affiliate)
                                                @if ($affiliate->hasRole('Affiliate'))
                                                    <option value="{{ $affiliate->id }}"
                                                        @if ($affiliate->id == $filter['affiliate']) selected @endif>
                                                        {{ $affiliate->name }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            @endif
                            <div class="col-md-4">
                                <div class="form-group">
                                    <strong>Customer:</strong>
                                    <input type="text" name="customer" class="form-control"
                                        value="{{ $filter['customer'] }}" placeholder="Enter Name or Email">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <strong>Driver:</strong>
                                    <select name="driver_id" class="form-control">
                                        <option value="">Select</option>
                                        @foreach ($users as $driver)
                                            @if ($driver->hasRole('Driver'))
                                                <option value="{{ $driver->id }}"
                                                    @if ($driver->id == $filter['driver']) selected @endif>{{ $driver->name }}
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <strong>Status:</strong>
                                    <select name="status" class="form-control">
                                        <option value="">Select</option>
                                        @foreach ($statuses as $status)
                                            <option value="{{ $status }}"
                                                @if ($status == $filter['status']) selected @endif>{{ $status }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <strong>Driver Status:</strong>
                                    <select name="driver_status" class="form-control">
                                        <option value="">Select</option>
                                        @foreach ($driver_statuses as $status)
                                            <option value="{{ $status }}"
                                                @if ($status == $filter['driver_status']) selected @endif>{{ $status }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <strong>Zone:</strong>
                                    <select name="zone" class="form-control">
                                        <option value="">Select</option>
                                        @foreach ($zones as $zone)
                                            <option
                                                value="{{ $zone }}"@if ($zone == $filter['zone']) selected @endif>
                                                {{ $zone }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <strong>Date From:</strong>
                                <input type="date" name="date_from" class="form-control"
                                    value="{{ $filter['date_from'] }}">
                            </div>
                            <div class="col-md-4">
                                <strong>Date To:</strong>
                                <input type="date" name="date_to" class="form-control"
                                    value="{{ $filter['date_to'] }}">
                            </div>
                            <div class="col-md-4">
                                <strong>Time Start:</strong>
                                <input type="time" name="time_start" class="form-control"
                                    value="{{ $filter['time_start'] }}">
                            </div>
                            <div class="col-md-4">
                                <strong>Time End:</strong>
                                <input type="time" name="time_end" class="form-control"
                                    value="{{ $filter['time_end'] }}">
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <strong>Payment Method:</strong>
                                    <select name="payment_method" class="form-control">
                                        <option value="">Select</option>
                                        @foreach ($payment_methods as $payment_method)
                                            <option value="{{ $payment_method }}"
                                                @if ($payment_method == $filter['payment_method']) selected @endif>{{ $payment_method }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 offset-md-8">
                                <div class="d-flex flex-wrap justify-content-md-end">
                                    <div class="col-md-3 mb-3">
                                        <a href="{{ url()->current() }}" class="btn btn-lg btn-secondary">Reset</a>
                                    </div>
                                    <div class="col-md-9 mb-3">
                                        <button type="submit" class="btn btn-lg btn-block btn-primary">Filter</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            @endif
        </div>
        <div class="fluid_container">
            <div class="row">
                <!-- First Column (Table) -->
                <div class="col-md-12 mt-3">
                    <h1>Orders: ({{ $total_order }})</h1>
                    @include('orders.list')
                    {!! $orders->links() !!}
                </div>
            </div>
        </div>
    </div>

    <script>
        $('.all-item-checkbox').click(function() {
            var allCheckboxState = $(this).prop('checked');
            $('.item-checkbox').prop('checked', allCheckboxState);
        });
        $('#bulkStatusBtn').click(function () {
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

    $('#bulkBookingUpdateBtn').click(function () {
        const selectedItems = getSelectedItems();

        if (selectedItems.length > 0) {
            $.ajax({
                url: '/addToCartModal/' + selectedItems+"?bulk=true",
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

    $('#bulkDriverStatusBtn').click(function () {
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
            .map(function () {
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
            success: function (data) {
                alert(data.message);
                window.location.reload();
            },
            error: function (error) {
                console.error('Error:', error);
                alert('An error occurred while processing your request. Please try again.');
            }
        });
    }
    </script>
@endsection
