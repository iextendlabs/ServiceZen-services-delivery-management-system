<!DOCTYPE html>
<html lang="en">

<head>
    <title>Order#{{ $order->id }}</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        table {
            font-family: arial, sans-serif;
            border-collapse: collapse;
            width: 100%;
        }

        td,
        th {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }
    </style>
</head>

<body>
    @php
    $sub_total = 0;
    $staff_charges = 0;
    $total_amount = 0;
    $staff_transport_charges = 0;
    @endphp
    <h2>Order#{{ $order->id }}</h2>
    <table>
        <td colspan="2"><i></i> Order Details</td>
        <tr>
            <td>
                <b>Order ID:</b> #{{ $order->id }} <br><br>
                <b>Date Added:</b> {{ $order->created_at }} <br><br>
                <b>Order Status:</b> {{ $order->status }}
            </td>
            <td>
                <b>Total Amount:</b> @currency($order->total_amount, true)
                @if($order->currency_symbol && $order->currency_rate)
                    ({{ $order->currency_symbol }}{{ number_format($order->total_amount * $order->currency_rate, 2) }})
                @endif <br><br>
                <b>Payment Method:</b> {{ $order->payment_method }}
            </td>
        </tr>
    </table>
    <table>
        <td colspan="3">Appointment Details</td>
        <tr>
            <td>
                <b>Staff:</b> {{ $order->staff_name }}

            </td>
            <td>
                <b>Date:</b> {{ $order->date }}
            </td>
            <td>
                <b>Time:</b> {{ $order->time_slot_value }}
            </td>
        </tr>
    </table>
    <table>
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
    <table>
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
        <tr>
            <td colspan="3">
                <b>Location of customer:</b> <a href="https://maps.google.com/maps?q={{ $order->latitude }},+{{ $order->longitude }}" target="_blank">click</a>
            </td>
        </tr>
    </table>
    <table>
        <td colspan="4">Services Details</td>
        <tr>
            <th>Service Name</th>
            <th>Status</th>
            <th>Duration</th>
            <th>Amount</th>
        </tr>
        @foreach($order->orderServices as $orderService)
        <tr>
            <td>{{ $orderService->service_name }}</td>
            <td>{{ $orderService->status }}</td>
            <td>{{ $orderService->duration ?? $orderService->service->duration ?? '' }}</td>
            <td class="text-right">
                @currency($orderService->price,true) 
                @if($order->currency_symbol && $order->currency_rate)
                    ({{ $order->currency_symbol }}{{ number_format($orderService->price * $order->currency_rate, 2) }})
                @endif
            </td>
        </tr>
        @endforeach

        <tr>
            <td colspan="3"><strong>Sub Total:</strong></td>
            <td class="text-right">
                @currency($order->order_total->sub_total,true)
                @if($order->currency_symbol && $order->currency_rate)
                    ({{ $order->currency_symbol }}{{ number_format($order->order_total->sub_total * $order->currency_rate, 2) }})
                @endif
            </td>
        </tr>
        <tr>
            <td colspan="3"><strong>Coupon Discount:</strong></td>
            <td class="text-right">
                @currency($order->order_total->discount ? '-' . $order->order_total->discount : 0 ,true)
                @if($order->currency_symbol && $order->currency_rate)
                    ({{ $order->currency_symbol }}{{ number_format(($order->order_total->discount ?? 0) * $order->currency_rate, 2) }})
                @endif
            </td>
        </tr>
        <tr>
            <td colspan="3"><strong>Staff Transport Charges:</strong></td>
            <td class="text-right">
                @currency($order->order_total->transport_charges ? $order->order_total->transport_charges : 0 ,true)
                @if($order->currency_symbol && $order->currency_rate)
                    ({{ $order->currency_symbol }}{{ number_format(($order->order_total->transport_charges ?? 0) * $order->currency_rate, 2) }})
                @endif
            </td>
        </tr>
        <tr>
            <td colspan="3"><strong>Staff Charges:</strong></td>
            <td class="text-right">
                @currency( $order->order_total->staff_charges ? $order->order_total->staff_charges : 0,true)
                @if($order->currency_symbol && $order->currency_rate)
                    ({{ $order->currency_symbol }}{{ number_format(($order->order_total->staff_charges ?? 0) * $order->currency_rate, 2) }})
                @endif
            </td>
        </tr>
        <tr>
            <td colspan="3"><strong>Total:</strong></td>
            <td class="text-right">
                @currency($order->total_amount,true)
                @if($order->currency_symbol && $order->currency_rate)
                    ({{ $order->currency_symbol }}{{ number_format($order->total_amount * $order->currency_rate, 2) }})
                @endif
            </td>
        </tr>
    </table>
    @if($order->order_comment)
    <table>
        <th colspan="4">Order Comment</th>
        <tr>
            <td>{!! nl2br($order->order_comment) !!}</td>
        </tr>
    </table>
    @endif

</body>

</html>