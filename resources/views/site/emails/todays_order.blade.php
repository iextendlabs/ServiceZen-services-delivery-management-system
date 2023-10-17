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
    <main role="main">
        <h3>Today's Order</h3>
        <table>
            <tr>
                <th>Order Id</th>
                <th>Customer Name</th>
                <th>Staff Name</th>
                <th>Date</th>
                <th>Slots</th>
                <th>Total Amount</th>
                <th>Payment Method</th>
                <th>Status</th>
                <th>Date Added</th>
            </tr>
            @foreach($orders as $order)
            <tr>
                <td>{{ $order->id }}</td>
                <td>{{ $order->customer_name }}</td>
                <td>{{ $order->staff_name }}</td>
                <td>{{ $order->date }}</td>
                <td>{{ $order->time_slot_value }}</td>
                <td>{{ $order->total_amount }}</td>
                <td>{{ $order->payment_method }}</td>
                <td>{{ $order->created_at }}</td>
                <td>{{ $order->status }}</td>
            </tr>
            @endforeach
        </table>
        </div>
        </div>
    </main>
</body>

</html>