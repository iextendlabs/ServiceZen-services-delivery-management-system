    @extends('layouts.app')
    @section('content')
    <div class="row">
        <div class="col-md-12 margin-tb">
            <div class="float-start">
                <h2>Orders</h2>
            </div>
            <div class="float-end">

            @can('order-download')
                <a href="{{ request()->fullUrlWithQuery(['print' => '1']) }}" class="btn btn-danger float-end no-print"><i class="fa fa-print"></i> PDF</a>
                <a href="{{ request()->fullUrlWithQuery(['csv' => '1']) }}" class="btn btn-success float-end no-print" style="margin-right: 10px;"><i class="fa fa-download"></i> Excel</a>
                @endcan
                <!-- Assuming you have Font Awesome properly linked in your HTML file -->
                @if(auth()->user()->getRoleNames() == '["Admin"]')
                <!-- All Orders -->
                <a class="btn btn-secondary ml-2 float-end" href="/orders" style="margin-right: 10px;">
                    <i class="fas fa-list"></i> All
                </a>

                <!-- Canceled Order -->
                <a class="btn btn-danger float-end" href="/orders?status=Canceled" style="margin-right: 10px;">
                    <i class="fas fa-times"></i> Canceled
                </a>

                @endif

                <!-- Complete Order -->
                <a class="btn btn-success float-end" href="/orders?status=Complete" style="margin-right: 10px;">
                    <i class="fas fa-check"></i> Complete
                </a>

                <!-- Inprogress Order -->
                <a class="btn btn-info float-end" href="/orders?status=Inprogress" style="margin-right: 10px;">
                    <i class="fas fa-hourglass-split"></i> Inprogress
                </a>

                <!-- Rejected Order -->
                <a class="btn btn-warning float-end" href="/orders?status=Rejected" style="margin-right: 10px;">
                    <i class="fas fa-times"></i> Rejected
                </a>

                <!-- Accepted Order -->
                <a class="btn btn-success float-end" href="/orders?status=Accepted" style="margin-right: 10px;">
                    <i class="fas fa-check"></i> Accepted
                </a>

                <!-- Pending Order -->
                <a class="btn btn-primary float-end" href="/orders?status=Pending" style="margin-right: 10px;">
                    <i class="fas fa-clock"></i> Pending
                </a>

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
        @if(auth()->user()->getRoleNames() != '["Staff"]')
        <!-- Second Column (Filter Form) -->
        <div class="col-md-12">
            <h3>Filter</h3>
            <hr>
            <form action="{{ route('orders.index') }}" method="GET" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <strong>Appointment Date:</strong>
                            <input type="date" name="appointment_date" class="form-control" value="{{ $filter['appointment_date'] }}">
                        </div>
                    </div>
                    @if(auth()->user()->getRoleNames() != '["Staff"]')
                    <div class="col-md-4">
                        <div class="form-group">
                            <strong>Staff:</strong>
                            <select name="staff_id" class="form-control">
                                <option value="">Select</option>
                                @foreach ($users as $staff)
                                @if($staff->getRoleNames() == '["Staff"]')
                                @if($staff->id == $filter['staff'])
                                <option value="{{ $staff->id }}" selected>{{ $staff->name }}</option>
                                @else
                                <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                                @endif
                                @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @endif
                    <div class="col-md-4">
                        <div class="form-group">
                            <strong>Status:</strong>
                            <select name="status" class="form-control">
                                <option value="">Select</option>
                                @foreach ($statuses as $status)
                                @if($status == $filter['status'])
                                <option value="{{ $status }}" selected>{{ $status }}</option>
                                @else
                                <option value="{{ $status }}">{{ $status }}</option>
                                @endif
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Add more form-groups here to create additional rows with 3 filters in each row -->
                    @if(auth()->user()->getRoleNames() == '["Admin"]')
                    <div class="col-md-4">
                        <div class="form-group">
                            <strong>Affiliate:</strong>
                            <select name="affiliate_id" class="form-control">
                                <option value="">Select</option>
                                @foreach ($users as $affiliate)
                                @if($affiliate->getRoleNames() == '["Affiliate"]')
                                @if($affiliate->id == $filter['affiliate'])
                                <option value="{{ $affiliate->id }}" selected>{{ $affiliate->name }}</option>
                                @else
                                <option value="{{ $affiliate->id }}">{{ $affiliate->name }}</option>
                                @endif
                                @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @endif
                    <div class="col-md-4">
                        <div class="form-group">
                            <strong>Customer:</strong>
                            <select name="customer_id" class="form-control">
                                <option value="">Select</option>
                                @foreach ($users as $customer)
                                @if($customer->getRoleNames() == '["Customer"]')
                                @if($customer->id == $filter['customer'])
                                <option value="{{ $customer->id }}" selected>{{ $customer->name }}</option>
                                @else
                                <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                @endif
                                @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <strong>Payment Method:</strong>
                            <select name="payment_method" class="form-control">
                                <option value="">Select</option>
                                @foreach ($payment_methods as $payment_method)
                                @if($payment_method == $filter['payment_method'])
                                <option value="{{ $payment_method }}" selected>{{ $payment_method }}</option>
                                @else
                                <option value="{{ $payment_method }}">{{ $payment_method }}</option>
                                @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <!-- Add more form-groups here to create additional rows with 3 filters in each row -->

                    
                    <div class="offset-6 col-md-3 text-center">
                        <a href="{{ url()->current() }}
" class="btn btn-lg btn-secondary">Reset</a>
                    </div>
                    <div class="col-md-3 text-center">
                        <button type="submit" class="btn btn-lg btn-block btn-primary">Filter</button>
                    </div>
                </div>
            </form>
        </div>
        @endif
        <!-- First Column (Table) -->
        <div class="col-md-12 mt-3">
            @include('orders.list')
            {!! $orders->links() !!}

        </div>
    </div>

    @endsection