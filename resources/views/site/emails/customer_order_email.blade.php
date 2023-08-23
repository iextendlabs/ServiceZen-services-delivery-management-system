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
                <b>Order ID:</b> #{{ $data['order']->id }} <br><br>
                <b>Date Added:</b> {{ $data['order']->created_at }} <br><br>
                <b>Order Status:</b> {{ $data['order']->status }}
            </td>
            <td>
                <b>Total Amount:</b> @currency( $data['order']->total_amount ) <br><br>
                <b>Payment Method:</b> {{ $data['order']->payment_method }}
            </td>
        </tr>
    </table>
    <table class="table">
        <td colspan="3">Appointment Details</td>
        <tr>
            <td>
                <b>Staff:</b> {{ $data['order']->staff_name }}

            </td>
            <td>
                <b>Date:</b> {{ $data['order']->date }}
            </td>
            <td>
                <b>Time:</b> {{ date('h:i A', strtotime($data['order']->time_slot->time_start)) }} -- {{ date('h:i A', strtotime($data['order']->time_slot->time_end)) }}
            </td>
        </tr>
    </table>
    <table class="table">
        <td colspan="3">Address Details</td>
        <tr>
            <td>
                <b>Building Name:</b> {{ $data['order']->buildingName }} <br><br>
                <b>Area:</b> {{ $data['order']->area }}
            </td>
            <td>
                <b>Flat / Villa:</b> {{ $data['order']->total_amount }} <br><br>
                <b>Land Mark:</b> {{ $data['order']->landmark }}
            </td>
            <td>
                <b>Street:</b> {{ $data['order']->street }} <br><br>
                <b>City:</b> {{ $data['order']->city }}
            </td>
        </tr>
    </table>
    <table class="table">
        <td colspan="2">Customer Details</td>
        <tr>
            <td>
                <b>Name:</b> {{ $order->customer->name }} <br><br>
                <b>Email:</b> {{ $order->customer->email }} <br><br>
                <b>Gender:</b> {{ $order->gender }}
            </td>
            <td>
                <b>Phone Number:</b> {{ $data['order']->number }} <br><br>
                <b>Whatsapp Number:</b> {{ $data['order']->whatsapp }}
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
        @foreach($data['order']->orderServices as $orderService)
        <tr>
            <td>{{ $orderService->service->name }}</td>
            <td>{{ $orderService->status }}</td>
            <td>{{ $orderService->service->duration }}</td>
            <td>@currency($orderService->price)</td>
        </tr>
        @endforeach

        <tr>
            <td colspan="3"><strong>Sub Total:</strong></td>
            <td>@currency($data['order']->order_total->sub_total)</td>
        </tr>
        <tr>
            <td colspan="3"><strong>Coupon Discount:</strong></td>
            <td>{{ config('app.currency') }}{{ $data['order']->order_total->discount ? '-'.$data['order']->order_total->discount : 0 }}</td>
        </tr>
        <tr>
            <td colspan="3"><strong>Staff Transport Charges:</strong></td>
            <td>{{ config('app.currency') }}{{ $data['order']->order_total->transport_charges ? $data['order']->order_total->transport_charges : 0 }}</td>
        </tr>
        <tr>
            <td colspan="3"><strong>Staff Charges:</strong></td>
            <td>{{ config('app.currency') }}{{ $data['order']->order_total->staff_charges ? $data['order']->order_total->staff_charges : 0 }}</td>
        </tr>
        <tr>
            <td colspan="3"><strong>Total:</strong></td>
            <td>@currency($data['order']->total_amount)</td>
        </tr>
    </table>
    @if($data['order']->order_comment)
    <table class="table">
        <th colspan="4">Order Comment</th>
        <tr>
            <td>{!! nl2br($data['order']->order_comment) !!}</td>
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