<html>
<head>
    <title>Order Replaced</title>
</head>
<body>
    <h1>Your Order Has Been Successfully Replaced.</h1>
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