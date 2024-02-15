<!DOCTYPE html>
<html>
<head>
    <title>Order Replacement Issue Notification</title>
</head>
<body>
    <p>Hello,</p>

    <p>The customer named {{ $body['name'] }} is facing an issue with replacing the order.</p>
    <p>Customer details:</p>
    <ul>
        <li>Name: {{ $body['name'] }}</li>
        <li>Email: {{ $body['email'] }}</li>
        <li>Number: {{ $body['number'] }}</li>
    </ul>
    <pre>
        {{ json_encode($body, JSON_PRETTY_PRINT) }}
    </pre>
    <p>Please address this issue promptly. Thank you.</p>
</body>
</html>
