<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/js/app.js'])

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <title>{{ config('app.name', 'Services Delivery Management System') }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 20px;
            color: #333;
            line-height: 1.6;
        }

        h2 {
            text-align: center;
            color: #fd245f;
            border-bottom: 2px solid #fd245f;
            padding-bottom: 5px;
        }

        p {
            color: #666;
        }

        section {
            background-color: #fff;
            margin-top: 10px;
        }
    </style>
</head>

<body>
    @yield('content')
</body>

</html>
