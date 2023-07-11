<html>
<head>
    <title>New Order Placed</title>
</head>
<body>
    <h1>You have received an order.</h1>
        <p> <b>Order ID:</b> {{ $order->id }}</p>
        <p><b>Date Added:</b> {{ $order->created_at }}</p>
        <p><b>Order Status:</b> {{ $order->status }}</p><br>
        <p> <b>Totals</b></p>
        <p><b>Sub-Total:</b> @currency( $order->order_total->sub_total )</p>
        <p><b>Staff Charges:</b> @currency( $order->order_total->staff_charges )</p>
        <p><b>Transport Charges:</b> @currency( $order->order_total->transport_charges )</p>
        <p><b>Total Amount:</b> @currency( $order->total_amount )</p>
</body>
</html>