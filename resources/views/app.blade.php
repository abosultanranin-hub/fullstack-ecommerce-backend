<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Your App</title>
</head>
<body>
    <div id="root"></div> <!-- هنا React -->
    <script src="{{ mix('js/app.js') }}"></script> <!-- ملف React -->
</body>
</html>
