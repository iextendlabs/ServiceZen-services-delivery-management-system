<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Viewer</title>
</head>
<body>
    <h1>Log Viewer</h1>

    <pre>{{ $logContent }}</pre>

    <form action="{{ route('log.empty') }}" method="post">
        @csrf
        @method('post')
        <button type="submit">Empty Log</button>
    </form>
</body>
</html>