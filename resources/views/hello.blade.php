
{{-- 
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Welcome Page</title>
</head>
<body>
<h1>Welcome!</h1>

<script src="https://js.pusher.com/8.4.0/pusher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.14.1/dist/echo.iife.js"></script>

<script>
    window.Pusher = Pusher;

    const currentUserId = {{ auth()->user()->id }};

    window.Echo = new Echo({
        broadcaster: 'pusher',
        key: 'effbeffc1937454407c0',
        cluster: 'ap2',
        forceTLS: true,
        authEndpoint: '/broadcasting/auth',
        auth: {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        }
    });

    window.Echo.private(`App.Models.User.7`)
        .notification((notification) => {
            console.log('إشعار جديد:', notification);
            alert('إشعار جديد: ' + notification.شmessage);
        });
</script>
</body>
</html>
 --}}