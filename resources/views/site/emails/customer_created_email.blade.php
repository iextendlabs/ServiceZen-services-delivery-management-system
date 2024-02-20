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
    </style>
</head>

<body>

    <h4>Your Customer Account Has Been Created Successfully.</h4>
    <p>We have create your user, you can visit our website/app to see your order detail</p>
    <p>Your Login credentials are.</p>
    <p> <b>Name:</b> {{ $data['name'] }}</p>
    <p><b>Email:</b> {{ $data['email'] }}</p>
    <p><b>Password:</b> {{ $data['password'] }}</p>

</body>

</html>