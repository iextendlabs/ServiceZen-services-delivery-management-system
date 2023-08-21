<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <style>
        body {
            font-family: Arial, sans-serif;
        }
        h3 {
            color: #333;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
        }
        th, td {
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
    <main role="main">
                    <h3>New Order Place</h3>
                <table>
                    <td colspan="2">Order Details</td>
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
                            <b>Time:</b> {{ date('h:i A', strtotime($order->time_slot->time_start)) }} -- {{ date('h:i A', strtotime($order->time_slot->time_end)) }}
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
                            <b>Name:</b> {{ $order->customer->name }} <br><br>
                            <b>Email:</b> {{ $order->customer->email }}
                        </td>
                        <td>
                            <b>Phone Number:</b> {{ $order->number }} <br><br>
                            <b>Whatsapp Number:</b> {{ $order->whatsapp }}
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
                        <td>{{ $orderService->service->name }}</td>
                        <td>{{ $orderService->status }}</td>
                        <td>{{ $orderService->service->duration }}</td>
                        <td>@currency($orderService->price)</td>
                    </tr>
                    @endforeach

                    <tr>
                        <td colspan="3"><strong>Sub Total:</strong></td>
                        <td>@currency($order->order_total->sub_total)</td>
                    </tr>
                    <tr>
                        <td colspan="3"><strong>Coupon Discount:</strong></td>
                        <td>{{ config('app.currency') }}{{ $order->order_total->discount ? '-'.$order->order_total->discount : 0 }}</td>
                    </tr>
                    <tr>
                        <td colspan="3"><strong>Staff Transport Charges:</strong></td>
                        <td>{{ config('app.currency') }}{{ $order->order_total->transport_charges ? $order->order_total->transport_charges : 0 }}</td>
                    </tr>
                    <tr>
                        <td colspan="3"><strong>Staff Charges:</strong></td>
                        <td>{{ config('app.currency') }}{{ $order->order_total->staff_charges ? $order->order_total->staff_charges : 0 }}</td>
                    </tr>
                    <tr>
                        <td colspan="3"><strong>Total:</strong></td>
                        <td>@currency($order->total_amount)</td>
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
            </div>
        </div>
    </main>
</body>

</html>