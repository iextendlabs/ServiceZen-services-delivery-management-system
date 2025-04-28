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
        @page  {
            margin: 0;
        }
        .table td,
        .table th {
            vertical-align: middle;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="float-start">
                    <h2>Orders</h2>
                </div>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-12">
                <table class="table table-striped table-bordered ">
                    <tr>
                        <th>Sr#</th>
                        <th>Order#</th>
                        <th>Staff</th>
                        <th><i class="fas fa-clock"></i> Appointment Date</th>
                        <th><i class="fas fa-clock"></i> Slots</th>

                        @if (auth()->user()->hasRole("Supervisor"))
                            <th>Landmark</th>
                            <th>Area</th>
                            <th>City</th>
                            <th>Building name</th>
                        @else
                            <th>Customer</th>
                            <th>Number</th>
                            <th>WhatsApp</th>
                            <th>Total Amount</th>
                            <th>Payment Method</th>
                            <th>Comment</th>
                            <th>Date Added</th>
                        @endif
                        <th>Status</th>
                    </tr>
                    @if(count($orders))
                    @foreach ($orders as $key => $order)
                    <tr>
                        <td>{{ ++$key }}</td>
                        <td>#{{ $order->id }}</td>
                        <td>{{ $order->staff_name }}</td>
                        <td>{{ $order->date }}</td>
                        <td>{{ $order->time_slot_value }}</td>
                        @if (auth()->user()->hasRole("Supervisor"))
                            <td>{{ $order->landmark }}</td>
                            <td>{{ $order->area }}</td>
                            <td>{{ $order->city }}</td>
                            <td>{{ $order->buildingName }}</td>
                        @else
                            <td>{{ $order->customer_name }}</td>
                            <td>{{ $order->number }}</td>
                            <td>{{ $order->whatsapp }}</td>
                            <td>@currency($order->total_amount,true)</td>
                            <td>{{ $order->payment_method }}</td>
                            <td>@if(isset($order->order_comment) && $order->order_comment != 'null'){{ substr($order->order_comment, 0, 50) }}... @endif</td>
                            <td>{{ $order->created_at }}</td>
                        @endif
                        <td>{{ $order->status }}</td>
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