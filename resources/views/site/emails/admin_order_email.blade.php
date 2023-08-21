<!doctype html>
<html lang="en">

<head>
    <base href="{{ env('APP_URL') }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>{{ env('APP_NAME') }}</title>

    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">
    <style>
        .table {
            margin-bottom: 0px !important;
        }
    </style>
</head>

<body>
    <main role="main">
        <div class="container">
            <div class="row">
                <div class="col-md-12 text-center">
                    <h3>New Order Place</h3>
                </div>
            </div>
            <div>
                <table class="table table-striped table-bordered album bg-light">
                    <td class="text-left" colspan="2"><i class="fas fa-shopping-cart"></i> Order Details</td>
                    <tr>
                        <td>
                            <b>Order ID:</b> #{{ $order->id }} <br><br>
                            <b>Date Added:</b> {{ $order->created_at }} <br><br>
                            <b>Order Status:</b> {{ $order->status }}
                        </td>
                        <td>
                            <b>Total Amount:</b> @currency( $order->total_amount ) <br><br>
                            <b>Payment Method:</b> {{ $order->payment_method }}
                        </td>
                    </tr>
                </table>
                <table class="table table-striped table-bordered album bg-light">
                    <td class="text-left" colspan="3"><i class="fas fa-clock"></i> Appointment Details</td>
                    <tr>
                        <td>
                            <b>Staff:</b> {{ $order->staff_name }}

                        </td>
                        <td>
                            <b>Date:</b> {{ $order->date }}
                        </td>
                        <td>
                            <b>Time:</b> {{ date('h:i A', strtotime($order->time_slot->time_start)) }} -- {{ date('h:i A', strtotime($order->time_slot->time_end)) }}
                        </td>
                    </tr>
                </table>
                <table class="table table-striped table-bordered album bg-light">
                    <td class="text-left" colspan="3">Address Details</td>
                    <tr>
                        <td>
                            <b>Building Name:</b> {{ $order->buildingName }} <br><br>
                            <b>Area:</b> {{ $order->area }}
                        </td>
                        <td>
                            <b>Flat / Villa:</b> {{ $order->total_amount }} <br><br>
                            <b>Land Mark:</b> {{ $order->landmark }}
                        </td>
                        <td>
                            <b>Street:</b> {{ $order->street }} <br><br>
                            <b>City:</b> {{ $order->city }}
                        </td>
                    </tr>
                </table>
                <table class="table table-striped table-bordered album bg-light">
                    <td class="text-left" colspan="2">Customer Details</td>
                    <tr>
                        <td>
                            <b>Name:</b> {{ $order->customer->name }} <br><br>
                            <b>Email:</b> {{ $order->customer->email }}
                        </td>
                        <td>
                            <b>Phone Number:</b> {{ $order->number }} <br><br>
                            <b>Whatsapp Number:</b> {{ $order->whatsapp }}
                        </td>
                    </tr>
                </table>
                <table class="table table-striped table-bordered album bg-light">
                    <td class="text-left" colspan="4"><i class="fas fa-spa"></i> Services Details</td>
                    <tr>
                        <th>Service Name</th>
                        <th>Status</th>
                        <th>Duration</th>
                        <th class="text-right">Amount</th>
                    </tr>
                    @foreach($order->orderServices as $orderService)
                    <tr>
                        <td>{{ $orderService->service->name }}</td>
                        <td>{{ $orderService->status }}</td>
                        <td>{{ $orderService->service->duration }}</td>
                        <td class="text-right">@currency($orderService->price)</td>
                    </tr>
                    @endforeach

                    <tr>
                        <td colspan="3" class="text-right"><strong>Sub Total:</strong></td>
                        <td class="text-right">@currency($order->order_total->sub_total)</td>
                    </tr>
                    <tr>
                        <td colspan="3" class="text-right"><strong>Coupon Discount:</strong></td>
                        <td class="text-right">{{ config('app.currency') }}{{ $order->order_total->discount ? '-'.$order->order_total->discount : 0 }}</td>
                    </tr>
                    <tr>
                        <td colspan="3" class="text-right"><strong>Staff Transport Charges:</strong></td>
                        <td class="text-right">{{ config('app.currency') }}{{ $order->order_total->transport_charges ? $order->order_total->transport_charges : 0 }}</td>
                    </tr>
                    <tr>
                        <td colspan="3" class="text-right"><strong>Staff Charges:</strong></td>
                        <td class="text-right">{{ config('app.currency') }}{{ $order->order_total->staff_charges ? $order->order_total->staff_charges : 0 }}</td>
                    </tr>
                    <tr>
                        <td colspan="3" class="text-right"><strong>Total:</strong></td>
                        <td class="text-right">@currency($order->total_amount)</td>
                    </tr>
                </table>
                @if($order->order_comment)
                <table class="table table-striped table-bordered album bg-light">
                    <th class="text-left" colspan="4">Order Comment</th>
                    <tr>
                        <td class="text-left">{!! nl2br($order->order_comment) !!}</td>
                    </tr>
                </table>
                @endif
            </div>
        </div>
    </main>
</body>

</html>