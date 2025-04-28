<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        h4 {
            color: #333;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>

<body>

    <h4>Your Order Has Been Successfully Placed.</h4>

    <table class="table">
        <td colspan="2">Order Details</td>
        <tr>
            <td>
                <b>Order ID:</b> #{{ $order->id }} <br><br>
                <b>Date Added:</b> {{ $order->created_at }} <br><br>
                <b>Order Status:</b> {{ $order->status }}
            </td>
            <td>
                <b>Total Amount:</b> @currency( $order->total_amount,false ) <br><br>
                <b>Payment Method:</b> {{ $order->payment_method }}
            </td>
        </tr>
    </table>
    <table class="table">
        <td colspan="3">Appointment Details</td>
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
    <table class="table">
        <td colspan="3">Address Details</td>
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
    <table class="table">
        <td colspan="2">Customer Details</td>
        <tr>
            <td>
                <b>Name:</b> {{ $order->customer_name }} <br><br>
                <b>Email:</b> {{ $order->customer_email }} <br><br>
                <b>Gender:</b> {{ $order->gender }}
            </td>
            <td>
                <b>Phone Number:</b> {{ $order->number }} <br><br>
                <b>Whatsapp Number:</b> {{ $order->whatsapp }}
            </td>
        </tr>
    </table>
    <table class="table">
        <td colspan="4">Services Details</td>
        <tr>
            <th>Service Name</th>
            <th>Status</th>
            <th>Duration</th>
            <th>Amount</th>
        </tr>
        @foreach($order->orderServices as $orderService)
        <tr>
            <td>
                <b>{{ $orderService->service_name }}</b>
                @if($orderService->option_name)
                    @foreach(explode(',', $orderService->option_name) as $option)
                        <br>{{ trim($option) }}
                    @endforeach
                @endif
            </td>
            <td>{{ $orderService->status }}</td>
            <td>{{ $orderService->duration ?? $orderService->service->duration ?? '' }}</td>
            <td>@currency($orderService->price,false)</td>
        </tr>
        @endforeach

        <tr>
            <td colspan="3"><strong>Sub Total:</strong></td>
            <td>@currency($order->order_total->sub_total,false)</td>
        </tr>
        <tr>
            <td colspan="3"><strong>Coupon Discount:</strong></td>
            <td>@currency($order->order_total->discount ? '-'.$order->order_total->discount : 0,false )</td>
        </tr>
        <tr>
            <td colspan="3"><strong>Staff Transport Charges:</strong></td>
            <td>@currency($order->order_total->transport_charges ? $order->order_total->transport_charges : 0,false )</td>
        </tr>
        <tr>
            <td colspan="3"><strong>Staff Charges:</strong></td>
            <td>@currency($order->order_total->staff_charges ? $order->order_total->staff_charges : 0,false )</td>
        </tr>
        <tr>
            <td colspan="3"><strong>Total:</strong></td>
            <td>@currency($order->total_amount,false)</td>
        </tr>
    </table>
    @if($order->order_comment && $order->order_comment != 'null')
    <table class="table">
        <th colspan="4">Order Comment</th>
        <tr>
            <td>{!! nl2br($order->order_comment) !!}</td>
        </tr>
    </table>
    @endif
    <br>
    @if( empty($data['password']))
    <p>We have create your user, you can visit our website to see your order detail</p>
    <p>Your Login credentials are.</p>
    <p> <b>Name:</b> {{ $data['name'] }}</p>
    <p><b>Email:</b> {{ $data['email'] }}</p>
    <p><b>Password:</b> {{ $data['password'] }}</p>
    @else
    <p>You have Customer account, you can visit our website to see your order detail</p>
    <p>Your Login credentials are.</p>
    <p> <b>Name:</b> {{ $data['name'] }}</p>
    <p><b>Email:</b> {{ $data['email'] }}</p>
    @endif

</body>

</html>