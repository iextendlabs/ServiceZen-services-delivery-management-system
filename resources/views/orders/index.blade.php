    @extends('layouts.app')
    @section('content')
    <div class="row">
        <div class="col-md-12 margin-tb">
            <div class="float-start">
                <h2>Orders</h2>
            </div>
            <div class="float-end">
                
                @can('order-download')
                <button type="button" class="btn btn-success float-end no-print" id="csvButton"><i class="fa fa-download"></i> Excel</button>
                <button type="button" class="btn btn-danger float-end no-print" id="printButton" style="margin-right: 10px;"><i class="fa fa-print"></i> PDF</button>
                @endcan
                <!-- Assuming you have Font Awesome properly linked in your HTML file -->

                <!-- All Orders -->
                <a class="btn btn-secondary ml-2 float-end" href="/orders" style="margin-right: 10px;">
                <i class="fas fa-list"></i> All Orders
                </a>

                <!-- Pending Order -->
                <a class="btn btn-primary float-end" href="/orders?status=Pending" style="margin-right: 10px;">
                <i class="fas fa-clock"></i> Pending Order
                </a>

                <!-- Complete Order -->
                <a class="btn btn-success float-end" href="/orders?status=Complete" style="margin-right: 10px;">
                <i class="fas fa-check"></i> Complete Order
                </a>

                <!-- Canceled Order -->
                <a class="btn btn-danger float-end" href="/orders?status=Canceled" style="margin-right: 10px;">
                <i class="fas fa-times"></i> Canceled Order
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

                <div class="offset-9 col-md-3 text-center">
                    <button type="submit" class="btn btn-lg btn-block btn-primary">Submit</button>
                </div>
            </div>
        </form>
    </div>

    <!-- First Column (Table) -->
    <div class="col-md-12 mt-3">
    <table class="table table-bordered table-responsive">
                <tr>
                    <th>Order Id</th>
                    <th>Customer</th>
                    <th>Staff</th>
                    <th>Data \ Time Slot</th>
                    <th>Total Amount</th>
                    <th>Payment Method</th>
                    <th>Status</th>
                    <th>Comment</th>
                    <th>Date Added</th>
                    <th style="min-width:160px">Action</th>
                </tr>
                @if(count($orders))
                @foreach ($orders as $order)
                <tr>
                    <td>#{{ $order->id }}</td>

                    <td>{{ $order->customer_name }}</td>
                    <td>{{ $order->staff_name }}</td>
                    <td>{{ $order->date }} \ {{ $order->time_slot_value }}</td>
                    <td>@currency($order->total_amount)</td>
                    <td>{{ $order->payment_method }}</td>
                    <td>{{ $order->status }}</td>
                    <td>{{ $order->order_comment }}</td>
                    <td>{{ $order->created_at }}</td>
                    <td>
                        <form action="{{ route('orders.destroy',$order->id) }}" method="POST">
                            <a class="btn btn-info" href="{{ route('orders.show',$order->id) }}">
                            <i class="fas fa-eye"></i> 
                            </a>

                            @can('order-edit')
                            <a class="btn btn-primary" href="{{ route('orders.edit',$order->id) }}">
                            <i class="fas fa-edit"></i> 
                            </a>
                            @endcan
                            @csrf
                            @method('DELETE')
                            @can('order-delete')
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash"></i> 
                            </button>

                            @endcan
                        </form>
                    </td>
                </tr>
                @endforeach
                @else
                <tr>
                    <td colspan="11" class="text-center"> There is no Order</td>
                </tr>
                @endif
            </table>
            {!! $orders->appends($existingParameters)->links() !!}
    </div>
</div>

    <script>
        $(document).ready(function() {
            // Function to add or update a URL parameter
            function updateUrlParameter(url, key, value) {
                var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
                var separator = url.indexOf('?') !== -1 ? "&" : "?";

                if (url.match(re)) {
                    return url.replace(re, '$1' + key + "=" + value + '$2');
                } else {
                    return url + separator + key + "=" + value;
                }
            }

            // Function to get the current URL parameters
            function getUrlParameters(url) {
                var params = {};
                var parser = document.createElement('a');
                parser.href = url;
                var query = parser.search.substring(1);
                var vars = query.split('&');

                for (var i = 0; i < vars.length; i++) {
                    var pair = vars[i].split('=');
                    params[pair[0]] = decodeURIComponent(pair[1]);
                }

                return params;
            }

            // Function to update the URL and redirect to the modified URL
            function redirectToPrint() {
                var currentUrl = window.location.href;

                var params = getUrlParameters(currentUrl);

                params.print = 1; // Add or update the "print" parameter

                var modifiedUrl = updateUrlParameter(currentUrl, 'print', 1);
                for (var key in params) {
                    if (key !== 'print') {
                        modifiedUrl = updateUrlParameter(modifiedUrl, key, params[key]);
                    }
                }

                window.location.href = modifiedUrl;
            }

            // Function to update the URL and redirect to the modified URL
            function redirectToCSV() {
                var currentUrl = window.location.href;

                var params = getUrlParameters(currentUrl);

                params.csv = 1; // Add or update the "csv" parameter

                var modifiedUrl = updateUrlParameter(currentUrl, 'csv', 1);
                for (var key in params) {
                    if (key !== 'csv') {
                        modifiedUrl = updateUrlParameter(modifiedUrl, key, params[key]);
                    }
                }

                window.location.href = modifiedUrl;
            }

            $('#printButton').click(function(e) {
                e.preventDefault();
                redirectToPrint();
            });

            $('#csvButton').click(function(e) {
                e.preventDefault();
                redirectToCSV();
            });
        });
    </script>

    @endsection