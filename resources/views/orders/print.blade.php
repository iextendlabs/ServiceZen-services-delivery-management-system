<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Order Print</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>
        .table td,
        .table th {
            vertical-align: middle;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-md-12 margin-tb">
                <div class="float-start">
                    <h2>Orders</h2>
                </div>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-12">
                <table class="table table-bordered">
                    <tr>
                        <th>Order Id</th>
                        <th>Customer</th>
                        <th>Staff</th>
                        <th>Data \ Time Slot</th>
                        <th>Total Amount</th>
                        <th>Payment Method</th>
                        <th>City</th>
                        <th>Status</th>
                        <th>Comment</th>
                        <th>Date Added</th>
                    </tr>
                    @if(count($orders))
                    @foreach ($orders as $order)
                    <tr>
                        <td>#{{ $order->id }}</td>
                        <td>@if($order->customer){{ $order->customer->name }}@endif</td>
                        <td>@if($order->staff){{ $order->staff->user->name }}@endif</td>
                        <td>{{ $order->date }} \ @if($order->time_slot) {{ date('h:i A', strtotime($order->time_slot->time_start)) }} -- {{ date('h:i A', strtotime($order->time_slot->time_end)) }} @endif</td>
                        <td>{{ $order->total_amount }}</td>
                        <td>{{ $order->payment_method }}</td>
                        <td>{{ $order->city }}</td>
                        <td>{{ $order->status }}</td>
                        <td>{{ $order->order_comment }}</td>
                        <td>{{ $order->created_at }}</td>
                    </tr>
                    @endforeach
                    @else
                    <tr>
                        <td colspan="7" class="text-center"> There is no Order</td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>
    </div>
</body>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<script>
    window.onload = function() {
        window.print();
    };
</script>

</html>